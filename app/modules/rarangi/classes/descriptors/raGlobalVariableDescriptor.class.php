<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

/**
 *
 */
class raGlobalVariableDescriptor  extends raBaseDescriptor {
    public $datatype;

    public function save() {
        if ($this->ignore)
            return;

        parent::save();
    }
}

