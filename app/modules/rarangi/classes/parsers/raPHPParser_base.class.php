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
     * @var raBaseDescriptor properties to store into the database
     */
    protected $descriptor;

    /**
     * @var ArrayIterator iterator on all php tokens
     */
    protected $iterator;

    /**
     * @var raParserInfo
     */
    public $parserInfo;

    /**
     * @param raPHPParser_base $fatherParser  the parser which instantiate this parser
     */
    function __construct ($fatherParser) {
        $this->iterator = $fatherParser->iterator;
        $this->parserInfo = $fatherParser->parserInfo;
    }

    /**
     * parse the part of the file on which the parser is responsible.
     * 'sub-parsers' should be called into this method, and data
     * should be saved.
     */
    abstract public function parse();

    /**
     * @return raBaseDescriptor
     */
    public function getDescriptor() { return $this->descriptor; }

    /**
     * @return  raParserInfo
     */
    public function getParserInfo() { return $this->parserInfo; }

    /**
     * @return ArrayIterator
     */
    public function getIterator() { return $this->iterator; }

    /**
     * set the cursor after the next "<?php" token
     */
    protected function toNextPhpSection(){
        while ($this->iterator->valid()) {
            $tok = $this->iterator->current();
            if (is_array($tok)) {
                if($tok[0] == T_OPEN_TAG){
                    $this->parserInfo->incLineS($tok[1]); //the token include next \n some time
                    return;
                }
                if (in_array($tok[0], array(T_COMMENT, T_DOC_COMMENT,T_ENCAPSED_AND_WHITESPACE, 
                            T_INLINE_HTML, T_STRING, T_WHITESPACE)))
                    $this->parserInfo->incLineS($tok[1]);
            }
            else {
                $this->parserInfo->incLineS($tok);
            }
            $this->iterator->next();
        }
    }

    /**
     * set the cursor on the next non whitespace token
     * @return boolean|array|string  return the next token found, or false if there isn't anymore token
     */
    protected function toNextPhpToken() {
        if (!$this->iterator->valid())
            return false;
        $this->iterator->next();

        while ($this->iterator->valid()) {
            $tok = $this->iterator->current();
            if (is_array($tok)) {
                if ($tok[0] == T_WHITESPACE ||
                    $tok[0] == T_ENCAPSED_AND_WHITESPACE ||
                    $tok[0] == T_INLINE_HTML) {
                    $this->parserInfo->incLineS($tok[1]);
                }
                elseif ($tok[0] == T_CLOSE_TAG) {
                    $this->iterator->next();
                    $this->toNextPhpSection();
                    if (!$this->iterator->valid())
                        return false;
                }
                else {
                    if ($tok[0] == T_COMMENT || $tok[0] == T_DOC_COMMENT) {
                        $this->parserInfo->incLineS($tok[1]);
                    }
                    return $tok;
                }
            }
            elseif (!preg_match('/^\s*$/',$tok)) {
                $this->parserInfo->incLineS($tok);
                return $tok;
            }
            else {
                $this->parserInfo->incLineS($tok);
            }
            $this->iterator->next();
        }
        return false;
    }
    
    /**
     * set the cursor on the next php token. It should correspond to the
     * given token, else an exception is thrown.
     * @return boolean|string the value of the token, or false if there isn't anymore token
     */

    protected function toNextSpecificPhpToken($tokentype) {
        $tok = $this->toNextPhpToken();
        if (is_string($tokentype)) {
            if (is_string($tok)) {
                if ($tok == $tokentype)
                    return $tok;
                else
                    throw new Exception ("invalid syntax. token expected : string \"$tokentype\", got \"".$tok."\"");
            }
            else
                throw new Exception ("invalid syntax. token expected : string \"$tokentype\", got ".token_name($tok[0])); //.":".$tok[1]
        }
        else {
            if (!is_array($tok))
                throw new Exception ("invalid syntax. token expected : ".token_name($tokentype).", got a string :\"$tok\"" );
            if ($tok[0] != $tokentype)
                throw new Exception ("invalid syntax token expected : ".token_name($tokentype).", got another token : ".token_name($tok[0]));
            return $tok[1];
        }
    }

    /**
     * advance the cursor until the given token is found
     * @return boolean|string the value of the token, or false if there isn't anymore token
     */
    protected function jumpToSpecificPhpToken($tokentype) {
        if (!$this->iterator->valid())
            return false;
        $this->iterator->next();

        while ($this->iterator->valid()) {
            $tok = $this->iterator->current();

            if (is_array($tok)) { 
                if ($tok[0] == T_WHITESPACE ||
                    $tok[0] == T_ENCAPSED_AND_WHITESPACE ||
                    $tok[0] == T_INLINE_HTML ||
                    $tok[0] == T_COMMENT ||
                    $tok[0] == T_DOC_COMMENT
                    ) {
                    $this->parserInfo->incLineS($tok[1]);
                }
                if (!is_string($tokentype) && $tok[0] == $tokentype)
                    return $tok[1];
            }
            else {
                $this->parserInfo->incLineS($tok);
                    
                if (is_string($tokentype) && $tok == $tokentype) {
                    return $tok;
                }
            }

            $this->iterator->next();
        }
        return false;
    }

    /**
     * read a variable name and an optional value
     * @param string|array $endToken the possible token on which it should stop the reading
     * @return array the array contains the name and the value.
     */
    protected function readVarnameAndValue($endToken = ';') {

        $tok = $this->iterator->current();

        if (is_array($tok) && $tok[0] != T_VARIABLE)
            throw new Exception('not a variable declaration');

        $name = substr($tok[1], 1);
        $tok = $this->toNextPhpToken();

        if (!$tok || !is_string($tok))
            throw new Exception('variable declaration invalid');

        if ($tok == '=') {
            try {
                $value = trim($this->readUntilPhpToken($endToken));
            }
            catch (Exception $e) {
                throw  new Exception('value of variable invalid ('.$name.','.$value.')');
            }
            return array($name, $value);
        }
        else if ($this->isEndToken($tok, $endToken)) {
            return array($name, null);
        }
        else
            throw new Exception('bad end of variable declaration');
    }

    /**
     * read tokens until a specific token
     * @param string|array $endToken the possible token on which it should stop the reading
     * @return string the string containing all readed tokens.
     */
    protected function readUntilPhpToken($endToken = ';', $parenthesisLevel = 0) {

        $value = '';
        $doExit = false;
        $this->iterator->next();
        while (!$doExit &&  $this->iterator->valid()) {
            $tok = $this->iterator->current();
            if (!$parenthesisLevel && $this->isEndToken($tok, $endToken))
                return $value;
            if (is_array($tok)) {
                if ($tok[0] == T_WHITESPACE ||
                    $tok[0] == T_ENCAPSED_AND_WHITESPACE ||
                    $tok[0] == T_INLINE_HTML) {
                    $this->parserInfo->incLineS($tok[1]);
                    $value.=' ';
                }
                elseif ($tok[0] == T_CLOSE_TAG) {
                    $this->iterator->next();
                    $this->toNextPhpSection();
                }
                else if ($tok[0] == T_COMMENT || $tok[0] == T_DOC_COMMENT) {
                    $this->parserInfo->incLineS($tok[1]);
                }
                else
                    $value .= $tok[1];
                $this->iterator->next();
                continue;
            }
            elseif (preg_match('/^\s*$/',$tok)) {
                $this->parserInfo->incLineS($tok);
                $this->iterator->next();
                continue;
            }

            switch($tok){
            case '(':
                $parenthesisLevel++;
                break;
            case ')':
                $parenthesisLevel--;
                break;
            default:
                $this->parserInfo->incLineS($tok);
            }
            $value .= $tok;
            $this->iterator->next();
        }

        return $value;
    }

    protected function isEndToken($tok, $endToken){
        if (is_array($endToken)) {
            return in_array($tok, $endToken);
        }
        else {
            return $tok == $endToken;
        }
    }

    protected function readConstAndValue($endToken = ';') {

        $tok = $this->iterator->current();
        if (is_array($tok) && $tok[0] != T_STRING)
            throw new Exception('not a const declaration');
    
        $name = $tok[1];
        $tok = $this->toNextPhpToken();

        if (!$tok || !is_string($tok))
            throw new Exception('invalid const declaration');

        if ($tok != '=')
            throw new Exception('invalid const declaration: undefined value');

        try {
            $value = trim($this->readUntilPhpToken($endToken));
        }
        catch (Exception $e) {
            throw  new Exception('invalid value for const  ('.$name.','.$value.')');
        }

        return array($name, $value);
    }

    protected function skipParenthesis() {
        $this->toNextSpecificPhpToken('(');
        $tok = $this->toNextPhpToken();
        $parenthesislevel = 1;
        $doExit = false;
        // jump to the end of the parenthesis block
        while(!$doExit &&  ($tok = $this->toNextPhpToken()) !== false ) {
            if (is_array($tok)) {
                /*switch($tok[0]){
                case T_:
                    break;
                }*/
            } else {
                switch($tok){
                case '(':
                    $parenthesislevel++;
                    break;
                case ')':
                    $parenthesislevel--;
                    $doExit = ($parenthesislevel == 0);
                    break;
                default:
                }
            }
        }
    }

    protected function skipBlock($onlyBrackets = false) {
        if (!$onlyBrackets) {
            $tok = $this->toNextPhpToken();
            if ($tok != '{') {
                if (is_array($tok)) {
                    if ($tok[0] == T_FOR) {
                        $this->skipParenthesis();
                        $this->skipBlock();
                    }
                    else
                        $this->jumpToSpecificPhpToken(';');
                }
                else {
                    $this->jumpToSpecificPhpToken(';');
                }
                return;
            }
        }

        $bracketlevel = 1;
        $doExit = false;
        // jump to the end of the function block
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
    }
}
