<?php
	/**
	 * @version 0.1 16/2/2011 
	 * @package geral
	 * @author Mario Akita
	 * @desc lida com a exibicao dos modulos do portal HTML
	 */
	
	/**
	 * @desc monta o menu lateral do usuario
	 * @param string $template caminho do arquivo de template
	 * @param int $perm array de permissoes
	 * @param int $area 30=index, 2=obras, 3=documentos, 5=OS
	 * @param connection $bd conexao com o bd
	 */
	function showMenu($template,$perm,$area,$bd) {
		//carrega o arquivo com o codigo HTML basico da pagina
		$html = file_get_contents($template);
		$buffer = '';
		
		//monta o 1o menu (de obras) se houver permissao
		if ($perm[12]) $data["itens_obra"] = '<a href="sgo.php?acao=buscar">Gerenciar Obra</a>';
		else{
			$data["itens_obra"] = "";
			$area /= 2;//areas que serao mostradas
		}
		
		//seleciona todos os tipos de documento
		$docs = $bd->query("SELECT nome,nomeAbrv,cadAcaoID FROM label_doc WHERE cadAcaoID > 0");
		//seleciona cada tipo de doc, verifica se tem premissao pra cadastrar e entao cria o link
		foreach ($docs as $doc){
			if ($perm[$doc['cadAcaoID']]) $buffer .= '<a href="sgd.php?acao=cad&amp;tipoDoc='.$doc['nomeAbrv'].'"><img src="img/p.png" border="0" alt="" />'.$doc['nome'].'</a><br />';
		}
		//monta os links de cadastro de documentos em que ha permissao
		$data["itens_doc"] = '';
		if($buffer != ""){
			$data["itens_doc"] .= '<span id="cadDocLink" class="pLink">Cadastrar</span>
			<div class="subMenu2" id="cadDoc">'
			.$buffer.
			'</div>';
		}
		//seleciona os documentos que podem ser criados
		$docs = $bd->query("SELECT nome,nomeAbrv,novoAcaoID FROM label_doc WHERE novoAcaoID > 0");
		$buffer = "";
		//monta os links de criacao de documentos
		foreach ($docs as $doc){
			if ($perm[$doc['novoAcaoID']]) $buffer .= '<a href="sgd.php?acao=novo&amp;tipoDoc='.$doc['nomeAbrv'].'"><img src="img/p.png" border="0" alt="" />'.$doc['nome'].'</a><br />';
		}
		
		//se houver pelo menos um documento para novo, cria a secao de novo documento
		if($buffer != "") 
		$data["itens_doc"] .=
		'<br /><span id="novoDocLink" class="pLink">Criar Novo</span>
		<div class="subMenu2" id="novoDoc">'
			.$buffer.
		'</div>';
		//se tiver permissao, cria o link para buscar documento
		if($perm[1]) $data["itens_doc"] .= '<br /><a href="sgd.php?acao=buscar">Buscar Documento</a><br />';
		
		if($data["itens_doc"] == '') $area /= 3;
		
		$areaatual = showCodTela();

		$data['ajuda'] = '<a href="#ajuda" onclick="mostrarAjuda(\''.substr($areaatual,6,6).'\')"><span id="labAj" class="menuHeader">Precisa de Ajuda?</span></a>';
		
		
		$data["script"] = '<script type="text/javascript">showMenu('.$area.');</script>';
		//coloca os itens na posicao marcada no template
		$html = str_replace('{$ajuda}', $data['ajuda'], $html);
		$html = str_replace('{$itens_obra}', $data['itens_obra'], $html);
		$html = str_replace('{$itens_doc}', $data['itens_doc'], $html);
		$html = str_replace('{$script}', $data['script'], $html);
		
		return $html;
	}
	
	/**
	 * Monta o caminho de 'diretorios'
	 * @param array $dir
	 * @param string $tipo
	 */
	function showNavBar($dir,$tipo='normal'){
		//inicio
		if($tipo == 'normal')
			$bar = '<a href="index.php">In&iacute;cio</a>';//se for janela normal, cria link
		elseif ($tipo == 'mini') {
			$bar = 'In&iacute;cio';//se for mini, apenas mostra o texto
		}
		//1o nivel
		switch ($_SERVER["PHP_SELF"]){//le qual o nome do arquivo sendo lido e decide qual secao o usuario esta
			case "/sgd.php" : $bar .= " :: Ger&ecirc;ncia de Documentos";
							  break;
			case "/sgo.php" : $bar .= " :: Ger&ecirc;ncia de Obras";
							  break;
			case "/os.php" : $bar .= " :: Ordem de Serviço Informática";
							  break;
		}
		//2o nivel em diante le o array de dados
		//para cada posicao do array (name, url), cria um link para aquela secao
		foreach ($dir as $d){
			if ($d['url'] != ''){
				$bar .= ' :: <a href="'.$d['url'].'">'.$d['name'].'</a>';
			}else{
				$bar .= ' :: '.$d['name'];//se nao houver link, mostra apenas o texto
			}
		}
		return $bar;
	}
	
	/**
	 * Monta o codigo da tela
	 */
	function showCodTela($arqNome = '') {
		$numTela = ' Tela ';
		//le o nome do arquivo atual para decicir qual secao esta sendo visitada
		
		if($arqNome == '') $arqNome = $_SERVER['REQUEST_URI'];
		
		if(strpos($arqNome,"/login.php") !== false)
			$cod[0] = 'LOG';
		elseif(strpos($arqNome, "/index.php") !== false)
			$cod[0] = 'IND';
		elseif(strpos($arqNome, "/sgd.php") !== false)
			$cod[0] = 'SGD';
		elseif(strpos($arqNome, "/empresa.php") !== false)
			$cod[0] = 'EMP';
		elseif(strpos($arqNome, "/adm.php") !== false)
			$cod[0] = 'ADM';
		elseif(strpos($arqNome, "/sgo.php") !== false)
			$cod[0] = 'SGO';
		elseif(strpos($arqNome, "/os.php") !== false)
			$cod[0] = 'OSI';
		elseif(strpos($arqNome, "/report_bug.php") !== false)
			$cod[0] = 'BUG';
		elseif(strpos($arqNome, "/ajuda.php") !== false)
			$cod[0] = 'AJU';
		else
			$cod[0] = '000';
		
		
		//le as variaveis passada por URL para definir a subsecao a ser exibida
	
		$vars = explode("?",$arqNome);//separa URL dos parametros
		$vars = explode("&",$vars[count($vars)-1]);//separa cada parametro em uma posicao do array
		foreach ($vars as $v) {
			if(strpos($v,"=") === false) continue; //1a posicao nao eh variavel se nao houver variavel
			$v = explode("=",$v);//separa valor da chave
			$varsGET[$v[0]] = $v[1];//coloca valor na chave do array
		}
		
		if ($cod[0] == 'SGD') {//sgd.php
			if (isset($varsGET['acao']) && isset($varsGET['docID']) && $varsGET['acao'] == 'ver'){
				$cod[1] = 'VD';
				while(strlen($varsGET['docID']) < 5) $varsGET['docID'] = '0'.$varsGET['docID'];
				$cod[2] = strtoupper($varsGET['docID']);
			} elseif (isset($varsGET['acao']) && isset($varsGET['tipoDoc']) && $varsGET['acao'] == 'cad'){
				$cod[1] = 'CD';
				while(strlen($varsGET['tipoDoc']) < 5) $varsGET['tipoDoc'] = '0'.$varsGET['tipoDoc'];
				$cod[2] = strtoupper($varsGET['tipoDoc']);
			} elseif (isset($varsGET['acao']) && isset($varsGET['tipoDoc']) && $varsGET['acao'] == 'novo'){
				$cod[1] = 'NV';
				while(strlen($varsGET['tipoDoc']) < 5) $varsGET['tipoDoc'] = '0'.$varsGET['tipoDoc'];
				$cod[2] = strtoupper($varsGET['tipoDoc']);
			} elseif (isset($varsGET['acao']) && $varsGET['acao'] == 'salvar'){
				$cod[1] = 'SV';
				$cod[2] = '00000';			
			} elseif (isset($varsGET['acao']) && $varsGET['acao'] == 'busca_mini'){
				$cod[1] = 'BM';
				$cod[2] = '00000';
			} elseif (isset($varsGET['acao']) && $varsGET['acao'] == 'buscar'){
				$cod[1] = 'BU';
				$cod[2] = '00000';
			} elseif (isset($varsGET['acao']) && $varsGET['acao'] == 'anexar'){
				$cod[1] = 'AA';
				$cod[2] = '00000';
			} elseif (isset($varsGET['acao']) && $varsGET['acao'] == 'despachar'){
				$cod[1] = 'DP';
				$cod[2] = '00000';
			} elseif (isset($varsGET['acao']) && $varsGET['acao'] == 'novoDocVar'){
				$cod[1] = 'ND';
				$cod[2] = '00000';
			}
		} elseif ($cod[0] == 'EMP') {//empresa.php
			if (isset($varsGET['acao']) && $varsGET['acao'] == 'buscar'){
				$cod[1] = 'BU';
				$cod[2] = '00000';
			} elseif (isset($varsGET['acao']) && $varsGET['acao'] == 'cad'){
				$cod[1] = 'CD';
				$cod[2] = '00000';
			}
		} elseif ($cod[0] == 'ADM') {//adm.php
			$cod[1] = '??.?';
		} elseif ($cod[0] == 'SGO') {//sgo.php
			$cod[1] = '??.?';
		} elseif ($cod[0] == 'OSI') {//os.php
			$cod[1] = '??.?';
		} elseif ($cod[0] == 'BUG') {//os.php
			if (isset($varsGET['acao']) && $varsGET['acao'] == 'enviar'){
				$cod[1] = 'EN';
				$cod[2] = '00000';
			} elseif (isset($varsGET['acao']) && $varsGET['acao'] == 'ver'){
				$cod[1] = 'VR';
				$cod[2] = '00000';
			} else {
				$cod[1] = 'CD';
				$cod[2] = '00000';
			}
		} else {
			$cod[1] = '00.00000';
			return $numTela.implode('.', $cod) . ' <a href="report_bug.php">Relatar erro</a>';
		}
		
		return $numTela.implode('.', $cod) . ' <a href="report_bug.php">Relatar erro</a>';
	}
	
	/**
	 * verifica se o usuario esta logado para mostrar uma determinada pagina
	 * @param int $id
	 */
	function checkLogin($id){
		//le qual pagina esta sendo acessada
		$varsGET = $_SERVER['PHP_SELF']."?";
		//le as variais passadas pela URL (_GET) e monta a URL completa
		foreach ($_GET as $key => $value) {
			$varsGET .= $key."=".$value."&";
		}
		//se nao estiver logado, volta para tela de login com as variaveis corretas
		if(!isset($_SESSION['logado']) || (isset($_SESSION['logado']) && !$_SESSION['logado']))
			showError($id,"login.php?redir=".urlencode($varsGET));
	}
	
	/**
	 * Verifica se o usuario tem permissao para realizar tal acao. Retorna para home com alert se nao tiver
	 * @param int $permID ID da acao
	 * @return bool $auth (true se ha permissao, false caso contrario)
	 */
	function checkPermission($permID) {
		//le no vetor de permissoes se o usuario logado tem permissao para realizar a acao
		if(isset($_SESSION['perm'][$permID]) && $_SESSION['perm'][$permID] > 0)
			return true;
		else
			showError(12);
	}
	
	/**
	 * Funcao que recebe o nome do campo e um array de valores e 
	 * retorna o nome do campo, label, codigo HTML para cadastro ou busca e o valor
	 * @param string $c
	 * @param mysql link $bd
	 * @param string $tipo
	 * @param array $valor
	 * @return array $campo com [cod], [nome], [label], [valor]
	 */
	function montaCampo($c,$tipo = 'cad',$valor = null,$busca = false){
		//le os dados do campo
		$cp = getCampo($c);
		
		if(isset($cp[0]))
			$cp = $cp[0];//seleciona o primeiro campo retornado
		else
			return null;//retorna null se nao achar o campo
		
		//os campos de busca (no cadastro, os campos chave sao reproduzidos no form de cadastro com
		//um '_' na frente)
		if($busca){
			$c = '_'.$c;
		}
		
		$campo['nome']  = $cp['nome']; //nome eh o proprio nome passado por parametro (e o mesmo no BD)
		$campo['label'] = $cp['label']; //o label eh aquele lido do BD sem tratamento algum
		$campo['cod']   = '';
		$campo['valor'] = '';
		$campo['parte'] = false;
		
		if ($cp['tipo'] == 'input') {
			//se for input com autocompletar de unidades, gera o HTML + javascript correspondente
			if (strpos($cp['extra'],"unOrg_autocompletar") !== false){
				$campo['cod'] = '<input autocomplete="off" name="'.$cp['nome'].'" id="'.$cp['nome'].'" '.$cp['attr'].' />
				<script type="text/javascript">$(document).ready(function(){$("#'.$cp['nome'].'").autocomplete("unSearch.php",{minChars:2,matchSubset:1,matchContains:true,maxCacheLength:20,extraParams:{\'show\':\'un\'},selectFirst:true,onItemSelect: function(){$("#'.$cp['nome'].'").focus();}});});</script>';
			//se for campo input com ano autal, cria input com pre-valor caso cadastro e em branco caso busca
			} elseif (strpos($cp['extra'],"current_year") !== false){
				if($tipo == 'cad') $campo['cod'] = '<input name="'.$cp['nome'].'" id="'.$cp['nome'].'" '.$cp['attr'].' value="'.date("Y").'" />';
				if($tipo == 'bus') $campo['cod'] = '<input name="'.$cp['nome'].'" id="'.$cp['nome'].'" '.$cp['attr'].' />';
			} else {
				//se for input simples, monta a tag
				$campo['cod'] = '<input name="'.$cp['nome'].'" id="'.$cp['nome'].'" '.$cp['attr'].' />';
			}
			// se campo text, valor eh o que foi digitado sem tratamento (valor do indice dado)
			if(isset($valor[$c])) $campo['valor'] = $valor[$c];
			
		} elseif ($cp['tipo'] == 'select') {
			//monta a estrutura basica de campo select (com 1a opcao --selecione--)
			$campo['cod'] = '<select name="'.$cp['nome'].'" id="'.$cp['nome'].'"><option selected value="nenhum"> -- Selecione -- </option>';
			//separa todas as opcoes da selecao
			$attr = explode(",",$cp['attr']);
			//para cada selecao, monta o HTML correspondente
			foreach ($attr as $c) {
				$c = explode("=", $c);
				//se for separador, cria a opcao desabilitada
				if($c[0] == '_separador_')
					$campo['cod'] .= '<option value="" disabled="" style="background-color: #404040; color:white">-&gt; '.$c[1].'</option>';
				else
					//senao cria campo normal
					$campo['cod'] .= '<option value="'.$c[0].'">'.$c[1].'</option>';
				
				//valor eh o 'value' da opcao selecionada
				if (isset($valor[$campo['nome']]) && $c[0] == $valor[$campo['nome']])
					$campo['valor'] = $c[1];
			}
			$campo['cod'] .= '</select>';
			
		} elseif ($cp['tipo'] == 'yesno') {
			//se for tipo yes/no monta os 2 campos			
			$campo['cod'] = '<input type="radio" name="'.$cp['nome'].'" id="'.$cp['nome'].'" value="1" /> Sim&nbsp;&nbsp;<input type="radio" name="'.$cp['nome'].'" id="'.$cp['nome'].'" value="0" /> N&atilde;o';
			//se o valor for 1, retorna sim, se 0, retorna nao, senao, nao informado
			if(isset($valor[$c])){
				if($valor[$c] == 1)	$campo['valor'] = 'sim';
				elseif($valor[$c] == 0) $campo['valor'] = 'n&atilde;o';
				else $campo['valor'] = 'n&atilde;o informado';
			}
			
		} elseif ($cp['tipo'] == 'checkbox') {
			//se for checkbox, monta o campo
			$campo['cod'] = '<input type="checkbox" name="'.$cp['nome'].'" id="'.$cp['nome'].'" value="1" />';
			if(isset($valor[$c])){
				//se valor for 1, retorna sim, se for 0, retorna nao, senao, retorna nao informado
				if($valor[$c] == 1)	$campo['valor'] = 'sim';
				elseif($valor[$c] == 0) $campo['valor'] = 'n&atilde;o';
				else $campo['valor'] = 'n&atilde;o informado';
			}
			
		} elseif ($cp['tipo'] == 'autoincrement') {
			//monta campo de autoincrement
			if($tipo == "cad") $campo['cod'] = '<input type="hidden" name="'.$cp['nome'].'" id="'.$cp['nome'].'" value="" />(Ser&aacute; gerado automaticamente.)';
			if($tipo == "bus") $campo['cod'] = '<input type="text" size="10" name="'.$cp['nome'].'" id="'.$cp['nome'].'" value="" />';
			if(isset($valor[$c])) $campo['valor'] = $valor[$c];
			
		} elseif ($cp['tipo'] == 'textarea') {
			//monta o campo de texto
			if($tipo == "cad") $campo['cod'] = '<textarea name="'.$cp['nome'].'" id="'.$cp['nome'].'" '.$cp['attr'].'"></textarea>';
			if($tipo == "bus") $campo['cod'] = '<input name="'.$cp['nome'].'" id="'.$cp['nome'].'" size="35" />';
			if(isset($valor[$c])) $campo['valor'] = $valor[$c];
			
		} elseif ($cp['tipo'] == 'userID') {
			//monta campo de usuario
			if (strpos($cp['extra'], 'current_user') !== false){
				//se for campo de usuario atual, apenas mostra o nome do usuario e cria campo oculto com o ID do usuario atual no cad e mostra input para busca por nome
				if($tipo == "cad") $campo['cod'] = '<input type="hidden" name="'.$cp['nome'].'" id="'.$cp['nome'].'" value="'.$_SESSION['id'].'" />'.$_SESSION['nomeCompl'];
				if($tipo == "bus") $campo['cod'] = '<input type="text" size="20" name="'.$cp['nome'].'" id="'.$cp['nome'].'" />';
			}
			if(strpos($cp['extra'], 'select') !== false){
				//campo de selecao de usuarios
				$campo['cod'] = '<select name="'.$cp['nome'].'" id="'.$cp['nome'].'"><option selected value=""> -- Selecione -- </option>';
				$attr = explode(",",$cp['attr']);
				foreach ($attr as $c) {
					$c = explode("=", $c);
					if($c[0] == '_separador_')
						//se o campo for separador, coloca opcao desabilitada
						$campo['cod'] .= '<option value="" disabled="" style="background-color: #404040; color:white">-&gt; '.$c[1].'</option>';
					else 
						$campo['cod'] .= '<option value="'.$c[0].'">'.$c[1].'</option>';
					if ($c[0] == $valor[$campo['nome']])
						$campo['valor'] = $c[1];//se select, valor eh o 'value' da opcao
				}
				$campo['cod'] .= '</select>'; 
			}
			//tendo o ID do usuario, procura no BD o nome do usuario, que eh o valor do campo
			if (isset($valor[$campo['nome']]) && $valor[$campo['nome']] > 0){
				$res = getNamesFromUsers($valor[$campo['nome']]);
				if (count($res))
					$campo['valor'] = $res[0]['nomeCompl'];
				else
					$campo['valor'] = 'Usu&aacute;rio desconhecido.';
			}
			
		} elseif ($cp['tipo'] == 'documentos') {
			//campos de documetos. cria div que mostrara os nomes de documentos e um campo oculto que guardas os IDs a serem colocados no campo do BD 
			$campo['cod'] = '<div id="'.$cp['nome'].'Nomes" class="cadDisp"></div><input type="hidden" name="'.$cp['nome'].'" id="'.$cp['nome'].'" />
				<a id="addDocLink" href="#" onclick="window.open(\'sgd.php?acao=busca_mini&amp;onclick=adicionarCampo&amp;target='.$cp['nome'].'\',\'addDoc\',\'width=750,height=550,scrollbars=yes,resizable=yes\')">Adicionar Documento </a>';
		
		} elseif ($cp['tipo'] == 'composto') {
			//tipo composto de varios outros campos
			//algoritmo: le as partes, procura, recursivamente, o codigo de cada parte
			$partes = explode("+",$cp['attr']);
			$campo['cod'] = '<input type="hidden" name="'.$c.'" id="'.$c.'" value= "'.$cp['nome'].'" />';
			//para cada parte, obtem o codigo da parte e concatena com os dados ja obtidos 
			foreach ($partes as $p) {
				$dados = montaCampo($p,$tipo,$valor,$busca);//busca nome, cod e valor da parte
				if($dados != null){//se a parte for campo
					$campo['nome'] .= ','.$dados['nome'];//concatena o nome da parte
					$campo['cod'] .= $dados['cod'];//concatena o codigo da parte
					$campo['valor'] .= $dados['valor'];//concatena o valor da parte
				}else{//se a parte nao for campo (separador, por ex)
					$campo['cod'] .= str_replace('"','',$p);//concatena o codigo com a parte sem aspas
					$campo['valor'] .= str_replace('"','',$p);//concatena o valor com a parte sem aspas
				}
			}
		
		} elseif($cp['tipo'] == 'anoSelect') {//tipo de anoSelect
			$anoAtual = date("Y");//determina o ano atual
			//se for busca, deicxa ano em branco por padrao (caso nao queira determinar o ano na busca)
			
			if($tipo == 'bus') {
				$campo['cod'] = '<input type="text" id="'.$campo['nome'].'" name="'.$campo['nome'].'" value="" size="3" />';
			} else { 
				$campo['cod'] = '<input type="hidden" id="'.$campo['nome'].'" name="'.$campo['nome'].'" value="'.$anoAtual.'" />';
				$campo['cod'] .= '<input type="text" id="'.$campo['nome'].'2" name="'.$campo['nome'].'2" size="3" maxlength="4" />'.//cria o campo de texto para digitar manualmente caso o ano seja inferior ao apresentados na selecao
							'<select name="'.$campo['nome'].'1" id="'.$campo['nome'].'1">';
				//cria opcao para nao determinar o ano de procura
				$campo['cod'] .= '<option selected name="'.$anoAtual.'" value="'.$anoAtual.'">'.$anoAtual.'</option>';
				//completa a selecao com os ultimos 5 anos
				for ($i = 1; $i < 6; $i++) {
					$campo['cod'] .= '<option name="'.($anoAtual-$i).'" value="'.($anoAtual-$i).'">'.($anoAtual-$i).'</option>';
				}
				//cria a opcao 'outros' e o codigo JS para mudar lidar com os campos
				$campo['cod'] .= '<option id="'.$campo['nome'].'outroAno" name="'.$campo['nome'].'outroAno">Outro</option>
								</select>
								<script type="text/javascript">
									//campo 2 comeca oculto
									$("#'.$campo['nome'].'2").hide();
									//quando mudados a opcao selecionada
									$("#'.$campo['nome'].'1").change(function(){
										//copia a opcao selecionada para o campo oculto principal
										$("#'.$campo['nome'].'").val($("#'.$campo['nome'].'1").val());
										if($("#'.$campo['nome'].'outroAno").attr("selected")){
											//se selecionarmos outro, limpa o valor do campo principal
											$("#'.$campo['nome'].'").val("");
											//esconde o campo de selecao
											$("#'.$campo['nome'].'1").hide();
											//mostra o campo de texto pra digitacao
											$("#'.$campo['nome'].'2").show();
											//coloca o campo de texto no foco
											$("#'.$campo['nome'].'2").focus();
										}
									});
									
									//para cada caracter digitado, copia o novo valor do campo2 para o campo principal
									$("#'.$campo['nome'].'2").keyup(function(){
										$("#'.$campo['nome'].'").val($("#'.$campo['nome'].'2").val());
									});
								</script>';
				}
			//valor do ano nao sofre tratamento
			if(isset($valor[$c])) $campo['valor'] = $valor[$c];
		} else {
			//se for outra coisa, copia o codigo HTML em attr
			$campo['cod'] = $cp['attr'];
			//valor do campo indefinido nao sobre nenhum tratamento
			if(isset($valor[$c])) $campo['valor'] = $valor[$c];
		}
		//se for parte de um campo composto, marca com a flag
		if (strpos($cp['extra'],'parte') !== false) {
			$campo['parte'] = true;
		}
		//so for obrigatorio, marca com a classe obrigatorio
		if(strpos($cp['extra'], 'obrigatorio') !== false && $tipo == 'cad'){
			$max = 1;
			$campo['cod'] = str_replace('" ', '" class="obrigatorio"', $campo['cod'], $max);
			$campo['cod'] .= '*';
		}
		return $campo;
	}
	
	/**
	 * Consulta todas as areas no BD
	 * @param Connection $bd
	 */
	function getDeptos(){
		//consulta todas as areas dos usuarios distintas
		$r = getAreasFromUsers();
		//coloca as areas em um array
		foreach ($r as $dep) {
			$deptos[] = $dep['area'];
		}
		//retorna o array
		return $deptos;
	}
	
	/**
	 * Gera codigo HTML para adicionar boxes de conteudo a pagina.
	 * Adicionalmente, gera JQuery para esconder os boxes cujo valor visible[c[i]]=false
	 * 
	 * (G)
	 * @param int $num
	 * @param array $visible
	 */
	function addContentBox($num,$visible) {
		if(count($visible) != $num+1)
			return null;
		
		$html = '';
		$js = '<script type="text/javascript">$(document).ready(function(){';
		for ($i = 0; $i < $num; $i++) {
			//cria o box de conteudo adicional
			$html .= '
			</div>
			<div id="c'.($i+2).'" class="boxCont">
			{$content'.($i+2).'}';
			
			if(!$visible[$i])//gera Jquery para esconder o campo se visible[i] eh falso
				$js .= '$("#c'.($i+1).'").hide();';
		}
		
		if(!$visible[$i])
				$js .= '$("#c'.($i+1).'").hide();';
		
		$js .= '}); </script>';
		return $html.$js;
	}
	
	/**
	 * Gera o PDF correspondente ao documento
	 * @param int $id id do documento a ser convertido.
	 * @param mysql link $bd conexao com o bd
	 */
	function geraPDF($id,$bd){
		$doc = new Documento($id);
		$doc->bd = $bd;
		$doc->loadCampos($bd);
		
		require_once("classes/mpdf51/mpdf.php");
		//le os arquivos HTML para determinar o cabecalho, rodape e conteudo
		$header = file_get_contents("templates/doc_header.html");  
		//$footer = file_get_contents("templates/doc_footer.html");
		$html = file_get_contents("templates/".$doc->dadosTipo['template']);
		
		//completa os campos de autor
		$autor = $_SESSION;
		foreach ($autor as $ch => $dado) {
			$html = str_replace('{$Autor_'.$ch."}", $dado, $html);
		}
		
		//tratamento de campos especiais (que nao apenas imprimir os dados do BD)
		foreach ($doc->campos as $ch => $dado) {
			$res = $bd->query("SELECT * FROM label_campo WHERE nome='".$ch."'");
			$res = $res[0];
			
			if($res['tipo'] == 'userID'){
				//tratamento de usuario
				$resuser = $bd->query("SELECT * FROM usuarios WHERE id = $dado");
				foreach ($resuser[0] as $atr => $val) {
					//pra cada atributo (nome, sobrenome, matr, etc) coloca o valor correspondente
					$html = str_replace('{$'.$ch.'_'.$atr."}", $val, $html);
				}
			}elseif($res['tipo'] == 'input' && strpos($res['extra'],"unOrg_autocompletar") !== false){
				//tratamento de unOrg
				$un = explode("(", $dado);//corta o campo nos (
				$unOrg['sigla'] = rtrim($un[count($un)-1],")");//a sigla eh o que esta entre u ultimo ()
				$un = explode(' - ',$un[0],2);//separa no ' - ' 
				$unOrg['cod'] = $un[0];//o cogigo eh o que esta antes do hifen
				$un = explode(" / ", $un[1]);//separa o resto pelas barras
				$unOrg['nome'] = $un[count($un)-1];// o que esta no ultimo pedaco eh o nome da unidade
				//coloca os dados nas posicoes corretas
				foreach ($unOrg as $atr => $val) {
					$html = str_replace('{$'.$ch.'_'.$atr."}", $val, $html);
				}
			}elseif ($res['tipo'] == 'documentos'){
				//tratamento de documentos (mostrar nomes, numeros, etc)
				$docID = explode(",", $dado);
				$docs[1]['nome'] = ''; $docs[2]['nome'] = ''; $docs['total']['nome'] = '';
				$docs[1]['tam'] = 0; $docs[2]['tam'] = 0; $docs['total']['tam'] = 0;
				$i = 0;
				foreach ($docID as $did) {
					//obtem os IDs dos documentos anexados
					if($did != ''){
						//carrega os dados do documento
						$doci = new Documento($did);
						$doci->loadTipoData($bd);
						$docs['total']['nome'] .= $doci->dadosTipo['nome'].' '.$doci->numeroComp.'<br />';
						$docs['total']['tam']++;
						//se houver mais de 5 documentos, divide em 2 colunas
						if(count($docID) >= 6 && $i%2 == 1){
						 	$docs[2]['nome'] .= $doci->dadosTipo['nome'].' '.$doci->numeroComp.'<br />';
						 	$docs[2]['tam']++;
						} else {
						 	$docs[1]['nome'] .= $doci->dadosTipo['nome'].' '.$doci->numeroComp.'<br />';
						 	$docs[1]['tam']++;
						}
						$i++;
					}
				}
				//completa as colunas para que a tabela tenha pelo menos 5 linhas de altura
				while ($docs[1]['tam'] < 5 && $docs[2]['tam'] < 5){
					$docs[1]['nome'] .= '<br />';
					$docs[2]['nome'] .= '<br />';
					$docs[1]['tam']++;
					$docs[2]['tam']++;
				}
				//completa a coluna total para que tenha pelo menos 5 linhas de altura
				while($docs['total']['tam'] < 5){
					$docs['total']['nome'] .= '<br />';
					$docs['total']['tam']++;
				}
				//coloca os documentos no lugar correto
				$html = str_replace('{$'.$ch."_1}", $docs[1]['nome'], $html);
				$html = str_replace('{$'.$ch."_2}", $docs[2]['nome'], $html);
				$html = str_replace('{$'.$ch."}", $docs['total']['nome'], $html);
			}else{
				$dado = montaCampo($ch, $bd, 'mostra', array($ch => $dado));
				$html = str_replace('{$'.$ch."}", $dado['valor'], $html);
			}
		}
		
		//coloca despacho no documento
		$despacho = $doc->getHist();
		$despacho = $despacho[count($despacho) - 1]['despacho'];
		$html = str_replace('{$despacho}',$despacho,$html);
		
		//completando as datas
		$data['dia1'] = date("j",$doc->data);
		$data['mes1'] = date("n",$doc->data);
		$mesExt = array("","janeiro","fevereiro","mar&ccedil;o","abril","maio","junho","julho","agosto","setembro","outubro","novembro","dezembro");
		$data['mes2'] = substr($mesExt[$data['mes1']],0,3);
		$data['mes3'] = $mesExt[$data['mes1']];
		$data['ano1'] = date("y",$doc->data);
		$data['ano2'] = date("Y",$doc->data);
		foreach ($data as $ch => $dado) {
			$html = str_replace('{$'.$ch."}", $dado, $html);
		}
		
		//para docs que contem 2 cabecalhos (rr)
		$html = str_replace('{$header}', $header, $html);
		
		//inicializa a variavel pdf com os tamanhos padrao
		$pdf = new mPDF('c','A4',12,'Arial',30,15,35,10,12,10,'P');
		$pdf->allow_charset_conversion=true;
		$pdf->charset_in='UTF-8';
		//seta os dados
		$pdf->SetHTMLHeader($header);
		//$pdf->SetHTMLFooter($footer); //Rodape eliminado
		$pdf->WriteHTML(utf8_encode($html));
		//seta o nome do arquivo
		$fileName = '['.$doc->id.']_'.$doc->dadosTipo['nome'].'_'.$doc->numeroComp.'_(PDF_DOC_ORIGINAL).pdf';
		$fileName = strtolower($fileName);
		$fileName = str_replace(array('/','ç','á','ã','â','ê','é','í','ó','õ','ô','ú','&ccedil;','&aacute;','&atilde;','&acirc;','&ecirc;','&eacute;','&iacute;','&oacute;','&otilde;','&ocirc;','&uacute',' ','?','\'','"','!','@',"'"), array('-','c','a','a','a','e','e','i','o','o','o','u','c','a','a','a','e','e','i','o','o','o','u','_','','','','','',''), $fileName);
		
		$pdf->Output('files/'.$fileName,'F');
		//anexa ao documento
		$doc->anexo[] = $fileName;
		$doc->salvaAnexos();
		//retorna o nome do arquivo
		return $fileName;
	}
?>