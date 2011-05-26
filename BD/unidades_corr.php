<?php
	include_once '../classes/BD.php';
	include_once '../conf.inc.php';
	
	$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);
	
	//criando campo sub
	//$bd->query("ALTER TABLE unidades ADD sub VARCHAR(100) NOT NULL");
	
	//mudando cod das unidades 1,...,9 para 01,...,09
	for ($i = 0; $i < 10; $i++) {
		$bd->query("UPDATE unidades SET id='0".$i."00.00.00.00' WHERE id='".$i."'");
	}
	
	//remocao das unidades de 4o e 5o nivel - nao utilizado
	//$bd->query("DELETE FROM unidades WHERE id LIKE '__.__.__.%'");
	
	//tratamento de id - colocando zeros nos niveis vazios a direita	
	//1o nivel
	$res = $bd->query("SELECT id FROM unidades WHERE id LIKE '__'");
	foreach ($res as $r) {
		$bd->query("UPDATE unidades SET id='".$r["id"].".00.00.00.00' WHERE id='".$r["id"]."'");
	}	

	//2o nivel
	$res = $bd->query("SELECT id FROM unidades WHERE id LIKE '__.__'");
	foreach ($res as $r) {
		$bd->query("UPDATE unidades SET id='".$r["id"].".00.00.00' WHERE id='".$r["id"]."'");
	}
	
	//3o nivel
	$res = $bd->query("SELECT id FROM unidades WHERE id LIKE '__.__.__'");
	foreach ($res as $r) {
		$bd->query("UPDATE unidades SET id='".$r["id"].".00.00' WHERE id='".$r["id"]."'");
	}
	
	//4o nivel
	$res = $bd->query("SELECT id FROM unidades WHERE id LIKE '__.__.__.__'");
	foreach ($res as $r) {
		$bd->query("UPDATE unidades SET id='".$r["id"].".00' WHERE id='".$r["id"]."'");
	}

	//retirando acentos
	$res = $bd->query("SELECT * FROM unidades");//CONSULTA TODAS AS TABELAS (PODE EXCEDER O TEMPO MAX)
	$ca = array("á","ã","à","â","é","ê","è","í","î","ï","ó","õ","ò","ô","ú","û","ü","ç",
		        "Á","Ã","À","Â","É","Ê","È","Í","Î","Ï","Ó","Õ","Ò","Ô","Ú","Û","Ü","Ç");
	$sa = array("a","a","a","a","e","e","e","i","i","i","o","o","o","o","u","u","u","c",
				"A","A","A","A","E","E","E","I","I","I","O","O","O","O","U","U","U","C");
	//preenchendo sub para economizar na quantidade de consultas
	foreach($res as $r){
		$subs = explode(".", $r['id']);
		$sub = '';
		$subsConcat = '';
		foreach ($subs as $s) {
			$subsConcat .= $s;
			if(addZero($subsConcat) == $r['id'])
				break;
			$r2 = $bd->query("SELECT sigla FROM unidades WHERE id = '".addZero($subsConcat)."'");
			if(count($r2) == 0)
				break;
			$sub .= $r2[0]['sigla'].' / ';
			$subsConcat .= '.';
		}
		$bd->query("UPDATE unidades SET nome='".str_replace($ca, $sa, $r["nome"])."', sigla='".str_replace($ca, $sa, $r["sigla"])."', sub = '$sub' WHERE id='".$r["id"]."'");
	       //print "UPDATE unidades SET nome='".str_replace($ca, $sa, $r["nome"])."', sub = '$sub' WHERE id='".$r["id"]."'<br />";
	}
	
	function addZero($id){
		if(strlen($id) == 2)  return $id.'.00.00.00.00';
		if(strlen($id) == 5)  return $id.'.00.00.00';
		if(strlen($id) == 8)  return $id.'.00.00';
		if(strlen($id) == 11) return $id.'.00';
		else return $id;
	}
	
?>