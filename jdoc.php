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
require($dirnamefile.'lib/jCmdUtils.class.php');
require($dirnamefile.'lib/jBuildUtils.lib.php');
require($dirnamefile.'jDocConfig.class.php');
require($dirnamefile.'parsers/jFileParser.class.php');
require($dirnamefile.'jInfos.class.php');
require($dirnamefile.'jDoc.class.php');
require($dirnamefile.'jLogger.class.php');


//------ read all options in the command line
$sws = array('-v'=>false, '-x'=>1);
$params = array('sourcepath'=>true);

try{
    list($switches, $parameters) = jCmdUtils::getOptionsAndParams($_SERVER['argv'], $sws, $params);
}catch(Exception $e){
    die($e->getMessage());
}

//------- setup the configuration
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


//------- prepare and launch the parsing

$docparser = jDoc::getInstance();
$docparser->setConfig($gConfig);

try {
    $docparser->run($parameters['sourcepath']);
}catch(Exception $e){
    echo "\n ERROR !!!!\n-->", $e->getMessage();
}


//------- process results

// @todo





?>