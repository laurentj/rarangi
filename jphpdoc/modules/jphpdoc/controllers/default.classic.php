<?php
/**
* @package   jphpdoc
* @subpackage jphpdoc
* @author    yourname
* @copyright 2008 yourname
* @link      http://www.yourwebsite.undefined
* @licence    All right reserved
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
        return $rep;
    }

    /**
    * display the main page of a project
    */
    function project() {
        $rep = $this->getResponse('html');

        $tpl = new jTpl();
        
        $projectname = $this->param('project');
        $dao = jDao::get('projects');
        $project = $dao->getByName($projectname);
        
        $tpl->assign('project',$project);
        $tpl->assign('projectname',$projectname);
        
        if(!$project){
            $rep->setHttpStatus('404','Not found');
            
        }
        else {
            
        }

        $rep->body->assign('MAIN', $tpl->fetch('projects_details'));

        return $rep;
    }


}
