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
class jClassDescriptor extends jBaseDescriptor {
    public $package;
    public $subpackage;
    public $name;
    public $inheritsFrom = null;
    public $interfaces = array();
    
    
    public $projectId;
    public $fileId;
    public $line;
    
    function __construct($projectId, $fileId, $line){
        $this->projectId = $projectId;
        $this->fileId = $fileId;
        $this->line = $line;
    }

    public function save() {
        if($this->name == '')
            throw new Exception('class name undefined');
        
        $dao = jDao::get('jphpdoc~classes');

        $record = jDao::createRecord('jphpdoc~classes');
        $record->name = $this->name;
        $record->project_id = $this->projectId;
        $record->file_id = $this->fileId;
        $record->linenumber = $this->line;
        $record->mother_class = $this->inheritsFrom;
        
        if($dao->get($this->name, $this->projectId)) {
            // if there is already a record, this is an empty record, created
            // during the parsing of an other class which inherits from the current class
            // so we can update all fields
            $dao->update($record);
        }
        else {
            $dao->insert($record);
        }  
    }
}

