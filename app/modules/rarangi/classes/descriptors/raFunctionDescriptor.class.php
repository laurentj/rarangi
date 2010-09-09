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
 *
 */
class raFunctionDescriptor  extends raBaseDescriptor {

    public $name;

    public $docParameters = array();
    
    protected $currentParam = '';

    public $parameters = array();

    public $returnType=array();
    
    public $returnDescription='';

    public $functionId = '';

    //public $usedGlobalsVars;
    //public $staticVars;

    protected function parseSpecificTag($tag, $content) {
        if ($tag == 'return') {
            if(preg_match("/^([^\s]+)(?:\s+(.+))?$/", $content, $m)) {
                $this->returnType = preg_split("/,|\|/", $m[1]);
                $this->returnDescription = (isset($m[2])?$m[2]:'');
            }
            else {
                $this->returnType = array();
                $this->returnDescription = '';
            }
            return true;
        }
        else if ($tag == 'param') {
            if(preg_match("/^([^\s]+)\s+\\$([a-zA-Z_0-9]+)(?:\s+(.+))?$/", $content, $m)) {
                $this->docParameters[$m[2]] = array(preg_split("/,|\|/", $m[1]), isset($m[3])?$m[3]:'');
                $this->currentParam = $m[2];
            }
            else {
                $this->currentParam = '';
                $this->project->logger()->warning('@param, invalid arguments: '.$content);
            }
        }
        return false;
    }

    protected function addContentToSpecificTag($tag, $content) {
        if ($tag == 'return') {
            $this->returnDescription .=  "\n".$content;
            return true;
        }
        elseif ($tag == 'param' && $this->currentParam) {
            $this->docParameters[$this->currentParam][1].="\n".$content;
        }
        return false;
    }
    
    public function save() {
        if ($this->ignore)
            return;

        if ($this->name == '')
            throw new Exception('function name undefined');

        $dao = jDao::get('rarangi~functions');
        $record = jDao::createRecord('rarangi~functions');
        $record->name = $this->name;
        $record->project_id = $this->project->id();
        $record->package_id = $this->project->getPackageId($this->package);
        if (count($this->returnType))
            $record->return_datatype = "|".implode("|",$this->returnType)."|";
        else
            $record->return_datatype = "";
        $record->return_description = $this->returnDescription;

        $record->file_id = $this->fileId;
        $record->line_start = $this->line;
        $record->line_end = $this->lineEnd;
        $record->short_description = $this->shortDescription;
        $record->description = $this->description;
        
        $this->fillRecord($record);

        $dao->insert($record);
        
        $this->functionId = $record->id;
        
        $funcauthor = jDao::createRecord("rarangi~functions_authors");
        $funcauthor->function_id = $record->id;
        $this->saveAuthorsContributors($funcauthor, "rarangi~functions_authors");
        
        $param = jDao::createRecord('rarangi~function_parameters');
        $parameters = jDao::get('rarangi~function_parameters');
        $param->function_id = $record->id;
        foreach ($this->parameters as $k=>$p) {
            $param->arg_number = $k+1;
            list($param->type, $param->name, $param->defaultvalue, $param->documentation) = $p;
            if (is_array($param->type)) {
                if (count($param->type))
                    $param->type = '|'.implode('|',$param->type).'|';
                else
                    $param->type = '';
            }
            elseif ($param->type !='')
                $param->type = '|'.$param->type.'|';
            else
                $param->type = '';
            $parameters->insert($param);
        }
    }
}
