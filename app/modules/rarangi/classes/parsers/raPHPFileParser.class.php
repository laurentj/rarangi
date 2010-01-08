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
require($dirnamefile.'raPHPParser_base.class.php');
require($dirnamefile.'raPHPInterfaceParser.class.php');
require($dirnamefile.'raPHPClassParser.class.php');
require($dirnamefile.'raPHPIncludeParser.class.php');
require($dirnamefile.'raPHPFunctionParser.class.php');
require($dirnamefile.'raPHPDefineParser.class.php');
require($dirnamefile.'raPHPGlobalVariableParser.class.php');

/**
 * Object which parses a file content
 */
class raPHPFileParser extends raPHPParser_base {

    /**
     * @param Iterator $it  the iterator on tokens
     */
    function __construct($parserInfo){
        
        $this->descriptor = new raFileDescriptor($parserInfo->project(),
                                          $parserInfo->getFullSourcePath(),
                                          $parserInfo->currentFile(),
                                          $parserInfo->currentFileName());

        $parserInfo->project()->logger()->message("Parsing file ".$this->descriptor->filepath);
        
        $content = file_get_contents($this->descriptor->fullpath);
        $lines = explode("\n", $content);
        $filecontentdao = jDao::get("rarangi~files_content");
        $line = jDao::createRecord("rarangi~files_content");
        foreach ($lines as $n=>$l) {
            $line->file_id = $this->descriptor->fileId;
            $line->project_id = $parserInfo->getProjectId();
            $line->linenumber = $n+1;
            $line->content = $l;
            $filecontentdao->insert($line);   
        }
        
        $tokens = new ArrayObject(token_get_all($content));
        $this->iterator = $tokens->getIterator();
        $this->parserInfo = $parserInfo;
    }

    public function parse() {
        $this->toNextPhpSection();
        $tok = $this->toNextPhpToken();
        if ($tok === false) {
            $this->parserInfo->project()->logger()->notice("file is empty");
            return;
        }

        if (!is_array($tok)) {
            $this->parserInfo->project()->logger()->warning("The file is not beginning by a doc comment !");
        }
        elseif ($tok[0] != T_DOC_COMMENT) {
            $this->parserInfo->project()->logger()->warning("The file is not beginning by a doc comment (2) !");
        }
        else {
            $this->descriptor->initFromPhpDoc($tok[1]);
        }

        try {
            

        $previousDocComment = '';
        $isAbstract = false;
        while( ($tok = $this->toNextPhpToken()) !== false) {
            if (is_array($tok)) {
                switch($tok[0]){
                case T_CLASS:
                    $subparser = new raPHPClassParser($this, $previousDocComment, $isAbstract);
                    $subparser->parse();
                    $previousDocComment = '';
                    $isAbstract = false;
                    break;
                case T_INTERFACE:
                    $subparser = new raPHPInterfaceParser($this, $previousDocComment);
                    $subparser->parse();
                    $previousDocComment = '';
                    $isAbstract = false;
                    break;
                case T_FUNCTION:
                    $subparser = new raPHPFunctionParser($this, $previousDocComment);
                    $subparser->parse();
                    $previousDocComment = '';
                    $isAbstract = false;
                    break;
                case T_INCLUDE:
                case T_INCLUDE_ONCE:
                case T_REQUIRE:
                case T_REQUIRE_ONCE:
                    //$subparser = new raPHPIncludeParser($this, $previousDocComment);
                    //$subparser->parse();
                    $this->jumpToSpecificPhpToken(';');
                    break;
                case T_VARIABLE:
                    $subparser = new raPHPGlobalVariableParser($this, $previousDocComment);
                    $subparser->parse();
                    $previousDocComment = '';
                    $isAbstract = false;
                    break;

                case T_ABSTRACT:
                    $isAbstract = true;
                    break;

                case T_DOC_COMMENT:
                    $previousDocComment = $tok[1];
                    break;

                case T_STRING:
                    switch ($tok[1]) {
                    case 'define':
                        $subparser = new raPHPDefineParser($this, $previousDocComment);
                        $subparser->parse();
                        $previousDocComment = '';
                        $isAbstract = false;
                        break;
                    default:
                        // ignore the rest of the instruction
                        $this->jumpToSpecificPhpToken(array(';', T_CLOSE_TAG));
                    }
                    $previousDocComment = '';
                    break;
                case T_COMMENT:
                    break;
                case T_CLOSE_TAG:
                case T_INLINE_HTML:
                    $this->toNextPhpSection();
                    break;
                default:
                    $this->jumpToSpecificPhpToken(array(';', T_CLOSE_TAG));
                }
            }
            else {
                $previousDocComment = '';
            }
        }
        }
        catch(jException $e) {
            $this->parserInfo->project()->logger()->error($e->getMessage());
            /*$GLOBALS['gJCoord']->handleError($GLOBALS['gJConfig']->error_handling['exception'], 'exception',
            $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());*/
            return;       
        }
        catch(Exception $e) {
            $this->parserInfo->project()->logger()->error($e->getMessage());
            return;
        }
        $this->descriptor->save();
    }
}
