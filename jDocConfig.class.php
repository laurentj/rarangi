<?php /**
* @package     jDoc
* @author      Jouanneau Laurent
* @contributor
* @copyright   2006 Jouanneau laurent
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/


class jDocConfig {
    protected $excludedFiles = array();
    protected $excludedFilesReg = array();



    public function setExcludedFiles($files){
        foreach($files as $f){
            if($f{0} == '*'){
                $s = preg_quote(substr($f,1),'/');
                $s = '/.*'.$s.'$/';
                $this->excludedFilesReg[] = $s;
            }elseif(substr($f,-1,1) == '*'){
                $s = preg_quote(substr($f,0,-1),'/');
                $s = '/^'.$s.'.*/';
                $this->excludedFilesReg[] = $s;
            }else{
                $this->excludedFiles[] = $f;
            }
        }
    }

    public function isExcludedFile($name){
        if(in_array($name, $this->excludedFiles)) return true;
        foreach($this->excludedFilesReg as $reg){
            if(preg_match($reg, $name)) return true;
        }
        return false;
    }

}


?>