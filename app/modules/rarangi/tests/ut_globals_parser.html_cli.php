<?php
/**
* @package     rarangi
* @subpackage  tests
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007-2009 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

require_once( dirname(__FILE__).'/parser_test.lib.php');

class ut_globals_parser extends jUnitTestCaseDb {
    protected $logger;
    protected $parserInfo;
    
    function setUp() {
        $logger = new raLogger();
        $this->logger = new raInMemoryLogger();
        $logger->addLogger($this->logger);

        $project = new ut_project_test($logger);

        $this->parserInfo = new raParserInfo($project, 'project/test.php','project','test.php');
        $logger->setCurrentParserInfo($this->parserInfo);
        
        $this->emptyTable('globals');
        $this->emptyTable('globals_authors');
        $this->emptyTable('packages');
        jDb::getConnection($this->dbProfile)->exec('ALTER TABLE `packages`  AUTO_INCREMENT =1');
    }

    function tearDown() {

    }




    function testSimpleVariable() {
        $content = "<?php /**
 * dummy file description
 */

/**
* this is a simple variable
*/
\$bar;
";

        $p = new ut_file_parser_test($content, $this->parserInfo);
        $p->parse();

        $this->assertEqual($p->getParserInfo()->currentLine(), 9);

        $log = $this->logger->getLog();
        $this->assertEqual(count($log['error']),0);
        $this->assertEqual(count($log['warning']),0);
        $this->assertEqual(count($log['notice']),0);

        $this->assertTableHasNRecords('globals', 1);
        $this->assertTableContainsRecords('globals', array(
            array(
                'name'=>'bar',
                'project_id'=>$this->parserInfo->getProjectId(),
                'package_id'=>1,
                'file_id'=>$p->getDescriptor()->fileId,
                'line_start'=>8,
                'datatype'=>'',
                'default_value'=>'',
                'type'=>0,
                'short_description'=>'this is a simple variable',
                'description'=>''
            )
        ), false);
        $records = array(array(
            'id'=>1,
            'name'=>'_unknown',
            'project_id'=>$this->parserInfo->getProjectId(),
            ));
        $this->assertTableContainsRecords('packages', $records);
    }

    function testGlobalVariable() {
        $content = "<?php /**
 * dummy file description
 */

/**
* this is a simple variable
*/
\$GLOBALS['bar']=true;
";

        $p = new ut_file_parser_test($content, $this->parserInfo);
        $p->parse();

        $this->assertEqual($p->getParserInfo()->currentLine(), 9);

        $log = $this->logger->getLog();
        $this->assertEqual(count($log['error']),0);
        $this->assertEqual(count($log['warning']),0);
        $this->assertEqual(count($log['notice']),0);

        $this->assertTableHasNRecords('globals', 1);
        $this->assertTableContainsRecords('globals', array(
            array(
                'name'=>'bar',
                'project_id'=>$this->parserInfo->getProjectId(),
                'package_id'=>1,
                'file_id'=>$p->getDescriptor()->fileId,
                'line_start'=>8,
                'datatype'=>'',
                'default_value'=>'true',
                'type'=>0,
                'short_description'=>'this is a simple variable',
                'description'=>''
            )
        ), false);
    }


    
    function testSimpleVariable2() {

$content =
"<?php /**
*/

/**
* @var string \$baz this is an other description
*/
\$baz =\"lorem ipsum\";

/**
* @const integer bli bla blo
*/
define('bla', 4);

/**
* @const integer #zoop oh, zoop variable ! with spaces in the value
*/
define('zoop', myfunc ( 'toto' ) );

\$other;

/**
* @var myClass
*/
\$anObject;

/**
* the data container
* @var jFormsDataContainer
*/
\$GLOBALS['container'] = null;

/**
* unknown. should be ignored.
* @var object
*/
\$GLOBALS[\$bli] = null;

/**
* a result stored in a global var
* @var string
*/
\$GLOBALS['result'] = myfunc(\$zip, 'zap');

";

        $p = new ut_file_parser_test($content, $this->parserInfo);
        $p->parse();

        $this->assertEqual($p->getParserInfo()->currentLine(), 44);

        $log = $this->logger->getLog();
        if (!$this->assertEqual(count($log['error']),0))
            $this->dump($log['error']);
        if (!$this->assertEqual(count($log['warning']),1))
            $this->dump($log['warning']);
        else {
            // the warning about $GLOBALS['bli']
            $this->assertEqual($log['warning'][0][3], 36);
        }
        $this->assertEqual(count($log['notice']),0);

        $this->assertTableHasNRecords('globals', 7);
        $this->assertTableContainsRecords('globals', array(
            array(
                'name'=>'baz',
                'project_id'=>$this->parserInfo->getProjectId(),
                'file_id'=>$p->getDescriptor()->fileId,
                'line_start'=>7,
                'datatype'=>'string',
                'default_value'=>'"lorem ipsum"',
                'type'=>0,
                'short_description'=>'this is an other description',
                'description'=>''
            )
        ), false);

        $this->assertTableContainsRecords('globals', array(
            array(
                'name'=>'bla',
                'project_id'=>$this->parserInfo->getProjectId(),
                'file_id'=>$p->getDescriptor()->fileId,
                'line_start'=>12,
                'datatype'=>'integer',
                'default_value'=>'4',
                'type'=>2,
                'short_description'=>'bli bla blo',
                'description'=>''
            )
        ), false);

        $this->assertTableContainsRecords('globals', array(
            array(
                'name'=>'zoop',
                'project_id'=>$this->parserInfo->getProjectId(),
                'file_id'=>$p->getDescriptor()->fileId,
                'line_start'=>17,
                'datatype'=>'integer',
                'default_value'=>'myfunc ( \'toto\' )',
                'type'=>2,
                'short_description'=>'oh, zoop variable ! with spaces in the value',
                'description'=>''
            )
        ), false);

        $this->assertTableContainsRecords('globals', array(
            array(
                'name'=>'other',
                'project_id'=>$this->parserInfo->getProjectId(),
                'file_id'=>$p->getDescriptor()->fileId,
                'line_start'=>19,
                'datatype'=>'',
                'default_value'=>'',
                'type'=>0,
                'short_description'=>'',
                'description'=>''
            )
        ), false);

        $this->assertTableContainsRecords('globals', array(
            array(
                'name'=>'anObject',
                'project_id'=>$this->parserInfo->getProjectId(),
                'file_id'=>$p->getDescriptor()->fileId,
                'line_start'=>24,
                'datatype'=>'myClass',
                'default_value'=>'',
                'type'=>0,
                'short_description'=>'',
                'description'=>''
            )
        ), false);

        $this->assertTableContainsRecords('globals', array(
            array(
                'name'=>'container',
                'project_id'=>$this->parserInfo->getProjectId(),
                'file_id'=>$p->getDescriptor()->fileId,
                'line_start'=>30,
                'datatype'=>'jFormsDataContainer',
                'default_value'=>'null',
                'type'=>0,
                'short_description'=>'the data container',
                'description'=>''
            )
        ), false);
        
        $this->assertTableContainsRecords('globals', array(
            array(
                'name'=>'result',
                'project_id'=>$this->parserInfo->getProjectId(),
                'file_id'=>$p->getDescriptor()->fileId,
                'line_start'=>42,
                'datatype'=>'string',
                'default_value'=>"myfunc(\$zip, 'zap')",
                'type'=>0,
                'short_description'=>'a result stored in a global var',
                'description'=>''
            )
        ), false);
    }
}
