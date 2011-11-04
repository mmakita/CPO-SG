<?php
class Fase {
	private $id;
	public $faseID;
	public $faseNome;
	public $subfaseID;
	public $subfaseNome;
	public $subfasedocID;
	public $subfasedocNome;
	public $docID;
	public $doc;
	public $etapaID;
	
	/**
	 * Metodo construtor. Inicializa as variaveis
	 * @param int $fID
	 * @param int $sfID
	 * @param int $sfdID
	 * @param int $dID
	 * @param int $eID
	 */
	function __construct($fID = 0, $sfID = 0, $sfdID = 0, $dID = 0, $eID = 0) {
		$this->id             = 0;
		$this->faseID         = $fID;
		$this->faseNome       = '';
		$this->subfaseID      = $sfID;
		$this->subfaseNome    = '';
		$this->subfasedocID   = $sfdID;
		$this->subfasedocNome = '';
		$this->docID          = $dID;
		$this->doc            = null;
		$this->etapaID         = $eID; 
	}
	
	/**
	 * Metodo que carrega os dados da Fase do BD
	 * @param int $id
	 */
	function load($id) {
		global $bd;
		//leitura do registro do BD
		$result = $bd->query("SELECT * FROM obra_fase WHERE id = $id");
		
		if(count($result) == 1) {
			//atribuicao de variaveis
			$this->id           = $id;
			$this->faseID       = $result[0]['faseID'];
			$this->subfaseID    = $result[0]['subfaseID'];
			$this->subfasedocID = $result[0]['subfasedocID'];
			$this->docID        = $result[0]['docID'];
			$this->etapaID       = $result[0]['etapaID'];
			
			//leitura do nome da fase
			$result = $bd->query("SELECT nome FROM label_obra_fase WHERE faseID={$this->faseID} AND subfaseID=0 AND subfasedocID=0");
			
			if(count($result) == 1) {
				$this->faseNome = $result[0]['nome'];
			} else {
				return array("success" => false, "errorNo" => 1, "errorFeedback" => "Impossivel achar nome da fase");
			}
			
			//leitura do nome da subfase
			$result = $bd->query("SELECT nome FROM label_obra_fase WHERE faseID={$this->faseID} AND subfaseID={$this->subfaseID} AND subfasedocID=0");
			if(count($result) == 1) {
				$this->subfaseNome = $result[0]['nome'];
			} else {
				return array("success" => false, "errorNo" => 2, "errorFeedback" => "Impossivel achar o nome da subfase");
			}
			
			//leitura do nome da subfasedoc
			$result = $bd->query("SELECT nome FROM label_obra_fase WHERE faseID={$this->faseID} AND subfaseID={$this->subfaseID} AND subfasedocID={$this->subfasedocID}");
			if(count($result) == 1) {
				$this->subfasedocNome = $result[0]['nome'];
			} else {
				return array("success" => false, "errorNo" => 3, "errorFeedback" => "Impossivel achar o nome do documento");
			}
			
			//leitura do documento
			$this->doc = new Documento($this->docID);
			$this->doc->loadCampos();
			
			return array("success" => true, "errorNo" => 0, "errorFeedback" => ""); 
		} else {
			return array("success" => false, "errorNo" => 4, "errorFeedback" => "Nao ha fase com esse id ou ha mais de uma com o mesmo id");
		}
		
	}
	
	/**
	 * metodo para salvar nova Fase em uma obra
	 */
	function saveNew() {
		global $bd;
		//verifica se foram passados todos os IDs para montar a fase
		if(!$this->subfasedocID && !$this->subfaseID && !$this->faseID)
			return array("success" => false, "errorNo" => 1, "errorFeedback" => "Ha dados faltando para insercao");
		
		//insercao no BD
		$res = $bd->query("INSERT INTO obra_fase(faseID, subfaseID, subfasedocID, docID, etapaID) VALUES ({$this->faseID}, {$this->subfaseID}, {$this->subfasedocID}, {$this->docID}, {$this->etapaID})");
		//descoberta do ID se foi inserido com sucesso
		if($res) {
			//recupera o ID da fase rece criada
			$res = $bd->query("SELECT id FROM obra_fase WHERE faseID={$this->faseID} AND subfaseID={$this->subfaseID} AND subfasedocID={$this->subfasedocID} AND docID={$this->docID} AND etapaID={$this->etapaID} ");
			if(count($res)==1) {
				//atribui variaveis
				$this->id = $res[0]['id'];
				$this->load($this->id);
				return array("success" => true, "errorNo" => 0, "errorFeedback" => ""); 
			}
			return array("success" => false, "errorNo" => 4, "errorFeedback" => "Nao ha fase com esses dados ou ha mais de uma com os mesmos dados");
		} else {
			return array("success" => false, "errorNo" => 4, "errorFeedback" => "Falha na insercao da fase no BD");
		}
		
	}
	
	/**
	 * Metodo para salvar possiveis modificações na fase.
	 */
	function save() {
		global $bd;
		//atualizacao do registro no bd
		$res = $bd->query("UPDATE obra_fase SET faseID={$this->faseID}, subfaseID={$this->subfaseID}, subfasedocID={$this->subfasedocID}, docID={$this->docID}, etapaID={$this->etapaID} WHERE id={$this->id}");
		
		//retorno
		if($res) {
			$this->load($this->id);
			return array("success" => true, "errorNo" => 0, "errorFeedback" => "");
		} else {
			return array("success" => false, "errorNo" => 4, "errorFeedback" => "Falha ao atualizar a base de dados");
		}
	}
}
?>