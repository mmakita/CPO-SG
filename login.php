<?php
	/**
	 * @version 0.1 16/2/2011 
	 * @package geral
	 * @author Mario Akita
	 * @desc efetua login do usuario ou mostra a tela para entrar com usuario e senha
	 */
	include_once('includeAll.php');
	include_once('error.php');
	$html = new html($conf,$conf['template_login']);
	$pessoa = new Pessoa();
	$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);

	if(isset($_GET['redir']) && $_GET['redir'] != '/login.php?')
		$html->content[1] = '<input type="hidden" name="redir" value="'.$_GET['redir'].'" />';
	else
		$html->content[1] = '<input type="hidden" name="redir" value="/index.php" />';
		
	if(isset($_POST['username']) && isset($_POST['senha']) ){
		if($pessoa->login($_POST['username'], $_POST['senha'],$bd))
			header("Location: ".$_POST['redir']);
		else{
			checklogin(1);
		}
	}
	
	$html->title .= "login";
	$html->campos['codPag'] = showCodTela();
	$html->showPage();
?>