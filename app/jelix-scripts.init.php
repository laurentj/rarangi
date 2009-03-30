<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
require (dirname(__FILE__).'/../lib/jelix/init.php');

define ('JELIX_APP_PATH', dirname (__FILE__).DIRECTORY_SEPARATOR); // don't change

define ('JELIX_APP_TEMP_PATH',    realpath(JELIX_APP_PATH.'../temp/rarangi-jelix-scripts/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_VAR_PATH',     realpath(JELIX_APP_PATH.'./var/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_LOG_PATH',     realpath(JELIX_APP_PATH.'./var/log/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_CONFIG_PATH',  realpath(JELIX_APP_PATH.'./var/config/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_WWW_PATH',     realpath(JELIX_APP_PATH.'./www/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_CMD_PATH',     realpath(JELIX_APP_PATH.'./scripts/').DIRECTORY_SEPARATOR);


define ('JELIX_APP_TEMP_CLI_PATH',    realpath(JELIX_APP_PATH.'../temp/rarangi-cli/').DIRECTORY_SEPARATOR);
define ('JELIX_APP_REAL_TEMP_PATH',    realpath(JELIX_APP_PATH.'../temp/rarangi/').DIRECTORY_SEPARATOR);