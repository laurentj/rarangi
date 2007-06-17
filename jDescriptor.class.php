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
abstract class jBaseDescriptor {
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
     * the object is initialize with all informations of an other 
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
        $docComment = substr($docComment , 2, -1); // on enlève les /* du debut et / de la fin
        $lignes=preg_split("/\015\012|\015|\012/",$docComment);
        $currentTag='shortDescription';
        foreach($lignes as $ligne){
            if(preg_match('/^\\s*\\*\\s+(:@(\w+))?(.*)$/',$ligne,$m)){
                if($m[1] != ''){


                }else{

                }
            }else{
               //jLogger::warning('a line in a doc comment doesn\'t begin with a *');
            }
        }
    }
}





/**
 *
 */
class jClassDescriptor extends jBaseDescriptor {


}

/**
 *
 */
class jFileDescriptor extends jBaseDescriptor  {
    public $filepath;
    public $filename;
    function __construct($fp, $fn){
        $this->filepath = $fp;
        $this->filename = $fn;
    }
}

/**
 *
 */
class jFunctionDescriptor  extends jBaseDescriptor {


}




?>