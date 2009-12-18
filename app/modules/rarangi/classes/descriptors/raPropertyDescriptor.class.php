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
class raPropertyDescriptor extends raBaseDescriptor {

    const TYPE_VAR = 0;
    const TYPE_STATIC_VAR = 1;
    const TYPE_CONST = 2;

    public $name = '';
    
    public $defaultValue = '';
    
    public $datatype = '';
    
    public $accessibility = T_PUBLIC;
    
    public $typeProperty = 0;
    
    public $classId = null;

    protected $acceptPackage= false;

    public function inheritsFrom($desc) {
        $this->deprecated = $desc->deprecated;
        $this->ignore = $desc->ignore;
        $this->since = $desc->since;
    }

    protected $currentVar = '';
    protected $varData = array();
    protected $unamedVarData = null;
    protected $varContentInError = false;

    protected function parseSpecificTag($tag, $content) {
        if ($tag == 'var' || $tag == 'const' ) {
            if (preg_match("/^([^\s]+)(?:\s+". ($tag == 'var'?'\$':'#')."([a-zA-Z_0-9]+))?(?:\s+(.+))?\s*$/", $content, $m)) {
                $this->varContentInError = false;
                $data = array($m[1], $this->shortDescription, $this->description);
                $name = '';

                if (isset($m[2])) {
                    $name = $m[2];
                    if (isset($m[3])) {
                        if ($this->shortDescription) {
                            $data[1] = $this->shortDescription;
                            if (trim($this->description)) {
                                $data[2] .= "\n".$m[3];
                            }
                            else
                                $data[2] = $m[3];
                        }
                        else {
                            $data[1] = $m[3];
                        }
                    }
                }
                $this->currentVar = $name;
                if ($name) {
                    $this->varData[$name] = $data;
                }
                else {
                    $this->unamedVarData = $data;
                }
            }
            else {
                $this->currentVar = '';
                $this->varContentInError = true;
                $this->project->logger()->warning('@'.$tag.': invalid arguments: '.$content);
            }
            return true;
        }
        return false;
    }
    
    protected function addContentToSpecificTag ($tag, $content) {
        if ($tag == 'var' || $tag == 'const') {
            if ($this->varContentInError)
                return true;

            if ($this->currentVar) {
                $d = $this->varData[$this->currentVar][2];
                if ($d)
                    $d .= "\n".$content;
                else
                    $d = $content;
                $this->varData[$this->currentVar][2] = $d;
            }
            else {
                $d = $this->unamedVarData[2];
                if ($d)
                    $d .= "\n".$content;
                else
                    $d = $content;
                $this->unamedVarData[2] = $d;
            }
            return true;
        }
        return false;
    }
    
    public function save() {
        if ($this->ignore)
            return;

        if ($this->name == '')
            throw new Exception('property name undefined');

        $dao = jDao::get('rarangi~class_properties');
        $record = jDao::createRecord('rarangi~class_properties');
        $record->name = $this->name;
        $record->class_id = $this->classId;
        $record->project_id = $this->project->id();
        $record->line_start = $this->line;
        $record->default_value = $this->defaultValue;
        $record->type = $this->typeProperty;
        if ($this->accessibility == T_PUBLIC)
            $record->accessibility = 'PUB';
        elseif ($this->accessibility == T_PROTECTED)
            $record->accessibility = 'PRO';        
        elseif ($this->accessibility == T_PRIVATE)
            $record->accessibility = 'PRI';

        if (isset($this->varData[$this->name])) {
            $data = $this->varData[$this->name];
        }
        else if ($this->unamedVarData !== null) {
            $data = $this->unamedVarData;
        }
        else
            $data = array('', $this->shortDescription, $this->description);

        list($record->datatype, $record->short_description, $record->description) = $data;

        $this->fillRecord($record);

        $dao->insert($record);
    }

}