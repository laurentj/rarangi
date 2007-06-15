<?php
/**
* @package     jDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2007 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/


/**
 * This object is responsible to manage all messages sended during the process
 * 
 * It calls "driver" which implement jILoggerDriver interface. theses drivers
 * can are responsible to store, display or send the messages.
 *
 * This class contains only static methods
 * @package     jDoc
 */
class jLogger {

    /**
     * list of active driver
     * @var array  array of jILoggerDriver objects
     */
    protected static $loggers = array();

    private function __construct(){}

    /**
     * register a new logger driver
     * @param jILoggerDriver $logger
     */
    static public function addLogger($logger){
        self::$loggers[] = $logger;
    }

    /**
     * remove all registered logger driver
     */
    static public function removeLoggers(){
        self::$loggers = array();
    }

    /**
     * generate a simple message
     * @param string $str the message
     */
    static public function message($str){ self::call($str,'message');}

    /**
     * generate a notice
     * @param string $str the message
     */
    static public function notice($str){ self::call($str,'notice');}

    /**
     * generate a warning
     * @param string $str the message
     */
    static public function warning($str){ self::call($str,'warning');}

    /**
     * generate an error
     * @param string $str the message
     */
    static public function error($str){ self::call($str,'error');}

    /**
     * call all registered drivers with the current message
     * @param string $str the message
     * @param string $meth the type of message : it should be the name of a method 
     *                     of jILoggerDriver interface
     */
    protected static function call($str, $meth){
        $f = jDoc::currentFile();
        $l = jDoc::currentLine();
        foreach(self::$loggers as $log){
            $log->$meth($str, $f, $l);
        }
    }

    /**
     * for debug only
     * @todo remove it at the end of developpement
     */
    static public function dumpTok($tok){
       /* if(is_array($tok)){
            self::message('-----token '.token_name($tok[0]).' value="'.$tok[1].'"');
        }else{
            self::message('-----token value="'.$tok.'"');
        }*/

    }

}

/**
 * interface for logger driver
 */
interface jILoggerDriver {
    /**
     * process a simple message
     * @param string $str the message
     * @param string $f   the filename where the message appears during the parsing
     * @param integer $l  the line number where the message appears during the parsing
     */
    public function message($str, $f, $l);

    /**
     * process a notice
     * @param string $str the message
     * @param string $f   the filename where the message appears during the parsing
     * @param integer $l  the line number where the message appears during the parsing
     */
    public function notice($str, $f, $l);

    /**
     * process a warning
     * @param string $str the message
     * @param string $f   the filename where the message appears during the parsing
     * @param integer $l  the line number where the message appears during the parsing
     */
    public function warning($str, $f, $l);

    /**
     * process an error
     * @param string $str the message
     * @param string $f   the filename where the message appears during the parsing
     * @param integer $l  the line number where the message appears during the parsing
     */
    public function error($str, $f, $l);

    /**
     * clear all messages
     */
    //public function clear();
}


/**
 * a logger which displays messages in the standard output
 */
class jConsoleLogger implements jILoggerDriver {

    public function message($str, $f, $l){ echo $str,"\n";}
    public function notice($str, $f, $l){ echo 'Notice: ',$str,"\n\t($f line $l)\n";}
    public function warning($str, $f, $l){ echo 'Warning: ',$str,"\n\t($f line $l)\n";}
    public function error($str, $f, $l){ echo 'Error: ',$str,"\n\t($f line $l)\n";}
    public function clear(){ }

}

/**
 * a logger which stores messages in memory
 */
class jInMemoryLogger implements jILoggerDriver {
    protected $log = array();

    public function message($str, $f, $l){ $this->log[] = array(0, $str, $f, $l);}
    public function notice($str, $f, $l){ $this->log[] = array(1, $str, $f, $l);}
    public function warning($str, $f, $l){ $this->log[] = array(2, $str, $f, $l);}
    public function error($str, $f, $l){ $this->log[] = array(3, $str, $f, $l);}
    public function clear(){ $this->log = array(); }
    
    /**
     * return the list of messages
     * @return array
     */
    public function getLog(){ return $this->log; }
}


?>