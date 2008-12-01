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
class jInterfaceDescriptor extends jBaseDescriptor {


    protected $isInterface = true;

    public $name ='';

    public $inheritsFrom = null;

    public $members = array();

    public $classId = null;

    public function save() {
        if($this->name == '')
            throw new Exception('class name undefined');

        $dao = jDao::get('jphpdoc~classes');
        
        $mother_id = null;
        if($this->inheritsFrom !== null) {
            $mother = $dao->getByName($this->inheritsFrom, $this->projectId);
            if(!$mother) {
                $mother = jDao::createRecord('jphpdoc~classes');
                $mother->name = $this->inheritsFrom;
                $mother->project_id = $this->projectId;
                if($this->isInterface)
                    $mother->is_interface = true;
                $dao->insert($mother);
            }
            $mother_id = $mother->id;
        }
        
        $record = $dao->getByName($this->name, $this->projectId);
        $toInsert=false;
        if(!$record) {
            $toInsert=true;
            $record = jDao::createRecord('jphpdoc~classes');
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

