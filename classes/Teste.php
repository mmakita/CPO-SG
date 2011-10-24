<?php

class Teste {
	private $id;
	public $nome;
	public $versao;
	public $data;
	public $casoUso;
	public $preCond;
	public $posCond;
	
	public $GETvars;
	public $POSTvars;
	public $vars;
	
	public $resEsperado;
	public $resTeste;
	public $resFinal;
	
	/**
	 * Função construtora. Atribui ID
	 * @param int $id
	 */
	function __construct($id) {
		$this->id = $id;
	}
	
	/**
	 * Metodo que prepara as variaveis para rodar o teste.
	 */
	function prepara() {
		foreach ($this->POSTvars as $var) {
			$_POST[$var['nome']] = $var['val'];
		}
		
		foreach ($this->GETvars as $var) {
			$_GET[$var['nome']] = $var['val'];
		}
		
		foreach ($this->vars as $var) {
			$$var['nome'] = $var['val'];
		}
		
		
	}
	
	function roda() {
		print "<b>Iniciando TESTE NUMERO: {$this->id} - {$this->nome} </b><br />";
		
	}
	
	function verificaResultado($res) {
		if ($res == $this->resEsperado) {
			print('<span style="color: green;">Teste bem-sucedido!</span><BR /><BR />');
		} else {
			print('<span style="color: red;">Teste malsucedido:</span><br /> Valor esperado:<br />');
			print_r($this->resEsperado);
			print("<br /> Valor Retornado:<br />");
			print_r($res);
			print("<br /> em ".date("d/n/Y H:m:s")."<BR /><BR />");
		}
	}
}

?>