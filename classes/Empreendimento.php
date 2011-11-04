<?php
define('DEBUG', false);

class Empreendimento {
	private $id;
	private $nome;
	private $justificativa;
	private $local;
	private $recursos;
	private $descricao;
	
	private $unOrg;
	private $solicitante;
	
	private $obras;
	
	private $bd;
	
	function __construct($bd) {
		$this->id = 0;
		$this->nome = '';
		$this->justificativa = '';
		$this->local = '';
		$this->recursos = array();
		$this->descricao = '';
		
		$this->unOrg = array('compl' => '', 'id' => '', 'nome' => '', 'sigla' => '');
		$this->solicitante = array('nome' => '', 'depto' => '', 'email' => '', 'ramal' => '');
		
		$this->obras = array();
		
		$this->bd = $bd;
	}
	
	function saveNew() {
		$sql = "INSERT INTO obra_empreendimento (nome, unOrg, justificativa, local, descricao, solicNome, solicDepto, solicEmail, solicRamal) VALUES ('{$this->nome}','{$this->unOrg['id']}','{$this->justificativa}','{$this->local}','{$this->descricao}','{$this->solicitante['nome']}','{$this->solicitante['depto']}','{$this->solicitante['email']}','{$this->solicitante['ramal']}')";
		$insert = $this->bd->query($sql); 
		if(constant('DEBUG')) print '<BR />'.$sql.'<BR />';
		
		if($insert) {
			$sql = "SELECT id FROM obra_empreendimento WHERE nome = '{$this->nome}' AND unOrg = '{$this->unOrg['id']}'";
			if(constant('DEBUG')) print '<BR />'.$sql.'<BR />';
			
			$selectID = $this->bd->query($sql);
			if(count($selectID) == 1){
				$this->id = $selectID[0]['id'];
				return true;
			}
		}
		return false;
	}
	
	function save() {
		if($this->id > 0){
			$sql = "UPDATE obra_empreendimento SET nome='{$this->nome}', unOrg='{$this->unOrg['id']}', justificativa='{$this->justificativa}', local='{$this->local}', descricao='{$this->descricao}', solicNome='{$this->solicitante['nome']}', solicDepto='{$this->solicitante['depto']}', solicEmail='{$this->solicitante['email']}', solicRamal='{$this->solicitante['ramal']}' WHERE id = $this->id";
			if(constant('DEBUG')) print '<BR />'.$sql.'<BR />';
			
			$update = $this->bd->query($sql);
			if($update) {
				return true;
			}
		}
		return false;
	}
	
	function load($id, $recurso = false, $obra = false) {
		if($id > 0){
			$sql = "SELECT * FROM obra_empreendimento WHERE id=$id";
			if(constant('DEBUG')) print '<BR />'.$sql.'<BR />';
			
			$empr = $this->bd->query($sql);
			
			if(count($empr) == 1 && $empr) {
				$this->id = $id;
				$this->nome = $empr[0]['nome'];
				$this->justificativa = $empr[0]['justificativa'];
				$this->local = $empr[0]['local'];
				$this->descricao = $empr[0]['descricao'];
								
				//conulta a unidade da obra
				$sql = "SELECT id,nome,sigla FROM unidades WHERE id='".$empr[0]['unOrg']."'";
				if(constant('DEBUG')) print '<BR />'.$sql.'<BR />';
				
				$unOrg = $this->bd->query($sql);
				
				if (count($unOrg) && $unOrg){
					$this->unOrg['compl'] = $unOrg[0]['id'].' - '.$unOrg[0]['nome'].' ('.$unOrg[0]['sigla'].')';
					$this->unOrg['id']    = $unOrg[0]['id'];
					$this->unOrg['nome']  = $unOrg[0]['nome'];
					$this->unOrg['sigla'] = $unOrg[0]['sigla'];
					
				} else {
					$this->unOrg = $empr['unOrg'];
				}
				
				$this->solicitante = array('nome' => $empr[0]['solicNome'] ,'depto' => $empr[0]['solicDepto'] ,'email' => $empr[0]['solicEmail'] ,'ramal' => $empr[0]['solicRamal']);
				return true;
			}
			
		}
		return false;
	}
	
	function getRecursos() {
		if($this->id > 0) {
			$sql = "SELECT id FROM obra_recurso WHERE empreendID=$this->id";
			if(constant('DEBUG')) print '<BR />'.$sql.'<BR />';
			
			$recID = $this->bd->query($sql);
			
			if(count($recID) > 0) {
				foreach ($recID as $rID) {
					$r = new Recurso($this->bd);
					if(getVal($r->load($rID),'success'))
						$this->recursos[] = $r;
				}
				return true;
			}
		}
		return false;
	}
	
	function transferRecursoToObra($recursoID,$valor,$obraID){
		$rec = new Recurso($this->bd);
		$rec->load($recursoID);
		
		if($rec->montante == $valor) {
			$rec->obraID = $obraID;
			$rec->save();
			
		} elseif ($rec->montante > $valor) {
			$rec->montante = $rec->montante - $valor;
			$rec->save();
			
			$rec2 = new Recurso($this->bd);
			$rec2->montante = $valor;
			$rec2->origem = $rec->origem;
			$rec2->prazo = $rec->prazo; 
			$rec2->insertRecursoInObra($obraID);
			
		} else {
			return false;
		}
		
		
	}
	
	function getObras(){
		if ($this->id > 0){
			$sql = "SELECT id FROM obras_cad WHERE empreendID=$this->id";
			if(constant('DEBUG')) print '<BR />'.$sql.'<BR />';
			
			$obraID = $this->bd->query($sql);
			
			if(count($obraID) > 0) {
				foreach ($obraID as $oID) {
					$obra = new Obra($this->bd);
					$obra->load($oID);
					$this->obras[] = $obra;
				}
				return true;
				
			}
			return false;
			
		}
	}
	
	/**
	 * Funcao para modificar o valor de alguma variavel da classe
	 * @param string $attr
	 * @param mixed $val
	 */
	function set($attr, $val){
		if(isset($this->$attr) && $attr != 'id'){
			$this->$attr = $val;
			return true;
		}
		return false;
	}
	
	/**
	 * Funcao para resgatar alguma variavel da classe
	 * @param string $attr
	 * @return mixed attributo or null se atributo nao existir
	 */
	function get($attr) {
		if(isset($this->$attr))
			return $this->$attr;
		else
			return null;
	}
	
	function getVars(){
		if(isset($_POST['unOrg']))
			$this->unOrg['id'] = $_POST['unOrg'];
		if(isset($_POST['solicNome']))
			$this->solicitante['nome'] = htmlentities($_POST['solicNome'], ENT_QUOTES);
		if(isset($_POST['solicDepto']))
			$this->solicitante['depto'] = htmlentities($_POST['solicDepto'], ENT_QUOTES);
		if(isset($_POST['solicEmail']))
			$this->solicitante['email'] = $_POST['solicEmail'];
		if(isset($_POST['solicRamal']))
			$this->solicitante['ramal'] = $_POST['solicRamal'];
		
		if(isset($_POST['nome']))
			$this->nome = htmlentities($_POST['nome'], ENT_QUOTES);
		if(isset($_POST['justificativa']))
			$this->justificativa = htmlentities($_POST['justificativa'], ENT_QUOTES);
		if(isset($_POST['local']))
			$this->local = htmlentities($_POST['local'], ENT_QUOTES);
		if(isset($_POST['descricao']))
			$this->descricao = htmlentities($_POST['descricao'], ENT_QUOTES);
	}
}

?>