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

class ut_file_parser extends jUnitTestCaseDb {
    protected $logger;
    protected $parserInfo;
    
    function setUp() {
        $logger = new raLogger();
        $this->logger = new raInMemoryLogger();
        $logger->addLogger($this->logger);

        $project = new ut_project_test($logger);

        $this->parserInfo = new raParserInfo($project, 'project/test.php','project','test.php');
        $logger->setCurrentParserInfo($this->parserInfo);
        
    }

    function tearDown() {

    }
    
    function testFunctionCall() {
        $content = "<?php /**
 * dummy file description
 */

//it should be ignored, at least, for globals statement
require(\$dirname.'foo');
foo();

";

        $p = new ut_file_parser_test($content, $this->parserInfo);
        $p->parse();

        $this->assertEqual($p->getParserInfo()->currentLine(), 9);

        $log = $this->logger->getLog();
        if(!$this->assertEqual(count($log['error']),0))
            $this->dump($log['error']);
        $this->assertEqual(count($log['warning']),0);
        $this->assertEqual(count($log['notice']),0);

        $this->assertTableHasNRecords('globals', 0);
    }

    function testInlineHtml() {
        $content = '<?php /**
 */

?>

<html xml:lang="<?php echo $check->messages->getLang(); ?>">
<head>

';

        $p = new ut_file_parser_test($content, $this->parserInfo);
        $p->parse();

        $this->assertEqual($p->getParserInfo()->currentLine(), 8);

        $log = $this->logger->getLog();
        if(!$this->assertEqual(count($log['error']),0))
            $this->dump($log['error']);
        $this->assertEqual(count($log['warning']),0);
        $this->assertEqual(count($log['notice']),0);

        $this->assertTableHasNRecords('globals', 0);
    }
}
