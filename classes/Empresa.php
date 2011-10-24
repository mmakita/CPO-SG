<?php
/**
 * @version 0.1 21/3/2011 
 * @package geral
 * @author Mario Akita
 * @desc contem os atributos dos documentos e os metodos para trabalho com empresas 
 */

class Empresa {
	var $id;
	var $nome;
	var $end;
	var $compl;
	var $cidade;
	var $cep;
	var $estado;
	var $email;
	var $tel;
	var $bd;
	
	function getEmpresa($id,$bd) {
		$this->bd = $bd;
		$empr = $bd->query("SELECT * FROM empresa WHERE id = $id");
		
		$this->id     = $empr[0]['id'];
		$this->nome   = $empr[0]['nome'];
		$this->end    = $empr[0]['endereco'];
		$this->compl  = $empr[0]['complemento'];
		$this->cidade = $empr[0]['cidade'];
		$this->estado = $empr[0]['estado'];
		$this->vcep   = $empr[0]['cep'];
		$this->tel    = $empr[0]['telefone'];
		$this->email  = $empr[0]['email'];
	}
	
	function saveEmpresa($dados){
		$this->bd->query("INSERT INTO empresa (nome,endereco,completemento,cidade,estado,cep,telefone,email)
		VALUES ('".$dados['nome']."','".$dados['end']."','".$dados['compl']."','".$dados['cidade']."','".$dados['estado']."','".$dados['cidade']."','".$dados['estado']."','".$dados['cep']."','".$dados['tel']."','".$dados['email']."',)");
	}

}

?>