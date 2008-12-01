<?php
/**
* @package     jDoc
* @subpackage  tests
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007-2008 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

require_once( dirname(__FILE__).'/../classes/jLogger.class.php');
require_once( dirname(__FILE__).'/../classes/jDoc.class.php');

class ut_class_parser_test extends jClassParser {
    
    public $tokAfterInit = null;
    
    function __construct( $content, $numberOfToken, $doccomment="/**\n*/", $isAbstract=false){
        $tokens = new ArrayObject(token_get_all($content));
        $this->iterator = $tokens->getIterator();
        $this->parserInfo = new jParserInfo(1, '', '', '');
        
        $this->toNextPhpSection();
        for($i=0; $i< $numberOfToken;$i++)
            $this->tokAfterInit = $this->toNextPhpToken();

        $fatherInfo =  new jFileDescriptor($this->parserInfo->getProjectId(),
                                          $this->parserInfo->getFullSourcePath(),
                                          $this->parserInfo->currentFile(),
                                          $this->parserInfo->currentFileName());

        $this->info = new jClassDescriptor(1, 1, $this->parserInfo->currentLine());
        $this->info->inheritsFrom($fatherInfo);
        $this->info->initFromPhpDoc($doccomment);
        $this->info->isAbstract = $isAbstract;
    }

    function getIterator() { return $this->iterator;}

}


class ut_class_parser extends jUnitTestCaseDb {
    protected $logger;
    
    function setUp() {
        jLogger::removeLoggers();
        $this->logger = new jInMemoryLogger();
        jLogger::addLogger($this->logger);
        $this->emptyTable('classes');
    }

    function tearDown() {
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
        $this->assertEqual(count($this->logger->getLog()),0);
        $this->assertEqual($p->getInfo()->name , 'foo');
        
        $records = array(array(
            'name'=>'foo',
            'project_id'=>1,
            'file_id'=>1,
            'linenumber'=>2,
            'package_id'=>null,
            'subpackage_id'=>null,
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
        $this->assertEqual(count($this->logger->getLog()),0);
        
        $this->assertEqual($p->getInfo()->name , 'foo');
        $this->assertEqual($p->getInfo()->inheritsFrom , 'bar');
        $this->assertEqual($p->getInfo()->interfaces , array());
        
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
        $this->assertEqual(count($this->logger->getLog()),0);
        
        $this->assertEqual($p->getInfo()->name , 'foo');
        $this->assertEqual($p->getInfo()->inheritsFrom , '');
        $this->assertEqual($p->getInfo()->interfaces , array('bar'));
    }



}
