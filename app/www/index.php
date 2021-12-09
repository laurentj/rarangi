<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008-2021 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
require ('../application.init.php');
require (JELIX_LIB_CORE_PATH.'request/jClassicRequest.class.php');

checkAppOpened();
jApp::loadConfig('index/config.ini.php');
jApp::setCoord(new jCoordinator());
jApp::coord()->process(new jClassicRequest());
