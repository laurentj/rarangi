<?php
/**
* @package     jPhpDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/jphpdoc
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
/**
 *
 */
class jClassDescriptor extends jInterfaceDescriptor {

    public $isAbstract = false;
    public $interfaces = array();

    protected $isInterface = false;

    public function save() {
        parent::save();

        $dao = jDao::get('jphpdoc~classes');

        foreach($this->interfaces as $interface) {
            $iface = $dao->get($interface, $this->projectId);
            if (!$iface) {
                $iface = jDao::createRecord('jphpdoc~classes');
                $iface->name = $interface;
                $iface->project_id = $this->projectId;
                $iface->isInterface = true;
                $dao->insert($iface);
            }
            $class_iface = jDao::createRecord('jphpdoc~interface_class');
            $class_iface->class_id = $this->classId;
            $class_iface->interface_id = $iface->id;
            jDao::get('jphpdoc~interface_class')->insert($class_iface);
        }
    }
}

