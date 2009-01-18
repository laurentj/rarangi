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

    public $parameters = array();

    public $returnType='';
    
    public $returnDescription='';

    //public $usedGlobalsVars;
    //public $staticVars;

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

        }
        return false;
    }

    protected function addContentToSpecificTag($tag, $content) {
        if($tag == 'return') {
            $this->returnDescription .=  "\n".$content;
            return true;
        }
        return false;
    }
    
    public function save() {
        if($this->name == '')
            throw new Exception('function name undefined');

        $dao = jDao::get('rarangi~functions');
        $record = jDao::createRecord('rarangi~functions');
        $record->name = $this->name;
        $record->project_id = $this->projectId;
        $record->package_id = $this->getPackageId($this->package);
        $record->line_start = $this->line;
        $record->line_end = $this->lineEnd;
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
        $funcauthors = jDao::get("rarangi~functions_authors");
        $funcauthor = jDao::createRecord("rarangi~functions_authors");
        $funcauthor->function_id = $record->id;
        $funcauthor->as_contributor = 0;
        foreach ($authors as $authorid) {
            $funcauthor->author_id = $authorid;
            $funcauthors->insert($funcauthor);
        }
        $funcauthor->as_contributor = 1;
        foreach ($contributors as $authorid) {
            $funcauthor->author_id = $authorid;
            $funcauthors->insert($funcauthor);
        }
    }
}
