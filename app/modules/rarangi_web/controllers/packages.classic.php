<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @contributor  Loic Mathaud
* @copyright 2008 Laurent Jouanneau, 2008-2009 Loic Mathaud
* @link      http://rarangi.org
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

jClasses::inc('rarangi_web~breadcrumbItem');

class packagesCtrl extends jController {
    
    protected $bcItems = array();
    protected $project;
    protected $package;
    protected $resp;
    protected $tpl;
    
    protected function _prepareTpl($title, $withPackage = false) {
        $this->resp = $this->getResponse('html');
        
        $this->tpl = new jTpl();
        $projectname = $this->param('project');
        $dao = jDao::get('rarangi~projects');
        $this->project = $dao->getByName($projectname);
        
        $this->tpl->assign('project', $this->project);
        $this->tpl->assign('projectname', $projectname);
        
        if ($withPackage) {
            $packagename = $this->param('package');
            $this->tpl->assign('packagename', $packagename);
            $this->resp->title = jLocale::get($title, array($packagename, $projectname));
        }
        else {
            $this->resp->title = jLocale::get($title, array($projectname));
        }

        if (!$this->project) {
            $this->resp->setHttpStatus('404','Not found');
        } else {
            $packagesItem = new breadcrumbItem('Packages', jUrl::get('rarangi_web~packages:index',
                                                                array('project'=>$this->project->name)));
            $daoPackages = jDao::get('rarangi~packages');
            foreach ($daoPackages->findByProject($this->project->id) as $p) {
                $packagesItem->children[] = new breadcrumbItem($p->name, jUrl::get('rarangi_web~packages:details',
                                                                    array('project'=>$this->project->name,
                                                                          'package'=>$p->name)));
            }
            $this->bcItems[] = $packagesItem;

            if ($withPackage) {
                $this->package = $daoPackages->getByName($this->project->id, $packagename, 0);
            }
        }

        if ($withPackage) {
            if (!$this->package) {
                $this->resp->setHttpStatus('404', 'Not found');
            }
            else {
                $packageItem = new breadcrumbItem($this->package->name, jUrl::get('rarangi_web~packages:details',
                                                                    array('project'=>$this->project->name,
                                                                          'package'=>$this->package->name)));
                $packageItem->children[] = new breadcrumbItem('Classes',
                                                              jUrl::get('rarangi_web~packages:classes',
                                                                    array('project'=>$this->project->name,
                                                                          'package'=>$this->package->name)));
                $packageItem->children[] = new breadcrumbItem('Interfaces',
                                                              jUrl::get('rarangi_web~packages:interfaces',
                                                                    array('project'=>$this->project->name,
                                                                          'package'=>$this->package->name)));
                $packageItem->children[] = new breadcrumbItem('Functions',
                                                              jUrl::get('rarangi_web~packages:functions',
                                                                    array('project'=>$this->project->name,
                                                                          'package'=>$this->package->name)));
                $packageItem->children[] = new breadcrumbItem('Globals',
                                                              jUrl::get('rarangi_web~packages:globals',
                                                                    array('project'=>$this->project->name,
                                                                          'package'=>$this->package->name)));
                $packageItem->children[] = new breadcrumbItem('Constants',
                                                              jUrl::get('rarangi_web~packages:constants',
                                                                    array('project'=>$this->project->name,
                                                                          'package'=>$this->package->name)));
                $this->bcItems[] = $packageItem;
                $this->tpl->assign('package', $this->package);
            }
        }

        return $this->tpl;
    }

    protected function _finishResponse($subtpl) {
        $this->resp->body->assignZone('BREADCRUMB',
                                'location_breadcrumb',
                                array('project' => $this->param('project'),
                                      'part'=>'packages',
                                      'items'=>$this->bcItems));
        $this->resp->body->assign('MAIN', $this->tpl->fetch($subtpl));
        return $this->resp;
    }
    
    /**
    * Display the list of packages
    */
    function index() {
        $tpl = $this->_prepareTpl('default.packages.title');

        if ($this->project) {
            // Get packages
            $dao = jDao::get('rarangi~packages');
            $packages = $dao->findByProject($this->project->id);
        }
        else
            $packages = null;
        $tpl->assign('packages', $packages);

        return $this->_finishResponse('packages_list');
    }
    
    /**
    * display details of a package and the list of subpackages
    */
    function details() {
        $tpl = $this->_prepareTpl('default.packages.details.title', true);

        if (!$this->package) {
            $tpl->assign('interfaces', null);
            $tpl->assign('classes', null);
            $tpl->assign('functions', null);
        }
        else {
            // Get interfaces
            $dao_classes = jDao::get('rarangi~classes');
            $interfaces = $dao_classes->findByPackage($this->project->id, $this->package->id, 1);
            $tpl->assign('interfaces', $interfaces);
            
            // Get classes
            $classes = $dao_classes->findByPackage($this->project->id, $this->package->id, 0);
            $tpl->assign('classes', $classes);
            
            // Get functions
            $dao_functions = jDao::get('rarangi~functions');
            $functions = $dao_functions->findByPackage($this->project->id, $this->package->id);
            $tpl->assign('functions', $functions);

            // Get globals
            $dao_globals = jDao::get('rarangi~globals');
            $globals = $dao_globals->findVariablesByPackage($this->project->id, $this->package->id);
            $tpl->assign('globals', $globals);

            // Get defines
            $defines = $dao_globals->findConstsByPackage($this->project->id, $this->package->id);
            $tpl->assign('defines', $defines);
        }
        return $this->_finishResponse('package_details');
    }

    /**
    * display the list of classes of a package
    */
    function classes() {
        $tpl = $this->_prepareTpl('default.packages.classes.title', true);

        if (!$this->package) {
            $tpl->assign('classes', null);
        } else {
            // Get classes
            $dao_classes = jDao::get('rarangi~classes');
            $classes = $dao_classes->findByPackage($this->project->id, $this->package->id, 0);
            $tpl->assign('classes', $classes);
            $this->bcItems[] = new breadcrumbItem('Classes',
                                                    jUrl::get('rarangi_web~packages:classes',
                                                          array('project'=>$this->project->name,
                                                                'package'=>$this->package->name)));
        }
        $tpl->assign('forInterfaces', false);
        return $this->_finishResponse('classes_list');
    }

    /**
    * display the list of interfaces of a package
    */
    function interfaces() {
        $tpl = $this->_prepareTpl('default.packages.interfaces.title', true);

        if (!$this->package) {
            $tpl->assign('classes', null);
        } else {
            // Get interfaces
            $dao_classes = jDao::get('rarangi~classes');
            $interfaces = $dao_classes->findByPackage($this->project->id, $this->package->id, 1);
            $tpl->assign('interfaces', $interfaces);
            $this->bcItems[] = new breadcrumbItem('Interfaces',
                                                    jUrl::get('rarangi_web~packages:interfaces',
                                                          array('project'=>$this->project->name,
                                                                'package'=>$this->package->name)));
        }
        $tpl->assign('forInterfaces', true);
        return $this->_finishResponse('classes_list');
    }

    /**
    * display the list of functions of a package
    */
    function functions() {
        $tpl = $this->_prepareTpl('default.packages.functions.title', true);

        if (!$this->package) {
            $tpl->assign('functions', null);
        } else {
            // Get functions
            $dao_functions = jDao::get('rarangi~functions');
            $functions = $dao_functions->findByPackage($this->project->id, $this->package->id);
            $tpl->assign('functions', $functions);
            $this->bcItems[] = new breadcrumbItem('Functions',
                                                    jUrl::get('rarangi_web~packages:functions',
                                                          array('project'=>$this->project->name,
                                                                'package'=>$this->package->name)));
        }
        return $this->_finishResponse('functions_list');
    }

    /**
    * display the list of constants of a package
    */
    function constants() {
        $tpl = $this->_prepareTpl('default.packages.constants.title', true);

        if (!$this->package) {
            $tpl->assign('components', null);
        }
        else {
            $dao_globals = jDao::get('rarangi~globals');
            $defines = $dao_globals->findConstsByPackage($this->project->id, $this->package->id);
            $tpl->assign('components', $defines);
            $this->bcItems[] = new breadcrumbItem('Constants',
                                                    jUrl::get('rarangi_web~packages:constants',
                                                          array('project'=>$this->project->name,
                                                                'package'=>$this->package->name)));
        }
        return $this->_finishResponse('package_constants');
    }

    /**
    * display the list of globals of a package
    */
    function globals() {
        $tpl = $this->_prepareTpl('default.packages.globals.title', true);

        if (!$this->package) {
            $tpl->assign('components', null);
        }
        else {
            $dao_globals = jDao::get('rarangi~globals');
            $defines = $dao_globals->findVariablesByPackage($this->project->id, $this->package->id);
            $tpl->assign('components', $defines);
            $this->bcItems[] = new breadcrumbItem('Globals',
                                                    jUrl::get('rarangi_web~packages:globals',
                                                          array('project'=>$this->project->name,
                                                                'package'=>$this->package->name)));
        }
        return $this->_finishResponse('package_globals');
    }

}
