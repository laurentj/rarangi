<?php
/**
* @package     rarangi
* @subpackage  
* @author      Laurent Jouanneau
* @copyright   2010 Laurent Jouanneau
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

class rarangiModuleInstaller extends jInstallerModule {

    function install() {
      $this->execSQLScript('install');
    }
}