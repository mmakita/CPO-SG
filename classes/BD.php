<?php
	/**
	 * @version 0.1 17/2/2011 
	 * @package geral
	 * @author Mario Akita
	 * @desc lida com o requisicoes para o BD
	 */

class BD {
	/**
	 * string que contem o host
	 * @var string
	 */
	private $host;

	/**
	 * login do BD
	 * @var string
	 */
	private $login;
	
	/**
	 * senha do BD
	 * @var string
	 */
	private $password;
	
	/**
	 * tabela a ser selecionada
	 * @var string
	 */
	private $table;
	
	/**
	 * variavel de conexao
	 * @var connection
	 */
	private $conn;
	
	/**
	 * @desc construtor da classe. inicia a conexao com o BD
	 * @param string $login 
	 * @param string $password
	 * @param string $host 
	 * @param string $table
	 * @return variavel da conexao
	 */
	public function __construct($login, $password, $host, $table) {
		$this->host = $host;
		$this->login = $login;
		$this->password = $password;
		$this->table = $table;
		
		$this->conn = mysql_connect($host, $login, $password) or showError(2);
	}
	
	/**
	 * @desc fecha a conexao com o bd
	 */
	public function disconnect(){
		mysql_close($this->conn);
	}
	
	/**
	 * @desc executa uma query na tabela passada por parametro
	 * @param string $sql SQL query a ser executada
	 * @param string $table tabela para ser procurada
	 * @return mixed associativa dos resultados ou true (dependendo da consulta)
	 */
	public function query($sql, $table = "sg") {
		$selectedDB = mysql_select_db($table,$this->conn) or showError(3);
		$r = mysql_query($sql);
		if ($r === true) return TRUE;
		if ($r === false){
			print 'Erro na consulta: '.$sql.' Erro obtido: '.mysql_error().' <br>
			<span style="color: red;">Esse &eacute; um erro grave.<b>Copie o erro acima</b> e informe o Administrador do sistema o quanto antes</span> <a href="report_bug.php">clicando aqui.</a>';			
			exit();
		}
		$ret = array();
		while ($res = mysql_fetch_assoc($r)){
			$ret[] = $res;
		}
		return $ret;
	}
}
?>