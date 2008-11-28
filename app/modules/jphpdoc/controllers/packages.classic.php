<?php
/**
* @package   jphpdoc
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/jphpdoc/
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

        $rep->body->assign('MAIN', $tpl->fetch('packages_list'));
        return $rep;
    }
    
    /**
    * display details of a package and the list of subpackages
    */
    function details() {
        $rep = $this->getResponse('html');
        $tpl = $this->_prepareTpl();

        $rep->body->assign('MAIN', $tpl->fetch('package_details'));
        return $rep;
    }

    /**
    * display the details of a subpackage
    */
    function subpackdetails() {
        $rep = $this->getResponse('html');
        $tpl = $this->_prepareTpl();

        $rep->body->assign('MAIN', $tpl->fetch('subpackage_details'));
        return $rep;
    }

    /**
    * display the list of classes of a package
    */
    function classes() {
        $rep = $this->getResponse('html');
        $tpl = $this->_prepareTpl();

        $rep->body->assign('MAIN', $tpl->fetch('classes_list'));
        return $rep;
    }

    /**
    * display the list of functions of a package
    */
    function functions() {
        $rep = $this->getResponse('html');
        $tpl = $this->_prepareTpl();

        $rep->body->assign('MAIN', $tpl->fetch('functions_list'));
        return $rep;
    }

}
