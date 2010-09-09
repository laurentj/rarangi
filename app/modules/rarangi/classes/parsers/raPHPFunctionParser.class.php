<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

/**
 * Object which parses a function content (method or standalone function)
 */
class raPHPFunctionParser extends raPHPParser_base {

    protected $isMethod = false;
    protected $isInInterface = false;

    /**
     * @param jParser_base $fatherParser  the parser which instancy this class
     * @param string $doccomment the documented comment associated to the function
     * @param integer $accessibility   T_PRIVATE, T_PUBLIC, T_PROTECTED
     * @param boolean $isStatic  indicates if the function is a static function of a class
     * @param boolean $isFinal indicates if the function is a final function in the class
     * @param boolean $isAbstract  indicates if the function is an abstract function in the class

     */
    function __construct ($fatherParser, $doccomment, $accessibility=0,
                         $isStatic=false, $isFinal=false, $isAbstract=false) {
        
        parent::__construct($fatherParser);
        
        if ($fatherParser instanceof raPHPClassParser || $fatherParser instanceof raPHPInterfaceParser) {
            if (!($fatherParser instanceof raPHPClassParser))
                $this->isInInterface = true;
            $this->isMethod = true;
            $this->descriptor = new raMethodDescriptor($this->parserInfo->project(),
                                               $fatherParser->getDescriptor()->fileId,
                                               $this->parserInfo->currentLine());
            $this->descriptor->inheritsFrom($fatherParser->getDescriptor());
            $this->descriptor->initFromPhpDoc($doccomment);
            $this->descriptor->accessibility = $accessibility;
            $this->descriptor->isStatic = $isStatic;
            $this->descriptor->isFinal = $isFinal;
            $this->descriptor->isAbstract = $isAbstract;
        }
        else {
            $this->isMethod = false;
            $this->descriptor = new raFunctionDescriptor($this->parserInfo->project(),
                                               $fatherParser->getDescriptor()->fileId,
                                               $this->parserInfo->currentLine());
            $this->descriptor->inheritsFrom($fatherParser->getDescriptor());
            $this->descriptor->initFromPhpDoc($doccomment);
        }
    }

    public function parse() {
        $this->descriptor->name = $this->toNextSpecificPhpToken(T_STRING);

        $this->toNextSpecificPhpToken('(');
        $tok = $this->toNextPhpToken();
        $this->descriptor->parameters = array();
        $pname = '';
        $pvalue = '';
        $ptype = '';
        // read parameters
        while($tok != ')' && $tok !== false) {
            if (is_array($tok)) {
                if ($tok[0] == T_STRING) {
                    $ptype = $tok[1];
                    $this->toNextSpecificPhpToken(T_VARIABLE);
                    list($pname, $pvalue) = $this->readVarnameAndValue(array(',',')'));
                    $tok = $this->iterator->current();
                    
                    $this->declareParameter($ptype, $pname, $pvalue);
                }
                else if ($tok[0] == T_VARIABLE) {
                    $ptype='';
                    list($pname, $pvalue) = $this->readVarnameAndValue(array(',',')'));
                    $tok = $this->iterator->current();
                    $this->declareParameter($ptype, $pname, $pvalue);
                }
                else
                    $tok = $this->toNextPhpToken();
            }
            else {
                $tok = $this->toNextPhpToken();
            }
        }
        if ($tok === false) {
            throw new Exception ("Function/method parsing, invalid syntax, no ended parenthesis or begin of bloc");
        }
        // stop here if it is an abstract method
        if ($this->isMethod && ($this->descriptor->isAbstract || $this->isInInterface)) {
            $this->toNextSpecificPhpToken(';');
            return;
        }

        $this->skipBlock(false);
        $this->descriptor->lineEnd = $this->parserInfo->currentLine();
        if(!$this->isMethod) $this->descriptor->save();
    }

    protected function declareParameter($type, $name, $defaultvalue) {
        $doc = '';
        if (isset($this->descriptor->docParameters[$name])) {
            $docparam = $this->descriptor->docParameters[$name];
            if ($type == '')
                $type = $docparam[0];
            $doc = $docparam[1];
        }
        $this->descriptor->parameters[] = array($type, $name, $defaultvalue, $doc);
    }

}
