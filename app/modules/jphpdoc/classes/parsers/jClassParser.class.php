<?php
/**
* @package     jDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

/**
 * Object which parses a class content
 */
class jClassParser extends jParser_base {

    /**
     * @var jClassDescriptor
     */
    protected $info;

    /**
     * @param Iterator $it  the iterator on tokens
     * @param string $doccomment the documented comment associated to the class
     */
    function __construct( $it, $doccomment){
        $this->info = new jClassDescriptor();
        $this->info->initFromPhpDoc($doccomment);
        parent::__construct( $it);
    }

    public function parse(){
/*
        $tok = $this->toNextSpecificPhpToken(T_STRING);
        
        $this->info->name = $tok;

        $tok = $this->toNextPhpToken();
        while(is_array($tok)) {
            if($tok[0] != T_IMPLEMENTS || $tok[0] != T_EXTENDS) {
                if($tok[0] == T_COMMENT) {
                    $tok = $this->toNextPhpToken();
                    continue;
                }
                throw new Exception ("invalid syntax");
            }
            $type = $tok[0];
            if($type == T_IMPLEMENTS)
                $this->info->interfaces[] = $this->toNextSpecificPhpToken(T_STRING);
            else
                $this->info->inherits[] = $this->toNextSpecificPhpToken(T_STRING);
            $tok = $this->toNextPhpToken();
            if(is_string($tok)&& $tok ==',')
                $tok = $this->toNextPhpToken();
        }

        if(!is_string($tok) || $tok != '{' )
            throw new Exception ("invalid syntax");

        $bracketlevel = 1;

        $previousDocComment = '';
        $doExit = false;
        while( ($tok = $this->toNextPhpToken()) !== false && !$doExit) {
            if (is_array($tok)) {
                switch($tok[0]){

                case T_FUNCTION:
                    //$subparser = new jFunctionParser($this->iterator, $previousDocComment);
                    //$subparser->parse();
                    break;
                case T_INCLUDE:
                case T_INCLUDE_ONCE:
                case T_REQUIRE:
                case T_REQUIRE_ONCE:
                    //$subparser = new jIncludeParser($this->iterator, $previousDocComment);
                    //$subparser->parse();
                    break;
                case T_VARIABLE:
                    //$subparser = new jGlobalVariableParser($this->iterator, $previousDocComment);
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
                    //$subparser = new jDefineParser($this->iterator, $previousDocComment);
                    //$subparser->parse();
                    break;
                default:
                }
                $previousDocComment = '';
            }
        } */
    }
}
?>