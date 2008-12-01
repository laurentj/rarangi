<?php
/**
* @package     jPhpDoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/jphpdoc
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

$dirname = dirname(__FILE__).'/descriptors/';
require($dirname.'jBaseDescriptor.class.php');
require($dirname.'jFileDescriptor.class.php');
require($dirname.'jInterfaceDescriptor.class.php');
require($dirname.'jClassDescriptor.class.php');
require($dirname.'jPropertyDescriptor.class.php');
require($dirname.'jMethodDescriptor.class.php');
require($dirname.'jFunctionDescriptor.class.php');
require($dirname.'jGlobalVariableDescriptor.class.php');
require($dirname.'jConstantDescriptor.class.php');
