<?php
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
}

/**
 * trata as variaveis GET colocando em um array organizado.
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
}

/**
 * Monta formulario para cadastro de obra
 */
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

?>