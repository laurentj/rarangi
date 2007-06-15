<?php
/**
* @package     jDoc
* @author      Jouanneau Laurent
* @contributor
* @copyright   2006 Jouanneau laurent
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
/*


support des mots cl�s


communs (utilisable dans toutes les sections, et si present pour fichier, sont h�rit�s dans les def du fichiers
    author       name <email>
    contributor  name <email>
    copyright   info
    deprecated  sinceversion  info
    example     filename (inclus exemple indiqu� dans le fichier)
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
    package       (classes, fonctions, variables en herite si non present � leur niveau)
    subpackage    (classes, fonctions, variables en herite si non present � leur niveau)

classes
    package
    subpackage

fonctions
    global  datatype $varname
    package
    subpackage
    param  datatype $param description
    return datatype description
    staticvar   datatype $name variable statique utilis�e � l'interieur de la fonction

methodes
    global  datatype $varname
    param  datatype $param description
    return datatype description
    staticvar   datatype $name variable statique utilis�e � l'interieur de la fonction

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
abstract class jBaseInfo {
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
    public $example;    //     filename (inclus exemple indiqu� dans le fichier)
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


    public function initFromPhpDoc($docComment){
        $docComment = substr($docComment , 2, -1); // on enl�ve les /* du debut et / de la fin
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
class jClassInfo extends jBaseInfo {


}

/**
 *
 */
class jFileInfo extends jBaseInfo  {
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
class jFunctionInfo  extends jBaseInfo {


}




?>