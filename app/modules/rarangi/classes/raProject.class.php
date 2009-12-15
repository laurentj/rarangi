<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2009 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/



/**
 * main class : it launches the parsing of files
 */
class raProject {

    /**
     * @var raLogger
     */
    protected $_logger;

    /**
     * @param string $projectName the name of the project
     * @param raLogger $logger
     */
    function __construct($projectName, $logger) {
        $this->_logger = $logger;
        $this->init($projectName);
    }
    
    /**
     * @return raLogger
     */
    function logger() {
        return $this->_logger;
    }

    /**
     * @var jDaoRecord
     */
    protected $projectRec;

    /**
     * initialize the object. create a project record if it doesn't exist
     * @param stdobj $config  the configuration object
     */
    protected function init($projectName) {
        $projectdao = jDao::get('rarangi~projects');
        if (!$this->projectRec = $projectdao->getByName($projectName)) {
            $this->projectRec = jDao::createRecord('rarangi~projects');
            $this->projectRec->name = $projectName;
            $projectdao->insert($this->projectRec);
        }
        else {
            $db = jDb::getConnection();
            $db->exec("DELETE FROM classes_authors where class_id IN (SELECT id FROM classes WHERE project_id = ".$this->projectRec->id.')');
            $db->exec("DELETE FROM functions_authors where function_id IN (SELECT id FROM functions WHERE project_id = ".$this->projectRec->id.')');
            $db->exec("DELETE FROM function_parameters where function_id IN (SELECT id FROM functions WHERE project_id = ".$this->projectRec->id.')');
            $db->exec("DELETE FROM files_authors where file_id IN (SELECT id FROM files WHERE project_id = ".$this->projectRec->id.')');
            $db->exec("DELETE FROM methods_authors where class_id IN (SELECT id FROM classes WHERE project_id = ".$this->projectRec->id.')');
            $db->exec("DELETE FROM method_parameters where class_id IN (SELECT id FROM classes WHERE project_id = ".$this->projectRec->id.')');
            jDao::get('authors')->deleteByProject($this->projectRec->id);
            jDao::get('interface_class')->deleteByProject($this->projectRec->id);
            jDao::get('class_properties')->deleteByProject($this->projectRec->id);
            jDao::get('class_methods')->deleteByProject($this->projectRec->id);
            jDao::get('classes')->deleteByProject($this->projectRec->id);
            jDao::get('functions')->deleteByProject($this->projectRec->id);
            jDao::get('files_content')->deleteByProject($this->projectRec->id);
            jDao::get('files')->deleteByProject($this->projectRec->id);
            jDao::get('packages')->deleteByProject($this->projectRec->id);
        }
    }

    public function id() {
        return $this->projectRec->id;
    }

    /**
     * @var array  list of id of packages. used by getPackageId as a cache
     */
    protected static $_packages = array();

    /**
     * return the package id corresponding to the given name. If the package
     * is not registered into the database, a new record is created
     * @param string $packageName the name of the package
     * @return integer the id of the package
     */
    public function getPackageId ($packageName) {
        if ($packageName == '')
            return null;

        if (isset(self::$_packages[$packageName]))
            return self::$_packages[$packageName];

        $package = jDao::get('rarangi~packages')->getByName($this->projectRec->id, $packageName);
        if (!$package) {
            $package = jDao::createRecord('rarangi~packages');
            $package->project_id = $this->projectRec->id;
            $package->name = $packageName;
            jDao::get('rarangi~packages')->insert($package);
        }
        self::$_packages[$packageName] = $package->id;
        return $package->id;
    }
}
