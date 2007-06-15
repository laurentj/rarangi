<?php
/**
* @package     jDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2007 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/



class jLogger {

    protected static $loggers = array();

    private function __construct(){}

    static public function addLogger($logger){
        self::$loggers[] = $logger;
    }
    static public function resetLoggers(){
        self::$loggers = array();
    }

    protected static function call($str, $meth){
        $f = jDoc::currentFile();
        $l = jDoc::currentLine();
        foreach(self::$loggers as $log){
            $log->$meth($str, $f, $l);
        }
    }

    static public function message($str){ self::call($str,'message');}
    static public function notice($str){ self::call($str,'notice');}
    static public function warning($str){ self::call($str,'warning');}
    static public function error($str){ self::call($str,'error');}

    static public function dumpTok($tok){
       /* if(is_array($tok)){
            self::message('-----token '.token_name($tok[0]).' value="'.$tok[1].'"');
        }else{
            self::message('-----token value="'.$tok.'"');
        }*/

    }

}


interface jILogger {
    public function message($str, $f, $l);
    public function notice($str, $f, $l);
    public function warning($str, $f, $l);
    public function error($str, $f, $l);
    public function clear();
}



class jConsoleLogger implements jILogger {

    public function clear(){ }
    public function message($str, $f, $l){ echo $str,"\n";}
    public function notice($str, $f, $l){ echo 'Notice: ',$str,"\n\t($f line $l)\n";}
    public function warning($str, $f, $l){ echo 'Warning: ',$str,"\n\t($f line $l)\n";}
    public function error($str, $f, $l){ echo 'Error: ',$str,"\n\t($f line $l)\n";}

}

class jInMemoryLogger implements jILogger {
    protected $log = array();

    public function getLog(){ return $this->log; }
    public function clear(){ $this->log = array(); }
    public function message($str, $f, $l){ $this->log[] = array(0, $str, $f, $l);}
    public function notice($str, $f, $l){ $this->log[] = array(1, $str, $f, $l);}
    public function warning($str, $f, $l){ $this->log[] = array(2, $str, $f, $l);}
    public function error($str, $f, $l){ $this->log[] = array(3, $str, $f, $l);}

}


?>