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
 * @notimplemented
 */
class jDescriptorTagBase {
    const FILE_LEVEL = 1;
    const CLASS_LEVEL = 2;
    const INTERFACE_LEVEL = 3;
    const FUNCTION_LEVEL = 4;
    const METHOD_LEVEL = 5;
    const PROPERTY_LEVEL = 6;
    const GLOBAL_VAR_LEVEL = 7;
    const GLOBAL_CONST_LEVEL = 8;
    const CLASS_CONST_LEVEL = 9;



    public $allowedInLevel = array();

    

    public function parseValue() {
    }

    




}

