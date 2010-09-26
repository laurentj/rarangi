<?php
/**
* @package   tests
* @subpackage 
* @author    yourname
* @copyright 2010 yourname
* @link      http://www.yourwebsite.undefined
* @license    All right reserved
*/

require_once ('../application-cli.init.php');

checkAppOpened();

require_once (JELIX_LIB_CORE_PATH.'jCmdlineCoordinator.class.php');

require_once (JELIX_LIB_CORE_PATH.'request/jCmdLineRequest.class.php');

$config_file = 'cmdline/jelix.ini.php';

$jelix = new jCmdlineCoordinator($config_file);
$jelix->process(new jCmdLineRequest());

