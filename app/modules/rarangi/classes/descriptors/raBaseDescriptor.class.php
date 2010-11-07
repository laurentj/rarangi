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

    const UNKNOWN_PACKAGE = '_unknown';

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
     * Indicate if it is deprecated
     * @var boolean
     */
    public $isDeprecated = false;

    /**
     * optional information for the deprecated status
     * @var string 
     */
    public $deprecated = '';

    /**
     * Indicate if it is experimental
     * @var boolean 
     */
    public $experimental = false;

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
     * @var string
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
    public $userTags = array();

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

    /**
     * @var raProject the owner project
     */
    public $project = null;
    
    /**
     * @var integer the id of the file where the component is declared
     */
    public $fileId = null;
    
    /**
     * @var integer the first line where the component is declared
     */
    public $line = 0;
    
    /**
     * @var integer the last line where the component is declared
     */
    public $lineEnd = 0;

    protected $acceptPackage = true;
    
    function __construct (raProject $project, $fileId, $line) {
        $this->project = $project;
        $this->fileId = $fileId;
        $this->line = $line;
        $this->package = self::UNKNOWN_PACKAGE;
    }

    /**
     * the object is initialized with all informations of an other 
     * @param raBaseDescriptor $desc a descriptor
     */
    public function inheritsFrom ($desc) {
        $this->package = $desc->package;
        $this->authors = $desc->authors;
        $this->contributors = $desc->contributors;
        $this->copyright = $desc->copyright;
        $this->isDeprecated = $desc->isDeprecated;
        $this->deprecated = $desc->deprecated;
        $this->experimental = $desc->experimental;
        $this->ignore = $desc->ignore;
        $this->since = $desc->since;
        $this->licenseLabel = $desc->licenseLabel;
        $this->licenseLink = $desc->licenseLink;
        $this->licenseText = $desc->licenseText;
    }

    /**
     * read informations from a phpdoc tag and store them in the object
     * @param string $docComment  the phpdoc comment
     */
    public function initFromPhpDoc ($docComment) {
        $docComment = substr($docComment, 2, -2); // we remove  /* at the begining and */ at the end
        $lignes = preg_split("/\015\012|\015|\012/", $docComment);
        $currentTag = 'shortDescription';
        foreach ($lignes as $ligne) {
            if (preg_match('/^\s*\*\s*(?:@(\w+))?(.*)$/', $ligne, $m)) {
                list(,$tag, $content) = $m;
                $content = trim($content);
                if ($tag != '') {
                    switch ($tag) {
                        case 'package':
                            if ($this->acceptPackage) {
                                if ($content != '')
                                    $this->package = $content;
                                else
                                    $this->noticeEmpty($tag);
                            }
                            else
                                $this->project->logger()->error('@package is not allowed here');
                            break;
                        case 'subpackage':
                            if ($this->acceptPackage) {
                                if ($content !='') {
                                    if ($this->package == self::UNKNOWN_PACKAGE) {
                                        $this->project->logger()->error('@subpackage shouldn\'t be defined because @package is not define');
                                    }
                                    else
                                        $this->package .= '.'.$content;
                                }
                                else
                                    $this->noticeEmpty($tag);
                            }
                            else
                                $this->project->logger()->error('subpackage tag is not allowed here');
                            break;
                        case 'author':
                        case 'contributor':
                            $this->parseAuthor($tag, $content);
                            break;
                        case 'copyright':
                            if ($content !='') {
                                $this->copyright .= ' '.$content;
                            }
                            else {
                                $this->noticeEmpty($tag);
                            }
                            break;
                        case 'deprecated':
                            $this->isDeprecated = true;
                            $this->deprecated = $content;
                            break;
                        case 'experimental':
                            $this->experimental = true;
                            if ($content != '') {
                                $this->project->logger()->notice('@experimental shouldn\'t have value');
                            }
                            break;
                        case 'ignore':
                            $this->ignore = true;
                            if ($content != '') {
                                $this->project->logger()->notice('@ignore shouldn\'t have value');
                            }
                            break;
                        case 'internal':
                            $this->internal .= $content;
                            break;
                        case 'link':
                            $link = $this->readLinkLabel($content);
                            if ($link[0] == '' && $link[1] == '') {
                                $this->noticeEmpty($tag);
                            }
                            else 
                                $this->links[] = $link;
                            break;
                        case 'see':
                            if ($content == '') {
                                $this->noticeEmpty($tag);
                            }
                            else
                                $this->see[] = $content;
                            break;
                        case 'uses':
                            if ($content == '') {
                                $this->noticeEmpty($tag);
                            }
                            else
                                $this->uses[] = $content;
                            break;
                        case 'since':
                            if ($content == '') {
                                $this->noticeEmpty($tag);
                            }
                            else
                                $this->since = $content;
                            break;
                        case 'changelog':
                            if ($content == '') {
                                $this->noticeEmpty($tag);
                            }
                            else
                                $this->changelog[] = $content;
                            break;
                        case 'todo':
                            if ($content == '') {
                                $this->noticeEmpty($tag);
                            }
                            else
                                $this->todo = $content;
                            break;
                        case 'license':
                        case 'licence':
                            if ($content == '') {
                                $this->noticeEmpty($tag);
                            }
                            else {
                                list($this->licenseLink, $this->licenseLabel)
                                    = $this->readLinkLabel($content);
                            }
                            break;
                        default:
                            if (!$this->parseSpecificTag($tag, $content)) {
                                $this->userTags[$tag] = $content;
                            }
                    }
                    $currentTag = $tag;
                }
                else { // no tag, just a simple string
                    switch ($currentTag) {
                    case 'shortDescription':
                        if (trim($content) == '' && $this->shortDescription != '') {
                            $currentTag = 'description';
                        }
                        else if ($this->shortDescription != '') {
                            $this->shortDescription .= "\n".$content;
                        }
                        else {
                            $this->shortDescription = $content;
                        }
                        break;

                    case 'description':
                        if ($this->description != '') {
                            $this->description .= "\n".$content;
                        }
                        else {
                            $this->description = $content;
                        }
                        break;

                    case 'internal':
                        $this->internal .= "\n".$content;
                        break;

                    case 'copyright':
                        $this->copyright .= "\n".$content;
                        break;

                    case 'changelog':
                        $this->changelog[count($this->changelog)-1] .= "\n".$content;
                        break;

                    case 'todo':
                        $this->todo .= "\n".$content;
                        break;

                    case 'license':
                    case 'licence':
                        if ($this->licenseText != '') {
                            $this->licenseText .= "\n".$content;
                        }
                        else {
                            $this->licenseText = $content;
                        }
                        break;

                    case 'deprecated':
                    case 'ignore':
                    case 'link':
                    case 'see':
                    case 'uses':
                    case 'since':
                    case 'experimental':
                    case 'author':
                    case 'contributor':
                        break;

                    default:
                        if (!$this->addContentToSpecificTag($currentTag, $content)) {
                            if ($this->userTags[$currentTag] != '') {
                                $this->userTags[$currentTag] .= "\n".$content;
                            }
                            else {
                                $this->userTags[$currentTag] = $content;
                            }
                        }
                    }
                }
            }
            else { // invalid line
                //throw new Exception("bad syntax in a doc comment");
               //$this->project->logger()->warning('bad syntax in a doc comment');
            }
        }
    }

    protected function noticeEmpty($tag) {
        $this->project->logger()->notice("@$tag is ignored because it is empty");
    }

    protected function readLinkLabel($content) {
        $pos = strpos($content," ");
        $link = $label = '';
        if ($pos === false) {
            $link = $content;
        }
        else {
            $link = substr($content, 0, $pos);
            $label = substr($content, $pos+1);
        }
        if (!preg_match("/^(https|http|ftp):\\/\\//", $link)) {
            $label = $content;
            $link = '';
        }
        return array($link, $label);
    }

    /**
     * this method is responsible to parse tags which are not commons to
     * all components type. Should be overrided in child classes.
     * @param string $tag the tag name
     * @param string $content the content following the tag on the same line
     * @return boolean true if the tag is supported, false if this is an unknown
     *                  tag.
     */
    protected function parseSpecificTag ($tag, $content) {
        return false;
    }

    /**
     * this method is responsible to parse additionnal content for non commons
     * tag. Should be overrided in child classes.
     * @param string $tag the tag name
     * @param string $content the content on one of lines following the tag
     * @return boolean true if the tag is supported, false if this is an unknown
     *                  tag.
     */
    protected function addContentToSpecificTag ($tag, $content) {
        return false;
    }

    /**
     * save the parsed informations into the database.
     */
    public function save() {}
    
    /**
     * fill a record with commons properties
     * @param jDaoRecordBase $record
     */
    protected function fillRecord($record) {
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
        $record->deprecated = $this->deprecated;
        $record->is_deprecated = $this->deprecated;
        $record->is_experimental = $this->experimental;
        $record->user_tags = serialize($this->userTags);
    }

    /**
     * parse the value of a tag author
     * @param string $tag the tag
     * @param string $content the value to parse
     */
    protected function parseAuthor($tag, $content) {
        if ($content == '') {
            $this->project->logger()->notice("@$tag is ignored because it is empty");
            return;
        }
        $parts = preg_split("/([\<\>\,\(\)])/", $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        $partType = 0; // 0=name, 1= email, 2 = comment
        
        $author = '';
        $email = '';
        $comment = '';

        foreach ($parts as $p) {
            if ($p == '')
                continue;
            switch ($partType) {
                case 0:
                case 3:
                    if ($p == '<')
                        $partType = 1;
                    else if ($p == '(')
                        $partType = 2;
                    else if ($p == ',') {
                        $author = trim($author);
                        if ($author == '') {
                            if ($email == '')
                                break;
                            $author = $email;
                        }

                        if ($tag == 'author')
                            $this->authors[] = array($author,$email);
                        else
                            $this->contributors[] = array($author, $email);
                        $author = $email = '';
                        $partType = 0;
                    }
                    else if ($partType == 0) {
                        $author .= $p;
                    }
                    break;
                case 1: // email
                    if ($p == '>')
                        $partType = 3;
                    else if ($p == '<' || $p == '(' || $p == ')' || $p == ',') {
                        $this->project->logger()->warning("@$tag: invalid character in email '$p'");
                        $email .= $p;
                    }
                    else
                        $email .= $p;
                    break;
                case 2: //comment
                    if ($p == ')')
                        $partType = 3;
                    break;
                
            }
        }

        if ($partType == 1 || $partType == 2) {
            $this->project->logger()->warning("@$tag: invalid syntax. > or ) is missing");
        }

        $author = trim($author);
        if ( $author != '' || trim ($email) != '') {
            if ($author == '')
                $author = $email;
            if ($tag == 'author')
                $this->authors[] = array($author,$email);
            else
                $this->contributors[] = array($author, $email);
        }
    }
    
    
    /**
     * save authors and contributors into the database, and returns their ids
     * @param jDaoRecordBase $record the record to use to insert authors and
     *                               contributors into database
     * @param string $daoName the name of the dao to use for the insertion
     */
    protected function saveAuthorsContributors($record, $daoName) {

        $authors = $this->_registerAuthors($this->authors);
        $contributors = $this->_registerAuthors($this->contributors);
        // we store saved authors into it, to avoid to insert duplicated
        // associations
        $saved = array();

        $daoauthors = jDao::get($daoName);
        $record->as_contributor = 0;
        foreach ($authors as $authorid) {
            if (!isset($saved[$authorid])) {
                $record->author_id = $authorid;
                $daoauthors->insert($record);
                $saved[$authorid] = true;
            }
        }

        $record->as_contributor = 1;
        foreach ($contributors as $authorid) {
            if (!isset($saved[$authorid])) {
                $record->author_id = $authorid;
                $daoauthors->insert($record);
                $saved[$authorid] = true;
            }
        }
    }

    /**
     * save a list of developers into the database.
     * @param array $developers each item is an array with the name and the
     *                          email of the developer
     * @see raBaseDescriptor::saveAuthorsContributors
     * @see raBaseDescriptor::$authors
     * @see raBaseDescriptor::$contributors
     * @return array list of ids of given developers
     */
    private function _registerAuthors(& $developers){
        $authorId = array();
        $dao = jDao::get('rarangi~authors');
        $pId = $this->project->id();
        foreach ($developers as $author) {
            list($name, $email) = $author;

            // first try if we found the name
            $dev = $dao->getByName($name, $pId);
            if ($dev) {
                // name found
                if ($dev->email == '' && $email != '') {
                    $dev->email = $email;
                    $dao->update($dev);
                }
            }
            else {
                // name not found
                $dev = $dao->getByEmail($email, $pId);
                if ($dev && $name != '') {
                    if ($dev->name == $dev->email) {
                        $dev->name = $name;
                        $dao->update($dev);
                    }
                    else
                        $dev = null;
                }
            }

            if (!$dev) {
                $dev = jDao::createRecord('rarangi~authors');
                $dev->name = $name;
                $dev->email = $email;
                $dev->project_id = $pId;
                $dao->insert($dev);
            }
            $authorId[] = $dev->id;
        }
        return $authorId;
    }

    protected static $KNOWN_CLASS_PACKAGES = array(
        'PHP'=> array('stdClass', 'Exception', 'ErrorException', 'Closure',
                'DateTime', 'DateTimeZone', 'DateInterval', 'DatePeriod',
                'finfo', '__PHP_Incomplete_Class', 'php_user_filter', 'Directory', 'PharException',
                'Phar','PharData', 'PharFileInfo', 'SimpleXMLElement', 'XMLReader', 'XMLWriter', 'ZipArchive',
        ),
        'PHP:SPL' => array(
                'RecursiveIteratorIterator', 'IteratorIterator', 'FilterIterator', 'RecursiveFilterIterator', 'ParentIterator',
                'LimitIterator', 'CachingIterator', 'RecursiveCachingIterator', 'NoRewindIterator', 'AppendIterator', 'InfiniteIterator',
                'RegexIterator', 'RecursiveRegexIterator', 'EmptyIterator', 'RecursiveTreeIterator', 'DirectoryIterator', 'FilesystemIterator',
                'RecursiveDirectoryIterator', 'GlobIterator', 'SimpleXMLIterator', 'MultipleIterator', 'ArrayIterator', 'RecursiveArrayIterator',
                'LogicException', 'BadFunctionCallException', 'BadMethodCallException', 'DomainException', 'InvalidArgumentException',
                'LengthException', 'OutOfRangeException', 'RuntimeException', 'OutOfBoundsException', 'OverflowException',
                'RangeException',  'UnderflowException', 'UnexpectedValueException','ArrayObject'
        )
    );

    protected function guessClassPackage($className) {
        foreach(self::$KNOWN_CLASS_PACKAGES as $pack=>$list) {
            if (in_array($className, $list))
                return $pack;
        }
        if (substr($className, 0, 3) == 'DOM')
            return 'PHP:DOM';
        if (substr($className, 0, 3) == 'PDO')
            return 'PHP:PDO';
        if (substr($className, 0, 3) == 'Spl')
            return 'PHP:SPL';
        if (substr($className, 0, 10) == 'Reflection')
            return 'PHP:Reflection';
        if (substr($className, 0, 4) == 'Phar')
            return 'PHP:Phar';
        if (substr($className, 0, 4) == 'Soap')
            return 'PHP:Soap';
        return self::UNKNOWN_PACKAGE;
    }

    protected static $KNOWN_IFACE_PACKAGES = array(
        'PHP'=> array('Traversable','IteratorAggregate','Iterator','ArrayAccess','Serializable'
        ),
        'PHP:SPL' => array(
                'Countable', 'OuterIterator','RecursiveIterator','SeekableIterator',
                'SplObserver', 'SplSubject'
        )
    );

    protected function guessInterfacePackage($className) {
        foreach(self::$KNOWN_IFACE_PACKAGES as $pack=>$list) {
            if (in_array($className, $list))
                return $pack;
        }
        
        if ($className == 'Reflector')
            return 'PHP:Reflection';
        return self::UNKNOWN_PACKAGE;
    }

    protected function guessFunctionPackage($fctName) {
        $list = get_defined_functions();
        if (in_array($fctName, $list['user']))
            return self::UNKNOWN_PACKAGE;
        else
            return 'PHP';
    }
}
