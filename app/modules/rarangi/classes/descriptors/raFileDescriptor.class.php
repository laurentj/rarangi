<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2009 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

/**
 *
 */
class raFileDescriptor extends raBaseDescriptor  {
    public $fullpath;
    public $filepath;
    public $filename;
    
    protected $record;
    
    function __construct($project, $fullSourcePath, $fullpath, $filename) {
        
        $relativeFullPath = substr($fullpath, strlen($fullSourcePath)+1);
        $relativePath = substr(dirname($fullpath), strlen($fullSourcePath)+1);

        $this->filepath = $relativeFullPath;
        $this->filename = $filename;
        $this->fullpath = $fullpath;
        $this->project = $project;
        
        $this->record = jDao::createRecord('rarangi~files');
        $this->record->project_id = $this->project->id();
        $this->record->isdir = 0;
        $this->record->fullpath = $relativeFullPath;
        $this->record->filename = $filename;
        $this->record->dirname = $relativePath;

        jDao::get('rarangi~files')->insert($this->record);
        $this->fileId = $this->record->id;
    }

    public function save() {
        if ($this->ignore) {
            jDao::get('rarangi~files')->delete($this->fileId);
            return;
        }

        $this->record->package_id = $this->project->getPackageId($this->package);
        $this->record->short_description = $this->shortDescription;
        $this->record->description = $this->description;

        $this->fillRecord($this->record);
        
        jDao::get('rarangi~files')->update($this->record);
        
        $fileauthor = jDao::createRecord("rarangi~files_authors");
        $fileauthor->file_id = $this->fileId;
        $this->saveAuthorsContributors($fileauthor,"rarangi~files_authors" );
    }
}
