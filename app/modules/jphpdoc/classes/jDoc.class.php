<?php
/**
* @package     jPhpDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/jphpdoc
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

$dirnamefile = dirname(__FILE__).'/';
require($dirnamefile.'jDescriptor.lib.php');
require($dirnamefile.'parsers/jParserInfo.class.php');
require($dirnamefile.'parsers/jFileParser.class.php');

/**
 * main class : it launches the parsing of files
 */
class jDoc {

    protected function __construct(){ }

    /**
     * only one instance of jDoc is allowed; this method returns the instance
     */
    static public function getInstance(){
        static $doc=null;
        if($doc === null){
            $doc = new jDoc();
        }
        return $doc;
    }

    /**
     * @var jParserInfo
     */
    protected $parserInfo;

    function getParserInfo() { return $this->parserInfo; }

    /**
     * @var jDaoRecord
     */
    protected $projectRec;

    protected $config;

    /**
     * @param jDocConfig $conf
     */
    public function setConfig($configfile){
        
        $this->config = parse_ini_file($configfile,true);
        
        $this->config['excludedFilesReg'] = array();

        if(isset($this->config['excludedFiles'])) {
            $this->setExcludedFiles(explode(',',$this->config['excludedFiles']));
        }
        else
            $this->config['excludedFiles'] = array('.svn','CVS', '.hg');
            
        if(!isset($this->config['sourceDirectories']['path']))
            throw new Exception ("no source directory defined in the config");
        if(!isset($this->config['projectName']))
            throw new Exception ("no project defined in the config");

        $this->config = (object) $this->config;
        
        $projectdao = jDao::get('jphpdoc~projects');
        if(! $this->projectRec = $projectdao->getByName($this->config->projectName)) {
            $this->projectRec = jDao::createRecord('jphpdoc~projects');
            $this->projectRec->name = $this->config->projectName;
            $projectdao->insert($this->projectRec);
        }
        else {
            jDao::get('files_content')->deleteByProject($this->projectRec->id);
            jDao::get('files')->deleteByProject($this->projectRec->id);
            jDao::get('classes')->deleteByProject($this->projectRec->id);
            jDao::get('packages')->deleteByProject($this->projectRec->id);
        }
    }


    public function setExcludedFiles($files) {
        $this->config['excludedFiles'] = array();
        foreach($files as $f){
            if($f{0} == '*'){
                $s = preg_quote(substr($f,1),'/');
                $s = '/.*'.$s.'$/';
                $this->config['excludedFilesReg'][] = $s;
            }elseif(substr($f,-1,1) == '*'){
                $s = preg_quote(substr($f,0,-1),'/');
                $s = '/^'.$s.'.*/';
                $this->config['excludedFilesReg'][] = $s;
            }else{
                $this->config['excludedFiles'][] = $f;
            }
        }
    }

    /**
     * Test if a filename is allowed or not
     * @return boolean true if the file shouldn't be parsed
     */
    public function isExcludedFile($name){
        if(in_array($name, $this->config->excludedFiles)) return true;
        foreach($this->config->excludedFilesReg as $reg){
            if(preg_match($reg, $name)) return true;
        }
        return false;
    }



    /**
     * @var jDaoRecord
     */
    protected $filesDao;
    
    /**
     * main method. launches the parsing
     */
    public function run(){
        $this->filesDao = jDao::create('jphpdoc~files');

        $fileRec = jDao::createRecord('jphpdoc~files');
        $fileRec->project_id = $this->projectRec->id;
        $fileRec->isdir = 1;
        $fileRec->fullpath = "";
        $fileRec->filename = "";
        $fileRec->dirname = "";
        $this->filesDao->insert($fileRec);

        foreach($this->config->sourceDirectories['path'] as $sourcepath) {
            $sourcepath = realpath($sourcepath);
            $this->fullSourcePath = $sourcepath;
            if($sourcepath !='')
                $this->readFiles(new RecursiveDirectoryIterator($sourcepath));
            else
                throw new Exception("unknow path: $sourcepath");            
        }
    }

    /**
     * read all files in a directory
     * @param RecursiveDirectoryIterator $rdi  an iterator on a directory content
     */
    protected function readFiles($rdi) {
        if (!is_object($rdi))
            return;
        
        for ($rdi->rewind();$rdi->valid();$rdi->next()) {
            
            if ($rdi->isDot()){
                continue;
            }
            
            if ($rdi->isDir() || $rdi->isFile()) {
                if($this->isExcludedFile($rdi->getFilename())) continue;
                if ($rdi->hasChildren()){
                    $fileRec = jDao::createRecord('jphpdoc~files');
                    $fileRec->project_id = $this->projectRec->id;
                    $fileRec->isdir = 1;
                    $fileRec->fullpath = substr($rdi->current(), strlen($this->fullSourcePath)+1);
                    $fileRec->filename = $rdi->getFilename();
                    $fileRec->dirname = substr(dirname($rdi->current()), strlen($this->fullSourcePath)+1);
                    $this->filesDao->insert($fileRec);
                    $this->readFiles($rdi->getChildren());
                }else{
                    $this->parserInfo = new jParserInfo($this->projectRec->id, $this->fullSourcePath, $rdi->current(), $rdi->getFilename());
                    if(preg_match('/\\.php5?$/',$rdi->getFilename())){
                        $fileparser = new jFileParser($this->parserInfo);
                        $fileparser->parse();
                    }
                }
            }
        }
    }
}
