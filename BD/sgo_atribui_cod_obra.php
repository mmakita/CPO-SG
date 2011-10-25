<?php

set_time_limit(0);

include_once '../includeAll.php';
includeModule('sgo');

$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);

$fp = fopen("processos_ano.csv", "r");

$linha_anterior=array(0=>'', 1=>'');

while (($linha = fgets($fp)) !== false) {//leitura da linha
	$obraID = 0;
	
	$linha = explode(";", str_ireplace(array("\r","\n"), '', $linha));
	
	if($linha[1] == $linha_anterior[1] || $linha[1] == "")
		continue;

	$linha_anterior = $linha;
	
	$linha[0] = explode("- ", $linha[0], 2);
	
	//1 - identificar a obra
	$linha[0][1] = htmlentities($linha[0][1],ENT_QUOTES);
	if(substr($linha[0][1],-1,1) == ' ') {
		$linha[0][1] = substr($linha[0][1], 0, strlen($linha[0][1])-1);
	}
	
	$obra = $bd->query("SELECT id,nome,unOrg FROM obra_cad WHERE nome='".$linha[0][1]."'");
	//1.1 - se houver 2 obras com o mesmo nome (ex: reforma sanitario)
	if (count($obra) > 1){
		if ($linha[0][0] == 'PREFEITURA')     $linha[0][0] = '01.14.00.00.00.00';
		elseif ($linha[0][0] == 'CEPETRO')    $linha[0][0] = '01.02.04.24.00.00';
		elseif ($linha[0][0] == 'CEPAGRI')    $linha[0][0] = '01.02.04.05.00.00';
		elseif ($linha[0][0] == 'EDITORA')    $linha[0][0] = '01.01.21.00.00.00';
		elseif ($linha[0][0] == 'COMVEST')    $linha[0][0] = '01.04.01.00.00.00';
		elseif ($linha[0][0] == 'HEMOCENTRO') $linha[0][0] = '32.00.00.00.00.00';
		elseif ($linha[0][0] == 'REITORIA')   $linha[0][0] = '01.00.00.00.00.00';
		elseif ($linha[0][0] == 'CPO')        $linha[0][0] = '01.14.16.00.00.00';
		elseif ($linha[0][0] == 'STU' || $linha[0][0] == 'CSS/CECOM' || $linha[0][0] == 'GASTROCENTRO')
			$linha[0][0] = '00.00.00.00.00.00';
		else {
			$res = $bd->query("SELECT id FROM unidades WHERE sigla = '{$linha[0][0]}'");
			if(count($res)){
				$linha[0][0] = $res[0]['id'];
			} else {
				$linha[0][0] = '00.00.00.00.00.00';
			}
		}
		
		$i = 0;
		while ($i < count($obra)) {
			if ($linha[0][0] == $obra[$i]['unOrg']) {
				$ano[$obra[$i]['id']] = $linha[1];
			}
			$i++;
		}
	} elseif(count($obra) == 1) {
		//$linha[0][0] = $obra[0]['unOrg'];
		$ano[$obra[0]['id']] = $linha[1];
	}
}


$obrasID = $bd->query("Select id from obra_cad order by unOrg");

foreach ($obrasID as $o) {
	$obra = new Obra();
	$obra->load($o['id']);
	$cod['id'] = $o['id'];
	
	if(isset($ano[$o['id']])) {
		$cod['ano'] = $ano[$o['id']];
	} else {
		$cod['ano'] = 'XX';
	}
	
	$obras_mesma_un = $bd->query("Select cod from obra_cad where unOrg like '".substr($obra->unOrg['id'],0,8)."%'");
	$maior = 1;
	foreach ($obras_mesma_un as $codO) {
		$codO = explode('-', $codO['cod']);
		if(isset($codO[2]) && $codO[1] == $cod['ano'] && $codO[2] >= $maior){
			$maior = $codO[2] + 1;
		}
	}
	
	if($maior < 10) {
		$cod['seq'] = '0'.$maior;	
	} else {
		$cod['seq'] = $maior;
	}
	
	$cod['unOrg'] = substr((str_ireplace('.', '', $obra->unOrg['id'])), 0, 6);
	$bd->query("UPDATE obra_cad SET cod = '{$cod['unOrg']}-{$cod['ano']}-{$cod['seq']}' WHERE id = {$cod['id']}");
	print("UPDATE obra_cad SET cod = '{$cod['unOrg']}-{$cod['ano']}-{$cod['seq']}' WHERE id = {$cod['id']}<br>");
}
?>