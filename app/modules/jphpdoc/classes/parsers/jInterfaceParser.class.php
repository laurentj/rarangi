<?php
/**
* @package     jPhpDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/jphpdoc
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
/**
 * Object which parses an interface content
 */
class jInterfaceParser extends jParser_base {

    /**
     * @param jParser_base $fatherParser  the parser which instancy this class
     * @param string $doccomment the documented comment associated to the class
     * @param boolean $isAbstract  indicates if the class is an abstract class
     */
    function __construct($fatherParser, $doccomment){
        parent::__construct($fatherParser);
        $this->info = new jInterfaceDescriptor($this->parserInfo->getProjectId(),
                                           $fatherParser->getInfo()->fileId,
                                           $this->parserInfo->currentLine());
        $this->info->inheritsFrom($fatherParser->getInfo());
        $this->info->initFromPhpDoc($doccomment);
    }

    public function parse(){

        $this->info->name = $this->toNextSpecificPhpToken(T_STRING);

        $this->parseDeclaration();
        
        $bracketlevel = 1;

        $previousDocComment = '';
        $doExit = false;
        
        $memberAccessibility = T_PUBLIC;
        $memberStatic = false;
        $memberFinal = false;
        $functionAbstract = false;
        
        $propertyExpected = false;
        $constExpected = false;
        
        while(!$doExit &&  ($tok = $this->toNextPhpToken()) !== false ) {
            if (is_array($tok)) {
                switch($tok[0]){

                case T_FUNCTION:
                    //$subparser = new jFunctionParser($this, $previousDocComment, $memberAccessibility, $memberStatic, $memberFinal, $functionAbstract);
                    //$subparser->parse();
                    $memberAccessibility = 0;
                    $memberStatic = false;
                    $memberFinal = false;
                    $functionAbstract = false;
                    break;
                case T_VARIABLE:
                    //$subparser = new jPropertyParser($this, $previousDocComment);
                    //$subparser->parse();
                    $propertyExpected = false;
                    $memberAccessibility = 0;
                    $memberStatic = false;
                    $memberFinal = false;
                    break;
                case T_DOC_COMMENT:
                    $previousDocComment = $tok[1];
                    break;
                case T_CONST:
                    $constExpected = true;
                    break;
                case T_FINAL:
                    $memberFinal = true;
                    break;
                case T_PRIVATE:
                    $memberAccessibility = T_PRIVATE;
                    break;
                case T_VAR:
                    $propertyExpected = true;
                case T_PUBLIC:
                    $memberAccessibility = T_PUBLIC;
                    break;
                case T_PROTECTED:
                    $memberAccessibility = T_PROTECTED;
                    break;
                case T_STATIC:
                    $memberStatic = true;
                    break;
                case T_ABSTRACT:
                    $functionAbstract = true;
                    break;
                }
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
                $previousDocComment = '';
            }
        }
        $this->info->save();
    }
 

    protected function parseDeclaration(){
         // read the content between the name and the next '{'
        $tok = $this->toNextPhpToken();

        while(is_array($tok)) {
            if($tok[0] == T_COMMENT) {
                $tok = $this->toNextPhpToken();
                continue;
            }
            if($tok[0] != T_EXTENDS) {
                throw new Exception ("Interface parsing, invalid syntax, bad token : ".token_name($tok[0]));
            }
            $this->info->inheritsFrom = $this->toNextSpecificPhpToken(T_STRING);
            $tok = $this->toNextPhpToken();
            break;
        }

        if(!is_string($tok) || $tok != '{' )
            throw new Exception ("Interface parsing, invalid syntax, '{' expected");
    }

}
?>