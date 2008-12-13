<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi
* @license     GNU General Public license see license file or http://www.gnu.org/licenses/gpl.html
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
    public $links = array();
    /**
     * array of components to link on the details page of the component
     * a string in the array = file.ext|elementname|class::methodname()|class::$variablename|functionname()|function functionname
     * @var array
     */
    public $see = array();
    /**
     * =see with backlinks
     * @var array
     */
    public $uses = array();
    /**
     * the version in which the component has been created
     * @var string
     */
    public $since = '';
    /**
     * changelogs
     * array( array('version','description')...)
     * @var array
     */
    public $changelog = array();
    /**
     * the description of what to do.
     * @var string
     */
    public $todo = '';

    /**
     * other tags which are not supported natively
     * @var array
     */
    public $otherTags = array();

    /**
     * @var string
     */
    public $licenseLink = '';
    
    /**
     * @var string
     */
    public $licenseLabel = '';

    /**
     * @var string
     */
    public $licenseText = '';


    public $projectId = null;
    public $fileId = null;
    public $line = 0;
    public $lineEnd = 0;
    
    protected $acceptPackage= true;
    
    function __construct($projectId, $fileId, $line){
        $this->projectId = $projectId;
        $this->fileId = $fileId;
        $this->line = $line;
    }

    /**
     * the object is initialized with all informations of an other 
     * @param raBaseDescriptor $desc a descriptor
     */
    public function inheritsFrom($desc) {
        $this->projectId = $desc->projectId;
        $this->package = $desc->package;
        $this->authors = $desc->authors;
        $this->contributors = $desc->contributors;
        $this->copyright = $desc->copyright;
        $this->deprecated = $desc->deprecated;
        $this->ignore = $desc->ignore;
        $this->since = $desc->since;
        $this->licenseLabel = $desc->licenseLabel;
        $this->licenseLink = $desc->licenseLink;
        $this->licenseText = $desc->licenseText;
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
            if(preg_match('/^\s*\*\s*(?:@(\w+))?(.*)$/',$ligne,$m)) {
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
                            $this->copyright .= $content;
                            break;
                        case 'deprecated':
                            $this->deprecated = trim($content);
                            break;
                        case 'ignore':
                            $this->ignore = true;
                            break;
                        case 'internal':
                            $this->internal .= $content;
                            break;
                        case 'links':
                            $pos = strpos($content," ");
                            $link = $label = '';
                            if($pos === false) {
                                $link = $content;
                            }
                            else {
                                $link = substr($content, 0, $pos);
                                $label = substr($content, $pos+1);
                            }
                            $this->links[] = array($link, $label);
                            break;
                        case 'see':
                            $this->see[] = $content;
                            break;
                        case 'uses':
                            $this->uses[] = $content;
                            break;
                        case 'since':
                            $this->since = $content;
                            break;
                        case 'changelog':
                            $this->changelog[] = $content;
                            break;
                        case 'todo':
                            $this->todo = $content;
                            break;
                        case 'license':
                            $pos = strpos($content," ");
                            $this->licenseLink = $this->licenseLabel = '';
                            if($pos === false) {
                                $this->licenseLink = $content;
                            }
                            else {
                                $this->licenseLink = substr($content, 0, $pos);
                                $this->licenseLabel = substr($content, $pos+1);
                            }
                            break;
                        default:
                            if(!$this->parseSpecificTag($tag, $content)) {
                                $this->otherTags[$tag] = $content;
                            }
                    }
                    $currentTag = $tag;
                }
                else {
                    switch ($currentTag) {
                    case 'shortDescription':
                        if (trim($content) == '' && $this->shortDescription != '') {
                            $currentTag = 'description';
                        } else if($this->shortDescription != ''){
                            $this->shortDescription .= "\n".$content;
                        } else {
                            $this->shortDescription = $content;
                        }
                        break;
                    
                    case 'description':
                        if ($this->description != '') {
                            $this->description .= "\n".$content;
                        } else {
                            $this->description = $content;
                        }
                        break;
                    case 'internal':
                        $this->internal.="\n".$content;
                        break;
                    case 'copyright':
                        $this->copyright.="\n".$content;
                        break;
                    case 'changelog':
                        $this->changelog[count($this->changelog)-1] .= "\n".$content;
                        break;
                    case 'todo':
                        $this->todo .= "\n".$content;
                        break;
                    case 'license':
                        if ($this->licenseText != '') {
                            $this->licenseText .= "\n".$content;
                        } else {
                            $this->licenseText = $content;
                        }
                        break;
                    case 'deprecated':
                    case 'ignore':
                    case 'links':
                    case 'see':
                    case 'uses':
                    case 'since':
                        break;
                    default:
                        if(!$this->addContentToSpecificTag($currentTag, $content)) {
                            $this->otherTags[$currentTag] .= $content;
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

    protected function addContentToSpecificTag($tag, $content) {
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
        return array ($this->_registerAuthors($this->authors), $this->_registerAuthors($this->contributors));
    }

    private function _registerAuthors(& $developers){
        $authorId = array();
        $dao = jDao::get('rarangi~authors');
        foreach($developers as $author) {
            list($name, $email) = $author;
            $dev = null;
            if($email !='') {
                $dev = $dao->getByEmail($email, $this->projectId);
                if ($dev && $dev->name == '' && $name !='') {
                    $dev->name = $name;
                    $dao->update($dev);
                }
            }
            else {
                // find by name
                $dev = $dao->getByName($name, $this->projectId);
            }
            if(!$dev) {
                $dev = jDao::createRecord('rarangi~authors');
                $dev->name = $name;
                $dev->email = $email;
                $dev->project_id = $this->projectId;
                $dao->insert($dev);
            }
            $authorId[] = $dev->id;
        }
        return $authorId;
    }
}


