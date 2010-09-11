<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/


/**
 * This object is responsible to manage all messages sended during the process
 * 
 * It calls "driver" which implement raILoggerDriver interface. theses drivers
 * can are responsible to store, display or send the messages.
 *
 * This class contains only static methods
 * @package     rarangi
 */
class raLogger {

    /**
     * list of active driver
     * @var array  array of raILoggerDriver objects
     */
    protected $loggers = array();
    
    /**
     * @var raParserInfo
     */
    protected $parserInfo;

    function __construct() {

    }

    /**
     * @param raParserInfo $parserInfo
     */
    function setCurrentParserInfo($parserInfo) {
        $this->parserInfo = $parserInfo;
    }

    /**
     * register a new logger driver
     * @param jILoggerDriver $logger
     */
    public function addLogger($logger) {
        $this->loggers[] = $logger;
    }

    /**
     * remove all registered logger driver
     */
    public function removeLoggers() {
        $this->loggers = array();
    }

    /**
     * get a logger driver
     * @param int $number
     * @return jILoggerDriver $logger
     */
    public function getLogger($key) {
        return $this->loggers[$key];
    }

    /**
     * generate a simple message
     * @param string $str the message
     */
    public function message($str) {
        $this->call($str,'message');
    }

    /**
     * generate a notice
     * @param string $str the message
     */
    public function notice($str) {
        $this->call($str,'notice');
    }

    /**
     * generate a warning
     * @param string $str the message
     */
    public function warning($str) {
        $this->call($str,'warning');
    }

    /**
     * generate an error
     * @param string $str the message
     */
    public function error($str) {
        $this->call($str,'error');
    }

    /**
     * call all registered drivers with the current message
     * @param string $str the message
     * @param string $meth the type of message : it should be the name of a method 
     *                     of raILoggerDriver interface
     */
    protected function call ($str, $meth) {

        if ($this->parserInfo) {
            $f = $this->parserInfo->currentFile();
            $l = $this->parserInfo->currentLine();
        }
        else {
            $f = '';
            $l = 0;
        }

        foreach ($this->loggers as $log) {
            $log->$meth($str, $f, $l);
        }
    }

    /**
     * for debug only
     * @todo remove it at the end of developpement
     */
    public function dumpTok($tok){
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
interface raILoggerDriver {
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
class raConsoleLogger implements raILoggerDriver {

    protected $resp;
    protected $verbose;
    
    /**
     * @param jResponseCmdline $resp
     */
    function __construct($resp, $verbose = true) {
        $this->resp = $resp;
        $this->verbose = $verbose;
    }

    public function message($str, $f, $l){ if($this->verbose) $this->resp->addContent($str."\n");}
    public function notice($str, $f, $l){ if($this->verbose) $this->resp->addContent( 'Notice: '.$str."\n\t($f line $l)\n");}
    public function warning($str, $f, $l){ $this->resp->addContent( 'Warning: '.$str."\n\t($f line $l)\n");}
    public function error($str, $f, $l){ $this->resp->addContent( 'Error: '.$str."\n\t($f line $l)\n");}
    public function clear(){ }

}

/**
 * a logger which stores messages in memory
 */
class raInMemoryLogger implements raILoggerDriver {
    protected $log = array('message'=>array(),
                           'notice'=>array(),
                           'warning'=>array(),
                           'error'=>array()
                           );

    public function message($str, $f, $l){ $this->log['message'][] = array(0, $str, $f, $l);}
    public function notice($str, $f, $l){ $this->log['notice'][] = array(1, $str, $f, $l);}
    public function warning($str, $f, $l){ $this->log['warning'][] = array(2, $str, $f, $l);}
    public function error($str, $f, $l){ $this->log['error'][] = array(3, $str, $f, $l);}
    public function clear(){ $this->log = array('message'=>array(),
                           'notice'=>array(),
                           'warning'=>array(),
                           'error'=>array()
                           ); }
    
    /**
     * return the list of messages
     * @return array
     */
    public function getLog(){ return $this->log; }
}

/**
 * a logger which stores messages in a SQL table
 */
class raInDaoLogger implements raILoggerDriver {

    protected $projectId;

    function __construct($project_id) {
        $this->projectId = $project_id;
    }

    public function message($str, $f, $l){}
    public function notice($str, $f, $l){ $this->save('notice', $str, $f, $l);}
    public function warning($str, $f, $l){ $this->save('warning', $str, $f, $l);}
    public function error($str, $f, $l){ $this->save('error', $str, $f, $l);}
    protected function save($typeerr, $msg, $file, $line) {
        $rec = jDao::createRecord('rarangi~errors');
        $rec->message = $msg;
        $rec->type = $typeerr;
        $rec->file = $file;
        $rec->line = $line;
        $rec->project_id = $this->projectId;
        jDao::get('rarangi~errors')->insert($rec);
    }
    
    public function clear(){ jDao::get('rarangi~errors')->deleteByProject($this->projectId); }

    public function getLog(){ return array(); }
}