<?php
/**
* @package     jDoc
* @subpackage  tests
* @author      Laurent Jouanneau
* @contributor
* @copyright   2008 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

require_once( dirname(__FILE__).'/../classes/jDescriptor.class.php');


class ut_doccomment extends jUnitTestCase {

    function testDescription() {
        $desc = new jBaseDescriptor();
        $c = '/**
*/';
        $desc->initFromPhpDoc($c);
    
        $this->assertEqual($desc->shortDescription,'');
        $this->assertEqual($desc->description,'');
        
        $desc = new jBaseDescriptor();
        $c =
        '/**
          * lorem ipsum
          */';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,'lorem ipsum');
        $this->assertEqual($desc->description,'');

        $desc = new jBaseDescriptor();
        $c =
        '/**
          * lorem ipsum
          * qsdosdpqosi
          */';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,"lorem ipsum\nqsdosdpqosi");
        $this->assertEqual($desc->description,'');
        
        $desc = new jBaseDescriptor();
        $c =
        '/**
          * lorem ipsum
          *
          * qsdosdpqosi
          */';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,"lorem ipsum");
        $this->assertEqual($desc->description,'qsdosdpqosi');

        $desc = new jBaseDescriptor();
        $c =
        '/**
          * lorem ipsum2
          *
          * foo
          *   bar
          */';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,"lorem ipsum2");
        $this->assertEqual($desc->description,"foo\nbar");
    }


}
?>