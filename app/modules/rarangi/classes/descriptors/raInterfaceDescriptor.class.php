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
class raInterfaceDescriptor extends raBaseDescriptor {

    /**
     * @var boolean indicate if data are for an interface or not.
     *              (some child could set it to false)
     */
    protected $isInterface = true;

    /**
     * @var string name of the interface/class
     */
    public $name ='';

    /**
     * @var string name of the mother of the interface/class
     */
    public $mother = '';

    /**
     * @var array list of members of the interface/class
     */
    public $members = array();

    public $classId = null;

    public function save() {
        if ($this->ignore)
            return;

        if ($this->name == '')
            throw new Exception('class name undefined');

        $dao = jDao::get('rarangi~classes');
        
        $mother_id = null;
        if ($this->mother != '') {
            $mother = $dao->getByName($this->project->id(), $this->mother);
            if (!$mother) {
                $mother = jDao::createRecord('rarangi~classes');
                $mother->name = $this->mother;
                $mother->project_id = $this->project->id();
                if ($this->isInterface)
                    $mother->is_interface = true;
                $dao->insert($mother);
            }
            $mother_id = $mother->id;
        }

        $record = $dao->getByNameAndFile($this->project->id(), $this->name, $this->fileId);
        $toInsert=false;
        if (!$record) {
            $toInsert=true;
            $record = jDao::createRecord('rarangi~classes');
        }

        $record->name = $this->name;
        $record->project_id = $this->project->id();
        $record->mother_class = $mother_id;
        if (!$this->isInterface)
            $record->is_abstract = $this->isAbstract;
        else
            $record->is_abstract = 0;

        $record->package_id = $this->project->getPackageId($this->package);
        $record->is_interface = $this->isInterface;

        $record->file_id = $this->fileId;
        $record->line_start = $this->line;
        $record->line_end = $this->lineEnd;
        $record->short_description = $this->shortDescription;
        $record->description = $this->description;

        $this->fillRecord($record);

        if (!$toInsert) {
            // if there is already a record, this is an empty record, created
            // during the parsing of an other class which inherits from the current class
            // so we can update all fields
            $dao->update($record);
        }
        else {
            $dao->insert($record);
        }
        $this->classId = $record->id;

        foreach ($this->members as $member) {
            $member->classId = $record->id;
            $member->save();
        }

        $classauthor = jDao::createRecord("rarangi~classes_authors");
        $classauthor->class_id = $this->classId;
        $this->saveAuthorsContributors($classauthor, "rarangi~classes_authors");
    }
}

