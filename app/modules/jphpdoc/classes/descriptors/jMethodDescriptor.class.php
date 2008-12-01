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
class jMethodDescriptor  extends jBaseDescriptor {
    public $usedGlobalsVars;
    public $parameters;
    public $return;
    public $staticVars;
    
    public $accessibility;
    public $isStatic;
    public $isFinal;
    public $isAbstract;

}

