<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2006-2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi
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


classes
    package
    @method int borp() borp(int $int1, int $int2) multiply two integers    <--- magic methods (__call)
	@property mixed $regular regular read/write property <-- magic property accessible by __set, __get
	@property-read int $foo the foo prop  <-- magic property accessible only by __get
	@property-write string $bar the bar prop  <-- magic property accessible only by __set

fonctions
    global  datatype $varname
    package
    param  datatype|datatype $param description
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
    name   indique le vrai nom de la variable globale (ou alors tenter de parser $_GLOBALS['']...)
    package
    global  datatype description

*/

$dirname = dirname(__FILE__).'/descriptors/';
require($dirname.'raBaseDescriptor.class.php');
require($dirname.'raGlobalVariableDescriptor.class.php');
require($dirname.'raFileDescriptor.class.php');
require($dirname.'raInterfaceDescriptor.class.php');
require($dirname.'raClassDescriptor.class.php');
require($dirname.'raPropertyDescriptor.class.php');
require($dirname.'raMethodDescriptor.class.php');
require($dirname.'raFunctionDescriptor.class.php');
require($dirname.'raConstantDescriptor.class.php');
