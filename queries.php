<?php

	//
	//  Consultas as tabelas de dados relativos a documentos
	//
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
		
		return $bd->query("SELECT nome,abrv FROM label_acao WHERE id = $id_acao");
	}
	
	/**
	 * Consulta os dados dos campos
	 * @uses $bd 
	 * @param string $campo_nome
	 */
	function getCampo($campo_nome){
		global $bd;
		
		return $bd->query("SELECT tipo,attr,extra,nome,label FROM label_campo WHERE nome = '$campo_nome'");
	}
	
	/**
	 * consulta atributos do tipo de documento
	 * @param string $tipo_doc
	 */
	function getDocTipo($tipo_doc){
		global $bd;
		
		return $bd->query("SELECT * FROM label_doc WHERE nomeAbrv = '$tipo_doc'");
	}
	
	/**
	 * Consulta todos os tipos de documento
	 */
	function getAllDocTypes(){
		global $bd;
		
		return  $bd->query("SELECT * FROM label_doc");
	}
	
	/**
	 * Seleciona algum atributo de uma tabela passada por parametro seguindo uma condicao predefinida
	 * e ordena por um atributo passado (ou nao ordena) com um certo limite de resultados (opcional)
	 * @param string $attr
	 * @param string $table
	 * @param string $condition
	 * @param string $orderBy
	 * @param string $order
	 * @param string $limit
	 */
	function attrFromGenericTable($attr, $table, $condition = '1', $orderBy = '', $order = "ASC", $limit = ''){
		global $bd;
		
		if($orderBy) $orderBy = "ORDER BY $orderBy $order";
		if($limit) $limit = "LIMIT $limit ";
		
		return $bd->query("SELECT $attr FROM $table WHERE $condition $orderBy $limit");
	}	
	
	//
	// Consulta as tabelas do sistema
	//
	/**
	 * loga a acao no BD
	 * @param string $user
	 * @param string $action
	 * @param connection $bd
	 */
	function doLog($user,$action) {
		global $bd;
		
		return $bd->query("INSERT INTO data_log (data,username,acao) VALUES (".time().",'$user','".htmlentities($action,ENT_QUOTES)."')");
	}
	
	//
	//  Consulta as tabelas de usuarios
	//
	function getUsers($user_id){
		global $bd;
		
		return $bd->query("SELECT * FROM usuarios WHERE id = $user_id");
	}
	
	/**
	 * Consulta os nomes de todos os usuarios
	 * @param string $user_id
	 */
	function getNamesFromUsers($user_id){
		global $bd;
		
		$bd->query("SELECT nomeCompl FROM usuarios WHERE id = $user_id");
	}
	
	/**
	 * Consultas todas as areas cadastradas
	 */
	function getAreasFromUsers(){
		global $bd;
		
		return $bd->query("SELECT area FROM usuarios GROUP BY area");
	} 
?>