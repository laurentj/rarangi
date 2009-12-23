<?php
header('Content-type:text/plain');



function stringify($s){
	return '"'.str_replace(array("\n","\t"), array('\n','\t'),$s).'"';
}

function show($src) {
	
	$t= token_get_all($src);
	echo "\n\n------------------------------------\n";
	foreach($t as $tok) {
		if(is_array($tok)) {
			echo token_name($tok[0])." :\t".stringify($tok[1])."\n";
		}
		else
			echo "string :\t".stringify($tok)."\n";
	}

}


show ('<?php /**
 */

?>

<html xml:lang="<?php echo $check->messages->getLang(); ?>">
<head>');


show('
<?php

namespace MyProject;

const CONNECT_OK = 1;
class Connection { /* ... */ }
function connect() { /* ... */  }

namespace AnotherProject {

const CONNECT_OK = 1;
class Connection { /* ... */ }
function connect() { /* ... */  }

}

?>');

show(
'<?php
namespace Foo\Bar;
include \'file1.php\';

const FOO = 2;
function foo() {}
class foo
{
    static function staticmethod() {}
}

foo();
foo::staticmethod();
echo FOO; 

subnamespace\foo(); 
subnamespace\foo::staticmethod(); 
echo subnamespace\FOO;
                                  
\Foo\Bar\foo(); 
\Foo\Bar\foo::staticmethod(); 
echo \Foo\Bar\FOO; 
'
);




?>