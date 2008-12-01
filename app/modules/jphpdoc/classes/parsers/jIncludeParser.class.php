<?php
/**
* @package     jPhpDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/jphpdoc
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

/**
 * Object which parses an include/require declaration
 */
class jIncludeParser extends jParser_base {

    /**
     * @var jIncludeDescriptor
     */
    protected $info;

    /**
     * @param Iterator $it  the iterator on tokens
     * @param string $doccomment the documented comment associated to the include declaration
     */
    function __construct( $it, $doccomment){
        //$this->info = new jIncludeDescriptor();
        //$this->info->initFromPhpDoc($doccomment);
        parent::__construct( $it);
    }

    public function parse(){

    }

}
?>