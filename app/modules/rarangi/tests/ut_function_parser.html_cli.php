<?php
/**
* @package     rarangi
* @subpackage  tests
* @author      Laurent Jouanneau
* @contributor
* @copyright   2009 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

require_once( dirname(__FILE__).'/parser_test.lib.php');


class ut_function_parser extends jUnitTestCaseDb {
    protected $logger;
    protected $parserInfo;
    
    function setUp() {
        $logger = new raLogger();
        $this->logger = new raInMemoryLogger();
        $logger->addLogger($this->logger);

        $project = new ut_project_test($logger);

        $this->parserInfo = new raParserInfo($project, 'project/test.php','project','test.php');
        $logger->setCurrentParserInfo($this->parserInfo);

        $this->emptyTable('functions');
        $this->emptyTable('functions_authors');
        $this->emptyTable('function_parameters');
    }

    function tearDown() {
    }
    
    function testFunctionNoName() {
        $content = ' <?php function { } ?>';
        $p = new ut_function_parser_test($content,1, $this->parserInfo);
        if($this->assertTrue(is_array($p->tokAfterInit)))
            $this->assertEqual($p->tokAfterInit[0] , T_FUNCTION);
        try {
            $p->parse();
            $this->fail("no exception");
        } catch(Exception $e) {
            $this->assertEqual($e->getMessage(),"invalid syntax. token expected : T_STRING, got a string :\"{\"");
        }
        $this->assertTableIsEmpty('functions');
    }

    function testFunctionNoParenthesis() {
        $content = ' <?php function foo { } ?>';
        $p = new ut_function_parser_test($content,1, $this->parserInfo);
        if($this->assertTrue(is_array($p->tokAfterInit)))
            $this->assertEqual($p->tokAfterInit[0] , T_FUNCTION);
        try {
            $p->parse();
            $this->fail("no exception");
        } catch(Exception $e) {
            $this->assertEqual($e->getMessage(),"invalid syntax. token expected : string \"(\", got \"{\"");
        }
        $this->assertTableIsEmpty('functions');
    }

    function testFunctionNoParenthesis2() {
        $content = ' <?php function foo ( { } ?>';
        $p = new ut_function_parser_test($content,1, $this->parserInfo);
        if($this->assertTrue(is_array($p->tokAfterInit)))
            $this->assertEqual($p->tokAfterInit[0] , T_FUNCTION);
        try {
            $p->parse();
            $this->fail("no exception");
        } catch(Exception $e) {
            $this->assertEqual($e->getMessage(),"Function/method parsing, invalid syntax, no ended parenthesis or begin of bloc");
        }
        $this->assertTableIsEmpty('functions');
    }


    function testEmptyFunction() {
        $content = " <?php \nfunction foo () {\n }\n ?>";
        $p = new ut_function_parser_test($content,1, $this->parserInfo);
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
            'id'=>$p->getDescriptor()->functionId,
            'name'=>'foo',
            'project_id'=>$this->parserInfo->getProjectId(),
            'package_id'=>null,
            'file_id'=>1,
            'line_start'=>2,
            'line_end'=>3,
            'short_description'=>'',
            'description'=>'',
            'return_datatype'=>'',
            'return_description'=>'',
            'copyright'=>'',
            'internal'=>'',
            'links'=>'a:0:{}',
            'see'=>'a:0:{}',
            'uses'=>'a:0:{}',
            'changelog'=>'a:0:{}',
            'todo'=>'',
            'since'=>'',
            'license_label'=>'',
            'license_link'=>'',
            'license_text'=>''
            ));
        $this->assertTableContainsRecords('functions', $records);
        $this->assertTableIsEmpty('functions_authors');
        $this->assertTableIsEmpty('function_parameters');
    }

    function testOneFunctionParameter() {
        $content = " <?php \nfunction foo (".'$aaa'.") {\n }\n ?>";
        $p = new ut_function_parser_test($content,1, $this->parserInfo);
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
        
        $this->assertEqual($p->getDescriptor()->parameters,
                           array(array('','aaa','','')));
        
        $records = array(array(
            'id'=>$p->getDescriptor()->functionId,
            'name'=>'foo',
            'project_id'=>$this->parserInfo->getProjectId(),
            'package_id'=>null,
            'file_id'=>1,
            'line_start'=>2,
            'line_end'=>3,
            'short_description'=>'',
            'description'=>'',
            'return_datatype'=>'',
            'return_description'=>'',
            'copyright'=>'',
            'internal'=>'',
            'links'=>'a:0:{}',
            'see'=>'a:0:{}',
            'uses'=>'a:0:{}',
            'changelog'=>'a:0:{}',
            'todo'=>'',
            'since'=>'',
            'license_label'=>'',
            'license_link'=>'',
            'license_text'=>''
            ));
        $this->assertTableContainsRecords('functions', $records);
        $this->assertTableIsEmpty('functions_authors');
        
        $records = array(array(
            'function_id'=>$p->getDescriptor()->functionId,
            'arg_number'=>1,
            'type'=>null,
            'name'=>'aaa',
            'defaultvalue'=>null,
            'documentation'=>''
        ));
        $this->assertTableContainsRecords('function_parameters', $records);
    }

    function testOneFunctionParameterWithDoc() {
        $content = " <?php \nfunction foo (".'$aaa'.") {\n }\n ?>";
        $p = new ut_function_parser_test($content,1, $this->parserInfo, "/**\n* @param string ".'$aaa'." this is a parameter\n*/");
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
        
        $this->assertEqual($p->getDescriptor()->parameters,
                           array(array(array('string'),'aaa','','this is a parameter')));
        
        $records = array(array(
            'id'=>$p->getDescriptor()->functionId,
            'name'=>'foo',
            'project_id'=>$this->parserInfo->getProjectId(),
            'package_id'=>null,
            'file_id'=>1,
            'line_start'=>2,
            'line_end'=>3,
            'short_description'=>'',
            'description'=>'',
            'return_datatype'=>'',
            'return_description'=>'',
            'copyright'=>'',
            'internal'=>'',
            'links'=>'a:0:{}',
            'see'=>'a:0:{}',
            'uses'=>'a:0:{}',
            'changelog'=>'a:0:{}',
            'todo'=>'',
            'since'=>'',
            'license_label'=>'',
            'license_link'=>'',
            'license_text'=>''
            ));
        $this->assertTableContainsRecords('functions', $records);
        $this->assertTableIsEmpty('functions_authors');
        
        $records = array(array(
            'function_id'=>$p->getDescriptor()->functionId,
            'arg_number'=>1,
            'type'=>'|string|',
            'name'=>'aaa',
            'defaultvalue'=>null,
            'documentation'=>'this is a parameter',
        ));
        $this->assertTableContainsRecords('function_parameters', $records);
    }


    function testFunctionParameters() {
        $content = " <?php \nfunction foo (".'$aaa'.", Iterator ".'$bbb'.", ".'$ccc'." = 'pipo', Plop ".'$ddd'." = null) {\n }\n ?>";
        $p = new ut_function_parser_test($content,1, $this->parserInfo);
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
        
        $this->assertEqual($p->getDescriptor()->parameters,
                           array(array('','aaa','',''),
                                 array('Iterator','bbb','', ''),
                                 array('','ccc',"'pipo'", ''),
                                 array('Plop','ddd','null', ''),));
        
        $records = array(array(
            'id'=>$p->getDescriptor()->functionId,
            'name'=>'foo',
            'project_id'=>$this->parserInfo->getProjectId(),
            'package_id'=>null,
            'file_id'=>1,
            'line_start'=>2,
            'line_end'=>3,
            'short_description'=>'',
            'description'=>'',
            'return_datatype'=>'',
            'return_description'=>'',
            'copyright'=>'',
            'internal'=>'',
            'links'=>'a:0:{}',
            'see'=>'a:0:{}',
            'uses'=>'a:0:{}',
            'changelog'=>'a:0:{}',
            'todo'=>'',
            'since'=>'',
            'license_label'=>'',
            'license_link'=>'',
            'license_text'=>''
            ));
        $this->assertTableContainsRecords('functions', $records);
        $this->assertTableIsEmpty('functions_authors');
        
        $records = array(array(
            'function_id'=>$p->getDescriptor()->functionId,
            'arg_number'=>1,
            'type'=>null,
            'name'=>'aaa',
            'defaultvalue'=>null,
            'documentation'=>'',
        ),
            array(
            'function_id'=>$p->getDescriptor()->functionId,
            'arg_number'=>2,
            'type'=>'|Iterator|',
            'name'=>'bbb',
            'defaultvalue'=>null,
            'documentation'=>'',
        ),
            array(
            'function_id'=>$p->getDescriptor()->functionId,
            'arg_number'=>3,
            'type'=>null,
            'name'=>'ccc',
            'defaultvalue'=>"'pipo'",
            'documentation'=>'',
        ),
            array(
            'function_id'=>$p->getDescriptor()->functionId,
            'arg_number'=>4,
            'type'=>'|Plop|',
            'name'=>'ddd',
            'defaultvalue'=>'null',
            'documentation'=>'',
        ));
        $this->assertTableContainsRecords('function_parameters', $records);
    }



    function testFunctionParametersWithdoc() {
        $doc = '/**
 *  @param integer|double $aaa first parameter
 *  @param array $ccc third parameter
   * @param Iteratoooooooor $bbb second parameter
   * with a long documentation
   *   and a bad type
* @param Plop $ddd what?
*  @return string the result
  */';
        $content = " <?php \nfunction foo (".'$aaa'.", Iterator ".'$bbb'.", ".'$ccc'." = array('pipo'), Plop ".'$ddd'." = null) {\n }\n ?>";
        $p = new ut_function_parser_test($content,1, $this->parserInfo, $doc);
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
        
        $this->assertEqual($p->getDescriptor()->parameters,
                           array(array(array('integer', 'double'),'aaa','','first parameter'),
                                 array('Iterator','bbb','', "second parameter\nwith a long documentation\nand a bad type"),
                                 array(array('array'),'ccc',"array('pipo')", 'third parameter'),
                                 array('Plop','ddd','null', 'what?'),));
        
        $records = array(array(
            'id'=>$p->getDescriptor()->functionId,
            'name'=>'foo',
            'project_id'=>$this->parserInfo->getProjectId(),
            'package_id'=>null,
            'file_id'=>1,
            'line_start'=>2,
            'line_end'=>3,
            'short_description'=>'',
            'description'=>'',
            'return_datatype'=>'|string|',
            'return_description'=>'the result',
            'copyright'=>'',
            'internal'=>'',
            'links'=>'a:0:{}',
            'see'=>'a:0:{}',
            'uses'=>'a:0:{}',
            'changelog'=>'a:0:{}',
            'todo'=>'',
            'since'=>'',
            'license_label'=>'',
            'license_link'=>'',
            'license_text'=>''
            ));
        $this->assertTableContainsRecords('functions', $records);
        $this->assertTableIsEmpty('functions_authors');
        
        $records = array(array(
            'function_id'=>$p->getDescriptor()->functionId,
            'arg_number'=>1,
            'type'=>'|integer|double|',
            'name'=>'aaa',
            'defaultvalue'=>null,
            'documentation'=>'first parameter',
        ),
            array(
            'function_id'=>$p->getDescriptor()->functionId,
            'arg_number'=>2,
            'type'=>'|Iterator|',
            'name'=>'bbb',
            'defaultvalue'=>null,
            'documentation'=>"second parameter\nwith a long documentation\nand a bad type",
        ),
            array(
            'function_id'=>$p->getDescriptor()->functionId,
            'arg_number'=>3,
            'type'=>'|array|',
            'name'=>'ccc',
            'defaultvalue'=>"array('pipo')",
            'documentation'=>'third parameter',
        ),
            array(
            'function_id'=>$p->getDescriptor()->functionId,
            'arg_number'=>4,
            'type'=>'|Plop|',
            'name'=>'ddd',
            'defaultvalue'=>'null',
            'documentation'=>'what?',
        ));
        $this->assertTableContainsRecords('function_parameters', $records);
    }
}
