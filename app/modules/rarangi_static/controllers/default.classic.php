<?php
/**
* @package   rarangi
* @subpackage rarangi_static
* @author    Laurent Jouanneau
* @copyright 2011 totr
* @link      totr.lu
* @license   http://www.gnu.org/licenses/gpl.html GPL
*/

class defaultCtrl extends jController {
    /**
    *
    */
    function index() {
        $rep = $this->getResponse('html');

        return $rep;
    }
}

