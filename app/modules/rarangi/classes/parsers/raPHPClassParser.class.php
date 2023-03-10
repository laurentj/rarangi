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
 * Object which parses a class content
 */
class raPHPClassParser extends raPHPInterfaceParser {

    /**
     * @param raParser_base $fatherParser  the parser which instancy this class
     * @param string $doccomment the documented comment associated to the class
     * @param boolean $isAbstract  indicates if the class is an abstract class
     */
    function __construct($fatherParser, $doccomment, $isAbstract = false){
        raPHPParser_base::__construct($fatherParser);
        $this->descriptor = new raClassDescriptor($this->parserInfo->project(),
                                           $fatherParser->getDescriptor()->fileId,
                                           $this->parserInfo->currentLine());
        $this->descriptor->inheritsFrom($fatherParser->getDescriptor());
        $this->descriptor->initFromPhpDoc($doccomment);
        $this->descriptor->isAbstract = $isAbstract;
    }

   
    protected function parseDeclaration(){
         // read the content between the name and the next '{'
        $tok = $this->toNextPhpToken();
        while (is_array($tok)) {
            if ($tok[0] != T_IMPLEMENTS && $tok[0] != T_EXTENDS) {
                if($tok[0] == T_COMMENT) {
                    $tok = $this->toNextPhpToken();
                    continue;
                }
                throw new Exception ("Class parsing, invalid syntax, bad token : ".token_name($tok[0]));
            }
            $type = $tok[0];
            if($type == T_IMPLEMENTS) {
                $this->descriptor->interfaces[] = $this->toNextSpecificPhpToken(T_STRING);
                $tok = $this->toNextPhpToken();
                while(is_string($tok) && $tok ==',') {
                    $this->descriptor->interfaces[] = $this->toNextSpecificPhpToken(T_STRING);
                    $tok = $this->toNextPhpToken();
                }
            }
            else {
                $this->descriptor->mother = $this->toNextSpecificPhpToken(T_STRING);
                $tok = $this->toNextPhpToken();
            }
        }

        if(!is_string($tok) || $tok != '{' )
            throw new Exception ("Class parsing, invalid syntax, '{' expected");
    }
    
}
