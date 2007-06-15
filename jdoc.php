<?php
/**
* @package     jDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2007 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

$dirnamefile = dirname(__FILE__).'/';
require_once($dirnamefile.'lib/jCmdUtils.class.php');
require_once($dirnamefile.'lib/jBuildUtils.lib.php');
require_once($dirnamefile.'jDocConfig.class.php');
require_once($dirnamefile.'jParser.class.php');
require_once($dirnamefile.'jInfos.class.php');
require_once($dirnamefile.'jDoc.class.php');
require_once($dirnamefile.'jLogger.class.php');



$sws = array('-v'=>false, '-x'=>1);
$params = array('sourcepath'=>true);

try{
    list($switches, $parameters) = jCmdUtils::getOptionsAndParams($_SERVER['argv'], $sws, $params);
}catch(Exception $e){
    die($e->getMessage());
}


$gConfig = new jDocConfig();
if(isset($switches['-x'])){
    $gConfig->setExcludedFiles(explode(',',$switches['-x']));
}else{
    $gConfig->setExcludedFiles(array('.svn','CVS'));
}


jLogger::addLogger(new jInMemoryLogger());
if(isset($switches['-v'])){
    jLogger::addLogger(new jConsoleLogger());
}

$docparser = jDoc::getInstance();
$docparser->setConfig($gConfig);
$docparser->run($parameters['sourcepath']);



/**
 *
 */
class jClassDiagram {


}





?>