<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @contributor    Loic Mathaud
* @copyright 2008 Laurent Jouanneau, 2009 Loic Mathaud
* @link      http://forge.jelix.org/projects/rarangi/
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
class defaultCtrl extends jController {
    /**
    * Display the list of the avaiblable projects
    */
    function index() {
        $resp = $this->getResponse('html');
        $resp->title = jLocale::get('default.page.home.title');
        $resp->bodyTagAttributes = array('id' => 'home');
        
        $resp->body->assignZone('BREADCRUMB', 'location_breadcrumb', array('mode' => 'home'));
        $resp->body->assign('MENUBAR', '<h1>'. jLocale::get('default.app.title') .'</h1>');

        $tpl = new jTpl();
        $tpl->assign('projectslist', jDao::get('projects')->findAll());
        $resp->body->assign('MAIN', $tpl->fetch('projects_list'));
        
        return $resp;
    }

    /**
    * Display the main page of a project
    */
    function project() {
        $resp = $this->getResponse('html');

        $projectname = $this->param('project');
        $resp->title = jLocale::get('default.page.project.home.title', array($projectname));

        // Get project
        $dao = jDao::get('projects');
        $project = $dao->getByName($projectname);

        $tpl = new jTpl();
        $tpl->assign('project',$project);
        $tpl->assign('projectname',$projectname);
        
        if (!$project) {
            $resp->setHttpStatus('404','Not found');
        } else {
            $resp->body->assignZone('BREADCRUMB', 'location_breadcrumb', array(
                    'mode' => 'projecthome',
                    'projectname' => $projectname));
            $resp->body->assignZone('MENUBAR', 'project_menubar', array(
                    'project' => $project,
                    'mode' => 'browse'));

            // Get authors of the project
            $dao_authors = jDao::get('authors');
            $authors = $dao_authors->findByProject($project->id);
            $tpl->assign('authors', $authors);
        }
        
        $resp->body->assign('MAIN', $tpl->fetch('projects_details'));

        return $resp;
    }
}
