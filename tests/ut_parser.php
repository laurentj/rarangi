<?php
/**
* @package     jDoc
* @subpackage  tests
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

include( '../jParser.class.php');


class dummyParser extends jBaseParser {

    public  function toNextPhp2(){
       return $this->toNextPhp();
    }
    
    public function skipBlank2(){
        return $this->skipBlank();
    }
    
    public function getIterator(){
        return  $this->iterator;
    }
    
}


class ut_parser extends UnitTestCase {
    
    function testSimpleNextPhp() {
        $content = ' <?php $a=     2; ?>';
        $tokens = new ArrayObject(token_get_all($content));
        $tokiter = $tokens->getIterator();
        
        $parser = new dummyParser($tokeniter);
        
        $parser->toNextPhp();
        $this->assertIdentical($this->iterator->current() , array(T_VARIABLE, '$a'));
        
        $parser->skipBlank();
        $this->assertIdentical($this->iterator->current() , '=');
        
        $parser->skipBlank();
        $this->assertIdentical($this->iterator->current() , '2');
        
        $this->pass('ok');
    }
}
?>