<?php
/**
* @package     jPhpDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/jphpdoc
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

/**
 * 
 */
class jBaseDescriptor {
    /**
     *
     * @var string
     */
    public $shortDescription;

    /**
     *
     * @var string 
     */
    public $description;

    /**
     *
     * @var string 
     */
    public $package;
    
    /**
     *
     * @var string 
     */
    public $subpackage;

    /**
     * list of array(name,email)
     * @var array
     */
    public $author;    //  name <email>

    /**
     * list of array(name,email)
     * @var arrayt
     */
    public $contributor;    //  name <email>

    /**
     *
     * @var string
     */
    public $copyright;    //   info
    /**
     * 
     * @var 
     */
    public $deprecated;    //  sinceversion  info
    /**
     *
     * @var 
     */
    public $example;    //     filename (inclus exemple indiqué dans le fichier)
    /**
     *
     * @var 
     */
    public $ignore;    //      (ne documente pas le truc)
    /**
     *
     * @var 
     */
    public $internal;    //    documentation interne
    /**
     *
     * @var 
     */
    public $internaluse;    // ne documenter que dans la doc dev
    /**
     *
     * @var 
     */
    public $link;    //     lien libelle
    /**
     *
     * @var 
     */
    public $see;    //   file.ext|elementname|class::methodname()|class::$variablename|functionname()|function functionname
    /**
     *
     * @var 
     */
    public $uses;    //  =see avec lien de retour
    /**
     *
     * @var 
     */
    public $since;    //   version
    /**
     *
     * @var 
     */
    public $changelog;    //  //version description
    /**
     *
     * @var 
     */
    public $todo;    //description

    public $projectId;
    public $fileId;
    public $line;
    
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
        $this->subpackage = $desc->subpackage ;
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
                if($tag != ''){
                    switch($tag) {
                        case 'package':
                            $this->package = trim($content);
                            break;
                        case 'subpackage':
                            $this->subpackage = trim($content);
                            break;
                    }
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
    
    public function save() {}
    
    
    protected function getPackageId($packageName, $isSubPackage = false) {
        if($packageName == '')
            return null;
        $package = jDao::get('jphpdoc~packages')->getByName($this->projectId, $packageName, $isSubPackage);
        if(!$package) {
            $package = jDao::createRecord('jphpdoc~packages');
            $package->project_id = $this->projectId;
            $package->name = $packageName;
            $package->is_sub = $isSubPackage;
            jDao::get('jphpdoc~packages')->insert($package);
        }
        return $package->id;
    }
}


