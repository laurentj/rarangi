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
class jParserInfo {

    /**
     * @var jDocConfig
     */
    public $config = null;

    protected $fullSourcePath = '';
    protected $currentFile = '';
    protected $currentFileName = '';
    protected $currentLine = 1;

    /**
     * @var jDaoRecord
     */
    protected $projectId;

    function __construct($projectId, $fullSourcePath, $filepath, $filename) {
        $this->projectId = $projectId;
        $this->currentFile = $filepath;
        $this->currentFileName = $filename;
        $this->fullSourcePath = $fullSourcePath;
    }

    /**
     * @return string the current file path
     */
    public function currentFile() { return $this->currentFile; }

    /**
     * @return string the current file name
     */
    public function currentFileName() { return $this->currentFileName; }

    /**
     * @return string the current file name
     */
    public function getFullSourcePath() { return $this->fullSourcePath; }
    
    /**
     * @return integer the current line which is parsed
     */
    public function currentLine() { return $this->currentLine; }
    
    public function eraseCurrentLine() { $this->currentLine = 1; }
    
    public function getProjectId() { return $this->projectId; }
    
    /**
     * increment the line counter by the given number
     */
    public function incLine($nb) { 
        $this->currentLine += $nb;
    }
    
    /**
     * increment the line counter by counting the number
     * of break line which are in the given string
     */
    public function incLineS($str) { 
        $this->currentLine += substr_count($str, "\n"); 
    }

}
