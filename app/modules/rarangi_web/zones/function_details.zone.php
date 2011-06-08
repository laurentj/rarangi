<?php
/**
* @package   app
* @subpackage rarangi
* @author    Laurent Jouanneau
* @contributor
* @copyright 2008-2009 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi
* @licence   http://www.gnu.org/licenses/gpl.html GNU General Public Licence
*/

class function_detailsZone extends jZone {
    protected $_tplname = 'function_details';

    protected function _prepareTpl() {

        $project = $this->param('project');

        if (!$project) {
            $this->_tpl->assign('project', $GLOBALS['currentproject']);
            $project = $GLOBALS['currentproject'];
        }

        $functionname = $this->param('functionname');
        $compInfo = jClasses::getService('rarangi_web~raComponentInfo');
        $func = $compInfo->getFunction($project, $functionname);

        $this->_tpl->assign('project', $project);
        $this->_tpl->assign('func',$func);
        $this->param('toReturn')->functionRecord = $func;
    }
}
