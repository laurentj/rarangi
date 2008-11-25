<?php
/**
* @package   jphpdoc
* @subpackage jphpdoc
* @author    yourname
* @copyright 2008 yourname
* @link      http://www.yourwebsite.undefined
* @licence    All right reserved
*/

class defaultCtrl extends jController {
    /**
    *
    */
    function index() {
        $rep = $this->getResponse('html');

        // this is a call for the 'welcome' zone after creating a new application
        // remove this line !
        $rep->body->assignZone('MAIN', 'jelix~check_install');

        return $rep;
    }
}
