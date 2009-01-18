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
        }
        $resp->body->assign('MAIN', $tpl->fetch('function_details'));


        return $resp;
    }
}
