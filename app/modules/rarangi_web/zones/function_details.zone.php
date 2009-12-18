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

        $this->_tpl->assign('project', $project);

        $dao = jDao::get('rarangi~functions');
        $func = $dao->getByName($project->id, $functionname);
        $this->_tpl->assign('func',$func);
        $this->param('toReturn')->functionRecord = $func;

        if ($func) {
            if ($func->links)
                $func->links = unserialize($func->links);

            if ($func->see)
                $func->see = unserialize($func->see);

            if ($func->uses)
                $func->uses = unserialize($func->uses);

            if ($func->changelog)
                $func->changelog = unserialize($func->changelog);

            if ($func->user_tags)
                $func->user_tags = unserialize($func->user_tags);

            $rs_func_params = jDao::get('rarangi~function_parameters')->findByFunction($func->id);
            $func_params = array();
            foreach ($rs_func_params as $p) {
                $func_params[] = $p;
            }
            $this->_tpl->assign('function_parameters', $func_params);
        }
    }
}
