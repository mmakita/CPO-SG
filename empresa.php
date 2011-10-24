<?php
	include_once 'includeAll.php';
	include_once 'empresa_modules.php';
	session_start();
	
	checkLogin(6);
	
	$html = new html($conf);
	$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);

	if(isset($_GET['acao'])){
		$acao = $_GET['acao']; 
	}else{
		$acao = "buscar";
	}
	if(isset($_GET['onclick'])){
		$onclick = $_GET['onclick']; 
	}else{
		$onclick = "buscar";
	}
	
/**/if($acao == "buscar"){
		$html->setTemplate($conf['template_mini']);
		$html->path = showNavBar(array(array("url" => "","name" => "Empresas"),array("url" => "","name" => "Buscar")));
		$html->title = "Buscar Empresa";
		$html->menu = '<script type="text/javascript">$(document).ready(function(){$("#c2").hide();$("#c3").hide();$("#c4").hide();$("#c5").hide();$(".boxLeft").css("width","0");$(".boxRight").css("width","100%");});</script>
					   <input type="hidden" id="onclick" value="'.$onclick.'" />';
		$html->content[1] = showBuscaEmprForm();
		$html->content[2] = '<center><input type="button" id="novaBusca" value="Buscar novamente" /></center>';
		$html->content[3] = '<div id="resBusca" width="100%"></div>';
		$html->content[4] = showFormCadEmpr();
		$html->campos['codPag'] = showCodTela();
		$html->showPage();
/**/}elseif ($acao == "doBusca"){
		if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == '') ){
			print "[]";
			exit();
		}
		$q = htmlentities($_GET['q']);
		
		$res = $bd->query("SELECT * FROM empresa WHERE nome LIKE '%".$q."%'");
		
		print(json_encode($res));
/**/}elseif ($acao == "cad"){
		if (!isset($_GET['data'])){
			print '0';
			exit();
		}
		$d = explode("|", $_GET['data']);
		
		for ($i = 0; $i < count($d); $i++) {
			$d[$i] = htmlentities($d[$i]);
		}
		
		$d[5] = str_replace("-", "", $d[5]);//tratamento cep
		$d[6] = str_replace(array("(",")","-"," "), array('','','',''), $d[6]);//tratamento telefone
		
		$sql = "INSERT INTO empresa (nome,endereco,complemento,cidade,estado,cep,telefone,email) VALUES (
				'".$d['0']."','".$d['1']."','".$d['2']."','".$d['3']."','".$d['4']."','".$d['5']."','".$d['6']."','".$d['7']."')";
		
		$res = $bd->query($sql);
		
		if ($res){
			$r = $bd->query("SELECT id FROM empresa WHERE nome = '".$d['0']."' AND endereco = '".$d['1']."' LIMIT 1");
			if ($r)
				print $r[0]['id'];
			else 
				print '0';
		}else{
			print '0';
		}
	}
	
	
?>