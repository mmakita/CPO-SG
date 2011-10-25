<?php
<<<<<<< HEAD
/**
 * Monta o formulario de Busca de obras
 */
function showBuscaObrasForm() {
	global $bd;
	//carrega o layuout basico da pagina de busca
	$html = showBuscaObrasGmaps();
	$tipos_input = '';
	
	//cria dinamicamente as checkboxes para os tipos
	$tipos = $bd->query("SELECT abrv, nome FROM label_obra_tipo");
	foreach ($tipos as $t) {
		$tipos_input .= geraInput("tipo_".$t['abrv'], array('name' => "tipo_".$t['abrv'], 'type' => 'checkbox', 'value' => $t['abrv'], "class" => 'tipo'))." ".$t['nome']."<br />";
	}
	$html = str_ireplace('{$tipo_checkbox}', $tipos_input, $html);
	
	//cria dinamicamente as checkboxes das caracteristicas
	$caract = $bd->query("SELECT abrv, nome FROM label_obra_caract");
	$caract_input = '';
	
	foreach ($caract as $c) {
		$caract_input .= geraInput("carct_".$c['abrv'], array('name' => "caract_".$c['abrv'], 'type' => 'checkbox', 'value' => $c['abrv'], "class" => 'caract'))." ".$c['nome']."<br />";
	}
	$html = str_ireplace('{$caract_checkbox}', $caract_input, $html);
	
	//seleciona as dimensoes para completar os valores de busca
	$area = $bd->query("SELECT dimensao FROM obra_cad WHERE dimensao IS NOT NULL GROUP BY dimensao ORDER BY dimensao");	
	if(count($area) < 2){
		$a[1] = 0;
		$a[2] = 0;
		$a[3] = 0;
		$a[4] = 0;		
	} else {
		$a[1] = $area[round(count($area)/3)]['dimensao'];
		$a[2] = $area[round(count($area)/3)+1]['dimensao'];
		$a[3] = $area[round(count($area)/3)*2]['dimensao'];
		$a[4] = $area[round(count($area)/3)*2+1]['dimensao'];
	}
	//completa os valores de busca
	$html = str_ireplace(array('{$a1}', '{$a2}', '{$a3}', '{$a4}'), $a, $html);
	
	//busca os valores de custo das obras
	$custo= $bd->query("SELECT custo FROM obra_cad WHERE custo IS NOT NULL GROUP BY custo ORDER BY custo");
	//calcula os valores de busca
	if(count($custo) < 2) { 
		$r[1] = 0;
		$r[2] = 0;
		$r[3] = 0;
		$r[4] = 0;
	} else {
		$r[1] = $custo[round(count($custo)/3)]['custo'];
		$r[2] = $custo[round(count($custo)/3)+1]['custo'];
		$r[3] = $custo[round(count($custo)/3)*2]['custo'];
		$r[4] = $custo[round(count($custo)/3)*2+1]['custo'];
	}
	//substitui os valores de busca
	$html = str_ireplace(array('{$r1}', '{$r2}', '{$r3}', '{$r4}'), $r, $html);
	
	//retorno da pagina formada
	return $html;
}

/**
 * Rotina para gravação de nova obra no BD
 */
function salvaNovaObra(){
	
	//cria nova obra,carrega os dados enviados do formulario e salva no BD
	$obra = new Obra();
	$feedback = $obra->saveNew();
	if($feedback['success']) {
		$obra->logaHistorico(1,array());
	}
	//cria a mensagem HTML de feedback
	$html = verObraFeedback($feedback, 'cad');
	
	$unOrg = $obra->get('unOrg');
	$nomeUn = attrFromGenericTable('nome, sigla', 'unidades');
	if(count($nomeUn))
		$unOrg .= ' - ' . $nomeUn[0]['nome'] . ' (' . $nomeUn[0]['sigla'] . ')';
	
	//preenche as variaveis na interface
	$fb_vars = array('{$id_obra}','{$cod_obra}','{$nome_obra}','{$unOrg_obra}');
	$fb_vals = array($obra->get('id'),$obra->get('codigo'),$obra->get('nome'),$unOrg);
	$html = str_ireplace($fb_vars, $fb_vals, $html);
	
	//retorna a pagina HTML formada para exibicao 
	return $html;
}

function salvaObra($obra, $post) {
	$res = $obra->save();
	if($res['success']) {
		$res2 = $obra->logaHistorico(2,array());
		if($res2['success']){
			$fb = verObraFeedback($res2, 'slv');
		} else {
			$fb = verObraFeedback($res2, 'slv');
		}
		$fb = verObraFeedback($res, 'slv');
	}
	
	return $fb;
=======
function salvaNovaObra(){
	
	$dadosObra = trataCadVars();
	
	if(!insertObra($dadosObra)) {
		return 'Erro ao adicionar obra.';
	}
	
	$id_obra = getLastObra();
	
	$prazo = explode('/',$_POST['prazoRec']);
	$prazo['unixtimestamp'] = mktime(0,0,0,$prazo[1],$prazo[0],$prazo[2]);
	
	if (isset($_POST['recursos']) && $_POST['recursos'] == 1) {
		if(!insereRecurso($id_obra[0]['id'], $_POST['montanteRec'], $_POST['origemRec'], $prazo['unixtimestamp'])){
			return 'Erro ao adicionar recursos.';
		}
	}
	
	return 'Dados adicionados com sucesso.';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
}

/**
 * trata as variaveis GET colocando em um array organizado.
<<<<<<< HEAD
 * @return Array com as variaveis get tratadas
 */
function trataCadVars(){
	//le os dados do formulario enviado e os trata adequadamente segundo cada tipo de dado e cada campo no BD
	$campos = array('nome','tipo','amianto','ocupacao','residuos','pavimentos','elevador','latObra','lngObra','dimensao','dimensaoUn','unOrgSolic','nomeSolic','deptoSolic','emailSolic','ramalSolic','caract','descricao','respProjID','respObraID','visivel');
	//trata cada campo do formulario de cadastro de obra
	foreach ($campos as $c ) {
		//a funcao deve tratar a variavel apenas se ela existir
		if (!isset($_POST[$c]) || (isset($_POST[$c]) && $_POST[$c] == '')) {
			//campos numericos devem ser NULL ( e nao zero) caso esteja, vazios
			if ($c == 'latObra' || $c == 'lngObra' || $c == 'dimensao' || $c == 'amianto' || $c == 'elevador' || $c == 'pavimentos' || $c == 'ramalSolic' || $c == 'respID') {
				$dadosObra[$c] = 'NULL';
			} else {
				//os demais, podem ser vazios
				$dadosObra[$c] = '';
			}			
		} else {
			//caso contrario, apenas deve ser tratada a acentuiacao e caracteres especiais.
			$dadosObra[$c] = htmlentities($_POST[$c],ENT_QUOTES);
			
			if (($c == 'latObra' || $c == 'lngObra' || $c == 'dimensao' || $c == 'amianto' || $c == 'elevador' || $c == 'pavimentos' || $c == 'montanteRec') && strpos($_POST[$c], ',')) {
				$dadosObra[$c] = str_ireplace(',', '.', $_POST[$c]);
			}
		}
	}
	return $dadosObra;
}

/**
 * Monta a pagina de resumo de uma obra
 * @param Obra $obra
 */
function showObraResumo($obra){
	$template = showObraResumoTemplate();
	$html = $template['template'];
	$etapa_tr = '';
	
	//se a obra possuir imagem, monta as tags de IMG
	if($obra->desc_img)
		$img = str_replace(array('{$obraCod}','{$imgNome}','{$obraNome}'), array($obra->codigo, $obra->desc_img, $obra->nome), $template['img']);
	else //senao, deixa em branco
		$img = '';
	
	//para cada etapa da obra que foi cadastrada
	foreach ($obra->etapas as $e) {
		//monta a linha da tabela contendo info da etapa
		if($e->processo) $proc = '<a href="javascript:void(0)" onclick="window.open(\'sgd.php?acao=ver&docID='.$e->processo->id.'\',\'detalhe\',\'width=900,height=650,scrollbars=yes,resizable=yes\')">Processo '.$e->processo->numeroComp .'</a>';
		else $proc = 'Nenhum processo';
		//e suibstitui os valores pelos da etapa
		$etapa_tr = str_replace(array('{$etapa_nome}', '{$etapa_proc}', '{$etapa_estado}'), array($e->tipo, $proc, $e->estado['label']), $template['etapa_tr']);
	}
	
	//inicializa vetores de recursos
	$rec = array('c' => 0, 'd' => 0);
	$tr = array('c' => '', 'd' => '');
	
	//para cada recurso encontrado, adiciona ou subtrai o montante
	foreach ($obra->recursos as $r) {
		if($r->tipo == 'c') {
			$rec['c'] += (floatval($r->montante));
		} else {
			$rec['d'] += (floatval($r->montante));
		}
	}
	
	//substitui os valores
	$variaveis = array('{$nome}', '{$img}','{$unOrg}',             '{$descricao}',            '{$area}',            '{$etapa_tr}', '{$total_c}',                      '{$total_d}',                      '{$total_geral}');
	$valores = array($obra->nome, $img,     $obra->unOrg['compl'], $obra->descricao['label'], $obra->area['compl'],$etapa_tr,     number_format($rec['c'],2,',','.'),number_format($rec['d'],2,',','.'),number_format($rec['c']-$rec['d'],2,',','.'));
	
	$html = str_replace($variaveis, $valores, $html);
	
	//retorna a agina formada
	return $html;
}

/**
 * Funcao para mostrar detalhes da obra
 * @param int $id
 */
function showObraDetalhes($obra) {
	//carrega o template basico
	$template = showObraDetalhesTemplate();

	//seta as variaveis a serem mostradas e substitui os vamores
	$variaveis = array('{$nome}','{$cod}','{$unOrg}','{$tipo}','{$local}','{$area}','{$responsavelProj_nome}','{$responsavelObra_nome}','{$estado}','{$ocupacao}','{$residuos}','{$elevador}','{$pavimentos}','{$amianto}','{$caract}');
	$valores= array($obra->nome,$obra->codigo,$obra->unOrg['compl'],$obra->tipo['label'],$obra->local['compl'],$obra->area['compl'],$obra->responsavelProj['nomeCompl'],$obra->responsavelObra['nomeCompl'],$obra->estado['label'],$obra->ocupacao['label'],$obra->residuos['label'],$obra->elevador['label'],$obra->pavimentos['label'],$obra->amianto['label'],$obra->caract['label']);
	$html = str_ireplace($variaveis, $valores, $template['template']);
	
	return $html;
}

/**
 * Mostra os recursos da obra
 * @param Obra $obra
 */
function showObraRecursos($obra) {
	$template = showObraRecursosTemplate();
	$html = $template['template'];
	$rec_html = '';
	
	$recursos = $obra->recursos;
	//para cada recurso encontrado
	foreach ($recursos as $r) {
		//carrega o template da linha
		$r_tr = $template['recurso_tr'];
		if($r->prazo)
			$prazo = date("d/n/Y",$r->prazo);
		else
			$prazo = '';
		//completa com o prazo, origem e montante
		$r_tr = str_ireplace(array('{$rec_montante}','{$rec_origem}','{$rec_prazo}'), array("R$ ". number_format((float)$r->montante, 2, ',', '.'), $r->origem, $prazo), $r_tr);
		$rec_html .= $r_tr;
	}
	//se nao houver recursos adicionados, mosntra mensagem pertinente
	if(count($recursos) == 0) {
		$rec_html = $template['semRec_tr'];
	}
	//substitui
	$html = str_ireplace(array('{$nome}', '{$obraID}' , '{$recurso_tr}'), array($obra->nome, $obra->get('id'), $rec_html), $template['template']);
	
	return $html;
}

function showObraEtapas($obra) {
	$template = showObraEtapasTemplate();
	$html = $template['template'];
	$e_html = '';
	//para cada etapa, cria linha de visualização dos detalhes
	foreach ($obra->etapas as $e) {
		$e_tr = $template['etapa_tr'];
		$e_tr_det = '';
		
		$e_tr = str_ireplace(array('{$etapa_nome}', '{$etapaID}'), array($e->tipo, $e->getID()), $e_tr);
		if($e->processo) $e_tr = str_ireplace('{$etapa_proc}', '<a href="javascript:void(0)" onclick="window.open(\'sgd.php?acao=ver&docID='.$e->processo->id.'\',\'detalhe\',\'width=900,height=650,scrollbars=yes,resizable=yes\')">Processo '.$e->processo->numeroComp .'</a>', $e_tr);
		else $e_tr = str_ireplace('{$etapa_proc}', '(link) Adicionar Processo', $e_tr);

		$det = //TODO
		'<b> 1. Analise</b>
		
		<table>
		<tr class="c"><td>1.1.1</td><td>Oficio Unidade</td><td><a href=javascript:void(0)>Oficio EXMPL 123/2011</a></td><td><span style="color:green">Concluido</span></td></tr>
		<tr class="c"><td>1.1.2</td><td>Formulario Solicitacao de Obra</td><td><a href=javascript:void(0)>Adicionar</a></td><td><span style="color:red">Pendente</span></td></tr>
		<tr class="c"><td>1.1.3</td><td>Formulario de Abertura de Processo</td><td><a href=javascript:void(0)>Solicitacao de Abertura de Processo 123/2011</a></td><td><span style="color:green">Concluido</span></td></tr>
		</table>
		';
		
		$e_tr_det = str_replace(array('{$etapaID}', '{$etapa_det}'), array($e->getID(), $det), $template['etapa_det_tr']);
		
		$e_html .= $e_tr . $e_tr_det;
	}
	
	if(!count($obra->etapas))
		$e_html = $template['semEtapa_tr'];
	
	//montagem array de usuarios
	foreach (getAllUsersName() as $k => $u) {
		$nomes[$k]['value'] = $u['id'];
		$nomes[$k]['label'] = $u['nomeCompl'];
	}
	
	foreach (getTiposEtapa() as $k => $e) {
		$etapas[$k]['value'] = $e['id'];
		$etapas[$k]['label'] = $e['nome'];
	}
		
	//substituicao de marcacoes pelo nome
	$html = str_ireplace(array('{$nome}', '{$etapa_tr}'), array($obra->nome, $e_html), $template['template']);
	
	
	//retorno da pagina formada	
	return $html;
}

function showObraHistorico($obra) {
	global $bd;
	$template = showObraHistoricoTemplate();
	$historico_html = '';
	
	$historico = $obra->historico;
	foreach ($historico as $h) {
		$data = $h->get('data');
		$user = $h->get('user');
		$label = $h->get('label');
		$historico_html .= str_replace(array('{$entr_data}', '{$entr_user}', '{$entr_texto}'), array($data['amigavel'], $user['nome'], $label['text']), $template['entrada_tr']);
	}
	if (!count($obra->historico))
		$historico_html = $template['semEntr_tr'];
	
	return str_replace(array('{$nome}','{$tr_entradas}'), array($obra->nome, $historico_html), $template['template']);
}




function showObraEditForm($obra) {
	global $bd;
	$template = showObraEditFormTemplate();
	
	//CARACT
	$r = $bd->query("SELECT nome, abrv FROM label_obra_caract");
	foreach ($r as $c) {
		$caracts[] = array('value' => $c['abrv'] , 'label' => $c['nome']);
	}
	$campos['caract'] = geraSelect('caract', $caracts, $obra->caract['abrv']);
	
	//TIPO
	$r = $bd->query("SELECT nome, abrv FROM label_obra_tipo");
	foreach ($r as $c) {
		$tipos[] = array('value' => $c['abrv'] , 'label' => $c['nome']);
	}
	$campos['tipos'] = geraSelect('tipo', $tipos, $obra->tipo['abrv']);
	
	//RESPONSAVEL
	$r = getAllUsersName();
	foreach ($r as $u) {
		$users[] = array('value' => $u['id'] , 'label' => $u['nomeCompl']);
	}
	$campos['respProj'] = geraSelect('respProjID', $users, $obra->responsavelProj['id'], 0);
	
	//RESPONSAVEL
	$r = getAllUsersName();
	foreach ($r as $u) {
		$users[] = array('value' => $u['id'] , 'label' => $u['nomeCompl']);
	}
	$campos['respObra'] = geraSelect('respObraID', $users, $obra->responsavelObra['id'], 0);
	
	//AMIANTO
	$campos['amianto'] = geraSimNao('amianto', $obra->amianto['bool']);
	
	//ELEVADOR
	$campos['elevador'] = geraSimNao('elevador', $obra->elevador['bool']);
	
	//NOVO NOME
	$campos['novoNome'] = geraInput('nome', array('type' => 'text', 'size' => '50', 'maxlength' => '150', 'value' => $obra->nome));
	
	//OCUP
	$campos['ocupacao'] = geraInput('ocupacao', array('type' => 'text', 'size' => '50', 'maxlength' => '150', 'value' => $obra->ocupacao['valor']));
	
	//RESIDUOS
	$campos['residuos'] = geraInput('residuos', array('type' => 'text', 'size' => '50', 'maxlength' => '150', 'value' => $obra->residuos['valor']));
	
	//NUM PAVIMENTOS
	$campos['pavimentos'] = geraInput('pavimentos', array('type' => 'text', 'size' => '5', 'maxlength' => '5', 'value' => $obra->pavimentos['valor']));
	
	//DESCRICAO
	$campos['descricao'] = geraTextArea('descricao', 40, 3, $obra->descricao['valor']);
	
	//AREA
	$campos['area'] = geraInput('dimensao', array('type' => 'text', 'size' => '5', 'maxlength' => '10', 'value' => $obra->area['dimensao']))
	.' '.geraSelect('dimensaoUn', array(array('value' => 'm', 'label' => 'm'),array('value' => 'm2', 'label' => 'm2'),array('value' => 'm3', 'label' => 'm3'),array('value' => 'kVA', 'label' => 'kVA')), $obra->area['un']['valor']);
	
	//LOCAL
	$campos['local'] = geraMapSelectionDIV($obra->local['lat'], $obra->local['lng']);
	
	//PUblico?
	$campos['visivel'] = geraSimNao('visivel',$obra->visivel['bool']);
	
	//IMAGEM
	$campos['img'] = geraInput('img', array('type' => 'file', 'onclick' => "$('#img_selUp').attr('checked','checked');"));
	
	$variaveis = array('{$obraID}','{$nome}','{$cod}','{$unOrg}','{$descr}','{$tipo}','{$local}','{$area}','{$responsavelProj_nome}','{$responsavelObra_nome}','{$estado}','{$ocupacao}','{$residuos}','{$elevador}','{$pavimentos}','{$amianto}','{$caract}','{$novo_nome}','{$img}','{$visivel}');
	$valores= array($obra->get('id'),$obra->nome,$obra->codigo,$obra->unOrg['compl'],$campos['descricao'],$campos['tipos'],$campos['local'],$campos['area'],$campos['respProj'],$campos['respObra'],$obra->estado['label'],$campos['ocupacao'],$campos['residuos'],$campos['elevador'],$campos['pavimentos'],$campos['amianto'],$campos['caract'],$campos['novoNome'],$campos['img'],$campos['visivel']);
	$html = str_ireplace($variaveis, $valores, $template['template']);
	
	return $html;
=======
 */
function trataCadVars(){
	$campos = array('ofir','saa','nome','tipo','amianto','ocupacao','residuos','pavimentos','elevador','latObra','lngObra','dimensao');
	
	foreach ($campos as $c ) {		
		if (!isset($_POST[$c]) || (isset($_POST[$c]) && $_POST[$c] == '')) {
			$dadosObra[$c] = 'null';
		} else {
			$dadosObra[$c] = $_POST[$c];
		}
	}
}

function buscaObrasGmaps() {
	buscaObraSQL();
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
}

/**
 * Monta formulario para cadastro de obra
 */
<<<<<<< HEAD
function showCadObraForm($ofirID){
	global $bd;
	
	if(isset($_GET['coord']) && strpos($_GET['coord'],"|") != false) {
		//caso tenha sido selecionado local para obra, despreza alguns algarismos menos significativos
		$pos['lat'] = substr(substr($_GET['coord'],0,10),0,strpos($_GET['coord'],"|"));
		$pos['lng'] = substr($_GET['coord'],strpos($_GET['coord'],"|")+1,10);
	} else {
		//caso nao tenha sido selecionado local para a obra, deixa vazio
		$pos['lat'] = '';
		$pos['lng'] = '';
	}
	
	$html = showObraCadForm($pos);
	
	//CARACT
	$r = $bd->query("SELECT nome, abrv FROM label_obra_caract");
	foreach ($r as $c) {
		$caracts[] = array('value' => $c['abrv'], 'label' => $c['nome']);
	}
	$caract = geraSelect('caract', $caracts);
	
	//TIPO
	$r = $bd->query("SELECT nome, abrv FROM label_obra_tipo");
	foreach ($r as $c) {
		$tipos[] = array('value' => $c['abrv'] , 'label' => $c['nome']);
	}
	$tipos = geraSelect('tipo', $tipos);
	
	$html = str_ireplace(array('{$select_tipo}','{$select_caract}'), array($tipos, $caract), $html);
	
	//se iniciou o cadastro atraves de oficio
	if ($ofirID) {
		$doc = new Documento($ofirID);
		$doc->loadCampos();
		
		$vars = array('{$ofirID}','{$ofirNome}','{$unOrgSolic}','{$nomeSolic}','{$deptoSolic}','{$emailSolic}','{$ramalSolic}','{$estilo}','{$local}');
		$vals = array($ofirID, 'Of&iacute;cio '.$doc->numeroComp, $doc->campos['unOrg'], $doc->campos['solicNome'], $doc->campos['solicDepto'], $doc->campos['solicEmail'], $doc->campos['solicRamal'],'background-color: #DDFFDD;',geraMapSelectionDIV($pos['lat'], $pos['lng']));
		$html = str_ireplace($vars, $vals, $html);
	
	//senao nao preenche os dados de oficio
	} else {
		$vars = array('{$ofirID}','{$ofirNome}','{$unOrgSolic}','{$nomeSolic}','{$deptoSolic}','{$emailSolic}','{$ramalSolic}','{$estilo}','{$local}');
		$vals = array('','Nenhum Selecionado','','','','','','',geraMapSelectionDIV($pos['lat'], $pos['lng']));
		$html = str_ireplace($vars, $vals, $html);
	}
	
	//monta o formulario na parte de interfaces
	return $html;
}

/**
 * Mostra menu de ações
 * @param Obra $obra
 * @param Array $perm
 * @param Array $itens
 */
function showObraActionMenu($obra, $perm, $itens){
	$template_basico = obraActionMenu();
	
	$links = '';
	//para cada item a ser mostrado, faz o tratamento
	foreach ($itens as $a) {
		if(isset($template_basico['acoes'][$a]) && (($perm != null && $perm[$a]) || $perm == null)) {
			$links .= str_replace('{$obraID}', $obra->get('id'), $template_basico['acoes'][$a]);
		}
	}
	return str_replace('{$acoes}', $links, $template_basico['estrutura']);
}

/**
 * trata data retornando o unixtimestamp das datas no formato dd/mm ou dd/mm/aaaa
 * @param string dfe entrada
 */
function trataData($string) {
	if(strpos($string, "/") !== false) {
		$string = explode('/', $string);
	} elseif(strpos($string, "-") !== false) {
		$string = explode('/', $string);
	} else {
		return 'NULL';
	}
	
	if(count($string) < 2 || count($string) > 3) {
		return 'NULL';
	} elseif (count($string) == 2) {
		$dia = 1; $mes = $string[0]; $ano = $string[1];
	} else {
		$dia = $string[0]; $mes = $string[1]; $ano = $string[2];
	}
	
	return mktime(23, 59, 59, $mes, $dia, $ano);
}

/**
 * Consulta o nome dos tipos de etapa
 */
function getTiposEtapa() {
	global $bd;
	
	return $bd->query("SELECT id, nome FROM label_obra_etapa ORDER BY nome");
}
=======
function showCadObraForm(){
	if(isset($_GET['coord']) && strpos($_GET['coord'],"|") != false){
		$pos['lat'] = substr(substr($_GET['coord'],0,10),0,strpos($_GET['coord'],"|"));
		$pos['lng'] = substr($_GET['coord'],strpos($_GET['coord'],"|")+1,10);
		$local = 'Lat: <input type="text" name="latObra" size="7" maxlength="18" value="'.$pos['lat'].'" />&deg; Long: <input type="text" name="lngObra" size="7" maxlength="18" value="'.$pos['lng'].'" />&deg;';
	} else {
		$local = 'Lat: <input type="text" name="latObra" size="7" maxlength="18" />&deg; Long: <input type="text" name="lngObra" size="7" maxlength="18" />&deg;
		<br /><span style="font-size: 9pt;">(use valores negativos para coordenadas Sul/Oeste e <b>ponto</b> como separador decimal)</span>';
	}
	
	$html = '
		<h3>Cadastro de obra</h3>
		<form action="sgo.php?acao=salvarNovaObra" method="post" id="cadNovaObra">
		<table style="border: 0; width:100%" cellpadding="0" cellspacing="0">
			<tbody>
				<tr class="c">
					<td class="c"><b>1.</b></td>
					<td class="c"><b>Of&iacute;cio de Requisi&ccedil;&atilde;o:</b></td>
					<td class="c">
						<input type="hidden" name="ofir" id="ofir" />
						<div id="ofirNomes">Nenhum selecionado.</div>
						<br />
						<div id="ofirLink">
							<a href="javascript:void(0);" onclick="newDocInNewWindow(\'ofe\',\'ofir\',\'cad\');">Cadastrar Of&iacute;cio de Requisi&ccedil;&atilde;o</a> ou <a href="javascript:void(0);" onclick="escolherDoc(\'ofir\');">Usar documento j&aacute; cadastrado</a>
						</div>
					</td>
				</tr>
				<tr class="c">
					<td class="c"><b>2.</b></td>
					<td class="c"><b>Informa&ccedil;&otilde;es da Obra:</b></td>
					<td class="c">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tbody>
								<tr class="c">
									<td class="c" width= "40%"><b>Nome da Obra</b></td>
									<td class="c"><input type="text" class="obrigatorio" name="nome" size="50" maxlength="100" />*</td>
								</tr>
								<tr class="c">
									<td class="c"><b>Tipo de Obra</b></td>
									<td class="c">
										<select name="tipo" class="obrigatorio" >
											<option value="nenhum">-- Selecione --</option>
											<option value="ref">Reforma de &Aacute;rea Existente (sem amplia&ccedil;&atilde;o)</option>
											<option value="nova">Obra Nova (independente de &aacute;rea existente)</option>
											<option value="ampl">Amplia&ccedil;&ailde;o (conectada &agrave; &aacute;rea existente)</option>
											<option value="ampl_ref">Amplia&ccedil;&atilde;o com Reforma de parte da &aacute;rea existente.</option>
											<option value="continuidade">Continuidade de Obra que se encontra paralisada.</option>
										</select>*
									</td>
								</tr>
								<tr class="c">
									<td class="c"><b>&Aacute;rea aproximada da interven&ccedil;&atilde;o</b></td>
									<td class="c"><input type="text" name="dimensao" size="10" maxlength="10" /> m<sup>2</sup> </td>
								</tr>
								<tr class="c">
									<td class="c"><b>Haver&aacute; altrera&ccedil;&atilde;o em elementos que contenham amianto?</b>(ex: divis&oacute;rias, telhas)</td>
									<td class="c"><input type="radio" name="amianto" value="1" />Sim | <input type="radio" name="amiantoObra" value="0" />N&atilde;o </td>
								</tr>
								<tr class="c">
									<td class="c"><b>Qual ser&aacute; a ocupa&ccedil;&atilde;o e uso do local?</b></td>
									<td class="c"><input type="text" name="ocupacao" size="50" maxlength="200" /></td>
								</tr>
								<tr class="c">
									<td class="c"><b>Quais res&iacute;duos ser&atilde;o gerados ap&oacute;s a ocupa&ccedil;&atilde;o do local?</b></td>
									<td class="c"><input type="text" name="residuos" size="50" maxlength="200" /></td>
								</tr>
								<tr class="c">
									<td class="c"><b>Quantidade de pavimentos:</b></td>
									<td class="c"><input type="text" name="pavimentos" size="2" maxlength="2" /></td>
								</tr>
								<tr class="c">
									<td class="c"><b>A obra ter&aacute; elevador?</b></td>
									<td class="c"><input type="radio" name="elevador" value="1" />Sim | <input type="radio" name="elevadorObra" value="0" />N&atilde;o </td>
								</tr>
								<tr class="c">
									<td class="c" colspan="2"><span style="font-size:12pt; font-weight:bold;">Recursos Financeiros</span></td>
								</tr>
								<tr class="c">
									<td class="c"><b>H&aacute; recursos garantidos?</b></td>
									<td class="c"><input type="radio" name="recursos" value="1" />Sim | <input type="radio" name="recursosObra" value="0" />N&atilde;o </td>
								</tr>
								<tr class="c">
									<td class="c"><b>Montante de recursos garantidos:</b></td>
									<td class="c">R$ <input type="text" name="montanteRec" size="10" maxlength="10" /> </td>
								</tr>
								<tr class="c">
									<td class="c"><b>Origem dos recursos:</b></td>
									<td class="c"><input type="text" name="origemRec" size="30" maxlength="200" /> </td>
								</tr>
								<tr class="c">
									<td class="c"><b>Prazo de Conv&ecirc;nios</b></td>
									<td class="c"><input type="text" name="prazoRec" size="10" maxlength="10" /> (dd/mm/aaaa)</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr class="c">
					<td class="c"><b>3.</b></td>
					<td class="c"><b>Localiza&ccedil;&atilde;o da Obra:</b></td>
					<td class="c">'.$local.'</td>
				</tr>
				<tr class="c">
					<td class="c"><b>4.</b></td>
					<td class="c"><b>Solic. Abertura de Processo:</b></td>
					<td class="c">
						<input type="hidden" name="saa" id="saa" />
						<div id="saaNomes">Nenhum selecionado.</div>
						<br />
						<div id="saaLink">
							<a href="javascript:void(0);" onclick="newDocInNewWindow(\'sap\',\'saa\',\'novo\');">Emitir nova Solicita&ccedil;&atilde;o de Abertura de Processo</a>
							ou <a href="javascript:void(0);" onclick="escolherDoc(\'saa\');">Usar documento j&aacute; cadastrado</a>
						</div>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="3"><input type="submit" value="Enviar" /></td>
					<td></td>
				</tr>
				<tr>
					<td><b></b></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		</form>
	';
	return $html;
}

>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
?>