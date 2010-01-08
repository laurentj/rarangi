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

        $dao = jDao::get('rarangi~globals');
        $comp = $dao->getByName($project->id, $compname, ($isConst?2:0));

        if ($isConst) {
            $this->param('toReturn')->constRecord = $comp;
            $this->_tplname = 'constants_details';
        }
        else {
            $this->param('toReturn')->globalRecord = $comp;
            $this->_tplname = 'globals_details';
        }

        if ($comp) {
            if ($comp->links)
                $comp->links = unserialize($comp->links);

            if ($comp->see)
                $comp->see = unserialize($comp->see);

            if ($comp->uses)
                $comp->uses = unserialize($comp->uses);

            if ($comp->changelog)
                $comp->changelog = unserialize($comp->changelog);

            if ($comp->user_tags)
                $comp->user_tags = unserialize($comp->user_tags);
        }
        $this->_tpl->assign('comp',$comp);
    }
}
