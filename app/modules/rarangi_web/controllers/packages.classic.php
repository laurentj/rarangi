<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @contributor  Loic Mathaud
* @copyright 2008 Laurent Jouanneau, 2008-2009 Loic Mathaud
* @link      http://forge.jelix.org/projects/rarangi/
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

class packagesCtrl extends jController {
    
    protected function _prepareTpl($resp, $title, $withPackage = false) {
        $tpl = new jTpl();
        $projectname = $this->param('project');
        $dao = jDao::get('rarangi~projects');
        $project = $dao->getByName($projectname);
        
        $tpl->assign('project',$project);
        $tpl->assign('projectname',$projectname);
        
        if ($withPackage) {
            $packagename = $this->param('package');
            $tpl->assign('packagename', $packagename);
            $resp->title = jLocale::get($title, array($packagename, $projectname));
        }
        else {
            $resp->title = jLocale::get($title, array($projectname));
        }

        $package = null;

        if (!$project) {
            $resp->setHttpStatus('404','Not found');
        } else {
            $resp->body->assignZone('BREADCRUMB',
                                    'location_breadcrumb',
                                    array(
                                        'mode' => 'projectbrowse',
                                        'projectname' => $projectname));

            $param = array('project'=>$project);
            if ($withPackage) {
                $param['mode'] = 'browse';
                $dao = jDao::get('rarangi~packages');
                $package = $dao->getByName($project->id, $packagename, 0);
            }

            $resp->body->assignZone('MENUBAR',
                                    'project_menubar',
                                    $param);
        }

        if ($withPackage) {
            if (!$package) {
                $resp->setHttpStatus('404', 'Not found');
            }
            else
                $tpl->assign('package', $package);
        }

        return $tpl;
    }
    
    /**
    * Display the list of packages
    */
    function index() {
        $resp = $this->getResponse('html');
        $tpl = $this->_prepareTpl($resp, 'default.packages.title');

        $project = $tpl->get('project');
        if ($project) {
            // Get packages
            $dao = jDao::get('rarangi~packages');
            $packages = $dao->findByProject($project->id);
        }
        else
            $packages = null;
        
        $tpl->assign('packages', $packages);
        $resp->body->assign('MAIN', $tpl->fetch('packages_list'));

        return $resp;
    }
    
    /**
    * display details of a package and the list of subpackages
    */
    function details() {
        $resp = $this->getResponse('html');
        $tpl = $this->_prepareTpl($resp, 'default.packages.details.title', true);

        $project = $tpl->get('project');
        $package = $tpl->get('package');

        if (!$package) {
            $tpl->assign('interfaces', null);
            $tpl->assign('classes', null);
            $tpl->assign('functions', null);
        }
        else {
            // Get interfaces
            $dao_classes = jDao::get('rarangi~classes');
            $interfaces = $dao_classes->findByPackage($project->id, $package->id, 1);
            $tpl->assign('interfaces', $interfaces);
            
            // Get classes
            $classes = $dao_classes->findByPackage($project->id, $package->id, 0);
            $tpl->assign('classes', $classes);
            
            // Get functions
            $dao_functions = jDao::get('rarangi~functions');
            $functions = $dao_functions->findByPackage($project->id, $package->id);
            $tpl->assign('functions', $functions);

            // Get globals
            $dao_globals = jDao::get('rarangi~globals');
            $globals = $dao_globals->findVariablesByPackage($project->id, $package->id);
            $tpl->assign('globals', $globals);

            // Get defines
            $defines = $dao_globals->findConstsByPackage($project->id, $package->id);
            $tpl->assign('defines', $defines);
        }
        $resp->body->assign('MAIN', $tpl->fetch('package_details'));
        
        return $resp;
    }

    /**
    * display the list of classes of a package
    */
    function classes() {
        $resp = $this->getResponse('html');
        $tpl = $this->_prepareTpl($resp, 'default.packages.classes.title', true);
        
        $project = $tpl->get('project');
        $package = $tpl->get('package');
        
        if (!$package) {
            $tpl->assign('classes', null);
        } else {
            // Get classes
            $dao_classes = jDao::get('rarangi~classes');
            $classes = $dao_classes->findByPackage($project->id, $package->id, 0);
            $tpl->assign('classes', $classes);
        }
        $tpl->assign('forInterfaces', false);
        $resp->body->assign('MAIN', $tpl->fetch('classes_list'));
        return $resp;
    }

    /**
    * display the list of interfaces of a package
    */
    function interfaces() {
        $resp = $this->getResponse('html');
        $tpl = $this->_prepareTpl($resp, 'default.packages.interfaces.title', true);

        $project = $tpl->get('project');
        $package = $tpl->get('package');

        if (!$package) {
            $tpl->assign('classes', null);
        } else {
            // Get interfaces
            $dao_classes = jDao::get('rarangi~classes');
            $interfaces = $dao_classes->findByPackage($project->id, $package->id, 1);
            $tpl->assign('interfaces', $interfaces);
        }
        $tpl->assign('forInterfaces', true);
        $resp->body->assign('MAIN', $tpl->fetch('classes_list'));
        return $resp;
    }

    /**
    * display the list of functions of a package
    */
    function functions() {
        $resp = $this->getResponse('html');
        $tpl = $this->_prepareTpl($resp, 'default.packages.functions.title', true);

        $project = $tpl->get('project');
        $package = $tpl->get('package');

        if (!$package) {
            $tpl->assign('functions', null);
        } else {
            // Get functions
            $dao_functions = jDao::get('rarangi~functions');
            $functions = $dao_functions->findByPackage($project->id, $package->id);
            $tpl->assign('functions', $functions);
        }
        $resp->body->assign('MAIN', $tpl->fetch('functions_list'));
        return $resp;
    }

    /**
    * display the list of constants of a package
    */
    function constants() {
        $resp = $this->getResponse('html');
        $tpl = $this->_prepareTpl($resp, 'default.packages.constants.title', true);

        $project = $tpl->get('project');
        $package = $tpl->get('package');

        if (!$package) {
            $tpl->assign('components', null);
        }
        else {
            $dao_globals = jDao::get('rarangi~globals');
            $defines = $dao_globals->findConstsByPackage($project->id, $package->id);
            $tpl->assign('components', $defines);
        }
        $resp->body->assign('MAIN', $tpl->fetch('package_constants'));
        return $resp;
    }


    /**
    * display the list of globals of a package
    */
    function globals() {
        $resp = $this->getResponse('html');
        $tpl = $this->_prepareTpl($resp, 'default.packages.globals.title', true);

        $project = $tpl->get('project');
        $package = $tpl->get('package');

        if (!$package) {
            $tpl->assign('components', null);
        }
        else {
            $dao_globals = jDao::get('rarangi~globals');
            $defines = $dao_globals->findVariablesByPackage($project->id, $package->id);
            $tpl->assign('components', $defines);
        }
        $resp->body->assign('MAIN', $tpl->fetch('package_globals'));
        return $resp;
    }

}
