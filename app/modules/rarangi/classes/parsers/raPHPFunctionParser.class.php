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
    function __construct( $fatherParser, $doccomment, $accessibility=0, $isStatic=false, $isFinal=false, $isAbstract=false){
        
        parent::__construct($fatherParser);
        
        if($fatherParser instanceof raPHPClassParser || $fatherParser instanceof raPHPInterfaceParser) {
            if (!($fatherParser instanceof raPHPClassParser))
                $this->isInInterface = true;
            $this->isMethod = true;
            $this->info = new raMethodDescriptor($this->parserInfo->getProjectId(),
                                               $fatherParser->getInfo()->fileId,
                                               $this->parserInfo->currentLine());
            $this->info->inheritsFrom($fatherParser->getInfo());
            $this->info->initFromPhpDoc($doccomment);
            $this->info->accessibility = $accessibility;
            $this->info->isStatic = $isStatic;
            $this->info->isFinal = $isFinal;
            $this->info->isAbstract = $isAbstract;
        }
        else {
            $this->isMethod = false;
            $this->info = new raFunctionDescriptor($this->parserInfo->getProjectId(),
                                               $fatherParser->getInfo()->fileId,
                                               $this->parserInfo->currentLine());
            $this->info->inheritsFrom($fatherParser->getInfo());
            $this->info->initFromPhpDoc($doccomment);
        }
    }

    public function parse(){
        $this->info->name = $this->toNextSpecificPhpToken(T_STRING);

        $this->toNextSpecificPhpToken('(');
        $tok = $this->toNextPhpToken();
        while($tok != ')') {
            if (is_array($tok)) {
                if ($tok[0] == T_STRING) {
                    $this->toNextSpecificPhpToken(T_VARIABLE);
                    list($pname, $pvalue) = $this->readVarnameAndValue(array(',',')'));
                    $tok = $this->iterator->current();
                }
                else if ($tok[0] == T_VARIABLE) {
                    list($pname, $pvalue) = $this->readVarnameAndValue(array(',',')'));
                    $tok = $this->iterator->current();
                }
                else
                    $tok = $this->toNextPhpToken();
            }
            else {
                $tok = $this->toNextPhpToken();
            }
        }

        if ($this->isMethod && ($this->info->isAbstract || $this->isInInterface)) {
            $this->toNextSpecificPhpToken(';');
            return;
        }

        $this->toNextSpecificPhpToken('{');
        $bracketlevel = 1;
        $doExit = false;

        while(!$doExit &&  ($tok = $this->toNextPhpToken()) !== false ) {
            if (is_array($tok)) {
                /*switch($tok[0]){
                case T_:
                    break;
                }*/
            } else {
                switch($tok){
                case '{':
                    $bracketlevel++;
                    break;
                case '}':
                    $bracketlevel--;
                    $doExit = ($bracketlevel == 0);
                    break;
                default:
                }
            }
        }

        if(!$this->isMethod) $this->info->save();
    }

}
