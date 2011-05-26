<?php
	/**
	 * @version 0.0 20/4/2011 
	 * @package geral
	 * @author Mario Akita
	 * @desc pagina que lida com os modulos de gerenciamento de obras
	 */
	include_once('includeAll.php');
	include_once('sgd_modules.php');
	session_start();
	
	checkLogin(6);
	
	$html = new html($conf);
	$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);
	
	$html->setTemplate($conf['template']);
	$html->header = "Ger&ecirc;ncia de Obras";
	$html->campos['codPag'] = showCodTela();
	$html->title .= "Em construηγo";
	$html->user = $_SESSION["nomeCompl"];
	$html->path = showNavBar(array());
	$html->menu = showMenu($conf['template_menu'],$_SESSION["perm"],30,$bd);
	
	$html->content[1] = "Em constru&ccedil;&atilde;o.";
	
	$html->showPage();
?>