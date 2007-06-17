<?php
/**
* @package     jDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2007 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/


abstract class jBaseParser {
    protected $iterator;
    protected $manager;

    function __construct( $it){
        $this->manager = jDoc::getInstance();
        $this->iterator = $it;
    }

    abstract public function parse();

    /**
     * set the cursor after the next "<?php" token
     */
    protected function toNextPhpSection(){
        while($this->iterator->valid()) {
            $tok = $this->iterator->current();

            if(is_array($tok)){
                
                if($tok[0] == T_OPEN_TAG){
                    jDoc::incLineS($tok[1]); //the token include next \n some time
                    return;
                }
                if(in_array($tok[0], array(T_COMMENT, T_DOC_COMMENT,T_ENCAPSED_AND_WHITESPACE, 
                            T_INLINE_HTML, T_STRING, T_WHITESPACE)))
                    jDoc::incLineS($tok[1]);
            }else{
                jDoc::incLineS($tok);
            }
            $this->iterator->next();
        }
    }

    /**
     * set the cursor on the next non whitespace token
     */
    protected function toNextPhpToken(){
        if(!$this->iterator->valid())
            return;
        $this->iterator->next();

        while($this->iterator->valid()) {
            $tok = $this->iterator->current();

            if(is_array($tok)){
                if($tok[0] == T_WHITESPACE || $tok[0] == T_ENCAPSED_AND_WHITESPACE || $tok[0] == T_INLINE_HTML){
                    jDoc::incLineS($tok[1]);
                }elseif($tok[0] == T_CLOSE_TAG) {
                    $this->iterator->next();
                    $this->toNextPhpSection();
                    if(!$this->iterator->valid())
                        return;
                }else
                    return;
            }elseif(!preg_match('/^\s*$/',$tok)){
                jDoc::incLineS($tok);
                return;
            }else{
                jDoc::incLineS($tok);
            }
            $this->iterator->next();
        }
    }

    /**
     * skip a PHP bloc
     */
    protected function skipPhpBlock(){
        if(!$this->iterator->valid())
            return;
        $this->iterator->next();
        if(!$this->iterator->valid())
            return;
        

    }

}


class jFileParser extends jBaseParser {
    protected $fileinfo;

    function __construct( $it, $fileinfo){
        $this->fileinfo = $fileinfo;
        parent::__construct( $it);
    }

    public function parse(){
        $this->toNextPhpSection();
        $this->toNextPhpToken();
        if(!$this->iterator->valid()){
            jLogger::notice(" file is empty");
            return;
        }

        $tok = $this->iterator->current();
        if(!is_array($tok)){
            jLogger::warning("The file is not beginning by a doc comment !");
        }elseif($tok[0] != T_DOC_COMMENT){
            jLogger::warning("The file is not beginning by a doc comment (2) !");
        }else{
            $this->fileinfo->initFromPhpDoc($tok[0]);
        }
        jLogger::message($this->fileinfo->filepath. " is ok \n");
    }

}


?>