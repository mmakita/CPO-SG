<?php
/**
 * cria um iframe para a visualizacao do mapa
 */
function showHomeObrasGmaps(){
	return '
	<a href="sgo.php?acao=cadastrar">Nova obra sem local definido</a><br />
	<iframe src="sgo_map.php?mode=cad" style="min-height: 500px; width: 100%; height: 75%; padding: 0; margin: 0; border: 0;"></iframe>';
}

/**
 * cria um iframe para a visualizacao do mapa de BUSCA
 */
function showBuscaObrasGmaps(){
	return '
	<table id="busca" style="width: 100%;">
		<tr>
			<td style="max-width: 50%;">
				<span class="header">Filtrar por:</span>
				<form id="buscaObraForm">
					<table style="width: 100%">
						<tr class="c"><td class="c" colspan="2">
							<b>Campus:</b> (clique sobre o nome para mudar o mapa)<br />
							<table style="width: 100%">
								<tr>
									<td colspan="2" style="width: 25%; text-align: center;">
										Campinas<br />
									</td>
									<td colspan="2" style="width: 25%; text-align: center;">
										Paul&iacute;nia<br />
									</td>
									<td colspan="2" style="width: 25%; text-align: center;">
										Limeira<br />
									</td>
									<td colspan="2" style="width: 25%; text-align: center;">
										Piracicaba<br />
									</td>
								</tr>
								<tr>
									<td style="width: 10%; text-align: right;">
										<input type="checkbox" class="campus" id="campus_unicamp" name="campus_unicamp" value="unicamp" />
									</td>
									<td style="width: 15%; text-align: left;">
										<a href="javascript:void(0)" onclick="javascript:document.getElementById(\'gmapsRes\').contentWindow.focusCampus(\'unicamp\')">Unicamp</a><br />
									</td>
									<td style="width: 10%; text-align: right;">
										<input type="checkbox" class="campus" id="campus_cpqba" name="campus_cpqba" value="cpqba" />
									</td>
									<td style="width: 15%; text-align: left;">
										<a href="javascript:void(0)" onclick="javascript:document.getElementById(\'gmapsRes\').contentWindow.focusCampus(\'cpqba\')">CPQBA</a>
									</td>
									<td style="width: 10%; text-align: right;">
										<input type="checkbox" class="campus" id="campus_lim1" name="campus_lim1" value="lim1" />
									</td>
									<td style="width: 15%; text-align: left;">
										<a href="javascript:void(0)" onclick="javascript:document.getElementById(\'gmapsRes\').contentWindow.focusCampus(\'lim1\')">Campus 1</a><br />
									</td>
									<td style="width: 10%; text-align: right;">
										<input type="checkbox" class="campus" id="campus_fop" name="campus_fop" value="fop" />
									</td>
									<td style="width: 15%; text-align: left;">
										<a href="javascript:void(0)" onclick="javascript:document.getElementById(\'gmapsRes\').contentWindow.focusCampus(\'fop\')">FOP</a><br />
									</td>
								</tr>
								<tr>
									<td style="text-align: right;">
										<input type="checkbox" class="campus" id="campus_cotuca" name="campus_cotuca" value="cotuca" />
									</td>
									<td style="text-align: left;">
										<a href="javascript:void(0)" onclick="javascript:document.getElementById(\'gmapsRes\').contentWindow.focusCampus(\'cotuca\')">Cotuca</a><br />
									</td>
									<td style="text-align: right;"></td>
									<td style="text-align: left;"></td>
									<td style="text-align: right;">
										<input type="checkbox" class="campus" id="campus_fca" name="campus_fca" value="fca" />
									</td>
									<td style="text-align: left;">
										<a href="javascript:void(0)" onclick="javascript:document.getElementById(\'gmapsRes\').contentWindow.focusCampus(\'fca\')">FCA</a><br />
									</td>
									<td style="text-align: right;">
										<input type="checkbox" class="campus" id="campus_pircentro" name="campus_pircentro" value="pircentro" />
									</td>
									<td style="text-align: left;">
										<a href="javascript:void(0)" onclick="javascript:document.getElementById(\'gmapsRes\').contentWindow.focusCampus(\'pircentro\')">Centro</a><br />
									</td>
								</tr>
							</table>
						</td></tr>
						<tr class="c"><td class="c" colspan="2">
							<b>Nome da obra: </b>
							<input type="text" name="nome" id="nome" size="50" maxlength="200" autocomplete="off" />
						</td></tr>
						<tr class="c"><td class="c" colspan="2">
							<b>Unidade/&Oacute;rg&atilde;o solicitante: </b>
							<input type="text" name="unOrg" id="unOrg" size="50" maxlength="200" />
						</td></tr>
						<tr class="c"><td class="c"  style="width:50%">
							<b>Caracter&iacute;stica: </b><br />
							{$caract_checkbox}
						</td>
						<td class="c" style="width:50%">
							<b>Tipo: </b><br />
							{$tipo_checkbox}
						</td>
						</tr>
						<tr class="c"><td class="c" style="width:50%">
							<b>&Aacute;rea: </b> <br />
							<input type="checkbox" class="area" name="area1" id="area1" value="1" /> At&eacute; {$a1} m<sup>2</sup><br />
							<input type="checkbox" class="area" name="area2" id="area2" value="2" /> De {$a2} a {$a3} m<sup>2</sup><br />
							<input type="checkbox" class="area" name="area3" id="area3" value="3" /> Acima de {$a4} m<sup>2</sup><br />
							<input type="checkbox" class="area" name="area0" id="area0" value="0" /> N&atilde;o informado.
							<input type="hidden" id="a1" value="{$a1}" />
							<input type="hidden" id="a2" value="{$a2}" />
							<input type="hidden" id="a3" value="{$a3}" />
							<input type="hidden" id="a4" value="{$a4}" />
						</td>
						<td class="c" style="width:50%">
							<b>Pavimentos: </b><br />
							<input type="checkbox" class="pav" name="pav0"  id="pav0" value="1" /> T&eacute;rreo<br />
							<input type="checkbox" class="pav" name="pav1"  id="pav1" value="2" /> 1 ou 2 pavimentos<br />
							<input type="checkbox" class="pav" name="pav2"  id="pav2" value="3" /> Acima de 2 pavimentos<br />
							<input type="checkbox" class="pav" name="pavNF" id="pavNF" value="0" /> N&atilde;o informado.
						</td></tr>
						<tr class="c"><td class="c">
							<b>Elevador: </b>
							<input type="checkbox" class="elev" name="elevador1" id="elevador1" value="1" /> Com elevador
						</td><td class="c">
							<input type="checkbox" class="elev" name="elevador0" id="elevador0" value="0" /> Sem elevador
						</td></tr>
						<tr class="c"><td class="c"  style="width:50%">
							<b>Recursos: </b><br />
							<input type="checkbox" class="rec" name="rec1" id="rec1" value="1" /> Custo de at&eacute; R$ {$r1}<br />
							<input type="checkbox" class="rec" name="rec2" id="rec2" value="2" /> Custo entre R$ {$r2} e R$ {$r3}<br />
							<input type="checkbox" class="rec" name="rec3" id="rec3" value="3" /> Custo maior que R$ {$r4}<br />
							<input type="checkbox" class="rec" name="rec0" id="rec0" value="0" /> Sem custo definido<br /><br />
						</td><td class="c"  style="width:50%">
							<input type="checkbox" name="todosRec" class="todos_rec" id="todosRec" value="1" /> Com todos os recursos garantidos<br />
							<input type="checkbox" name="pendRec" class="todos_rec" id="pendRec" value="0" /> Com recursos pendentes
							<input type="hidden" id="r1" value="{$r1}" />
							<input type="hidden" id="r2" value="{$r2}" />
							<input type="hidden" id="r3" value="{$r3}" />
							<input type="hidden" id="r4" value="{$r4}" />
						</td></tr>
						<tr class="c">
						<td class="c" style="text-align: center;" colspan="2">
							<input type="submit" value="Filtrar" />
						</td></tr>
					</table>
				</form>
			</td>
			<td style="width: 50%; min-width:550px;">
				Mostrar: <a href="javascript:void(0)" id="show_map" style="text-decoration: underline;" onclick="showMap()">mapa</a> <a href="javascript:void(0)" id="show_list" onclick="showList()">lista</a><br />
						<iframe id="gmapsRes" src="sgo_map.php?mode=bus" scrolling="no" style="height: 95%; width: 100%; min-height: 500px; padding: 0; margin: 0; border: 0;  overflow-y: hidden; "></iframe>
				<div id="listaRes" style="display:none;">
				</div>
			</td>
		</tr>
	</table>';
}

/**
 * Gera o feedback do cadastro de obras
 * @param array $fb
 */
function verObraFeedback ($fb, $msg = null){
	//caso o feedback seja positivo, gera mensagem pertinente
	if($fb['success']) {
		$text = "A&ccedil;&atilde;o efetuada com sucesso!";
		if($msg == 'cad') {
			$data = '<br />
			C&oacute;digo da Obra: {$cod_obra}<br />
			Nome da Obra: {$nome_obra}</a><br />
			Unidade/&Oacute;rg&atilde;o: {$unOrg_obra}<br />
			<br />
			<b>P&aacute;gina da obra:</b> <a href="javascript:void(0)" onclick="javascript:window.open(\'sgo.php?acao=ver&amp;obraID={$id_obra}\',\'detalheObra{$id_obra}\',\'width=900,height=650,scrollbars=yes,resizable=yes\');">Clique aqui.</a><br />
			<br />
			Outras a&ccedil;&otilde;es: <br />
			<a href="index.php">Voltar para o inicio</a>
			';
		} elseif($msg = 'slv') {
			$data = '';
		}
	} else {
		//caso tenha ocorrido um erro, mostra a mensagem de erro ocorrida
		$text = "Ocorreu um erro ao efetuar essa a&ccedil;&atilde;o.<br /> Descri&ccedil;&atilde;o:
		<b>{$fb['errorFeedback']}</b>
		<br />
		<span style=\"text-align:center\"><a href=\"javascript:history.go(-1);\">Voltar</a></span>";
		$data = '';
	}
	
	$html = '<div style="border: 1px solid red; padding: 5px;">'.$text.'</div>'.$data;
	
	return $html;
	
}

function obraActionMenu() {
	return array('estrutura' =>'
	<div class="boxLeftMenu">	
		{$acoes}
	</div>',
	'acoes' => array(10 => '', 
					  9 => '<a id="editObraDetLink" class="menuHeader" href="sgo.php?acao=edit&obraID={$obraID}" >
							Editar detalhes da Obra
							</a>',
					 'voltar' => '<a id="verObraLink" class="menuHeader" href="sgo.php?acao=ver&obraID={$obraID}" >
							Ver dados da Obra
							</a>',
					 'salvar' => ''
					  )
	);
}

function showObraTopMenu($id=0) {
	return '
	<table style="border-width: 0;" width="100%">
		<tr>
			<td width="20%" style="text-align: center;" class="topMenu"><a href="javascript:void(0)" class="menu_link" id="resumo_link">Resumo</a></td>
			<td width="20%" style="text-align: center;" class="topMenu"><a href="javascript:void(0)" class="menu_link" id="detalhes_link">Detalhes</a></td>
			<td width="20%" style="text-align: center;" class="topMenu"><a href="javascript:void(0)" class="menu_link" id="etapas_link">Etapas</a></td>
			<td width="20%" style="text-align: center;" class="topMenu"><a href="javascript:void(0)" class="menu_link" id="recursos_link">Finan&ccedil;as</a></td>
			<td width="20%" style="text-align: center;" class="topMenu"><a href="javascript:void(0)" class="menu_link" id="historico_link">Hist&oacute;rico</a></td>
		</tr>
	</table>';
}

function showObraResumoTemplate() {
	return array('template' => '
	<p style="text-align: center; font-size: 14pt; color:#BE1010;">{$nome}</p>
	<table style="border-width: 0;" width="100%">
		<tr>
			<td style="text-align: center; min-width:270px; width:25%;" rowspan="4" class="c">{$img}</td>
			<td style="width: 15%;" class="c"><b>Unidade: </b></td>
			<td style="width: 60%;" class="c">{$unOrg}</td>
		</tr>
		<tr>
			<td style="width: 15%;" class="c"><b>Descri&ccedil;&atilde;o: </b></td>
			<td style="width: 60%;" class="c">{$descricao}</td>
		</tr>
		<tr>
			<td style="width: 15%;" class="c"><b>&Aacute;rea: </b></td>
			<td style="width: 60%;" class="c">{$area}</td>
		</tr>
		<tr>
			<td colspan="2" style="width: 25%; text-align: right;" class="c"><!--<a href="javascript:void(0)" class="menu_link" id="detalhes_link2">Mais detalhes</a>--></td>
		</tr>
	</table>

	<p style="text-align: center; font-size: 12pt; font-weight: bold;">Planejamento</p>	
	
	<table style="border-width: 0;" width="100%">
		<tr>
			<td class="c" colspan="3"><!-- filler --></td>
		</tr>
		<tr>
			<td class="c" style="text-align: center; font-weight: bold;">Etapa</td>
			<td class="c" style="text-align: center; font-weight: bold;">Processo</td>
			<td class="c" style="text-align: center; font-weight: bold;">Estado</td>
		</tr>
		{$etapa_tr}
	</table>
	
	<p style="text-align: center; font-size: 12pt; font-weight: bold;">Finan&ccedil;as</p>
	
	<table style="width:100%">
		<tr>
			<td class="c" colspan="2"></td>
		</tr>
		<tr>
			<td class="c"><b>Montante total reservado:</b></td>
			<td class="c"  style="color: #00CC00 "><b>R$ {$total_c}</b></td>
		</tr>
		<!--<tr>
			{$origens_td}
		</tr>-->
		<tr>
			<td class="c"><b>Montante total desembolsado:</b></td>
			<td class="c" style="color: red; "><b>R$ {$total_d}</b></td>
		</tr>
		<tr>
			<td class="c"><b>Balan&ccedil;o:</b></td>
			<td class="c" style="color:{$cor_total}"><b>R$ {$total_geral}</b></td>
		</tr>
		
	</table>
	',
	'img' => '<img src="img/obras/{$obraCod}/{$imgNome}" alt="{$obraNome}" style="margin: 5px; width: 250px">',
	'etapa_tr' => '
	<tr class="c">
		<td class="c" style="text-align: center;">{$etapa_nome}</td>
		<td class="c" style="text-align: center;">{$etapa_proc}</td>
		<td class="c" style="text-align: center;">{$etapa_estado}</td>
	</tr>');
}

function showObraDetalhesTemplate() {
	return array('template' => '
	<p style="text-align: center; font-size: 14pt; color:#BE1010;">{$nome}</p>
	<table style="border-width: 0;" width="100%">
	<tr><td class="c" colspan="2"></td></tr>
	<tr class="c"><td class="c"><b>C&oacute;d. Obra:</b></td><td class="c">{$cod}</td></tr>
	<tr class="c"><td class="c"><b>Unidade/&Oacute;rg&atilde;o:</b></td><td class="c">{$unOrg}</td></tr>
	<tr class="c"><td class="c"><b>Caracter&iacute;stica:</b></td><td class="c">{$caract}</td></tr>
	<tr class="c"><td class="c"><b>Tipo:</b></td><td class="c">{$tipo}</td></tr>
	<tr class="c"><td class="c"><b>Local:</b></td><td class="c">{$local}</td></tr>
	<tr class="c"><td class="c"><b>&Aacute;rea:</b></td><td class="c">{$area}</td></tr>
	<tr class="c"><td class="c"><b>Respons&aacute;vel pelo Projeto:</b></td><td class="c">{$responsavelProj_nome}</td></tr>
	<tr class="c"><td class="c"><b>Respons&aacute;vel pela Obra:</b></td><td class="c">{$responsavelObra_nome}</td></tr>
	<tr class="c"><td class="c"><b>Estado Atual:</b></td><td class="c"></td></tr>
	<tr class="c"><td class="c"><b>Alterações em materiais com amianto:</b></td><td class="c">{$amianto}</td></tr>
	<tr class="c"><td class="c"><b>Ocupa&ccedil;&atilde;o:</b></td><td class="c">{$ocupacao}</td></tr>
	<tr class="c"><td class="c"><b>Res&iacute;duos:</b></td><td class="c">{$residuos}</td></tr>
	<tr class="c"><td class="c"><b>N&deg; de pavimentos:</b></td><td class="c">{$pavimentos}</td></tr>
	<tr class="c"><td class="c"><b>Elevador:</b></td><td class="c">{$elevador}</td></tr>
	
	<!--<tr class="c"><td class="c"><b></b></td><td class="c"></td>-->
	</table>');
}

function showObraEditFormTemplate() {
	return array('template' => '
	<p style="text-align: center; font-size: 14pt; color:#BE1010;">{$nome}</p>
	<form action="sgo.php?acao=salvar&obraID={$obraID}" method="post" enctype="multipart/form-data">
	<table style="border-width: 0;" width="100%">
	<tr><td class="c" colspan="2"></td></tr>
	<tr class="c"><td class="c"><b>Novo Nome:</b></td><td class="c">{$novo_nome}</td></tr>
	<tr class="c"><td class="c"><b>C&oacute;d. Obra:</b></td><td class="c">{$cod}</td></tr>
	<tr class="c"><td class="c"><b>Unidade/&Oacute;rg&atilde;o:</b></td><td class="c">{$unOrg}</td></tr>
	<tr class="c"><td class="c"><b>Caracter&iacute;stica:</b></td><td class="c">{$caract}</td></tr>
	<tr class="c"><td class="c"><b>Tipo:</b></td><td class="c">{$tipo}</td></tr>
	<tr class="c">
		<td class="c"><b>Local:</b></td>
		<td class="c">
			{$local}
		</td>
	</tr>
	<tr class="c"><td class="c"><b>Descri&ccedil;&atilde;o:</b></td><td class="c">{$descr}</td></tr>
	<tr class="c"><td class="c"><b>&Aacute;rea:</b></td><td class="c">{$area}</td></tr>
	<tr class="c"><td class="c"><b>Respons&aacute;vel pelo Projeto:</b></td><td class="c">{$responsavelProj_nome}</td></tr>
	<tr class="c"><td class="c"><b>Respons&aacute;vel pela Obra:</b></td><td class="c">{$responsavelObra_nome}</td></tr>
	<tr class="c"><td class="c"><b>Estado Atual:</b></td><td class="c">{$estado}</td></tr>
	<tr class="c"><td class="c"><b>Alterações em materiais com amianto:</b></td><td class="c">{$amianto}</td></tr>
	<tr class="c"><td class="c"><b>Ocupa&ccedil;&atilde;o:</b></td><td class="c">{$ocupacao}</td></tr>
	<tr class="c"><td class="c"><b>Res&iacute;duos:</b></td><td class="c">{$residuos}</td></tr>
	<tr class="c"><td class="c"><b>N&deg; de pavimentos:</b></td><td class="c">{$pavimentos}</td></tr>
	<tr class="c"><td class="c"><b>Elevador:</b></td><td class="c">{$elevador}</td></tr>
	
	<tr class="c"><td class="c"><b>Aparecer no Site P&uacute;blico:</b></td><td class="c">{$visivel}</td></tr>
	
	<tr class="c">
		<td class="c"><b>Imagem de exibição:</b></td>
		<td class="c"><input type="radio" id="img_selMt" name="img_sel" value="mantain" checked="checked" /> Manter imagem atual <br />
					  <input type="radio" id="img_selUp" name="img_sel" value="upload" /> Tranferir imagem do computador: {$img}<br />
					  <input type="radio" id="img_selSl" name="img_sel" value="select" disabled="disabled" /> Selecionar foto da galeria (NF)<br />
					  <input type="radio" id="img_selRm" name="img_sel" value="remove" /> Remover imagem de exibi&ccedil;&atilde;o</td></tr>
	<tr class="c"><td class="c" colspan="2" style="text-align:center"><input type="submit" value="Enviar" /></td></tr>
	</form>
	<!--<tr class="c"><td class="c"><b></b></td><td class="c"></td></tr>-->
	</table>');
}

function showObraRecursosTemplate() {
	return array('template' => '
	<input type="hidden" id="obraID" value="{$obraID}">
	<p style="text-align: center; font-size: 14pt; color:#BE1010;">{$nome}</p>
	<table style="border-width: 0;" width="100%" id="recTable">
	<tr><td class="c" colspan="4"></td></tr>
	<tr class="c"><td class="c" width="25%"><b>Montante</b></td><td class="c" width="40%"><b>Origem</b></td><td class="c" width="25%"><b>Prazo</b></td><td class="c" width="10%"></td></tr>
	{$recurso_tr}
	</table>',
	'recurso_tr'  => '<tr class="c"><td class="c">{$rec_montante}</td><td class="c">{$rec_origem}</td><td class="c">{$rec_prazo}</td><td class="c"></td></tr>',
	'semRec_tr'   => '<tr id="noRecRow"><td colspan="3" style="text-align:center"><b>Nenhum recurso encontrado.</b></td></tr>',
	'addRecLink'  => '<span id="addRecRow" style="text-align:right; display: block; width: 100%"><a href="javascript:addRecurso(true)" class="addRecLink">Adicionar recurso</a></span>',
	'addRecTable' => '<table width="100%" id="addRecTable" style="display:none"><tr class="c"><td class="c" width="25%">R$ <input type="text" id="valor" size="9" /></td><td class="c" width="40%"><input type="text" id="origem" /></td><td class="c" width="25%"><input type="text" id="prazo" /></td width="10%"><td class="c"><a href="javascript:salvaRec(0);" style="font-size:9pt" id="salvaRec">Salvar</a></td></tr></table>');
}

function showObraEtapasTemplate() {
return array('template' => '
	<p style="text-align: center; font-size: 14pt; color:#BE1010;">{$nome}</p>
	<table style="border-width: 0;" width="100%" id="etapasTable">
	<tr class="c"><td class="c"></td><td class="c"></td><td class="c"></td></tr>
	<tr class="c"><td class="c"><b>Etapa</b></td><td class="c"><b>Processo</b></td><td class="c"><b>Detalhes</b></td></tr>
	{$etapa_tr}
	</table>',
	'etapa_tr'      => '<tr class="c"><td class="c">{$etapa_nome}</td><td class="c">{$etapa_proc}</td><td class="c"><a href="javascript:void(0)" onclick="showEtapaDet({$etapaID})">Ver detalhes</a></td></tr>',
	'semEtapa_tr'   => '<tr id="noEtapaRow"><td colspan="3" style="text-align:center"><b>Nenhuma etapa encontrada.</b></td></tr>',
	'addEtapaLink'  => '<span id="addRecRow" style="text-align:right; display: block; width: 100%"><a href="javascript:addEtapa(true)" class="addEtapaLink">Adicionar Etapa</a></span>',
	'etapa_det_tr'  => '<tr class="c" id="det{$etapaID}" style="display:none;"><td class="c" colspan="3">{$etapa_det}</td></tr>',
	'addEtapaTable' => '<table width="100%" id="addEtapaTable" style="display:none; margin: 5px 15px 5px 5px; border: 1px black solid;">
						<tr>
						<td class="c" colspan="2"><span class="header">Cadastro de Etapa</span></td>
						</tr>
						<tr class="c">
						<td class="c" width="35%"><b>Tipo de Etapa: </b></td>
						<td class="c" width="65%">{$tipoEtapa}*</td>
						</tr>
						<tr class="c">
						<td class="c"><b>Respons&aacute;vel: </b></td>
						<td class="c">{$responsavel}*</td>
						</tr>
						<tr class="c">
						<td class="c"><b>Processo: </b></td>
						<td class="c">
							<div id="procEtapaNomes">Nenhum Selecionado</div>
							<input type="hidden" name="procEtapa" id="procEtapa" />
							<a href="javascript:void(0);" id="addProcesso" onclick="escolherDoc(\'procEtapa\')"> Adicionar Processo </a>*
						</td>
						</tr>
						<tr class="c">
						<td><b></b></td>
						<td><input type="button" value="Adicionar" onclick="salvaEtapa(0)" id="salvaEtapa" /></td>
						</tr>
						</table>');
}

function showObraHistoricoTemplate(){
return array('template' => '
	<table style="border-width: 0;" width="100%" id="recTable">
	<tr><td class="c" colspan="4"></td></tr>
	<tr class="c"><td class="c" width="20%" style="text-align: center;"><b>Data</b></td><td class="c" width="15%" style="text-align: center;"><b>Usu&aacute;rio</b></td><td class="c" width="75%" style="text-align: center;"><b>A&ccedil;&atilde;o</b></td></tr>
	{$tr_entradas}
	</table>',
	'entrada_tr' => '<tr class="c"><td class="c" style="text-align: center;">{$entr_data}</td><td class="c" style="text-align: center;">{$entr_user}</td><td class="c">{$entr_texto}</td></tr>',
	'semEntr_tr' => '<tr id="noRecRow"><td colspan="3" style="text-align:center"><b>Nenhuma entrada encontrada.</b></td></tr>');

}

/**
 * gera o formulario para o cadastro de obras colocando ou nao as coordenadas lidas
 * @param array $pos
 */
function showObraCadForm ($pos) {
	$html = '
		<h3>Cadastro de obra</h3>
		<form action="sgo.php?acao=salvarNova" method="post" id="cadNovaObra">
		<table style="border: 0; width:100%" cellpadding="0" cellspacing="0">
			<tbody>
				<tr><td class="c" colspan="3"></td></tr>
				<tr class="c" id="passo1">
					<td class="c"><b>1.</b></td>
					<td class="c"><b>Of&iacute;cio de Requisi&ccedil;&atilde;o:</b></td>
					<td class="c">
						<div id="caixaAviso" style="border: 1px red solid; text-align: center; color: red; font-weight:bold; background-color: yellow; padding: 5px; display: none;"></div>
						<input type="hidden" name="ofir" id="ofir" value="{$ofirID}"/>
						<div id="ofirNomes">{$ofirNome}</div>
						<br />
						<div id="ofirLink">
							<a href="javascript:void(0);" onclick="newDocInNewWindow(\'ofe\',\'ofir\',\'cad\');">Cadastrar Of&iacute;cio de Requisi&ccedil;&atilde;o</a> ou <a href="javascript:void(0);" onclick="escolherDoc(\'ofir\');">Usar documento j&aacute; cadastrado</a>
						</div>
					</td>
				</tr>
				<tr class="c" id="passo2">
					<td class="c"><b>2.</b></td>
					<td class="c"><b>Identifica&ccedil;&atilde;o do solicitante:</b></td>
					<td class="c">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tbody>
								<tr class="c">
									<td class="c" width="40%"><b>Unidade/&Oacute;rg&atilde;o:</b></td>
									<td class="c"><input type="text" class="obrigatorio cadObra" name="unOrgSolic" id="unOrgSolic" size="50" maxlength="250" value="{$unOrgSolic}" style="{$estilo}" />*</td>
								</tr>
								<tr class="c">
									<td class="c" width="40%"><b>Nome:</b></td>
									<td class="c"><input type="text" class="cadObra" name="nomeSolic" id="nomeSolic" size="50" maxlength="250" value="{$nomeSolic}"  style="{$estilo}" /></td>
								</tr>
								<tr class="c">
									<td class="c"><b>Departamento:</b></td>
									<td class="c"><input type="text" class="cadObra" name="deptoSolic" id="deptoSolic" size="25" maxlength="50" value="{$deptoSolic}"  style="{$estilo}" /></td>
								</tr>
								<tr class="c">
									<td class="c"><b>E-mail:</b></td>
									<td class="c"><input type="text" class="cadObra" name="emailSolic" id="emailSolic" size="25" maxlength="100" value="{$emailSolic}"  style="{$estilo}" /></td>
								</tr>	
								<tr class="c">
									<td class="c"><b>Ramal:</b></td>
									<td class="c"><input type="text" class="cadObra int" name="ramalSolic" id="ramalSolic" size="5" maxlength="10" value="{$ramalSolic}"  style="{$estilo}" /></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr class="c" id="passo3">
					<td class="c"><b>3.</b></td>
					<td class="c"><b>Informa&ccedil;&otilde;es da Obra:</b></td>
					<td class="c">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tbody>
								<tr class="c">
									<td class="c" width= "40%"><b>Nome da Obra</b></td>
									<td class="c">
										<input type="text" class="obrigatorio" name="nome" id="nome" size="50" maxlength="250" autocomplete="off"/>*
										<div id="sugestoesObra" style="padding: 3px; margin: 2px; border: 1px #BE1010 solid; display: none;"></div>
									</td>
								</tr>
								<tr class="c">
									<td class="c"><b>Tipo de Obra</b></td>
									<td class="c">
										{$select_tipo} *
									</td>
								</tr>
								<tr class="c">
									<td class="c"><b>Caracter&iacute;stica de Obra</b></td>
									<td class="c">
										{$select_caract} *
									</td>
								</tr>
								<tr class="c">
									<td class="c"><b>Breve descri&ccedil;&atilde;o da obra</b></td>
									<td class="c">
										<textarea id="descricao" name="descricao" rows="3" cols="20" style="width: 85%"></textarea>
									</td>
								</tr>
								<tr class="c">
									<td class="c"><b>&Aacute;rea aproximada da interven&ccedil;&atilde;o</b></td>
									<td class="c">
										<input class="float" type="text" id="dimensao" name="dimensao" id="dimensao" size="10" maxlength="10" />
										<select id="dimensaoUn" name="dimensaoUn" style="text-align: right; background-color: #DDFFDD">
											<option value=""> -- Selecione -- </option>
											<option value="m">m</option>
											<option value="m2" selected="selected">m<sup>2</sup></option>
											<option value="m3">m<sup>3</sup></option>
											<option value="kVA">kVA</option>
										</select>
									</td>
								</tr>
								<tr class="c">
									<td class="c"><b>Haver&aacute; altrera&ccedil;&atilde;o em elementos que contenham amianto?</b>(ex: divis&oacute;rias, telhas)</td>
									<td class="c"><input type="radio" id="amianto" name="amianto" value="1" />Sim | <input type="radio" name="amianto" value="0" />N&atilde;o </td>
								</tr>
								<tr class="c">
									<td class="c"><b>Qual ser&aacute; a ocupa&ccedil;&atilde;o e uso do local?</b></td>
									<td class="c"><input type="text" id="ocupacao" name="ocupacao" size="50" maxlength="200" /></td>
								</tr>
								<tr class="c">
									<td class="c"><b>Quais res&iacute;duos ser&atilde;o gerados ap&oacute;s a ocupa&ccedil;&atilde;o do local?</b></td>
									<td class="c"><input type="text" id="residuos" name="residuos" size="50" maxlength="200" /></td>
								</tr>
								<tr class="c">
									<td class="c"><b>Quantidade de pavimentos:</b></td>
									<td class="c"><input class="int" type="text" id="pavimentos" name="pavimentos" size="2" maxlength="2" /></td>
								</tr>
								<tr class="c">
									<td class="c"><b>A obra ter&aacute; elevador?</b></td>
									<td class="c"><input type="radio" name="elevador" value="1" />Sim | <input type="radio" name="elevador" value="0" />N&atilde;o </td>
								</tr>
								<tr class="c">
									<td class="c" colspan="2"><span style="font-size:12pt; font-weight:bold;">Recursos Financeiros</span></td>
								</tr>
								<tr class="c">
									<td class="c"><b>H&aacute; recursos garantidos?</b></td>
									<td class="c"><input type="radio" id="recursos1" name="recursos" value="1" />Sim | <input type="radio" id="recursos0" name="recursos" value="0" checked="checked" />N&atilde;o </td>
								</tr>
								<tr class="c">
									<td class="c"><b>Montante de recursos garantidos:</b></td>
									<td class="c">R$ <input class="float" type="text" id="montanteRec" name="montanteRec" size="10" maxlength="10" disabled="disabled" /> </td>
								</tr>
								<tr class="c">
									<td class="c"><b>Origem dos recursos:</b></td>
									<td class="c"><input type="text" name="origemRec" id="origemRec" size="30" maxlength="200" disabled="disabled" /> </td>
								</tr>
								<tr class="c">
									<td class="c"><b>Prazo de Conv&ecirc;nios</b></td>
									<td class="c"><input type="text" name="prazoRec" id="prazoRec" size="10" maxlength="10" disabled="disabled" /> (dd/mm/aaaa)</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr class="c" id="passo4">
					<td class="c"><b>4.</b></td>
					<td class="c"><b>Localiza&ccedil;&atilde;o da Obra:</b></td>
					<td class="c">
						{$local}
						<!--Lat: <input type="text" id="latObra" name="latObra" size="7" maxlength="18" value="'.$pos['lat'].'" />&deg; Long: <input type="text" id="lngObra" name="lngObra" size="7" maxlength="18" value="'.$pos['lng'].'" />&deg;
						<br /><span style="font-size: 9pt;">(use valores negativos para coordenadas Sul/Oeste e <b>ponto</b> como separador decimal)</span></td>-->
				</tr>
				<tr class="c" id="passo5">
					<td class="c"><b>5.</b></td>
					<td class="c"><b>Solic. Abertura de Processo:</b></td>
					<td class="c">
						<input type="checkbox" id="abrirSAP" name="abrirSAP" value="1" /> Gerar nova Solicita&ccedil;&atilde;o de Abertura de Processo de Planejamento.
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
		</form>';
	return $html;
}


?>