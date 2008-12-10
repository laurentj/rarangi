<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

$dirnamefile = dirname(__FILE__).'/';
require($dirnamefile.'jPHPParser_base.class.php');
require($dirnamefile.'jPHPInterfaceParser.class.php');
require($dirnamefile.'jPHPClassParser.class.php');
require($dirnamefile.'jPHPIncludeParser.class.php');
require($dirnamefile.'jPHPFunctionParser.class.php');
require($dirnamefile.'jPHPDefineParser.class.php');

/**
 * Object which parses a file content
 */
class jPHPFileParser extends jPHPParser_base {

    /**
     * @param Iterator $it  the iterator on tokens
     */
    function __construct($parserInfo){
        
        $this->info = new jFileDescriptor($parserInfo->getProjectId(),
                                          $parserInfo->getFullSourcePath(),
                                          $parserInfo->currentFile(),
                                          $parserInfo->currentFileName());
        jLogger::message("Parsing file ".$this->info->filepath);
        $content = file_get_contents($this->info->fullpath);

        $lines = explode("\n", $content);
        $filecontentdao = jDao::get("rarangi~files_content");
        $line = jDao::createRecord("rarangi~files_content");
        foreach ($lines as $n=>$l) {
            $line->file_id = $this->info->fileId;
            $line->project_id = $parserInfo->getProjectId();
            $line->linenumber = $n;
            $line->content = $l;
            $filecontentdao->insert($line);   
        }
        
        $tokens = new ArrayObject(token_get_all($content));
        $this->iterator = $tokens->getIterator();
        $this->parserInfo = $parserInfo;
    }

    public function parse(){
        $this->toNextPhpSection();
        $tok = $this->toNextPhpToken();
        if($tok === false){
            jLogger::notice("file is empty");
            return;
        }

        if(!is_array($tok)){
            jLogger::warning("The file is not beginning by a doc comment !");
        }elseif($tok[0] != T_DOC_COMMENT){
            jLogger::warning("The file is not beginning by a doc comment (2) !");
        }else{
            $this->info->initFromPhpDoc($tok[1]);
        }

        try {
            

        $previousDocComment = '';
        $isAbstract = false;
        while( ($tok = $this->toNextPhpToken()) !== false) {
            if (is_array($tok)) {
                switch($tok[0]){
                case T_CLASS:
                    $subparser = new jPHPClassParser($this, $previousDocComment, $isAbstract);
                    $subparser->parse();
                    $previousDocComment = '';
                    $isAbstract = false;
                    break;
                case T_INTERFACE:
                    $subparser = new jPHPInterfaceParser($this, $previousDocComment);
                    $subparser->parse();
                    $previousDocComment = '';
                    $isAbstract = false;
                    break;
                case T_FUNCTION:
                    $subparser = new jPHPFunctionParser($this, $previousDocComment);
                    $subparser->parse();
                    $previousDocComment = '';
                    $isAbstract = false;
                    break;
                /*case T_INCLUDE:
                case T_INCLUDE_ONCE:
                case T_REQUIRE:
                case T_REQUIRE_ONCE:
                    $subparser = new jPHPIncludeParser($this, $previousDocComment);
                    $subparser->parse();
                    break;
                case T_VARIABLE:
                    break;*/
                case T_ABSTRACT:
                    $isAbstract = true;
                    break;
                case T_DOC_COMMENT:
                    $previousDocComment = $tok[1];
                    break;
                }
            } else {
                switch($tok){
                /*case 'define':
                    $subparser = new jDefineParser($this, $previousDocComment);
                    $subparser->parse();
                    break;*/
                default:
                    $previousDocComment = '';
                }
            }
        }
        } catch(jException $e) {
            $GLOBALS['gJCoord']->handleError($GLOBALS['gJConfig']->error_handling['exception'], 'exception',
            $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());
            return;       
        } catch(Exception $e) {
            jLogger::error($e->getMessage());
            return;
        }
        $this->info->save();
    }
}


?>