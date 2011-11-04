<?php

class Recurso {
	
	private $id;
	
	public $empreendID;
	
	public $ObraID;
	
	public $montante;
	
	public $origem;
	
	public $prazo;
	
	public $tipo;//pode ser c=credito ou d=debito
	
	private $bd;
	
	/**
	 * Metodo construtor. Preenche com os dados de formulario enviado ou apenas cria um Rercurso em branco
	 */
	function __construct($bd) {
		//na epoca de construido, o recurso ainda nao tem ID
		$this->id = 0;
		//se ha dados de formulario enviado, le e constroi a classe com esses dados
		if (isset($_POST['montanteRec']) && isset($_POST['origemRec']) && isset($_POST['prazoRec']) && $_POST['montanteRec'] != '') {
			if(strpos($_POST['montanteRec'], ','))
				$this->montante = str_ireplace(',', '.', $_POST['montanteRec']);
			else
				$this->montante = $_POST['montanteRec'];
			
			$this->origem   = htmlentities($_POST['origemRec'],ENT_QUOTES);
			//se o prazo estiver em branco, atribui NULL, senao, tenta ler a data entrada
			if ($_POST['prazoRec'] != '') {
				$prazo = explode('/',$_POST['prazoRec']);
				$this->prazo = mktime(0,0,0,$prazo[1],$prazo[0],$prazo[2]);
			} else {
				$this->prazo = 'NULL';
			}
		}
		
		$this->tipo = 'c';
		$this->bd = $bd;
	}
	
	/**
	 * Metodo que carrega os dados do recurso com o id passado por parametro
	 * @param int $id
	 */
	function load($id) {
		//seleciona a coluna do BD
		$rec = $this->bd->query("SELECT obraID, montante, origem, prazo, tipo FROM obra_rec WHERE id={$id}");
		//se houver exatamente 1 recurso encontrado
		if(count($rec) == 1) {
			//atribui os valores as variaveis
			$this->id         = $id;
			$this->empreendID = $rec[0]['empreendID'];
			$this->ObraID     = $rec[0]['obraID'];
			$this->montante   = $rec[0]['montante'];
			$this->origem     = $rec[0]['origem'];
			$this->prazo      = $rec[0]['prazo'];
			$this->tipo       = $rec[0]['tipo'];
			//retorna sucesso
			return array("success" => true, "errorNo" => 0, "errorFeedback" => "");
		} else {
			//senao, algo muito estranho aconteceu
			return array("success" => false, "errorNo" => 6, "errorFeedback" => "Recurso Inexistente");
		}
	}
	
	/**
	 * Metodo para inserir recurso em uma determinada obra com um ID passado por parametro
	 * @param int $obraID
	 */
	function insertRecursoInEmpreend($empreendID) {
		//se foi inserido um ID inválido
		if(!$empreendID) return array("success" => false, "errorNo" => 5, "errorFeedback" => "ID invalido");
		//se foi inserido montante em branco
		if(!$this->montante) return array("success" => false, "errorNo" => 5, "errorFeedback" => "Nao e possivel adicionar recurso igual a zero ou vazio");
				
		//insercao no BD
		$r = $this->bd->query("INSERT INTO obra_rec (empreendID, obraID,montante,origem,prazo,tipo) VALUES (empreendID, 0,{$this->montante},'{$this->origem}',{$this->prazo},'{$this->tipo}')");
		
		//descoberta do id
		//EH FALSO SE HA 2 RECURSOS IGUAIS! DIFERENCIACAO OU SEM ID??
		//$recID = $bd->query("SELECT id FROM obra_rec WHERE obraID=$obraID AND montante={$this->montante} AND origem='{$this->origem}' AND prazo={$this->prazo}");
		
		//achou id
		if ($r) {
			//$this->id = $recID[0]['id'];
			return array("success" => true, "errorNo" => 0, "errorFeedback" => "");
		} else {
			return array("success" => false, "errorNo" => 2, "errorFeedback" => "ID invalido");
		}
		
	}
	
	/**
	 * Salva no BD as atualizacoes de recurso
	 */
	function save() {
		//se hao ha id para selecionar registro
		if(!$this->id) return array("success" => false, "errorNo" => 5, "errorFeedback" => "ID invalido");
		
		//atualizacao do BD
		$res = $this->bd->query("UPDATE obra_rec SET montante={$this->montante}, origem='{$this->origem}', prazo={$this->prazo}, obraID={$this->obraID} WHERE id={$this->id}");
		
		//retorno
		if($res) {
			return array("success" => true, "errorNo" => 0, "errorFeedback" => "");
		} else {
			return array("success" => false, "errorNo" => 4, "errorFeedback" => "Falha ao atualizar a base de dados");
			
		}
	}
}