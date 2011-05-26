<?php
/**
 * @version 0.6 21/3/2011 
 * @package geral
 * @author Mario Akita
 * @desc contem os atributos dos documentos e os metodos para trabalho com documentos 
 */

class Documento {
	/**
	 * id do documento
	 * @var int
	 */
	public $id;
	
	/**
	 * indica se o doc ja foi anexado a algum doc. Um doc anexado nao pode ser despachado e nem ter doc anexados a ele
	 * @var boolean
	 */
	public $anexado;
	public $docPaiID;

	/**
	 * data de criacao do documento em unix timestamp
	 * @var int
	 */
	public $data;
	
	/**
	 * id do usuario que criou o documento
	 * @var int
	 */
	public $criador;
	
	/**
	 * id do usuario que possui o documento no momento (que tem pendente)
	 * @var int
	 */
	public $owner;
	
	/**
	 * id do tipo de documento no BD
	 * @var int
	 */
	public $labelID;
	
	/**
	 * id do documento dentro da tabela
	 * @var int
	 */
	public $tipoID;
	
	/**
	 * array com os nomes de arquivos anexos
	 * @var array
	 */
	public $anexo;
	
	/**
	 * array com os nomes e valores dos campos
	 * @var array
	 */
	public $campos;
	
	/**
	 * dados do tipo
	 * @var array
	 */
	public $dadosTipo;
	
	/**
	 * Nome do emitente (tabela pendentes)
	 * @var string
	 */
	public $emitente;
	
	/**
	 * Numero completo do documento
	 * @var string
	 */
	public $numeroComp;
	
	/**
	 * Instancia de conexao ao BD para uso interno
	 * @var BD
	 */
	public $bd;
	
	/**
	 * construtor da classe. atribui apenas ID do documento
	 * @param int $id
	 */
	function __construct($id){
		$this->id = $id;
	}
	
	/**
	 * carrega dados do documento comuns a todos os documentos (DOC)
	 * @param conectionn $bd
	 */
	function loadDados($bd) {
		$this->bd = $bd;
		
		$res = $bd->query("SELECT * FROM doc WHERE id = ".$this->id);
		if(count($res) != 1) showError(5);
		else $res = $res[0];
		
		$this->data = $res['data'];
		$this->criador = $res['criadorID'];
		$this->owner = $res['ownerID'];
		if($res['anexado']) $this->anexado = true;
		else $this->anexado = false;
		$anexo = explode(",",$res['anexos']);
		$this->docPaiID = $res['docPaiID'];
		if($anexo[0] != '')
			$this->anexo = $anexo; 
		$this->labelID = $res['labelID'];
		$this->tipoID = $res['tipoID'];
		$this->emitente = $res['emitente'];
		$this->numeroComp = $res['numeroComp'];
	}

	/**
	 * carrega os dados relativo ao tipo de documento (LABEL_DOC)
	 * @param connection $bd
	 */
	function loadTipoData($bd){
		
		if($this->labelID != null){
			$res = $bd->query("SELECT * FROM label_doc WHERE id = ".$this->labelID);
		}elseif(isset($this->dadosTipo['nomeAbrv'])){
			$res = $bd->query("SELECT * FROM label_doc WHERE nomeAbrv = '".$this->dadosTipo['nomeAbrv']."'");
		}else{
			$this->loadDados($bd);
			$res = $bd->query("SELECT * FROM label_doc WHERE id = ".$this->labelID);
		}
		
		if (!count($res))
			showError(5);
		
		$this->dadosTipo = $res[0];
	}
	
	/**
	 * carrega os dados relativo aos campos do documento (DOC_TIPO)
	 * @param connection $bd
	 */
	function loadCampos($bd){
		if ($this->dadosTipo == null)
			$this->loadTipoData($bd);
		
		$res = $bd->query("SELECT * FROM ".$this->dadosTipo['tabBD']." WHERE id = ".$this->tipoID);
		
		if (!count($res))
			showError(5);
				
		foreach ($res[0] as $name => $valor) {
			$tipo = $bd->query("SELECT tipo,attr FROM label_campo WHERE nome = '$name'");
			
			if(isset($tipo[0]) && $tipo[0]['tipo'] ==  'composto'){
				$partes = explode("+", $tipo[0]['attr']);
				$valorC = $res[0][$name];
				for ($i = 0; $i < count($partes); $i++) {
					if(substr($partes[$i], 0, 1) == '"'){//se comeca com " entao eh separador
						if($i == 0) continue;
						$quebra = explode(substr($partes[$i], 1, -1), $valorC);
						$res[0][$partes[$i-1]] = $quebra[0];
						if (isset($quebra[1])) $valorC = $quebra[1];
					}
				}
				if (substr($partes[count($partes)-1],0,1) != '"')
					$res[0][$partes[count($partes)-1]] = $valorC;
			}
		}
		$this->campos = $res[0];
	}
	
	/**
	 * Le os dados de cada documento anexo e o retorna em forma de array
	 * @return array com par [id],[nome] do documento anexo ou null
	 */
	function getDocAnexoDet(){
		$data = '';
		if (isset($this->campos['documento'])) {
			$ids = explode(",", $this->campos['documento']);
		
			foreach ($ids as $id){
				if ($id){
					$doc = new Documento($id);
					$doc->loadTipoData($this->bd);
					$data[] = array("id" => $doc->id, "nome" => $doc->dadosTipo['nome']." ".$doc->numeroComp);
				}
			}
			return $data;
		}else{
			return null;
		}
	}
	
	/**
	 * Le os dados do historico do documento e o retorna em forma de array
	 * @return array na forma [id][data][username][userID][action]
	 */
	function getHist($UNIXTimestamp = false) {
		$res = $this->bd->query("SELECT sg.data_historico.id,sg.data_historico.data,sg.data_historico.despacho, sg.usuarios.username, sg.usuarios.id as userID, sg.data_historico.acao FROM sg.data_historico
		LEFT JOIN sg.usuarios ON sg.data_historico.usuarioID = sg.usuarios.id WHERE sg.data_historico.docID =".$this->id." ORDER BY sg.data_historico.id DESC");
		
		if(!$UNIXTimestamp){
			for ($i = 0; $i < count($res); $i++) {
				$res[$i]['data'] = date("j/n/Y G:i",$res[$i]['data']);
			}
		}
		
		return $res;
	}
	/**
	 * Faz upload dos arquivos enviados via form
	 * @return string Relatorio do upload
	 */
	function doUploadFiles(){
		$success = array();
		$failure = array();
		
		for ($i = 1; isset($_FILES["arq".$i]); $i++) {
			
			if ($_FILES["arq".$i]['name'] == '')
				continue;
			
			if($_FILES["arq".$i]['error'] > 0 && $this->id == 0){
				$failure[] = array("name" => $_FILES["arq".$i]['name'], "errorID" => $_FILES["arq".$i]['error']);
				continue;
			}
			
			$fileName = "[".$this->id."]".$_FILES["arq".$i]['name'];
			$fileName = str_replace(array('/','ç','á','ã','â','ê','é','í','ó','õ','ô','ú','&ccedil;','&aacute;','&atilde;','&acirc;','&ecirc;','&eacute;','&iacute;','&oacute;','&otilde;','&ocirc;','&uacute',' ','?','\'','"','!','@',"'"), array('-','c','a','a','a','e','e','i','o','o','o','u','c','a','a','a','e','e','i','o','o','o','u','_','','','','','',''), $fileName);
		
			
			if (file_exists("files/" . $fileName)){
				//tratamento de nomes duplicados
				$j = 2;
				
		    	do  {//verifica se o nome do documento ja existe, se sim, adiciona (j) estilo windows para nao sobrescrever
			    	$oldName = explode(".", $fileName);
					
					if($oldName[count($oldName)-2])
						$oldName[count($oldName)-2] .= "(".$j.")";
					else
						$oldName[count($oldName)-1] .= "(".$j.")";
					
					$newName = implode(".", $oldName);
		    		$j++;
		    	} while (file_exists("files/".$newName));
		    	
		    	move_uploaded_file($_FILES["arq".$i]["tmp_name"], "files/" . $newName);
		    	$success[] = $newName;
		    	$this->anexo[] = $newName;
		    	$this->doLogHist($_SESSION['id'], "Adicionou o arquivo $newName ao documento",'');
		    			    	
		    } else {
		    	
		      move_uploaded_file($_FILES["arq".$i]["tmp_name"], "files/" . $fileName);
		      $success[] = $fileName;
		      $this->anexo[] = $fileName;
		      $this->doLogHist($_SESSION['id'], "Adicionou o arquivo $fileName ao documento",'');
		      
			}
		}
		$files['success'] = $success;
		$files['failure'] = $failure;
		return $files;
	}
	
	function salvaCampos(){
		if ($this->id == 0){//adicao de novo registro no BD
			$sql = "INSERT INTO ".$this->dadosTipo['tabBD'];
			$colunas = '';
			$valores = '';
			foreach ($this->campos as $nome => $valor) {
				if($colunas)
					$colunas .= ",";
					
				$colunas .= $nome;
					
				if($valores)
					$valores .= ",";
					
				$valores .= "'".$valor."'";
			}
			
			$sql .= " (".$colunas.") VALUES (".$valores.")";
			
		} else {//atualizacao de registro no BD
			$sql = "UPDATE ".$this->dadosTipo['tabBD']." SET ";
			
			foreach ($this->campos as $nome => $valor) {
				$sql .= $nome." = '".$valor."' , ";
			}
			$sql = rtrim($str,", ");
			$sql .= " WHERE id = ".$this->id;
		}
		//echo str_ireplace("'", '', htmlentities ($sql,ENT_QUOTES));
		return $this->bd->query($sql);
		//return 1;
	}
	
	function salvaDoc($ownerID){
		include_once 'conf.inc.php';
		
		$q = "SELECT id FROM ".$this->dadosTipo['tabBD']." WHERE ";
		
		$campoBusca = explode("," , $this->dadosTipo['campoBusca']);
		foreach ($campoBusca as $cp) {
			$q .= $cp."='".$this->campos[$cp]."' AND " ;
		}
		$q = rtrim($q," AND "); 
		
		$id = $this->bd->query($q);
		$id = $id[0]["id"];
		
		$numComp = '';
		$campoComp = explode("+", $this->dadosTipo['numeroComp']);
		foreach ($campoComp as $cp) {
			if(isset($this->campos[$cp])){
				$cpDados = $this->bd->query("SELECT extra FROM label_campo WHERE nome = '".$cp."'");
				if(strpos($cpDados[0]['extra'],"unOrg_autocompletar") !== false){//tratamento para unOrg
					$c = explode("(",$this->campos[$cp]);
					$c = rtrim($c[count($c)-1],")");
					$numComp .= $c;
				}else{
					$numComp .= $this->campos[$cp];
				}
			}else{
				$numComp .= $cp;
			}
		}
		$numComp = rtrim($numComp," ");
		
		$campoEmitente = explode(',', $this->dadosTipo['emitente']);
		foreach ($campoEmitente as $cp) {
			if (isset($this->campos[$cp])) {
				$tipo = $this->bd->query("SELECT tipo,extra FROM label_campo WHERE nome = '".$cp."'");
				if ($tipo[0]['tipo'] == 'userID' || strpos($tipo[0]['extra'],'current_user') !== false){
					$emitente = $_SESSION['nome']." ".$_SESSION['sobrenome'];
					$this->campos[$cp] = $_SESSION['id'];
				} elseif ($tipo[0]['tipo'] == 'userID'){
					$nome = $this->bd->query("SELECT nome,sobrenome FROM usuarios WHERE id=".$this->campos[$cp]);
					$emitente = $nome[0]['nome']." ".$nome[0]['sobrenome'];
				} else {
					$emitente = $this->campos[$cp];
				}
				break;
			}
		}
		
		if ($this->id == 0){//adicao de novo registro no BD
			$sql = "INSERT INTO doc (data,criadorID,ownerID,labelID,tipoID,emitente,numeroComp,anexos)
					VALUES  (".time().",".$_SESSION['id'].",".$ownerID.",".
					$this->dadosTipo['id'].",".$id.",'".$emitente."','".$numComp."','')";
			if ($this->bd->query($sql) === false){
				return false;
			} else {
				$idDoc = $this->bd->query("SELECT id FROM doc WHERE labelID = ".$this->dadosTipo['id']." AND tipoID = ".$id);
				$this->id = $idDoc[0]['id'];
				$this->numeroComp = $numComp;
				$this->emitente = $emitente;
			}
		} else {
			//troca de arquivo salvaAnexos(), troca de dono doDespacha()
		}
		return true;
	}
	
	/**
	 * Atualiza os nomes dos arquivos anexos no BD
	 */
	function salvaAnexos(){
		if(count($this->anexo) < 1)
			return 0;
		$anexo = implode(",", $this->anexo);
		return $this->bd->query("UPDATE doc SET anexos='".$anexo."' WHERE id = ".$this->id); 
	}
	
	/**
	 * Realiza o despacho de documentos
	 * @param int $userID ID do usuario atual
	 * @param int $dados [funcID] [despExt] [outro] campos para decisao de despacho
	 * @param string $despacho Conteudo do despacho
	 */
	function doDespacha($userID,$dados) {
		$ownerID = 0;
		if ($dados['funcID']){
			$ownerID = $dados['funcID'];//doc despachado para funcionario
			$para = $this->bd->query("SELECT nomeCompl FROM usuarios WHERE id = ".$ownerID);
			$para = $para[0]['nomeCompl'];
		}elseif ($dados['despExt']){
			$para = $dados['despExt'];//despacho para outra unOrg
		}elseif ($dados['outro']){
			$para = htmlentities($dados['outro']);//despacho para outros
		}elseif ($dados['para'] ==  'solic'){
			$para = " o solicitante"; // despacho para solicitante
		}elseif ($dados['para'] == 'cpo_arq')
			$para = " o Arquivo";
		else{
			$ownerID = $_SESSION['id'];//doc pendente para usuario atual caso nao tenha despachado para lugar nenhum
		}
		
		$r = $this->bd->query("UPDATE doc SET ownerID = $ownerID WHERE id = ".$this->id);
		
		if($r && $ownerID != $_SESSION['id']){
			if(!$this->doLogHist($userID, "Despachou para ".$para.".",$dados['despacho']))
				return false;
			return $para;
		}elseif($ownerID == $_SESSION['id']){
			return 0;
		}elseif(!$r){
			return false;//erro ao atualizar BD
		}else{
			return $ownerID;
		}
	}
	
	/**
	 * Grava historico do documento
	 * @param int $id id do usuario logado
	 * @param string $acao
	 */
	function doLogHist($id,$acao,$despacho){
		return $this->bd->query("INSERT INTO data_historico (data,docID,usuarioID,acao,despacho) VALUES (".time().",".$this->id.",$id,'$acao','$despacho')");
	}
	
	/**
	 * Grava anexado = 1 nos campos dos docs anexados a este documento e o id do pai
	 */
	function doFlagAnexado(){
		if(isset($this->campos['documento']) && $this->campos['documento'] != ''){
			$docsAnexados = explode(",", $this->campos['documento']);
			foreach ($docsAnexados as $doc) {
				if ($doc){
					$r = $this->bd->query("UPDATE doc SET anexado = 1, docPaiID = ".$this->id.", ownerID = 0 WHERE id = $doc");
					if (!$r)
						return false;
					$docA = new Documento($doc);
					$docA->bd = $this->bd;
					$docA->loadDados($this->bd);
					$docA->doLogHist($_SESSION['id'], "Anexou este documento ao documento ".$this->id." (".$this->dadosTipo['nome']." ".$this->numeroComp.")",'');
					doLog($_SESSION['username'],"Anexou documento ".$docA->id." (".$docA->dadosTipo['nome']." ".$docA->numeroComp.") ao documento ".$this->id." (".$this->dadosTipo['nome']." ".$this->numeroComp.")",$this->bd);
				}
			}
			return true;			
		}
		return true;
	}
}

?>