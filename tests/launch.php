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
require_once('simpletest_addons/junittestcase.class.php');


class myTextReporter extends TextReporter {

   function paintMessage($message) {
        echo $message,"\n";
   }
}



$test = &new GroupTest('All tests');
$test->addTestFile('ut_config.php');
$test->addTestFile('ut_parser_base.php');

$test->run(new myTextReporter());
?>