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
 * Object which parses an include/require declaration
 */
class raPHPIncludeParser extends raPHPParser_base {

    /**
     * @var jIncludeDescriptor
     */
    protected $info;

    /**
     * @param Iterator $it  the iterator on tokens
     * @param string $doccomment the documented comment associated to the include declaration
     */
    function __construct( $it, $doccomment){
        //$this->descriptor = new jIncludeDescriptor();
        //$this->descriptor->initFromPhpDoc($doccomment);
        parent::__construct( $it);
    }

    public function parse(){

    }

}
?>