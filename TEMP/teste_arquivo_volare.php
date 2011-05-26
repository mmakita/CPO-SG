<?php
//$linhas = file("http://143.106.56.64:8888/temp/eee.rpt");
$fh = fopen ("http://143.106.56.64:8888/temp/eee.rpt","r");

$cont =  '';
while(!feof($fh)){
	$linha = explode(",",fgets($fh));
	if($linha[0]!= $cont){
		$cont = $linha [0];
		print "<br />".implode(",",$linha);
	} else {
		print ",".$linha[1];
	}
}

?>