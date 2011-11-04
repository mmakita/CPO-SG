<?php

class Etapa {
	private $id;
	public $tipoID;
	public $tipo;
	public $obraID;
	public $processoID;
	public $processo;
	public $responsavel;
	public $estado;
	public $fases;

	/**
	 * Construtor da classe. Inicializa as variaveis
	 * @param int $obraID
	 * @param int $tipoID
	 **/
	function __construct($obraID, $tipoID = 1, $procID = 0) {
		//verif do ID da obra
		if (!$obraID) return array("success" => false, "errorNo" => 1, "errorFeedback" => "Obra nula");
		
		//atribuicao das variaveis
		$this->obraID = $obraID;
		$this->tipoID = $tipoID;
		$this->tipo = '';
		$this->responsavel = 0;
		$this->processoID = $procID;
		$this->processo = null;
		$this->estado = array("id" => 0, "label" => 'Desconhecido.');
	}
	
	/**
	 * metodo para adicionar fase a uma etapa
	 * @param int $fID
	 * @param int $sfID
	 * @param int $sfdID
	 * @param int $docID
	 **/
	function addFase($fID, $sfID, $sfdID, $docID) {
		//cria fase com os dados passados por parametro
		$fase = new Fase($fID, $sfID, $sfdID, $docID, $this->id);
		//salva os dados no BD
		$fase->saveNew();
		//adiciona ao array de fases desta etapa
		$this->fases[] = $fase;
	}
	
	/**
	 * Metodo para salvar a Etapa no BD
	 */
	function save() {
		global $bd;
		
		//verifica se a etapa ja esta cadastrada
		$res = $bd->query("SELECT id FROM obra_etapa WHERE tipoID={$this->tipoID} AND obraID={$this->obraID} AND processoID={$this->processoID}");
		
		if(count($res) == 1) {
		//esta cadastrado - UPDATE
			$this->id = $res[0]['id'];
			if ($bd->query("UPDATE obra_etapa SET responsavel='{$this->responsavel}', estado={$this->estado['id']}  WHERE id={$this->id}")) {
				return array("success" => true, "errorNo" => 0, "errorFeedback" => '');
			} else {
				return array("success" => false, "errorNo" => 1, "errorFeedback" => "Obra nula");
			}
			
		} elseif(!count($res)) {
		//nao esta cadastrado - INSERT
			if ($bd->query("INSERT INTO obra_etapa (obraID, tipoID, responsavel, processoID) VALUES ({$this->obraID}, {$this->tipoID}, {$this->responsavel}, {$this->processoID})")) {
				$res = $bd->query("SELECT id FROM obra_etapa WHERE tipoID={$this->tipoID} AND obraID={$this->obraID}");
				$this->id = $res[0]['id'];
				return array("success" => true, "errorNo" => 0, "errorFeedback" => '');
			} else {
				return array("success" => false, "errorNo" => 3, "errorFeedback" => "Erro ao inserir Etapa da Obra");
			}
			
		} else {
		//algo esta errado 2 etapas iguais para uma mesma obra
			return array("success" => false, "errorNo" => 2, "errorFeedback" => "Erro de consistencia: Ha mais de uma etapa com essas caracteristicas.");
		}
	}
	
	/**
	 * Carrega os dados da etapa cujo ID eh passado por parametro 
	 * @param int $id
	 */
	function load($id){
		global $bd;
		//seleciona o registro com o id passado
		$res = $bd->query("SELECT tipoID, obraID, responsavel,processoID,estado FROM obra_etapa WHERE id=$id");
		//se houver apenas 1 etapa com esse ID
		if (count($res) == 1) {
			//atribui variaveis
			$this->id = $id;
			$this->tipoID = $res[0]['tipoID'];
			$this->obraID = $res[0]['obraID'];
			$this->processoID = $res[0]['processoID'];
			$this->responsavel= $res[0]['responsavel'];
			
			$this->estado['id'] = $res[0]['estado'];
			if($this->estado['id']) {
				$res2 = $bd->query("SELECT nome FROM label_etapa_estado WHERE id={$this->estado}");
				if(count($res2))
					$this->estado['label'] = $res2[0]['nome'];
			}
			
			//seleciona ID das fases
			$fasesID = $bd->query("SELECT id FROM obra_fase WHERE etapaID={$this->id}");
			//se houver fases retornadas
			if(count($fasesID)) {
				//para cas fase, carrega os attributos
				foreach ($fasesID as $f) {
					$fase = new Fase();
					$fase->load($f['id']);
					$this->fases[] = $fase;
				}
			} else {
				//se nao houver fases nessa etapa, deixa o array vazio
				$this->fases = array();
			}
			
			if ($this->processoID) {
				$doc = new Documento($this->processoID);
				$doc->loadCampos();
				$this->processo = $doc;
			}
			
			$tp = $bd->query("SELECT nome FROM label_obra_etapa WHERE id=$this->tipoID");
			
			if (count($tp)) {
				$this->tipo = $tp[0]['nome'];
			} else {			
				$this->tipo =  '';
			}
			
			return array("success" => true, "errorNo" => 0, "errorFeedback" => "");
		} else {
			return array("success" => false, "errorNo" => 6, "errorFeedback" => "Etapa Inexistente");
		}
		
	}
	
	/**
	 * Retorna do ID da Etapa
	 * @return number
	 */
	function getID(){
		return $this->id;
	}
	
}