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

class ut_interface_parser_test extends raPHPInterfaceParser {
    
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

        $this->info = new raInterfaceDescriptor(1, 1, $this->parserInfo->currentLine());
        $this->info->inheritsFrom($fatherInfo);
        $this->info->initFromPhpDoc($doccomment);
    }

    function getIterator() { return $this->iterator;}

}


class ut_interface_parser extends jUnitTestCaseDb {
    protected $logger;
    
    function setUp() {
        raLogger::removeLoggers();
        $this->logger = new raInMemoryLogger();
        raLogger::addLogger($this->logger);
        $this->emptyTable('classes');
        $this->emptyTable('interface_class');
    }

    function tearDown() {
    }
    
    function testInterfaceNoName() {
        $content = ' <?php interface { } ?>';
        $p = new ut_interface_parser_test($content,1);
        if($this->assertTrue(is_array($p->tokAfterInit)))
            $this->assertEqual($p->tokAfterInit[0] , T_INTERFACE);
        try {
            $p->parse();
            $this->fail("no exception");
        } catch(Exception $e) {
            $this->assertEqual($e->getMessage(),"invalid syntax. token expected : T_STRING, got a string :\"{\"");
        }
        $this->assertTableIsEmpty('classes');
    }
    
    function testEmptyInterface() {
        $content = " <?php \ninterface foo {\n }\n ?>";
        $p = new ut_interface_parser_test($content,1);
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
            'linenumber'=>2,
            'package_id'=>null,
            'mother_class'=>null,
            'is_abstract'=>0,
            'is_interface'=>1,
            ));
        $this->assertTableContainsRecords('classes', $records);
    }

    function testEmptyInheritingInterface() {
        $content = " <?php interface foo extends bar {\n }\n ?>";
        $p = new ut_interface_parser_test($content,1);
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
            'linenumber'=>1,
            'package_id'=>null,
            'mother_class'=>$barId,
            'is_abstract'=>0,
            'is_interface'=>1,
            ),
            array(
            'id'=>$barId,
            'name'=>'bar',
            'project_id'=>1,
            'file_id'=>null,
            'linenumber'=>0,
            'package_id'=>null,
            'mother_class'=>null,
            'is_abstract'=>0,
            'is_interface'=>1,
            )
            );
        $this->assertTableContainsRecords('classes', $records);
    }
}
