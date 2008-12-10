<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi/
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
        
        jClasses::inc("raDocGenerator");
        jClasses::inc("raLogger");

        raLogger::addLogger(new raInMemoryLogger());
        raLogger::addLogger(new raConsoleLogger($rep, isset($this->_options['-v'])));

        $docparser = raDocGenerator::getInstance();
        $docparser->setConfig($this->param('config'));

        $docparser->run();

        return $rep;
    }
}
