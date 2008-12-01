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
class jFileDescriptor extends jBaseDescriptor  {
    public $fullpath;
    public $filepath;
    public $filename;
    public $id;
    public $licence; // libelle, lien
    public $package;
    public $subpackage;
    
    protected $record;
    
    function __construct($projectId, $fullSourcePath, $fullpath, $filename){
        
        $relativeFullPath = substr($fullpath, strlen($fullSourcePath)+1);
        $relativePath = substr(dirname($fullpath), strlen($fullSourcePath)+1);

        $this->filepath = $relativeFullPath;
        $this->filename = $filename;
        $this->fullpath = $fullpath;
        
        $this->record = jDao::createRecord('jphpdoc~files');
        $this->record->project_id = $projectId;
        $this->record->isdir = 0;
        $this->record->fullpath = $relativeFullPath;
        $this->record->filename = $filename;
        $this->record->dirname = $relativePath;
        jDao::get('jphpdoc~files')->insert($this->record);
        $this->id = $this->record->id;
    }
    public function save() {
        jDao::get('jphpdoc~files')->update($this->record);
    }
}
