<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008-2009 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi/
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

class componentsCtrl extends jController {
    
    public $classRecord = null;
    
    /**
    * display details of a class
    */
    function classdetails() {
        $resp = $this->getResponse('html');

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
        else {
            $resp->body->assignZone('BREADCRUMB', 'location_breadcrumb', array(
                    'mode' => 'projectbrowse',
                    'projectname' => $GLOBALS['currentproject']->name));
            $resp->body->assignZone('MENUBAR', 'project_menubar', array(
                                                            'project'=>$GLOBALS['currentproject']));
        }

        return $resp;
    }
    
    public $interfaceRecord = null;
    /**
    * display details of an interface
    */
    function interfacedetails() {
        $resp = $this->getResponse('html');

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
        else {
            $resp->body->assignZone('BREADCRUMB', 'location_breadcrumb', array(
                    'mode' => 'projectbrowse',
                    'projectname' => $GLOBALS['currentproject']->name));
            $resp->body->assignZone('MENUBAR', 'project_menubar', array(
                                                            'project'=>$GLOBALS['currentproject']));
        }

        return $resp;
    }

    public $functionRecord = null;
    /**
    * display details of a function
    */
    function functiondetails() {
        $resp = $this->getResponse('html');
        $tpl = new jTpl();
        
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
        } else {
            $resp->body->assignZone('BREADCRUMB', 'location_breadcrumb', array(
                    'mode' => 'projectbrowse',
                    'projectname' => $GLOBALS['currentproject']->name));
            $resp->body->assignZone('MENUBAR', 'project_menubar', array(
                                                            'project'=>$GLOBALS['currentproject']));
        }

        return $resp;
    }
}
