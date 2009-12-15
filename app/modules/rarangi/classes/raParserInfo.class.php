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
class raParserInfo {

    protected $fullSourcePath = '';
    protected $currentFile = '';
    protected $currentFileName = '';
    protected $currentLine = 1;

    /**
     * @var raProject
     */
    protected $project;

    /**
     * @param raProject $project
     * @param string $fullSourcePath the main path where sources are analysed
     * @param string $currentFile the full path of the current analysed file
     * @param string $currentFileName the name of the current analysed file
     */
    function __construct($project, $fullSourcePath, $currentFile, $currentFileName) {
        $this->project = $project;
        $this->currentFile = $currentFile;
        $this->currentFileName = $currentFileName;
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
    
    public function getProjectId() { return $this->project->id(); }
    
    /**
     * @return raProject
     */
    public function project() {
        return $this->project;
    }
    
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
