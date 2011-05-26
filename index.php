<?php
	/**
	 * @version 0.1 16/2/2011 
	 * @package geral
	 * @author Mario Akita
	 * @desc pagina inicial do sistema
	 * @global $_SESSION
	 */
	include_once('includeAll.php');
	
	$pessoa = new Pessoa();
	$html = new html($conf);	
	$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);
	
	$html->title = "Portal de Acesso";
	$html->header = "Portal de Acesso";
	
	//verifica se o usuario esta logado, caso nao esteja redireciona para tela de login
	
	checkLogin(0);
	
	$html->user = $_SESSION["nomeCompl"];
		
	$html->menu = showMenu($conf['template_menu'],$_SESSION["perm"],30,$bd);
	
	$html->path = showNavBar(array());
	if (!isset($_GET['alert'])) {
		$html->content[1] = showDocsPend($_SESSION['id'], $bd);
	} else {
		$html->content[1] = $_GET['alert'];
	}
	
	
	$html->campos['codPag'] = showCodTela();
	
	$html->showPage();
	$bd->disconnect();
?>