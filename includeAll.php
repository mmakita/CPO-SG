<?php
	include_once('conf.inc.php');
	include_once('error.php');
	include_once('modules.php');
	include_once('interfaces.php');
	include_once('sgd_interface.php');
	include_once('sgd_modules.php');
	include_once('queries.php');
	/* include de classes */
	include_once('classes/Html.php');
	include_once('classes/Pessoa.php');
	include_once('classes/BD.php');
	include_once('classes/Documento.php');
	include_once('classes/Empresa.php');
	
	
	if(isset($_GET['alert']) && $_GET['alert'] != '')
		$conf['head'] =  '<script type="text/javascript">alert(\''.$_GET['alert'].'\');</script>';		
		
	date_default_timezone_set($conf['timezone']);
	
	session_start();
	
	function includeModule($name){
		if ($name == 'sgo') {
			include_once('sgo_modules.php');
			include_once('sgo_interface.php');
			include_once('classes/EntradaHistoricoObra.php');
			include_once('classes/Obra.php');
			include_once('classes/Recurso.php');
			include_once('classes/Etapa.php');
			include_once('classes/Fase.php');
			include_once('classes/SGO.php');
		}
	}
?>