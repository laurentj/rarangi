<?php
/**
* @package     rarangi
* @subpackage  tests
* @author      Laurent Jouanneau
* @contributor
* @copyright   2008 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

require_once( dirname(__FILE__).'/../classes/raDescriptor.lib.php');


class ut_doccomment extends jUnitTestCase {

    function testDescription() {
        $desc = new raBaseDescriptor(1, 1, 1);
        $c = '/**
*/';
        $desc->initFromPhpDoc($c);
    
        $this->assertEqual($desc->shortDescription,'');
        $this->assertEqual($desc->description,'');
        
        $desc = new raBaseDescriptor(1, 1, 1);
        $c =
        '/**
          * lorem ipsum
          */';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,'lorem ipsum');
        $this->assertEqual($desc->description,'');

        $desc = new raBaseDescriptor(1, 1, 1);
        $c =
        '/**
          * lorem ipsum
          * qsdosdpqosi
          */';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,"lorem ipsum\nqsdosdpqosi");
        $this->assertEqual($desc->description,'');
        
        $desc = new raBaseDescriptor(1, 1, 1);
        $c =
        '/**
          * lorem ipsum
          *
          * qsdosdpqosi
          */';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,"lorem ipsum");
        $this->assertEqual($desc->description,'qsdosdpqosi');

        $desc = new raBaseDescriptor(1, 1, 1);
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

    function testPackage(){
        $desc = new raBaseDescriptor(1, 1, 1);
        $c = '/**
* @package jDoc
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,'');
        $this->assertEqual($desc->description,'');
        $this->assertEqual($desc->package,'jDoc');

        $c = '/**
* @package jDoc machin bidule
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,'');
        $this->assertEqual($desc->description,'');
        $this->assertEqual($desc->package,'jDoc machin bidule');
        
        $c = '/**
* @package jDoc
* @subpackage parser
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,'');
        $this->assertEqual($desc->description,'');
        $this->assertEqual($desc->package,'jDoc.parser');

        $c = '/**
* lorem ipsum
* @package jDoc
* @subpackage parser
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,'lorem ipsum');
        $this->assertEqual($desc->description,'');
        $this->assertEqual($desc->package,'jDoc.parser');
    }

}
?>