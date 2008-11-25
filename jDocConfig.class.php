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

    protected $storage;

    function __construct($configfile='') {
        if ($configfile != '') {
            $config = parse_ini_file($configfile,true);
            if(isset($config['excluded_files']))
                $this->setExcludedFiles(explode(',',$config['excluded_files']));
            if(isset($config['storage']))
                $this->storage = $config['storage'];
        }
    }

    function getDbConfig() {
        return $this->storage;
    }


    /**
     * add list of file names which won't be parsed
     *
     * filename could be plain filename, or you can use "*" pattern at the
     * begin or at the end of a name : array('*.foo', 'bar.*', 'CVS')
     * @param array $files 
     */
    public function setExcludedFiles($files){
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


?>