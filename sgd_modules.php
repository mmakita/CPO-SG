<?php
	/**
	 * @version 0.1 18/2/2011 
	 * @package geral
	 * @author Mario Akita
	 * @desc contem os modulos que lidam com a impressao dos modulos na tela 
	 */
	
	/**
	 * @desc mostra os documentos pendentes para um determinado usuario
	 * @param int $userID
	 * @param connection $bd
	 */
	function showDocsPend($userID, $bd){
		$res = $bd->query("SELECT id,labelID,tipoID FROM doc WHERE ownerID = ".$_SESSION["id"]);
		
		$table = '<span class="header">Documentos Pendentes</span>
		<table width="100%" cellspacing="0" cellpadding="0">';
		
		if (!count($res)) {
			$table .= '<tr><td colspan="5"><center><br /><b>N&atilde;o h&aacute; documentos pendentes.</b></center></td></tr>';
		} else {
			$table .= '<tr><td class="c" width="5%"><center><b>N° CPO</b></center></td><td  class="c" width="20%"><b>Tipo/Número</b></td><td class="c" width="30%"><b><center>Emitente</center></b></td><td  class="c" width="35%"><b><center>Assunto</center></b></td><td  class="c" width="10%"><b>Ações</b></td></tr>';
		}
		
		foreach ($res as $r) {
			$doc = new Documento($r['id']);
			$doc->loadCampos($bd);
			
			$acoes = explode(",",$doc->dadosTipo['acoes']);
			
			$table .= '<tr class="c">';
			$table .= '<td class="c"><center>'.$doc->id.'</center></td>';
			$table .= "<td class=\"c\"><a href=\"#\" onclick=\"window.open('sgd.php?acao=ver&docID=".$doc->id."','detalhe".$doc->id."','width=900,height=650,scrollbars=yes,resizable=yes')\">".$doc->dadosTipo['nome']." ".$doc->numeroComp.'</a></td>';
			
			$emitente = explode(" - ",$doc->emitente);
			$emitenteF = $emitente[0];
			if(isset($emitente[1])) {
				$emitente = explode("/",$emitente[1]);
				$emitenteF .= ' - '.$emitente[count($emitente)-1];
			}
			
			$table .= '<td class="c"><center>'.$emitenteF.'</center></td>';
			if (isset($doc->campos['assunto']))
				$table .= '<td class="c"><center>'.$doc->campos['assunto'].'</center></td>';
			else
				$table .= '<td class="c"><center> - </center></td>';
			$table .= '<td class="c">';
			
			foreach ($acoes as $acao) {
				if ($acao){
					$r = $bd->query("SELECT nome,abrv FROM label_acao WHERE id = $acao");
					$table .= "<a href=\"#\" onclick=\"window.open('sgd.php?acao=".$r[0]['abrv']."&docID=".$doc->id."','detalhe".$doc->id."','width=950,height=650,scrollbars=yes,resizable=yes')\">".$r[0]['nome'].'</a><br />';
				}
			}
			
			$table .= '</td></tr>';
		}
		return $table.'</table><br />';
	}
	
	/**
	 * @desc mostra os conteudos dos campos do documento (exceto emissor)
	 * @param Documento $doc documento passado por parametro
	 */
	function showDetalhes($doc){
		//adiciona o titulo
		$html = '<script type="text/javascript">
		</script>
		<span class="headerLeft">Dados do Documento</span>';
		 
		//le os nomes dos campos desse tipo de documento
		$campos = explode(",", $doc->dadosTipo['campos']);

		if (!$campos[0])
			return $html."<br /><center><b>Não há dados dispon&iacute;veis</b></center><br />";		
		
		$html .= '<table border="0" width="100%"><tr><td width=20%></td><td width=80%></td></tr>';
		$html .= '<tr class="c"><td><b>N&uacute;mero do Doc (CPO):</b> </td><td>'.$doc->id.'</td></tr>';
		
		//mostra tabela com os dados deste tipo de documento
		foreach ($campos as $c) {
			if(strpos($doc->dadosTipo['emitente'],$c) === false){
				$c = montaCampo($c, $doc->bd,'mostra',$doc->campos);
				$html .= '<tr class="c"><td><b>'.$c['label'].':</b> </td><td>'.$c['valor'].'</td></tr>';
			}
		}
		//mostra campos extras de documentos, obras e arquivos anexos
		if (isset($doc->campos["documento"]) && $doc->campos["documento"] != '')
			$html .= '<tr class="c"><td><b>Documentos Anexos: </b></td><td> '.showDocAnexo($doc->getDocAnexoDet()).'</td></tr>';
		if ($doc->anexado){
			$dp = new Documento($doc->docPaiID);
			$dp->loadTipoData($doc->bd);
			$html .= '<tr class="c"><td><b>Documento Pai:</b> </td><td>'.showDocAnexo(array(array("id" => $dp->id, "nome" => $dp->dadosTipo['nome']." ".$dp->numeroComp))).'</td></tr>';
		}	
		if (isset($doc->campos["obra"]) && $doc->campos['obra'] != 0)
			$html .= '<tr class="c"><td><b>Obra Ref:</b> </td><td>'.$doc->campos[$doc->campos["obra"]].'</td></tr>';
		
		$html .= '<tr class="c"><td><b>Arquivos Anexos:</b> </td><td>'.showArqAnexo($doc->anexo).'</td></tr>';
		
		return $html."</table>";
	}
	
	/**
	 * @desc mostra os dados do emissor
	 * @param Documento $doc
	 */
	function showEmissor($doc){
		$html = '<span class="headerLeft">Dados do Emissor</span>';
		
		$campo = array();
		$data = false;
		
		if($doc->dadosTipo['emitente'])
			$html .= '<table border="0" width="100%"><tr><td width=20%></td><td width=80%></td></tr>';
		else
			return $html."<br /><center><b>Não há dados dispon&iacute;veis</b></center><br />";
			
		$campos = explode(",", $doc->dadosTipo['campos']);
		foreach ($campos as $c) {
			$c = montaCampo($c, $doc->bd, 'mostrar', $doc->campos);
			if (strpos($doc->dadosTipo['emitente'],$c['nome']) !== false){
				$html .= '<tr class="c"><td><b>'.$c['label'].'</b>: </td><td>'.$c['valor'].'</td></tr>';
				$data = true;
			}
		}
		
		return $html.'</table>';
	}
	
	/**
	 * Mostra o historico do documento
	 * @param Documento $doc
	 */
	function showHist($doc){
		$html = '<span class="headerLeft">Histórico do Documento</span>';
		
		$res = $doc->getHist();
		
		if (count($res) == 0) {
			return $html."<center><b>Nenhum dado dispon&iacute;vel.</b></center><br />";
		}else{
			$html .= '<table border="0" width="100%" cellpadding="0" cellspacing="0">
			<tr><td width="100" class="cc"><b>data</b></td><td width="100" class="cc"><b>usu&aacute;rio</b></td><td class="cc"><b>a&ccedil;&atilde;o</b></td></tr>'; 
			foreach ($res as $r) {
				if($r['despacho']){
					$html .= '<tr class="c"><td class="cc" style="border: 0;">'.$r['data'].'</td><td class="cc" style="border: 0;">'.$r['username'].'</td><td class="c" style="border: 0;">'.$r['acao'].'</td></tr>';
					$html .= '<tr class="c"><td class="c" colspan="3"><b>Despacho: </b>'.$r['despacho'].'</td></tr>'; 
				} else {
					$html .= '<tr class="c"><td class="cc">'.$r['data'].'</td><td class="cc">'.$r['username'].'</td><td class="c">'.$r['acao'].'</td></tr>';
				}
			}
			$html .= '</table>';
		}
		
		return $html;
	}
	
	/**
	 * @desc mostra os documentos anexos com os ids passados por parametro
	 * @param array $anexos
	 * @param connection $bd
	 */
	function showDocAnexo($anexos){
		$html = "";
		if($anexos == '')
			return '';
		
		foreach ($anexos as $a) {
			$html .= "<a href=\"#\" onclick=\"window.open('sgd.php?acao=ver&docID=".$a['id']."','detalhe".$a['id']."','width=900,height=650,scrollbars=yes,resizable=yes')\">Documento ".$a['id'].": ".$a['nome']."</a><br />";
		}
		
		return $html;
	}
	
	/**
	 * @desc monta o menu lateral do visualizador de doc
	 * @param Documento $doc
	 * @param BD $bd
	 */
	function showAcoes($doc,$bd){
		$html = '<script type="text/javascript" src="scripts/menu_mini.js"></script>
		<span class="menuHeader" onclick="showDet(1)">Ver Detalhes</span><br />';
		
		if ($doc->owner == $_SESSION['id'])
			$html .= '<span class="menuHeader" onclick="showDet(2)">Despachar</span><br />';
		
		$acoes = explode(",", $doc->dadosTipo['acoes']);
		foreach ($acoes as $acao){
			$res = $bd->query("SELECT * FROM label_acao WHERE id = ".$acao);
			if($_SESSION['perm'][$acao])
				$html .= '<span class="menuHeader" onclick="showDet(3)">'.$res[0]['nome'].'</span><br />';
		}
		return $html;
	}
	
	/**
	 * mostra os campos para anexar arquivo
	 */
	function showAnexar($tipo = "f", $doc = null){
		$html = '';
		if($tipo == "f") $html .= '<span class="headerLeft">Anexar Arquivo</span><form action="sgd.php?acao=anexar" method="post" enctype="multipart/form-data">';
		if($doc != null && $doc->id != 0) $html .= '<input type="hidden" name="id" value="'.$doc->id.'" />';
		$html .= '<div id="fileUpCell">
		<div id="arqs"></div>
		<input type="file" id="arq1" name="arq1" onclick="showInputFile(2)" />
		</div>';
		if($tipo == "f") $html .= '<input type="submit" value="Enviar" />
		</form>';
		return $html;
	}
	
	/**
	 * Monta e mostra a seção de despacho para um documento passado por parametro
	 * @param Documento $doc
	 */
	function showDesp($tipo = "f", $deptos ,$doc = null){
		//doc ja esta anexado a aoutro e nao pode ser despachado
		if($doc != null && $doc->id != 0 && $doc->anexado)
			return '<b>Esse documento já está anexado a outro e n&atilde;o pode ser despachado.</b>';
		//montagem do form
		$html = '
		<script type="text/javascript" src="scripts/jquery.js"></script>
		<script type="text/javascript" src="scripts/jquery.autocomplete.js"></script>
		<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
		<script type="text/javascript" src="scripts/despacho.js">		
		</script>';
		if($tipo == "f") $html.= '<span class="headerLeft">Despachar Documento</span>
		<form action="sgd.php?acao=despachar" method="post">';
		if($doc != null && $doc->id != 0) $html .= '<input type="hidden" name="id" value="'.$doc->id.'" />';
		if($tipo == "f" || $tipo == "sf") $html .= '<textarea id="despacho" name="despacho" rows="4" style="width:98%;">Digite o despacho aqui.</textarea><br />';
		$html.= '<b>Despachar para:</b> <br />
		<select id="para" name="para">
		<option selected> --Selecione-- </option>
		<option name="" disabled style="background-color: #808080; color:white">-> Solicitante</option>
		<option id="solic" value="solic">Solicitante</option>
		<option name="" disabled style="background-color: #808080; color:white">-> CPO</option>';
		foreach ($deptos as $dep) {
			$html .= '<option id="'.$dep.'" value="'.$dep.'">'.$dep.'</option>';
		}
		$html.= '<option id="arq" value="cpo_arq">Arquivo</option>
		<option name="" disabled style="background-color: #808080; color:white">-> Outra Unidade</option>
		<option id="ext" value="ext">Outra Unidade/&Oacute;rg&atilde;o</option>
		<option name="" disabled style="background-color: #808080; color:white">-> Outro</option>
		<option id="outr" value="outro">Outro</option>
		</select><br />
		<select id="subp" name="funcID"></select>
		<input type="text" size=25 name="outro" id="outro" />
		<input type="text" size=100 id="despExt" name="despExt" autocomplete="off" /><br />';
		if($tipo == "f") $html.= '<input type="submit" value="Despachar" />
		</form>';
		return $html;
	}
	
	/**
	 * Mostra formulario de Cadastramento/Criacao de documento
	 * @param string $acao
	 * @param string $tipo
	 * @param connection $bd
	 */
	function showForm($acao,$tipo,$bd){
		//define arquivo de template
		$template = "templates/template_".$acao.".php";

		//carrega arquivo de template
		$html = file_get_contents($template);
				
		//monta os campos de busca
		$dados = $bd->query("SELECT * FROM label_doc WHERE nomeAbrv = '".$tipo."'");
		
		//variaveis que vao guardar os campos gerais e de busca e emitente
		$cGeral = '';
		$cBusca = '';
		$cGeralNome = '';
		$cEmitente =  '';
		$conteudo = '';
		$cBuscaNomes = ''; 
		
		if($acao == "cad"){
			//separa os campos
			$campos = explode(",", $dados[0]['campos']);
			
			//separa os campos e cria os inputs
			foreach ($campos as $c) {
				$c = montaCampo($c, $bd, 'cad',$dados);
				$c['nomeCampo'] = explode(",",$c['nome']);
				if(strpos($dados[0]['campoBusca'], $c['nomeCampo'][0]) === false){
					//nao eh campo de busca, cria o input na parte de campos
					if($cGeralNome) $cGeralNome .= ",";
					$cGeralNome .= $c['nome'];
					$cGeral .= '<tr class="c"><td><b>'.$c['label'].':</b></td><td width="255">'.$c['cod'].'</td></tr>';
					
				}else{
					//eh campo de busca, cria o input na area de busca
					$cBusca .= '<b>'.$c['label'].':</b> '.$c['cod'];
					$cBuscaNomes .= $c['nome'].',';
					//cria inputs ocultos no campo geral para passar as infos de busca para prox pagina
					foreach (explode(",",$c['nome']) as $campo) {
						$cGeral .= '<input type="hidden" id="_'.$campo.'" name="_'.$campo.'" value="" />';
					}
				}
			}
			$cBuscaNomes = rtrim($cBuscaNomes,",");
			
			//adicao dos campos ocultos
			$cBusca .= '
			<input type="hidden" name="labelID" id="labelID" value="'.$dados[0]['id'].'" />
			<input type="hidden" name="tabBD" id="tabBD" value="'.$dados[0]['tabBD'].'" />
			<input type="hidden" name="camposBusca" id="camposBusca" value="'.$cBuscaNomes.'" />';
			
			//adicao da tabela para alinhar os campos gerais
			$cGeral .= '<input type="hidden" name="tipoDocCad" id="tipoDocCad" value="'.$tipo.'" /> 
			<input type="hidden" name="camposBusca" id="camposBusca" value="'.$cBuscaNomes.'" />
			<input type="hidden" name="id" id="id" value="0" />
			<input type="hidden" name="action" id="action" value="'.$acao.'" />
			<input type="hidden" name="camposGerais" id="camposGerais" value="'.$cGeralNome.'" />';
			$cGeral = '<table width="100%" border=0>'.$cGeral.'</table>';
			
		}elseif ($acao = "novo"){
			$html = file_get_contents($template);
			
			//separa os campos
			$campos = explode(",", $dados[0]['campos']);

			//separa os campos e cria os inputs
			foreach ($campos as $c) {
				$c = montaCampo($c, $bd, 'cad');
				if(strpos($dados[0]['emitente'], $c['nome']) === false){
					//nao eh campo de emitente, cria o input na parte de campos
					if($c['nome'] == 'conteudo')
						$conteudo = '<tr class="c"><td colspan="2"><b>'.$c['label'].':</b><br />'.$c['cod'].'</td></tr>';
					else 
						$cGeral .= '<tr class="c"><td><b>'.$c['label'].':</b></td><td width="251">'.$c['cod'].'</td></tr>';
						
				}else{//eh campo emitente, cria o campo (se houver) na parte lateral
					//eh campo de busca, cria o input na area de busca
					$cEmitente .= '<b>'.$c['label'].':</b> '.$c['cod'];
				}
			}
			$cGeral .= $conteudo;
			
			$cGeral = '
			<input type="hidden" name="tipoDocCad" id="tipoDocCad" value="'.$tipo.'" />
			<input type="hidden" name="id" id="id" value="0" />
			<input type="hidden" name="action" id="action" value="'.$acao.'" />
			<table width="100%" border=0>'.$cGeral.'</table>';
		}
		$historico = '<div id="hist" class="cadDisp"></div>';
		
		$documentos = '
		<b>Documentos Anexos:</b><br />
		<div id="docsAnexosNomes" class="cadDisp"></div><input type="hidden" name="docsAnexos" id="docsAnexos" />
		<a id="addDocLink" href="#" onclick="window.open(\'sgd.php?acao=busca_mini&onclick=adicionar&target=docsAnexos\',\'addDoc\',\'width=750,height=550,scrollbars=yes,resizable=yes\')">';
		if($dados[0]['docAnexo']) $documentos .= 'Adicionar Documento';
		$documentos .= '</a><br /><br />';
		
		$obra = '
		<b>Obra Ref:</b><br />
		<div id="obra" class="cadDisp"></div><input type="hidden" name="obrasAnexas" id="obrasAnexas" />
		<a id="addObraLink" href="#" onclick="window.open(\'\',\'addDoc\',\'width=750,height=550,scrollbars=yes,resizable=yes\')">';
		if($dados[0]['obra']) $obra .= 'Adicionar Obra';
		$obra .= '</a><br /><br />';
		
		$empresa = '
		<b>Empresa Ref:</b><br />
		<div id="empresa" class="cadDisp"></div><input type="hidden" name="emprAnexas" id="emprAnexas" />
		<a id="addEmpresaLink" href="#" onclick="window.open(\'empresa.php?acao=buscar&onclick=adicionar\',\'addEmpr\',\'width=750,height=550,scrollbars=yes,resizable=yes\')">';
		if($dados[0]['empresa']) $empresa .= 'Adicionar Empresa';
		$empresa .= '</a><br /><br />';
		
		$recebimento = showReceb();
		
		$html = str_replace('{$campos_busca}', $cBusca, $html);
		$html = str_replace('{$campos}', $cGeral, $html);
		$html = str_replace('{$emitente}', $cEmitente, $html);
		$html = str_replace('{$documentos}', $documentos, $html);
		$html = str_replace('{$obra}', $obra, $html);
		$html = str_replace('{$empresa}', $empresa, $html);
		$html = str_replace('{$anexarArq}', showAnexar('sf'), $html);
		$html = str_replace('{$historico}', $historico, $html);
		$html = str_replace('{$recebimento}', $recebimento, $html);
		$html = str_replace('{$despacho}', showDesp('sf',getDeptos($bd),null), $html);
			
		return $html;
	}
	/**
	 * Monta os campos de recebimento
	 */
	function showReceb() {
		$html = '<b>n&deg; Rela&ccedil;&atilde;o de Remessa:</b> <input type="text" id="rrNumReceb" name="rrNumReceb" size="2" maxlength="4" />/<input type="text" id="rrAnoReceb" name="rrAnoReceb" size="2" maxlength="4" value="'.date("Y").'" /> <b>Un/Org de Origem:</b> <input type="text" id="unOrgReceb" name="unOrgReceb" size="60" />
		<script type="text/javascript">
		$(document).ready(function(){
			$("#unOrgReceb").autocomplete("unSearch.php",{minChars:2,matchSubset:1,matchContains:true,maxCacheLength:20,extraParams:{\'show\':\'un\'},selectFirst:true,onItemSelect: function(){$("#unOrgReceb").focus();}});	
		});</script>';
		return $html;
	}
	
	/**
	 * Mostra links para visualizacao dos documentoa anexos
	 * @param array $anexos
	 */
	function showArqAnexo($anexos){
		$html = '';
		
		if (strlen($anexos[0]) > 0) {
			foreach ($anexos as $a){
				$html .= "<a href=\"#\" onclick=\"window.open('files/$a','ArqAnexo','width=900,height=650,scrollbars=yes,resizable=yes')\">".$a.'</a><br />';
			}
		}else{
			$html .= '<b>N&atilde;o h&aacute; arquivos anexos.</b>';
		}
		
		return $html;
	}
	
	/**
	 * Le o nome da obra e cria um link para mostrar os detalhes dela
	 * @param int $id
	 * @todo integrar com a classe de obras
	 */
	function showObraLink($id){
		
	}
	
	/**
	 * Monta a formulario de busca simples
	 * @var string $onclick acao a ser realizada quando um item de resultado for clicado (ex: ver)
	 * @var mysql link $bd
	 */
	function showBuscaForm($onclick,$bd){
		$html = '
		<script type="text/javascript" src="scripts/busca_doc.js"></script>
		<script type="text/javascript" src="scripts/jquery.autocomplete.js"></script>
		<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
		<input type="hidden" id="onclick" value="'.$onclick.'" />
		<form id="buscaForm">
		<table width="100%" border="0">
		<tr><td width=35%><b>Selecione o tipo de documento:</b><br />
		';
		$res = $bd->query("SELECT * FROM label_doc");
		
		foreach ($res as $r){
			$html .= '<input type="radio" class="tipoDoc" id="'.$r['nomeAbrv'].'" value="'.$r['nomeAbrv'].'" name="tipoDoc" /> <span id="nome_'.$r['nomeAbrv'].'">'.$r['nome']."</span><br />\n";
			$nomesTotal = '';
			$campos = explode(",", $r['campos']);
			$div[$r['nomeAbrv']] = '<table width="100%" border="0">';
			foreach ($campos as $c) {
				$dadosCampo = montaCampo($c, $bd, "bus");
				$div[$r['nomeAbrv']] .= '<tr class="c"><td class="c" width="30%">'.$dadosCampo['label'].': </td><td width="70%">'.str_ireplace(array('name="','id="','("#'), array('name="' . $r['nomeAbrv'] . '_', 'id="' . $r['nomeAbrv'] . '_', '("#' . $r['nomeAbrv'] . '_'), $dadosCampo['cod']).'</td></tr>'; 
				$nomesTotal .= $dadosCampo['nome'].',';
			}
			$nomesTotal = rtrim($nomesTotal,",");
			$html .= '<input type="hidden" id="camposEsp_'.$r['nomeAbrv'].'" value="'.$r['nomeAbrv']."_".str_ireplace(",", ",".$r['nomeAbrv']."_", $nomesTotal).'" />';
			$div[$r['nomeAbrv']] .= '</table>';
		}
		$html .= '<input type="radio" class="tipoDoc" id="_outro_" value="_outro_" name="tipoDoc" /> Buscar todos os tipos'."<br /></td>";
		$html .= '<td><span class="campoDoc"><b>Campos de busca:</b><br /><br /><b>Dica:</b> Para buscar todos os documentos desse tipo, deixe todos os campos em branco.<br /><br /></span>
		<table width="100%" border="0" class="campoDoc">
		<tr class="c"><td class="c" style="width:30%">N&uacute;mero do documento (CPO):         </td><td style="width:70%"><input id="numCPO"      type="text" size=20 name="s_cpo"      /></td></tr>
		<tr class="c"><td class="c" style="width:30%">Data de Cria&ccedil;&atilde;o:            </td><td style="width:70%"><input id="dataCr"  type="text" size=20 name="s_criacao"  /></td></tr>
		</table>';
		foreach ($div as $tipoDoc => $cod) {
			$html .= '<div id="campos_'.$tipoDoc.'" class="campoDocEsp" style="width: 100%;" class="campoDoc">
			'.$cod.'</div>';
		}
		$html .= '<table width="100%" border="0" class="campoDoc">
		<tr id="td_desp" class="c"><td style="width:30%" class="c">Hist&oacute;rico: </td><td style="width:70%;"><input id="desp" type="text" size=20 name="s_desp"     /></td></tr>
		<input type="hidden" id="s_tipoDoc" />
		<input type="hidden" id="s_selectedDocCampos" />
		</table>
		
		<center><input type="submit" id="btnBuscar" value="Buscar" class="campoDoc" /></center>
		</form>
		</td></tr></table>';
		return $html;
	}
	
	/**
	 * Salva os dados do documento no BD
	 * @param array $dados
	 * @param mysql link $bd
	 */
	function salvaDados($dados,$bd) {
		$DEBUG = 0;
		$html = '<span class="header">Relat&oacute;rio de Cadastro</span>';
		if($dados['id'] == 0){//verifica se eh novo documento (nao ha ID)
			$doc = new Documento($dados['id']);
			$doc->bd = $bd;
			$doc->dadosTipo['nomeAbrv'] = $dados['tipoDocCad'];
			$doc->loadTipoData($bd);
			
			if($dados['action'] == 'cad'){
				//verifica se o documento ja esta cadastrado TODO
				$query = "SELECT * FROM ".$doc->dadosTipo['tabBD']." WHERE ";
				foreach (explode(",", $dados['camposBusca']) as $d) {
					$campoDados = montaCampo($d, $bd,'cad',$dados,true);
					if(!$campoDados['parte']) $query .= $d."='".$campoDados['valor']."' AND ";
				}
				$query = rtrim($query, "AND ");
				$r = $bd->query($query);
				if(count($r))
					return 'Documento j&aacute; cadastrado.<br /><a href="sgd.php?acao=cad&tipoDoc='.$doc->dadosTipo['nomeAbrv'].'">Cadastrar outro(a) '.$doc->dadosTipo['nome'].'</a> <br /> <a href=""></a>';
				
				//array para armazenar os campos do formulario
				//campos de busca
				$camposGerais = explode(",", $dados['camposGerais'].','.$dados['camposBusca']);
				foreach ($camposGerais as $cb) {
					$r = $bd->query("SELECT tipo,attr,extra FROM label_campo WHERE nome ='".$cb."'");
					$cbo = $cb;
					if(!isset($dados[$cb]) && isset($dados['_'.$cb])){
						$cb = '_'.$cb;
					}
					if(!isset($r[0])){
						unset($dados[$cb]);
						unset($camposGerais[$cb]);
						continue;
					}
					//tratamento de campos para salvamento
					if(!isset($dados[$cb]) || $dados[$cb] == ''){
						if($r[0]['tipo'] == "checkbox")
							$campos[$cbo] = 0;
						if($r[0]['tipo'] == "autoincrement"){
							$r2 = $bd->query("SELECT ".$cb." FROM ".$doc->dadosTipo['tabBD']." ORDER BY ".$cb." DESC LIMIT 1");
							$campos[$cbo] = $r2[0][$cb] + 1;
						}
					}
					if($r[0]['tipo'] == 'composto'){
						$partes = explode("+",$r[0]['attr']);
						$campos[$cbo] = '';
						foreach ($partes as $p) {							
							if(!isset($dados[$p]) && isset($dados['_'.$p])) $p = '_'.$p;
							if (isset($dados[$p])){
								$campos[$cbo] .= $dados[$p];
								unset($dados[$p]);
								unset($camposGerais[array_search(substr($p,1,strlen($p)),$camposGerais)]);
							} else {
								$campos[$cbo] .= str_replace('"','',$p);
							}
						}
					} elseif($r[0]['tipo'] == 'parte'){
						continue;
					} else {
						if(isset($dados[$cb]))
							$campos[$cbo] = $dados[$cb];
					} 
						if(isset($dados[$cb])){
							$campos[$cbo] =  htmlentities ($campos[$cbo],ENT_QUOTES);
							$campos[$cbo] =  str_replace("\n", "<br />", $campos[$cbo]);
						}
				}
				
			}elseif($dados['action'] == 'novo'){
				$camposForm = explode(",", $doc->dadosTipo['campos']);
				foreach ($camposForm as $cb) {//tratamento de campos
					if(!isset($dados[$cb]) || $dados[$cb] == ''){
						$r = $bd->query("SELECT tipo,attr,extra FROM label_campo WHERE nome ='".$cb."'");
						if($r[0]['tipo'] == "checkbox") {
							$dados[$cb] = 0;
						} elseif($r[0]['tipo'] == "autoincrement"){
							if(strpos($r[0]['extra'], "current_year") !== false){
								$r2 = $bd->query("SELECT t.".$cb." FROM ".$doc->dadosTipo['tabBD']." AS t LEFT JOIN doc AS d ON t.id=d.tipoID WHERE d.data>".mktime(0,0,0,1,1,date("Y"))." AND d.labelID=".$doc->dadosTipo['id']." ORDER BY d.id DESC LIMIT 1");
								//print($r2[0][$cb]."SELECT t.".$cb." FROM ".$doc->dadosTipo['tabBD']." AS t LEFT JOIN doc AS d ON t.id=d.tipoID WHERE d.data>".mktime(0,0,0,1,1,date("Y"))." AND d.labelID=".$doc->dadosTipo['id']." ORDER BY t.".$cb." DESC LIMIT 1");
								if (isset($r2[0])){
									$dados[$cb] = (($r2[0][$cb]) + 1) . '/' . date("Y");
								} else {
									$dados[$cb] = 1 . '/' . date("Y");
								}
							} else {
								$r2 = $bd->query("SELECT ".$cb." FROM ".$doc->dadosTipo['tabBD']." ORDER BY ".$cb." DESC LIMIT 1");
								if (isset($r2[0])){
									$dados[$cb] = $r2[0][$cb] + 1;
								} else {
									$dados[$cb] = 1;
								}
							}
							//print ">>>".$dados[$cb];
						}elseif(strpos($r[0]['extra'], "current_user") !== false){
							$dados[$cb] = $_SESSION['id'];
						}elseif($r[0]['tipo'] == 'composto'){
							$partes = explode("+",$r[0]['attr']);
							foreach ($partes as $p) {
								if (isset($dados[$p])){
									$dados[$cb] .= $dados[$p];
									unset($dados[$p]);
								} else {
									$dados[$cb] .= str_replace('"','',$p);
								}
								
							}
						}elseif($r[0]['tipo'] == 'parte'){
							continue;
						}
						
					}
					//print_r($dados);
					//exit();
					//print $cb . "=" .   htmlentities ($dados[$cb],ENT_QUOTES)."<br>";
					$campos[$cb] = htmlspecialchars_decode( htmlentities ($dados[$cb],ENT_QUOTES), ENT_QUOTES);
					
					if($cb == "conteudo"){
						$campos[$cb] = str_replace(array("'","\n"),array("\'",""), $campos[$cb]);
					} else {
						$campos[$cb] = str_replace(array("\n","'"),array("<br />","\'"), $campos[$cb]);
					}
				}
			}
			
			$doc->campos = $campos;
			
			if($DEBUG) $html .= "Dados lidos com sucesso.<br />";	
			//adicao dos anexos ao documento
			if(strlen($dados['docsAnexos'])){
				$doc->campos['documento'] = rtrim($dados['docsAnexos'],",");
				if($DEBUG) $html .= "Documento(s) adicionado(s) com sucesso.<br />";
			}
			if(strlen($dados['obrasAnexas'])){
				$doc->campos['obra'] = rtrim($dados['obrasAnexas'],",");
				if($DEBUG) $html .= "Obra(s) adicionada(s) com sucesso.<br />";
			}
			if(strlen($dados['emprAnexas'])){
				$doc->campos['empresa'] = rtrim($dados['emprAnexas'],",");
				if($DEBUG) $html .= "Empresa(s) adicionada(s) com sucesso.<br />";
			}
			
			//salvar campos no BD
			if ($doc->salvaCampos()){
				if($DEBUG) $html .= "Campos salvos com sucesso.<br />";
			} else { 
				if($DEBUG) $html .= "<b>Erro ao salvar campos. O documento n&atilde;o foi criado.</b><br />";
				return $html;
			}
			
			//salvar doc no BD
			if ($doc->salvaDoc(0)){
				if($DEBUG) $html .= "Documento criado com sucesso.<br />";
			} else { 
				if($DEBUG) $html .= "<b>Erro ao criar documento.</b><br />";
				return $html;
			}
			
			//logar historico de recebimento
			if($dados['action'] == 'cad' && $dados['unOrgReceb'] && $dados['rrNumReceb'] && $dados['rrAnoReceb']){
				if ($doc->doLogHist($_SESSION['id'],"Recebido de ".$dados['unOrgReceb']." via Rel. Remessa n&deg;".$dados['rrNumReceb']."/".$dados['rrAnoReceb'],"")) {
					if($DEBUG) $html .= "Hist&oacute;rico criado com sucesso.<br />";
				} else {
					if($DEBUG) $html .= '<b>Falha ao criar hist&oacute;rico de Recebimento</b><br />';
				}
			}
			
			
			//logar historico
			if ($doc->doLogHist($_SESSION['id'],"Criou o documento.","")){
				if($DEBUG) $html .= "Hist&oacute;rico criado com sucesso.<br />";
			}else{
				if($DEBUG) $html .= '<b>Falha ao criar hist&oacute;rico</b><br />';
			}
			
			//upload de arquivos
			if($DEBUG) $html .= "<br /><b>Arquivos</b><br />";			
			$relArq = $doc->doUploadFiles();
			
			if($DEBUG) $html .= montaRelArq($relArq,$bd);
			$anexoSalvo = $doc->salvaAnexos();
			
			if($DEBUG) 
				if ($anexoSalvo === true) {
					$html .= "<br />Arquivos anexados com sucesso.<br />";
				}elseif ($anexoSalvo === false){
					$html .= "<br /><b>Erro ao anexar arquivos.</B><br />";
				}elseif ($anexoSalvo === 0){
					$html .= "N&atilde;o h&aacute; arquivo anexado.<br /><br />";
				}
			
			if(!$doc->doFlagAnexado())
				if($DEBUG) $html .= "<b>Erro ao salvar dados nos documentos filhos</b><br />";
			
			if(!isset($dados['funcID'])) $dados['funcID'] = false;

			//gravar despacho
			//$html .= showDespStatus($doc, array('para' => $dados['para'] ,"outro" => $dados['outro'], 'funcID' => $dados['funcID'], 'despExt' => $dados['despExt'], 'despacho' => htmlentities($dados['despacho'])));
						
			//CLAUSULA ESPECIAL PARA RR - caso seja RR, logar que os documentos foram enviados por ela
			if($doc->dadosTipo['nomeAbrv'] == 'rr'){
				foreach (explode(",", $dados['docsDesp']) as $ddid) {
						if($ddid > 0){
						$docDesp = new Documento($ddid);
						$docDesp->bd = $bd;
						$docDesp->doLogHist($_SESSION['id'], "Despachou o documento para ".$doc->campos['unOrgDest']." via Rel. Remessa CPO n&deg:".$doc->campos['numeroRR'],'');
					}
				}
			}
			
			$despStatus = showDespStatus($doc, array('para' => $dados['para'] ,"outro" => $dados['outro'], 'funcID' => $dados['funcID'], 'despExt' => $dados['despExt'], 'despacho' => htmlentities($dados['despacho'])),'hideFB');
			
			//gerar PDF
			if($dados['action'] == 'novo'){
				$pdfFile = geraPDF($doc->id,$bd);
				if($pdfFile)
					if($DEBUG) $html .= '<b>Arquivo PDF gerado com sucesso. Clique para <a href="files/'.$pdfFile.'">visualizar/baixar o documento PDF </a>.</b>';
				else
					if($DEBUG) $html .= '<b>Erro ao gerar arquivo PDF.</b>';
			}
			
			//Reload dos campos para impressao
			$doc->loadDados($bd);
			$doc->loadCampos($bd);
			//impressao dos dados
			$html .= 'Documento gerado com o N&uacute;mero CPO: <b><font color="red"><a href="#" onclick="window.open('."'sgd.php?acao=ver&amp;docID=".$doc->id."','detalhe".$doc->id."','width=900,height=650,scrollbars=yes,resizable=yes'".')">'.$doc->id.'</a></font></b>
			<br />'.showDetalhes($doc).
			'<br /><b>Outras A&ccedil;&otilde;es:</b>
			<br /><a href="sgd.php?acao='.$dados['action'].'&tipoDoc='.$doc->dadosTipo['nomeAbrv'].'"> Cadastrar novo(a) '.$doc->dadosTipo['nome'].'</a>'
			.$despStatus;
			
			//Imprimir RR?
			//@todo
			
			$html .= '<br /> <a href="index.php">Voltar para p&aacute;gina inicial.</a>';
				
			//LOG dos usuarios
			doLog($_SESSION['username'],'Criou o documento '.$doc->id,$bd);
			
		}else{//se doc ja existe, faz o despacho
			$doc = new Documento($dados['id']);
			$doc->dadosTipo['nomeAbrv'] = $dados['tipoDocCad'];
			$doc->bd = $bd;
			
			if(!isset($dados['funcID'])) $dados['funcID'] = false;
			
			//logar historico de recebimento
			if($dados['action'] == 'cad' && $dados['unOrgReceb'] && $dados['rrNumReceb'] && $dados['rrAnoReceb']){
				if ($doc->doLogHist($_SESSION['id'],"Recebido de ".$dados['unOrgReceb']." via Rel. Remessa n&deg;".$dados['rrNumReceb']."/".$dados['rrAnoReceb'],'')) {
					if($DEBUG) $html .= "Hist&oacute;rico criado com sucesso.<br />";
				} else {
					if($DEBUG) $html .= '<b>Falha ao criar hist&oacute;rico de Recebimento</b><br />';
				}
			}
			
			//gravar despacho
			$html .= showDespStatus($doc,array('para' => $dados['para'] ,"outro" => $dados['outro'], 'funcID' => $dados['funcID'], 'despExt' => $dados['despExt'], 'despacho' => htmlentities($dados['despacho'])));
			
			$html .= '<br /> <a href="index.php">Voltar para p&aacute;gina inicial.</a>';
			
			
			//$html .= '<br /> O documento j&aacute; est&aacute; cadastrado sob o n&uacute;mero CPO <b><font color="red">'.$doc->id.'</font></b>. Apenas o despacho foi gravado.<br />
			//<a href="#" onclick="window.open('."'sgd.php?acao=ver&amp;docID=".$doc->id."','detalhe".$doc->id."','width=900,height=650,scrollbars=yes,resizable=yes'".')">Clique aqui para ver os detalhes do documento.</a>';
		}
	return $html;
	}
	
	/**
	 * Monta o relatorio de upload dos arquivos (DEBUG)
	 * @param Array $files
	 */
	function montaRelArq($files,$bd){
		$html = '';
		
		foreach ($files['success'] as $file) {
			$html .= '<i>'.$file.'</i>: Arquivo foi anexado com sucesso.<br />';
			doLog($_SESSION['username'], "Anexou o arquivo $file.", $bd);
		}
		
		foreach ($files['failure'] as $file) {
			$html .= '<i>'.$file['name'].'</i>: Erro ao anexar arquivo (Erro '.$files['errorID'].').<br />';
			doLog($_SESSION['username'], "Obteve erro ao adicionar $file.", $bd);
		}
		return $html;
	}
	
	/**
	 * Retorna string de feedback correspondente para dado return da funcao de salvamento. (DEBUG)
	 * @param string $desp
	 **/
	function showDespStatus($doc,$dados,$mode = 'showFB') {
		$html = "";
		$desp = $doc->doDespacha($_SESSION['id'],$dados);
		if($mode != "hideFB"){
			if($desp === false){
				$html = "<b>Falha ao gravar despacho.</b><br />";
			}elseif($desp === 0){
				$html = "<b>N&atilde;o h&aacute; despacho. Documento est&aacute; pendente para o usu&aacute;rio atual.</b><br />";
			}else{
				$html = "Despacho para $desp gravado com sucesso.<br />";
			}
		}
		
		if($dados['para'] == 'ext' && $dados['despExt'] && $doc->dadosTipo['nomeAbrv'] != 'rr')
				$html .= '<br /><a href="#" onclick="window.open('."'sgd.php?acao=novoDocVar&action=novo&tipoDoc=rr&docsDesp=".$doc->id."&unOrgDest=".$dados['despExt']."&para=ext&despExt=".$dados['despExt']."&despacho=".$dados['despacho']."','novaRR','width=900,height=650,scrollbars=yes,resizable=yes'".')">Gerar Rela&ccedil;&atilde;o de Remessa</a>.<br />';
		
		return $html;
	}
	
	/**
	 * 
	 * @param array $GET
	 */
	function trataGetVars($GET,$bd){
		$doc = new Documento(0);
		$doc->dadosTipo['nomeAbrv'] = $GET['tipoDoc'];
		$doc->loadTipoData($bd);
		
		foreach (explode(",",$doc->dadosTipo['campos']) as $campo) {
			$nome = montaCampo($campo,$bd);
			$camposNomes[] = $nome['nome'];
		}
		
		foreach ($camposNomes as $campo) {
			if (isset($GET[$campo])) {
				$dados[$campo] = $GET[$campo];
			} else {
				$dados[$campo] = '';
			}
		}
		
		if($GET['action'] == 'cad'){
			$dados['action'] = 'cad';
			$dados['camposGerais'] = $doc->dadosTipo['campos'];
			$dados['camposBusca'] = '';
		}elseif ($GET['action'] == 'novo'){
			$dados['action'] = 'novo';
		}
		
		$dados['tipoDocCad'] = $GET['tipoDoc'];
		$dados['id'] = 0;
		if(isset($GET['para']))    $dados['para'] = $GET['para'];         else   $dados['para'] = '';
		if(isset($GET['despExt'])) $dados['despExt'] = $GET['despExt'];   else   $dados['despExt'] = '';
		if(isset($GET['outro']))   $dados['outro'] = $GET['outro'];       else   $dados['outro'] = '';
		if(isset($GET['despacho']))$dados['despacho'] = $GET['despacho']; else   $dados['despacho'] = '';
		
		if (isset($GET['docsAnexos'])) {
			$dados['docsAnexos'] = $GET['docsAnexos'];
		} else {
			$dados['docsAnexos'] = '';
		}
		if (isset($GET['obrasAnexas'])) {
			$dados['obrasAnexas'] = $GET['obrasAnexas'];
		} else {
			$dados['obrasAnexas'] = '';
		}
		if (isset($GET['emprAnexas'])) {
			$dados['emprAnexas'] = $GET['emprAnexas'];
		} else {
			$dados['emprAnexas'] = '';
		}
		
		return $dados;
	}
?>