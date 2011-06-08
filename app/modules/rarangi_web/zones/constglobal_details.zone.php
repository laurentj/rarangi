<?php
/**
* @package   app
* @subpackage rarangi
* @author    Laurent Jouanneau
* @contributor
* @copyright 2010 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi
* @licence   http://www.gnu.org/licenses/gpl.html GNU General Public Licence
*/

class constglobal_detailsZone extends jZone {

    protected function _prepareTpl() {

        $isConst = $this->param('is_const', false);
        $project = $this->param('project');

        if (!$project) {
            $this->_tpl->assign('project', $GLOBALS['currentproject']);
            $project = $GLOBALS['currentproject'];
        }

        $compname = $this->param('compname');

        $compInfo = jClasses::getService('rarangi_web~raComponentInfo');
        $comp = $compInfo->getConstGlobal($project, $compname, $isConst);

        if ($isConst) {
            $this->param('toReturn')->constRecord = $comp;
            $this->_tplname = 'constants_details';
        }
        else {
            $this->param('toReturn')->globalRecord = $comp;
            $this->_tplname = 'globals_details';
        }

        $this->_tpl->assign('comp',$comp);
    }
}
