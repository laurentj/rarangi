<?php
/**
* @package   jphpdoc
* @subpackage jphpdoc
* @author    yourname
* @copyright 2008 yourname
* @link      http://www.yourwebsite.undefined
* @licence    All right reserved
*/

class defaultCtrl extends jControllerCmdLine {

    /**
    * Options to the command line
    *  'method_name' => array('-option_name' => true/false)
    * true means that a value should be provided for the option on the command line
    */
    protected $allowed_options = array(
            'index' => array('-v'=>false));

    /**
     * Parameters for the command line
     * 'method_name' => array('parameter_name' => true/false)
     * false means that the parameter is optionnal. All parameters which follow an optional parameter
     * is optional
     */
    protected $allowed_parameters = array(
            'index' => array('config'=>true));
    /**
    *
    */
    function index() {
        $rep = $this->getResponse();
        
        jClasses::inc("jDoc");
        jClasses::inc("jLogger");

        jLogger::addLogger(new jInMemoryLogger());
        if(isset($switches['-v'])){
            jLogger::addLogger(new jConsoleLogger($rep));
        }

        $config = jClasses::create("jDocConfig");
        $config->readConfig($this->param('config'));

        $docparser = jDoc::getInstance();
        $docparser->setConfig($gConfig);

        $docparser->run();

        return $rep;
    }
}
