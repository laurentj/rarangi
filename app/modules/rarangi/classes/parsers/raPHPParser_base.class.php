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
                throw new Exception ("invalid syntax. token expected : string \"$tokentype\", got ".token_name($tok[0]));
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

            if (is_string($tokentype) && is_string($tok) && $tok == $tokentype) {
                return $tok;
            }
            else if (!is_string($tokentype) && is_array($tok)) {
                if ($tok[0] == $tokentype)
                    return $tok[1];
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
                $value = $this->readUntilPhpToken($endToken);
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
    protected function readUntilPhpToken($endToken = ';') {

        $tok = $this->toNextPhpToken();
        $exit = $this->isEndToken($tok, $endToken);
        $parenthesisLevel = 0;
        $value = '';

        while ($tok && !$exit) {
            if (is_array($tok)) {
                $value .= $tok[1];
            }
            else {
                $value .= $tok;
                if ($tok == '(') {
                    $parenthesisLevel++;
                }
                elseif ($tok == ')') {
                    $parenthesisLevel--;
                }
            }
            $tok = $this->toNextPhpToken();
            
            if (!$parenthesisLevel) {
                $exit =  $this->isEndToken($tok, $endToken);
            }
        }
        if (!$tok) {
            throw  new Exception('didn\'t find the token');
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
            $value = $this->readUntilPhpToken($endToken);
        }
        catch (Exception $e) {
            throw  new Exception('invalid value for const  ('.$name.','.$value.')');
        }

        return array($name, $value);
    }
}
