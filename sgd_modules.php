<?php
	/**
	 * @version 1.0 25/5/2011 
	 * @package geral
	 * @author Mario Akita
	 * @desc contem os modulos que lidam com a impressao dos modulos na tela 
	 */

	/**
	 * @desc mostra os documentos pendentes para um determinado usuario
	 * @param int $userID
	 * @param connection $bd
	 */
	function showDocsPend($userID){
		//le varial global contendo conexao com BD
		global $bd;
		//seleciona todos os documentos que estao com determinado usuario
		$res = getPendentDocs($userID,$_SESSION['area']);
		//comeca a construcao da tabela de documentos pendentes
		$table = '<span class="header">Documentos Pendentes</span>
		<table width="100%" cellspacing="0" cellpadding="0">';
		//se nao houver nenhum documento, mostra mensagem indicando isso
		if (!count($res)) {
			$table .= '<tr><td colspan="5"><center><br /><b>N&atilde;o h&aacute; documentos pendentes.</b></center></td></tr>';
		//se houver, cria a primeira linha da tabela
		} else {
			$table .= '<tr><td class="c" width="5%"><center><b>N° CPO</b></center></td><td  class="c" width="20%"><b>Tipo/Número</b></td><td class="c" width="30%"><b><center>Emitente</center></b></td><td  class="c" width="35%"><b><center>Assunto</center></b></td><td  class="c" width="10%"><b>Ações</b></td></tr>';
		}
		//pra cada documento pendente encontrado, cria uma linha na tabela a ser mostrada
		foreach ($res as $r) {
			//inicializa um novo documento generico
			$doc = new Documento($r['id']);
			//carrega os dados especificos do tipo de documento
			$doc->loadCampos();
			//le as acoes possiveis para o tipo de documento para mostrar
			$acoes = explode(",",$doc->dadosTipo['acoes']);
			//cria uma linha
			$table .= '<tr class="c">';
			$table .= '<td class="c"><center>'.$doc->id.'</center></td>';
			$table .= "<td class=\"c\"><a href=\"#\" onclick=\"window.open('sgd.php?acao=ver&docID=".$doc->id."','detalhe".$doc->id."','width=900,height=650,scrollbars=yes,resizable=yes')\">".$doc->dadosTipo['nome']." ".$doc->numeroComp.'</a></td>';
			//preenche o emitente
			$emitente = explode(" - ",$doc->emitente);
			$emitenteF = $emitente[0];
			if(isset($emitente[1])) {
				$emitente = explode("/",$emitente[1]);
				$emitenteF .= ' - '.$emitente[count($emitente)-1];
			}
			$table .= '<td class="c"><center>'.$emitenteF.'</center></td>';
			//preenche o assunto
			if (isset($doc->campos['assunto']))
				$table .= '<td class="c"><center>'.$doc->campos['assunto'].'</center></td>';
			else
				$table .= '<td class="c"><center> - </center></td>';
			$table .= '<td class="c">';
			//preenche as acoes possiveis
			foreach ($acoes as $acao) {
				if ($acao){
					$r = getAcao($acao);
					$table .= "<a href=\"#\" onclick=\"window.open('sgd.php?acao=".$r[0]['abrv']."&docID=".$doc->id."','detalhe".$doc->id."','width=950,height=650,scrollbars=yes,resizable=yes')\">".$r[0]['nome'].'</a><br />';
				}
			}
<<<<<<< HEAD
			//mostra linha para adicionar obra
			if(isset($doc->campos['solicObra']) && $doc->campos['solicObra'] && checkPermission(11))
				$table .= '<a href="sgo.php?acao=cadastrar&amp;docOrigemID='.$doc->id.'">Cadastrar Obra</a><br />';
=======
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
			//fecha tags da linha
			$table .= '</td></tr>';
		}
		//fecha as tags da tabela e retorna o codigo html da tabela
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
		//se o documento nao tiver campos, retorna mensagem
		if (!$campos[0])
			return $html."<br /><center><b>Não há dados dispon&iacute;veis</b></center><br />";		
		//senao, comeca a montar a tabela
		$html .= '<table border="0" width="100%"><tr><td width=20%></td><td width=80%></td></tr>';
<<<<<<< HEAD
		$html .= '<tr class="c"><td><b>N&uacute;mero do Doc (CPO):</b> </td><td><span id="docID">'.$doc->id.'</span></td></tr>';
=======
		$html .= '<tr class="c"><td><b>N&uacute;mero do Doc (CPO):</b> </td><td>'.$doc->id.'</td></tr>';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		
		//mostra tabela com os dados deste tipo de documento
		foreach ($campos as $c) {
			if(strpos($doc->dadosTipo['emitente'],$c) === false){
<<<<<<< HEAD
				$c = montaCampo($c,'edt',$doc->campos);
				$html .= '<tr class="c"><td><b>'.$c['label'].':</b> </td><td><span id="'.$c['nome'].'_val">'.$c['valor'].'</span>';
				if(($doc->owner == $_SESSION['id'] || $doc->criador == $_SESSION['id'] || ($doc->owner == -1 && $doc->areaOwner == $_SESSION['area'])) && checkPermission(2)  && $c['cod'] != ''){
					$html .= '<span id="'.$c['nome'].'_edit" style="display:none;">
					'.$c['cod'].'
					</span>
					<a id="'.$c['nome'].'_link" href="javascript:editVal(\''.$c['nome'].'\')" style="font-size:8pt;">Editar</a>';
				}
				$html .= '</td></tr>';
			}
		}
		
		//mostra campos extras de documentos, obras e arquivos anexos
		if (isset($doc->campos["documento"]) && $doc->campos["documento"] != '')
			$html .= '<tr class="c"><td><b>Documentos Anexos: </b></td><td> '.showDocAnexo($doc->getDocAnexoDet()).'</td></tr>';
		
=======
				$c = montaCampo($c,'mostra',$doc->campos);
				$html .= '<tr class="c"><td><b>'.$c['label'].':</b> </td><td>'.$c['valor'].'</td></tr>';
			}
		}
		//mostra campos extras de documentos, obras e arquivos anexos
		if (isset($doc->campos["documento"]) && $doc->campos["documento"] != '')
			$html .= '<tr class="c"><td><b>Documentos Anexos: </b></td><td> '.showDocAnexo($doc->getDocAnexoDet()).'</td></tr>';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		//se esse documento foi anexado a algum outro documento, mostra o documento pai
		if ($doc->anexado){
			$dp = new Documento($doc->docPaiID);
			$dp->loadTipoData();
			$html .= '<tr class="c"><td><b>Documento Pai:</b> </td><td>'.showDocAnexo(array(array("id" => $dp->id, "nome" => $dp->dadosTipo['nome']." ".$dp->numeroComp))).'</td></tr>';
		}
		//se ha obra anexada, monta o campo pertinente
<<<<<<< HEAD
		//if (isset($doc->campos["obra"]) && $doc->campos['obra'] != 0)
		//	$html .= '<tr class="c"><td><b>Obra Ref:</b> </td><td>'.$doc->campos[$doc->campos["obra"]].'</td></tr>';
=======
		if (isset($doc->campos["obra"]) && $doc->campos['obra'] != 0)
			$html .= '<tr class="c"><td><b>Obra Ref:</b> </td><td>'.$doc->campos[$doc->campos["obra"]].'</td></tr>';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		//mostra os arquivos anexos
		$html .= '<tr class="c"><td><b>Arquivos Anexos:</b> </td><td>'.showArqAnexo($doc->anexo).'</td></tr>';
		//retorna o cod html da tabela
		return $html."</table>";
	}
	
	/**
	 * @desc mostra os dados do emissor
	 * @param Documento $doc
	 */
	function showEmissor($doc){
		//monta o cabecalho
		$html = '<span class="headerLeft">Dados do Emissor</span>';
		//inicializacao de variaveis
		$campo = array();
		$data = false;
		//se ha emitente para o documento
		if($doc->dadosTipo['emitente']) {
			//mostra os canpos relativos ao emitente
			$html .= '<table border="0" width="100%"><tr><td width=20%></td><td width=80%></td></tr>';
		} else {
			//senao mostra mensagem pertinente
			return $html."<br /><center><b>Não há dados dispon&iacute;veis</b></center><br />";
		}
		//separa os nomes dos campos
		$campos = explode(",", $doc->dadosTipo['campos']);
		//para cada campo
		foreach ($campos as $c) {
			//pega os dados do campo
<<<<<<< HEAD
			$c = montaCampo($c, 'edt', $doc->campos);
			//verifica se o campo eh de emitente
			if (strpos($doc->dadosTipo['emitente'],$c['nome']) !== false){
				//se for, gera o codigo HTML
				$html .= '<tr class="c"><td><b>'.$c['label'].'</b>: </td><td><span id="'.$c['nome'].'_val">'.$c['valor'].'</span>';
				if(($doc->owner == $_SESSION['id'] || $doc->criador == $_SESSION['id'] || ($doc->owner == -1 && $doc->areaOwner == $_SESSION['area'])) && checkPermission(2)  && $c['cod'] != ''){
					$html .= '<span id="'.$c['nome'].'_edit" style="display:none;">
					'.$c['cod'].'
					</span>
					<a id="'.$c['nome'].'_link" href="javascript:editVal(\''.$c['nome'].'\')" style="font-size:8pt;">Editar</a>';
				}
				$html .= '</td></tr>';
=======
			$c = montaCampo($c, 'mostrar', $doc->campos);
			//verifica se o campo eh de emitente
			if (strpos($doc->dadosTipo['emitente'],$c['nome']) !== false){
				//se for, gera o codigo HTML
				$html .= '<tr class="c"><td><b>'.$c['label'].'</b>: </td><td>'.$c['valor'].'</td></tr>';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
				$data = true;
			}
		}
		//retorna codigo HTML
		return $html.'</table>';
	}
	
	/**
	 * Mostra o historico do documento
	 * @param Documento $doc
	 */
	function showHist($doc){
		//cria o cabecalho
		$html = '<span class="headerLeft">Histórico do Documento</span>';
		//le o hitorico do documento
		$res = $doc->getHist();
		//se nao houver entrada de historico, avisa nao ha historico
		if (count($res) == 0) {
			return $html."<center><b>Nenhum dado dispon&iacute;vel.</b></center><br />";
		//se nao, cria a tabela de historico
		}else{
			//tags de inicio da tabela
			$html .= '<table border="0" width="100%" cellpadding="0" cellspacing="0">
			<tr><td width="100" class="cc"><b>data</b></td><td width="100" class="cc"><b>usu&aacute;rio</b></td><td class="cc"><b>a&ccedil;&atilde;o</b></td></tr>'; 
			//para cada entrada no historico
			foreach ($res as $r) {
<<<<<<< HEAD
				//print_r($r);
				//cria uma linha para este documento
				if($r['tipo'] == 'criacao') {
					$acao = 'Criou este documento.';
				} elseif ($r['tipo'] == 'obs') {
					$acao = 'Adicionou observa&ccedil;&atilde;o a este documento.';
				} elseif ($r['tipo'] == 'entrada') {
					$acao = 'Registrou entrada deste documento de '.$r['unidade'];
				} elseif ($r['tipo'] == 'saida') {
					$acao = 'Registou saida deste documento para '.$r['unidade'];
				} elseif ($r['tipo'] == 'despIntern') {
					$acao = 'Despachou este documento para '.$r['unidade'];
				} else {
					$acao = $r['acao'];
				}
				//cria uma linha para este documento
				$html .= '<tr class="c" style="cursor:pointer;" ';
				if($r['despacho']) $html .= 'onclick="showDesp('.$r['id'].')"';
				$html .= '><td class="cc">'.$r['data'].'</td><td class="cc" >'.$r['username'].'</td><td class="c">'.$acao.'</td></tr>';
				
				if($r['despacho']){
					$html .= '<tr id="desp'.$r['id'].'" class="c" style="display:none"><td class="c" colspan="3"><b>'.$r['label'].'</b>: '.$r['despacho'].'</td></tr>';
				}
				/*if($r['despacho']){
=======
				//cria uma linha para este documento
				if($r['despacho']){
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
					//se ha despacho, cria a linha de despacho
					$html .= '<tr class="c"><td class="cc" style="border: 0;">'.$r['data'].'</td><td class="cc" style="border: 0;">'.$r['username'].'</td><td class="c" style="border: 0;">'.$r['acao'].'</td></tr>';
					$html .= '<tr class="c"><td class="c" colspan="3"><b>Despacho: </b>'.$r['despacho'].'</td></tr>'; 
				} else {
					//senao, apenas cria a linha de acao
					$html .= '<tr class="c"><td class="cc">'.$r['data'].'</td><td class="cc">'.$r['username'].'</td><td class="c">'.$r['acao'].'</td></tr>';
<<<<<<< HEAD
				}*/
=======
				}
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
			}
			//fecha tag para tabela
			$html .= '</table>';
		}
		//retorna o codigo HTMl da tabela gerada
		return $html;
	}
	
	/**
	 * @desc mostra os documentos anexos com os ids passados por parametro
	 * @param array $anexos
	 * @param connection $bd
	 */
	function showDocAnexo($anexos){
		//inicializacao de variaveis
		$html = "";
		//se nao houver anexos, nao retorna nada.
		if($anexos == '')
			return '';
		//para cada anexode um documento
		foreach ($anexos as $a) {
			//cria um link para visualizar esse doc anexo.
			$html .= "<a href=\"#\" onclick=\"window.open('sgd.php?acao=ver&docID=".$a['id']."','detalhe".$a['id']."','width=900,height=650,scrollbars=yes,resizable=yes')\">Documento ".$a['id'].": ".$a['nome']."</a><br />";
		}
		//retorna o cod HTML a tabela gerada
		return $html;
	}
	
	/**
	 * @desc monta o menu lateral do visualizador de doc
	 * @param Documento $doc
	 * @param BD $bd
	 */
	function showAcoes($doc){
		//inicializacao de variaveis.
		$html = '<script type="text/javascript" src="scripts/menu_mini.js"></script>
		<a href="sgd.php?acao=ver&docID='.$doc->id.'"><span class="menuHeader">Ver Detalhes</span></a><br />';
		//se o usuario eh dono do documento e ele tem permissao para despachar
		if ($doc->owner == $_SESSION['id'] || ($doc->owner == -1 && $doc->areaOwner == $_SESSION['area']))
			//mostra o link para depsachar
			$html .= '<a href="sgd.php?acao=desp&docID='.$doc->id.'"><span class="menuHeader">Despachar</span></a><br />';
		//demais acoes
		$acoes = explode(",", $doc->dadosTipo['acoes']);
		//para cada acao
		
		foreach ($acoes as $acao){
			//le os dados da acao do BD
			if($acao){
				$res = getAcao($acao);
				//verifica se tem permissao para faze-la
				if($_SESSION['perm'][$acao])
					//adiciona link para a acao no menu
					$html .= '<a href="sgd.php?acao='.$res[0]['abrv'].'&docID='.$doc->id.'"><span class="menuHeader">'.$res[0]['nome'].'</span></a><br />';
			}
		}
		
<<<<<<< HEAD
		if ($doc->owner == 0 && !$doc->anexado && $doc->dadosTipo['nomeAbrv'] != 'rr') {
			$html .= '<a href="sgd.php?acao=entrada&docID='.$doc->id.'"><span class="menuHeader">Registrar Entrada deste doc.</span></a><br />';
		}
		$html .= '<a href="sgd.php?acao=anexDoc&docID='.$doc->id.'&onclick=anex"><span class="menuHeader">Anexar Documento</span></a><br />';
		
		if($_SESSION['perm'][10])
			$html .= '<a href="sgd.php?acao=atribObra&docID='.$doc->id.'&onclick=anex"><span class="menuHeader">Atribuir a uma obra</span></a><br />';
=======
		if ($doc->owner == 0) {
			$html .= '<a href="sgd.php?acao=entrada&docID='.$doc->id.'"><span class="menuHeader">Registrar Entrada deste doc.</span></a><br />';
		}
		
		
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		//retorna o codigo HTML das acoes para o documento
		return $html;
	}
	
	/**
	 * mostra os campos para anexar arquivo
	 */
	function showAnexar($tipo = "f", $doc = null){
		//inicializacao de variavele
		$html = '';
		//se nao for cadastro de documento, coloca campo oculto com o id pra prox pagina
<<<<<<< HEAD
		if($doc != null && $doc->id != 0) $html .= '
		<form id="fileUpForm" action="sgd.php?acao=anexArq&docID='.$doc->id.'&feedback" method="post" enctype="multipart/form-data">
		<input type="hidden" name="id" value="'.$doc->id.'" />';
=======
		if($doc != null && $doc->id != 0) $html .= '<input type="hidden" name="id" value="'.$doc->id.'" />';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		//inclui HTML do formulario para upload\
		$html .= '<div id="fileUpCell">
		<div id="arqs"></div>
		<input type="file" id="arq1" name="arq1" onclick="showInputFile(2)" />
		</div>';
		//se o tipo de exibicao for o formulario completo, coloca o botao enviar
<<<<<<< HEAD
		if($tipo == "f") $html .= '<input type="submit" value="Enviar" id="sendFiles" />
=======
		if($tipo == "f") $html .= '<input type="submit" value="Enviar" />
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		</form>';
		//retorna HRML do form
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
		//se for tipo formulario completo, cria as tags de form
		if($tipo == "f") $html.= '<span class="headerLeft">Despachar Documento</span>
<<<<<<< HEAD
		<form action="sgd.php?acao=despachar" method="post" id="despachoForm">';
=======
		<form action="sgd.php?acao=despachar" method="post">';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		//cria input para levar o id par aa prox pagina
		if($doc != null && $doc->id != 0) $html .= '<input type="hidden" name="id" value="'.$doc->id.'" />';
		if($tipo == "f" || $tipo == "sf") $html .= '<textarea id="despacho" name="despacho" rows="4" style="width:98%;">Digite o despacho aqui.</textarea><br />';
		//cria select box para despacho
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
		//retorna o codigo html do form
		return $html;
	}
	
	function showEntradaForm($deptos,$doc){
		$html = '<span class="headerLeft">Despachar Documento</span>
		<form action="sgd.php?acao=despachar&entrada=1" method="post">';
		$html .= showDesp("sf",$deptos, $doc);
		$html .= '<span style="color: #BE1010; font-weight: bold;">Rela&ccedil;&atilde;o de Remessa de Entrada</span><br />';
		$html .= showReceb();
		$html .= '<br />
		<center><input type="submit" value="Enviar" /></center> 
		</form>';
		return $html;
	}
	/**
	 * Mostra formulario de Cadastramento/Criacao de documento
	 * @param string $acao
	 * @param string $tipo
	 * @param connection $bd
	 */
	function showForm($acao,$tipo){
		//define arquivo de template
		$template = "templates/template_".$acao.".php";

		//carrega arquivo de template
		$html = file_get_contents($template);
				
		//monta os campos de busca
		$dados = getDocTipo($tipo);
		
		//variaveis que vao guardar os campos gerais e de busca e emitente
		$cGeral = '';
		$cBusca = '';
		$cGeralNome = '';
		$cEmitente =  '';
		$conteudo = '';
		$cBuscaNomes = ''; 
		
<<<<<<< HEAD
		//(integracao OBRAS) gera campo para avisar a proxima pagina que ela deve exportar o docID para pagina pai
		if(($_GET['acao'] == 'novo_mini' || $_GET['acao'] == 'cad_mini') && isset($_GET['targetInput'])) {
			$cGeral .= '<input type="hidden" name="targetInput" id="targetInput" value="'.$_GET['targetInput'].'" />';
		}
		
		//gera campos para cadastro
=======
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		if($acao == "cad"){
			//separa os campos
			$campos = explode(",", $dados[0]['campos']);
			
			//para cada campo do documento
			foreach ($campos as $c) {
				//monta o HTML do campo
				$c = montaCampo($c, 'cad',$dados);
				//
				$c['nomeCampo'] = explode(",",$c['nome']);
				if(strpos($dados[0]['campoBusca'], $c['nomeCampo'][0]) === false){
					//nao eh campo de busca, cria o input na parte de campos
					if($cGeralNome) $cGeralNome .= ",";
					$cGeralNome .= $c['nome'];
<<<<<<< HEAD
					$cGeral .= '<tr class="c"><td style="width: 50%;"><b>'.$c['label'].':</b></td><td style="width: 50%;">'.$c['cod'].'</td></tr>';
=======
					$cGeral .= '<tr class="c"><td><b>'.$c['label'].':</b></td><td width="255">'.$c['cod'].'</td></tr>';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
					
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
			//tira a virgula do final
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
			//coloca osinputs dentro da tabela
<<<<<<< HEAD
			$cGeral = '<table width="80%" border="0">'.$cGeral.'</table>';
=======
			$cGeral = '<table width="100%" border=0>'.$cGeral.'</table>';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		//cria os inpouts para novo documento
		}elseif ($acao = "novo"){
			//carrega o template
			$html = file_get_contents($template);
			
			//separa os campos
			$campos = explode(",", $dados[0]['campos']);

			//separa os campos e cria os inputs
			foreach ($campos as $c) {
				$c = montaCampo($c, 'cad');
				if(strpos($dados[0]['emitente'], $c['nome']) === false){
					//nao eh campo de emitente, cria o input na parte de campos
					if($c['nome'] == 'conteudo')
						$conteudo = '<tr class="c"><td colspan="2"><b>'.$c['label'].':</b><br />'.$c['cod'].'</td></tr>';
					else 
<<<<<<< HEAD
						$cGeral .= '<tr class="c"><td style="width: 50%;"><b>'.$c['label'].':</b></td><td style="width: 50%;">'.$c['cod'].'</td></tr>';
=======
						$cGeral .= '<tr class="c"><td><b>'.$c['label'].':</b></td><td width="251">'.$c['cod'].'</td></tr>';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
						
				}else{//eh campo emitente, cria o campo (se houver) na parte lateral
					//eh campo de busca, cria o input na area de busca
					$cEmitente .= '<b>'.$c['label'].':</b> '.$c['cod'];
				}
			}
			$cGeral .= $conteudo;
			//cria campos ocultos adicionais
			$cGeral = '
			<input type="hidden" name="tipoDocCad" id="tipoDocCad" value="'.$tipo.'" />
			<input type="hidden" name="id" id="id" value="0" />
			<input type="hidden" name="action" id="action" value="'.$acao.'" />
			<table width="100%" border=0>'.$cGeral.'</table>';
		}
		//cria o campo para  historico
		$historico = '<div id="hist" class="cadDisp"></div>';
		//cria campo para anexar documentos
<<<<<<< HEAD
		/*$documentos = '
=======
		$documentos = '
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		<b>Documentos Anexos:</b><br />
		<div id="docsAnexosNomes" class="cadDisp"></div><input type="hidden" name="docsAnexos" id="docsAnexos" />
		<a id="addDocLink" href="#" onclick="window.open(\'sgd.php?acao=busca_mini&onclick=adicionar&target=docsAnexos\',\'addDoc\',\'width=750,height=550,scrollbars=yes,resizable=yes\')">';
		//coloca os campos no documento caso seja possivel adicionar documentos
		if($dados[0]['docAnexo']) $documentos .= 'Adicionar Documento';
		$documentos .= '</a><br /><br />';
		//ccria campos para adicionar obras
		$obra = '
		<b>Obra Ref:</b><br />
		<div id="obra" class="cadDisp"></div><input type="hidden" name="obrasAnexas" id="obrasAnexas" />
		<a id="addObraLink" href="#" onclick="window.open(\'\',\'addDoc\',\'width=750,height=550,scrollbars=yes,resizable=yes\')">';
		//coloca os campos no documento caso seja possivel adicionar obras
		if($dados[0]['obra']) $obra .= 'Adicionar Obra';
		$obra .= '</a><br /><br />';
		//cria campos para adicionar empresa
		$empresa = '
		<b>Empresa Ref:</b><br />
		<div id="empresa" class="cadDisp"></div><input type="hidden" name="emprAnexas" id="emprAnexas" />
		<a id="addEmpresaLink" href="#" onclick="window.open(\'empresa.php?acao=buscar&onclick=adicionar\',\'addEmpr\',\'width=750,height=550,scrollbars=yes,resizable=yes\')">';
		//coloca os campos dos documentos para adicionar empresa
		if($dados[0]['empresa']) $empresa .= 'Adicionar Empresa';
<<<<<<< HEAD
		$empresa .= '</a><br /><br />';*/
=======
		$empresa .= '</a><br /><br />';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		//coloca codigo de recebimento
		$recebimento = showReceb();
		//coloca os elementos no template nas posicoes corretas
		$html = str_replace('{$campos_busca}', $cBusca, $html);
		$html = str_replace('{$campos}', $cGeral, $html);
		$html = str_replace('{$emitente}', $cEmitente, $html);
<<<<<<< HEAD
		//$html = str_replace('{$documentos}', $documentos, $html);
		//$html = str_replace('{$obra}', $obra, $html);
		//$html = str_replace('{$empresa}', $empresa, $html);
=======
		$html = str_replace('{$documentos}', $documentos, $html);
		$html = str_replace('{$obra}', $obra, $html);
		$html = str_replace('{$empresa}', $empresa, $html);
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		$html = str_replace('{$anexarArq}', showAnexar('sf'), $html);
		$html = str_replace('{$historico}', $historico, $html);
		$html = str_replace('{$recebimento}', $recebimento, $html);
		$html = str_replace('{$despacho}', showDesp('sf',getDeptos(),null), $html);
		//retorna o cod HTML do formulario
		return $html;
	}
	/**
	 * Monta os campos de recebimento
	 */
	function showReceb() {
		//adiciona campo para numero da RR de entrada e unidade de origem
		$html = '<b>n&deg; Rela&ccedil;&atilde;o de Remessa:</b>
		<input type="text" id="rrNumReceb" name="rrNumReceb" size="3" maxlength="5" />/<input type="text" id="rrAnoReceb" name="rrAnoReceb" size="2" maxlength="4" value="'.date("Y").'" />
		<br />
		<b>Un/Org Expedidor:</b> <input type="text" id="unOrgReceb" name="unOrgReceb" size="60" />
		<script type="text/javascript">
		$(document).ready(function(){
			$("#unOrgReceb").autocomplete("unSearch.php",{minChars:2,matchSubset:1,matchContains:true,maxCacheLength:20,extraParams:{\'show\':\'un\'},selectFirst:true,onItemSelect: function(){$("#unOrgReceb").focus();}});	
		});</script>';
		//retorna HTML dos campos
		return $html;
	}
	
	/**
	 * Mostra links para visualizacao dos documentoa anexos
	 * @param array $anexos
	 */
	function showArqAnexo($anexos){
		//inicializacao de variaveis
		$html = '';
		//se houver anexos
		if (strlen($anexos[0]) > 0) {
			//para cada anexo
			foreach ($anexos as $a){
				//cria um link para o arquivo
				$html .= "<a href=\"#\" onclick=\"window.open('files/$a','ArqAnexo','width=900,height=650,scrollbars=yes,resizable=yes')\">".$a.'</a><br />';
			}
		//se nao houver anexos
		}else{
			//produz mensagem avisando
			$html .= '<b>N&atilde;o h&aacute; arquivos anexos.</b>';
		}
		//retorna o codigo HTML dos anexos
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
	 * @var mysql_link $bd
	 */
	function showBuscaForm($onclick){
		//inicializacao dos scripts e inicializacao da tabela
		$html = '
<<<<<<< HEAD
		<input type="hidden" id="onclick" value="'.$onclick.'" />
		<form id="buscaForm" action="" method="post">
		<table width="100%" border="0">
		<tr class="buscaFormTable"><td width="35%" colspan="3"><b>Efetuar busca nos seguintes tipos de documento:</b><br />
		';
		//le todos os tipos de documento
		$res = getAllDocTypes();
		//para cada tipo de documento separados em 3 colunas
		$col = array(0 => '', 1 => '', 2 => '');
		$i = 0;
		foreach ($res as $r){
			//cria um radio para selecionar esse tipo de documento
			$col[$i%3] .= '<input type="checkbox" class="tipoDoc" id="'.$r['nomeAbrv'].'" value="'.$r['nomeAbrv'].'" name="tipoDoc" /> <span id="nome_'.$r['nomeAbrv'].'">'.$r['nome']."</span><br />\n";
			$i++;
		}
		//cria link para adicionar todos os documentos
		$col[0] .= '<a href="javascript:checkAll();">(des) marcar todos</a>';
		$html .= '
		<tr class="buscaFormTable"><td>'.$col[0].'</td><td>'.$col[1].'</td><td>'.$col[2].'</td></tr>
		<tr class="buscaFormTable"><td colspan="3"><br /><b>Com os seguintes campos:</b></td></tr>
		<tr class="buscaFormTable"><td colspan="3"><div id="camposBusca">Primeiro, selecione algum tipo de documento para efetuar a busca</div>
		<tr class="novaBuscaBtn" style="display:none"><td colspan="3"><center><input type="button" value="Nova Busca" onclick="novaBusca()" /></center></td></tr>
		<tr><td colspan="3"><b>Resultados:</b></td></tr>
		<tr><td colspan="3">
			<div id="resBusca">
			
			</div>
		</td></tr>
=======
		<script type="text/javascript" src="scripts/busca_doc.js"></script>
		<script type="text/javascript" src="scripts/jquery.autocomplete.js"></script>
		<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
		<input type="hidden" id="onclick" value="'.$onclick.'" />
		<form id="buscaForm">
		<table width="100%" border="0">
		<tr><td width=35%><b>Selecione o tipo de documento:</b><br />
		';
		//le todos os tipos de documento
		$res = getAllDocTypes();
		//para cada tipo de documento
		foreach ($res as $r){
			//cria um radio para selecionar esse tipo de documento
			$html .= '<input type="radio" class="tipoDoc" id="'.$r['nomeAbrv'].'" value="'.$r['nomeAbrv'].'" name="tipoDoc" /> <span id="nome_'.$r['nomeAbrv'].'">'.$r['nome']."</span><br />\n";
			//inicializa a var.
			$nomesTotal = '';
			//separa os campos desse tipo de documento
			$campos = explode(",", $r['campos']);
			//inicializa a tabela do tipo de documento
			$div[$r['nomeAbrv']] = '<table width="100%" border="0">';
			//para cada campo desse tipo de documento
			foreach ($campos as $c) {
				//monta o inout de busca correspondente
				$dadosCampo = montaCampo($c, "bus");
				//monta a linha da tabela usando o input
				$div[$r['nomeAbrv']] .= '<tr class="c"><td class="c" width="30%">'.$dadosCampo['label'].': </td><td width="70%">'.str_ireplace(array('name="','id="','("#'), array('name="' . $r['nomeAbrv'] . '_', 'id="' . $r['nomeAbrv'] . '_', '("#' . $r['nomeAbrv'] . '_'), $dadosCampo['cod']).'</td></tr>'; 
				//gurada o nome do campo adicionado
				$nomesTotal .= $dadosCampo['nome'].',';
			}
			//retira a virgula do final dos nomes
			$nomesTotal = rtrim($nomesTotal,",");
			//cria campo oculto com o nome dos campos do formulario desse tipo de documento
			$html .= '<input type="hidden" id="camposEsp_'.$r['nomeAbrv'].'" value="'.$r['nomeAbrv']."_".str_ireplace(",", ",".$r['nomeAbrv']."_", $nomesTotal).'" />';
			//fecha tag tabela
			$div[$r['nomeAbrv']] .= '</table>';
		}
		//cria radio com para selecao de 'outros' (todos) os documentos
		$html .= '<input type="radio" class="tipoDoc" id="_outro_" value="_outro_" name="tipoDoc" /> Buscar todos os tipos'."<br /></td>";
		//cria campos comuns a todos os documentos
		$html .= '<td><span class="campoDoc"><b>Campos de busca:</b><br /><br /><b>Dica:</b> Para buscar todos os documentos desse tipo, deixe todos os campos em branco.<br /><br /></span>
		<table width="100%" border="0" class="campoDoc">
		<tr class="c"><td class="c" style="width:30%">N&uacute;mero do documento (CPO):         </td><td style="width:70%"><input id="numCPO"      type="text" size=20 name="s_cpo"      /></td></tr>
		<tr class="c"><td class="c" style="width:30%">Data de Cria&ccedil;&atilde;o:            </td><td style="width:70%"><input id="dataCr"  type="text" size=20 name="s_criacao"  /></td></tr>
		</table>';
		//gera, no template, o lugar onde deverao ser inseridos os campos correspondentes aos tipos de doc
		foreach ($div as $tipoDoc => $cod) {
			//cria HTML da divisao
			$html .= '<div id="campos_'.$tipoDoc.'" class="campoDocEsp" style="width: 100%;" class="campoDoc">
			'.$cod.'</div>';
		}
		//cria campo de historico comum a todos os documentos
		$html .= '<table width="100%" border="0" class="campoDoc">
		<tr id="td_desp" class="c"><td style="width:30%" class="c">Hist&oacute;rico: </td><td style="width:70%;"><input id="desp" type="text" size=20 name="s_desp"     /></td></tr>
		<input type="hidden" id="s_tipoDoc" />
		<input type="hidden" id="s_selectedDocCampos" />
		</table>		
		<center><input type="submit" id="btnBuscar" value="Buscar" class="campoDoc" /></center>
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		</form>
		</td></tr></table>';
		//retorna o cod HTML do formulario
		return $html;
	}
	
	/**
	 * Salva os dados do documento no BD
	 * @param array $dados
	 * @param mysql link $bd
	 */
<<<<<<< HEAD
	function salvaDados($dados,$bd) { 
=======
	function salvaDados($dados,$bd) {
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		//variavel debug deve ficar desativada em producao. Eh utilizada apenas para debugar a insercao de doc
		//quando =1, gera o relatorio completo da insercao do documento e mostra onde ocorreu um possivel erro
		$DEBUG = 0;
		//gera o cabecalho
		$html = '<span class="header">Relat&oacute;rio de Cadastro</span>';
		if($dados['id'] == 0){//verifica se eh novo documento (nao ha ID)
			//se for cadastro ou geracao de novo documento
			//inicializacao das variaveis
			$doc = new Documento($dados['id']);
			$doc->dadosTipo['nomeAbrv'] = $dados['tipoDocCad'];
			$doc->loadTipoData();
			// se for cadastro de um documento
			if($dados['action'] == 'cad'){
				//verifica se o documento ja esta cadastrado
				$query = "SELECT * FROM ".$doc->dadosTipo['tabBD']." WHERE ";
				//para cada campo de busca
				foreach (explode(",", $dados['camposBusca']) as $d) {
					//monta o campo com os dados enviados
					$campoDados = montaCampo($d, 'cad',$dados,true);
					//se esse campo nao faz parte de outro, concatena para realizar a consulta
					if(!$campoDados['parte']) $query .= $d."='".$campoDados['valor']."' AND ";
				}
				//adiciona AND para adicao dos dados
				$query = rtrim($query, "AND ");
				//consulta para ver se o documento ja esta cadastrado
				$r = $bd->query($query);
				//feedback
				if(count($r))
					return 'Documento j&aacute; cadastrado.<br /><a href="sgd.php?acao=cad&tipoDoc='.$doc->dadosTipo['nomeAbrv'].'">Cadastrar outro(a) '.$doc->dadosTipo['nome'].'</a> <br /> <a href=""></a>';
				
				//array para armazenar os campos do formulario
				//concatena todos os campos para trata-los
				$camposGerais = explode(",", $dados['camposGerais'].','.$dados['camposBusca']);
				//para cada campo geral
				foreach ($camposGerais as $cb) {
					//le os dados do campo
					$r = getCampo($cb);
					//copia variavel cb pois ela sera modificada
					$cbo = $cb;
					//verifica se o dado em questao era de busca (com '_' no  comeco)
					if(!isset($dados[$cb]) && isset($dados['_'.$cb])){
						//se tiver, modifica a variavel para fazer essa referencia 
						$cb = '_'.$cb;
					}
					//se nao achou o campo no BD (verif de seguranca)
					if(!isset($r[0])){
						//apaga as variaveis
						unset($dados[$cb]);
						unset($camposGerais[$cb]);
						//passa para o proximo campo
						continue;
					}
					//tratamento de campos para salvamento
					if(!isset($dados[$cb]) || $dados[$cb] == ''){
						//se o campo for checkbox e nao tiver variavel com esse nome
						if($r[0]['tipo'] == "checkbox")
							//eh porque ela nao foi checada
							$campos[$cbo] = 0;
						//se for campo autoincrement
						if($r[0]['tipo'] == "autoincrement"){
							//seleciona o attr da tabela
							$r2 = attrFromGenericTable($cb, $doc->dadosTipo['tabBD'], '1', $cb, 'DESC', '1');
							//incrementa o valor do attr e guarda no valor do campo
							$campos[$cbo] = $r2[0][$cb] + 1;
						}
					}
					//tratamento para o campo composto
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
					//partes podem ser ignoradas pois sao tratadas na recursao
					} elseif($r[0]['tipo'] == 'parte'){
						continue;
<<<<<<< HEAD
					//de resto, se o campo foi preenchido, atribui o dado a variavel
=======
					//de resto, se o campo foi preenchido, atribui o dado a variaveld
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
					} else {
						if(isset($dados[$cb]))
							$campos[$cbo] = $dados[$cb];
					}
					//tratamento de acentos e quebra de linha para HTML/HTML entities
					if(isset($dados[$cb])){
						//converte caracteres especiais/acentuados para HTML entities
<<<<<<< HEAD
=======
						$campos[$cbo] =  htmlentities ($campos[$cbo],ENT_QUOTES);
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
						$campos[$cbo] =  str_replace("\n", "<br />", $campos[$cbo]);
					}
				}
			//tratamento de campos para geracao de novo documento
			}elseif($dados['action'] == 'novo'){
				//nao ha cambos de busca na criacao de um novo documento
				//separa os campos do documento
				$camposForm = explode(",", $doc->dadosTipo['campos']);
				//para campo, trata de acordo com o tipo de campo
				foreach ($camposForm as $cb) {
					//se a variavel nao for passada, pode ser check box nao marcado
					if(!isset($dados[$cb]) || $dados[$cb] == ''){
						//le os dados do campo para 
						$r = getCampo($cb);
						//se o campo for checkbox, entao ele nao foi checkado
						if($r[0]['tipo'] == "checkbox") {
							//coloca zero no valor do campo
							$dados[$cb] = 0;
<<<<<<< HEAD
						//se o campo for selecao de ano, considere o ano atual
						} elseif($r[0]['tipo'] == "anoSelect") {
							$dados[$cb] = date("Y");							
=======
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
						//se o campo for autoincrement, deve-se verificar o ultimo valor para incrementa-lo
						} elseif($r[0]['tipo'] == "autoincrement"){
							//se campo reseta a cada ano
							if(strpos($r[0]['extra'], "current_year") !== false){
								//Seleciona o documento mais velho *deste ano* com o maior numero do attr autoincrement
								$r2 = $bd->query("SELECT t.".$cb." FROM ".$doc->dadosTipo['tabBD']." AS t LEFT JOIN doc AS d ON t.id=d.tipoID WHERE d.data>".mktime(0,0,0,1,1,date("Y"))." AND d.labelID=".$doc->dadosTipo['id']." ORDER BY d.id DESC LIMIT 1");
<<<<<<< HEAD
								// print($r2[0][$cb]."SELECT t.".$cb." FROM ".$doc->dadosTipo['tabBD']." AS t LEFT JOIN doc AS d ON t.id=d.tipoID WHERE d.data>".mktime(0,0,0,1,1,date("Y"))." AND d.labelID=".$doc->dadosTipo['id']." ORDER BY t.".$cb." DESC LIMIT 1");
								//se achar alguma entrada, incrementa o valor do ultimo doc
								if (isset($r2[0][$cb]) && $r2[0][$cb]){
									$dados[$cb] = (($r2[0][$cb]) + 1);//. '/' . date("Y");
								//senao, nenhum doc foi criado nesse ano, ainda. Cria o id 1/aaaa
								} else {
									$dados[$cb] = 1;//. '/' . date("Y");
=======
								//TODO print($r2[0][$cb]."SELECT t.".$cb." FROM ".$doc->dadosTipo['tabBD']." AS t LEFT JOIN doc AS d ON t.id=d.tipoID WHERE d.data>".mktime(0,0,0,1,1,date("Y"))." AND d.labelID=".$doc->dadosTipo['id']." ORDER BY t.".$cb." DESC LIMIT 1");
								//se achar alguma entrada, incrementa o valor do ultimo doc
								if (isset($r2[0])){
									$dados[$cb] = (($r2[0][$cb]) + 1) . '/' . date("Y");
								//senao, nenhum doc foi criado nesse ano, ainda. Cria o id 1/aaaa
								} else {
									$dados[$cb] = 1 . '/' . date("Y");
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
								}
							//se o campo nao reseta a cada ano
							} else {
								//consulta o maior numero ja cadastrado
								$r2 = attrFromGenericTable($cb, $doc->dadosTipo['tabBD'], '1', $cb, 'DESC', '1');
								//e incrementa em uma unidade
								if (isset($r2[0])){
									$dados[$cb] = $r2[0][$cb] + 1;
								//se nao houver linhas, apenas cria a primeira
								} else {
									$dados[$cb] = 1;
								}
							}
						//trata campo de usuario atual
						}elseif(strpos($r[0]['extra'], "current_user") !== false){
							//coloca o id do usuario como valor
							$dados[$cb] = $_SESSION['id'];
						//trata campo composto
						}elseif($r[0]['tipo'] == 'composto'){
							//separa as partes do campo composto
							$partes = explode("+",$r[0]['attr']);
							//para cada parte do campo composto
							foreach ($partes as $p) {
								//verifica se o sub-campo tem algum valor
								if (isset($dados[$p])){
									//se tiver, concatena com o campo
									$dados[$cb] .= $dados[$p];
									unset($dados[$p]);
								//senao, eh uma string e nao nome de variavel
								} else {
									//entao, retira as aspas e concatena com o valor do campo
									$dados[$cb] .= str_replace('"','',$p);
								}
							}
						//se for parte de um campo, ignora pois ja foi tratado acima
						}elseif($r[0]['tipo'] == 'parte'){
							continue;
						}	
					}
					//trata o valor do campo convertendo acentos em entidades HTML
<<<<<<< HEAD
					$campos[$cb] = htmlspecialchars_decode( $dados[$cb], ENT_QUOTES);
=======
					$campos[$cb] = htmlspecialchars_decode( htmlentities ($dados[$cb],ENT_QUOTES), ENT_QUOTES);
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
					//se for conteudo, converte as quebras de linha em cod HTML e aspas
					if($cb == "conteudo"){
						$campos[$cb] = str_replace(array("'","\n"),array("\'",""), $campos[$cb]);
					} else {
						$campos[$cb] = str_replace(array("\n","'"),array("<br />","\'"), $campos[$cb]);
					}
				}
			}
			//atribui a array temporaria ao documento
			$doc->campos = $campos;
			
			if($DEBUG) $html .= "Dados lidos com sucesso.<br />";	
			//adicao dos documentos anexos e feedback se debug setado
<<<<<<< HEAD
			if($doc->dadosTipo['docAnexo']){
				$doc->campos['documento'] = '';
			}
			//adicao das obras anexas
			if($doc->dadosTipo['obra']){
				$doc->campos['obra'] = '';
			}
			//adicao das empresas anexas
			if($doc->dadosTipo['empresa']){
				$doc->campos['empresa'] = '';
			}
			// print_r($doc); exit();
=======
			if(strlen($dados['docsAnexos'])){
				$doc->campos['documento'] = rtrim($dados['docsAnexos'],",");
				if($DEBUG) $html .= "Documento(s) adicionado(s) com sucesso.<br />";
			}
			//adicao das obras anexas
			if(strlen($dados['obrasAnexas'])){
				$doc->campos['obra'] = rtrim($dados['obrasAnexas'],",");
				if($DEBUG) $html .= "Obra(s) adicionada(s) com sucesso.<br />";
			}
			//adicao das empresas anexas
			if(strlen($dados['emprAnexas'])){
				$doc->campos['empresa'] = rtrim($dados['emprAnexas'],",");
				if($DEBUG) $html .= "Empresa(s) adicionada(s) com sucesso.<br />";
			}
			
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
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
			
<<<<<<< HEAD
			//logar historico de recebimento se os campos forem preenchidos
			if($dados['action'] == 'cad' && $dados['unOrgReceb'] && $dados['rrNumReceb'] && $dados['rrAnoReceb']){
				if ($doc->doLogHist($_SESSION['id'],'',"Via Rel. Remessa n&deg;".$dados['rrNumReceb']."/".$dados['rrAnoReceb'],$dados['unOrgReceb'],'entrada','','Recebido')) {
=======
			//logar historico de recebimento se os campos forempreenchidos
			if($dados['action'] == 'cad' && $dados['unOrgReceb'] && $dados['rrNumReceb'] && $dados['rrAnoReceb']){
				if ($doc->doLogHist($_SESSION['id'],"Recebido de ".$dados['unOrgReceb']." via Rel. Remessa n&deg;".$dados['rrNumReceb']."/".$dados['rrAnoReceb'],"")) {
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
					if($DEBUG) $html .= "Hist&oacute;rico criado com sucesso.<br />";
				} else {
					if($DEBUG) $html .= '<b>Falha ao criar hist&oacute;rico de Recebimento</b><br />';
				}
			}
						
			//logar historico
<<<<<<< HEAD
			if ($doc->doLogHist($_SESSION['id'],"","",'','criacao','','')){
=======
			if ($doc->doLogHist($_SESSION['id'],"Criou o documento.","")){
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
				if($DEBUG) $html .= "Hist&oacute;rico criado com sucesso.<br />";
			}else{
				if($DEBUG) $html .= '<b>Falha ao criar hist&oacute;rico</b><br />';
			}
			
<<<<<<< HEAD
			//faz upload de arquivos, salva no documento e loga no historico
=======
			//faz upload de arquivos, salva no documento e loga no hostorico
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
			if($DEBUG) $html .= "<br /><b>Arquivos</b><br />";			
			$relArq = $doc->doUploadFiles();
			if($DEBUG) $html .= montaRelArq($relArq);
			$anexoSalvo = $doc->salvaAnexos();
			
			//se estiver no modo debug, mostra o estado de cada arquivo anexado
			if($DEBUG) 
				if ($anexoSalvo === true) {
					$html .= "<br />Arquivos anexados com sucesso.<br />";
				}elseif ($anexoSalvo === false){
					$html .= "<br /><b>Erro ao anexar arquivos.</B><br />";
				}elseif ($anexoSalvo === 0){
					$html .= "N&atilde;o h&aacute; arquivo anexado.<br /><br />";
				}
			
			//marca que os documentos filho foram anexados.
			if(!$doc->doFlagAnexado())
				if($DEBUG) $html .= "<b>Erro ao salvar dados nos documentos filhos</b><br />";
			
			if(!isset($dados['funcID'])) $dados['funcID'] = false;

			//CLAUSULA ESPECIAL PARA RR - caso seja RR, logar que os documentos foram enviados por ela
			if($doc->dadosTipo['nomeAbrv'] == 'rr'){
				//separar os documentos enviados pela RR gerada
				foreach (explode(",", $dados['docsDesp']) as $ddid) {
					//verif de seguranca para nao incluir documentos invalidos
					if($ddid > 0){
						//carrega os dados do documento 
						$docDesp = new Documento($ddid);
<<<<<<< HEAD
						showDespStatus($docDesp, array('para' => html_entity_decode($dados['para']) ,"outro" => $dados['outro'], 'funcID' => $dados['funcID'], 'despExt' => $dados['despExt'], 'despacho' => $dados['despacho']),'hideFB');
						$docDesp->doLogHist($_SESSION['id'], '', "Via Rel. Remessa CPO n&deg:".$doc->campos['numeroRR'],$doc->campos['unOrgDest'],'saida','','Despacho');
=======
						$docDesp->doLogHist($_SESSION['id'], "Despachou o documento para ".$doc->campos['unOrgDest']." via Rel. Remessa CPO n&deg:".$doc->campos['numeroRR'],'');
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
					}
				}
			}
			//grava despacho
<<<<<<< HEAD
			$despStatus = showDespStatus($doc, array('para' => html_entity_decode($dados['para']) ,"outro" => $dados['outro'], 'funcID' => $dados['funcID'], 'despExt' => $dados['despExt'], 'despacho' => $dados['despacho']),'hideFB');
=======
			$despStatus = showDespStatus($doc, array('para' => $dados['para'] ,"outro" => $dados['outro'], 'funcID' => $dados['funcID'], 'despExt' => $dados['despExt'], 'despacho' => htmlentities($dados['despacho'])),'hideFB');
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
			
			//gerar PDF
			if($dados['action'] == 'novo'){
				$pdfFile = geraPDF($doc->id);
				if($pdfFile)
					if($DEBUG) $html .= '<b>Arquivo PDF gerado com sucesso. Clique para <a href="files/'.$pdfFile.'">visualizar/baixar o documento PDF </a>.</b>';
				else
					if($DEBUG) $html .= '<b>Erro ao gerar arquivo PDF.</b>';
			}
			
			//Reload dos campos para impressao
			$doc->loadDados($bd);
			$doc->loadCampos($bd);
			//impressao dos dados
<<<<<<< HEAD
			$html .= 'Documento gerado com o N&uacute;mero CPO: <b><font color="red"><a href="javascript:void(0);" onclick="window.open('."'sgd.php?acao=ver&amp;docID=".$doc->id."','detalhe".$doc->id."','width=900,height=650,scrollbars=yes,resizable=yes'".')">'.$doc->id.'</a></font></b>
			<br />'.showDetalhes($doc).
			'<br /><b>Outras A&ccedil;&otilde;es:</b>';
			if(isset($_POST['targetInput'])) {
				$html .= '<br /><b><a href="javascript:void(0);" onclick="javascript:window.opener.newDocLink(\''.$doc->id.'\',\''.$doc->dadosTipo['nome'].' '.$doc->numeroComp.'\',\''.$_POST['targetInput'].'\',\'<br>\');self.close();">Adicionar documento ao formul&aacute;rio e fechar janela</a></b>';
			}
			$html .= '<br /><a href="sgd.php?acao='.$dados['action'].'&tipoDoc='.$doc->dadosTipo['nomeAbrv'].'"> Cadastrar novo(a) '.$doc->dadosTipo['nome'].'</a>'
=======
			$html .= 'Documento gerado com o N&uacute;mero CPO: <b><font color="red"><a href="#" onclick="window.open('."'sgd.php?acao=ver&amp;docID=".$doc->id."','detalhe".$doc->id."','width=900,height=650,scrollbars=yes,resizable=yes'".')">'.$doc->id.'</a></font></b>
			<br />'.showDetalhes($doc).
			'<br /><b>Outras A&ccedil;&otilde;es:</b>
			<br /><a href="sgd.php?acao='.$dados['action'].'&tipoDoc='.$doc->dadosTipo['nomeAbrv'].'"> Cadastrar novo(a) '.$doc->dadosTipo['nome'].'</a>'
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
			.$despStatus;
			
			//Imprimir RR?
			//@todo
			
			//atalho para a pagina inicial
			$html .= '<br /> <a href="index.php">Voltar para p&aacute;gina inicial.</a>';
				
			//LOG dos usuarios
			doLog($_SESSION['username'],'Criou o documento '.$doc->id);
			
		}else{//se doc ja existe, faz o despacho
			$doc = new Documento($dados['id']);
			$doc->dadosTipo['nomeAbrv'] = $dados['tipoDocCad'];
			
			if(!isset($dados['funcID'])) $dados['funcID'] = false;
			
			//logar historico de recebimento
			if($dados['action'] == 'cad' && $dados['unOrgReceb'] && $dados['rrNumReceb'] && $dados['rrAnoReceb']){
<<<<<<< HEAD
				if ($doc->doLogHist($_SESSION['id'],'',"Via Rel. Remessa n&deg;".$dados['rrNumReceb']."/".$dados['rrAnoReceb'],$dados['unOrgReceb'],'entrada','','Recebido')) {
=======
				if ($doc->doLogHist($_SESSION['id'],"Recebido de ".$dados['unOrgReceb']." via Rel. Remessa n&deg;".$dados['rrNumReceb']."/".$dados['rrAnoReceb'],'')) {
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
					if($DEBUG) $html .= "Hist&oacute;rico criado com sucesso.<br />";
				} else {
					if($DEBUG) $html .= '<b>Falha ao criar hist&oacute;rico de Recebimento</b><br />';
				}
			}
			//gravar despacho
			$html .= showDespStatus($doc,array('para' => $dados['para'] ,"outro" => $dados['outro'], 'funcID' => $dados['funcID'], 'despExt' => $dados['despExt'], 'despacho' => htmlentities($dados['despacho'])));
			$html .= '<br /> <a href="index.php">Voltar para p&aacute;gina inicial.</a>';
		}
		return $html;
	}
	
	/**
	 * Monta o relatorio de upload dos arquivos (DEBUG)
	 * @param Array $files
	 */
	function montaRelArq($files){
		//inicializacao de variaveis
		$html = '';
		//para cada arquivo bem sucedido
		foreach ($files['success'] as $file) {
			//se o arquivo foi enviado corretamente. Gera a mensagem
			$html .= '<i>'.$file.'</i>: Arquivo foi anexado com sucesso.<br />';
			//loga a acao
			doLog($_SESSION['username'], "Anexou o arquivo $file.");
		}
		//para cada arquivo mau sucedido
		foreach ($files['failure'] as $file) {
			//se o arquivo obteve falha
			$html .= '<i>'.$file['name'].'</i>: Erro ao anexar arquivo (Erro '.$files['errorID'].').<br />';
			//loga a acao
			doLog($_SESSION['username'], "Obteve erro ao adicionar $file.");
		}
		//retorna o cod html da mensagem
		return $html;
	}
	
	/**
	 * Retorna string de feedback correspondente para dado return da funcao de salvamento. (DEBUG)
	 * @param Documento $doc
	 * @param string $dados
	 * @param string $mode
	 **/
	function showDespStatus($doc,$dados,$mode = 'showFB',$entrada = false) {
		//inicializacao da variavel
		$html = "";
		//print $entrada.'/'.$dados['rrNumReceb'].'/'.$dados['unOrgReceb'].'/'.$dados['rrAnoReceb'];exit();
<<<<<<< HEAD
		if($entrada && isset($dados['unOrgReceb']) && isset($dados['rrNumReceb']) && isset($dados['rrAnoReceb']) && $dados['unOrgReceb'] && $dados['rrNumReceb'] && $dados['rrAnoReceb']){
			if ($doc->doLogHist($_SESSION['id'],'', " via Rel. Remessa n&deg;".$dados['rrNumReceb']."/".$dados['rrAnoReceb'],$dados['unOrgReceb'],'entrada','','Recebido')) {
=======
		if($entrada && isset($dados['unOrgReceb']) && isset($dados['rrNumReceb']) && isset($dados['rrAnoReceb']) && $dados['unOrgReceb'] && $dados['rrNumReceb'] && $dados['rrAnoReceb']){	if ($doc->doLogHist($_SESSION['id'],"Recebido de ".$dados['unOrgReceb']." via Rel. Remessa n&deg;".$dados['rrNumReceb']."/".$dados['rrAnoReceb'],'')) {
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
				if($mode == 'showFB') $html .= "Hist&oacute;rico criado com sucesso.<br />";
			} else {
				if($mode == 'showFB') $html .= '<b>Falha ao criar hist&oacute;rico de Recebimento</b><br />';
			}
		}
		
		//realiza o despacho
		$desp = $doc->doDespacha($_SESSION['id'],$dados);
		//se o modo de operacao eh diferente de hideFeedBack
		if($mode != "hideFB"){
			//se houve falha ao realizar o despacho
			if($desp === false){
				//gera feedback
				$html = "<b>Falha ao gravar despacho.</b><br />";
			//se nao foi digitado nenhum despacho.
			}elseif($desp === 0){
				//avisa que nao houve dispacho digitado
				$html = "<b>N&atilde;o h&aacute; despacho. Documento est&aacute; pendente para o usu&aacute;rio atual.</b><br />";
			//senao - sucesso ao salvar despacho
			}else{
				//gera msg de sucesso
<<<<<<< HEAD
				$html = 'Despacho para '.$desp.' gravado com sucesso.<br />';
=======
				$html = 'Despacho para '.$desp.' gravado com sucesso.<br />
				<a href="javascript:void(0)" onclick="window.open(\'sgd.php?acao=geraCI&id='.$doc->id.'\')">Gerar CI</a>';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
			}
		}
		//se o despacho foi para fora, e nao foi uma RR
		if($dados['para'] == 'ext' && $dados['despExt'] && $doc->dadosTipo['nomeAbrv'] != 'rr')
			//gerar atalho para RR 
<<<<<<< HEAD
			$html .= '<br /><a href="#" onclick="window.open('."'sgd.php?acao=novoDocVar&amp;action=novo&amp;tipoDoc=rr&amp;anoE=2011&amp;docsDesp=".$doc->id."&amp;unOrgDest=".urlencode($dados['despExt'])."&amp;ppara=ext&amp;despExt=".urlencode($dados['despExt'])."&amp;despacho=".urlencode(html_entity_decode($dados['despacho']))."','novaRR','width=900,height=650,scrollbars=yes,resizable=yes'".')">Gerar Rela&ccedil;&atilde;o de Remessa</a>.<br />';
=======
			$html .= '<br /><a href="#" onclick="window.open('."'sgd.php?acao=novoDocVar&action=novo&tipoDoc=rr&docsDesp=".$doc->id."&unOrgDest=".$dados['despExt']."&para=ext&despExt=".$dados['despExt']."&despacho=".urlencode(html_entity_decode($dados['despacho']))."','novaRR','width=900,height=650,scrollbars=yes,resizable=yes'".')">Gerar Rela&ccedil;&atilde;o de Remessa</a>.<br />';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		//retorna o cod html
		return $html;
	}
	
	/**
	 * trata as variaveis passadas via $_GET para via $_POST (criacao de documento via URL)
	 * @param array $GET
	 */
	function trataGetVars($GET){
		//inicia o novo documento a ser criado
		$doc = new Documento(0);
		$doc->dadosTipo['nomeAbrv'] = $GET['tipoDoc'];
		$doc->loadTipoData();
		//para cada campo do documento
		foreach (explode(",",$doc->dadosTipo['campos']) as $campo) {
			//monta o campo a ser lido
			$nome = montaCampo($campo);
			//coloca o nome em um array
			$camposNomes[] = $nome['nome'];
		}
		//para cada campo do documento
		foreach ($camposNomes as $campo) {
			//verifica se ele foi passado para a pagina
			if (isset($GET[$campo])) {
				//se sim, coloca seu valor na variavel
<<<<<<< HEAD
				$dados[$campo] = urldecode($GET[$campo]);
=======
				$dados[$campo] = $GET[$campo];
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
			} else {
				//senao, deixa o valo da variavel vazio
				$dados[$campo] = '';
			}
		}
		//se a acao for de cadastro de novo documento, seta as variaveis pertinentes
		if($GET['action'] == 'cad'){
			//sinaliza que a acao deve ser de cadastro
			$dados['action'] = 'cad';
			//seta os campos gerais
			$dados['camposGerais'] = $doc->dadosTipo['campos'];
			//seta os campos de busca
			$dados['camposBusca'] = '';
<<<<<<< HEAD
		} elseif ($GET['action'] == 'novo'){
=======
		}elseif ($GET['action'] == 'novo'){
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
			//se for novo documento, apenas eh necessario setar a acao
			$dados['action'] = 'novo';
		}
		//indica qual o tipo de documento sera salvo
		$dados['tipoDocCad'] = $GET['tipoDoc'];
		//id=0 pois eh um novo documento
		$dados['id'] = 0;
		//cria os dados para despacho se houver. senao deixa os campos de despacho em branco
<<<<<<< HEAD
		if(isset($GET['para']))    $dados['para']     = urldecode($GET['ppara']);    else   $dados['para'] = '';
		if(isset($GET['despExt'])) $dados['despExt']  = urldecode($GET['despExt']);  else   $dados['despExt'] = '';
		if(isset($GET['outro']))   $dados['outro']    = urldecode($GET['outro']);    else   $dados['outro'] = '';
		if(isset($GET['despacho']))$dados['despacho'] = htmlentities(htmlentities($GET['despacho'])); else   $dados['despacho'] = '';
=======
		if(isset($GET['para']))    $dados['para']     = $GET['para'];     else   $dados['para'] = '';
		if(isset($GET['despExt'])) $dados['despExt']  = $GET['despExt'];  else   $dados['despExt'] = '';
		if(isset($GET['outro']))   $dados['outro']    = $GET['outro'];    else   $dados['outro'] = '';
		if(isset($GET['despacho']))$dados['despacho'] = $GET['despacho']; else   $dados['despacho'] = '';
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
		//cria a 'lista' de documentos anexos, se houver
		if (isset($GET['docsAnexos'])) {
			$dados['docsAnexos'] = $GET['docsAnexos'];
		} else {
			$dados['docsAnexos'] = '';
		}
		//cria a 'lista' de obras anexas se passada via URL
		if (isset($GET['obrasAnexas'])) {
			$dados['obrasAnexas'] = $GET['obrasAnexas'];
		} else {
			$dados['obrasAnexas'] = '';
		}
		//cria a 'lista' de empresas anexas, se passada via URL
		if (isset($GET['emprAnexas'])) {
			$dados['emprAnexas'] = $GET['emprAnexas'];
		} else {
			$dados['emprAnexas'] = '';
		}
		//retorna array com todas as variaveis necessarias para a acriacao do documento
		return $dados;
	}
<<<<<<< HEAD
	
	/**
	 * Salva nova atribuição de valor a um determinado documento
	 * @param int $docID
	 * @param string $campoName
	 * @param string $oldCampoVal
	 * @param string $newCampoVal
	 * 
	 */
	function editDoc($docID, $campoName, $newCampoVal) {
		$doc = new Documento($docID);
		$doc->loadCampos();
		$res = $doc->updateCampo($campoName, $newCampoVal);
		
		if ($res){
			$campo = montaCampo($campoName,'mostra',$doc->campos,false);
			doLog($_SESSION['username'],'Alterou informa&ccedil;&otilde;es do documento'.$doc->id.'. Campo '.$campo['label'].' alterado de "'.$campo['valor'].'" para "'.$newCampoVal.'"');
			$doc->campos[$campoName] = $newCampoVal;
			if(strpos($doc->dadosTipo['numeroComp'], $campoName) !== false){
				$newNumeroComp = $doc->geraNumComp();
				$doc->update('numeroComp',$newNumeroComp);
			}
			$ret[] = array('success' => 'true');
		} else {
			$ret[] = array('success' => 'false');
		}
		return $ret;
	}
	
	/**
	 * Adiciona  HTML dos campos para anexar documento
	 * @param documento $doc
	 *
	 **/
	function addAnexarDoc($doc) {
		$alert = '';
		if($doc->anexado) {
			$alert = '<div id="alert" style="display: none; border: 1px solid red; text-align: center; margin: 5px; padding: 5px;">
			<span style="color: red">Aviso:</span> Este documento j&aacute est&aacute anexado. Caso opte por anex&aacute;-lo a outro, a liga&ccedil;&atilde;o anterior ser&aacute; perdida.
			</div>';
		}
		$html = '<input id="addEste" type="radio" name="tipo" value="1" onclick="showAlert();" /> Anexar este documento a outro.<br />';
		if($doc->dadosTipo['docAnexo']) {
			$html .= '<input id="addOutr" type="radio" name="tipo" value="1" onclick="hideAlert();" /> Anexar outros documentos a este.';
		}
		$html .= $alert;
		return $html;
	}
	
	function anexarDoc($filhoID,$paiID) {
		$doc = new Documento($paiID);
		$doc->loadCampos();
		$res = $doc->anexaDoc($filhoID);
		
		if($res) {
			$docAnexado = new Documento($filhoID);
			$docAnexado->loadDados();
			$doc->doLogHist($_SESSION['id'], 'Anexou o documento '.$docAnexado->id.' ('.$docAnexado->dadosTipo['nome'].' '.$docAnexado->numeroComp.') a este documento.', '', '', 'anexOutro', '', '');
			$docAnexado->doLogHist($_SESSION['id'], 'Anexou este documento ao '.$doc->id.' :'.$docAnexado->dadosTipo['nome'].' '.$doc->numeroComp,	'', '', 'anexoEste', '', '');
			return array(array('success' => 'true'));
		} else {
			return array(array('success' => 'false'));
		}
	}
	
	function showAtribuirAObra($docID){
		includeModule('sgo');
		global $bd;
		$template = showAtribuirObraTemplate();
		$doc = new Documento($docID);
		$doc->loadDados();
		
		if($doc->obraID){
			$obra = new Obra();
			$obra->load($doc->obraID, true);
			$atual = str_replace(array('{$obra_nome}', '{$obra_id}'), array($obra->get('nome'), $obra->get('id')), $template['com_obra']);
		} else {
			$atual = $template['sem_obra'];
		}
		
		$vars = array('{$obraAtual}');
		$vals = array($atual);
		
		$html = str_replace($vars, $vals, $template['template']);
		
		return $html;
	}
	
	function atribObra($docID, $obraID) {
		$doc = new Documento($docID);
		$doc->loadDados();
		$res = $doc->update('obraID', $obraID);
		
		if ($res){
			return array(array('success' => true));
		} else {
			return array(array('success' => false));
		}
	}
=======
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
?>