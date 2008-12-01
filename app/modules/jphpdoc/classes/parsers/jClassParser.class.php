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
 * Object which parses a class content
 */
class jClassParser extends jParser_base {

    /**
     * @param Iterator $it  the iterator on tokens
     * @param string $doccomment the documented comment associated to the class
     */
    function __construct($fatherParser, $doccomment){
        parent::__construct($fatherParser);
        $this->info = new jClassDescriptor($this->parserInfo->getProjectId(),
                                           $fatherParser->getInfo()->id,
                                           $this->parserInfo->currentLine());
        $this->info->initFromPhpDoc($doccomment);
    }

    public function parse(){

        $this->info->name = $this->toNextSpecificPhpToken(T_STRING);

        // read the content between the name and the next '{'
        $tok = $this->toNextPhpToken();
        while(is_array($tok)) {
            if($tok[0] != T_IMPLEMENTS && $tok[0] != T_EXTENDS) {
                if($tok[0] == T_COMMENT) {
                    $tok = $this->toNextPhpToken();
                    continue;
                }
                throw new Exception ("Class parsing, invalid syntax, bad token : ".token_name($tok[0]));
            }
            $type = $tok[0];
            if($type == T_IMPLEMENTS)
                $this->info->interfaces[] = $this->toNextSpecificPhpToken(T_STRING);
            else
                $this->info->inheritsFrom = $this->toNextSpecificPhpToken(T_STRING);
            $tok = $this->toNextPhpToken();
            if(is_string($tok)&& $tok ==',')
                $tok = $this->toNextPhpToken();
        }

        if(!is_string($tok) || $tok != '{' )
            throw new Exception ("Class parsing, invalid syntax");

        $bracketlevel = 1;

        $previousDocComment = '';
        $doExit = false;
        while(!$doExit &&  ($tok = $this->toNextPhpToken()) !== false ) {
            if (is_array($tok)) {
                switch($tok[0]){

                case T_FUNCTION:
                    //$subparser = new jFunctionParser($this, $previousDocComment);
                    //$subparser->parse();
                    break;
                case T_INCLUDE:
                case T_INCLUDE_ONCE:
                case T_REQUIRE:
                case T_REQUIRE_ONCE:
                    //$subparser = new jIncludeParser($this, $previousDocComment);
                    //$subparser->parse();
                    break;
                case T_VARIABLE:
                    //$subparser = new jGlobalVariableParser($this, $previousDocComment);
                    //$subparser->parse();
                    break;
                case T_DOC_COMMENT:
                    $previousDocComment = $tok[1];
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
                case 'define':
                    //$subparser = new jDefineParser($this, $previousDocComment);
                    //$subparser->parse();
                    break;
                default:
                }
                $previousDocComment = '';
            }
        }
        $this->info->save();
    }
}
