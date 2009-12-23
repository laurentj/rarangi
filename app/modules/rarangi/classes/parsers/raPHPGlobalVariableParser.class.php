<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2009 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

/**
 * Object which parses a global variable declaration
 */
class raPHPGlobalVariableParser extends raPHPParser_base {

    /**
     * @param raParser_base $fatherParser  the parser which instancy this class
     * @param string $doccomment the documented comment associated to the define
     */
    function __construct($fatherParser, $doccomment){
        parent::__construct($fatherParser);
        $this->descriptor = new raGlobalVariableDescriptor($this->parserInfo->project(),
                                           $fatherParser->getDescriptor()->fileId,
                                           $this->parserInfo->currentLine());
        $this->descriptor->inheritsFrom($fatherParser->getDescriptor());
        $this->descriptor->initFromPhpDoc($doccomment);
        $this->descriptor->typeProperty = raGlobalVariableDescriptor ::TYPE_VAR;
    }

    public function parse() {

        $tok =  $this->iterator->current();
        if ($tok[1] == '$GLOBALS') {

            // parse a $GLOBAL declaration
            $tok = $this->toNextPhpToken();
            if (!is_string($tok)) {
                $this->parserInfo->project()->logger()->error("Bad syntax to read this global variable");
                $this->jumpToSpecificPhpToken(';');
                return;
            }

            if ($tok == '[') {
                $tok = $this->toNextPhpToken();
                if (!is_array($tok) || $tok[0] != T_CONSTANT_ENCAPSED_STRING) {
                    // we don't know what the $GLOBALS is about (too difficult to parse it)
                    // so let's save informations about the content, if the doc
                    // comment is well enough documented
                    if (!$this->descriptor->saveForAll()) {
                        $this->parserInfo->project()->logger()->warning("This global variable is ignored. not well enough documented.");
                    }

                    $this->jumpToSpecificPhpToken(';');
                    return;
                }

                $this->descriptor->name = substr($tok[1],1,-1);
                $tok = $this->toNextSpecificPhpToken(']');
                $tok = $this->toNextSpecificPhpToken('=');
                $this->descriptor->defaultValue = $this->readUntilPhpToken(';');
                $this->descriptor->save();
            }
            else if ($tok == '=') {
                // we don't know what the value is (too difficult to parse it)
                // so let's save informations about the content, if the doc
                // comment is well enough documented
                if (!$this->descriptor->saveForAll()) {
                    $this->parserInfo->project()->logger()->warning("This global variable is ignored. not well enough documented.");
                }
                $this->jumpToSpecificPhpToken(';');
            }
        }
        else {
            list($pname, $pvalue) = $this->readVarnameAndValue(';');
            $this->descriptor->name = $pname;
            $this->descriptor->defaultValue = $pvalue;
            $this->descriptor->save();
        }
    }
}
?>