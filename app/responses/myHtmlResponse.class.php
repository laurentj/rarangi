<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/


require_once (JELIX_LIB_CORE_PATH.'response/jResponseHtml.class.php');

class myHtmlResponse extends jResponseHtml {

    public $bodyTpl = 'main~main';

    function __construct() {
        parent::__construct();
        $this->addCSSLink(jApp::config()->urlengine['basePath'].'themes/'.jApp::config()->theme.'/main.css');
        $this->addCSSLink(jApp::config()->urlengine['basePath'].'themes/'.jApp::config()->theme.'/rarangi_web.css');
    }

    protected function doAfterActions() {
        // Include all process in common for all actions, like the settings of the
        // main template, the settings of the response etc..
        $this->body->assignIfNone('BREADCRUMB','');
        $this->body->assignIfNone('MENUBAR','');
        $this->body->assignIfNone('MAIN','<p>no content</p>');
        
        if($this->title != '')
            $this->title .=' - Rarangi';
        else
            $this->title = 'Rarangi';
        
    }
}
