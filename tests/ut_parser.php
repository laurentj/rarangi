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

include( '../jDoc.class.php');
include( '../jParser.class.php');


class dummyParser extends jBaseParser {

    public function parse(){}

    public  function toNextPhpSection2(){
       return $this->toNextPhpSection();
    }
    
    public function toNextPhpToken2(){
        return $this->toNextPhpToken();
    }
    
    public function getIterator(){
        return  $this->iterator;
    }
    
}


class ut_parser extends jUnitTestCase {
    
    function testNextPhpSimple() {
        $content = ' <?php $a=     2; ?>';
        $tokens = new ArrayObject(token_get_all($content));
        $tokeniter = $tokens->getIterator();

        //$this->sendMessage("tok=". token_name(305));
        $parser = new dummyParser($tokeniter);
        
        $parser->toNextPhpSection2();
        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() , array(T_VARIABLE, '$a'));
        
        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() , '=');
        
        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() , array(T_LNUMBER, '2'));

        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() , ';');
        
        $parser->toNextPhpToken2();
        $this->assertFalse($tokeniter->valid());
        

    }

    function testNextPhp2() {
        $content = ' foo bar <?php $a=     2; ?> <a href=""> </a> <?php public function aaa() ?> oooo ';
        $tokens = new ArrayObject(token_get_all($content));
        $tokeniter = $tokens->getIterator();

        //$this->sendMessage("tok=". token_name(305));
        $parser = new dummyParser($tokeniter);
        
        $parser->toNextPhpSection2();
        $this->assertIdentical($tokeniter->current() , array(T_OPEN_TAG, '<?php '));

        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() , array(T_VARIABLE, '$a'));
        
        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() , '=');
        
        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() , array(T_LNUMBER, '2'));

        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() , ';');
        
        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() ,  array(T_PUBLIC, 'public'));

        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() ,  array(T_FUNCTION, 'function'));

        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() ,  array(T_STRING, 'aaa'));

        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() ,  '(');

        $parser->toNextPhpToken2();
        $this->assertIdentical($tokeniter->current() ,  ')');

        $parser->toNextPhpToken2();
        $this->assertFalse($tokeniter->valid());
    }



}
?>