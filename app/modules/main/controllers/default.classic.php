<?php
/**
* @package   rarangi
* @subpackage main.module
* @author    Laurent Jouanneau
* @copyright 2010 Laurent Jouanneau
* @link      http://rarangi.org
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
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
        $tpl = new jTpl();
        $resp->body->assign('MAIN', $tpl->fetch('about'));
        return $resp;
    }


}

