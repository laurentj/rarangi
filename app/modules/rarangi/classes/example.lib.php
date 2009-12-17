<?php
/**
* This file contains many examples of the use of all tags supported by Rarangi.
*
* In this file, we try to use ALL tags in all situations, so we can see how
* the web interface or the static files generators display them. Of course this
* file is not used for unit tests.
* @package     rarangi
* @subpackage  example
* @author      Laurent Jouanneau
* @contributor John Smith
* @copyright   2009 Laurent Jouanneau
* @copyright   2009 John Smith
* @link        http://forge.jelix.org/projects/rarangi
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

/**
 * 
 * Lorem ipsum dolor sit amet
 *
 * consectetur adipiscing elit. Nulla sit amet bibendum sapien. Fusce
 * eleifend scelerisque ante, et cursus metus faucibus vel. Sed tempor,
 * est quis sagittis ornare, massa nulla molestie erat, vel dictum odio
 * urna at nunc. Mauris laoreet tincidunt elit ut consectetur.
 * 
 */
interface IExample1 {
    
    /**
     * a sample function
     * @return string
     */
    function getSomething();
}


/**
 * 
 * Nulla sit amet bibendum sapien.
 */
interface IExample2 {
    
    /**
     * a sample function
     * @param string $value the value to set
     */
    function setSomething($value);
}

/**
 * a short class
 */
class exampleShortClass {
    
    public $bling;
    
}

/**
 * Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * 
 * Nulla sit amet bibendum sapien. Fusce eleifend scelerisque ante,
 * et cursus metus faucibus vel. Sed tempor, est quis sagittis ornare,
 * massa nulla molestie erat, vel dictum odio urna at nunc. Mauris
 * laoreet tincidunt elit ut consectetur.
 *
 * @internal Aenean in eros velit, id ultrices risus. Ut malesuada
 * aliquam sem non vulputate. Nulla et quam massa. Aliquam erat volutpat.
 * Curabitur gravida leo quis odio accumsan imperdiet. Curabitur bibendum
 * tellus eu dui tristique sed posuere nunc tincidunt. Donec porttitor
 * felis nec est vestibulum euismod.
 * @contributor Jammie Clark
 * @since 1.0
 * @link http://jelix.org the web site
 * @link http://rarangi.org
 * @see otherClass
 * @see ArrayObject
 * @changelog version 0.8: Aenean mollis gravida semper. Phasellus
 * varius posuere leo sed laoreet.
 * @changelog 0.9: Nam eget neque tortor, ac semper ante.
 * @todo we should add more methods..
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public Licence
 */
class exampleClass1 extends exampleShortClass implements IExample1, IExample2 {

    /**
     * @ignore
     */
    public $ignoredProperty;
    
    /**
     * @ignore
     * @return string something
     */
    public function ignoredFunction() {
        return 'something';
    }

    /**
     * In sodales hendrerit est sit amet sollicitudin.
     * @var array  array of strings
     * @deprecated
     */
    protected $deprecatedVar = array();
    
    /**
     * Nam rutrum orci eget eros mattis
     *
     * Nulla rutrum lacinia massa at placerat.
     * Nulla volutpat lacinia ipsum, quis dictum
     * neque accumsan quis. Ut auctor pulvinar quam,
     * a tempus ante mollis sed.
     * @deprecated
     * @param ArrayObject $ar the array to process
     * @param boolean $reverse true if it is in the reverse order
     * @return boolean true if success
     * @contributor Johnnie Halité
     * @since 1.2
     * @internal Praesent lobortis, leo non sodales pharetra,
     * magna eros tempor augue, vel mollis purus velit in eros.
     * Praesent blandit dui non dolor placerat ut vehicula
     * tortor imperdiet.
     * @link http://jelix.org the web site
     * @link http://rarangi.org
     * @see otherClass
     * @see ArrayObject
     * @changelog version 0.8: Aenean mollis gravida semper. Phasellus
     * varius posuere leo sed laoreet.
     * @changelog 0.9: Nam eget neque tortor, ac semper ante.
     * @todo no more things to do on this method..
     */
    public function deprecatedFunction(ArrayObject $ar, $reverse) {
        return true;
    }
    
    /**
     * constant for the status property
     * @const integer #STATUS_OK  when the result is ok
     * @const integer #STATUS_FAILED  when the process has failed
     * @since 0.8
     */
    const STATUS_OK = 1, STATUS_FAILED = 0;

    /**
     * the constructor. initialize the process.
     * Nulla et quam massa. 
     *
     * Sed consequat pulvinar sapien. Ut quis urna id enim vehicula sagittis
     * sed a lorem. Integer faucibus luctus pulvinar.
     */
    function __construct() {

    }

    /**
     * return the parameter used for the process
     * @return string
     */
    function getSomething() {
        
    }

    /**
     * set a parameter for the process
     * @param string $value the value to set
     */
    function setSomething($value) {
        
    }
}



/**
 * Nam accumsan laoreet nunc ac pretium.
 * 
 * Etiam varius volutpat lacus. Cras at risus vitae sapien dictum bibendum at
 * vel nisl. Duis congue imperdiet ipsum. Praesent et est magna. Pellentesque
 * elementum ipsum sagittis odio varius vulputate. Nullam nibh dui, elementum
 * hendrerit tincidunt quis, lacinia eget nisl.
 *
 * @internal Aenean in eros velit, id ultrices risus. Ut malesuada
 * aliquam sem non vulputate. Nulla et quam massa. Aliquam erat volutpat.
 * Curabitur gravida leo quis odio accumsan imperdiet. Curabitur bibendum
 * tellus eu dui tristique sed posuere nunc tincidunt. Donec porttitor
 * felis nec est vestibulum euismod.
 * @contributor Jammie Clark
 * @since 1.0
 * @link http://jelix.org the web site
 * @link http://rarangi.org
 * @see otherClass
 * @see ArrayObject
 * @changelog version 0.8: Aenean mollis gravida semper. Phasellus
 * varius posuere leo sed laoreet.
 * @changelog 0.9: Nam eget neque tortor, ac semper ante.
 * @todo we should add more methods..
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public Licence
 * @param string $foo a first parameter
 * @param integer $bar a second parameter
 * @param IExample1 $baz a third parameter
 * @return string the generated content
 */
function exampleFunction($foo, $bar, IExample1 $baz) {
    return "bouh !";
}

