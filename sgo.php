<?php
	/**
	 * @version 0.0 20/4/2011 
	 * @package geral
	 * @author Mario Akita
	 * @desc pagina que lida com os modulos de gerenciamento de obras
	 */
	include_once('includeAll.php');
<<<<<<< HEAD
	includeModule('sgo');

	//verifica se usuario esta logado
	checkLogin(6);
	
	//inicialização de variaveis
	$html = new html($conf);
	$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);
	
	//configurações da pagina HTML
	$html->setTemplate($conf['template']);
	$html->header = "Ger&ecirc;ncia de Obras";
	$html->campos['codPag'] = showCodTela();
	$html->title .= "SGO";
	$html->user = $_SESSION["nomeCompl"];
	$html->path = showNavBar(array());
	
	$sgo = new SGO();
	//seleciona a acao a ser efetuada
	if(isset($_GET['acao'])) {
	
		if($_GET['acao'] == 'buscar') {//acao buscar obra
			$html = $sgo->montaBuscaObra($html, $conf);
						
		} elseif($_GET['acao'] == 'cadastrar') {//acao cadastrar obra
			$html = $sgo->montaCadObra($html, $conf);
		
		} elseif($_GET['acao'] == 'salvarNova') {//acao salvar nova obra
			$html = $sgo->montaSalvaObra($html, $conf);
			
		} elseif($_GET['acao'] == 'ver') {//acao ver detalhes da obra
			if (!isset($_GET['obraID'])) {
				verObraFeedback(array("success" => false, "errorNo" => 1, "errorFeedback" => "nenhuma obra selecionada"));
			} else {
				$html->content[4] = '
					</div>
					<div id="c4" class="boxCont">
					{$content4}';
				$html = $sgo->montaVerObra($html, $conf);
			}
			
		} elseif($_GET['acao'] == 'edit') {//acao ver detalhes da obra
			if (!isset($_GET['obraID'])) {
				verObraFeedback(array("success" => false, "errorNo" => 1, "errorFeedback" => "nenhuma obra selecionada"));
			} else {
				$html = $sgo->montaEditObra($html, $conf, $_SESSION["perm"]);
			}
			
		} elseif($_GET['acao'] == 'cadHome') {//acao salvar nova obra
			$html->menu = showMenu($conf['template_menu'],$_SESSION["perm"],2,$bd);
			$html->content[1] = showHomeObrasGmaps();

		} elseif($_GET['acao'] == 'salvar') {
		if (!isset($_GET['obraID'])) {
				verObraFeedback(array("success" => false, "errorNo" => 1, "errorFeedback" => "nenhuma obra selecionada"));
			} else {
				$html = $sgo->salvaObra($html, $conf, $_SESSION["perm"], $_GET['obraID'], $_POST);
			}
			
		} elseif($_GET['acao'] == 'salvaRec') {
			if((isset($_GET['obraID']) && !$_GET['obraID']) || !isset($_GET['rec_valor']) || !isset($_GET['rec_origem']) || !isset($_GET['rec_prazo']))
				$ret = array(array('success' => false));
			else
				$ret[] = $sgo->salvaRecAJAX($_GET['obraID'], array('montante' => $_GET['rec_valor'], 'origem' => htmlentities(urldecode($_GET['rec_origem'])), 'prazo' => $_GET['rec_prazo']));
			
			print json_encode($ret);
			exit();

		} elseif ($_GET['acao'] == 'salvaEtapa') {
			//TODO
			if(!isset($_GET['obraID']) || !isset($_GET['tipoID']) || !isset($_GET['respID']) || !isset($_GET['procID']))
				$ret = array(array('success' => false));
			else
				$ret[] = $sgo->salvaEtapaAJAX($_GET['obraID'], array('tipoID' => $_GET['tipoID'], 'respID' => $_GET['respID'], 'procID' => $_GET['procID']));
				
			print json_encode($ret);
			exit();
		
		} else {
			$html->content[1] = verObraFeedback(array("success" => false, "errorNo" => 1, "errorFeedback" => "P&aacute;gina inv&aacute;lida"));
		}
	} else {
		$html->content[1] = verObraFeedback(array("success" => false, "errorNo" => 1, "errorFeedback" => "P&aacute;gina inv&aacute;lida"));
	}
=======
	include_once('sgd_modules.php');
	session_start();
	
	checkLogin(6);
	
	$html = new html($conf);
	$bd = new BD($conf["DBLogin"], $conf["DBPassword"], $conf["DBhost"], $conf["DBTable"]);
	
	$html->setTemplate($conf['template']);
	$html->header = "Ger&ecirc;ncia de Obras";
	$html->campos['codPag'] = showCodTela();
	$html->title .= "Em construção";
	$html->user = $_SESSION["nomeCompl"];
	$html->path = showNavBar(array());
	$html->menu = showMenu($conf['template_menu'],$_SESSION["perm"],30,$bd);
	
	$html->content[1] = "Em constru&ccedil;&atilde;o.";
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
	
	$html->showPage();
?>