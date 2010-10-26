<?php
/*
* @package   rarangi
* @subpackage main.module
* @author    Laurent Jouanneau
* @copyright 2010 Laurent Jouanneau
* @link      http://rarangi.org
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

class mainModuleInstaller extends jInstallerModule {

    function install() {
        //if ($this->firstDbExec())
        //    $this->execSQLScript('sql/install');

        /*if ($this->firstExec('acl2')) {
            jAcl2DbManager::addSubject('my.subject', 'main~acl.my.subject');
            jAcl2DbManager::addRight(1, 'my.subject'); // for admin group
        */
    }
}