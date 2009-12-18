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
class raMethodDescriptor  extends raBaseDescriptor {

    public $name;
    
    public $classId = null;

    public $accessibility = T_PUBLIC;

    public $isStatic = false;

    public $isFinal = false;

    public $isAbstract = false;

    public $docParameters = array();
    
    protected $currentParam = '';
    
    public $parameters = array();

    public $returnType='';
    
    public $returnDescription='';

    //public $usedGlobalsVars;
    //public $staticVars;
    
    protected $acceptPackage= false;

    public function inheritsFrom($desc) {
        $this->deprecated = $desc->deprecated;
        $this->ignore = $desc->ignore;
        $this->since = $desc->since;
    }

    protected function parseSpecificTag($tag, $content) {
        if($tag == 'return') {
            if(preg_match("/^([^\s]+)(?:\s+(.+))?$/", $content, $m)) {
                $this->returnType = $m[1];
                $this->returnDescription = (isset($m[2])?$m[2]:'');
            }
            else {
                $this->returnType = '';
                $this->returnDescription = '';
            }
            return true;
        }
        else if($tag == 'param') {
            if (preg_match("/^([^\s]+)\s+\\$([a-zA-Z_0-9]+)(?:\s+(.+))?$/", $content, $m)) {
                $this->docParameters[$m[2]] = array($m[1], (isset($m[3])?$m[3]:''));
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
            throw new Exception('method name undefined');

        $dao = jDao::get('rarangi~class_methods');
        $record = jDao::createRecord('rarangi~class_methods');
        $record->name = $this->name;
        $record->class_id = $this->classId;
        $record->project_id = $this->project->id();
        $record->is_static = $this->isStatic;
        $record->is_final = $this->isFinal;
        $record->is_abstract = $this->isAbstract;
        if($this->accessibility == T_PUBLIC)
            $record->accessibility = 'PUB';
        elseif($this->accessibility == T_PROTECTED)
            $record->accessibility = 'PRO';        
        elseif($this->accessibility == T_PRIVATE)
            $record->accessibility = 'PRI';
        else
            $record->accessibility = 'PUB';
        $record->return_datatype = $this->returnType;
        $record->return_description = $this->returnDescription;

        $record->line_start = $this->line;
        $record->line_end = $this->lineEnd;
        $record->short_description = $this->shortDescription;
        $record->description = $this->description;

        $this->fillRecord($record);

        $dao->insert($record);

        $methauthor = jDao::createRecord("rarangi~methods_authors");
        $methauthor->name = $this->name;
        $methauthor->class_id = $this->classId;
        $this->saveAuthorsContributors($methauthor, "rarangi~methods_authors");

        $param = jDao::createRecord('rarangi~method_parameters');
        $parameters = jDao::get('rarangi~method_parameters');
        $param->class_id = $this->classId;
        $param->method_name = $this->name;
        foreach ($this->parameters as $k=>$p) {
            $param->arg_number = $k+1;
            list($param->type, $param->name, $param->defaultvalue, $param->documentation) = $p;
            $parameters->insert($param);
        }
    }
}

