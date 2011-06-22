<?php
	/**
	 * @version 0.13 3/3/2011 
	 * @package geral
	 * @author Mario Akita
	 * @desc pagina que concentra todas as funcoes de busca do sgd
	 */
include_once('includeAll.php');
session_start();
$res = array();
$data = array();

//conexao no BD
$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);

//BUSCA POR CAMPO EXATO
if (isset($_GET['tipoBusca'])){//tipo de busca
	if($_GET['tipoBusca'] == "cadSearch"){//seleciona o tipo de busca
		if (isset($_GET['campos']) && isset($_GET['tabela']) && isset($_GET['labelID'])) {
			$tab = $_GET['tabela'];
			$labelID = $_GET['labelID'];
			$campos = explode(",", $_GET['campos']);
			
			//monta busca com os campos preenchidos
			$query = '';
			foreach ($campos as $c) {
				if (isset($_GET[$c]) != false) {
					$tipoCampo = $bd->query("SELECT tipo,attr,extra FROM label_campo WHERE nome = '$c'");
					if($tipoCampo[0]['tipo'] == 'composto') {
						$partes = explode("+",$tipoCampo[0]['attr']);
						$query .= ' AND ( '.$c." LIKE '";
						foreach ($partes as $p) {
							if(isset($_GET[$p]) != null){
								$query .= "%".htmlentities($_GET[$p],ENT_QUOTES)."%";
							}else{
								$query .= str_replace('"','',$p);
							}
						}
						$query .= "') ";						
					} elseif($tipoCampo[0]['extra'] == 'parte') {
						continue;							
					} else {
						$query .= " AND tab.".$c." = '".htmlentities($_GET[$c],ENT_QUOTES)."'";
					}
				} else {
					showError(9);
				}
			}
			//efetua a busca retornando os IDs das matches
			
			$q = "
			SELECT doc.id FROM sg.doc AS doc
			INNER JOIN ".$tab." AS tab ON doc.tipoID = tab.id
			WHERE doc.labelID = ".$labelID.$query;
			$res = $bd->query($q);
			
			/*print($q.'<br>');
			print_r($bd->query($q)); print("<BR>");
			$q = "
			SELECT doc.id FROM sg.doc AS doc
			INNER JOIN doc_gen AS tab ON doc.tipoID = tab.id
			Where doc.labelID = 7 AND tab.tipoDoc = 'CARTA' AND tab.numero_dgen = '1233' AND tab.anoE = '2011' AND tab.unOrg = '22.07.02.00.00.00 - MANUTENCAO (MANUT)'";
			$res = $bd->query($q);
			
			/*$res = $bd->query("
			SELECT sg.doc.id FROM sg.doc AS doc
			RIGHT JOIN ".$tab." AS tab ON doc.tipoID = tab.id
			WHERE doc.labelID = ".$labelID.$query);//*/
			
			//print_r($q);
			//print_r($res);exit();
		}
	}elseif($_GET['tipoBusca'] == "buscaSearch"){
		if(isset($_GET['tipoDoc'])) {
			$tipoDoc = $_GET['tipoDoc'];// print_r($_GET);exit();
			if($tipoDoc == "_outro_") {//seleciona em qual tabela(s) procurar
				$sql = "SELECT id FROM doc as d WHERE d.id > 0";
			} else {
				$doc = new Documento('0');
				$doc->dadosTipo['nomeAbrv'] = $tipoDoc; //print_r($doc);
				$doc->loadTipoData();  
				$tab = $doc->dadosTipo['tabBD'];
				//print($tab);exit();
				
				//$tab = $bd->query("SELECT id,tabBD FROM label_doc WHERE nomeAbrv='".$tipoDoc."'");
				$sql = "SELECT d.id FROM doc as d
				LEFT JOIN ".$tab." AS t ON d.tipoID = t.id
				WHERE d.labelID = ".$doc->dadosTipo['id'];
			}
			
			foreach ($_GET as $cNome => $cValor) {
				if($cNome == "tipoDoc" || $cNome == 'tipoBusca')
					continue;
				
				elseif($cNome == 'dataCr'){//algoritmo deve identif intervalo de data ou data especifica
					if($cValor != '') {
						$dataCr = montaData($_GET['dataCr']);
						$sql .= ' AND d.data > '.$dataCr[0].' AND d.data < '.$dataCr[1];
					}
				} elseif ($cNome == "numCPO"){//campo numero CPO
					if($cValor != '') {
						$sql .= ' AND d.id='.$cValor;
					}
				} elseif ($cNome == 'desp'){//restringe a busca para os ids que tem o despacho igual ao da busca
					if($cValor != '') {	
						$resDesp = $bd->query("SELECT docID as id FROM data_historico WHERE acao LIKE '%".htmlentities($cValor)."%' GROUP BY docID");
						if (count($resDesp)) {
							$sql .= ' AND (';
							$firstID =  true;
							foreach ($resDesp as $r) {
								if(!$firstID)
									$sql .= " OR";
								$sql .= " d.id=".$r['id'];
								$firstID = false;
							}//end foreach
							$sql .= ')';
						}
					}
				} else {
					$tipoCampo = $bd->query("SELECT tipo,attr,extra FROM label_campo WHERE nome = '$cNome'");
					if($cValor != '' || $tipoCampo[0]['tipo'] == 'composto'){
						if($tipoCampo[0]['tipo'] == 'userID' && strpos($tipoCampo[0]['tipo'] == 'userID', "select") === false){
							$userID = $bd->query("SELECT id FROM usuarios WHERE nome LIKE '%".htmlentities($cValor)."%'");
							if (count($userID)){
								$sql .= ' AND (';
								foreach ($userID as $id) {
									$sql .= "$cNome = ".htmlentities($id['id'])." OR ";
								}
								$sql = rtrim($sql," OR ");
								$sql .= ")";
							} 
							
						} elseif($tipoCampo[0]['tipo'] == 'select' && $cValor == "nenhum") {
							$sql .= ' AND t.'.$cNome." LIKE '%'";
						} elseif($tipoCampo[0]['tipo'] == 'composto') {
							$partes = explode("+",$tipoCampo[0]['attr']);
							$sql .= ' AND ( '.$cNome." LIKE '";
							foreach ($partes as $p) {
								if(isset($_GET[$p]) != null){
									$sql .= "%".htmlentities($_GET[$p])."%";
								}else{
									$sql .= htmlentities(str_replace('"','',$p));
								}
							}
							$sql .= "') ";				
						} elseif($tipoCampo[0]['extra'] == 'parte') {
							continue;							
						} else {
							$sql .= ' AND t.'.$cNome." LIKE '%".htmlentities($cValor)."%'";
						}
					}
				}
			}
			$sql .= ' LIMIT 100';
			
			//realiza a query
			//print($sql);exit();
			$res = $bd->query($sql);
			
		}//end isset campos
	
	}//end if tipoBusca
}else{//end isset tipoBusca
	
}

foreach ($res as $idx => $pos) {

}

//conversao para JSON
foreach ($res as $r) {
	//leitura dos dados do documento
	$doc = new Documento($r['id']);
	$doc->loadCampos($bd);
	
	//inicio da montagem do array de saida
	//copia dos campos do documento
	$d = $doc->campos;
	 
	$d['emitente'] = $doc->emitente;
	
	//adiciona o nome do documento
	$d['nome'] = $doc->dadosTipo['nome'].' '.$doc->numeroComp;
	
	//ignora o ID (autoincrement na tabela de tipo) para ID (tabela doc) da CPO
	$d['id'] = $doc->id;
	
	//adiciona o ID do documento pai e flag de anexado
	$d['anexado'] = $doc->anexado;
	$d['docPaiID'] = $doc->docPaiID;
	
	//carregamento dos dados dos documentos anexos
	$d['docs'] = $doc->getDocAnexoDet();
	if(!isset($d['docs']))
		$d['docs'] = array();
	
	unset($d['documento']);
	
	//carregamento dos dados dos arquivos anexos
	$d['arqs'] = $doc->anexo;
	if ($d['arqs'] == null)
		$d['arqs'] = array();
	
	//carregamento dos dados da obra associada
	//TODO carregamento de obras
	$d['obra'] = array(array("id" => "", "nome" => ""));
	
	//carregamento dos dados do historico do documento
	$d['hist'] = $doc->getHist();
	
	//carega o dono atual do documento
	$d['ownerID'] = $doc->owner;
	
	//carrega se o documento eh despachavel (ownerID != usuario atual)
	if ($d['ownerID'] == $_SESSION['id'] || $d['ownerID'] == 0)
		$d['despachavel'] = 1;
	else
		$d['despachavel'] = 0;
	
	//se nao houver empresa, coloca array vazio
	if(!isset($d['empresa']))
		$d['empresa'] = array();

	//se hao houver assunto, coloca hifen
	if(!isset($d['assunto']))
		$d['assunto'] = '-';
		
	$data[] = $d;
}

print json_encode($data);
$bd->disconnect();

function montaData($data){
	$datas = explode(" ", $data);
	$datas[0] = explode("/", $datas[0]);
	if(isset($datas[2])){//se for intervalo		
		$datas[1] = explode("/", $datas[2]);
		return array(mktime(0,0,1,$datas[0][1],$datas[0][0],$datas[0][2]),mktime(23,59,59,$datas[1][1],$datas[1][0],$datas[1][2]));
	}else{//se for data especifica
		return array(mktime(0,0,1,$datas[0][1],$datas[0][0],$datas[0][2]),mktime(23,59,59,$datas[0][1],$datas[0][0],$datas[0][2]));
	}
}
?>