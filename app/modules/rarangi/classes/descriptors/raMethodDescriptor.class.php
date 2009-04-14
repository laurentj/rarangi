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
        $this->projectId = $desc->projectId;
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
            if(preg_match("/^([^\s]+)\s+\\$([a-zA-Z_0-9]+)(?:\s+(.+))?$/", $content, $m)) {
                $this->docParameters[$m[2]] = array($m[1], (isset($m[3])?$m[3]:''));
                $this->currentParam = $m[2];
            }
            else
                raLogger::warning('@param, invalid arguments :'.$content);
        }
        return false;
    }

    protected function addContentToSpecificTag($tag, $content) {
        if($tag == 'return') {
            $this->returnDescription .=  "\n".$content;
            return true;
        }
        elseif ($tag == 'param') {
            $this->docParameters[$this->currentParam][1].="\n".$content;
        }
        return false;
    }

    public function save() {
        if($this->name == '')
            throw new Exception('method name undefined');

        $dao = jDao::get('rarangi~class_methods');
        $record = jDao::createRecord('rarangi~class_methods');
        $record->name = $this->name;
        $record->class_id = $this->classId;
        $record->project_id = $this->projectId;
        $record->line_start = $this->line;
        $record->line_end = $this->lineEnd;
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

        $record->short_description = $this->shortDescription;
        $record->description = $this->description;
        $record->return_datatype = $this->returnType;
        $record->return_description = $this->returnDescription;
        $record->copyright = $this->copyright;
        $record->internal = $this->internal;
        $record->links = serialize($this->links);
        $record->see = serialize($this->see);
        $record->uses = serialize($this->uses);
        $record->changelog = serialize($this->changelog);
        $record->todo = $this->todo;
        $record->since = $this->since;
        $record->license_label = $this->licenseLabel;
        $record->license_link = $this->licenseLink;
        $record->license_text = $this->licenseText;
        $dao->insert($record);

        list($authors, $contributors) = $this->saveAuthorsContributors();
        $methauthors = jDao::get("rarangi~methods_authors");
        $methauthor = jDao::createRecord("rarangi~methods_authors");
        $methauthor->name = $this->name;
        $methauthor->class_id = $this->classId;
        $methauthor->as_contributor = 0;
        foreach ($authors as $authorid) {
            $methauthor->author_id = $authorid;
            $methauthors->insert($methauthor);
        }
        $methauthor->as_contributor = 1;
        foreach ($contributors as $authorid) {
            $methauthor->author_id = $authorid;
            $methauthors->insert($methauthor);
        }

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

