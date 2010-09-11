<?php
/**
* @package     rarangi
* @subpackage  tests
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007-2009 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

require_once( dirname(__FILE__).'/../classes/raLogger.class.php');
require_once( dirname(__FILE__).'/../classes/raProject.class.php');
require_once( dirname(__FILE__).'/../classes/raDocGenerator.class.php');
require_once( dirname(__FILE__).'/../classes/raDescriptor.lib.php');

class ut_project_test extends raProject {

    function __construct($logger) {
        $this->_logger = $logger;
        $this->init('test');
    }
}

class ut_file_parser_test extends raPHPFileParser {

    /**
     * @param Iterator $it  the iterator on tokens
     */
    function __construct($content, $parserInfo){

        $this->parserInfo = $parserInfo;

        $this->descriptor = new raFileDescriptor($this->parserInfo->project(),
                                          $this->parserInfo->getFullSourcePath(),
                                          $this->parserInfo->currentFile(),
                                          $this->parserInfo->currentFileName());

        $tokens = new ArrayObject(token_get_all($content));
        $this->iterator = $tokens->getIterator();
    }
}

class ut_interface_parser_test extends raPHPInterfaceParser {
    
    public $tokAfterInit = null;
    
    function __construct ($content, $numberOfToken,$parserInfo, 
                          $doccomment = "/**\n*/", $fatherDesc = null) {

        $tokens = new ArrayObject(token_get_all($content));
        $this->iterator = $tokens->getIterator();

        $this->parserInfo = $parserInfo;
        
        $this->toNextPhpSection();
        for ($i=0; $i< $numberOfToken; $i++)
            $this->tokAfterInit = $this->toNextPhpToken();

        if (!$fatherDesc)
            $fatherDesc = new raFileDescriptor ($this->parserInfo->project(),
                                                $this->parserInfo->getFullSourcePath(),
                                                $this->parserInfo->currentFile(),
                                                $this->parserInfo->currentFileName());

        $this->descriptor = new raInterfaceDescriptor($this->parserInfo->project(),
                                                      1,
                                                      $this->parserInfo->currentLine());
        $this->descriptor->inheritsFrom($fatherDesc);
        $this->descriptor->initFromPhpDoc($doccomment);
    }

    function getIterator() { return $this->iterator;}

}


class ut_class_parser_test extends raPHPClassParser {
    
    public $tokAfterInit = null;
    
    function __construct ($content, $numberOfToken, $parserInfo, $doccomment="/**\n*/", $isAbstract=false){
        $tokens = new ArrayObject(token_get_all($content));
        $this->iterator = $tokens->getIterator();
        $this->parserInfo = $parserInfo;
        $this->toNextPhpSection();
        for($i=0; $i< $numberOfToken;$i++)
            $this->tokAfterInit = $this->toNextPhpToken();

        $fatherInfo =  new raFileDescriptor($this->parserInfo->project(),
                                          $this->parserInfo->getFullSourcePath(),
                                          $this->parserInfo->currentFile(),
                                          $this->parserInfo->currentFileName());

        $this->descriptor = new raClassDescriptor($this->parserInfo->project(),
                                                  1,
                                                  $this->parserInfo->currentLine());
        $this->descriptor->inheritsFrom($fatherInfo);
        $this->descriptor->initFromPhpDoc($doccomment);
        $this->descriptor->isAbstract = $isAbstract;
    }

    function getIterator() { return $this->iterator;}
}


class ut_function_parser_test extends raPHPFunctionParser {
    
    public $tokAfterInit = null;
    
    function __construct ($content, $numberOfToken, $parserInfo, $doccomment="/**\n*/"){
        $tokens = new ArrayObject(token_get_all($content));
        $this->iterator = $tokens->getIterator();
        $this->parserInfo = $parserInfo;
        
        $this->toNextPhpSection();
        for($i=0; $i< $numberOfToken;$i++)
            $this->tokAfterInit = $this->toNextPhpToken();

        $fatherInfo =  new raFileDescriptor($this->parserInfo->project(),
                                          $this->parserInfo->getFullSourcePath(),
                                          $this->parserInfo->currentFile(),
                                          $this->parserInfo->currentFileName());
        
        $this->isMethod = false;
        $this->descriptor = new raFunctionDescriptor($this->parserInfo->project(),
                                                     1,
                                                     $this->parserInfo->currentLine());
        $this->descriptor->inheritsFrom($fatherInfo);
        $this->descriptor->initFromPhpDoc($doccomment);
    }

    function getIterator() { return $this->iterator;}
}

class ut_method_parser_test extends raPHPFunctionParser {
    
    public $tokAfterInit = null;
    
    function __construct ($content, $numberOfToken, $parserInfo, $doccomment="/**\n*/",
                         $accessibility=0, $isStatic=false, $isFinal=false,
                         $isAbstract=false, $isInterface=false){
        $tokens = new ArrayObject(token_get_all($content));
        $this->iterator = $tokens->getIterator();
        $this->parserInfo = $parserInfo;

        $this->toNextPhpSection();
        for($i=0; $i< $numberOfToken;$i++)
            $this->tokAfterInit = $this->toNextPhpToken();

        $fatherInfo =  new raClassDescriptor($this->parserInfo->project(), 1, 1);

        $this->isMethod = true;
        $this->descriptor = new raMethodDescriptor($this->parserInfo->project(),
                                                   1,
                                                   $this->parserInfo->currentLine());
        $this->descriptor->inheritsFrom($fatherInfo);
        $this->descriptor->initFromPhpDoc($doccomment);
        $this->descriptor->accessibility = $accessibility;
        $this->descriptor->isStatic = $isStatic;
        $this->descriptor->isFinal = $isFinal;
        $this->descriptor->isAbstract = $isAbstract;
        $this->descriptor->classId = 1;
        $this->isInInterface = $isInterface;
    }

    function getIterator() { return $this->iterator;}
}

//
//class bdesc_test extends raBaseDescriptor {
// 
//   function cleanPackage
//    
//}