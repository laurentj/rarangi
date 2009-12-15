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

class ut_tag_ignore extends jUnitTestCaseDb {
    protected $logger,
    $parserInfo;
    
    function setUp() {
        $logger = new raLogger();
        $this->logger = new raInMemoryLogger();
        $logger->addLogger($this->logger);

        $project = new ut_project_test($logger);
        $project->emptyPackageCache();
        $this->parserInfo = new raParserInfo($project, '/home/foo/project','/home/foo/project/bar/test.php','test.php');

        $this->emptyTable('authors');
        $this->emptyTable('classes');
        $this->emptyTable('classes_authors');
        $this->emptyTable('class_properties');
        $this->emptyTable('class_methods');
        $this->emptyTable('files');
        $this->emptyTable('files_authors');
        $this->emptyTable('methods_authors');
        $this->emptyTable('method_parameters');
        $this->emptyTable('functions');
        $this->emptyTable('functions_authors');
        $this->emptyTable('function_parameters');
        $this->emptyTable('interface_class');
        $this->emptyTable('packages');
    }

    function tearDown() {

    }
    
    function testFileIgnore() {
        $content = '<?php
/**
* @ignore
* @package test
*/

/**
 *
 */
class bar{
  function foo() { }
}

/**
 *
 */
interface iBar {
  function bla();
}

function superTest($plop) {
    
}

?>';
        $p = new ut_file_parser_test($content, $this->parserInfo);
        $p->parse();
        $this->assertTrue($p->getDescriptor()->ignore);

        $log = $this->logger->getLog();
        if(!$this->assertEqual(count($log['error']),0))
            $this->dump($log['error'],'raLogger::error');
        if(!$this->assertEqual(count($log['warning']),0))
            $this->dump($log['warning'],'raLogger::warning');
        if(!$this->assertEqual(count($log['notice']),0))
            $this->dump($log['notice'],'raLogger::notice');

        $this->assertTableIsEmpty('authors');
        $this->assertTableIsEmpty('classes');
        $this->assertTableIsEmpty('classes_authors');
        $this->assertTableIsEmpty('class_properties');
        $this->assertTableIsEmpty('class_methods');
        $this->assertTableIsEmpty('files');
        $this->assertTableIsEmpty('files_authors');
        $this->assertTableIsEmpty('methods_authors');
        $this->assertTableIsEmpty('method_parameters');
        $this->assertTableIsEmpty('functions');
        $this->assertTableIsEmpty('functions_authors');
        $this->assertTableIsEmpty('function_parameters');
        $this->assertTableIsEmpty('interface_class');
        $this->assertTableIsEmpty('packages');
        $this->assertTableHasNRecords('projects',1);
    }


    function testClassIgnore() {
        $content = '<?php
/**
* @package test2
*/

/**
 * @ignore
 */
class bar{
  function foo() { }
  function hello($name) { }
}

/**
 *
 */
interface iBar {
  function bla();
}

function superTest($plop) {
    
}

?>';
        $p = new ut_file_parser_test($content, $this->parserInfo);
        $p->parse();
        $this->assertFalse($p->getDescriptor()->ignore);

        $log = $this->logger->getLog();
        if(!$this->assertEqual(count($log['error']),0))
            $this->dump($log['error'],'raLogger::error');
        if(!$this->assertEqual(count($log['warning']),0))
            $this->dump($log['warning'],'raLogger::warning');
        if(!$this->assertEqual(count($log['notice']),0))
            $this->dump($log['notice'],'raLogger::notice');

        $this->assertTableIsEmpty('authors');
        $this->assertTableHasNRecords('classes',1);
        $this->assertTableIsEmpty('classes_authors');
        $this->assertTableIsEmpty('class_properties');
        $this->assertTableHasNRecords('class_methods',1);


        $records = array(array(
            'id'=>$p->getDescriptor()->fileId,
            'project_id'=>$this->parserInfo->getProjectId(),
            'fullpath'=>'bar/test.php',
            'dirname'=>'bar',
            'filename'=>'test.php',
            'description'=>'',
            'short_description'=>'',
            ));
        $this->assertTableContainsRecords('files', $records);

        $this->assertTableIsEmpty('files_authors');
        $this->assertTableIsEmpty('methods_authors');
        $this->assertTableIsEmpty('method_parameters');
        $this->assertTableHasNRecords('functions',1);
        $this->assertTableIsEmpty('functions_authors');
        $this->assertTableHasNRecords('function_parameters',1);
        $this->assertTableIsEmpty('interface_class');
        $this->assertTableHasNRecords('packages',1);
        $this->assertTableHasNRecords('projects',1);
    }

    function testInterfaceIgnore() {
        $content = '<?php
/**
* @package test
*/

/**
 *
 */
class bar{
  function foo() { }
  function hello($name) { }
}

/**
 * @ignore
 */
interface iBar {
  function bla();
}

function superTest($plop) {
    
}

?>';
        $p = new ut_file_parser_test($content, $this->parserInfo);
        $p->parse();
        $this->assertFalse($p->getDescriptor()->ignore);

        $log = $this->logger->getLog();
        if(!$this->assertEqual(count($log['error']),0))
            $this->dump($log['error'],'raLogger::error');
        if(!$this->assertEqual(count($log['warning']),0))
            $this->dump($log['warning'],'raLogger::warning');
        if(!$this->assertEqual(count($log['notice']),0))
            $this->dump($log['notice'],'raLogger::notice');

        $this->assertTableIsEmpty('authors');
        $this->assertTableHasNRecords('classes',1);
        $this->assertTableIsEmpty('classes_authors');
        $this->assertTableIsEmpty('class_properties');
        $this->assertTableHasNRecords('class_methods',2);

        $records = array(array(
            'id'=>$p->getDescriptor()->fileId,
            'project_id'=>$this->parserInfo->getProjectId(),
            'fullpath'=>'bar/test.php',
            'dirname'=>'bar',
            'filename'=>'test.php',
            'description'=>'',
            'short_description'=>'',
            ));
        $this->assertTableContainsRecords('files', $records);

        $this->assertTableIsEmpty('files_authors');
        $this->assertTableIsEmpty('methods_authors');
        $this->assertTableHasNRecords('method_parameters',1);
        $this->assertTableHasNRecords('functions',1);
        $this->assertTableIsEmpty('functions_authors');
        $this->assertTableHasNRecords('function_parameters',1);
        $this->assertTableIsEmpty('interface_class');
        $this->assertTableHasNRecords('packages',1);
        $this->assertTableHasNRecords('projects',1);
    }

    function testFunctionIgnore() {
        $content = '<?php
/**
* @package test
*/

/**
 *
 */
class bar{
  function foo() { }
  function hello($name) { }
}

/**
 *
 */
interface iBar {
  function bla();
}

/**
 *  @ignore
 */
function superTest($plop) {
    
}

?>';
        $p = new ut_file_parser_test($content, $this->parserInfo);
        $p->parse();
        $this->assertFalse($p->getDescriptor()->ignore);

        $log = $this->logger->getLog();
        if(!$this->assertEqual(count($log['error']),0))
            $this->dump($log['error'],'raLogger::error');
        if(!$this->assertEqual(count($log['warning']),0))
            $this->dump($log['warning'],'raLogger::warning');
        if(!$this->assertEqual(count($log['notice']),0))
            $this->dump($log['notice'],'raLogger::notice');

        $this->assertTableIsEmpty('authors');
        $this->assertTableHasNRecords('classes',2);
        $this->assertTableIsEmpty('classes_authors');
        $this->assertTableIsEmpty('class_properties');
        $this->assertTableHasNRecords('class_methods',3);

        $records = array(array(
            'id'=>$p->getDescriptor()->fileId,
            'project_id'=>$this->parserInfo->getProjectId(),
            'fullpath'=>'bar/test.php',
            'dirname'=>'bar',
            'filename'=>'test.php',
            'description'=>'',
            'short_description'=>'',
            ));
        $this->assertTableContainsRecords('files', $records);

        $this->assertTableIsEmpty('files_authors');
        $this->assertTableIsEmpty('methods_authors');
        $this->assertTableHasNRecords('method_parameters',1);
        $this->assertTableIsEmpty('functions');
        $this->assertTableIsEmpty('functions_authors');
        $this->assertTableIsEmpty('function_parameters');
        $this->assertTableIsEmpty('interface_class');
        $this->assertTableHasNRecords('packages',1);
        $this->assertTableHasNRecords('projects',1);
        
        $records = array(array(
            //'id'=>1,
            'project_id'=>$this->parserInfo->getProjectId(),
            'name'=>'test',
            ));
        $this->assertTableContainsRecords('packages', $records);
    }
}
