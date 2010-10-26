<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008 Laurent Jouanneau
* @link      http://rarangi.org
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
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
    
    
    public $help = array(
        'index'=>'php rarangi.php file.ini

This script parses PHP files according to setting indicated in the given ini file.
        '
    );
    /**
    *
    */
    function index() {
        $rep = $this->getResponse();
        $memBegin = memory_get_usage();

        $logger = jClasses::create("raLogger");
        //$logger->addLogger(new raInMemoryLogger());
        $logger->addLogger(new raConsoleLogger($rep, isset($this->_options['-v'])));

        jClasses::inc("raDocGenerator");
        $docparser = new raDocGenerator($this->param('config'), $logger);
        $docparser->run();

        $rep->addContent('Memory usage at the start: '.$memBegin." bytes\n");
        $rep->addContent('Memory usage at the end: '.memory_get_usage()." bytes\n");
        $rep->addContent('Memory peak usage: '.memory_get_peak_usage()." bytes\n");

        return $rep;
    }
}
