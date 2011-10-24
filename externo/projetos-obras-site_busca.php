<?php
include_once('../includeAll.php');
includeModule('sgo');
$res = array();
$data = array();

//conexao no BD
$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);


if (isset($_GET['campus']) && isset($_GET['nome']) && isset($_GET['unOrg']) && isset($_GET['tipo']) && isset($_GET['area'])) {
	$restr = null;
	
	if($_GET['campus']) {
		$c_restr = '(';
		$campus = explode("|", $_GET['campus']);
		foreach ($campus as $c) {
			$c_restr .= "campus = '$c' OR ";
		}
		$restr[] = rtrim($c_restr, ' OR ') . ')';
	}
	
	if($_GET['nome']) {
		$nome = explode(" ", htmlentities(urldecode($_GET['nome'])));
		foreach ($nome as $n) {
			$restr[] = "nome LIKE '%{$n}%'";
		}
	}
	
	if($_GET['unOrg']) {
		$regex1 = preg_match("|^[0-9]{2}.[0-9]{2}.[0-9]{2}.[0-9]{2}.[0-9]{2}.[0-9]{2}|", $_GET['unOrg'], $matches);
		if ($regex1)
			$restr[] = "unOrg ='".substr($_GET['unOrg'], 0, 17)."'";
		else	
			$restr[] = "unOrg LIKE '%".htmlentities($_GET['unOrg'])."%'";
	}
	
	if($_GET['caract']) {
		$c_restr = '(';
		$tipos = explode("|", $_GET['caract']);
		foreach ($tipos as $c) {
			if($c == 'ref') $c_restr .= "caract = 'ref' OR ";
			if($c == 'nova') $c_restr .= "caract = 'nova' OR ";
			if($c == 'ampl') $c_restr .= "caract = 'ampl' OR ";
			if($c == 'ampl_ref') $c_restr .= "caract = 'ampl_ref' OR ";
			if($c == 'continuidade') $c_restr .= "caract = 'continuidade' OR ";
	}
		$restr[] = rtrim($c_restr, ' OR ') . ')';
	}
	
	if($_GET['tipo']) {
		$c_restr = '(';
		$tipos = explode("|", $_GET['tipo']);
		foreach ($tipos as $c) {
			if($c == 'pred') $c_restr .= "tipo = 'pred' OR ";
			if($c == 'infra') $c_restr .= "tipo = 'infra' OR ";
			}
		$restr[] = rtrim($c_restr, ' OR ') . ')';
	}
	
	//filtro de area
	if($_GET['area']) {
		$c_restr = '(';
		$notdef = false;
		//adicionando restricao de nulidade quando alguma dimensao for selecionada
		if (substr($_GET['area'], 0 , 2) == 'N|') {
			$c_restr .= "dimensao IS NULL ";
			$_GET['area'] = substr($_GET['area'], 2);
			$notdef = true;
		}
		//separa os campos min e max passados
		$tipo = explode('-', $_GET['area']);
		if (count($tipo) == 2) {
			if ($tipo[0] || $tipo[1]){
				if ($notdef) $c_restr .= "OR (";
				else $c_restr .="(";
				
				if($tipo[0]) $c_restr .= " dimensao >= {$tipo[0]} AND ";
				else $c_restr .= '';
				
				if($tipo[1]) $c_restr .= " dimensao <= {$tipo[1]}";
				else $c_restr .= '';
				
				$c_restr = rtrim($c_restr, " AND ") . ')';
			
			}
		}
		//se nao tiver nenhuma restricao, nao coloca no vetor de restricao
		if($c_restr != '(')
			$restr[] = $c_restr . ')';
		}
		
		$restr[] = ' visivel = 1 ';

		//monta consulta SQL
		$sql = "SELECT id FROM obra_cad";
		
		if(count($restr)) {
			$sql .= " WHERE ";
			foreach ($restr as $r) {
				$sql .= $r . ' AND ';
			}
			$sql = rtrim($sql, ' AND ');
		}
		
		$sql .= ' ORDER BY unOrg, nome';
		//DEBUG
		//print ($sql);
		
		//consulta o BD
		$res = $bd->query($sql);
	}


foreach ($res as $r) {
	$obra = new Obra();
	$obra->load($r['id'], true);
	
	$o["id"]        	  = $obra->get("id");
	$o["codigo"]    	  = $obra->codigo;	
	$o["nome"]      	  = $obra->nome;
	$o["tipo"]      	  = $obra->tipo;
	$o["caract"]		  = $obra->caract;
	$o["area"]      	  = $obra->area;
	$o["amianto"]   	  = $obra->amianto;
	$o["ocupacao"]  	  = $obra->ocupacao;
	$o["residuos"]  	  = $obra->residuos;
	$o["pavimentos"]	  = $obra->pavimentos;
	$o["elevador"]  	  = $obra->elevador;
	$o["responsavelProj"] = $obra->responsavelProj;
	$o["responsavelObra"] = $obra->responsavelObra;
	$o["estado"]          = $obra->estado;
	$o["unidade"]         = $obra->unOrg;
	$o["local"]           = $obra->local;
	$o['descr']		      = $obra->descricao;
	$o['lat']			  = $obra->local['lat'];
	$o['lng']			  = $obra->local['lng'];
	$o['img']			  = $obra->desc_img;
		
	$data[] = $o;
}

print json_encode($data);

?>