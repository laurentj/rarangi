<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi/
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

class componentsCtrl extends jController {
    
    /**
    * display details of a class
    */
    function classdetails() {
        $resp = $this->getResponse('html');
        $tpl = new jTpl();
        
        $project = $GLOBALS['currentproject'];
        $classname = $this->param('classname');
        $package = $this->param('package');

        $resp->title = jLocale::get('default.classes.details.title', array($classname));

        $tpl->assign('classname', $classname);
        $tpl->assign('project', $project);
        $tpl->assign('package', $package);

        $dao = jDao::get('classedetails');
        $class = $dao->getByName($project->id,$classname);
        $tpl->assign('class',$class);
        
        if (!$class) {
            $resp->setHttpStatus('404', 'Not Found');
        } else {
            $resp->body->assignZone('BREADCRUMB', 'location_breadcrumb', array(
                    'mode' => 'projectbrowse',
                    'projectname' => $project->name));
            $resp->body->assignZone('MENUBAR', 'project_menubar', array(
                                                            'project'=>$project));

            if ($class->links)
                $class->links = unserialize($class->links);
            
            if ($class->see)
                $class->see = unserialize($class->see);

            if ($class->uses)
                $class->uses = unserialize($class->uses);

            if ($class->changelog)
                $class->changelog = unserialize($class->changelog);
            
            $rs_properties = jDao::get('class_properties')->findByClass($project->id, $class->id);
            $properties = array();
            foreach ($rs_properties as $prop) {
                if ($prop->links)
                    $prop->links = unserialize($prop->links);
  
                if ($prop->see)
                    $prop->see = unserialize($prop->see);
    
                if ($prop->uses)
                    $prop->uses = unserialize($prop->uses);
    
                if ($prop->changelog)
                    $prop->changelog = unserialize($prop->changelog);
                $properties[] = $prop;
            }
            $tpl->assign('properties', $properties);

            $rs_method_params = jDao::get('method_parameters')->findByClass($class->id);
            $method_params = array();
            foreach ($rs_method_params as $p) {
                if(!isset($method_params[$p->method_name]))
                    $method_params[$p->method_name] = array();
                $method_params[$p->method_name][] = $p;
            }

            $rs_methods = jDao::get('class_methods')->findByClass($project->id, $class->id);
            $methods = array();
            foreach ($rs_methods as $meth) {
                if ($meth->links)
                    $meth->links = unserialize($meth->links);
  
                if ($meth->see)
                    $meth->see = unserialize($meth->see);
    
                if ($meth->uses)
                    $meth->uses = unserialize($meth->uses);
    
                if ($meth->changelog)
                    $meth->changelog = unserialize($meth->changelog);
                $methods[] = $meth;
                if(!isset($method_params[$meth->name]))
                    $method_params[$meth->name] = array();
            }
            $tpl->assign('method_parameters', $method_params);
            $tpl->assign('methods', $methods);
        }
        $resp->body->assign('MAIN', $tpl->fetch('class_details'));

        return $resp;
    }

    /**
    * display details of a function
    */
    function functiondetails() {
        $resp = $this->getResponse('html');
        $tpl = new jTpl();
        
        $project = $GLOBALS['currentproject'];
        $functionname = $this->param('functionname');
        $package = $this->param('package');

        $resp->title = $functionname;

        $tpl->assign('functionname', $functionname);
        $tpl->assign('project', $project->name);

        $dao = jDao::get('functions');
        $func = $dao->getByName($project->id, $functionname);
        $tpl->assign('function',$func);
        
        if (!$func) {
            $resp->setHttpStatus('404', 'Not Found');
        } else {
            $resp->body->assignZone('BREADCRUMB', 'location_breadcrumb', array(
                    'mode' => 'projectbrowse',
                    'projectname' => $project->name));
            $resp->body->assignZone('MENUBAR', 'project_menubar', array(
                                                            'project'=>$project));
            
            if ($func->links)
                $func->links = unserialize($func->links);
            
            if ($func->see)
                $func->see = unserialize($func->see);

            if ($func->uses)
                $func->uses = unserialize($func->uses);

            if ($func->changelog)
                $func->changelog = unserialize($func->changelog);

            $rs_func_params = jDao::get('function_parameters')->findByFunction($func->id);
            $func_params = array();
            foreach ($rs_func_params as $p) {
                $func_params[] = $p;
            }
            $tpl->assign('function_parameters', $func_params);
        }
        $resp->body->assign('MAIN', $tpl->fetch('function_details'));


        return $resp;
    }
}
