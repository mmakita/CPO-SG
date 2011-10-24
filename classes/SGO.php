<?php
class SGO {
	private $bd;
	
	/**
	 * Construtor. Apenas inicializa a variavel de BD
	 */
	function __construct() {
		global $bd;
		$this->bd = $bd;
	}
	
	/**
	 * Retorna os scripts para sripts a serem incluidos
	 */
	function getHeadScripts() {
		return '<script type="text/javascript" src="scripts/sgo_cad.js"></script>
				<script type="text/javascript" src="scripts/sgo_bus.js"></script>
				<script type="text/javascript" src="scripts/jquery.autocomplete.js"></script>
				<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />';
	}
	
	/**
	 * Monta tela para busca de obras
	 * @param HTML $html
	 * @param Array $conf
	 */
	function montaBuscaObra($html, $conf) {
		$html->head .= $this->getHeadScripts();
		$html->menu  = showMenu($conf['template_menu'],$_SESSION["perm"],2,$this->bd);
		$html->content[1] = showBuscaObrasForm();
		
		return $html;
	}
	
	/**
	 * Monta tela de formulario para cadastro de obra.
	 * @param HTML $html
	 * @param Array $conf
	 */
	function montaCadObra($html, $conf) {
		$html->head .= $this->getHeadScripts();
		$html->menu = showMenu($conf['template_menu'],$_SESSION["perm"],2,$this->bd);
		
		if(isset($_GET['docOrigemID']) && $_GET['docOrigemID'])
			$ofirID = $_GET['docOrigemID'];
		else
			$ofirID = 0;
		$html->content[1] = showCadObraForm($ofirID);
		
		return $html;
	}
	
	/**
	 * Monta feedback para cadastro de obra
	 * @param HTML $html
	 * @param Array $conf
	 */
	function montaSalvaObra($html, $conf) {
		$html->menu = showMenu($conf['template_menu'],$_SESSION["perm"],2,$this->bd);
		$html->content[1] = salvaNovaObra();
		
		return $html;
	}
	
	/**
	 * Monta a tela para visualizar uma obra (detalhes em nova janela)
	 * @param HTML $html
	 * @param Array $conf
	 */
	function montaVerObra($html, $conf) {
		$obra = new Obra();
		$obra->load($_GET['obraID']);
		
		$html->setTemplate('templates/template_obra_mini.php');
		$html->menu = showObraActionMenu($obra, $_SESSION['perm'], array(9, 10));
		$html->head .= '<script type="text/javascript" src="scripts/sgo_ver.js"></script>';
		
		$html->content[1] = showObraTopMenu();
		$html->content[2] = showObraResumo($obra);
		$html->content[3] = showObraDetalhes($obra);
		$html->content[4] = showObraRecursos($obra);
		$html->content[5] = showObraEtapas($obra);
		$html->content[6] = showObraHistorico($obra);
		
		return $html;
	}
	
	/**
	 * Monta formulario para edição de detalhes da obra
	 * @param HTML $html
	 * @param Array $conf
	 * @param Array $perm
	 */
	function montaEditObra($html, $conf, $perm) {
		$obra = new Obra();
		$obra->load($_GET['obraID']);
		
		$html->menu = showObraActionMenu($obra, null, array('voltar'));
		
		$html->head .= '<script type="text/javascript" src="scripts/sgo_ver.js"></script>';
		
		$html->content[1] = showObraEditForm($obra);
		
		return $html;
	}
	
	/**
	 * Chama o metodo para salvar obra apos cadastro
	 * @param Html $html
	 * @param Array $conf
	 * @param Array $perm
	 * @param int $obraID
	 * @param Array $post
	 */
	function salvaObra($html, $conf, $perm, $obraID, $post){
		$obra = new Obra();
		$obra->load($obraID);
		
		$html->menu = showObraActionMenu($obra, null, array('voltar'));
		
		if(!$perm[9]) {
			$html->content[1] = verObraFeedback(array('success' => false, 'errorNo' => 1, 'errorFeedback' => 'Usuario nao tem permissoes suficientes para realizar esta acao.'));
		} else {
			$html->content[1] = salvaObra($obra, $post);
		}
		
		return $html;
	}
	
	function salvaRecAJAX($obraID, $rec_dados){
		$rec = new Recurso();
		$rec->montante = $rec_dados['montante']; 
		$rec->origem = urldecode($rec_dados['origem']);
		$rec->prazo = trataData($rec_dados['prazo']);

		return $rec->insertRecursoInObra($obraID);
	}
	
	function salvaEtapaAJAX($obraID, $etapa_dados) {
		$etapa = new Etapa($obraID, $etapa_dados['tipoID'], $etapa_dados['procID']);
		$etapa->responsavel = $etapa_dados['respID'];
		$ret = $etapa->save();
		$ret['etapaID'] = $etapa->getID();
		
		return $ret;
	}
}


?>