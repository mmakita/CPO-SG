<?php
	/**
	 * @version 0.1 16/2/2011 
	 * @package geral
	 * @author Mario Akita
	 * @desc pagina que lida com erros do sistema
	 */

	/**
	 * @desc Mostra um alert na tela informando erro de sistema e redireciona
	 * @param int $cod Codigo de erro
	 * @param string $redir Endereco de redirecionamento (se houver)
	 */
	function showError($cod, $redir = "index.php"){
		$erro[0] = "";
		$erro[1] = "Erro ao efetuar login. Por favor, tente novamente."; //login.php
		$erro[2] = "Erro ao conectar-se com o banco de dados."; //classes/BD.php
		$erro[3] = "Erro ao selecionar base de dados"; //classes/BD.php
		$erro[4] = "Erro ao efetuar logout. Por favor, tente novamente."; //logout.php
		$erro[5] = "Erro ao ler os dados do documento."; //Documento.php
		$erro[6] = "Vocъ deve estar logado para visualisar a pсgina. Por favor, efetue o login no sistema para prosseguir."; //modules.php
		$erro[7] = "Erro ao selecionar documento para visualizaчуo.";
		$erro[9] = "Nao foi possivel realizar a busca.";
		$erro[10]= "Erro. Vocъ nуo tem permissуo para realizar esta aчуo";
		$erro[11]= "Erro. Nуo hс dados suficientes pra realizar esta aчуo.";
		$erro[12]= "Erro. Este usuсrio nуo tem privilщgios suficentes para realizar esta operaчуo.";
		$erro[13]= "Erro ao se conectar ao Active Directory. Contate o administrador de redes.";
		
		if (strpos($redir, "?") === FALSE)
			header("Location: ".$redir."?alert=".$erro[$cod]);
		else
			header("Location: ".$redir."&alert=".$erro[$cod]);
}
?>