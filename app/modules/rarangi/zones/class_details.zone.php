<?php
/**
* @package   app
* @subpackage rarangi
* @author    Laurent Jouanneau
* @contributor  
* @copyright 2008-2009 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi
* @licence   http://www.gnu.org/licenses/gpl.html GNU General Public Licence
*/

class class_detailsZone extends jZone {
    protected $_tplname = 'class_details';

    protected function _prepareTpl() {
        
        $project = $this->param('project');
        
        if (!$project) {
            $this->_tpl->assign('project', $GLOBALS['currentproject']);
            $project = $GLOBALS['currentproject'];
        }

        $classname = $this->param('classname');
        $isInterface = $this->param('isInterface', false);
        
        $dao = jDao::get('classedetails');
        $class = $dao->getByName($project->id, $classname, ($isInterface?1:0));
        $this->_tpl->assign('class',$class);
        if ($isInterface)
            $this->param('toReturn')->interfaceRecord = $class;
        else
            $this->param('toReturn')->classRecord = $class;
        if ($class) {
            if ($class->links)
                $class->links = unserialize($class->links);
            
            if ($class->see)
                $class->see = unserialize($class->see);

            if ($class->uses)
                $class->uses = unserialize($class->uses);

            if ($class->changelog)
                $class->changelog = unserialize($class->changelog);
            
            $properties = array();
            if (!$class->is_interface) {
                $rs_properties = jDao::get('class_properties')->findByClass($project->id, $class->id);
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
            }
            $this->_tpl->assign('properties', $properties);

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
            $this->_tpl->assign('method_parameters', $method_params);
            $this->_tpl->assign('methods', $methods);
        }
    }
}
