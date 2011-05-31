<?php
	/**
	 * @version 1.0 20/4/2011 
	 * @package geral
	 * @author Mario Akita
	 * @desc pagina que lida com os modulos de gerenciamento de documentos 
	 */
	include_once('includeAll.php');
	include_once('sgd_modules.php');
	
	//inicia a sessao
	session_start();
	
	//verifica se o usuario esta logado
	checkLogin(6);
	
	//cria uma nova pagina HTML
	$html = new html($conf);
	//seta o texto de cabecalho da pagina
	$html->header = "Ger&ecirc;ncia de Documentos";
	//gera o codigo de tela para esta pagina
	$html->campos['codPag'] = showCodTela();
	//completa o nome de usuario
	$html->user = $_SESSION['nomeCompl'];
	//inicia conexao com o banco de dados
	$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);
	
	if (isset($_GET['acao'])) {
/*VD*/	if(($_GET['acao'] == "ver")||($_GET['acao'] == "desp")||($_GET['acao'] == "anexArq")) {
		//rotina para  ver documento
			if(isset($_GET['docID'])){
				//se o ID do documento estiver especificado, cria variavel e carrega os dados do doc
				$doc = new Documento($_GET['docID']);
				$doc->loadCampos();
			}else{
				//senao, mostra erro: vc quer ver um documento sem numero
				showError(7);
			}
			//verifica permissão
			checkPermission($doc->dadosTipo['verAcaoID']);
			
			//rotina para completar o template, caminho do arquivo e titulo 
			$html->setTemplate($conf['template_mini']);
			$html->path = showNavBar(array(array("url" => "","name" => "Detalhes")),'mini');
			$html->title .= "SGD : Detalhes: ".$doc->dadosTipo['nome']." ".$doc->numeroComp;
			//completa o espaco de menu com as acoes possiveis para o documento
			$html->menu = showAcoes($doc);
			//area 1 - contem os detalhes do documento
			$html->content[1] = showDetalhes($doc);
			//area 2 - detalhes do emissor
			$html->content[2] = showEmissor($doc);
			//area 3 - historico do documento
			$html->content[3] = showHist($doc);
			//area 4 - area para despachar
			$html->content[4] = showDesp('f',getDeptos(),$doc);
			//area 5 - area para anexar arquivo
			$html->content[5] = showAnexar('f',$doc);
			//dependendo da acao, gera JS para esconder/mostrar as areas pertinentes
			if($_GET['acao'] == "ver") {
				//mostra detalhes do doc/emissor e historico. esconde anexar arquivo e despacho
				$html->menu .= '<script type="text/javascript">$(document).ready(function(){$("#c4").hide();$("#c5").hide();});</script>';
			}elseif($_GET['acao'] == "desp"){
				//esconde os detalhes/historico e anexar arquivo. mostra despachar
				$html->menu .= '<script type="text/javascript">$(document).ready(function(){$("#c1").hide();$("#c2").hide();$("#c3").hide();$("#c5").hide();});</script>';
			}elseif($_GET['acao'] == "anexArq"){
				//esconde os detalhes/historico e despachar. mostra anexar arquivo
				$html->menu .= '<script type="text/javascript">$(document).ready(function(){$("#c1").hide();$("#c2").hide();$("#c3").hide();$("#c4").hide();});</script>';
			}
			//loga a acao do usuario no BD para administracao
			doLog($_SESSION['username'], "viu detalhes do documento ".$doc->id." (".$doc->dadosTipo['nome']." ".$doc->numeroComp.")", $bd);

/*CD*/	}elseif ($_GET['acao'] == "cad") {
			//rotina para cadastrar documento
			//caso nao seja especificado tipo de documento a ser cadastrado, mostra erro
			if(!(isset($_GET['tipoDoc']))) showError(7);
			//cria novo doc para ler acaoID
			$doc = new Documento(0);
			$doc->dadosTipo['nomeAbrv'] = $_GET['tipoDoc'];
			$doc->loadTipoData();
			//verifica permissão
			checkPermission($doc->dadosTipo['cadAcaoID']);			
			//rotina para definir template, caminho, titulo, nome de usuario.
			$html->setTemplate($conf['template']);
			$html->path = showNavBar(array(array("url" => "","name" => "Cadastrar Documento")));
			$html->title .= "SGD : Cadastrar Documento";
			//menu principal
			$html->menu = showMenu($conf['template_menu'],$_SESSION["perm"],30,$bd);
			//gera formulario para cadastro de documento
			$html->content[1] = showForm("cad",$_GET['tipoDoc'],$bd);

/*NV*/	}elseif ($_GET['acao'] == "novo") {
			//rotina para cadastrar documento
			//caso nao seja especificado tipo de documento a ser cadastrado, mostra erro
			if(!(isset($_GET['tipoDoc']))) showError(7);
			//cria novo doc para ler acaoID
			$doc = new Documento(0);
			$doc->dadosTipo['nomeAbrv'] = $_GET['tipoDoc'];
			$doc->loadTipoData($bd);
			//verifica permissão
			checkPermission($doc->dadosTipo['novoAcaoID']);
			//inclui no cabecalho os scripts para usar o CKEdit
			$html->head .= '<script type="text/javascript" src="ckeditor/ckeditor.js"></script>';
			//rotina para novo documento
			//rotina para definir template, caminho, titulo, nome de usuario.
			$html->setTemplate($conf["template"]);
			$html->path = showNavBar(array(array("url" => "","name" => "Novo Documento")));
			$html->title .= "SGD : Novo Documento";
			//menu principal
			$html->menu = showMenu($conf['template_menu'],$_SESSION["perm"],30,$bd);
			//gera formulario de novo documento
			$html->content[1] = showForm("novo",$_GET['tipoDoc'],$bd);
			
/*SV*/	}elseif ($_GET['acao'] == "salvar"){
			//rotina para salvar os dados na criacao/cadastro de documento
			//rotina para definir template, caminho, titulo, nome de usuario
			$html->setTemplate($conf["template"]);
			$html->path = showNavBar(array(array("url" => "","name" => "Salvar documento")));
			$html->title .= "SGD : Salvar Documento";
			//menu principal
			$html->menu = showMenu($conf['template_menu'],$_SESSION["perm"],30,$bd);
			//funcao que salva os dados e gera visualizacao dos resultados
			//print_r($_POST);
			$html->content[1] = salvaDados($_POST,$bd);
			
/*BM*/	}elseif ($_GET['acao'] == "busca_mini") {
			//verifica permissao para realizar acao de buscar
			checkPermission(1);
			//rotina para gerar tela de busca de documentos (nova janela)
			//define a acao a ser tomada quando o usuario clica no link, se nao houver nenhuma explicita, considera que eh para mostrar detalhes do documento
			if(!isset($_GET['onclick'])) $_GET['onclick'] = 'ver';
			//rotina para definir template, caminho, titulo, nome de usuario.
			$html->setTemplate($conf["template_mini"]);
			$html->path = showNavBar(array(array("url" => "","name" => "Adicionar Documento")));
			$html->title .= "SGD : Adicionar Documento";
			//gera javascript para ocultar os divs nao utilizados
			$html->content[1] = '<script type="text/javascript">$(document).ready(function(){$("#c2").hide();$("#c3").hide();$("#c4").hide();$("#c5").hide();$(".boxLeft").css("width","0");$(".boxRight").css("width","100%");});</script>';
			//gera formulario de busca de documentos na area 1
			$html->content[1] .= showBuscaForm($_GET['onclick']);
			//gera botao de buscar novamente na area 2
			$html->content[2] = '<center><input type="button" onclick="novaBusca()" value="Buscar novamente" /></center>';
			//gera div de resposta na area 3
			$html->content[3] = '<div id="resBusca" width="100%"></div>';
			
/*BU*/	}elseif ($_GET['acao'] == "buscar") {
			//verifica permissao para realizar acao de buscar
			checkPermission(1);
			//rotina para gerar tela de busca de documentos
			//define a acao a ser tomada quando o usuario clica no link, se nao houver nenhuma explicita, considera que eh para mostrar detalhes do documento
			if(!isset($_GET['onclick'])) $_GET['onclick'] = 'ver';
			//rotina para definir template, caminho, titulo, nome de usuario
			$html->setTemplate($conf["template"]);
			$html->path = showNavBar(array(array("url" => "","name" => "Buscar Documento")));
			$html->title .= "SGD : Buscar Documento";
			$html->menu = showMenu($conf['template_menu'],$_SESSION["perm"],30,$bd);
			//gera formulario de busca na area 1
			$html->content[1] = showBuscaForm($_GET['onclick']);
			//gera novas areas no layour principal
			$contVisible = array(true,false,false);
			$html->content[1] .= addContentBox(2,$contVisible);
			//gera botao de buscar novamente na area 2
			$html->content[2] = '<center><input type="button" onclick="novaBusca()" value="Buscar novamente" /></center>';
			//gera div de resposta na area 3
			$html->content[3] = '<div id="resBusca" width="100%"></div>';
		
/*DP*/	}elseif ($_GET['acao'] == 'despachar'){
			//cria novo documento com o ID especificado
			$doc = new Documento($_POST['id']);
			$doc->bd = $bd;
			$doc->dadosTipo['nomeAbrv'] = $_POST['id'];
			$doc->loadTipoData($bd);
			//verifica permissão
			checkPermission($doc->dadosTipo['despAcaoID']);
			//rotina para efetuar despacho de um arquivo
			if(!isset($_POST['funcID'])) $_POST['funcID'] = false;
			//rotina para definir template, caminho, titulo, nome de usuario	
			$html->title .= "SGD : Despachar Documento";
			$html->setTemplate($conf["template_mini"]);
			$html->menu .= '<script type="text/javascript">$(document).ready(function(){$("#c4").hide();$("#c2").hide();$("#c3").hide();$("#c5").hide();});</script>';
			$html->path = showNavBar(array(array("url" => "","name" => "Despachar Documento")),'mini');
			//retira acentos do conteudo do despacho
			$_POST['despacho'] = htmlentities($_POST['despacho']);
			//efetua o despacho do documento
			$html->content[1] = showDespStatus($doc, array('para' => $_POST['para'], "outro" => $_POST['outro'], 'funcID' => $_POST['funcID'], 'despExt' => $_POST['despExt'],"despacho" => $_POST['despacho']));
			$html->content[1] .= '<br /><a href="sgd.php?acao=ver&docID='.$_POST['id'].'">Voltar para os detalhes do documento.</a>';
			
/*AA*/	}elseif ($_GET['acao'] == 'anexar'){
			//verifica permissão
			checkPermission(13);
			//rotina para efetuar despacho de um arquivo
			$doc = new Documento($_POST['id']);
			//inicializacao de variaveis
			$doc->bd = $bd;
			$doc->loadDados($bd);
			//rotina para definir template, caminho, titulo, nome de usuario	
			$html->title .= "SGD : Anexar Arquivo ao Documento";
			$html->setTemplate($conf["template_mini"]);
			$html->menu .= '<script type="text/javascript">$(document).ready(function(){$("#c4").hide();$("#c2").hide();$("#c3").hide();$("#c5").hide();});</script>';
			$html->path = showNavBar(array(array("url" => "","name" => "Anexar Arquivo ao Documento")),'mini');
			//rotina para anexar um arquivo
			//upload do arquivo
			$html->content[1] = montaRelArq($doc->doUploadFiles(),$bd);
			//salva dados no BD
			$anexoSalvo = $doc->salvaAnexos();
			if ($anexoSalvo === true) {
				$html->content[1] .= "<br />Arquivos anexados com sucesso.<br />";
			}elseif ($anexoSalvo === false){
				$html->content[1] .= "<br /><b>Erro ao anexar arquivos.</B><br />";
			}elseif ($anexoSalvo === 0){
				$html->content[1] .= "<br />N&atilde;o h&aacute; arquivo anexado.<br />";
			}
			$html->content[1] .= '<br /><a href="sgd.php?acao=ver&docID='.$_POST['id'].'">Voltar para os detalhes do documento.</a>';
			
/*ND*/	}elseif ($_GET['acao'] == 'novoDocVar') {
			//tipo de documento e action devem estar especificados explicitamente
			if(!isset($_GET['tipoDoc']) || !isset($_GET['action']))
				showError(11);
			//cria novo doc para ler acaoID
			$doc = new Documento(0);
			$doc->dadosTipo['nomeAbrv'] = $_GET['tipoDoc'];
			$doc->loadTipoData($bd);
			//verifica permissão
			checkPermission($doc->dadosTipo['novoAcaoID']);
			//realiza o tratamento das variaveis
			$dados = trataGetVars($_GET,$bd);
			//define template, barra de navegacao, titulo e menu
			$html->setTemplate($conf["template_mini"]);
			$html->path = showNavBar(array(array("url" => "","name" => "Salvar documento")));
			$html->title .= "SGD : Salvar Documento";
			$html->menu = '<script type="text/javascript">$(document).ready(function(){$("#c2").hide();$("#c3").hide();$("#c4").hide();$("#c5").hide();$(".boxLeft").css("width","0");$(".boxRight").css("width","100%");});</script>';
			//salva o documento no BD
			$html->content[1] = salvaDados($dados,$bd);
		}else {
			//se acao eh invalida, volta para o inicio
			header("Location: index.php");
		}
	} else {
		//se nao ha acao especificado, volta para o inicio
		header("Location: index.php");
	}
	
	
	$html->showPage();
	$bd->disconnect();
?>