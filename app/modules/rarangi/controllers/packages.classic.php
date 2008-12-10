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
    * display the list of packages
    */
    function index() {
        $rep = $this->getResponse('html');
        $tpl = $this->_prepareTpl();
        
        $project = $tpl->get('project');
        $projectname = $tpl->get('projectname');
        $rep->title = 'Packages for project '. $projectname;
        
        if (!$project) {
            $rep->setHttpStatus('404','Not found');
        } else {
            $rep->body->assignZone('SUBMENUBAR', 'project_menubar', array(
                                                            'project'=>$project));
        }
        
        // Get packages
        $dao = jDao::get('packages');
        $packages = $dao->findByProject($project->id);

        if (!$packages) {
            $rep->setHttpStatus('404', 'Not found');
        }
        $tpl->assign('packages', $packages);
        
        $rep->body->assign('MAIN', $tpl->fetch('packages_list'));
        
        return $rep;
    }
    
    /**
    * display details of a package and the list of subpackages
    */
    function details() {
        $rep = $this->getResponse('html');
        $tpl = $this->_prepareTpl();
        
        $project = $tpl->get('project');
        $projectname = $tpl->get('projectname');
        $packagename = $this->param('package');
        $tpl->assign('packagename', $packagename);
        $rep->title = 'Details for package '. $packagename .' in project '. $projectname;
        
        if (!$project) {
            $rep->setHttpStatus('404','Not found');
        } else {
            $rep->body->assignZone('SUBMENUBAR', 'project_menubar', array(
                                                            'project'=>$project));
        }
        
        // Get package
        $dao = jDao::get('packages');
        $package = $dao->getByName($project->id, $packagename, 0);
        if (!$package) {
            $rep->setHttpStatus('404', 'Not found');
        }
        $tpl->assign('package', $package);

        // Get classes
        $dao_classes = jDao::get('classes');
        $classes = $dao_classes->findByPackage($project->id, $package->id);
        $tpl->assign('classes', $classes);
        
        // Get functions TODO
        $functions = false;
        $tpl->assign('functions', $functions);

        $rep->body->assign('MAIN', $tpl->fetch('package_details'));
        return $rep;
    }

    /**
    * display the list of classes of a package
    * TODO
    */
    function classes() {
        $rep = $this->getResponse('html');
        $tpl = $this->_prepareTpl();

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
