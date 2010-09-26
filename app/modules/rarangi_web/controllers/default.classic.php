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
        $tpl->assign('projectslist', jDao::get('rarangi~projects')->findAll());
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
        $dao = jDao::get('rarangi~projects');
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
            $dao_authors = jDao::get('rarangi~authors');
            $authors = $dao_authors->findByProject($project->id);
            $tpl->assign('authors', $authors);
            
            // Get some stats on souce code
            $dao_files = jDao::get('rarangi~files');
            $files_counter = $dao_files->countByProject($project->id, 0);
            $tpl->assign('files_counter', $files_counter);
            
            $dao_files_content = jDao::get('rarangi~files_content');
            $lines_counter = $dao_files_content->countByProject($project->id);
            $tpl->assign('lines_counter', $lines_counter);
            
            // Get some stats on components
            $dao_classes = jDao::get('rarangi~classes');
            $classes_counter = $dao_classes->countByProject($project->id);
            $tpl->assign('classes_counter', $classes_counter);
            
            $dao_functions = jDao::get('rarangi~functions');
            $functions_counter = $dao_functions->countByProject($project->id);
            $tpl->assign('functions_counter', $functions_counter);
            
            $dao_packages = jDao::get('rarangi~packages');
            $packages_counter = $dao_packages->countByProject($project->id);
            $tpl->assign('packages_counter', $packages_counter);

            $tpl->assign('errors_counter', jDao::get('rarangi~errors')->countByProject($project->id));

        }
        
        $resp->body->assign('MAIN', $tpl->fetch('projects_details'));

        return $resp;
    }

    function errors() {
        $resp = $this->getResponse('html');

        $criteria = $this->param('criteria');
        $projectname = $this->param('project');
        $resp->title = jLocale::get('default.page.project.errors.title', array($projectname));

        // Get project
        $dao = jDao::get('rarangi~projects');
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

            $dao_errors = jDao::get('rarangi~errors');
            switch($criteria) {
                case 'all':
                    $list = $dao_errors->findByProject($project->id);
                    break;
                case 'error':
                case 'warning':
                case 'notice':
                    $list= $dao_errors->findByType($project->id, $criteria);
                    break;
                default:
                    $list= $dao_errors->findErrorWarningByProject($project->id);
                    $criteria = '';
            }
            $tpl->assign('criteria', $criteria);
            $tpl->assign('errors', $list);
        }
        
        $resp->body->assign('MAIN', $tpl->fetch('errors'));

        return $resp;
    }

    /**
    * Display the help page
    */
    function help() {
        $resp = $this->getResponse('html');
        $resp->title = jLocale::get('default.page.help.title');

        $resp->body->assignZone('BREADCRUMB', 'location_breadcrumb', array(
                    'mode' => 'help'));
        $tpl = new jTpl();
        $resp->body->assign('MAIN', $tpl->fetch('help'));
        
        return $resp;
    }

    /**
    * Display the about rarangi page
    */
    function about() {
        $resp = $this->getResponse('html');
        $resp->title = jLocale::get('default.page.about.title');

        $resp->body->assignZone('BREADCRUMB', 'location_breadcrumb', array(
                    'mode' => 'about'));
        $tpl = new jTpl();
        $resp->body->assign('MAIN', $tpl->fetch('about'));
        
        return $resp;
    }
}
