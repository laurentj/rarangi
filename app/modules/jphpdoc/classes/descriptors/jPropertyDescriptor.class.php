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
class jPropertyDescriptor extends jBaseDescriptor {

    public $name;
    
    public $defaultValue = '';
    
    public $datatype='';
    
    public $accessibility = T_PUBLIC;
    
    public $isStatic = false;
    
    public $classId = null;
    
    protected function parseSpecificTag($tag, $content) {
        if($tag == 'var') {
            $this->datatype = $content;
        }
    }
    
    public function save() {
        if($this->name == '')
            throw new Exception('property name undefined');

        $dao = jDao::get('jphpdoc~class_properties');
        $record = jDao::createRecord('jphpdoc~class_properties');
        $record->name = $this->name;
        $record->class_id = $this->classId;
        $record->project_id = $this->projectId;
        $record->line_number = $this->line;
        $record->datatype = $this->datatype;
        $record->default_value = $this->defaultValue;
        $record->is_static = $this->isStatic;
        if($this->accessibility == T_PUBLIC)
            $record->accessibility = 'PUB';
        elseif($this->accessibility == T_PROTECTED)
            $record->accessibility = 'PRO';        
        elseif($this->accessibility == T_PRIVATE)
            $record->accessibility = 'PRI';
        
        $record->short_description = $this->shortDescription;
        $record->description = $this->description;
        
        $dao->insert($record);
    }

    
}