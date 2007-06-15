<?php
/**
* @package     jDoc
* @author      Jouanneau Laurent
* @contributor
* @copyright   2006 Jouanneau laurent
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/



class jDoc {

    public $config;
    protected $currentFile;
    protected $currentLine;

    protected function __construct(){ }

    static public function getInstance(){
        static $doc=null;
        if($doc === null){
            $doc = new jDoc();
        }
        return $doc;
    }

    static public function currentFile() { $d = self::getInstance(); return $d->currentFile; }
    static public function currentLine() { $d = self::getInstance(); return $d->currentLine; }
    static public function incLine($nb) { $d = self::getInstance(); $d->currentLine+= $nb; }
    static public function incLineS($str) { $d = self::getInstance(); $d->currentLine+= substr_count($str, "\n"); }



    public function setConfig($conf){
        $this->config = $conf;
    }


    public $originalSourcePath;

    public function run($sourcepath){
        
        $sourcepath = realpath($sourcepath);
        $this->fullSourcePath = $sourcepath;
        if($sourcepath !='')
            $this->readFiles(new RecursiveDirectoryIterator($sourcepath));
        else
            die ("unknow path\n");
    }

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


    protected function addPhpFile($filepath, $filename){
        $content = file_get_contents($filepath);
    
        $tokens = new ArrayObject(token_get_all($content));
        $tokiter = $tokens->getIterator();
    
        $filepath=substr($filepath, strlen($this->fullSourcePath)+1);

        $this->fileList[$filepath] = new jFileInfo($filepath, $filename);
    
        $fileparser = new jFileParser( $tokiter, $this->fileList[$filepath]);
    
        $fileparser->parse();
    }

    protected $fileList=array();
    protected $classList=array();
    protected $functionList=array();
    protected $globalVarList=array();

    public function getClassInfo($classname){

    }

    public function getFunctionInfo($classInfo){

    }

    public function getGlobalVarInfo($classInfo){

    }

}

?>