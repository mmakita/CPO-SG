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
	function getPendentDocs($id_usuario,$area_usuario){
		global $bd;
		
		return $bd->query("SELECT id,labelID,tipoID FROM doc WHERE ownerID = $id_usuario OR (ownerID = -1 AND ownerArea = '$area_usuario')");
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
	function getAllUsersName($activeOnly = true){
		global $bd;
		
		if ($activeOnly) $where = "WHERE ativo = 1";
		else $where = '';
		
		return $bd->query("SELECT id, nomeCompl FROM usuarios $where ORDER BY nomeCompl");
	}
	
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
		
		return $bd->query("SELECT nomeCompl FROM usuarios WHERE id = $user_id");
	}
	
	/**
	 * Consultas todas as areas cadastradas
	 */
	function getAreasFromUsers(){
		global $bd;
		
		return $bd->query("SELECT area FROM usuarios GROUP BY area");
	} 
	
	function getAreaFromUser($id){
		global $bd;
		
		return $bd->query("SELECT area FROM usuarios WHERE id=$id LIMIT 1");
	}
	
	//
	//CONSULTAS DE BUSCA DE DOCUMENTOS
	//
	
	function searchDesp($variables) {
		if(!count($variables))
			return null;
		$recebIDs = "h.tipo = 'entrada' AND ";
		$despIDs = "h.tipo = 'saida' AND ";
		$dataDespacho = montaData($variables['dataDespacho']);
		$dataReceb = montaData($variables['dataReceb']);
		
		if(count($dataDespacho) || $variables['un']) { // procura por despacho
			if($dataDespacho[0])
				$despIDs .= 'h.data > '.$dataDespacho[0].' AND ';
			if($dataDespacho[1])
				$despIDs .= 'h.data < '.$dataDespacho[1].' AND ';
			if($variables['unReceb'])
				$despIDs .= "h.unidade LIKE '%".$variables['unDespacho']."%' AND ";
		
		}
		if(count($dataReceb) || $variables['unReceb']) { //procura por Recebimento
			if($dataReceb[0])
				$recebIDs .= 'h.data > '.$dataReceb[0].' AND ';
			if($dataReceb[1])
				$recebIDs .= 'h.data < '.$dataReceb[1].' AND ';
			if($variables['unDespacho'])
				$recebIDs .= "h.unidade LIKE '%".$variables['unReceb']."%' AND ";
		} 
		
		$despIDs = rtrim($despIDs," AND ");
		$recebIDs = rtrim($recebIDs," AND ");
		
		$sql = "SELECT docID FROM data_historico AS h WHERE ";
		
		if($dataDespacho[0] || $dataDespacho[1] || $variables['unDespacho']) {
			$sql .= " ($despIDs) AND ";
		} elseif($dataReceb[0] || $dataReceb[1] || $variables['unDespacho']) {
			$sql .= " ($recebIDs) AND ";
		}
		
		if($variables['contDesp']) {//procura em todo historico
			$sql .= " (h.despacho LIKE '%".$variables['contDesp']."%' OR h.acao LIKE '%".$variables['contDesp']."%') GROUP BY h.docID";
		} else {
			$sql = rtrim($sql, ' WHERE ');
			$sql = rtrim($sql, ' AND ');
			$sql .= " GROUP BY h.docID";
		}
		//print $sql;exit();
		return $sql;
	}
	
	function searchDoc($id, $docNum, $criacao, $tipos ,$restrTipos, $histBuscaSQL, $contGen) {
		global $bd;
		//verifica se ha algum criterio de busca para efetua-la
		if(!$id && !$docNum && !$criacao && !count($restrTipos) && !count($histBuscaSQL))
			return null;
		//verifica se ha busca por ID
		if($id)
			$restr[] = "d.id = $id";
		//verifica se ha busca por numCPO
		if($docNum)
			$restr[] = "d.numeroComp LIKE '%$docNum%'";
		//verifica se ha busca por data de criacao
		if(isset($criacao[0]))
			$restr[] = "d.data > {$criacao[0]}";
		if(isset($criacao[1]))
			$restr[] = "d.data < {$criacao[1]}";
		//verifica quais os tipos de documentos serao procurados e gera a parte da consulta SQL referente a isso
		$restr['tipo'] = '(';
		foreach ($tipos as $t) {
			$restr['tipo'] .= "d.labelID = ".$t['id']." OR ";
		}
		$restr['tipo'] = rtrim($restr['tipo'], " OR");
		$restr['tipo'] .= ')';
		
		if(count($tipos) > 1) {
			$sql = "SELECT d.id FROM doc AS d RIGHT JOIN data_historico AS h ON d.id = h.docID WHERE ";
		} else {
			$tab = $tipos[0]['tab'];
			$sql = "SELECT d.id FROM doc AS d INNER JOIN $tab AS t ON t.id = d.tipoID RIGHT JOIN data_historico AS h ON d.id = h.docID WHERE ";
			foreach ($restrTipos as $cName => $cValue) {
				$valor = montaCampo($cName,'bus',$restrTipos);
				//tratamento de campos compostos
				if($valor['tipo'] == 'composto'){
					foreach (explode(",",$valor['nome']) as $n) {
						$v = montaCampo($n,'bus',$restrTipos);
						if($v['valor'] && $v['tipo'] != 'composto')
							$restr[] = " t.".$cName." LIKE '%".$v['valor']."%' ";
					}
				} elseif($valor['parte']) { //tratamento de campos partes
					continue;
				} elseif($valor['valor']) { //montagem da condicao
					$restr[] = " t.".$cName." LIKE '%".$cValue."%' ";
				}
			}
		}
		//adicionando condicoes
		foreach ($restr as $r) {
			$sql .= $r." AND ";
		}
		$sql = rtrim($sql," AND ");
		//adicionando condicoes de historico
		if ($histBuscaSQL){
			$sql_desp = '';
			$sql_receb = '';
			$sql_cont = '';
			if((isset($histBuscaSQL['dataDespacho']) && count($histBuscaSQL['dataDespacho']) == 2) || (isset($histBuscaSQL['unDespacho']) && $histBuscaSQL['unDespacho'])) {
				$sql_desp = "(h.tipo = 'saida' AND ";
				if(isset($histBuscaSQL['dataDespacho']) && count($histBuscaSQL['dataDespacho']) == 2)
					$sql_desp .= "h.data < ".$histBuscaSQL['dataDespacho'][1]." AND h.data > ".$histBuscaSQL['dataDespacho'][0]." AND ";
				if(isset($histBuscaSQL['unDespacho']) && $histBuscaSQL['unDespacho'])
					$sql_desp .= "h.unidade LIKE '%".str_replace(' ', '%', $histBuscaSQL['unDespacho'])."%'";
				$sql_desp = rtrim($sql_desp, " AND "). ")";
			}
			if((isset($histBuscaSQL['dataReceb']) && count($histBuscaSQL['dataReceb']) == 2) || (isset($histBuscaSQL['unReceb']) && $histBuscaSQL['unReceb'])) {
				$sql_receb = "(h.tipo = 'entrada' AND ";
				if(isset($histBuscaSQL['dataReceb']) && count($histBuscaSQL['dataReceb']) == 2)
					$sql_receb .= "h.data < ".$histBuscaSQL['dataReceb'][1]." AND h.data > ".$histBuscaSQL['dataReceb'][0]." AND ";
				if(isset($histBuscaSQL['unReceb']) && $histBuscaSQL['unReceb'])
					$sql_receb .= "h.unidade LIKE '%".str_replace(' ', '%', $histBuscaSQL['unReceb'])."%'";
				$sql_receb = rtrim($sql_receb, " AND "). ")";
			}
			if(isset($histBuscaSQL['contDesp']) && $histBuscaSQL['contDesp']) {
				$sql_cont = "( h.despacho LIKE '%".str_replace(' ', '%', $histBuscaSQL['contDesp'])."%') ";
			}
			if($sql_cont || $sql_desp || $sql_receb){
				$sql .= " AND (";
				if($sql_cont)
					$sql .= $sql_cont." OR ";
				if($sql_desp)
					$sql .= $sql_desp." OR ";
				if($sql_receb)
					$sql .= $sql_receb;
				$sql = rtrim($sql," OR ").")";
			}
			
		}
		
		$genID = buscaGen($contGen,$restr['tipo'],$tipos);
		if($genID)
			$sql .= " AND d.id IN ($genID)";
		//nao repetir documentos e ordenar em ordem decrescente
		$sql .= ' GROUP BY d.id ORDER BY d.data DESC LIMIT 100';
		
		
		//print($sql);
		//exit();
		return $bd->query($sql); 
		
	}
	
	function getUnidadeName($id){
		global $bd;
		
		$res = $bd->query("SELECT * FROM unidades WHERE id = '{$id}'");
		
		if (count($res)){
			return $id . ' - ' . $res[0]['nome'] . ' (' . $res[0]['sigla'] . ')';
		}
	}
	
	function buscaGen($contGen,$restrTipo,$tipos) {
		if($contGen) {
			$rPalavra = '';
			foreach (explode(' ', $contGen) as $palavra) {
				$rTipo = '';
				foreach ($tipos as $rt) {
					$doc = new Documento(0);
					$doc->dadosTipo['nomeAbrv'] = $rt['nomeAbrv'];
					$doc->loadTipoData();
					$rCampos = '';
					foreach (explode(',',$doc->dadosTipo['campos']) as $campo) {
						$campo = montaCampo($campo,'bus');
						if($campo['tipo'] == 'input' || $campo['tipo'] == 'textarea') {
							$rCampos .= " ".$rt['nomeAbrv'].".".$campo['nome']." LIKE '%$palavra%' OR ";
						}
					}
					$rCampos = rtrim($rCampos,' OR');
					$rTipo .= 'd.id IN (SELECT d.id FROM doc AS d INNER JOIN '.$rt['tab'].' AS '.$rt['nomeAbrv'].' ON d.tipoID='.$rt['nomeAbrv'].".id WHERE ($rCampos OR d.numeroComp LIKE '%$palavra%') AND d.labelID = ".$rt['id'].") OR ";
	
				}
				$rTipo = rtrim($rTipo,' OR');
				
				$rPalavra .= '(' . $rTipo . ') AND ';			
			}
			$rPalavra = rtrim($rPalavra, ' AND ');
			$sql = "SELECT d.id FROM doc AS d WHERE $rPalavra ";
			//print($sql); exit();
			return $sql;
		} else {
			return null;
		}
	}
?>