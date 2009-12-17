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

require_once( dirname(__FILE__).'/parser_test.lib.php');

class ut_class_parser extends jUnitTestCaseDb {
    protected $logger;
    protected $parserInfo;
    
    function setUp() {
        $logger = new raLogger();
        $this->logger = new raInMemoryLogger();
        $logger->addLogger($this->logger);

        $project = new ut_project_test($logger);

        $this->parserInfo = new raParserInfo($project, 'project/test.php','project','test.php');

        $this->emptyTable('classes_authors');
        $this->emptyTable('methods_authors');
        $this->emptyTable('class_properties');
        $this->emptyTable('class_methods');
        $this->emptyTable('interface_class');
        $this->emptyTable('classes');
    }

    function tearDown() {

    }
    
    function testClassNoName() {
        $content = ' <?php class { } ?>';
        $p = new ut_class_parser_test($content,1,$this->parserInfo);
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
        $p = new ut_class_parser_test($content,1,$this->parserInfo);
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
        $this->assertEqual($p->getDescriptor()->name , 'foo');
        
        $records = array(array(
            'id'=>$p->getDescriptor()->classId,
            'name'=>'foo',
            'project_id'=>$this->parserInfo->getProjectId(),
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
        $p = new ut_class_parser_test($content,1,$this->parserInfo);
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
        
        $this->assertEqual($p->getDescriptor()->name , 'foo');
        $this->assertEqual($p->getDescriptor()->mother , 'bar');
        $this->assertEqual($p->getDescriptor()->interfaces , array());

        $db = jDb::getConnection();
        $rs = $db->query("SELECT id FROM classes WHERE name='bar'");
        $this->assertNotEqual($rs, false);
        $rec= $rs->fetch();
        $this->assertNotEqual($rec, false);
        $barId = $rec->id;
        $rs = null;


        $records = array(array(
            'id'=>$p->getDescriptor()->classId,
            'name'=>'foo',
            'project_id'=>$this->parserInfo->getProjectId(),
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
            'project_id'=>$this->parserInfo->getProjectId(),
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
        $p = new ut_class_parser_test($content,1,$this->parserInfo);
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
        
        $this->assertEqual($p->getDescriptor()->name , 'foo');
        $this->assertEqual($p->getDescriptor()->mother , '');
        $this->assertEqual($p->getDescriptor()->interfaces , array('bar'));

        $db = jDb::getConnection();
        $rs = $db->query("SELECT id FROM classes WHERE name='bar'");
        $this->assertNotEqual($rs, false);
        $rec= $rs->fetch();
        $this->assertNotEqual($rec, false);
        $barId = $rec->id;
        $rs = null;


        $records = array(array(
            'id'=>$p->getDescriptor()->classId,
            'name'=>'foo',
            'project_id'=>$this->parserInfo->getProjectId(),
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
            'project_id'=>$this->parserInfo->getProjectId(),
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
            array('class_id'=>$p->getDescriptor()->classId,
                  'interface_id'=>$barId,
                  'project_id'=>$this->parserInfo->getProjectId())
        );
        $this->assertTableContainsRecords('interface_class', $records);
    }

    function testEmptyImplementingClass2() {
        $content = " <?php class foo implements bar, baz {\n }\n ?>";
        $p = new ut_class_parser_test($content,1,$this->parserInfo);
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
        
        $this->assertEqual($p->getDescriptor()->name , 'foo');
        $this->assertEqual($p->getDescriptor()->mother , '');
        $this->assertEqual($p->getDescriptor()->interfaces , array('bar', 'baz'));

        $db = jDb::getConnection();
        $rs = $db->query("SELECT id, name FROM classes");
        $this->assertNotEqual($rs, false);
        $barId = $bazId = null;
        $n = 0;
        while ($rec = $rs->fetch()) {
            if ($rec->name == 'bar')
                $barId = $rec->id;
            else if ($rec->name == 'baz')
                $bazId = $rec->id;
            $n++;
        }
        $this->assertEqual($n, 3);
        $this->assertNotNull($barId);
        $this->assertNotNull($bazId);
        $rs = null;


        $records = array(array(
            'id'=>$p->getDescriptor()->classId,
            'name'=>'foo',
            'project_id'=>$this->parserInfo->getProjectId(),
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
            'project_id'=>$this->parserInfo->getProjectId(),
            'file_id'=>null,
            'line_start'=>0,
            'line_end'=>0,
            'package_id'=>null,
            'mother_class'=>null,
            'is_abstract'=>0,
            'is_interface'=>1,
            ),
            array(
            'id'=>$bazId,
            'name'=>'baz',
            'project_id'=>$this->parserInfo->getProjectId(),
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
            array('class_id'=>$p->getDescriptor()->classId,
                  'interface_id'=>$barId,
                  'project_id'=>$this->parserInfo->getProjectId()),
            array('class_id'=>$p->getDescriptor()->classId,
                  'interface_id'=>$bazId,
                  'project_id'=>$this->parserInfo->getProjectId())

        );
        $this->assertTableContainsRecords('interface_class', $records);
    }


    function testSimpleClass() {
        $content = " <?php
 class foo {
    /**
    * this is a description
    */
    public \$bar;
    /**
    * @var string \$baz this is an other description
    */
    protected static \$baz =\"lorem ipsum\";

    /**
     * @const integer bli bla blo
     */
    const bla = 4;
    /**
     * begin of the description
     * @var string the description continue here.
     *             and also here.
     */
    private \$aprivate;
    
    public \$other;

    /**
     * @const integer #zoop oh, zoop variable !
     */
    const zoop = 8;
    
    /**
     * @var myClass
     */
    public \$anObject;

   /**
    * the data container
    * @var jFormsDataContainer
    */
    protected \$container = null;
}
?>";
        $p = new ut_class_parser_test($content,1,$this->parserInfo);
        $p->parse();
        $this->assertEqual($p->getParserInfo()->currentLine(), 40);
        
        if($this->assertTrue($p->getIterator()->valid())) {
            $tok = $p->getIterator()->current();
            $this->assertEqual($tok, '}');
        }
        $log = $this->logger->getLog();
        $this->assertEqual(count($log['error']),0);
        $this->assertEqual(count($log['warning']),0);
        $this->assertEqual(count($log['notice']),0);
        $this->assertEqual($p->getDescriptor()->name , 'foo');
        
        $records = array(array(
            'id'=>$p->getDescriptor()->classId,
            'name'=>'foo',
            'project_id'=>$this->parserInfo->getProjectId(),
            'file_id'=>1,
            'line_start'=>2,
            'line_end'=>40,
            'package_id'=>null,
            'mother_class'=>null,
            'is_abstract'=>0,
            'is_interface'=>0,
            ));
        $this->assertTableContainsRecords('classes', $records);
        
        $this->assertTableHasNRecords('class_properties', 8);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'bar',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>6,
                'datatype'=>'',
                'default_value'=>'',
                'type'=>0,
                'accessibility'=>'PUB',
                'short_description'=>'this is a description',
                'description'=>''
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'baz',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>10,
                'datatype'=>'string',
                'default_value'=>'"lorem ipsum"',
                'type'=>1,
                'accessibility'=>'PRO',
                'short_description'=>'this is an other description',
                'description'=>''
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'bla',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>15,
                'datatype'=>'integer',
                'default_value'=>'4',
                'type'=>2,
                'accessibility'=>'PUB',
                'short_description'=>'bli bla blo',
                'description'=>''
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'aprivate',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>21,
                'datatype'=>'string',
                'default_value'=>'',
                'type'=>0,
                'accessibility'=>'PRI',
                'short_description'=>'begin of the description',
                'description'=>'the description continue here.
and also here.'
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'other',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>23,
                'datatype'=>'',
                'default_value'=>'',
                'type'=>0,
                'accessibility'=>'PUB',
                'short_description'=>'',
                'description'=>''
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'zoop',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>28,
                'datatype'=>'integer',
                'default_value'=>'8',
                'type'=>2,
                'accessibility'=>'PUB',
                'short_description'=>'oh, zoop variable !',
                'description'=>''
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'anObject',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>33,
                'datatype'=>'myClass',
                'default_value'=>'',
                'type'=>0,
                'accessibility'=>'PUB',
                'short_description'=>'',
                'description'=>''
            ),
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'container',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>39,
                'datatype'=>'jFormsDataContainer',
                'default_value'=>'null',
                'type'=>0,
                'accessibility'=>'PRO',
                'short_description'=>'the data container',
                'description'=>''
            ),
        ), false);
    }

    function testCombinedProperties() {
        $content = " <?php
class foo {
    /**
    * this is a description
    */
    public \$bar, \$bar2;

    /**
    * description 2
    * @var string \$baz2 documentation of baz2
    */
    protected \$baz =\"lorem ipsum\", \$baz2;

    /**
     * @var null|object \$priv1  doc of priv1
     *              which continue here.
     * @var integer \$priv4 doc of priv4
     * @var string valid for all
     */
    private \$priv1 = null, \$priv2= 3.141,
  \$priv4, \$priv5='bachibouzouk';
   
   /**
    * @const integer #zoop bla bla
    * @const integer #bla yes, an other bla const
    */
   const bla = 4, zoop = 'toto';
}
?>";

        $p = new ut_class_parser_test($content,1,$this->parserInfo);
        $p->parse();
        $this->assertEqual($p->getParserInfo()->currentLine(), 28);

        $log = $this->logger->getLog();
        $this->assertEqual(count($log['error']),0);
        $this->assertEqual(count($log['warning']),0);
        $this->assertEqual(count($log['notice']),0);
        $this->assertEqual($p->getDescriptor()->name , 'foo');
        
        $records = array(array(
            'id'=>$p->getDescriptor()->classId,
            'name'=>'foo',
            'project_id'=>$this->parserInfo->getProjectId(),
            'file_id'=>1,
            'line_start'=>2,
            'line_end'=>28,
            'package_id'=>null,
            'mother_class'=>null,
            'is_abstract'=>0,
            'is_interface'=>0,
            ));
        $this->assertTableContainsRecords('classes', $records);
        
        $this->assertTableHasNRecords('class_properties',10);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'bar',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>6,
                'datatype'=>'',
                'default_value'=>'',
                'type'=>0,
                'accessibility'=>'PUB',
                'short_description'=>'this is a description',
                'description'=>''
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'bar2',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>6,
                'datatype'=>'',
                'default_value'=>'',
                'type'=>0,
                'accessibility'=>'PUB',
                'short_description'=>'this is a description',
                'description'=>''
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'baz',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>12,
                'datatype'=>'',
                'default_value'=>'"lorem ipsum"',
                'type'=>0,
                'accessibility'=>'PRO',
                'short_description'=>'description 2',
                'description'=>''
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'baz2',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>12,
                'datatype'=>'string',
                'default_value'=>'',
                'type'=>0,
                'accessibility'=>'PRO',
                'short_description'=>'description 2',
                'description'=>'documentation of baz2'
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'priv1',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>20,
                'datatype'=>'null|object',
                'default_value'=>'null',
                'type'=>0,
                'accessibility'=>'PRI',
                'short_description'=>'doc of priv1',
                'description'=>'which continue here.'
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'priv2',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>20,
                'datatype'=>'string',
                'default_value'=>'3.141',
                'type'=>0,
                'accessibility'=>'PRI',
                'short_description'=>'valid for all',
                'description'=>''
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'priv4',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>20,
                'datatype'=>'integer',
                'default_value'=>'',
                'type'=>0,
                'accessibility'=>'PRI',
                'short_description'=>'doc of priv4',
                'description'=>''
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'priv5',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>20,
                'datatype'=>'string',
                'default_value'=>'\'bachibouzouk\'',
                'type'=>0,
                'accessibility'=>'PRI',
                'short_description'=>'valid for all',
                'description'=>''
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'bla',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>27,
                'datatype'=>'integer',
                'default_value'=>'4',
                'type'=>2,
                'accessibility'=>'PUB',
                'short_description'=>'yes, an other bla const',
                'description'=>''
            )
        ), false);
        $this->assertTableContainsRecords('class_properties', array(
            array(
                'name'=>'zoop',
                'class_id'=>$p->getDescriptor()->classId,
                'project_id'=>$this->parserInfo->getProjectId(),
                'line_start'=>27,
                'datatype'=>'integer',
                'default_value'=>'\'toto\'',
                'type'=>2,
                'accessibility'=>'PUB',
                'short_description'=>'bla bla',
                'description'=>''
            ),
        ),false);
    }


}
