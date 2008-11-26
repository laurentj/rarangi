<?php
/**
* @package     jDoc
* @subpackage  tests
* @author      Laurent Jouanneau
* @contributor
* @copyright   2007-2008 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

require_once( dirname(__FILE__).'/../classes/jDocConfig.class.php');
require_once( dirname(__FILE__).'/../classes/jDoc.class.php');
require_once( dirname(__FILE__).'/../classes/parsers/jParser_base.class.php');


class dummyParser extends jParser_base {

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


class ut_parser_base extends jUnitTestCase {
    
    function testNextPhpSimple() {
        $content = ' <?php $a=     2; ?>';
        $tokens = new ArrayObject(token_get_all($content));
        $tokeniter = $tokens->getIterator();

        //$this->sendMessage("tok=". token_name(305));
        $parser = new dummyParser($tokeniter);
        
        $parser->toNextPhpSection2();
        $tok = $parser->toNextPhpToken2();
        if(is_array($tok) && count($tok)>2) {
            $data = array(
                array(T_VARIABLE, '$a',1),
                array(T_LNUMBER, '2',1),
            );
        }
        else {
            $data = array(
                array(T_VARIABLE, '$a'),
                array(T_LNUMBER, '2'),
            );
        }
        $this->assertIdentical($tok , $data[0]);
        
        $tok = $parser->toNextPhpToken2();
        $this->assertIdentical($tok , '=');
        
        $tok = $parser->toNextPhpToken2();
        $this->assertIdentical($tok , $data[1]);

        $tok = $parser->toNextPhpToken2();
        $this->assertIdentical($tok , ';');
        
        $tok = $parser->toNextPhpToken2();
        $this->assertFalse($tok);
        

    }

    function testNextPhp2() {
        $content = ' foo bar <?php $a=     2; ?> <a href=""> </a> <?php public function aaa() ?> oooo ';
        $tokens = new ArrayObject(token_get_all($content));
        $tokeniter = $tokens->getIterator();

        $parser = new dummyParser($tokeniter);
        
        $parser->toNextPhpSection2();
        $tok = $tokeniter->current();
        if(is_array($tok) && count($tok)>2) {
            $data = array(
                array(T_OPEN_TAG, '<?php ',1),
                array(T_VARIABLE, '$a',1),
                array(T_LNUMBER, '2',1),
                array(T_PUBLIC, 'public',1),
                array(T_FUNCTION, 'function',1),
                array(T_STRING, 'aaa',1)
            );
        }
        else {
            $data = array(
                array(T_OPEN_TAG, '<?php '),
                array(T_VARIABLE, '$a'),
                array(T_LNUMBER, '2'),
                array(T_PUBLIC, 'public'),
                array(T_FUNCTION, 'function'),
                array(T_STRING, 'aaa')
            );
        }
        
        $this->assertIdentical($tok , $data[0]);

        $tok = $parser->toNextPhpToken2();
        $this->assertIdentical($tok , $data[1]);
        
        $tok = $parser->toNextPhpToken2();
        $this->assertIdentical($tok , '=');
        
        $tok = $parser->toNextPhpToken2();
        $this->assertIdentical($tok , $data[2]);

        $tok = $parser->toNextPhpToken2();
        $this->assertIdentical($tok , ';');
        
        $tok = $parser->toNextPhpToken2();
        $this->assertIdentical($tok ,  $data[3]);

        $tok = $parser->toNextPhpToken2();
        $this->assertIdentical($tok ,  $data[4]);

        $tok = $parser->toNextPhpToken2();
        $this->assertIdentical($tok ,  $data[5]);

        $tok = $parser->toNextPhpToken2();
        $this->assertIdentical($tok ,  '(');

        $tok = $parser->toNextPhpToken2();
        $this->assertIdentical($tok ,  ')');

        $tok = $parser->toNextPhpToken2();
        $this->assertFalse($tok);
    }

}
?>