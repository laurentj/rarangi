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
class jMethodDescriptor  extends jBaseDescriptor {

    public $name;
    
    public $classId = null;

    public $accessibility = T_PUBLIC;

    public $isStatic = false;

    public $isFinal = false;

    public $isAbstract = false;

    public $parameters = array();

    public $return;

    //public $usedGlobalsVars;
    //public $staticVars;

    protected function parseSpecificTag($tag, $content) {
        if($tag == 'return') {
            $this->return = $content;
        }
    }
    
    public function save() {
        if($this->name == '')
            throw new Exception('method name undefined');

        $dao = jDao::get('jphpdoc~class_methods');
        $record = jDao::createRecord('jphpdoc~class_methods');
        $record->name = $this->name;
        $record->class_id = $this->classId;
        $record->project_id = $this->projectId;
        $record->line_number = $this->line;
        $record->is_static = $this->isStatic;
        $record->is_final = $this->isFinal;
        $record->is_abstract = $this->isAbstract;
        if($this->accessibility == T_PUBLIC)
            $record->accessibility = 'PUB';
        elseif($this->accessibility == T_PROTECTED)
            $record->accessibility = 'PRO';        
        elseif($this->accessibility == T_PRIVATE)
            $record->accessibility = 'PRI';
        else
            $record->accessibility = 'PUB';

        $record->short_description = $this->shortDescription;
        $record->description = $this->description;
        $dao->insert($record);
    }
}

