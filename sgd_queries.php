<?php
	/**
	 * Retorna todos os documentos de posse do usuario atual
	 * @uses $_SESSION
	 * @uses $bd
	 * @return array
	 */
	function getPendentDocs($id_usuario){
		global $bd;
		
		return $bd->query("SELECT id,labelID,tipoID FROM doc WHERE ownerID = $id_usuario");
	}
	
	/**
	 * Retorna o nome e abrev. de uma acao dado ID
	 * @param int $id_acao
	 * @uses $bd
	 * @return array
	 */
	function getAcao($id_acao){
		global $bd;
		
		$bd->query("SELECT nome,abrv FROM label_acao WHERE id = $id_acao");
	}
	
	
	function getCampo($campo_nome){
		global $bd;
		
		$bd->query("SELECT * FROM label_campo WHERE nome = '$campo_nome'");
	}
?>