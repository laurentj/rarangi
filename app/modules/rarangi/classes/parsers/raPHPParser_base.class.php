<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/


abstract class raPHPParser_base {

    /**
     * @var jDescriptor
     */
    protected $info;
    
    protected $iterator;

    /**
     * @var jParserInfo
     */
    public $parserInfo;

    function __construct( $fatherParser){
        $this->iterator = $fatherParser->iterator;
        $this->parserInfo = $fatherParser->parserInfo;
    }

    abstract public function parse();
    
    public function getInfo() { return $this->info; }
    
    public function getParserInfo() { return $this->parserInfo; }
    
    public function getIterator() { return $this->iterator; }

    /**
     * set the cursor after the next "<?php" token
     */
    protected function toNextPhpSection(){
        while($this->iterator->valid()) {
            $tok = $this->iterator->current();

            if(is_array($tok)){
                if($tok[0] == T_OPEN_TAG){
                    $this->parserInfo->incLineS($tok[1]); //the token include next \n some time
                    return;
                }
                if(in_array($tok[0], array(T_COMMENT, T_DOC_COMMENT,T_ENCAPSED_AND_WHITESPACE, 
                            T_INLINE_HTML, T_STRING, T_WHITESPACE)))
                    $this->parserInfo->incLineS($tok[1]);
            }else{
                $this->parserInfo->incLineS($tok);
            }
            $this->iterator->next();
        }
    }

    /**
     * set the cursor on the next non whitespace token
     * @return boolean|array|string  return the next token found, or false if there isn't anymore token
     */
    protected function toNextPhpToken(){
        if(!$this->iterator->valid())
            return false;
        $this->iterator->next();

        while($this->iterator->valid()) {
            $tok = $this->iterator->current();

            if(is_array($tok)){
                if($tok[0] == T_WHITESPACE || $tok[0] == T_ENCAPSED_AND_WHITESPACE || $tok[0] == T_INLINE_HTML){
                    $this->parserInfo->incLineS($tok[1]);
                }elseif($tok[0] == T_CLOSE_TAG) {
                    $this->iterator->next();
                    $this->toNextPhpSection();
                    if(!$this->iterator->valid())
                        return false;
                }else
                    return $tok;
            }elseif(!preg_match('/^\s*$/',$tok)){
                $this->parserInfo->incLineS($tok);
                return $tok;
            }else{
                $this->parserInfo->incLineS($tok);
            }
            $this->iterator->next();
        }
        return false;
    }
    
    protected function toNextSpecificPhpToken($tokentype) {
        $tok = $this->toNextPhpToken();
        if(is_string($tokentype)) {
            if(is_string($tok))
                return $tok;
            else
                throw new Exception ("invalid syntax. token expected : string \"$tokentype\", got ".token_name($tok[0]));
        }
        else {
            if(!is_array($tok))
                throw new Exception ("invalid syntax. token expected : ".token_name($tokentype).", got a string :\"$tok\"" );
            if($tok[0] != $tokentype)
                throw new Exception ("invalid syntax token expected : ".token_name($tokentype).", got another token : ".token_name($tok[0]));
            return $tok[1];
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

    protected function readVarnameAndValue($endToken = ';') {
        $tok = $this->iterator->current();
        if(is_array($tok) && $tok[0] != T_VARIABLE)
            throw new Exception('not a variable declaration');
        $name = substr($tok[1],1);
        $tok = $this->toNextPhpToken();
        if(!$tok || !is_string($tok))
            throw new Exception('variable declaration invalid');
        if($tok == '=') {
            $value = '';
            $tok = $this->toNextPhpToken();
            $exit = $this->isEndToken($tok, $endToken);
            $insideArray = false;
            $parenthesisLevel = 0;
            
            while ( $tok && !$exit) {
                if(is_array($tok)) {
                    $value.= $tok[1];
                    if($tok[0] == T_ARRAY)
                        $insideArray = true;
                }
                else {
                    $value .= $tok;
                    if ($insideArray) {
                        if($tok == '(') {
                            $parenthesisLevel++;
                        }
                        elseif($tok == ')') {
                            $parenthesisLevel--;
                            if($parenthesisLevel == 0)
                                $insideArray = false;
                        }
                    }
                }
                $tok = $this->toNextPhpToken();
                
                if (!$insideArray) {
                    $exit =  $this->isEndToken($tok, $endToken);
                }
            }
            if(!$tok) {
                throw  new Exception('value of variable invalid ('.$name.','.$value.')');
            }
            return array($name, $value);
        }
        else if($this->isEndToken($tok, $endToken)) {
            return array($name, null);
        }
        else
            throw  new Exception('bad end of variable declaration');
    }
    
    protected function isEndToken($tok, $endToken){
        if(is_array($endToken)) {
            return in_array($tok, $endToken);
        }
        else {
            return $tok == $endToken;
        }
    }
    
}

