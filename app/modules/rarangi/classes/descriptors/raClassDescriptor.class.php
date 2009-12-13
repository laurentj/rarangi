<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
/**
 *
 */
class raClassDescriptor extends raInterfaceDescriptor {

    public $isAbstract = false;
    public $interfaces = array();

    protected $isInterface = false;

    public function save() {
        parent::save();

        $dao = jDao::get('rarangi~classes');

        foreach ($this->interfaces as $interface) {
            $iface = $dao->getByName($this->projectId, $interface);
            if (!$iface) {
                $iface = jDao::createRecord('rarangi~classes');
                $iface->name = $interface;
                $iface->project_id = $this->projectId;
                $iface->is_interface = true;
                $dao->insert($iface);
            }
            $class_iface = jDao::createRecord('rarangi~interface_class');
            $class_iface->class_id = $this->classId;
            $class_iface->interface_id = $iface->id;
            $class_iface->project_id = $this->projectId;
            jDao::get('rarangi~interface_class')->insert($class_iface);
        }
    }
}

