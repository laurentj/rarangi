<?php
/**
* @package   app
* @subpackage rarangi
* @author    Laurent Jouanneau
* @contributor    Loic Mathaud
* @copyright 2008 Laurent Jouanneau, 2008 Loic Mathaud
* @link      http://forge.jelix.org/projects/rarangi
* @licence   http://www.gnu.org/licenses/gpl.html GNU General Public Licence
*/

class project_menubarZone extends jZone {
    protected $_tplname = 'project_menubar';

    protected function _prepareTpl() {
        if (!$this->param('project')) {
            $this->_tpl->assign('project', $GLOBALS['currentproject']);
        }
        if (!$this->param('mode')) {
            $this->_tpl->assign('mode', 'browse');
        }
    }
}
