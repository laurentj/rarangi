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

    public $licence; // libelle, lien
    
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
        $this->record->subpackage_id = $this->getPackageId($this->subpackage, true);
        jDao::get('rarangi~files')->update($this->record);
    }
}
