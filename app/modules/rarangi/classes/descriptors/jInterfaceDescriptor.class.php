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
class jInterfaceDescriptor extends jBaseDescriptor {

    protected $isInterface = true;

    public $name ='';

    public $inheritsFrom = null;

    public $members = array();

    public $classId = null;

    public function save() {
        if($this->name == '')
            throw new Exception('class name undefined');

        $dao = jDao::get('rarangi~classes');
        
        $mother_id = null;
        if($this->inheritsFrom !== null) {
            $mother = $dao->getByName($this->projectId, $this->inheritsFrom);
            if(!$mother) {
                $mother = jDao::createRecord('rarangi~classes');
                $mother->name = $this->inheritsFrom;
                $mother->project_id = $this->projectId;
                if($this->isInterface)
                    $mother->is_interface = true;
                $dao->insert($mother);
            }
            $mother_id = $mother->id;
        }

        $record = $dao->getByNameAndFile($this->projectId, $this->name, $this->fileId);
        $toInsert=false;
        if(!$record) {
            $toInsert=true;
            $record = jDao::createRecord('rarangi~classes');
        }

        $record->name = $this->name;
        $record->project_id = $this->projectId;
        $record->file_id = $this->fileId;
        $record->linenumber = $this->line;
        $record->mother_class = $mother_id;
        if (!$this->isInterface)
            $record->is_abstract = $this->isAbstract;
        else
            $record->is_abstract = 0;
        $record->package_id = $this->getPackageId($this->package);
        $record->subpackage_id = $this->getPackageId($this->subpackage, true);
        $record->is_interface = $this->isInterface;
        $record->short_description = $this->shortDescription;
        $record->description = $this->description;
        
        if(!$toInsert) {
            // if there is already a record, this is an empty record, created
            // during the parsing of an other class which inherits from the current class
            // so we can update all fields
            $dao->update($record);
        }
        else {
            $dao->insert($record);
        }
        $this->classId = $record->id;
        
        foreach($this->members as $member) {
            $member->classId = $record->id;
            $member->save();
        }
    }
}

