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

    public $return;

    //public $usedGlobalsVars;
    //public $staticVars;

    protected function parseSpecificTag($tag, $content) {
        if($tag == 'return') {
            $this->return = $content;
        }
    }
    
    public function save() {
        if($this->name == '')
            throw new Exception('function name undefined');

        $dao = jDao::get('rarangi~functions');
        $record = jDao::createRecord('rarangi~functions');
        $record->name = $this->name;
        $record->project_id = $this->projectId;
        $record->package_id = $this->getPackageId($this->package);
        $record->line_number = $this->line;        
        $record->short_description = $this->shortDescription;
        $record->description = $this->description;
        
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
