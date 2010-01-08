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
class raGlobalVariableDescriptor  extends raBaseDescriptor {

    const TYPE_VAR = 0;
    const TYPE_STATIC_VAR = 1;
    const TYPE_CONST = 2;

    /**
     * @var string name of the interface/class
     */
    public $name ='';

    /**
     *
     */
    public $defaultValue = '';
    
    /**
     * the type of the global : variable or const
     * @var integer one of the TYPE_* const
     */
    public $typeProperty = 0;

    protected $currentVar = '';
    protected $varData = array();
    protected $unamedVarData = null;
    protected $varContentInError = false;

    /**
     * the object is initialized with all informations of an other 
     * @param raBaseDescriptor $desc a descriptor
     */
    public function inheritsFrom ($desc) {
        $this->package = $desc->package;
        $this->isDeprecated = $desc->isDeprecated;
        $this->deprecated = $desc->deprecated;
        $this->experimental = $desc->experimental;
        $this->ignore = $desc->ignore;
        $this->package = $desc->package;
    }

    protected function parseSpecificTag($tag, $content) {
        if ($tag == 'var' || $tag == 'const' || ($this->acceptPackage && $tag == 'global')) {
            if (preg_match("/^([^\s]+)(?:\s+". ($tag == 'const'?'#':'\$')."([a-zA-Z_0-9]+))?(?:\s+(.+))?\s*$/", $content, $m)) {
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
        if ($tag == 'var' || $tag == 'const' || ($this->acceptPackage && $tag == 'global')) {
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
            throw new Exception('global name undefined');

        $dao = jDao::get('rarangi~globals');
        $record = jDao::createRecord('rarangi~globals');
        $record->name = $this->name;
        $record->project_id = $this->project->id();
        $record->file_id = $this->fileId;
        $record->line_start = $this->line;
        $record->default_value = $this->defaultValue;
        $record->type = $this->typeProperty;
        $record->package_id = $this->project->getPackageId($this->package);

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

        $globauthor = jDao::createRecord("rarangi~globals_authors");
        $globauthor->global_id = $record->id;
        $this->saveAuthorsContributors($globauthor, "rarangi~globals_authors");
    }
    
    /**
     * save all defined variables found in the doc comment
     * useful when the parser ahs some difficulties to read the php variable.
     * so we assume that the doc comment is well enough documented.
     * @return boolean true if it saved some variables documentation or not
     */
    public function saveForAll() {
        if (!count($this->varData))
            return false;
        foreach($this->varData as $name =>$d) {
            $this->name = $name;
            $this->save();
        }
        return true;
    }
}

