<?php
/**
* @package   rarangi
* @subpackage rarangi_static
* @author    Laurent Jouanneau
* @copyright 2011 totr
* @link      totr.lu
* @license   http://www.gnu.org/licenses/gpl.html GPL
*/


class rarangi_staticModuleInstaller extends jInstallerModule {

    function install() {
        //if ($this->firstDbExec())
        //    $this->execSQLScript('sql/install');

        /*if ($this->firstExec('acl2')) {
            jAcl2DbManager::addSubject('my.subject', 'rarangi_static~acl.my.subject', 'subject.group.id');
            jAcl2DbManager::addRight('admins', 'my.subject'); // for admin group
        }
        */
    }
}