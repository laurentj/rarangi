<?php
/**
* @package     jDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2007 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

$dirnamefile = dirname(__FILE__).'/';
require($dirnamefile.'jParser_base.class.php');
require($dirnamefile.'jInterfaceParser.class.php');
require($dirnamefile.'jClassParser.class.php');
require($dirnamefile.'jIncludeParser.class.php');
require($dirnamefile.'jFunctionParser.class.php');
require($dirnamefile.'jGlobalVariableParser.class.php');
require($dirnamefile.'jDefineParser.class.php');

/**
 * Object which parses a file content
 */
class jFileParser extends jParser_base {

    /**
     * @var jFileDescriptor
     */
    protected $fileinfo;

    /**
     * @param Iterator $it  the iterator on tokens
     * @param jFileDescriptor $fileinfo 
     */
    function __construct( $it, $fileinfo){
        $this->fileinfo = $fileinfo;
        parent::__construct( $it);
    }

    public function parse(){
        $this->toNextPhpSection();
        $tok = $this->toNextPhpToken();
        if($tok === false){
            jLogger::notice(" file is empty");
            return;
        }

        if(!is_array($tok)){
            jLogger::warning("The file is not beginning by a doc comment !");
        }elseif($tok[0] != T_DOC_COMMENT){
            jLogger::warning("The file is not beginning by a doc comment (2) !");
        }else{
            $this->fileinfo->initFromPhpDoc($tok[1]);
        }

        $previousDocComment = '';
        while( ($tok = $this->toNextPhpToken()) !== false) {
            if (is_array($tok)) {
                switch($tok[0]){
                case T_CLASS:
                    $subparser = new jClassParser($this->iterator, $previousDocComment);
                    $subparser->parse();
                    break;
                /*case T_INTERFACE:
                    $subparser = new jInterfaceParser($this->iterator, $previousDocComment);
                    $subparser->parse();
                    break;
                case T_FUNCTION:
                    $subparser = new jFunctionParser($this->iterator, $previousDocComment);
                    $subparser->parse();
                    break;
                case T_INCLUDE:
                case T_INCLUDE_ONCE:
                case T_REQUIRE:
                case T_REQUIRE_ONCE:
                    $subparser = new jIncludeParser($this->iterator, $previousDocComment);
                    $subparser->parse();
                    break;
                case T_VARIABLE:
                    $subparser = new jGlobalVariableParser($this->iterator, $previousDocComment);
                    $subparser->parse();
                    break;*/
                case T_DOC_COMMENT:
                    $previousDocComment = $tok[1];
                    break;
                }
            } else {
                switch($tok){
                /*case 'define':
                    $subparser = new jDefineParser($this->iterator, $previousDocComment);
                    $subparser->parse();
                    break;*/
                default:
                    $previousDocComment = '';
                }
            }
        }
        jLogger::message($this->fileinfo->filepath. " is ok \n");
    }
}


?>