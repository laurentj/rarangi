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

class ut_interface_parser extends jUnitTestCaseDb {
    protected $logger;
    protected $parserInfo;
    
    function setUp() {
        $logger = new raLogger();
        $this->logger = new raInMemoryLogger();
        $logger->addLogger($this->logger);

        $project = new ut_project_test($logger);

        $this->parserInfo = new raParserInfo($project, 'project/test.php','project','test.php');
        $logger->setCurrentParserInfo($this->parserInfo);

        $this->emptyTable('classes');
        $this->emptyTable('interface_class');
    }

    function tearDown() {
    }
    
    function testInterfaceNoName() {
        $content = ' <?php interface { } ?>';
        $p = new ut_interface_parser_test($content,1, $this->parserInfo);
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
        $p = new ut_interface_parser_test($content,1, $this->parserInfo);
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
            'is_interface'=>1,
            ));
        $this->assertTableContainsRecords('classes', $records);
    }

    function testEmptyInheritingInterface() {
        $content = " <?php interface foo extends bar {\n }\n ?>";
        $p = new ut_interface_parser_test($content,1, $this->parserInfo);
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
            'is_interface'=>1,
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
    }
}
