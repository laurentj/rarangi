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

    /**
     * other tags which are not supported natively
     * @var array
     */
    public $otherTags = array();


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
        $this->package = $desc->package;
        $this->authors = $desc->authors;
        $this->contributors = $desc->contributors;
        $this->copyright = $desc->copyright;
        $this->deprecated = $desc->deprecated;
        $this->ignore = $desc->ignore;
        $this->internal = $desc->internal;
        $this->links = $desc->links;
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
                        case 'author':
                        case 'contributor':
                            $people = explode(",",$content);
                            foreach($people as $p) {
                                if(trim($p) == '')
                                    continue;
                                if(preg_match('/^([^\<]+)(?:\<([^\>]*)\>)?$/', $p,$m)) {
                                    $n = trim($m[1]);
                                    $e = (isset($m[2])?trim($m[2]):'');
                                    if ($tag == 'author')
                                        $this->authors[]=array($n,$e);
                                    else
                                        $this->contributors[]=array($n,$e);
                                }
                                else {
                                    raLogger::warning($tag." name malformed :".$p);
                                    if ($tag == 'author')
                                        $this->authors[]=array(trim($p),'');
                                    else
                                        $this->contributors[]=array(trim($p),'');
                                }
                            }
                            break;
                        case 'copyright':
                            break;
                        case 'deprecated':
                            break;
                        case 'ignore':
                            break;
                        case 'internal':
                            break;
                        case 'links':
                            break;
                        case 'see':
                            break;
                        case 'uses':
                            break;
                        case 'since':
                            break;
                        case 'changelog':
                            break;
                        case 'todo':
                            break;
                        default:
                            if(!$this->parseSpecificTag($tag, $content)) {
                                $this->otherTags[$tag] = $content;
                            }
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
    
    protected function parseSpecificTag($tag, $content) {
        return false;
    }
    
    public function save() {}
    
    protected static $_packages = array();
    
    protected function getPackageId($packageName) {
        if($packageName == '')
            return null;
        if(isset(self::$_packages[$packageName]))
            return self::$_packages[$packageName];
        $package = jDao::get('rarangi~packages')->getByName($this->projectId, $packageName);
        if(!$package) {
            $package = jDao::createRecord('rarangi~packages');
            $package->project_id = $this->projectId;
            $package->name = $packageName;
            jDao::get('rarangi~packages')->insert($package);
        }
        self::$_packages[$packageName] = $package->id;
        return $package->id;
    }
    
    protected function saveAuthorsContributors(){
        $authorId = array();
        $dao = jDao::get('rarangi~authors');
        foreach($this->authors as $author) {
            //$dao
        }
    }
}


