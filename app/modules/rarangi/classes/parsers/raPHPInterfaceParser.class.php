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
 * Object which parses an interface content
 */
class raPHPInterfaceParser extends raPHPParser_base {

    /**
     * @param jParser_base $fatherParser  the parser which instancy this class
     * @param string $doccomment the documented comment associated to the class
     * @param boolean $isAbstract  indicates if the class is an abstract class
     */
    function __construct($fatherParser, $doccomment){
        parent::__construct($fatherParser);
        $this->info = new raInterfaceDescriptor($this->parserInfo->getProjectId(),
                                           $fatherParser->getInfo()->fileId,
                                           $this->parserInfo->currentLine());
        $this->info->inheritsFrom($fatherParser->getInfo());
        $this->info->initFromPhpDoc($doccomment);
    }

    const MEMBER_TYPE_CONST = 1;
    const MEMBER_TYPE_VAR = 2;
    const MEMBER_TYPE_FUNC = 3;
    const MEMBER_TYPE_FUNC_ABST = 4;

    public function parse(){

        $this->info->name = $this->toNextSpecificPhpToken(T_STRING);
        if ($this->info instanceof raClassDescriptor)
            raLogger::message("   parsing class ".$this->info->name);
        else
            raLogger::message("   parsing interface ".$this->info->name);
        $this->parseDeclaration();
        
        $bracketlevel = 1;

        $previousDocComment = '';
        $doExit = false;
        
        $memberAccessibility = T_PUBLIC;
        $memberStatic = false;
        $memberFinal = false;
        $memberType = 0;
        
        while(!$doExit &&  ($tok = $this->toNextPhpToken()) !== false ) {
            if (is_array($tok)) {
                switch($tok[0]){
                case T_FUNCTION:
                    $subparser = new raPHPFunctionParser($this, $previousDocComment, $memberAccessibility, $memberStatic, $memberFinal, $memberType == self::MEMBER_TYPE_FUNC_ABST);
                    $subparser->parse();
                    $this->info->members[]=$subparser->getInfo();
                    $memberAccessibility = T_PUBLIC;
                    $memberStatic = false;
                    $memberFinal = false;
                    $memberType= 0;
                    break;
                case T_VARIABLE:
                    $info = new raPropertyDescriptor($this->info->projectId, $this->info->fileId, $this->parserInfo->currentLine());
                    $info->initFromPhpDoc($previousDocComment);
                    $info->accessibility = $memberAccessibility;
                    if ($memberStatic)
                      $info->typeProperty = raPropertyDescriptor::TYPE_STATIC_VAR;
                    list($pname, $pvalue) = $this->readVarnameAndValue(';');
                    $info->name = $pname;
                    $info->defaultValue = $pvalue;
                    $this->info->members[]=$info;
                    $memberAccessibility = T_PUBLIC;
                    $memberStatic = false;
                    $memberFinal = false;
                    $memberType= 0;
                    break;
                case T_DOC_COMMENT:
                    $previousDocComment = $tok[1];
                    break;
                case T_CONST:
                    $memberType = self::MEMBER_TYPE_CONST;
                    break;
                case T_FINAL:
                    $memberFinal = true;
                    $memberType = self::MEMBER_TYPE_FUNC;
                    break;
                case T_PRIVATE:
                    $memberAccessibility = T_PRIVATE;
                    break;
                case T_VAR:
                    $memberType = self::MEMBER_TYPE_VAR;
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
                    $memberType = self::MEMBER_TYPE_FUNC_ABST;
                    break;
                case T_STRING:
                    if ($memberType == self::MEMBER_TYPE_CONST) {
                      $info = new raPropertyDescriptor($this->info->projectId, $this->info->fileId, $this->parserInfo->currentLine());
                      $info->initFromPhpDoc($previousDocComment);
                      $info->accessibility = $memberAccessibility;
                      $info->typeProperty = raPropertyDescriptor::TYPE_CONST;
                      list($pname, $pvalue) = $this->readConstAndValue(';');
                      $info->name = $pname;
                      $info->defaultValue = $pvalue;
                      $this->info->members[]=$info;
                      $memberAccessibility = T_PUBLIC;
                      $memberStatic = false;
                      $memberFinal = false;
                      $memberType= 0;
                    }
                    else
                       throw new Exception ("Interface/class parsing, invalid syntax, unexpected string '".$tok[1]."'");
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
        $this->info->lineEnd = $this->parserInfo->currentLine();
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