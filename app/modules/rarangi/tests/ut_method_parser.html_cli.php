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


class ut_method_parser_test extends raPHPFunctionParser {
    
    public $tokAfterInit = null;
    
    function __construct( $content, $numberOfToken, $doccomment="/**\n*/",
                         $accessibility=0, $isStatic=false, $isFinal=false, $isAbstract=false, $isInterface=false){
        $tokens = new ArrayObject(token_get_all($content));
        $this->iterator = $tokens->getIterator();
        $this->parserInfo = new raParserInfo(1, '', '', '');
        
        $this->toNextPhpSection();
        for($i=0; $i< $numberOfToken;$i++)
            $this->tokAfterInit = $this->toNextPhpToken();

        $fatherInfo =  new raClassDescriptor($this->parserInfo->getProjectId(), 1, 1);
        
        $this->isMethod = true;
        $this->info = new raMethodDescriptor(1,1,$this->parserInfo->currentLine());
        $this->info->inheritsFrom($fatherInfo);
        $this->info->initFromPhpDoc($doccomment);
        $this->info->accessibility = $accessibility;
        $this->info->isStatic = $isStatic;
        $this->info->isFinal = $isFinal;
        $this->info->isAbstract = $isAbstract;
        $this->info->classId = 1;
        $this->isInInterface = $isInterface;
    }

    function getIterator() { return $this->iterator;}
}


class ut_method_parser extends jUnitTestCaseDb {
    protected $logger;
    
    function setUp() {
        raLogger::removeLoggers();
        $this->logger = new raInMemoryLogger();
        raLogger::addLogger($this->logger);
        $this->emptyTable('class_methods');
        $this->emptyTable('methods_authors');
        $this->emptyTable('method_parameters');
    }

    function tearDown() {
        raLogger::removeLoggers();
    }

    function testMethodNoName() {
        $content = ' <?php function { } ?>';
        $p = new ut_method_parser_test($content,1);
        if($this->assertTrue(is_array($p->tokAfterInit)))
            $this->assertEqual($p->tokAfterInit[0] , T_FUNCTION);
        try {
            $p->parse();
            $this->fail("no exception");
        } catch(Exception $e) {
            $this->assertEqual($e->getMessage(),"invalid syntax. token expected : T_STRING, got a string :\"{\"");
        }
        $this->assertTableIsEmpty('class_methods');
    }

    function testMethodNoParenthesis() {
        $content = ' <?php function foo { } ?>';
        $p = new ut_method_parser_test($content,1);
        if($this->assertTrue(is_array($p->tokAfterInit)))
            $this->assertEqual($p->tokAfterInit[0] , T_FUNCTION);
        try {
            $p->parse();
            $this->fail("no exception");
        } catch(Exception $e) {
            $this->assertEqual($e->getMessage(),"invalid syntax. token expected : string \"(\", got \"{\"");
        }
        $this->assertTableIsEmpty('class_methods');
    }

    function testMethodNoParenthesis2() {
        $content = ' <?php function foo ( { } ?>';
        $p = new ut_method_parser_test($content,1);
        if($this->assertTrue(is_array($p->tokAfterInit)))
            $this->assertEqual($p->tokAfterInit[0] , T_FUNCTION);
        try {
            $p->parse();
            $this->fail("no exception");
        } catch(Exception $e) {
            $this->assertEqual($e->getMessage(),"Function/method parsing, invalid syntax, no ended parenthesis or begin of bloc");
        }
        $this->assertTableIsEmpty('class_methods');
    }


    function testEmptyMethod() {
        $content = " <?php \nfunction foo () {\n }\n ?>";
        $p = new ut_method_parser_test($content,1);
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
        
        $p->getInfo()->save();

        $records = array(array(
            'name'=>'foo',
            'project_id'=>1,
            'class_id'=>1,
            'line_start'=>2,
            'line_end'=>3,
            'is_static'=>0,
            'is_final'=>0,
            'is_abstract'=>0,
            'accessibility'=>'PUB',
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
        $this->assertTableContainsRecords('class_methods', $records);
        $this->assertTableIsEmpty('methods_authors');
        $this->assertTableIsEmpty('method_parameters');
    }

   function testOneMethodParameter() {
        $content = " <?php \nfunction foo (".'$aaa'.") {\n }\n ?>";
        $p = new ut_method_parser_test($content,1);
        $p->parse();
        $p->getInfo()->save();
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
        
        $this->assertEqual($p->getInfo()->parameters,
                           array(array('','aaa','','')));
        
        $records = array(array(
            'name'=>'foo',
            'project_id'=>1,
            'class_id'=>1,
            'line_start'=>2,
            'line_end'=>3,
            'is_static'=>0,
            'is_final'=>0,
            'is_abstract'=>0,
            'accessibility'=>'PUB',
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
        $this->assertTableContainsRecords('class_methods', $records);
        $this->assertTableIsEmpty('methods_authors');

        $records = array(array(
            'class_id'=>1,
            'method_name'=>'foo',
            'arg_number'=>1,
            'type'=>null,
            'name'=>'aaa',
            'defaultvalue'=>null,
            'documentation'=>''
        ));
        $this->assertTableContainsRecords('method_parameters', $records);
    }

    function testOneMethodParameterWithDoc() {
        $content = " <?php \nfunction foo (".'$aaa'.") {\n }\n ?>";
        $p = new ut_method_parser_test($content,1, "/**\n* @param string ".'$aaa'." this is a parameter\n*/");
        $p->parse();
        $p->getInfo()->save();
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
        
        $this->assertEqual($p->getInfo()->parameters,
                           array(array('string','aaa','','this is a parameter')));
        
        $records = array(array(
            'name'=>'foo',
            'project_id'=>1,
            'class_id'=>1,
            'line_start'=>2,
            'line_end'=>3,
            'is_static'=>0,
            'is_final'=>0,
            'is_abstract'=>0,
            'accessibility'=>'PUB',
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
        $this->assertTableContainsRecords('class_methods', $records);
        $this->assertTableIsEmpty('methods_authors');
        
        $records = array(array(
            'class_id'=>1,
            'method_name'=>'foo',
            'arg_number'=>1,
            'type'=>'string',
            'name'=>'aaa',
            'defaultvalue'=>null,
            'documentation'=>'this is a parameter',
        ));
        $this->assertTableContainsRecords('method_parameters', $records);
    }


    function testMethodParameters() {
        $content = " <?php \nfunction foo (".'$aaa'.", Iterator ".'$bbb'.", ".'$ccc'." = 'pipo', Plop ".'$ddd'." = null) {\n }\n ?>";
        $p = new ut_method_parser_test($content,1);
        $p->parse();
        $p->getInfo()->save();
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
        
        $this->assertEqual($p->getInfo()->parameters,
                           array(array('','aaa','',''),
                                 array('Iterator','bbb','', ''),
                                 array('','ccc',"'pipo'", ''),
                                 array('Plop','ddd','null', ''),));
        
        $records = array(array(
            'name'=>'foo',
            'project_id'=>1,
            'class_id'=>1,
            'line_start'=>2,
            'line_end'=>3,
            'is_static'=>0,
            'is_final'=>0,
            'is_abstract'=>0,
            'accessibility'=>'PUB',
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
        $this->assertTableContainsRecords('class_methods', $records);
        $this->assertTableIsEmpty('methods_authors');
        
        $records = array(array(
            'class_id'=>1,
            'method_name'=>'foo',
            'arg_number'=>1,
            'type'=>null,
            'name'=>'aaa',
            'defaultvalue'=>null,
            'documentation'=>'',
        ),
            array(
            'class_id'=>1,
            'method_name'=>'foo',
            'arg_number'=>2,
            'type'=>'Iterator',
            'name'=>'bbb',
            'defaultvalue'=>null,
            'documentation'=>'',
        ),
            array(
            'class_id'=>1,
            'method_name'=>'foo',
            'arg_number'=>3,
            'type'=>null,
            'name'=>'ccc',
            'defaultvalue'=>"'pipo'",
            'documentation'=>'',
        ),
            array(
            'class_id'=>1,
            'method_name'=>'foo',
            'arg_number'=>4,
            'type'=>'Plop',
            'name'=>'ddd',
            'defaultvalue'=>'null',
            'documentation'=>'',
        ));
        $this->assertTableContainsRecords('method_parameters', $records);
    }



    function testMethodParametersWithdoc() {
        $doc = '/**
 *  @param integer $aaa first parameter
 *  @param string $ccc third parameter
   * @param Iteratoooooooor $bbb second parameter
   * with a long documentation
   *   and a bad type
* @param Plop $ddd what?
*  @return string the result
  */';
        $content = " <?php \nfunction foo (".'$aaa'.", Iterator ".'$bbb'.", ".'$ccc'." = 'pipo', Plop ".'$ddd'." = null) {\n }\n ?>";
        $p = new ut_method_parser_test($content,1, $doc);
        $p->parse();
        $p->getInfo()->save();
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
        
        $this->assertEqual($p->getInfo()->parameters,
                           array(array('integer','aaa','','first parameter'),
                                 array('Iterator','bbb','', "second parameter\nwith a long documentation\nand a bad type"),
                                 array('string','ccc',"'pipo'", 'third parameter'),
                                 array('Plop','ddd','null', 'what?'),));
        
        $records = array(array(
            'name'=>'foo',
            'project_id'=>1,
            'class_id'=>1,
            'line_start'=>2,
            'line_end'=>3,
            'is_static'=>0,
            'is_final'=>0,
            'is_abstract'=>0,
            'accessibility'=>'PUB',
            'short_description'=>'',
            'description'=>'',
            'return_datatype'=>'string',
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
        $this->assertTableContainsRecords('class_methods', $records);
        $this->assertTableIsEmpty('methods_authors');
        
        $records = array(array(
            'class_id'=>1,
            'method_name'=>'foo',
            'arg_number'=>1,
            'type'=>'integer',
            'name'=>'aaa',
            'defaultvalue'=>null,
            'documentation'=>'first parameter',
        ),
            array(
            'class_id'=>1,
            'method_name'=>'foo',
            'arg_number'=>2,
            'type'=>'Iterator',
            'name'=>'bbb',
            'defaultvalue'=>null,
            'documentation'=>"second parameter\nwith a long documentation\nand a bad type",
        ),
            array(
            'class_id'=>1,
            'method_name'=>'foo',
            'arg_number'=>3,
            'type'=>'string',
            'name'=>'ccc',
            'defaultvalue'=>"'pipo'",
            'documentation'=>'third parameter',
        ),
            array(
            'class_id'=>1,
            'method_name'=>'foo',
            'arg_number'=>4,
            'type'=>'Plop',
            'name'=>'ddd',
            'defaultvalue'=>'null',
            'documentation'=>'what?',
        ));
        $this->assertTableContainsRecords('method_parameters', $records);
    }


}
