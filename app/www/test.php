<?php
header('Content-type:text/plain');


$t= token_get_all("
<?php

class truc implements machin, bidule {
	
}
");

function stringify($s){
	return '"'.str_replace(array("\n","\t"), array('\n','\t'),$s).'"';
}


foreach($t as $tok) {
	if(is_array($tok)) {
		echo token_name($tok[0])." :\t".stringify($tok[1])."\n";
	}
	else
		echo "string :\t".stringify($tok)."\n";
}



?>