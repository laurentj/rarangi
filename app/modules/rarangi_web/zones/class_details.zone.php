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

class class_detailsZone extends jZone {
    protected $_tplname = 'class_details';

    protected function _prepareTpl() {

        $project = $this->param('project');

        if (!$project) {
            $this->_tpl->assign('project', $GLOBALS['currentproject']);
            $project = $GLOBALS['currentproject'];
        }

        $classname = $this->param('classname');
        $isInterface = $this->param('isInterface', false);

        $compInfo = jClasses::getService('rarangi_web~raComponentInfo');
        $class = $compInfo->getClass($project, $classname, $isInterface);

        $this->_tpl->assign('class',$class);

        if ($isInterface)
            $this->param('toReturn')->interfaceRecord = $class;
        else
            $this->param('toReturn')->classRecord = $class;

        if ($class) {
            $this->_tpl->assign('relations', $compInfo->getRelations($class));
        }
    }
}
