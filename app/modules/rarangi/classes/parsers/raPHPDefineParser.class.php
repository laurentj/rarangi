<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

/**
 * Object which parses a define declaration
 */
class raPHPDefineParser extends raPHPParser_base {

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
        $this->descriptor->typeProperty = raGlobalVariableDescriptor ::TYPE_CONST;
    }

    public function parse() {

        $this->toNextSpecificPhpToken('(');
        $tok = $this->toNextPhpToken();
        if (!is_array($tok) || $tok[0] != T_CONSTANT_ENCAPSED_STRING) {
            // we don't know what this define try to define (too difficult to parse it)
            // so let's save informations about the content, let's hope the doc
            // comment is well enough documented
            if (!$this->descriptor->saveForAll()) {
                $this->parserInfo->project()->logger()->warning("This define is ignored. not well enough documented.");
            }
            $this->jumpToSpecificPhpToken(';');
            return;
        }

        $this->descriptor->name = substr($tok[1],1,-1);
        $this->toNextSpecificPhpToken(',');

        $value = trim(rtrim($this->readUntilPhpToken(';', 1), ')'));

        $this->descriptor->defaultValue = $value;
        
        //$this->descriptor->lineEnd = $this->parserInfo->currentLine();
        $this->descriptor->save();
    }

}
?>