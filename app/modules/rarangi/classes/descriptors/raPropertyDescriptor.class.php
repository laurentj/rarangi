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
class raPropertyDescriptor extends raGlobalVariableDescriptor {

    public $accessibility = T_PUBLIC;

    public $classId = null;

    protected $acceptPackage = false;

    public function inheritsFrom($desc) {
        $this->experimental = $desc->experimental;
        $this->isDeprecated = $desc->isDeprecated;
        $this->deprecated = $desc->deprecated;
        $this->ignore = $desc->ignore;
        $this->since = $desc->since;
    }

    public function save() {
        if ($this->ignore)
            return;

        if ($this->name == '')
            throw new Exception('property name undefined');

        $dao = jDao::get('rarangi~class_properties');
        $record = jDao::createRecord('rarangi~class_properties');
        $record->name = $this->name;
        $record->class_id = $this->classId;
        $record->project_id = $this->project->id();
        $record->line_start = $this->line;
        $record->default_value = $this->defaultValue;
        $record->type = $this->typeProperty;
        if ($this->accessibility == T_PUBLIC)
            $record->accessibility = 'PUB';
        elseif ($this->accessibility == T_PROTECTED)
            $record->accessibility = 'PRO';        
        elseif ($this->accessibility == T_PRIVATE)
            $record->accessibility = 'PRI';

        if (isset($this->varData[$this->name])) {
            $data = $this->varData[$this->name];
        }
        else if ($this->unamedVarData !== null) {
            $data = $this->unamedVarData;
        }
        else
            $data = array('', $this->shortDescription, $this->description);

        list($record->datatype, $record->short_description, $record->description) = $data;

        $this->fillRecord($record);

        $dao->insert($record);
    }

}