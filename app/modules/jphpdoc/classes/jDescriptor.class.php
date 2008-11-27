<?php
/**
* @package     jDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2007 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
/*


support des mots clés


communs (utilisable dans toutes les sections, et si present pour fichier, sont hérités dans les def du fichiers)
    author       name <email>
    contributor  name <email>
    copyright   info
    deprecated  sinceversion  info
    example     filename (inclus exemple indiqué dans le fichier)
    ignore      (ne documente pas le truc)
    internal    documentation interne
    internaluse ne documenter que dans la doc dev
    link     lien libelle
    see   file.ext|elementname|class::methodname()|class::$variablename|functionname()|function functionname
    uses  =see avec lien de retour
    since   version
    changelog version description
    todo   description
    experimental


fichier
    licence  lien libelle
    package       (classes, fonctions, variables en herite si non present à leur niveau)
    subpackage    (classes, fonctions, variables en herite si non present à leur niveau)

classes
    package
    subpackage

fonctions
    global  datatype $varname
    package
    subpackage
    param  datatype $param description
    return datatype description
    staticvar   datatype $name variable statique utilisée à l'interieur de la fonction

methodes
    global  datatype $varname
    param  datatype $param description
    return datatype description
    staticvar   datatype $name variable statique utilisée à l'interieur de la fonction

propriete
    var  datatype description

variable globale
    package
    subpackage
    global  datatype description

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


    /**
     * the object is initialized with all informations of an other 
     * @param jBaseDescriptor $desc a descriptor
     */
    public function inheritFrom($desc){
        $this->shortDescription = $desc->shortDescription ;
        $this->description = $desc->description ;
        $this->author = $desc->author ;
        $this->contributor = $desc->contributor ;
        $this->copyright = $desc->copyright ;
        $this->deprecated = $desc->deprecated ;
        $this->example = $desc->example ;
        $this->ignore = $desc->ignore ;
        $this->internal = $desc->internal ;
        $this->internaluse = $desc->internaluse ;
        $this->link = $desc->link ;
        $this->see = $desc->see ;
        $this->uses = $desc->uses ;
        $this->since = $desc->since ;
        $this->changelog = $desc->changelog ;
        $this->todo = $desc->todo ;
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
}



/**
 *
 */
class jFileDescriptor extends jBaseDescriptor  {
    public $fullpath;
    public $filepath;
    public $filename;
    public $id;
    public $licence; // libelle, lien
    public $package;
    public $subpackage;
    
    protected $record;
    
    function __construct($projectId, $fullSourcePath, $fullpath, $filename){
        
        $relativeFullPath = substr($fullpath, strlen($fullSourcePath)+1);
        $relativePath = substr(dirname($fullpath), strlen($fullSourcePath)+1);

        $this->filepath = $relativeFullPath;
        $this->filename = $filename;
        $this->fullpath = $fullpath;
        
        $this->record = jDao::createRecord('jphpdoc~files');
        $this->record->project_id = $projectId;
        $this->record->isdir = 0;
        $this->record->fullpath = $relativeFullPath;
        $this->record->filename = $filename;
        $this->record->dirname = $relativePath;
        jDao::get('jphpdoc~files')->insert($this->record);
        $this->id = $this->record->id;
    }
    public function save() {
        jDao::get('jphpdoc~files')->update($this->record);
    }
}


/**
 *
 */
class jClassDescriptor extends jBaseDescriptor {
    public $package;
    public $subpackage;
    public $name;
    public $inheritsFrom = null;
    public $interfaces = array();
    
    
    public $projectId;
    public $fileId;
    public $line;
    
    function __construct($projectId, $fileId, $line){
        $this->projectId = $projectId;
        $this->fileId = $fileId;
        $this->line = $line;
    }

    public function save() {
        if($this->name == '')
            throw new Exception('class name undefined');
        
        $dao = jDao::get('jphpdoc~classes');

        $record = jDao::createRecord('jphpdoc~classes');
        $record->name = $this->name;
        $record->project_id = $this->projectId;
        $record->file_id = $this->fileId;
        $record->linenumber = $this->line;
        $record->mother_class = $this->inheritsFrom;
        
        if($dao->get($this->name, $this->projectId)) {
            // if there is already a record, this is an empty record, created
            // during the parsing of an other class which inherits from the current class
            // so we can update all fields
            $dao->update($record);
        }
        else {
            $dao->insert($record);
        }  
    }
}

/**
 *
 */
class jInterfaceDescriptor extends jBaseDescriptor {
    public $package;
    public $subpackage;
    public $name;
    public $inheritsFrom;
}



/**
 *
 */
class jPropertyDescriptor extends jBaseDescriptor {
    public $datatype;

}

/**
 *
 */
class jMethodDescriptor  extends jBaseDescriptor {
    public $usedGlobalsVars;
    public $parameters;
    public $return;
    public $staticVars;
}


/**
 *
 */
class jFunctionDescriptor  extends jBaseDescriptor {
    public $package;
    public $subpackage;
    public $usedGlobalsVars;
    public $parameters;
    public $return;
    public $staticVars;
}


/**
 *
 */
class jGlobalVariableDescriptor  extends jBaseDescriptor {
    public $package;
    public $subpackage;
    public $datatype;
}

/**
 *
 */
class jConstantDescriptor  extends jBaseDescriptor {
    public $package;
    public $subpackage;
    public $value;
}




?>