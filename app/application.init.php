<?php
/**
* @package   rarangi
* @subpackage
* @author    Laurent Jouanneau
* @copyright 2008-2011 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
$appPath = dirname (__FILE__).'/';
require ($appPath.'../lib/jelix/init.php');

jApp::initPaths(
    $appPath
    //$appPath.'www/',
    //$appPath.'var/',
    //$appPath.'var/log/',
    //$appPath.'var/config/',
    //$appPath.'scripts/'
);
jApp::setTempBasePath(realpath($appPath.'../temp/rarangi/').'/');
