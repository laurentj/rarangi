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
class raBaseDescriptor {
    /**
     * short description of the component (label)
     * @var string
     */
    public $shortDescription = null;

    /**
     * long description of the component
     * @var string 
     */
    public $description = null;

    /**
     * package name
     * @var string 
     */
    public $package = '';

    /**
     * list of authors array( ... array(name,email)...)
     * @var array
     */
    public $authors = array();

    /**
     * list of contributors array( ... array(name,email)...)
     * @var array
     */
    public $contributors = array();

    /**
     * copyright informations
     * @var string
     */
    public $copyright = '';
    /**
     * Indicate if it is deprecated + some optional information
     * like the version since it is deprecated
     * @var 
     */
    public $deprecated = '';
    /**
     * filename of an example to include in the documentation
     * array ( ... array(filename, label)... )
     * @var string
     */
    //public $examples;
    /**
     * indicate if the component should be ignored
     * @var boolean
     */
    public $ignore = false;
    /**
     * internal documentation for contributors
     * @var 
     */
    public $internal = '';
    /**
     * links for details
     * array(.... array(link, label)...)
     * @var array
     */
    public $links;
    /**
     * array of components to link on the details page of the component
     * a string in the array = file.ext|elementname|class::methodname()|class::$variablename|functionname()|function functionname
     * @var array
     */
    public $see;
    /**
     * =see with backlinks
     * @var array
     */
    public $uses;
    /**
     * the version in which the component has been created
     * @var string
     */
    public $since;
    /**
     * changelogs
     * array( array('version','description')...)
     * @var array
     */
    public $changelog;
    /**
     * the description of what to do.
     * @var string
     */
    public $todo;

    public $projectId = null;
    public $fileId = null;
    public $line = 0;
    
    function __construct($projectId, $fileId, $line){
        $this->projectId = $projectId;
        $this->fileId = $fileId;
        $this->line = $line;
    }

    /**
     * the object is initialized with all informations of an other 
     * @param jBaseDescriptor $desc a descriptor
     */
    public function inheritsFrom($desc) {
        $this->projectId = $desc->projectId;
        $this->package = $desc->package ;
        $this->author = $desc->author ;
        $this->contributor = $desc->contributor ;
        $this->copyright = $desc->copyright ;
        $this->deprecated = $desc->deprecated ;
        $this->ignore = $desc->ignore ;
        $this->internaluse = $desc->internaluse ;
        $this->link = $desc->link ;
        $this->since = $desc->since;
    }

    /**
     * read informations from a phpdoc tag and save them
     * @param string $docComment  the phpdoc comment
     */
    public function initFromPhpDoc($docComment){
        $docComment = substr($docComment , 2, -2); // we remove  /* at the begining and */ at the end
        $lignes = preg_split("/\015\012|\015|\012/",$docComment);
        $currentTag = 'shortDescription';
        foreach($lignes as $ligne){
            if(preg_match('/^\s*\*\s*(?:@(\w+))?(.*)$/',$ligne,$m)){
                list(,$tag, $content) = $m;
                $content = trim($content);
                if($tag != ''){
                    switch($tag) {
                        case 'package':
                            $this->package = $content;
                            break;
                        case 'subpackage':
                            if($content !='')
                                $this->package .= '.'.$content;
                            break;
                    }
                    $currentTag = $tag;
                }
                else {
                    if($currentTag == 'shortDescription') {
                        if(trim($content) == '' && $this->shortDescription != '') {
                            $currentTag = 'description';
                        } else if($this->shortDescription != ''){
                            $this->shortDescription .= "\n".$content;
                        } else {
                            $this->shortDescription = $content;
                        }
                        
                    }
                    else if($currentTag == 'description') {
                        if($this->description != ''){
                            $this->description .= "\n".$content;
                        } else {
                            $this->description = $content;
                        }
                    }
                }
            }else{
                //throw new Exception("bad syntax in a doc comment");
               //jLogger::warning('bad syntax in a doc comment');
            }
        }
    }
    
    protected function parseSpecificTag($tag, $content) {}
    
    public function save() {}
    
    
    protected function getPackageId($packageName) {
        if($packageName == '')
            return null;
        $package = jDao::get('rarangi~packages')->getByName($this->projectId, $packageName);
        if(!$package) {
            $package = jDao::createRecord('rarangi~packages');
            $package->project_id = $this->projectId;
            $package->name = $packageName;
            jDao::get('rarangi~packages')->insert($package);
        }
        return $package->id;
    }
}


