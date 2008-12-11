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
    protected $logger;

    function setUp() {
        raLogger::removeLoggers();
        $this->logger = new raInMemoryLogger();
        raLogger::addLogger($this->logger);
    }

    function tearDown() {
        raLogger::removeLoggers();
    }

    function checkLogEmpty() {
        $log = $this->logger->getLog();
        $a = $this->assertEqual(count($log['error']),0);
        $b = $this->assertEqual(count($log['warning']),0);
        $c = $this->assertEqual(count($log['notice']),0);
        return $a && $b && $c;
    }

    function testDescription() {
        $desc = new raBaseDescriptor(1, 1, 1);
        $c = '/**
*/';
        $desc->initFromPhpDoc($c);
    
        $this->assertEqual($desc->shortDescription,'');
        $this->assertEqual($desc->description,'');
        $this->assertTrue($this->checkLogEmpty());
        
        $desc = new raBaseDescriptor(1, 1, 1);
        $c =
        '/**
          * lorem ipsum
          */';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,'lorem ipsum');
        $this->assertEqual($desc->description,'');
        $this->assertTrue($this->checkLogEmpty());

        $desc = new raBaseDescriptor(1, 1, 1);
        $c =
        '/**
          * lorem ipsum
          * qsdosdpqosi
          */';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,"lorem ipsum\nqsdosdpqosi");
        $this->assertEqual($desc->description,'');
        $this->assertTrue($this->checkLogEmpty());
        
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
        $this->assertTrue($this->checkLogEmpty());

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
        $this->assertTrue($this->checkLogEmpty());
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
        $this->assertTrue($this->checkLogEmpty());

        $c = '/**
* @package jDoc machin bidule
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,'');
        $this->assertEqual($desc->description,'');
        $this->assertEqual($desc->package,'jDoc machin bidule');
        $this->assertTrue($this->checkLogEmpty());
        
        $c = '/**
* @package jDoc
* @subpackage parser
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,'');
        $this->assertEqual($desc->description,'');
        $this->assertEqual($desc->package,'jDoc.parser');
        $this->assertTrue($this->checkLogEmpty());

        $c = '/**
* lorem ipsum
* @package jDoc
* @subpackage parser
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,'lorem ipsum');
        $this->assertEqual($desc->description,'');
        $this->assertEqual($desc->package,'jDoc.parser');
        $this->assertTrue($this->checkLogEmpty());
    }



    function testAuthor(){
        $desc = new raBaseDescriptor(1, 1, 1);
        $c = '/**
* @author laurent
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->authors,array(array('laurent','')));
        $this->assertTrue($this->checkLogEmpty());

        $desc->authors = array();
        $c = '/**
* @author laurent <toto@truc.local>
*/';
        $desc->initFromPhpDoc($c);
        if(!$this->assertEqual($desc->authors,array(array('laurent','toto@truc.local'))))
            $this->sendMessage("authors:".var_export($desc->authors, true));
        $this->assertTrue($this->checkLogEmpty());

        $desc->authors = array();
        $c = '/**
* @author laurent <toto2@truc.local>, loic
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->authors,array(array('laurent','toto2@truc.local'), array('loic','')));
        $this->assertTrue($this->checkLogEmpty());

        $desc->authors = array();
        $c = '/**
* @author laurent <toto3@truc.local>, <loic@bidule.local>
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->authors,array(
                        array('laurent','toto3@truc.local'),
                        array('','loic@bidule.local')));
        $this->assertTrue($this->checkLogEmpty());

        $desc->authors = array();
        $c = '/**
* @author laurent <toto4@truc.local>, ,   ,,  loic <loic2@bidule.local>
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->authors,array(
                        array('laurent','toto4@truc.local'),
                        array('loic','loic2@bidule.local')));
        $this->assertTrue($this->checkLogEmpty());

        $desc->authors = array();
        $c = '/**
* @author laurent <toto4@truc.local>, ,   ,,  loic <loic2@bidule.local>
* @author bastien, julien<julien@ohoh.local>
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->authors,array(
                        array('laurent','toto4@truc.local'),
                        array('loic','loic2@bidule.local'),
                        array('bastien',''),
                        array('julien','julien@ohoh.local'),
                        ));
        $this->assertTrue($this->checkLogEmpty());
        $desc->authors = array();

        $c = '/**
* @author laurent <toto4@truc.local>, ,   ,,  loic <loic2@bidule.local>
* @contributor bastien, julien<julien@ohoh.local>
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->authors,array(
                        array('laurent','toto4@truc.local'),
                        array('loic','loic2@bidule.local'),
                        ));
        $this->assertEqual($desc->contributors,array(
                        array('bastien',''),
                        array('julien','julien@ohoh.local'),
                        ));
        $this->assertTrue($this->checkLogEmpty());
    }
}
?>