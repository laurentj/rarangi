<?php
/**
* @package     jDoc
* @subpackage  tests
* @author      Jouanneau Laurent
* @contributor
* @copyright   2007 Jouanneau laurent
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

$test = &new GroupTest('All tests');
$test->addTestFile('ut_logger.php');

$test->run(new TextReporter());
?>