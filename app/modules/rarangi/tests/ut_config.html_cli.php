<?php
/**
* @package     rarangi
* @subpackage  tests
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007-2008 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

require_once( dirname(__FILE__).'/../classes/raDocGenerator.class.php');

class docConfigTest extends raDocGenerator {

    function resetConfig() {
        $this->config = array(
            'excludedFiles' => array(),
            'excludedFilesReg' => array()
        );
    }

    function getExcludedFiles () { return $this->config['excludedFiles']; }
    function getExcludedFilesReg () { return $this->config['excludedFilesReg']; }

    function convertConfig() {
        $this->config = (object) $this->config;
    }
}


class ut_config extends jUnitTestCase {

    function testEmptyConfig() {
        $doc = new docConfigTest();
        $doc->resetConfig();
        $doc->setExcludedFiles(array());
        $this->assertIdentical($doc->getExcludedFiles (), array());
        $this->assertIdentical($doc->getExcludedFilesReg (), array());
    }

    function testExcludedSimpleFile() {
        $doc = new docConfigTest();
        $doc->resetConfig();
        $doc->setExcludedFiles(array('CVS','.svn'));
        $this->assertIdentical($doc->getExcludedFiles (), array('CVS','.svn'));
        $this->assertIdentical($doc->getExcludedFilesReg (), array());
    }

    function testExcludedRegFile() {
        $doc = new docConfigTest();
        $doc->resetConfig();
        $doc->setExcludedFiles(array('*foo', '*.foo','bar.*', 'CVS', 'sv*n'));
        $this->assertIdentical($doc->getExcludedFiles (), array('CVS','sv*n'));
        $this->assertIdentical($doc->getExcludedFilesReg (), array('/.*foo$/', '/.*\.foo$/','/^bar\..*/'));
    }

    function testRightExcludedFile(){
        $doc = new docConfigTest();
        $doc->resetConfig();
        $doc->setExcludedFiles(array('*foo', '*.foo','bar.*', 'CVS', 'sv*n'));
        $doc->convertConfig();
        $this->assertTrue($doc->isExcludedFile('CVS'));
        $this->assertTrue($doc->isExcludedFile('foo'));
        $this->assertTrue($doc->isExcludedFile('truc.foo'));
        $this->assertTrue($doc->isExcludedFile('bar.truc'));
        $this->assertTrue($doc->isExcludedFile('aaaaafoo'));
        $this->assertTrue($doc->isExcludedFile('sv*n'));

    }

    function testWrongExcludedFile(){
        $doc = new docConfigTest();
        $doc->resetConfig();
        $doc->setExcludedFiles(array('*foo', '*.foo','bar.*', 'CVS', 'sv*n'));
        $doc->convertConfig();
        $this->assertFalse($doc->isExcludedFile('truc.txt'));
        $this->assertFalse($doc->isExcludedFile('foomachin'));
        $this->assertFalse($doc->isExcludedFile('CVS2'));
        $this->assertFalse($doc->isExcludedFile('.svn'));
        $this->assertFalse($doc->isExcludedFile('bartruc'));
        $this->assertFalse($doc->isExcludedFile('bar3.truc'));
        $this->assertFalse($doc->isExcludedFile('aaasv*n'));

    }

}
?>