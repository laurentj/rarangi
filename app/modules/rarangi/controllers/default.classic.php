<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi/
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
class defaultCtrl extends jController {
    /**
    * display the list of projects
    */
    function index() {
        $rep = $this->getResponse('html');
        $tpl = new jTpl();
        $tpl->assign('projectslist', jDao::get('projects')->findAll());
        $rep->body->assign('MAIN', $tpl->fetch('projects_list'));
        $rep->title = 'Project list';
        return $rep;
    }

    /**
    * display the main page of a project
    */
    function project() {
        $rep = $this->getResponse('html');

        $tpl = new jTpl();
        
        $projectname = $this->param('project');
        $rep->title = 'Project '.$projectname;

        $dao = jDao::get('projects');
        $project = $dao->getByName($projectname);
        
        $tpl->assign('project',$project);
        $tpl->assign('projectname',$projectname);
        
        if(!$project){
            $rep->setHttpStatus('404','Not found');
        }
        else {
            $rep->body->assignZone('SUBMENUBAR', 'project_menubar', array('project'=>$project));
        }

        $rep->body->assign('MAIN', $tpl->fetch('projects_details'));

        return $rep;
    }
}
