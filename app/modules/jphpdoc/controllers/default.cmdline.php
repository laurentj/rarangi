<?php
/**
* @package   jphpdoc
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/jphpdoc/
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
    /**
    *
    */
    function index() {
        $rep = $this->getResponse();
        
        jClasses::inc("jDoc");
        jClasses::inc("jLogger");

        jLogger::addLogger(new jInMemoryLogger());
        jLogger::addLogger(new jConsoleLogger($rep, isset($this->_options['-v'])));

        $docparser = jDoc::getInstance();
        $docparser->setConfig($this->param('config'));

        $docparser->run();

        return $rep;
    }
}
