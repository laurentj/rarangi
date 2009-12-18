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

require_once( dirname(__FILE__).'/parser_test.lib.php');

class ut_doccomment extends jUnitTestCase {
    protected $logger;

    function setUp() {

        $logger = new raLogger();
        $this->logger = new raInMemoryLogger();
        $logger->addLogger($this->logger);

        $this->project = new ut_project_test($logger);
    }

    function tearDown() {
    }

    function checkLogEmpty() {
        $log = $this->logger->getLog();
        $a = count($log['error']) == 0;
        $b = count($log['warning']) == 0;
        $c = count($log['notice']) == 0;
        if (!($a && $b && $c))
            $this->dump($log);
        return $a && $b && $c;
    }

    function testDescription() {
        $desc = new raBaseDescriptor($this->project, 1, 1);
        $c = '/**
*/';
        $desc->initFromPhpDoc($c);
    
        $this->assertEqual($desc->shortDescription,'');
        $this->assertEqual($desc->description,'');
        $this->assertTrue($this->checkLogEmpty());
        
        $desc = new raBaseDescriptor($this->project, 1, 1);
        $c =
        '/**
          * lorem ipsum
          */';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,'lorem ipsum');
        $this->assertEqual($desc->description,'');
        $this->assertTrue($this->checkLogEmpty());

        $desc = new raBaseDescriptor($this->project, 1, 1);
        $c =
        '/**
          * lorem ipsum
          * qsdosdpqosi
          */';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription,"lorem ipsum\nqsdosdpqosi");
        $this->assertEqual($desc->description,'');
        $this->assertTrue($this->checkLogEmpty());
        
        $desc = new raBaseDescriptor($this->project, 1, 1);
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

        $desc = new raBaseDescriptor($this->project, 1, 1);
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
        $desc = new raBaseDescriptor($this->project, 1, 1);
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
        $desc = new raBaseDescriptor($this->project, 1, 1);

        $this->logger->clear();
        $desc->authors = array();
        $c = '/**
* @author 
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual(count($desc->authors),0);
        $log = $this->logger->getLog();
        if(!$this->assertEqual(count($log['error']),0))
            $this->dump($log['error'],'raLogger::error');
        if(!$this->assertEqual(count($log['warning']),0))
            $this->dump($log['warning'],'raLogger::warning');
        if(!$this->assertEqual(count($log['notice']),1))
            $this->dump($log['notice'],'raLogger::notice');

        $this->logger->clear();
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
                        array('loic@bidule.local','loic@bidule.local')));
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

        $this->logger->clear();
        $desc->authors = array();
        $c = '/**
* @author laurent <toto4@truc.local> blalba
* @author julien <toto1@truc.local> (blalba)
* @author bastien (bug fixed)
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->authors,array(
                        array('laurent','toto4@truc.local'),
                        array('julien','toto1@truc.local'),
                        array('bastien','')
                        ));
        $log = $this->logger->getLog();
        if(!$this->assertEqual(count($log['error']),0))
            $this->dump($log['error'],'raLogger::error');
        if(!$this->assertEqual(count($log['warning']),0))
            $this->dump($log['warning'],'raLogger::warning');
        if(!$this->assertEqual(count($log['notice']),3))
            $this->dump($log['notice'],'raLogger::notice');

        $this->logger->clear();
        $desc->authors = array();
        $c = '/**
* @author laurent <toto4@truc.local
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->authors,array(
                        array('laurent','')
                        ));
        $log = $this->logger->getLog();
        if(!$this->assertEqual(count($log['error']),0))
            $this->dump($log['error'],'raLogger::error');
        if(!$this->assertEqual(count($log['warning']),1))
            $this->dump($log['warning'],'raLogger::warning');
        if(!$this->assertEqual(count($log['notice']),0))
            $this->dump($log['notice'],'raLogger::notice');

    }
    
    function testMethodDescriptor(){
        $desc = new raMethodDescriptor($this->project, 1, 1);
        $c = '/**
* lorem ipsum
*
* Praesent at ante. Maecenas condimentum congue sapien. In vehicula arcu dictum enim.
* Quisque mi. Nunc mauris. Suspendisse vitae quam quis odio semper scelerisque.
* @return void
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->shortDescription, 'lorem ipsum');
        $this->assertEqual($desc->description, "Praesent at ante. Maecenas condimentum congue sapien. In vehicula arcu dictum enim.\nQuisque mi. Nunc mauris. Suspendisse vitae quam quis odio semper scelerisque.");
        $this->assertEqual($desc->returnType, 'void');
        $this->assertEqual($desc->returnDescription, '');
        
        $desc = new raMethodDescriptor($this->project, 1, 1);
        $c = '/**
* lorem ipsum
*
* Praesent at ante. Maecenas condimentum congue sapien. In vehicula arcu dictum enim.
* @return string Praesent at ante. Maecenas condimentum
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->returnType, 'string');
        $this->assertEqual($desc->returnDescription, 'Praesent at ante. Maecenas condimentum');

        $desc = new raMethodDescriptor($this->project, 1, 1);
        $c = '/**
* lorem ipsum
*
* Praesent at ante. Maecenas condimentum congue sapien. In vehicula arcu dictum enim.
* @return string Praesent at ante. Maecenas condimentum
*   Quisque mi. Nunc mauris. Suspendisse vitae quam
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->returnType, 'string');
        $this->assertEqual($desc->returnDescription, "Praesent at ante. Maecenas condimentum\nQuisque mi. Nunc mauris. Suspendisse vitae quam");
    }

    function testLink(){
        $desc = new raBaseDescriptor($this->project, 1, 1);

        $this->logger->clear();
        $desc->links = array();
        $c = '/**
* @link 
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual(count($desc->links),0);
        $log = $this->logger->getLog();
        if(!$this->assertEqual(count($log['error']),0))
            $this->dump($log['error'],'raLogger::error');
        if(!$this->assertEqual(count($log['warning']),0))
            $this->dump($log['warning'],'raLogger::warning');
        if(!$this->assertEqual(count($log['notice']),1))
            $this->dump($log['notice'],'raLogger::notice');

        $this->logger->clear();
        $desc->links = array();
        $c = '/**
* @link hello
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->links, array(array('','hello')));
        $this->assertTrue($this->checkLogEmpty());

        $this->logger->clear();
        $desc->links = array();
        $c = '/**
* @link hello world
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->links, array(array('','hello world')));
        $this->assertTrue($this->checkLogEmpty());

        $this->logger->clear();
        $desc->links = array();
        $c = '/**
* @link http://foo.local hello world
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->links, array(array('http://foo.local','hello world')));
        $this->assertTrue($this->checkLogEmpty());

        $this->logger->clear();
        $desc->links = array();
        $c = '/**
* @link http://foo.local
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->links, array(array('http://foo.local','')));
        $this->assertTrue($this->checkLogEmpty());
    }
    
    function testUserTag(){
        $desc = new raBaseDescriptor($this->project, 1, 1);

        $this->logger->clear();
        $desc->userTags = array();
        $c = '/**
* @mytag
* @othertag bla bla bla
*     lorem ipsum
* @onetag hello world
*/';
        $desc->initFromPhpDoc($c);
        $this->assertEqual($desc->userTags,array(
            'mytag'=>'',
            'othertag'=>'bla bla bla
lorem ipsum',
            'onetag'=>'hello world'
        ));
        $this->assertTrue($this->checkLogEmpty());

    }

    function testDeprecated(){
        $desc = new raBaseDescriptor($this->project, 1, 1);

        $this->logger->clear();
        $desc->isDeprecated = false;
        $desc->deprecated = '';
        $c = '/**
* @deprecated
*/';
        $desc->initFromPhpDoc($c);
        $this->assertTrue($desc->isDeprecated);
        $this->assertEqual($desc->deprecated, '');
        $this->assertTrue($this->checkLogEmpty());

        $this->logger->clear();
        $desc->isDeprecated = false;
        $desc->deprecated = '';
        $c = '/**
* @deprecated hello world
*/';
        $desc->initFromPhpDoc($c);
        $this->assertTrue($desc->isDeprecated);
        $this->assertEqual($desc->deprecated, 'hello world');
        $this->assertTrue($this->checkLogEmpty());
    }

    function testExperimental(){
        $desc = new raBaseDescriptor($this->project, 1, 1);

        $this->logger->clear();
        $desc->experimental = false;
        $c = '/**
* @experimental
*/';
        $desc->initFromPhpDoc($c);
        $this->assertTrue($desc->experimental);
        $this->assertTrue($this->checkLogEmpty());
    }

}
?>