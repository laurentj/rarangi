<?php
/**
* @package   jphpdoc
* @subpackage 
* @author    yourname
* @copyright 2008 yourname
* @link      http://www.yourwebsite.undefined
* @licence    All right reserved
*/

require ('../application.init.php');
require (JELIX_LIB_CORE_PATH.'request/jClassicRequest.class.php');

$config_file = 'index/testconfig.ini.php';

$jelix = new jCoordinator($config_file);
$jelix->process(new jClassicRequest());

