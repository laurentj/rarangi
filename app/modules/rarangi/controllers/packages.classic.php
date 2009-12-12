<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @contributor  Loic Mathaud
* @copyright 2008 Laurent Jouanneau, 2008 Loic Mathaud
* @link      http://forge.jelix.org/projects/rarangi/
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

class packagesCtrl extends jController {
    
    protected function _prepareTpl() {
        $tpl = new jTpl();
        $projectname = $this->param('project');
        $dao = jDao::get('projects');
        $project = $dao->getByName($projectname);
        
        $tpl->assign('project',$project);
        $tpl->assign('projectname',$projectname);
        
        return $tpl;
    }
    
    /**
    * Display the list of packages
    */
    function index() {
        $resp = $this->getResponse('html');
        $tpl = $this->_prepareTpl();
        
        $project = $tpl->get('project');
        $projectname = $tpl->get('projectname');
        $resp->title = jLocale::get('default.packages.title', array($projectname));
        
        if (!$project) {
            $resp->setHttpStatus('404','Not found');
        } else {
            $resp->body->assignZone('BREADCRUMB', 'location_breadcrumb', array(
                    'mode' => 'projectbrowse',
                    'projectname' => $projectname));
            $resp->body->assignZone('MENUBAR', 'project_menubar', array(
                    'project'=>$project,
                    'mode' => 'browse'));
        }
        
        // Get packages
        $dao = jDao::get('packages');
        $packages = $dao->findByProject($project->id);

        if (!$packages) {
            $resp->setHttpStatus('404', 'Not found');
        }
        $tpl->assign('packages', $packages);
        
        $resp->body->assign('MAIN', $tpl->fetch('packages_list'));
        
        return $resp;
    }
    
    /**
    * display details of a package and the list of subpackages
    */
    function details() {
        $resp = $this->getResponse('html');
        $tpl = $this->_prepareTpl();
        
        $project = $tpl->get('project');
        $projectname = $tpl->get('projectname');
        $packagename = $this->param('package');
        $tpl->assign('packagename', $packagename);
        $resp->title = jLocale::get('default.packages.details.title', array($packagename, $projectname));
        
        if (!$project) {
            $resp->setHttpStatus('404','Not found');
        } else {
            $resp->body->assignZone('BREADCRUMB', 'location_breadcrumb', array(
                    'mode' => 'projectbrowse',
                    'projectname' => $projectname));
            $resp->body->assignZone('MENUBAR', 'project_menubar', array(
                                                            'project'=>$project));
        }

        // Get package
        $dao = jDao::get('packages');
        $package = $dao->getByName($project->id, $packagename, 0);
        $tpl->assign('package', $package);

        if (!$package) {
            $resp->setHttpStatus('404', 'Not found');
            $tpl->assign('interfaces', null);
            $tpl->assign('classes', null);
            $tpl->assign('functions', null);
        }
        else {
            // Get interfaces
            $dao_classes = jDao::get('classes');
            $interfaces = $dao_classes->findByPackage($project->id, $package->id, 1);
            $tpl->assign('interfaces', $interfaces);
            
            // Get classes
            $classes = $dao_classes->findByPackage($project->id, $package->id, 0);
            $tpl->assign('classes', $classes);
            
            // Get functions
            $dao_functions = jDao::get('functions');
            $functions = $dao_functions->findByPackage($project->id, $package->id);
            $tpl->assign('functions', $functions);
        }
        $resp->body->assign('MAIN', $tpl->fetch('package_details'));
        
        return $resp;
    }

    /**
    * display the list of classes of a package
    * TODO
    */
    function classes() {
        $rep = $this->getResponse('html');
        $tpl = $this->_prepareTpl();
        $tpl->assign('forInterfaces', false);
        $rep->body->assign('MAIN', $tpl->fetch('classes_list'));
        return $rep;
    }

    /**
    * display the list of interfaces of a package
    * TODO
    */
    function interfaces() {
        $rep = $this->getResponse('html');
        $tpl = $this->_prepareTpl();
        $tpl->assign('forInterfaces', true);
        $rep->body->assign('MAIN', $tpl->fetch('classes_list'));
        return $rep;
    }

    /**
    * display the list of functions of a package
    * TODO
    */
    function functions() {
        $rep = $this->getResponse('html');
        $tpl = $this->_prepareTpl();

        $rep->body->assign('MAIN', $tpl->fetch('functions_list'));
        return $rep;
    }

}
