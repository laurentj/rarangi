<?php
/**
* @package   jphpdoc
* @subpackage 
* @author    yourname
* @copyright 2008 yourname
* @link      http://www.yourwebsite.undefined
* @licence    All right reserved
*/


require_once (JELIX_LIB_CORE_PATH.'response/jResponseHtml.class.php');

class myHtmlResponse extends jResponseHtml {

    public $bodyTpl = 'jphpdoc~main';

    function __construct() {
        parent::__construct();
        $this->addCSSLink($GLOBALS['gJConfig']->urlengine['basePath']."design/styles.css");

    }

    protected function doAfterActions() {
        // Include all process in common for all actions, like the settings of the
        // main template, the settings of the response etc..
        $this->body->assignIfNone('SIDEBAR','');
        $this->body->assignIfNone('SUBMENUBAR','');
        $this->body->assignIfNone('MAIN','<p>no content</p>');
        
        if($this->title != '')
            $this->title .=' - jPhpDoc';
        else
            $this->title = 'jPhpDoc';
        
    }
}