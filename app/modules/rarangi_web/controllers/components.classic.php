<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008-2009 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi/
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

jClasses::inc('rarangi_web~breadcrumbItem');

class componentsCtrl extends jController {

    protected $bcItems = array();
    protected $notFound = false;
    protected $project;
    protected $package;
    protected $resp;
    protected $tpl;

    protected function prepareResponse() {   
        $this->resp = $this->getResponse('html');

        $projectname = $this->param('project');
        $packagename = $this->param('package');

        $dao = jDao::get('rarangi~projects');
        $this->project = $dao->getByName($projectname);

        $this->tpl = new jTpl();
        $this->tpl->assign('project', $this->project);
        $this->tpl->assign('projectname', $projectname);
        $this->tpl->assign('packagename', $packagename);

        if (!$this->project) {
            $this->resp->setHttpStatus('404','Not found');
            $this->notFound = true;
            return null;
        }

        $packagesItem = new breadcrumbItem('Packages', jUrl::get('rarangi_web~packages:index',
                                                            array('project'=>$this->project->name)));
        $daoPackages = jDao::get('rarangi~packages');
        foreach ($daoPackages->findByProject($this->project->id) as $p) {
            $packagesItem->children[] = new breadcrumbItem($p->name, jUrl::get('rarangi_web~packages:details',
                                                                array('project'=>$this->project->name,
                                                                      'package'=>$p->name)));
        }
        $this->bcItems[] = $packagesItem;

        $this->package = $daoPackages->getByName($this->project->id, $packagename, 0);

        if (!$this->package) {
            $this->resp->setHttpStatus('404', 'Not found');
            $this->notFound = true;
            return null;
        }

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

        return $this->resp;
    }




    protected function finishResponse() {
        $this->resp->body->assignZone('BREADCRUMB',
                                'location_breadcrumb',
                                array('project' => $GLOBALS['currentproject']->name,
                                      'part'=>'packages',
                                      'items'=>$this->bcItems));
        if ($this->notFound)
            $this->resp->body->assign('MAIN', $this->tpl->fetch('component_notfound'));
        return $this->resp;
    }


    /**
     * filled by the zone, so we can access to the record from the controller
     * without the need to reload it
     */
    public $classRecord = null;

    /**
    * display details of a class
    */
    function classdetails() {
        $resp = $this->prepareResponse();
        if (is_null($resp))
            return $this->finishResponse();

        $classname = $this->param('classname');

        $resp->title = jLocale::get('default.classes.details.title', array($classname));

        $zparams = array(
          'classname'=>$classname,
          'project'=>$GLOBALS['currentproject'],
          'package'=>$this->param('package'),
          'toReturn'=>$this,
          'isInterface'=>false,
        );

        $resp->body->assignZone('MAIN', 'class_details', $zparams); 

        if (!$this->classRecord) {  
            $resp->setHttpStatus('404', 'Not Found');
        }
        $item = new breadcrumbItem('Classes',
                                                      jUrl::get('rarangi_web~packages:classes',
                                                            array('project'=>$this->project->name,
                                                                  'package'=>$this->package->name)));
        $dao_classes = jDao::get('rarangi~classes');
        $classes = $dao_classes->findByPackage($this->project->id, $this->package->id, 0);
        foreach($classes as $class) {
            $item->children[] = new breadcrumbItem($class->name,
                                                    jUrl::get('rarangi_web~components:classdetails',
                                                              array('project'=>$this->project->name,
                                                                    'package'=>$this->package->name,
                                                                    'classname'=>$class->name)));
        }
        $this->bcItems[] = $item;
        $this->bcItems[] = new breadcrumbItem($classname,'');
        return $this->finishResponse();
    }
    
    public $interfaceRecord = null;
    /**
    * display details of an interface
    */
    function interfacedetails() {
        $resp = $this->prepareResponse();
        if (is_null($resp))
            return $this->finishResponse();

        $classname = $this->param('interfacename');

        $resp->title = jLocale::get('default.interfaces.details.title', array($classname));

        $zparams = array(
          'classname'=>$classname,
          'project'=>$GLOBALS['currentproject'],
          'package'=>$this->param('package'),
          'toReturn'=>$this,
          'isInterface'=>true,
        );

        $resp->body->assignZone('MAIN', 'class_details', $zparams); 

        if (!$this->interfaceRecord) {  
            $resp->setHttpStatus('404', 'Not Found');
        }

        $item = new breadcrumbItem('Interfaces',
                                                      jUrl::get('rarangi_web~packages:interfaces',
                                                            array('project'=>$this->project->name,
                                                                  'package'=>$this->package->name)));
        $dao_classes = jDao::get('rarangi~classes');
        $classes = $dao_classes->findByPackage($this->project->id, $this->package->id, 1);
        foreach($classes as $class) {
            $item->children[] = new breadcrumbItem($class->name,
                                                    jUrl::get('rarangi_web~components:interfacedetails',
                                                              array('project'=>$this->project->name,
                                                                    'package'=>$this->package->name,
                                                                    'interfacename'=>$class->name)));
        }
        $this->bcItems[] = $item;
        $this->bcItems[] = new breadcrumbItem($classname,'');
        return $this->finishResponse();
    }

    public $functionRecord = null;
    /**
    * display details of a function
    */
    function functiondetails() {
        $resp = $this->prepareResponse();
        if (is_null($resp))
            return $this->finishResponse();

        $functionname = $this->param('functionname');

        $resp->title = $functionname;
        
        $zparams = array(
          'functionname'=>$functionname,
          'project'=>$GLOBALS['currentproject'],
          'package'=>$this->param('package'),
          'toReturn'=>$this,
        );

        $resp->body->assignZone('MAIN', 'function_details', $zparams); 
    
        if (!$this->functionRecord) {
            $resp->setHttpStatus('404', 'Not Found');
        }

        $item = new breadcrumbItem('Functions',
                                                      jUrl::get('rarangi_web~packages:functions',
                                                            array('project'=>$this->project->name,
                                                                  'package'=>$this->package->name)));
        $dao_functions = jDao::get('rarangi~functions');
        $functions = $dao_functions->findByPackage($this->project->id, $this->package->id);
        foreach($functions as $func) {
            $item->children[] = new breadcrumbItem($func->name,
                                                    jUrl::get('rarangi_web~components:functiondetails',
                                                              array('project'=>$this->project->name,
                                                                    'package'=>$this->package->name,
                                                                    'functionname'=>$func->name)));
        }

        $this->bcItems[] = $item;
        $this->bcItems[] = new breadcrumbItem($functionname,'');
        return $this->finishResponse();
    }

    public $globalRecord;
    /**
    * display details of a global variable
    */
    function globaldetails() {
        $resp = $this->prepareResponse();
        if (is_null($resp))
            return $this->finishResponse();

        $globalname = $this->param('globalname');

        $resp->title = $globalname;
        
        $zparams = array(
          'compname'=>$globalname,
          'project'=>$GLOBALS['currentproject'],
          'package'=>$this->param('package'),
          'is_const'=>false,
          'toReturn'=>$this,
        );

        $resp->body->assignZone('MAIN', 'constglobal_details', $zparams); 
    
        if (!$this->globalRecord) {
            $resp->setHttpStatus('404', 'Not Found');
        }
        $item = new breadcrumbItem('Globals',
                                                      jUrl::get('rarangi_web~packages:globals',
                                                            array('project'=>$this->project->name,
                                                                  'package'=>$this->package->name)));
        $dao_globals = jDao::get('rarangi~globals');
        $globals = $dao_globals->findVariablesByPackage($this->project->id, $this->package->id);
        foreach($globals as $glob) {
            $item->children[] = new breadcrumbItem($glob->name,
                                                    jUrl::get('rarangi_web~components:globaldetails',
                                                              array('project'=>$this->project->name,
                                                                    'package'=>$this->package->name,
                                                                    'globalname'=>$glob->name)));
        }

        $this->bcItems[] = $item;
        $this->bcItems[] = new breadcrumbItem($globalname,'');
        return $this->finishResponse();
    }
    public $constRecord;
    /**
    * display details of a global variable
    */
    function constantdetails() {
        $resp = $this->prepareResponse();
        if (is_null($resp))
            return $this->finishResponse();

        $globalname = $this->param('constantname');

        $resp->title = $globalname;
        
        $zparams = array(
          'compname'=>$globalname,
          'project'=>$GLOBALS['currentproject'],
          'package'=>$this->param('package'),
          'is_const'=>true,
          'toReturn'=>$this,
        );

        $resp->body->assignZone('MAIN', 'constglobal_details', $zparams); 
    
        if (!$this->constRecord) {
            $resp->setHttpStatus('404', 'Not Found');
        }

        $item = new breadcrumbItem('Constants',
                                                      jUrl::get('rarangi_web~packages:constants',
                                                            array('project'=>$this->project->name,
                                                                  'package'=>$this->package->name)));
        $dao_globals = jDao::get('rarangi~globals');
        $globals = $dao_globals->findConstsByPackage($this->project->id, $this->package->id);
        foreach($globals as $glob) {
            $item->children[] = new breadcrumbItem($glob->name,
                                                    jUrl::get('rarangi_web~components:constantdetails',
                                                              array('project'=>$this->project->name,
                                                                    'package'=>$this->package->name,
                                                                    'constantname'=>$glob->name)));
        }
        $this->bcItems[] = $item;
        $this->bcItems[] = new breadcrumbItem($globalname,'');
        return $this->finishResponse();
    }

}
