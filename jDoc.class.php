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

    public function setConfig($conf){
        $this->config = $conf;
    }

    /**
     * main method. launches the parsing
     * @param string $sourcepath  the path of the main directory which contains sources
     */
    public function run($sourcepath){
        
        $sourcepath = realpath($sourcepath);
        $this->fullSourcePath = $sourcepath;
        if($sourcepath !='')
            $this->readFiles(new RecursiveDirectoryIterator($sourcepath));
        else
            throw new Exception("unknow path");
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
        $content = file_get_contents($filepath);

        $tokens = new ArrayObject(token_get_all($content));

        $filepath=substr($filepath, strlen($this->fullSourcePath)+1);

        $this->fileList[$filepath] = new jFileDescriptor($filepath, $filename);

        $fileparser = new jFileParser( $tokens->getIterator(), $this->fileList[$filepath]);

        $fileparser->parse();
    }



    public function getClassInfo($classname){

    }

    public function getFunctionInfo($classInfo){

    }

    public function getGlobalVarInfo($classInfo){

    }

}

?>