<?php
/**
* @package     jDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

/**
 * Object which parses a class content
 */
class jClassParser extends jParser_base {

    /**
     * @var jClassDescriptor
     */
    protected $info;

    /**
     * @param Iterator $it  the iterator on tokens
     * @param string $doccomment the documented comment associated to the class
     */
    function __construct( $it, $doccomment){
        //$this->info = new jClassDescriptor();
        //$this->info->initFromPhpDoc($doccomment);
        parent::__construct( $it);
    }

    public function parse(){

    }

}
?>