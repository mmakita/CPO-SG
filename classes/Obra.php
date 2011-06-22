<?php

class Obra {
	private $id;
	private $cadastro;
	private $recurso;
	
	/**
	 * cria nova obra com os atrributos passados por parametro
	 * @param array $attr
	 */
	public function __construct($id){
		$this->id = $id;
	}
	
	/**
	 * Cria um novo cadastro de obra
	 * @param array $attrVal
	 */
	public function newCadastro($attrVal){
		if ($this->id != 0) return null;
		
		global $bd;
		
		$attr = array('nome','local_lat','local_lng','dimensao','pavimentos','tipo','ocupacao','residuos','elevador','fase');
		
		foreach ($attr as $a) {
			if(isset($attrVal[$a]))
				;
			
				
		}
		
		$this->cadastro[$attr];
	}
	
	/**
	 * Le um cadastro de obra do BD
	 */
	public function readCadastro(){
		if ($this->id == 0) return null;
		
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $attr
	 */
	public function getAttr($attr) {
		;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $attr
	 */
	public function setAttr($attr) {
		;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $attr
	 */
	public function updateCadastro($attr){
		;
	}
}