<?php
/**
* @package   app
* @subpackage jphpdoc
* @author    Laurent Jouanneau
* @copyright 2008 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/jphpdoc
* @licence   http://www.gnu.org/licenses/gpl.html GNU General Public Licence
*/

class project_menubarZone extends jZone {
    protected $_tplname='project_menubar';

    protected function _prepareTpl(){
        if(!$this->param('project'))
            $this->_tpl->assign('project', $GLOBALS['currentproject']);
    }
}
