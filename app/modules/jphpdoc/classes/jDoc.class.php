<?php
/**
* @package     jDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2007 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

/**
 * main class : it launches the parsing of files
 */
class jDoc {

    /**
     * @var jDocConfig
     */
    public $config = null;
    public $originalSourcePath;


    protected $currentFile = '';
    protected $currentLine = 0;
    protected $fileList=array();
    protected $classList=array();
    protected $functionList=array();
    protected $globalVarList=array();

    protected function __construct(){ }


// static methods
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
     * @return string the current filename
     */
    static public function currentFile() { $d = self::getInstance(); return $d->currentFile; }
    
    /**
     * @return integer the current line which is parsed
     */
    static public function currentLine() { $d = self::getInstance(); return $d->currentLine; }
    
    /**
     * increment the line counter by the given number
     */
    static public function incLine($nb) { 
        $d = self::getInstance(); 
        $d->currentLine+= $nb;
    }
    
    /**
     * increment the line counter by counting the number
     * of break line which are in the given string
     */
    static public function incLineS($str) { 
        $d = self::getInstance(); 
        $d->currentLine+= substr_count($str, "\n"); 
    }


// non static methods

    /**
     * @var jDaoRecord
     */
    protected $projectRec;

    /**
     * @param jDocConfig $conf
     */
    public function setConfig($conf){
        $this->config = $conf;
        $projectdao = jDao::get('jphpdoc~projects');
        if(! $this->projectRec = $projectdao->getByName($this->config->getProjectName())) {
            $this->projectRec = jDao::createRecord('jphpdoc~projects');
            $this->projectRec->name = $this->config->getProjectName();
            $projectdao->insert($this->projectRec);
        }
        else {
            jDao::get('files_content')->deleteByProject($this->projectRec->id);
            jDao::get('files')->deleteByProject($this->projectRec->id);
            jDao::get('classes')->deleteByProject($this->projectRec->id);
        }
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

        foreach($this->config->getSourceDirectories() as $sourcepath) {
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
                if($this->config->isExcludedFile($rdi->getFilename())) continue;
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
                    $this->currentFile = $rdi->current();
                    $this->currentLine = 1;
                    if(preg_match('/\\.php5?$/',$rdi->getFilename())){
                        $this->addPhpFile($rdi->current(), $rdi->getFilename());
                    }
                }
            }
        }
    }

    /**
     * parse a php file
     * @param string $filepath  the path of the file
     * @param string $filename  the file name
     */
    protected function addPhpFile($filepath, $filename){
        
        $relativeFullPath = substr($filepath, strlen($this->fullSourcePath)+1);
        $relativePath = substr(dirname($filepath), strlen($this->fullSourcePath)+1);
        
        $fileRec = jDao::createRecord('jphpdoc~files');
        $fileRec->project_id = $this->projectRec->id;
        $fileRec->isdir = 0;
        $fileRec->fullpath = $relativeFullPath;
        $fileRec->filename = $filename;
        $fileRec->dirname = $relativePath;
        $this->filesDao->insert($fileRec);

        $content = file_get_contents($filepath);

        $lines = explode("\n", $content);
        $filecontentdao = jDao::get("jphpdoc~files_content");
        $line = jDao::createRecord("jphpdoc~files_content");
        foreach ($lines as $n=>$l) {
            $line->file_id = $fileRec->id;
            $line->project_id = $this->projectRec->id;
            $line->linenumber = $n;
            $line->content = $l;
            $filecontentdao->insert($line);   
        }

/*
        $tokens = new ArrayObject(token_get_all($content));

        $filepath=substr($filepath, strlen($this->fullSourcePath)+1);

        $this->fileList[$filepath] = new jFileDescriptor($filepath, $filename);

        $fileparser = new jFileParser( $tokens->getIterator(), $this->fileList[$filepath]);

        $fileparser->parse();
*/
    }



    public function getClassInfo($classname){

    }

    public function getFunctionInfo($classInfo){

    }

    public function getGlobalVarInfo($classInfo){

    }

}

?>