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
class raPropertyDescriptor extends raBaseDescriptor {

    const TYPE_VAR = 0;
    const TYPE_STATIC_VAR = 1;
    const TYPE_CONST = 2;

    public $name;
    
    public $defaultValue = '';
    
    public $datatype='';
    
    public $accessibility = T_PUBLIC;
    
    public $typeProperty = 0;
    
    public $classId = null;

    protected $acceptPackage= false;

    public function inheritsFrom($desc) {
        $this->projectId = $desc->projectId;
        $this->deprecated = $desc->deprecated;
        $this->ignore = $desc->ignore;
        $this->since = $desc->since;
    }

    protected function parseSpecificTag($tag, $content) {
        if($tag == 'var') {
            $this->datatype = $content;
        }
    }
    
    public function save() {
        if($this->name == '')
            throw new Exception('property name undefined');

        $dao = jDao::get('rarangi~class_properties');
        $record = jDao::createRecord('rarangi~class_properties');
        $record->name = $this->name;
        $record->class_id = $this->classId;
        $record->project_id = $this->projectId;
        $record->line_start = $this->line;
        $record->datatype = $this->datatype;
        $record->default_value = $this->defaultValue;
        $record->type = $this->typeProperty;
        if($this->accessibility == T_PUBLIC)
            $record->accessibility = 'PUB';
        elseif($this->accessibility == T_PROTECTED)
            $record->accessibility = 'PRO';        
        elseif($this->accessibility == T_PRIVATE)
            $record->accessibility = 'PRI';
        
        $record->short_description = $this->shortDescription;
        $record->description = $this->description;
        
        $record->copyright = $this->copyright;
        $record->internal = $this->internal;
        $record->links = serialize($this->links);
        $record->see = serialize($this->see);
        $record->uses = serialize($this->uses);
        $record->changelog = serialize($this->changelog);
        $record->todo = $this->todo;
        $record->since = $this->since;
        $record->license_label = $this->licenseLabel;
        $record->license_link = $this->licenseLink;
        $record->license_text = $this->licenseText;

        $dao->insert($record);
    }

    
}