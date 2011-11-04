<?php
class Obra {
	
	private $id;
	
	public $codigo;
	
	public $nome;
	
	public $caract;
	
	public $tipo;
	
	public $descricao;
	
	public $area;
	
	public $amianto;
	
	public $ocupacao;
	
	public $residuos;
	
	public $pavimentos;
	
	public $elevador;
	
	public $responsavelProj;
	
	public $responsavelObra;
	
	public $estado;
	
	public $unOrg;
	
	public $local;
	
	public $campus;
	
	public $recursos;
	
	public $etapas;
	
	public $custo;
	
	public $desc_img;
	
	public $visivel;
	
	public $historico;
	
	private $bd;
	/**
	 * 
	 */
	function __construct($bd) {
		//construtor de classe. Inicia as variaveis
		$this->id = 0;
		$this->codigo = '';
		$this->nome = '';
		$this->tipo = '';
		$this->caract = '';
		$this->area = array('valor' => '', 'un' => '');
		$this->amianto = '';
		$this->ocupacao = '';
		$this->residuos = '';
		$this->pavimentos = '';
		$this->elevador = '';
		$this->responsavelProj = array("id" => "", "matr" => "", "username" => "", "gid" => "", "nome" => "", "sobrenome" => "", "nomeCompl" => "", "cargo" => "", "area" => "", "ramal" => "", "email" => "", "descr");
		$this->responsavelObra = array("id" => "", "matr" => "", "username" => "", "gid" => "", "nome" => "", "sobrenome" => "", "nomeCompl" => "", "cargo" => "", "area" => "", "ramal" => "", "email" => "", "descr");
		$this->estado = '';
		$this->recursos = array();
		$this->local = array('lat' => '', 'lng' => '') ;
		$this->etapas = array();
		$this->custo = null;
		$this->desc_img = null;
		$this->visivel = array("bool" => null, "text" => "N&atilde;o");
		$this->bd = $bd;
	}
	
	/**
	 * Carrega os dados da obra cujo ID eh passado por parametro
	 * @param int $id
	 */
	function load($id, $basic = false) {
		
		//se ID eh zero ou negativo, eh automaticamente invalido
		if ($id <= 0) 
			return array("success" => false, "errorNo" => 5, "errorFeedback" => "ID invalido");
		
		$bd = $this->bd;
		
		//carrega registro com ID selecionado do BD
		$result = $bd->query("SELECT * FROM obra_cad WHERE id=".$id);
		if (count($result) != 1) {
			return array("success" => false, "errorNo" => 2, "errorFeedback" => "Ha varios cadastros ou nenhum cadastro com esses dados");
		} else { 
			$result = $result[0];
		}
		//atribuicao de variaveis
		$this->id = $result['id'];
		$this->codigo = $result['cod'];
		$this->nome = $result['nome'];
		
		$this->desc_img = $result['desc_img'];
		
		$this->ocupacao['valor'] = $result['ocupacao'];
		if($result['ocupacao'] != '')
			$this->ocupacao['label'] = $result['ocupacao'];
		else
			$this->ocupacao['label'] = 'Desconhecido';
		
		$this->residuos['valor'] = $result['residuos'];
		if($result['residuos'] != '')
			$this->residuos['label'] = $result['residuos'];
		else
			$this->residuos['label'] = 'Desconhecido';
		
		$this->pavimentos['valor'] = $result['pavimentos'];
		if($result['pavimentos'] != '')
			$this->pavimentos['label'] = $result['pavimentos'];
		else
			$this->pavimentos['label'] = "Desconhecido";
		
		//AREA
		$this->area          = array('dimensao' => $result['dimensao'], 'un' => array('valor' => $result['dimensaoUn'], 'label' => ''));
		if(!$this->area['dimensao']) {
			$this->area['compl'] = 'Desconhecido';
		} else {
			if($this->area['un']['valor'] == 'm2') {
				$this->area['un']['label'] = 'm<sup>2</sup>';
			} elseif($this->area['un']['valor'] == 'm3') {
				$this->area['un']['label'] = 'm<sup>3</sup>';
			} else {
				$this->area['un']['label'] = $this->area['un']['valor'];
			}
			$this->area['compl'] = $this->area['dimensao'] . ' ' . $this->area['un']['label'];
		}
		
		
		//CARACT
		$this->caract['abrv']= $result['caract'];
		$nome = $bd->query("SELECT nome FROM label_obra_caract WHERE abrv = '{$result['caract']}'");
		if (count($nome) == 1){
			$this->caract['label'] = $nome[0]['nome'];
		} else {
			$this->caract['label'] = 'Desconhecido';
		}
		
		//TIPO
		$this->tipo['abrv'] = $result['tipo'];
		$nome = $bd->query("SELECT nome FROM label_obra_tipo WHERE abrv = '{$result['tipo']}'");
		if (count($nome) == 1){
			$this->tipo['label'] = $nome[0]['nome'];
		} else {
			$this->tipo['label'] = 'Desconhecido';
		}
		
		//AMIANTO1
		if($result['amianto']) {
			$this->amianto['bool']  = true;
			$this->amianto['label'] = "Sim";
		} elseif($result['amianto'] != null) {
			$this->amianto['bool'] = false;
			$this->amianto['label'] = "N&atilde;o";
		} else {
			$this->amianto['bool'] = null;
			$this->amianto['label'] = "Desconhecido";
		}
		
		//ESTADO
		$this->estado['id']  = $result['estadoID'];
		if($result['estadoID']){
			$nome = $bd->query("SELECT nome FROM label_obra_estado WHERE id = {$result['estadoID']}");
			if (count($nome) == 1){
				$this->estado['label'] = $nome[0]['nome'];
			} else {
				$this->estado['label'] = 'Desconhecido';
			}
		} else {
			$this->estado['label'] = 'Desconhecido';
		}
		//ELEVADOR
		if($result['elevador']) { 
			$this->elevador['bool'] = true;
			$this->elevador['label'] = "Sim";
		} elseif($result['elevador'] != null)  {
			$this->elevador['bool'] = false;
			$this->elevador['label'] = "N&atilde;o";
		} else {
			$this->elevador['bool'] = null;
			$this->elevador['label'] = "Desconhecido";
		}
		
		//LOCAL
		$this->local = array('lat' => $result['lat'], 'lng' => $result['lng']);
		if($this->local['lat'] && $this->local['lng'])
			$this->local['compl'] = $this->local['lat'] . '&deg; ' . $this->local['lng']. '&deg; (Campus '.$this->getCampusNome($result['campus']).')';
		else
			$this->local['compl'] = 'N&atilde;o Informado';
		
		//RESPONSAVEL PROJ
		if($result['responsavelProjID']) {
			$resp = $bd->query("SELECT * FROM usuarios WHERE id = {$result['responsavelProjID']}");
			$this->responsavelProj = $resp[0];
		} else {
			$this->responsavelProj['nomeCompl'] = "Desconhecido.";
		}
		
		//RESPONSAVEL OBRA
		if($result['responsavelObraID']) {
			$resp = $bd->query("SELECT * FROM usuarios WHERE id = {$result['responsavelObraID']}");
			$this->responsavelObra = $resp[0];
		} else {
			$this->responsavelObra['nomeCompl'] = "Desconhecido.";
		}
		
		//visivel
		if($result['visivel']) { 
			$this->visivel['bool'] = true;
			$this->visivel['label'] = "Sim";
		} elseif($result['visivel'] != null)  {
			$this->visivel['bool'] = false;
			$this->visivel['label'] = "N&atilde;o";
		} else {
			$this->visivel['bool'] = null;
			$this->visivel['label'] = "Desconhecido";
		}
		
		//carregamento de recursos
		if(!$basic)
			$this->getRecursos();
		
		//carregamento das etapas
		if(!$basic)
			$this->getEtapas();
		
		//carregamento do historico
		if(!$basic)
			$this->getHistorico();
	}
	
	/**
	 * Salva os dados da obra no BD
	 */
	function save(){
		$bd = $this->bd;
		$dados = trataCadVars();

		$this->calcula_campus($dados['latObra'],$dados['lngObra']);
		$dados['campus'] = $this->campus; 
		$arqDesc = '';
		
		//verifica se o doc ja foi cadastrado
		$isCad = $bd->query("SELECT id FROM obra_cad WHERE nome='{$dados['nome']}' AND tipo='{$dados['tipo']}' AND unOrg='{$this->unOrg['id']}'");
		if (count($isCad) > 0 && $this->nome != $dados['nome']) {
			return array("success" => false, "errorNo" => 5, "errorFeedback" => "Os dados desta obra conflitam com outro registro. Favor escolher outro nome.");
		}
		//se houver arquivo para upload
		if($_POST['img_sel'] == 'upload') {
			if(isset($_FILES["img"]) && $_FILES["img"]['error'] == 0){
				$ext = explode('.', $_FILES["img"]["name"]);
				$ext = $ext[count($ext)-1];
				
				$i = 1;
				if (file_exists('img/obras/' . $this->codigo . '/_imgDescricao(' . $i . ').' . $ext)){
					//tratamento de nomes duplicados
				    do {//verifica se o nome do documento ja existe, se sim, adiciona (i) estilo windows para nao sobrescrever
					   	$i++;
				    } while (file_exists('img/obras/' . $this->codigo . '/_imgDescricao(' . $i . ').' . $ext));
				}
				//se o diretorio nao existir, cria
				if(!is_dir('img/obras/' . $this->codigo)){
					mkdir('img/obras/' . $this->codigo);
				}
				//copia o arquivo para o diretorio padrao
				move_uploaded_file($_FILES["img"]["tmp_name"], 'img/obras/' . $this->codigo . '/_imgDescricao(' . $i . ').' . $ext);
				$arqDesc = ", desc_img = '_imgDescricao($i).$ext'";
			}
		} else if ($_POST['img_sel'] == 'remove') {
			//se foi selecionado para remover a imagem, apenas coloca '' no nome da img a ser mostrada
			//mas nao deleta a imagem do diretorio
			$arqDesc = ", desc_img = ''";
		}
		
		//cria a consulta SQL para a atualização de dados
		$sql = "UPDATE obra_cad SET
		nome              = '{$dados['nome']}',
		tipo              = '{$dados['tipo']}',
		caract            = '{$dados['caract']}',
		lat               =  {$dados['latObra']},
		lng               =  {$dados['lngObra']},
		campus            = '{$dados['campus']}',
		dimensao          =  {$dados['dimensao']},
		dimensaoUn        = '{$dados['dimensaoUn']}',
		responsavelProjID =  {$dados['respProjID']},
		responsavelObraID =  {$dados['respObraID']},
		ocupacao          = '{$dados['ocupacao']}',
		amianto           =  {$dados['amianto']},
		residuos          = '{$dados['residuos']}',
		pavimentos        =  {$dados['pavimentos']},
		visivel           =  {$dados['visivel']},
		elevador          =  {$dados['elevador']}"
		. $arqDesc . "
		WHERE id = {$this->get('id')}";		
		
		//realiza a consulta
		$res = $bd->query($sql);
		
		//monta o feedback dependendo do sucesso da consulta
		if($res) {
			return array('success' => true, "errorNo" => 0, "errorFeedback" => "");
		} else {
			return array('success' => false, "errorNo" => 1, "errorFeedback" => "Erro ao atualizar dados da obra");
		}
	}
	
	function saveNew() {
		$bd = $this->bd;
		
		//tratamento de variaveis
		$d = trataCadVars();
		
		//tratamento UnOrg
		if(preg_match("|[0-9]{2}.[0-9]{2}.[0-9]{2}.[0-9]{2}.[0-9]{2}.[0-9]{2}|", $d['unOrgSolic'], $matches)) {
			$d['unOrgSolic'] = substr($d['unOrgSolic'], 0, 17);
			
			//gera o cod da obra
			$codObra = str_ireplace('.', '', substr($d['unOrgSolic'], 0,8)).'-'.date("y").'-';
			$no = $bd->query("SELECT id FROM obra_cad WHERE cod LIKE '$codObra%'");
			if(count($no) > 9) $codObra .= count($no) + 1;
			else $codObra .= '0' . (count($no) + 1);
			
		} else {
			$codObra = '';
		}
		
		//verifica se o doc ja foi cadastrado
		$isCad = $bd->query("SELECT id FROM obra_cad WHERE nome='{$d['nome']}' AND tipo='{$d['tipo']}' AND unOrg='{$d['unOrgSolic']}'");
		if (count($isCad) > 0) {
			return array("success" => false, "errorNo" => 5, "errorFeedback" => "Erro ao inserir registro. Esta obra ja foi inserida anteriormente.");
		}
		
		//monta consulta e insere no BD
		$insert = $bd->query("INSERT INTO obra_cad (
			nome,
			cod,
			lat,
			lng,
			dimensao,
			dimensaoUn,
			pavimentos,
			caract,
			tipo,
			amianto,
			ocupacao,
			residuos,
			elevador,
			) VALUES (
			'{$d['nome']}',
			'{$codObra}',
			 {$d['latObra']},
			 {$d['lngObra']},
			 {$d['dimensao']},
			'{$d['dimensaoUn']}',
			 {$d['pavimentos']},
			'{$d['caract']}',
			'{$d['tipo']}',
			 {$d['amianto']},
			'{$d['ocupacao']}',
			'{$d['residuos']}',
			 {$d['elevador']},
			)");
		
		if(!$insert) return array("success" => false, "errorNo" => 2, "errorFeedback" => "Erro ao salvar dados no BD");
		
		//atribuicao de variaveis
		$this->nome          = $d['nome'];
		$this->local['lat']  = $d['latObra'];
		$this->local['lng']  = $d['lngObra'];
		$this->area['valor'] = $d['dimensao'];
		$this->area['un']    = $d['dimensaoUn'];
		$this->pavimentos    = $d['pavimentos'];
		$this->caract		 = $d['caract'];
		$this->tipo          = $d['tipo'];
		$this->amianto       = $d['amianto'];
		$this->ocupacao      = $d['ocupacao'];
		$this->residuos      = $d['residuos'];
		$this->elevador      = $d['elevador'];
		$this->codigo		 = $codObra;		
		
		//recupera o ID da obra salva
		$result = $bd->query("SELECT id FROM obra_cad WHERE nome='{$d['nome']}' AND tipo='{$d['tipo']}' AND unOrg='{$d['unOrgSolic']}'");
		if (count($result) == 1) {
			$this->id = $result[0]['id'];
		} else if(count($result) >1 ) {
			//se ha mais de um registro retornado, algo estranho aconteceu
			return array("success" => false, "errorNo" => 2, "errorFeedback" => "Ha varios cadastros com esses dados");
		} else {
			//se ha nenhum registro retornado, algo estranho aconteceu
			print_r($result);
			
		}
		
		//UPDATE: Recurso da tela de cadastro agora vai para o empreendimento
		/*if(isset($_POST['recursos']) && $_POST['recursos'] && isset($_POST['montanteRec']) && $_POST['montanteRec']){
			//adicao de recurso
			$rec = new Recurso();
			$insert = $rec->insertRecursoInObra($this->id);
			if (!$insert['success']) return array("success" => false, "errorNo" => 2, "errorFeedback" => "Erro ao salvar dados no BD");
			$this->getRecursos();
		}*/
		//cria a etapa padrao (Geral)
		$etapa = new Etapa($this->id, 1);
		$etapa->save();
		
		//adicao de fase/documento oficio de requisicao
		if(isset($_POST['ofir']) && $_POST['ofir'] > 0) {
			$etapa->addFase(1,1,1,$_POST['ofir']);
			
			$ofir = new Documento($_POST['ofir']);
			$ofir->loadCampos();
			if(isset($_POST['abrirSAP']) && $_POST['abrirSAP'] > 0)
				$despOfir = showDespStatus($ofir, array('para' => 'Protocolo' ,"outro" => '', 'funcID' => '_todos', 'despExt' => '', 'despacho' => 'Despachado automaticamente pelo sistema para anexar ao processo referente a obra '.$this->nome),'hideFB');
		}
		
		//adicao de fase/documento solicitacao de abertura de processo
		if(isset($_POST['abrirSAP']) && $_POST['abrirSAP'] > 0) {
			$doc = new Documento(0);
			$doc->dadosTipo['nomeAbrv'] = 'sap';
			$doc->loadTipoData();
			
			//seleciona o attr da tabela
			$r2 = attrFromGenericTable('numero_sap', $doc->dadosTipo['tabBD'], '1', 'numero_sap', 'DESC', '1');
			//incrementa o valor do attr e guarda no valor do campo
			$numero = explode('/', $r2[0]['numero_sap']);
			//seta os campos a serem preenchidos
			$doc->campos['numero_sap']  = ($numero[0] + 1);
			$doc->campos['anoE'] = date("Y");
			$doc->campos['contato'] = $_SESSION['id'];
			$doc->campos['unOrgIntSAP'] = getUnidadeName($this->unOrg);
			$doc->campos['pessoaIntSAP'] = $this->solic['nome'];
			if($this->caract == 'nova') {
				$caract = 'Obra';
			} elseif($this->caract == 'ampl') {
				$caract = 'Reforma';
			} elseif($this->caract == 'ref') {
				$caract = 'Amplia&ccedil;&atilde;ao';
			} elseif($this->caract == 'ampl_ref') {
				$caract = 'Amplia&ccedil;&atilde;o e Reforma';
			} else {
				$caract = '';
			}
			
			$doc->campos['assunto'] = 'Planejamento '.$caract.' '.$this->nome;
			$doc->campos['tipoProc'] = 'plan';
			$doc->campos['justificativa'] = '';
			//salva novo documento
			$doc->salvaCampos();
			$doc->salvaDoc(0);
			if (!$doc->doLogHist($_SESSION['id'],"","",'','criacao','','')) {
				return array("success" => false, "errorNo" => 2, "errorFeedback" => "Erro ao salvar o despacho de criacao.");
			}
			//gera PDF da SAP
			geraPDF($doc->id);
			//gera feedback
			$despStatus = showDespStatus($doc, array('para' => 'Protocolo' ,"outro" => '', 'funcID' => '_todos', 'despExt' => '', 'despacho' => 'Documento gerado automaticamente para abertura de processo de planejamento referente a obra '.$this->nome.'. Favor providenciar a abertura de processo para esta obra e anexar este documento a ele assim como o of&iacute;cio correspondete (se houver)'),'hideFB');
			//adiciona fase: solicitacao de abertura de processo de contratacao
			$etapa->addFase(1,1,2,$doc->id);
		}
		
		return array("success" => true, "errorNo" => 0, "errorFeedback" => "");
	}
	/**
	 * Le todos os recursos adicionados para esta obra
	 */
	function getRecursos() {
		$bd = $this->bd;
		
		//seleciona todos os IDs de obras referentes a esta obra
		$recID = $bd->query("SELECT id FROM obra_rec WHERE obraID = $this->id");
		//se houver IDs retornados
		if(count($recID)) {
			//pra cada ID, carrega os dados do recurso
			foreach ($recID as $r) {
				$rec = new Recurso();
				$rec->load($r['id']);
				//adiciona o recurso ao array de recursos
				$recs[] = $rec;
			}
			//atribui a obra os recursos lidos
			$this->recursos = $recs;
		} else {
			return array();
		}
	}
	
	/**
	 * Le todas as etapas adicionadas a esta obra
	 */
	function getEtapas() {
		$bd = $this->bd;
		
		//seleciona IDs de todos as etapas relacionadas a esta obra
		$etapasID = $bd->query("SELECT id FROM obra_etapa WHERE obraID=$this->id");
		//se houver IDs retornados
		if(count($etapasID)) {
			//pra cada ID, le os dados da etapa
			foreach ($etapasID as $e) {
				$etapa = new Etapa($this->id);
				$etapa->load($e['id']);
				//adiciona a etapa lida a array de etapas
				$etapas[] = $etapa;
			}
			//atribui as etapas lidas a obra
			$this->etapas = $etapas;
		} else {
			return array();
		}		
	}
	
	
	function getHistorico() {
		$bd = $this->bd;
		
		$histID = $bd->query("SELECT id FROM obra_historico WHERE obraID=$this->id");
		
		if (count($histID)) {
			foreach ($histID as $h) {
				$hist = new EntradaHistoricoObra();
				$hist->load($h['id']);
				
				$this->historico[] = $hist;
			}
		} else {
			$this->historico = array();
		}	
	}
	
	
	/**
	 * Funcao para resgatar alguma variavel da classe
	 * @param string $attr
	 * @return mixed attributo or null se atributo nao existir
	 */
	function get($attr) {
		if(isset($this->$attr))
			return $this->$attr;
		else
			return null;
	}
	
	/**
	 * Retorna o nome completo do campus passado como parâmetro
	 * @param String $abrv
	 */
	function getCampusNome($abrv) {
		if ($abrv == 'unicamp') {
			return 'Unicamp';
		}
		if ($abrv == 'cotuca') {
			return 'Cotuca';
		}
		if ($abrv == 'cpqba') {
			return 'CPQBA (Paul&iacute;nia)';
		}
		if ($abrv == 'lim1') {
			return 'Limeira 1';
		}
		if ($abrv == 'fca') {
			return 'FCA (Limeira)';
		}
		if ($abrv == 'fop') {
			return 'FOP (Piracicaba)';
		}
		if ($abrv == 'pircentro') {
			return 'Piracicaba Centro';
		}
	}
	
	/**
	 * Calcula em qual campus está o ponto cuja coordenadas foram passadas por parametro.
	 * @param float $lat
	 * @param float $lng
	 * @return string com o nome abrev. do campus. ver getCampusNome para descobrir o nome completo do campus
	 */
	function calcula_campus($lat, $lng) {
		if($lat < -22.811580 && $lat > -22.832895 && $lng < -47.056064 && $lng > -47.075261)
			$this->campus = 'unicamp';
		elseif($lat < -22.901869 && $lat > -22.902950 && $lng < -47.066584 && $lng > -47.067503)
			$this->campus = 'cotuca';
		elseif($lat < -22.795659 && $lat > -22.798986 && $lng < -47.113253 && $lng > -47.116561)
			$this->campus = 'cpqba';
		elseif($lat < -22.560086 && $lat > -22.563382 && $lng < -47.422261 && $lng > -47.432944)
			$this->campus = 'lim1';
		elseif($lat < -22.699287 && $lat > -22.702890 && $lng < -47.645914 && $lng > -47.650432)
			$this->campus = 'fop';
		elseif($lat < -22.548854 && $lat > -22.558457 && $lng < -47.424016 && $lng > -47.432944)
			$this->campus = 'fca';
		elseif($lat < -22.727234 && $lat > -22.727871 && $lng < -47.651205 && $lng > -47.641407)
			$this->campus = 'pircentro';
		else
			$this->campus = '';
	}
	
	
	/**
	 * Loga uma acao relacionada a obra no BD
	 * @param int $labelID
	 * @param array $dados
	 */
	function logaHistorico($labelID, $dados){
		$histEntr = new EntradaHistoricoObra($this->id, $labelID, $dados);
		if($histEntr->save())
			$this->historico[] = $histEntr;
	}
	
}


?>