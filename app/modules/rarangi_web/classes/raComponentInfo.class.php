<?php
/**
* @package   app
* @subpackage rarangi_web
* @author    Laurent Jouanneau
* @contributor
* @copyright 2008-2011 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi
* @licence   http://www.gnu.org/licenses/gpl.html GNU General Public Licence
*/


class raClassRelations {
    /**
     * @var jDbResultSet
     */
    public $implementations;

    /**
     * @var jDbResultSet
     */
    public $descendants;

    /**
     * @var jDbResultSet
     */
    public $asMethodParameter;

    /**
     * @var jDbResultSet
     */
    public $asFunctionParameter;

    /**
     * @var jDbResultSet
     */
    public $asMethodReturn;

    /**
     * @var jDbResultSet
     */
    public $asFunctionReturn;

}


class raComponentInfo {

    /**
     *
     */
    function getClass($project, $classname, $isInterface) {

        $dao = jDao::get('rarangi~class_details');
        $class = $dao->getByName($project->id, $classname, ($isInterface?1:0));

        if ($class) {

            if ($class->mother_class) {
                $class->mother_class = $dao->get($class->mother_class);
            }

            if ($class->links)
                $class->links = unserialize($class->links);

            if ($class->see)
                $class->see = unserialize($class->see);

            if ($class->uses)
                $class->uses = unserialize($class->uses);

            if ($class->changelog)
                $class->changelog = unserialize($class->changelog);

            if ($class->user_tags)
                $class->user_tags = unserialize($class->user_tags);

            $properties = array();
            if (!$class->is_interface) {
                $rs_properties = jDao::get('rarangi~class_properties')->findByClass($project->id, $class->id);
                foreach ($rs_properties as $prop) {
                    if ($prop->links)
                        $prop->links = unserialize($prop->links);

                    if ($prop->see)
                        $prop->see = unserialize($prop->see);

                    if ($prop->uses)
                        $prop->uses = unserialize($prop->uses);

                    if ($prop->changelog)
                        $prop->changelog = unserialize($prop->changelog);

                    if ($prop->user_tags)
                        $prop->user_tags = unserialize($prop->user_tags);

                    $properties[] = $prop;
                }
            }
            $class->properties = $properties;

            $rs_method_params = jDao::get('rarangi~method_parameters')->findByClass($class->id);
            $method_params = array();
            foreach ($rs_method_params as $p) {
                if(!isset($method_params[$p->method_name]))
                    $method_params[$p->method_name] = array();
                $method_params[$p->method_name][] = $p;
            }

            $rs_methods = jDao::get('rarangi~class_methods')->findByClass($project->id, $class->id);
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

                if ($meth->user_tags)
                    $meth->user_tags = unserialize($meth->user_tags);

                $methods[] = $meth;
                if(!isset($method_params[$meth->name]))
                    $method_params[$meth->name] = array();
            }

            $class->methodParameters = $method_params;
            $class->methods = $methods;
        }
        return $class;
    }

    /**
     * @return raClassRelations
     */
    function getRelations ($class) {

        $rel = new raClassRelations();

        $db = jDb::getConnection();

        // -----implementation
        $sql = "SELECT  c.name, c.id, p.name as package FROM ".$db->prefixTable('interface_class')." ic, ".
                        $db->prefixTable('classes')." c , ".
                        $db->prefixTable('packages')." p WHERE c.package_id = p.id AND";
        if ($class->is_interface) {
            $sql.=" ic.interface_id = ".$class->id. " AND ic.class_id = c.id";
        }
        else {
            $sql.=" ic.class_id = ".$class->id. " AND ic.interface_id = c.id";
        }
        $rel->implementations = $db->query($sql);

        // ------ descendants
        $sql = "SELECT  c.name, c.id, p.name as package FROM ".$db->prefixTable('classes')." c, ".
                        $db->prefixTable('packages')." p WHERE c.package_id=p.id AND mother_class = ".$class->id;
        $rel->descendants = $db->query($sql);

        // as returned value

        // as method parameter
        $sql = "SELECT  distinct method_name, c.name, c.is_interface, p.name as package FROM ".$db->prefixTable('method_parameters')." mp, "
                .$db->prefixTable('classes')." c, ".
                $db->prefixTable('packages')." p WHERE ";
        $sql .= "  p.id = c.package_id AND mp.type LIKE ".$db->quote('%|'.$class->name.'|%')." AND mp.class_id = c.id ORDER BY c.name, method_name";
        $rel->asMethodParameter = $db->query($sql);

        // as function parameter
        $sql = "SELECT  distinct f.name, p.name as package FROM ".$db->prefixTable('function_parameters')." fp, "
                .$db->prefixTable('functions')." f, ".
                $db->prefixTable('packages')." p WHERE ";
        $sql .= "  p.id = f.package_id AND fp.type = ".$db->quote('%|'.$class->name.'|%')." AND fp.function_id = f.id ORDER BY f.name";
        $rel->asFunctionParameter = $db->query($sql);

        // as method return value
        $sql = "SELECT  distinct m.name as method_name, c.name, c.is_interface, p.name as package FROM ".$db->prefixTable('class_methods')." m,  "
                .$db->prefixTable('classes')." c, ".
                $db->prefixTable('packages')." p WHERE ";
        $sql .= "  p.id = c.package_id AND m.return_datatype LIKE ".$db->quote('%|'.$class->name.'|%')." AND m.class_id = c.id ORDER BY c.name, m.name";
        $rel->asMethodReturn = $db->query($sql);

        // as function return value
        $sql = "SELECT  distinct f.name, p.name as package FROM  "
                .$db->prefixTable('functions')." f, ".
                $db->prefixTable('packages')." p WHERE ";
        $sql .= "  p.id = f.package_id AND f.return_datatype = ".$db->quote('%|'.$class->name.'|%')." ORDER BY f.name";
        $rel->asFunctionReturn = $db->query($sql);

        return $rel;
    }

    function getFunction($project, $functionname) {

        $dao = jDao::get('rarangi~functions');
        $func = $dao->getByName($project->id, $functionname);

        if ($func) {
            if ($func->links)
                $func->links = unserialize($func->links);

            if ($func->see)
                $func->see = unserialize($func->see);

            if ($func->uses)
                $func->uses = unserialize($func->uses);

            if ($func->changelog)
                $func->changelog = unserialize($func->changelog);

            if ($func->user_tags)
                $func->user_tags = unserialize($func->user_tags);

            $rs_func_params = jDao::get('rarangi~function_parameters')->findByFunction($func->id);
            $func_params = array();
            foreach ($rs_func_params as $p) {
                $func_params[] = $p;
            }
            $func->parameters = $func_params;
        }
        return $func;
    }

    function getConstGlobal($project, $compname, $isConst) {
        $dao = jDao::get('rarangi~globals');
        $comp = $dao->getByName($project->id, $compname, ($isConst?2:0));

        if ($comp) {
            if ($comp->links)
                $comp->links = unserialize($comp->links);

            if ($comp->see)
                $comp->see = unserialize($comp->see);

            if ($comp->uses)
                $comp->uses = unserialize($comp->uses);

            if ($comp->changelog)
                $comp->changelog = unserialize($comp->changelog);

            if ($comp->user_tags)
                $comp->user_tags = unserialize($comp->user_tags);
        }

        return $comp;
    }
}
