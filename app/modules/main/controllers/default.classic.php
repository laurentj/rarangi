<?php
/**
* @package   app
* @subpackage main
* @author    yourname
* @copyright 2010 yourname
* @link      http://www.yourwebsite.undefined
* @license    All right reserved
*/

class defaultCtrl extends jController {
    /**
    *
    */
    function index() {
        $rep = $this->getResponse('html');
        $rep->body->assign('MAIN', 'toto');
        return $rep;
    }


    /**
    * Display the help page
    */
    function help() {
        $resp = $this->getResponse('html');
        $resp->title = jLocale::get('default.page.help.title');

        $resp->body->assignZone('BREADCRUMB', 'rarangi_web~location_breadcrumb', array(
                    'mode' => 'help'));
        $tpl = new jTpl();
        $resp->body->assign('MAIN', $tpl->fetch('help'));
        
        return $resp;
    }

    /**
    * Display the about rarangi page
    */
    function about() {
        $resp = $this->getResponse('html');
        $resp->title = jLocale::get('default.page.about.title');

        $resp->body->assignZone('BREADCRUMB', 'rarangi_web~location_breadcrumb', array(
                    'mode' => 'about'));
        $tpl = new jTpl();
        $resp->body->assign('MAIN', $tpl->fetch('about'));
        
        return $resp;
    }


}

