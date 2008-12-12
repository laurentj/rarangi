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
class raFileDescriptor extends raBaseDescriptor  {
    public $fullpath;
    public $filepath;
    public $filename;

    public $licenceLink = '';
    public $licenceLabel = '';
    
    protected $record;
    
    function __construct($projectId, $fullSourcePath, $fullpath, $filename){
        
        $relativeFullPath = substr($fullpath, strlen($fullSourcePath)+1);
        $relativePath = substr(dirname($fullpath), strlen($fullSourcePath)+1);

        $this->filepath = $relativeFullPath;
        $this->filename = $filename;
        $this->fullpath = $fullpath;
        
        $this->record = jDao::createRecord('rarangi~files');
        $this->projectId = $this->record->project_id = $projectId;
        $this->record->isdir = 0;
        $this->record->fullpath = $relativeFullPath;
        $this->record->filename = $filename;
        $this->record->dirname = $relativePath;

        jDao::get('rarangi~files')->insert($this->record);
        $this->fileId = $this->record->id;
    }
    public function save() {
        $this->record->package_id = $this->getPackageId($this->package);
        // TODO : take all authors indicated in sub components and agregate them
        // into the authors. same for contributors
        jDao::get('rarangi~files')->update($this->record);
        
        list($authors, $contributors) = $this->saveAuthorsContributors();
        $fileauthors = jDao::get("rarangi~files_authors");
        $fileauthor = jDao::createRecord("rarangi~files_authors");
        $fileauthor->file_id = $this->fileId;
        $fileauthor->as_contributor = 0;
        foreach ($authors as $authorid) {
            $fileauthor->author_id = $authorid;
            $fileauthors->insert($fileauthor);
        }
        $fileauthor->as_contributor = 1;
        foreach ($contributors as $authorid) {
            $fileauthor->author_id = $authorid;
            $fileauthors->insert($fileauthor);
        }
    }
}
