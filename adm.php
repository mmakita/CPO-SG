<?php
	/**
	 * @version 0.1 18/2/2011 
	 * @package geral
	 * @author Mario Akita
	 * @desc pagina que lida com os modulos de gerenciamento de documentos 
	 */

	include_once('includeAll.php');
	//include_once('adm_modules.php');
	//include_once('adm_conections.php');
	session_start();
	//verifica se o usuario esta logado
	checkLogin(6);
	//verifica se o usuario tem permissao para administrar o sistema
	checkPermission(20);
	//declara inicio de uma nova pagina HTML
	$html = new html($conf);
	//abre conexao com BD
	$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);
	//seta titulo, cabecalho, menu, cod pagina e nome do usuario no template padrao
	$html->title .= "Administra&ccedil;&atilde;o do Sistema";
	$html->header = "Administra&ccedil;&atilde;o SiGPOD";
	$html->user = $_SESSION['nomeCompl'];
	$html->menu = showMenu($conf['template_menu'],$_SESSION["perm"],1,$bd);
	$html->campos['codPag'] = showCodTela();
	
	//o encadeamento de condicoes abaixo redirecionam o fluxo para a secao sendo visitada.
	//ao chegar na cadeia correta, montam-se o caminho e chama-se a funcao para gerar o conteudo da pagina
	
	if (isset($_GET['area'])) {
		/*DOCUMENTOS*/	
		if ($_GET['area'] == 'dc'){
			
/*EDIT DOC*/if (isset($_GET['acao']) && isset($_GET['tipoDoc']) && $_GET['acao'] == 'edit') {
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=dc&amp;acao=geren","name" => "Gerenciar Documentos"),array("url" => '', 'name' => 'Editar tipo de Documento')));
				$html->content[1] = showEditDocForm($bd,$_GET['tipoDoc']);
			
/*NOVO DOC*/} elseif (isset($_GET['acao']) && $_GET['acao'] == 'novo') {
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=dc&amp;acao=geren","name" => "Gerenciar Documentos"),array("url" => "","name" => "Novo tipo de Documento")));
				$html->content[1] = showEditDocForm($bd);
				
/*EXCL DOC*/} elseif (isset($_GET['acao']) && isset($_GET['tipoDoc']) && $_GET['acao'] == 'excl') {
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=dc&amp;acao=geren","name" => "Gerenciar Documentos"),array("url" => "","name" => "Excluir Documento")));
				$html->content[1] = excluiDoc($bd);
				
/*SALV DOC*/} elseif (isset($_GET['acao']) && $_GET['acao'] == 'salvar') {
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=dc&amp;acao=geren","name" => "Gerenciar Documentos"),array("url" => "","name" => "Salvar tipo de Documento")));
				$html->content[1] = salvaDoc($bd);
				
/*GERENCIAR*/} else {
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "","name" => "Gerenciar Documentos")));
				$html->content[1] = showTiposDocs($bd);
				
			}

		/*GRUPOS*/	
	} elseif ($_GET['area'] == 'gr'){
		if(isset($_GET['acao'])){
/*EDIT GRU*/if ($_GET['acao'] == 'edit' && isset($_GET['id'])) {
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=gr","name" => "Gerenciar Grupos"),array("url" => '', 'name' => 'Editar grupo')));
				$html->content[1] = showEditGrupoForm($bd,$_GET['id']);
				
/*NOVO GRU*/} elseif ($_GET['acao'] == 'novo') {
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=gr","name" => "Gerenciar Grupos"),array("url" => '', 'name' => 'Novo grupo')));
				$html->content[1] = showEditGrupoForm($bd);
				
/*EXCL GRU*/} elseif (isset($_GET['id']) && $_GET['acao'] == 'excl') {
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=gr","name" => "Gerenciar Grupos"),array("url" => '', 'name' => 'Excluir grupo')));
				$html->content[1] = excluiGrupo($bd);
					
/*SALV GRU*/} elseif ($_GET['acao'] == 'salvar') {
					$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=gr","name" => "Gerenciar Grupos"),array("url" => '', 'name' => 'Salvar dados de grupo')));
					$html->content[1] = salvaGrupo($bd);
			} 
 			
/*GERE*/}else {
			$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "","name" => "Gerenciar Grupos")));
			$html->content[1] = showGrupos($bd);
		}
			
		/*UNIDADES E ORGAOS*/	
		} elseif ($_GET['area'] == 'un'){
 /*ATUALIZ*/if ($_GET['acao'] == 'atual'){
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "","name" => "Atualizar Tabela Unidades/&Oacute;rg&atilde;os")));
				$html->content[1] = updateUnidades($bd);
 			}
						
		/*PERMISSOES*/
		} elseif ($_GET['area'] == 'pe'){
 /*GERENCI*/if ($_GET['acao'] == 'geren'){
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "","name" => "Gerenciar Permiss&otilde;es")));
				$html->content[1] = showPermGroups($bd);
				
 /*SALVAR */} elseif ($_GET['acao'] == 'salvar'){
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=pe&amp;acao=geren","name" => "Gerenciar Permiss&otilde;es"),array("url" => "","name" => "Salvar Permiss&otilde;es")));
				$html->content[1] = salvaPermissoes($bd);
			}

		/*EMPRESAS*/
		} elseif ($_GET['area'] == 'em'){				
/*ADD EMPR*/if (isset($_GET['acao']) && $_GET['acao'] == 'nova'){
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=em","name" => "Gerenciar Empresas"),array("url" => "","name" => "Nova Empresa")));
				$html->content[1] = showEditEmprForm($bd);
				
/*EDIT EMP*/} elseif(isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])){
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=em","name" => "Gerenciar Empresas"),array("url" => "","name" => "Editar Empresa")));
				$html->content[1] = showEditEmprForm($bd,$_GET['id']);
				
/*SALV EMP*/} elseif(isset($_GET['acao']) && $_GET['acao'] == 'salvar'){
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=em","name" => "Gerenciar Empresas"),array("url" => "","name" => "Salvar dados")));
				$html->content[1] = salvaEmpr($bd);
				
/*EXCL EMP*/} elseif(isset($_GET['acao']) && $_GET['acao'] == 'excl'){
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=em","name" => "Gerenciar Empresas"),array("url" => "","name" => "Salvar dados")));
				$html->content[1] = excluiEmpr($bd);
/*GERENC*/	} else {
				$html->path = showNavBar(array(array("url" => "adm.php","name" => "Administra&ccedil;&atilde;o do Sistema"),array("url" => "adm.php?area=em","name" => "Gerenciar Empresas"),array("url" => "","name" => "Gerenciar Empresas")));
				$html->content[1] = showEmpr($bd);
			}
/*INDX*/} else{
			$html->path = showNavBar(array(array("url" => "","name" => "Administra&ccedil;&atilde;o do Sistema")));
			$html->content[1] = geraIndiceAdm();
		}
/**/} else {
		$html->path = showNavBar(array(array("url" => "","name" => "Administra&ccedil;&atilde;o do Sistema")));
		$html->content[1] = geraIndiceAdm();
	}
		
	$html->showPage();
	
	/**
	 * gera menu principal com os links para administrar
	 * @return string codigo HTML da pagina
	 */
	function geraIndiceAdm(){
		$html = '
		<h3>Administrar Documentos</h3>
		&nbsp;&nbsp;<a href="adm.php?area=dc">Gerenciar tipos de documentos</a><br />
		&nbsp;&nbsp;<a href="adm.php?area=dc&amp;acao=novo">Adicionar novo tipo de documento</a><br />
		<h3>Administrar Grupos</h3>
		&nbsp;&nbsp;<a href="adm.php?area=gr">Gerenciar grupos</a><br />
		&nbsp;&nbsp;<a href="adm.php?area=gr&amp;acao=novo">Adicionar novo grupo</a><br />
		<h3>Administrar Unidades/&Oacute;rg&atilde;os</h3>
		&nbsp;&nbsp;<a href="adm.php?area=un&amp;acao=atual">Atualizar lista de Unidades/&Oacute;rg&atilde;os</a><br />
		<h3>Administrar Permiss&otilde;es</h3>
		&nbsp;&nbsp;<a href="adm.php?area=pe&amp;acao=geren">Gerenciar Permiss&otilde;es</a><br />
		<h3>Administrar Empresas</h3>
		&nbsp;&nbsp;<a href="adm.php?area=em&amp;acao=geren">Gerenciar empresas</a><br />
		&nbsp;&nbsp;<a href="adm.php?area=em&amp;acao=addnv">Adicionar nova empresa</a><br />
		';

		return $html;
	}
	
	/**
	 * gera tabela com os tipos de documentos existentes
	 * @param mysql_link $bd
	 */
	function showTiposDocs($bd){
		$html = '<h3>Tipos de documento</h3>
		<table width="500">
		<tbody>
		<tr><td><b>Nome</b></td><td></td><td></td></tr>';
		//consulta os tipos de documentos cadastrados
		$docs = $bd->query("SELECT nome,nomeAbrv,cadAcaoID,novoAcaoID FROM label_doc");
		//para cada documento consultado
		foreach ($docs as $d) {
			//gera codigo HTML da tabela
			$html .= '<tr class="c">
				<td>'.$d['nome'].'</td>
				<td><a href="adm.php?area=dc&amp;acao=edit&amp;tipoDoc='.$d['nomeAbrv'].'">Editar</a></td>';
			//se o doc tiver sido excluido, nao monta o link para exclusao
			if(!$d['cadAcaoID'] && !$d['novoAcaoID'])
				$html .= '<td>Excluido</td>';
			else
				$html .= '<td><a href="adm.php?area=dc&amp;acao=excl&amp;tipoDoc='.$d['nomeAbrv'].'">Excluir</a></td>';
			$html .= '</tr>';
		}
		//fecha tags de tabela
		$html .= '</tbody>
		</table>
		';
		
		return $html;
	}
	
	/**
	 * Gera o formulario para edicao do tipo de documento
	 * @param mysql_link $bd
	 * @param string $tipo
	 */
	function showEditDocForm($bd,$tipo = null){
		//se tipo eh null, entao deve ser gerado formulario em branco para cadastro de novo tipo de documento
		if ($tipo == null){
			//inicializa as variaveis em vazias para cadastro de novo documento
			$res = array('nome' => '', 'template' => '', 'numeroComp' => '');
			$anex = '';
			$campos = array();
			$campoHTML = '';			
			$campoInput = '<input type="hidden" name="campos" id="campos" value="" />';
			$nomeAbrv = '<input type="text" name="nomeAbrv" size="5" maxlength="5" value="" />';
			$cad = '';
			$novo = '';
			$anex = '';
			$anexDoc = '';
			$anexObr = '';
			$anexEmp = '';
		//se o tipo de cadastro foi passado, entao gera o formulario com os dados atuais do documento
		} else {
			//consulta os dados do documento no BD
			$res = $bd->query("SELECT * FROM label_doc WHERE nomeAbrv = '".$tipo."'");
			//se ha resultados
			if(count($res) == 1){
				//seleciona o primeiro e unico
				$res = $res[0];
			} else {
				//retorna erro.
				return "Erro. Tipo n&atilde;o v&aacute;lido.";
			}
			//inicializacao de variaveis
			$cad = '';
			$novo = '';
			$anex = '';
			$anexDoc = '';
			$anexObr = '';
			$anexEmp = '';
			$nomeAbrv = '<input type="text" size="5" maxlength="5" value="'.$res['nomeAbrv'].'" disabled="disabled" />'.'<input type="hidden" name="nomeAbrv" value="'.$res['nomeAbrv'].'" />';
			//carrega o conteudo do aquivo de modelo do documento
			if($res['template'] != ''){
				$res['template'] = file_get_contents('templates/'.$res['template']);
			}
			//checa o campo de cadastravel se o documento eh cadastravel
			if($res['cadAcaoID'] != 0) {
				$cad = 'checked="checked"';
			}
			//checa o campo de criavel se o documento eh criavel
			if($res['novoAcaoID'] != 0) {
				$novo = 'checked="checked"';
			}
			//checa o campo de anexavel se ha a acao de anexar
			if(strpos($res['acoes'], "13") !== false){
				$anex = 'checked="checked"';
			}
			//checa o campo de possivel anexar documentos
			if($res['docAnexo']){
				$anexDoc = 'checked="checked"';
			}
			//checa o campo de possivel anexar obras
			if($res['obra']){
				$anexObr = 'checked="checked"';
			}
			//checa o campo de possivel anexar empresa
			if($res['empresa']){
				$anexEmp = 'checked="checked"';
			}
			
			//campos
			$campoInput = '<input type="hidden" name="campos" id="campos" value="'.$res['campos'].'," />';
			$campoHTML = '';
			$campos = explode(',',$res['campos']);
			//para cada campo adicionado ao documento
			foreach ($campos as $c) {
				$emi = '';
				$emiPrin = '';
				$cBusca = '';
				$cIndice = '';
				//verifica se o campo consta como emitente
				if ($res['emitente'] != ''    && strpos($res['emitente'], $c) !== false) {
					$emi = 'checked="checked"';
				}
				//verifica se eh o primeiro emitente (que vai aparecer na busca/doc pendentes)
				if ($res['emitente'] != ''    && strpos($res['emitente'], $c) === 0) {
					$emiPrin = 'checked="checked"';
				}
				//verifica se o campo eh de busca
				if ($res['campoBusca'] != ''  && strpos($res['campoBusca'], $c) !== false) {
					$cBusca = 'checked="checked"';
				}
				//verifica se o campo eh indice
				if ($res['campoIndice'] != '' && strpos($res['campoIndice'], $c) !== false) {
					$cIndice = 'checked="checked"';
				}
				//seleciona os dados do campo
				$campoData = $bd->query("SELECT * FROM label_campo WHERE nome = '$c'");
				$campoData = $campoData[0];
				//gera uma linha da tabela com os dados lidos
				$campoHTML .= '<tr id="'.$campoData['nome'].'Det" class="c">
								<td class="c"><b>'.$campoData['label'].'</b></td>
								<td class="c" style="text-align: center;">'.$campoData['tipo'].'</td>
								<td class="c" style="text-align: center;"> <input type="checkbox" name="'.$campoData['nome'].'_emi" value="1" '.$emi.' /> </td>
								<td class="c" style="text-align: center;"> <input type="radio"    name="emiPrinc" value="'.$campoData['nome'].'" '.$emiPrin.' /> </td>
								<td class="c" style="text-align: center;"> <input type="checkbox" name="'.$campoData['nome'].'_campoBusca" value="1" '.$cBusca.' /> </td>
								<td class="c" style="text-align: center;"> <input type="radio"    name="campoIndice" value="'.$campoData['nome'].'" '.$cIndice.' /> </td>
								<td class="c"><a href="javascript:void(0);" onclick="excluirCampo(\''.$campoData['nome'].'\')">[Excluir]</a></td>
								</tr>';
			}
		}
		//codigo HTML do formulario de edicao do tipo de documento
		$html = '<script type="text/javascript" src="scripts/adm.js"></script>
			<form action="adm.php?area=dc&amp;acao=salvar" method="post"><input type="hidden" name="action" value="'.$_GET['acao'].'" />
			<table style="width: 100%; border: 0" cellpadding="2" cellspacing="2">
				<tbody>
					<tr>
						<td width="230"><b>Nome do Documento:</b></td>
						<td>'.$campoInput.'
						<input type="text" name="nome" size="35" maxlength="200" value="'.$res['nome'].'" /></td>
						<td rowspan="6" width="300"><div id="tdajuda"></div></td>
					</tr>
					<tr>
						<td><b>Nome Abreviado</b> (at&eacute; 5 caracteres):</td>
						<td>'.$nomeAbrv.'</td>
					</tr>
					<tr>
						<td><b>Campos:</b></td>
						<td>
							<table width="100%" id="camposDet"><tbody>
								<tr>
									<td class="c"><b>Nome</b></td>
									<td class="c" style="text-align: center; width:100px;"><b>tipo</b></td>
									<td class="c" style="text-align: center; width:60px;"><b>Emitente</b></td>
									<td class="c" style="text-align: center; width:60px;"><b>Emitente Princ.</b></td>
									<td class="c" style="text-align: center; width:60px;"><b>Busca</b></td>
									<td class="c" style="text-align: center; width:60px;"><b>&Iacute;ndice</b></td>
									<td class="c" style="text-align: center; width:60px;"></td>
								</tr>
								'.$campoHTML.'
							</tbody></table>
							<div style="display:block; width:100%"></div>
							<div id="novoCampo"><a href="javascript:void(0)" onclick="javascript:addCampo(1);">Adicionar</a></div>
							<br />
						</td>
					</tr>
					<tr>
						<td><b>Composi&ccedil;&atilde;o do n&uacute;mero:</b></td>
						<td><input type="text" name="numComp" size="40" value="'.$res['numeroComp'].'" /></td>
					</tr>
					<tr>
						<td><b>A&ccedil;&otilde;es:</b></td>
						<td>
							<input name="cad" type="checkbox"  '.$cad.'     value="1" /> &Eacute; poss&iacute;vel cadastrar esse documento.<br />
							<input name="new" type="checkbox"  '.$novo.'    value="1" /> &Eacute; poss&iacute;vel criar esse documento.<br />
							<input name="anex" type="checkbox" '.$anex.'    value="1" /> &Eacute; poss&iacute;vel anexar arquivos a esse documento.<br />
							<input name="doc" type="checkbox"  '.$anexDoc.' value="1" /> &Eacute; poss&iacute;vel anexar outros documentos a este.<br />
							<input name="obr" type="checkbox"  '.$anexObr.' value="1" /> &Eacute; poss&iacute;vel anexar obras a esse documento.<br />
							<input name="emp" type="checkbox"  '.$anexEmp.' value="1" /> &Eacute; poss&iacute;vel anexar empresas esse documento.<br />
						</td>
					</tr>
					<tr>
						<td><b>Template:</b> (apenas cria&ccedil;&atilde;o de doc)</td>
						<td><script type="text/javascript" src="CKEditor/ckeditor.js"></script>
							<textarea name="template" class="ckeditor" style="width:100%" rows="20" cols="10">'.$res['template'].'</textarea></td>
					</tr>
					<tr>
						<td colspan="2"><center><input type="submit" value="Salvar" /></center></td>
					</tr>
				</tbody>
			</table>
			</form>
			<div id="configCampo" style="display:none; border: 2px solid #BE1010; background-color: #D8D8D8; position: fixed; top: 150px; left: 35%; width: 500px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td style="background-color:#BE1010; color: white;"><b>Adicionar Campo</b></td><td style="background-color:#BE1010; text-align:right; width: 50px;"><b><a href="javascript:closeCampo();" style="color:#D8D8D8;">Fechar</a></b></td><td style="background-color:#D8D8D8; border: 1px solid #BE1010; width:12px;text-align: center;"><b><a href="javascript:closeCampo();">X</a></b></td></tr></tbody></table>
			<div id="configCampoCont" style="padding: 5px;"></div>
			</div>';
		return $html;
	}
	/**
	 * Efetua a gravacao dos dados do novo documento e cria/modifica as tabelas no BD
	 * @param mysql_link $bd
	 */
	function salvaDoc($bd){
		//faz a leitura dos dados enviados
		$nome = htmlentities($_POST['nome']);
		$nomeAbrv = $_POST['nomeAbrv']; 
		$campos = $_POST['campos'];
		//inicializacao das variaveis
		if(isset($_POST['emiPrinc'])) {
			$emitente = $_POST['emiPrinc'].',';
		} else {
			$emitente = '';
		}
		if(isset($_POST['cIndice'])){
			$campoIndice = $_POST['cIndice'];
		} else {
			$campoIndice = '';
		}
		//se o documento for novo, cadastra a acao de ver o documento
		if($_POST['action'] == 'novo')
			$bd->query("INSERT INTO label_acao (nome, abrv) VALUES ('Ver ".$nome."','ver')");
		// consulta o ID da acao de ver o documento
		$res = $bd->query("SELECT id FROM label_acao WHERE nome = 'Ver ".$nome."'");
		$verID = $res[0]['id'];
		//se o documento for novo, cadastra a acao de despachar o documento
		if($_POST['action'] == 'novo')
			$bd->query("INSERT INTO label_acao (nome, abrv) VALUES ('Despachar ".$nome."','desp')");
		//consulta o ID da acao de despachar o documento
		$res = $bd->query("SELECT id FROM label_acao WHERE nome = 'Despachar ".$nome."'");
		$despID = $res[0]['id'];
		
		if(isset($_POST['new'])) {
			//se foi marcado que o documento eh criavel, consulta o ID de novo documento
			$res = $bd->query("SELECT id FROM label_acao WHERE nome = 'Novo ".$nome."'");
			if(count($res) == 0){
				//se nao houver ID de novo documento, cria nova acao
				$bd->query("INSERT INTO label_acao (nome, abrv) VALUES ('Novo ".$nome."','novo')");
				$res = $bd->query("SELECT id FROM label_acao WHERE nome = 'Novo ".$nome."'");
			}
			//seleciona o ID de criacao
			$newID = $res[0]['id'];
		} else {
			//se nao for criavel, id de criacao = 0
			$newID = 0;
		}
		
		if(isset($_POST['cad'])) {
			//consulta o id da acao de cadastrar documento
			$res = $bd->query("SELECT id FROM label_acao WHERE nome = 'Cadastrar ".$nome."'");
			if(count($res) == 0){
				//se nao houver, cadastra acao
				$bd->query("INSERT INTO label_acao (nome, abrv) VALUES ('Cadastrar ".$nome."','cad')");
				$res = $bd->query("SELECT id FROM label_acao WHERE nome = 'Cadastrar ".$nome."'");
			}
			//seleciona o ID de cadastro
			$cadID = $res[0]['id'];
		} else {
			//senao ID de cadastro = 0
			$cadID = 0;
		}
		//inicializa variavel de anexo
		if(isset($_POST['anex'])) {
			$acoes = '13';
		} else {
			$acoes = '';
		}
		//inicializa variaveis de anexos
		$documento = 0;
		$obra = 0;
		$empresa = 0;
		
		if(isset($_POST['doc'])) {
			$documento = 1;
		}
		if(isset($_POST['obr'])) {
			$obra = 1;
		}
		if(isset($_POST['emp'])) {
			$empresa = 1;
		}
		
		//salvando template
		if($_POST['template'] != '') {
			if(file_put_contents('templates/modelo_'.$nomeAbrv.'.html', $_POST['template']) === false) {
				return false;
			}
			$template = 'modelo_'.$nomeAbrv.'.html';
		} else {
			$template = '';
		}
		//inicializacao de variaveis
		$numComp = $_POST['numComp'];
		
		$campoBusca = '';
		
		$campos = rtrim($campos, ",");
		$campo = explode(',', $campos);
		foreach ($campo as $c) {
			if(isset($_POST[$c.'_emi']) && $_POST[$c.'_emi'] == 1 && strpos($emitente, $c) === false){
				$emitente .= $c.',';
			}
			if(isset($_POST[$c.'_campoBusca']) && $_POST[$c.'_campoBusca'] == 1){
				$campoBusca .= $c.','; 
			}
		}
		//retira a virgula
		$emitente = rtrim($emitente,",");
		$campoBusca = rtrim($campoBusca,",");
		//se for adicao de novo tipo de documento
		if($_POST['action'] == 'novo'){
			//insere o novo tipo de documento
			$sql = "INSERT INTO label_doc (nome,nomeAbrv,campos,emitente,numeroComp,cadAcaoID,novoAcaoID,verAcaoID,despAcaoID,tabBD,CampoIndice,campoBusca,acoes,obra,empresa,docAnexo,template)
					VALUES ('$nome','$nomeAbrv','$campos','$emitente','$numComp',$cadID,$newID,$verID,$despID,'doc_$nomeAbrv','$campoIndice','$campoBusca','$acoes',$obra,$empresa,$documento,'$template')";
			//cria tabela do banco de dados
			$createTable = "CREATE TABLE doc_$nomeAbrv (
							id int(5) NOT NULL AUTO_INCREMENT,";
			//para cada campo do documento, cria um atributo na tabela do BD dependendo do tipo.
			foreach ($campo as $c){
				$r = $bd->query("SELECT tipo FROM label_campo");
				$tipo = $r[0]['tipo'];
				$createTable .= $c;
				if($tipo == 'documentos' || $tipo == 'textarea' || $tipo == 'composto') $createTable .= " text NOT NULL,";
				if($tipo == 'input') $createTable .= " varchar(200) NOT NULL DEFAULT '',";
				if($tipo == 'select') $createTable .= " varchar(50) NOT NULL DEFAULT '',";
				if($tipo == 'userID' || $tipo == 'autoincrement' || $tipo == 'anoSelect') $createTable .= " int(5) NOT NULL DEFAULT 0,";
				if($tipo == 'yesno' || $tipo == 'checkbox') $createTable .= " int(1) NOT NULL DEFAULT 0,";
			}
			$createTable .= "PRIMARY KEY (id) ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
			//cria nova tabela
			$bd->query($createTable);
		//se for edicao de um tipo de documento novo
		} elseif ($_POST['action'] == 'edit'){
			//le os campos atuais
			$cols = $bd->query("SELECT campos FROM label_doc WHERE nomeAbrv='".$nomeAbrv."'");
			//atualiza a tupla deste tipo de documento no BD
			$sql = "UPDATE label_doc
					SET nome='$nome',campos='$campos',emitente='$emitente',numeroComp='$numComp',cadAcaoID=$cadID,novoAcaoID=$newID,verAcaoID=$verID,despAcaoID=$despID,
					tabBD='doc_$nomeAbrv',CampoIndice='$campoIndice',campoBusca='$campoBusca',acoes='$acoes',obra=$obra,empresa=$empresa,docAnexo=$documento,template='$template'
					WHERE nomeAbrv = '$nomeAbrv'";
			//altera a tabela no bd para criar atributos para eventuais novos campos
			$sqlAlter = "ALTER table doc_$nomeAbrv ";
			//para cada campo enviado
			foreach (explode(",", $campos) as $c) {
				//se for campo novo, cria atributo no BD para ele
				if(strpos($cols[0]['campos'], $c) === false){
					$r = $bd->query("SELECT tipo FROM label_campo");
					$tipo = $r[0]['tipo'];
					//tipo do atributo depende do tipo de campo
					if($tipo == 'documentos' || $tipo == 'textarea' || $tipo == 'composto') $sqlAlter .= "ADD $c text, ";
					if($tipo == 'input') $sqlAlter .= "ADD $c varchar(200) NOT NULL DEFAULT '', ";
					if($tipo == 'select') $sqlAlter .= "ADD $c varchar(50) NOT NULL DEFAULT '', ";
					if($tipo == 'userID' || $tipo == 'autoincrement' || $tipo == 'anoSelect') $sqlAlter .= "ADD $c int(5) NOT NULL DEFAULT 0, ";
					if($tipo == 'yesno' || $tipo == 'checkbox') $sqlAlter .= "ADD $c int(1) NOT NULL DEFAULT 0, ";
				}
			}
			//altera a tabela
			$bd->query($sqlAlter);
		}
		//executa a consulta de atualizacao/insercao
		$add = $bd->query($sql);
		//feedback
		if($add)
			return "Documento modificado com sucesso.";
		else
			return "Erro durante a opera&ccedil;&atilde;o.";
	}
	
	function excluiDoc($bd) {
		//excluir um tipo de documento apenas bloqueia a cracao e cadastro de documentos daquele tipo
		//os documentos ja cadastrados nao serao excluidos.
		if($bd->query("UPDATE label_doc SET cadAcaoID=0,novoAcaoID=0 WHERE nomeAbrv='".$_GET['tipoDoc']."'"))
			return 'Tipo de documento excluido. Os Documentos desse tipo j&aacute; cadastrados n&atilde;o ser&atilde;o excluidos.';
			
	}
	
	function showPermGroups($bd){
		//carrega os nomes de grupos
		$nomes = $bd->query("SELECT * FROM label_grupos ORDER BY id");
		//carrega os nomes de acoes e permissoes
		$acao = $bd->query("SELECT * FROM label_acao");
		//criacao do cod HTML do cabecalho
		$html = '<h3>Tipos Permiss&atilde;o por Grupo</h3>
		<form action="adm.php?area=pe&amp;acao=salvar" method="post">
		<table><tbody>
		<tr><td class="c"><b>A&ccedil;&atilde;o</b></td>';
		//cria 1a linha com os nomes de grupos
		for ($i = 0; $i < count($nomes); $i++) {
			$html .= '<td class="c"><b>'.$nomes[$i]["nome"]."</b></td>";
		}
		//para cada acao, cria uma linha com os checkboxes de permissoes
		foreach ($acao as $aNum => $a) {
			//coloca o nome da acao
			$html .= '<tr class="c"><td class="c" width="200">'.$a['nome'].'</td>';
			for ($i = 0; $i < count($nomes); $i++) {
				//e as colunas com os checkboxes checados se tal grupo tem permissao
				if($a['G'.$nomes[$i]['id']] == 1) {
					$checkbox = '<input type="checkbox" name="'.($aNum+1).'G'.$nomes[$i]['id'].'" value="1" checked />';
				} else {
					$checkbox = '<input type="checkbox" name="'.($aNum+1).'G'.$nomes[$i]['id'].'" value="1" />';
				}
				$html .= '<td class="c" style="text-align:center">'.$checkbox."</td>";
			}
			$html .= '</tr>';
		}
		//cria ultima linha com o botao para enviar
		$html .= '<tr><td align="center"> <input type="submit" value="Salvar" /></td></tr>
		</tbody></table></form>';
		//retorna o codigo HTML
		return $html;
	}
	/**
	 * Salva as permissoes editadas no formulario no banco de dados
	 */
	function salvaPermissoes($bd){
		//consulta a quantidade de grupos cadastrados
		$qgrupos = count($bd->query("SELECT id FROM label_grupos"));
		//consulta a quantidade de acoes cadastradas
		$qacoes = count($bd->query("SELECT id FROM label_acao"));
		//para cada acao, se o grupo tiver permissao
		for ($i = 1; $i <= $qacoes; $i++) {
			$sql = "UPDATE label_acao SET ";
			for ($j = 1; $j <= $qgrupos; $j++) {
				//se o grupo tiver permissao, seta Gx = 1 na coluna da acao
				if(isset($_POST[$i."G".$j]) && $_POST[$i."G".$j]) $sql .= " G$j=1,";
				else $sql .= " G$j=0,";
			}
			//retira virgula sobresalente
			$sql = rtrim($sql,",");
			//finaliza a consulta
			$sql .= " WHERE id=$i";
			//realiza a consulta
			$res = $bd->query($sql);
			//se a consulta for mal sucedida, retorna feedback
			if(!$res){
				return "Erro ao atualizar dados no Banco de Dados";
			}
		}
		//senao, retorna feedback positivo
		return "Dados atualizados com sucesso!";
	}
	
	/**
	 * Gera a tabela para gerenciamento de grupos
	 * @param mysql_link $bd
	 */
	function showGrupos($bd){
		//monta as primeiras tags da tabela da tabela/cabecalho
		$html = '<h3>Grupos de Permiss&atilde;o</h3>
		<a href="adm.php?area=gr&amp;acao=novo">Adicionar Grupo</a><br /><br />
		<table width="500">
		<tbody>
		<tr><td><b>Nome</b></td><td></td><td></td></tr>';
		//seleciona o nome de todos os grupos
		$grupos = $bd->query("SELECT nome,id FROM label_grupos");
		//para cada grupo cadastrado
		foreach ($grupos as $g) {
			//cria a tag HTML de uma linha: nome|editar|exluir
			$html .= '<tr class="c">
			<td>'.$g['nome'].'</td><td><a href="adm.php?area=gr&amp;acao=edit&amp;id='.$g['id'].'">Editar</a></td>
			<td><a href="adm.php?area=gr&amp;acao=excl&amp;id='.$g['id'].'">Excluir</a></td>
			</tr>';
		}
		//fecha as tags
		$html .= '</tbody>
		</table>
		';
		//retorna o codigo
		return $html;
	}
	
	/**
	 * Mostra formulario para editar os dados dos grupos
	 * @param mysql_link $bd
	 * @param int $id
	 */
	function showEditGrupoForm($bd, $id = null){
		//se id eh passado
		if($id){
			//retira dados desse ID
			$res = $bd->query("SELECT nome FROM label_grupos WHERE id = $id");
			//se consulta foi bem sucedida (aka. ID eh valido)
			if(count($res)) {
				//preenche o nome do grupo e id
				$nome = $res[0]['nome'];
				$id = $id;
			} else {
				//se ID invalido, cria formulario em banco
				$nome = '';
				$id = 0;
			}
		} else {
			//se id = null,cria formulario em branco
			$nome = '';
			$id = 0;
		}
		//monta o cod HTML do formulario
		$html = '<form action="adm.php?area=gr&amp;acao=salvar" method="post">
		<table>
		<tr class="c"><td>ID:</td><td><input type="text" name="id" value="'.$id.'" size=2 disabled="disabled" /></td></tr>
		<tr class="c"><td>Nome:</td><td><input type="text" name="nome" value="'.$nome.'" size=30 /><input type="hidden" name="id" value="'.$id.'" /></td></tr>
		<tr class="c"><td colspan="2"><input type="submit" value="Enviar" /></td></tr>
		</table>
		</form>'; 
		//retorna codigo HTML
		return $html;
	}
	
	/**
	 * realiza exclusao de um grupo
	 * @param mysql_link $bd
	 */
	function excluiGrupo($bd){
		//se nao houver um ID especificado, retorna erro
		if(!isset($_GET['id'])) {
			return "Faltam dados para salvar os dados corretamente. Nenhum dado foi salvo.";
		}
		//para excluir um grupo sao realizados os seguintes procedimento:
		//1-Retira a coluna relativa as permissoes do grupo da tabela de acoes
		//2-Exclui a tupla correspondente aquele grupo na tabela de grupos
		//3-Coloca permissao minima para os integrantes do grupo exluido
		if($bd->query("ALTER TABLE label_acao DROP G".$_GET['id']) &&
			$bd->query("DELETE FROM label_grupos WHERE id=".$_GET['id']) &&
			$bd->query("UPDATE usuarios SET gid=1 WHERE gid=".$_GET['id'])){
			//retorna feedback positivo se as 3 partes forem bem sucedidas
			return "Grupo exclu&iacute;do com sucesso.";
		}
		//senao, retorna mensagem de erro
		return "Erro ao deletar as bases de dado.";
		
	}
	
	/**
	 * salva os dados do grupo
	 * @param mysql_link $bd
	 */
	function salvaGrupo($bd){
		//se nao foi enviado nome ou id do grupo(mesmo que seja 0) retorna erro
		if(!isset($_POST['id']) || !isset($_POST['nome'])){
			return "Faltam dados para salvar os dados corretamente. Nenhum dado foi salvo.";
		}
		//se id=0 faz criacao do documento
		if ($_POST['id'] == 0){
			//insere a tupla correspondente na tabela de grupos
			$res = $bd->query("INSERT INTO label_grupos (nome) VALUES ('".$_POST['nome']."')");
			//se insercao bem sucedida
			if(count($res)){
				//consulta o ID do grupo criado
				$res = $bd->query("SELECT id FROM label_grupos WHERE nome = '".$_POST['nome']."'");
				$id = $res[0]['id'];
				//adiciona a coluna desse grupo na tabela de acoes (permissoes)
				$res = $bd->query("ALTER TABLE label_acao ADD G".$id." BOOLEAN NOT NULL DEFAULT 0");
			}
		//se id!=0 eh a edicao de um grupo
		} else {
			//apenas atualiza o nome no BD
			$res = $bd->query("UPDATE label_grupos SET nome = '".$_POST['nome']."' WHERE id = ".$_POST['id']);
		}
		//se tudo deu certo, retorna feedback positivo
		if($res){
			return 'Dados salvos com sucesso. Para editar as permiss&otilde;es, <a href="adm.php?area=pe&amp;acao=geren">aqui</a>.';
		//senao, retorna mensagem de erro
		} else {
			return "Erro ao salvar os dados";
		}
	}
	
	/**
	 * Mostra formulario para edicao de emresas
	 * @param mysql_link $bd
	 * @param int $id
	 */
	function showEditEmprForm($bd, $id = 0){
		//se ID = 0, cria um formulario em branco para criacao de uma nova empresa
		if($id == 0){
			$empr = array(
			'nome'  => '' ,
			'end'   => '' ,
			'compl' => '' ,
			'cid'   => '' ,
			'est'   => '' ,
			'cep'   => '' ,
			'tel'   => '' ,
			'email' => '' );
		//senao, pre-prenche os campos com dados da empresa cujo id eh passado
		} else {
			$empr = $bd->query("SELECT * FROM empresa WHERE id = ".$id);
			if(count($empr)){
				$empr   =  $empr[0];
				$empr   =  array('nome' => $empr['nome'] ,
				'end'   => $empr['endereco'] ,
				'compl' => $empr['complemento'] ,
				'cid'   => $empr['cidade'] ,
				'est'   => $empr['estado'] ,
				'cep'   => $empr['cep'] ,
				'tel'   => $empr['telefone'] ,
				'email' => $empr['email'] );
			}
		}
		//monta o codigo HTML do formulario de edicao
		return '<form action="adm.php?area=em&amp;acao=salvar" method="post">
		<table width="100%" border="0"> <input type="hidden" name="id" value="'.$id.'" />
		<tr><td><b>Nome da Empresa:</b> <input id="nome" name="nome" value="'.$empr['nome'].'" size="50" /></td></tr>
		<tr><td><b>Endereço:</b> <input id="end" name="end" value="'.$empr['end'].'" size="60" /></td></tr>
		<tr><td><b>Complemento:</b><input id="compl" name="compl" value="'.$empr['compl'].'" size="55"></td></tr>
		<tr><td><b>Cidade:</b> <input id="cid" name="cid" size="22" value="'.$empr['cid'].'" /> <b>Estado:</b> <input id="est" name="est" size="2" value="'.$empr['est'].'" /> <b>CEP:</b> <input id="cep" name="cep" size="10" value="'.$empr['cep'].'" /></td></tr>
		<tr><td><b>Telefone:</b> <input id="tel" name="tel" size="15" value="'.$empr['tel'].'" /> <b>e-mail:</b> <input id="email" name="email" size="30" value="'.$empr['email'].'" /></td></tr>
		<tr><td><center><input type="submit" value="Cadastrar" /></center></td></tr>
		</table></form>';
	}
	
	/**
	 * Mostra tabela de empresas com links para edicao/exclusao
	 * @param mysql_link $bd
	 */
	function showEmpr($bd){
		$empr = $bd->query("SELECT * FROM empresa");
		//cria perimeira linha da tabela
		$html = '<a href="adm.php?area=em&amp;acao=nova">Adicionar Empresa</a>
			<table><tbody>
			<tr>
			<td class="c"><b>Nome</b></td>
			<td class="c"><b>Endereço</b></td>
			<td class="c"><b>Complemento</b></td>
			<td class="c"><b>Cidade</b></td>
			<td class="c"><b>Estado</b></td>
			<td class="c"><b>CEP</b></td>
			<td class="c"><b>Telefone</b></td>
			<td class="c"><b>E-mail</b></td>
			<td class="c"></td>
			<td class="c"></td>
			</tr>';
		//para cada empresa cadastrada, cria uma linha na tabela
		foreach ($empr as $e) {
			$html .= '<tr class="c"><td class="c">'.$e['nome'].'</td>
			<td class="c">'.$e['endereco'].'</td>
			<td class="c">'.$e['complemento'].'</td>
			<td class="c">'.$e['cidade'].'</td>
			<td class="c">'.$e['estado'].'</td>
			<td class="c">'.$e['cep'].'</td>
			<td class="c">'.$e['telefone'].'</td>
			<td class="c">'.$e['email'].'</td>
			<td class="c"><a href="adm.php?area=em&amp;acao=editar&amp;id='.$e['id'].'">Editar</a></td>
			<td class="c"><a href="adm.php?area=em&amp;acao=excl&amp;id='.$e['id'].'">Remover</a></td>
			</tr>';
		}
		//fecha as tags de tabela abertas
		$html .= "</tbody></table>";
		//retorna o codigo html
		return $html;
	}
	
	/**
	 * rotina que salva os dados da empresa
	 * @param mysql_link $bd
	 */
	function salvaEmpr($bd){
		//se for cadastro de nova empresa, insere no BD
		if($_POST['id'] == 0) $sql = "INSERT INTO empresa (nome,endereco,complemento,cidade,estado,cep,telefone,email) VALUES ('".$_POST['nome']."','".$_POST['end']."','".$_POST['compl']."','".$_POST['cid']."','".$_POST['est']."','".$_POST['cep']."','".$_POST['tel']."','".$_POST['email']."')";
		//senao, atualiza os dados da empresa no BD
		else $sql = "UPDATE empresa SET nome = '".$_POST['nome']."' , endereco = '".$_POST['end']."' , complemento = '".$_POST['compl']."' , cidade = '".$_POST['cid']."' , estado = '".$_POST['est']."' , cep = '".$_POST['cep']."' , telefone = '".$_POST['tel']."' , email = '".$_POST['email']."' WHERE id = ".$_POST['id'];
		//se consulta bem sucedida, mostra feedback positivo.
		if($bd->query($sql)){
			return "Dados salvos com sucesso";
		//senao, mostra mensagem de erro
		} else {
			return "Erro ao salvar dados.";
		}
	}
	
	/**
	 * exclui entrada de uma empresa
	 * @param mysql_link $bd
	 */
	function excluiEmpr($bd){
		//se ID nao for passado por parametro, mostra mensagem de erro
		if(!isset($_GET['id'])) {
			return "Faltam dados para salvar os dados corretamente. Nenhum dado foi salvo.";
		}
		//senao, eclui empresa do BD e retorna mensagem de feedback.
		if($bd->query("DELETE FROM empresa WHERE id=".$_GET['id'])){
			return "Empresa exclu&iacute;da com sucesso.";
		}
	}
	
	/**
	 * Funcao para atualizar unidades a partir de arquivo CSV
	 * @param mysql_link $bd
	 */
	function updateUnidades($bd) {
		//mostra tela de confirmacao se nao achar flag de confirmacao
		if(!isset($_GET['confirm']))
		//retorna mensagem de confirmacao
			return '<h3>Instru&ccedil;&otilde;es para atualiza&ccedil;&atilde;o da tabela de unidades</h3>
			1. Converta o arquivo de unidades/&oacute;rg&atilde;os para um CSV com a seguinte estrutura em codifica&ccedil;&atilde;o Latin1:<br />
			codigo,sigla,nome da unidade/&oacute;rg&atilde;o<br />
			(n&atilde;o h&aacute; obrigatoriedade de colocar aspas e <b>n&atilde;o &eacute; permitido v&iacute;rgula na sigla</b>)
			<br /><br />
			2. Coloque o CSV na pasta BD/ com o nome &quot;unidades.csv&quot;
			<br /><br />
			3. Clique <a href="adm.php?area=un&amp;acao=atual&amp;confirm">aqui</a> para iniciar a atualiza&ccedil;&atilde;o.
			';
		//inicializacao de variaveis
		$novos = 0;
		$alterados = 0;
		//array para retirar acentos
		$ca = array("á","ã","à","â","é","ê","è","í","î","ï","ó","õ","ò","ô","ú","û","ü","ç",
		            "Á","Ã","À","Â","É","Ê","È","Í","Î","Ï","Ó","Õ","Ò","Ô","Ú","Û","Ü","Ç");
		$sa = array("a","a","a","a","e","e","e","i","i","i","o","o","o","o","u","u","u","c",
				    "A","A","A","A","E","E","E","I","I","I","O","O","O","O","U","U","U","C");
		//le os dados do arquivo de unidades e guarda em um array
		$unidades = file('BD/unidades.csv');
		//para cada unidade lida
		foreach ($unidades as $un) {
			//separa os campos separados por virgula
			$un = explode(',', str_replace(array('"',"'"), array('',''), str_replace($ca,$sa,$un)),3);
			//adiciona os zeros para completar o tamanho correto do cod da unidade
			$un[0] = addZero($un[0]);
			//se nao houver nome da unidade, coloca vazio
			if(!isset($un[2])) $un[2] = '';	
			//verifica se ja tem alguma unidade com aquele codigo cadastrada
			$unbd = $bd->query("SELECT * FROM unidades WHERE id = '".$un[0]."'");
			//se ha unidade com esse codigo cadastrada
			if(count($unbd)){
				//seleciona a primeira tupla retornada
				$unbd = $unbd[0];
				//se o nome e a sigla lidas foresm iguais ao que esta cadastrado no BD
				if($un[1] == $unbd['sigla'] && $un[2] == $unbd['nome']){
					//entao nao precisa modificar nada e passa para a proxima unidades
					continue;
				//senao deve-se atualizar a base de dados
				} else {
					//realiza a atualizacao da unidade
					$un[2] = rtrim($un[2],"\n");
					$ok = $bd->query("UPDATE unidades SET sigla='".$un[1]."', nome='".$un[2]."' WHERE id = '".$un[0]."'");
					//marca que mais uma unidade foi modificada
					$alterados += 1;
				}
			//senao achar um documento com aquele codigo no BD
			} else {
				//insere umanova tupla no BD com o codigo lido
				$un[2] = rtrim($un[2],"\n");
				print "INSERT INTO unidades (id,sigla,nome) VALUES ('".$un[0]."','".$un[1]."','".$un[2]."')";
				$ok = $bd->query("INSERT INTO unidades (id,sigla,nome) VALUES ('".$un[0]."','".$un[1]."','".$un[2]."')");
				//marca que mais uma unidade foi marcada
				$novos += 1;
			}
			//se atualizacao/insercao foi bem sucedida
			if($ok) {
				//algoritmo para montar o 'caminho'
				$subs = explode(".", $un[0]);
				//inicializa a variavel do 'caminho'
				$sub = '';
				//e a de concatenacao dos codigos
				$subsConcat = '';
				//para cada 'area' do codigo
				foreach ($subs as $s) {
					//concartena o cod da area
					$subsConcat .= $s;
					//completa o cod com zeros para match no BD
					if(addZero($subsConcat) == $un[0])
						//se for igual ao codigo 'total', para o algoritmo
						break;
					//verifica qual a sigla da 'area' selecionada
					$r2 = $bd->query("SELECT sigla FROM unidades WHERE id = '".addZero($subsConcat)."'");
					//se nao achar a sigla da 'area' acima, para o algoritmo
					if(count($r2) == 0)
						break;
					//senao, monta o caminho
					$sub .= $r2[0]['sigla'].' / ';
					//e recria o codigo colocando o 'ponto'
					$subsConcat .= '.';
				}
				//ao final, se gerar caminho diferente, grava o caminho gerado
				if (!isset($unbd['sub']) || $sub != $unbd['sub']) {
					$bd->query("UPDATE unidades SET sub = '$sub' WHERE id='".$un[0]."'");
				} 
			//se insercao/atualizacao mal sucedida, entao mostra mesagem de erro
	     	} else {
				return "ERRO nas consultas ao banco de dados. <br /> Tuplas alteradas: $alterados <br /> Tuplas adicionadas: $novos";
			}
		}
		//se tudo ocorreu ok, mostra mensagem de sucesso.
		return "Dados atualizados com sucesso. <br /> Tuplas alteradas: $alterados <br /> Tuplas adicionadas: $novos";
	}
	
	/**
	 * Funcao auxiliar para colocar zeros de modo a todas as unidades terem o mesmo numero de digitos
	 * @param string $id
	 */
	function addZero($id) {
		if (strlen($id) == 1)  return '0'.$id.'.00.00.00.00.00';
		if (strlen($id) == 2)  return $id.'.00.00.00.00.00';
		if (strlen($id) == 5)  return $id.'.00.00.00.00';
		if (strlen($id) == 8)  return $id.'.00.00.00';
		if (strlen($id) == 11) return $id.'.00.00';
		if (strlen($id) == 14) return $id.'.00';
		return $id;	
	}
?>