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
        $record->line_start = $this->line;
        $record->line_end = $this->lineEnd;
        $record->mother_class = $mother_id;
        if (!$this->isInterface)
            $record->is_abstract = $this->isAbstract;
        else
            $record->is_abstract = 0;
        $record->package_id = $this->getPackageId($this->package);
        $record->is_interface = $this->isInterface;
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

        list($authors, $contributors) = $this->saveAuthorsContributors();
        $classauthors = jDao::get("rarangi~classes_authors");
        $classauthor = jDao::createRecord("rarangi~classes_authors");
        $classauthor->class_id = $this->classId;
        $classauthor->as_contributor = 0;
        foreach ($authors as $authorid) {
            $classauthor->author_id = $authorid;
            $classauthors->insert($classauthor);
        }
        $classauthor->as_contributor = 1;
        foreach ($contributors as $authorid) {
            $classauthor->author_id = $authorid;
            $classauthors->insert($classauthor);
        }
    }
}

