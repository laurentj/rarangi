<?php /**
* @package     jDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2007 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

/**
 * Configuration container
 * @package     jDoc
 */
class jDocConfig {
    /**
     * list of file which won't be parsed
     * @var array
     */
    protected $excludedFiles = array();

    /**
     * list of regular expression to test name of files which won't be parsed
     * @var array
     */
    protected $excludedFilesReg = array();
    
    
    function __construct() {
        $this->setExcludedFiles(array('.svn','CVS', '.hg'));
    }

    function readConfig($configfile) {
        $config = parse_ini_file($configfile,true);
        if(isset($config['excluded_files']))
            $this->setExcludedFiles(explode(',',$config['excluded_files']));
        if(isset($config['source_directories']['path']))
            $this->sourceDirectories = $config['source_directories']['path'];
        else
            throw new Exception ("no source directory defined in the config");
        if(isset($config['project_name']))
            $this->projectName = $config['project_name'];
        else
            throw new Exception ("no project defined in the config");
    
    }

    protected $sourceDirectories;
    function getSourceDirectories() {
        return $this->sourceDirectories;
    }

    protected $projectName;
    function getProjectName() {
        return $this->projectName;
    }


    /**
     * add list of file names which won't be parsed
     *
     * filename could be plain filename, or you can use "*" pattern at the
     * begin or at the end of a name : array('*.foo', 'bar.*', 'CVS')
     * @param array $files 
     */
    public function setExcludedFiles($files){
        $this->excludedFilesReg = array();
        $this->excludedFiles = array();
        foreach($files as $f){
            if($f{0} == '*'){
                $s = preg_quote(substr($f,1),'/');
                $s = '/.*'.$s.'$/';
                $this->excludedFilesReg[] = $s;
            }elseif(substr($f,-1,1) == '*'){
                $s = preg_quote(substr($f,0,-1),'/');
                $s = '/^'.$s.'.*/';
                $this->excludedFilesReg[] = $s;
            }else{
                $this->excludedFiles[] = $f;
            }
        }
    }

    /**
     * Test if a filename is allowed or not
     * @return boolean true if the file shouldn't be parsed
     */
    public function isExcludedFile($name){
        if(in_array($name, $this->excludedFiles)) return true;
        foreach($this->excludedFilesReg as $reg){
            if(preg_match($reg, $name)) return true;
        }
        return false;
    }

}

