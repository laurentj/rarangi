<?php
/**
* @package     rarangi
* @subpackage  tests
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007-2008 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

require_once( dirname(__FILE__).'/../classes/raLogger.class.php');
require_once( dirname(__FILE__).'/../classes/raDocGenerator.class.php');

class ut_class_parser_test extends raPHPClassParser {
    
    public $tokAfterInit = null;
    
    function __construct( $content, $numberOfToken, $doccomment="/**\n*/", $isAbstract=false){
        $tokens = new ArrayObject(token_get_all($content));
        $this->iterator = $tokens->getIterator();
        $this->parserInfo = new raParserInfo(1, '', '', '');
        
        $this->toNextPhpSection();
        for($i=0; $i< $numberOfToken;$i++)
            $this->tokAfterInit = $this->toNextPhpToken();

        $fatherInfo =  new raFileDescriptor($this->parserInfo->getProjectId(),
                                          $this->parserInfo->getFullSourcePath(),
                                          $this->parserInfo->currentFile(),
                                          $this->parserInfo->currentFileName());

        $this->info = new raClassDescriptor(1, 1, $this->parserInfo->currentLine());
        $this->info->inheritsFrom($fatherInfo);
        $this->info->initFromPhpDoc($doccomment);
        $this->info->isAbstract = $isAbstract;
    }

    function getIterator() { return $this->iterator;}

}


class ut_class_parser extends jUnitTestCaseDb {
    protected $logger;
    
    function setUp() {
        raLogger::removeLoggers();
        $this->logger = new raInMemoryLogger();
        raLogger::addLogger($this->logger);
        $this->emptyTable('classes_authors');
        $this->emptyTable('methods_authors');
        $this->emptyTable('class_properties');
        $this->emptyTable('class_methods');
        $this->emptyTable('interface_class');
        $this->emptyTable('classes');
    }

    function tearDown() {
        raLogger::removeLoggers();
    }
    
    function testClassNoName() {
        $content = ' <?php class { } ?>';
        $p = new ut_class_parser_test($content,1);
        if($this->assertTrue(is_array($p->tokAfterInit)))
            $this->assertEqual($p->tokAfterInit[0] , T_CLASS);
        try {
            $p->parse();
            $this->fail("no exception");
        } catch(Exception $e) {
            $this->assertEqual($e->getMessage(),"invalid syntax. token expected : T_STRING, got a string :\"{\"");
        }
        $this->assertTableIsEmpty('classes');
    }
    
    function testEmptyClass() {
        $content = " <?php \nclass foo {\n }\n ?>";
        $p = new ut_class_parser_test($content,1);
        $p->parse();
        $this->assertEqual($p->getParserInfo()->currentLine(), 3);
        
        if($this->assertTrue($p->getIterator()->valid())) {
            $tok = $p->getIterator()->current();
            $this->assertEqual($tok, '}');
        }
        $log = $this->logger->getLog();
        $this->assertEqual(count($log['error']),0);
        $this->assertEqual(count($log['warning']),0);
        $this->assertEqual(count($log['notice']),0);
        $this->assertEqual($p->getInfo()->name , 'foo');
        
        $records = array(array(
            'id'=>$p->getInfo()->classId,
            'name'=>'foo',
            'project_id'=>1,
            'file_id'=>1,
            'line_start'=>2,
            'line_end'=>3,
            'package_id'=>null,
            'mother_class'=>null,
            'is_abstract'=>0,
            'is_interface'=>0,
            ));
        $this->assertTableContainsRecords('classes', $records);
    }

    function testEmptyInheritingClass() {
        $content = " <?php class foo extends bar {\n }\n ?>";
        $p = new ut_class_parser_test($content,1);
        $p->parse();
        $this->assertEqual($p->getParserInfo()->currentLine(), 2);
        
        if($this->assertTrue($p->getIterator()->valid())) {
            $tok = $p->getIterator()->current();
            $this->assertEqual($tok, '}');
        }
        $log = $this->logger->getLog();
        $this->assertEqual(count($log['error']),0);
        $this->assertEqual(count($log['warning']),0);
        $this->assertEqual(count($log['notice']),0);
        
        $this->assertEqual($p->getInfo()->name , 'foo');
        $this->assertEqual($p->getInfo()->inheritsFrom , 'bar');
        $this->assertEqual($p->getInfo()->interfaces , array());

        $db = jDb::getConnection();
        $rs = $db->query("SELECT id FROM classes WHERE name='bar'");
        $this->assertNotEqual($rs, false);
        $rec= $rs->fetch();
        $this->assertNotEqual($rec, false);
        $barId = $rec->id;
        $rs = null;


        $records = array(array(
            'id'=>$p->getInfo()->classId,
            'name'=>'foo',
            'project_id'=>1,
            'file_id'=>1,
            'line_start'=>1,
            'line_end'=>2,
            'package_id'=>null,
            'mother_class'=>$barId,
            'is_abstract'=>0,
            'is_interface'=>0,
            ),
            array(
            'id'=>$barId,
            'name'=>'bar',
            'project_id'=>1,
            'file_id'=>null,
            'line_start'=>0,
            'line_end'=>0,
            'package_id'=>null,
            'mother_class'=>null,
            'is_abstract'=>0,
            'is_interface'=>0,
            )
            );
        $this->assertTableContainsRecords('classes', $records);
    }

    function testEmptyImplementingClass() {
        $content = " <?php class foo implements bar {\n }\n ?>";
        $p = new ut_class_parser_test($content,1);
        $p->parse();
        $this->assertEqual($p->getParserInfo()->currentLine(), 2);
        
        if($this->assertTrue($p->getIterator()->valid())) {
            $tok = $p->getIterator()->current();
            $this->assertEqual($tok, '}');
        }
        $log = $this->logger->getLog();
        $this->assertEqual(count($log['error']),0);
        $this->assertEqual(count($log['warning']),0);
        $this->assertEqual(count($log['notice']),0);
        
        $this->assertEqual($p->getInfo()->name , 'foo');
        $this->assertEqual($p->getInfo()->inheritsFrom , '');
        $this->assertEqual($p->getInfo()->interfaces , array('bar'));

        $db = jDb::getConnection();
        $rs = $db->query("SELECT id FROM classes WHERE name='bar'");
        $this->assertNotEqual($rs, false);
        $rec= $rs->fetch();
        $this->assertNotEqual($rec, false);
        $barId = $rec->id;
        $rs = null;


        $records = array(array(
            'id'=>$p->getInfo()->classId,
            'name'=>'foo',
            'project_id'=>1,
            'file_id'=>1,
            'line_start'=>1,
            'line_end'=>2,
            'package_id'=>null,
            'mother_class'=>null,
            'is_abstract'=>0,
            'is_interface'=>0,
            ),
            array(
            'id'=>$barId,
            'name'=>'bar',
            'project_id'=>1,
            'file_id'=>null,
            'line_start'=>0,
            'line_end'=>0,
            'package_id'=>null,
            'mother_class'=>null,
            'is_abstract'=>0,
            'is_interface'=>1,
            )
            );
        $this->assertTableContainsRecords('classes', $records);
        
        $records = array(
            array('class_id'=>$p->getInfo()->classId, 'interface_id'=>$barId, 'project_id'=>1)
        );
        $this->assertTableContainsRecords('interface_class', $records);
        
    }

    function testSimpleClass() {
        $content = " <?php \nclass foo {\n public \$bar; \n protected static \$baz =\"lorem ipsum\"; \n}\n ?>";
        $p = new ut_class_parser_test($content,1);
        $p->parse();
        $this->assertEqual($p->getParserInfo()->currentLine(), 5);
        
        if($this->assertTrue($p->getIterator()->valid())) {
            $tok = $p->getIterator()->current();
            $this->assertEqual($tok, '}');
        }
        $log = $this->logger->getLog();
        $this->assertEqual(count($log['error']),0);
        $this->assertEqual(count($log['warning']),0);
        $this->assertEqual(count($log['notice']),0);
        $this->assertEqual($p->getInfo()->name , 'foo');
        
        $records = array(array(
            'id'=>$p->getInfo()->classId,
            'name'=>'foo',
            'project_id'=>1,
            'file_id'=>1,
            'line_start'=>2,
            'line_end'=>5,
            'package_id'=>null,
            'mother_class'=>null,
            'is_abstract'=>0,
            'is_interface'=>0,
            ));
        $this->assertTableContainsRecords('classes', $records);
        
        $records = array(
            array(
                'name'=>'bar',
                'class_id'=>$p->getInfo()->classId,
                'project_id'=>1,
                'line_start'=>3,
                'datatype'=>'',
                'default_value'=>'',
                'is_static'=>0,
                'accessibility'=>'PUB',
                'short_description'=>'',
                'description'=>''
            ),
            array(
                'name'=>'baz',
                'class_id'=>$p->getInfo()->classId,
                'project_id'=>1,
                'line_start'=>4,
                'datatype'=>'',
                'default_value'=>'"lorem ipsum"',
                'is_static'=>1,
                'accessibility'=>'PRO',
                'short_description'=>'',
                'description'=>''
            ),
        );
        $this->assertTableContainsRecords('class_properties', $records);
    }

}
