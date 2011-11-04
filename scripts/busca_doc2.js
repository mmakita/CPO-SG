$(document).ready(function(){
	
	$("input:checkbox").click(function(){
		carregaCampos();
	});
	
	$("#buscaForm").submit(function(submit){
		submit.preventDefault();
		doBusca();
	});
	
});


function doBusca(){
	$("#resBusca").html('<center><img src="img/carregando.gif" width="235" height="235" alt="Carregando... Aguarde!"/></center>');
	$("#resBusca").show();
	$(".buscaFormTable").slideUp();
	$(".novaBuscaBtn").show();
	
	var camposNomes = $("#camposNomes").val().split(',');
	var valoresBusca = '';
	var tipoDoc = '';
	var checkedBoxes = $(".tipoDoc:checked");
	var urlVar = getUrlVars();
	
	if(urlVar['onclick'] == undefined)
		urlVar['onclick'] = '';
	if(urlVar['target'] == undefined)
		urlVar['target'] = '';
		
	$("#btnBuscar").val('Buscando...');
	
	$.each(checkedBoxes, function(i){
		tipoDoc += checkedBoxes[i].value + ',';
	});
	
	$.each(camposNomes, function(i){
		if(camposNomes[i] != '')
			valoresBusca += camposNomes[i] + '=' + escape($("#" + camposNomes[i]).val()) + '|';
	});
	
	$.get('sgd_busca.php',{
			tipoDoc:      tipoDoc,
			tipoBusca:    "busca", 
			numCPO:       $("#numCPO").val(),
			numDoc:       $("#numDoc").val(),
			assunto_gen:  $("#assunto_gen").val(),
			dataCriacao:  $("#dataCriacao").val(),
			dataDespacho: $("#dataDespacho").val(),
			unDespacho:   $("#unDespacho").val(),
			dataReceb:    $("#dataReceb").val(),
			unReceb:      $("#unReceb").val(),
			contDesp:     $("#contDesp").val(),
			contGen:      escape($("#contGen").val()),
			valoresBusca: valoresBusca
		}, function(data){
		//alert(data);
		data = eval(data);
		
		if(data.length != 0){
			$("#resBusca").html('<table width="100%" id="res"><tr><td class="cc"><b>n° Doc.</b></td><td class="cc"><b>Tipo/Número</b></td><td class="cc"><b>Emitente</b></td><td class="cc"><b>Assunto</b></td><td class="cc"><b>A&ccedil;&atilde;o</b></td></tr>');
			$.each(data,function(i){
				var id = data[i].id;
				var nome = data[i].nome;
				var emissor = data[i].emitente;
				var assunto = data[i].assunto;
				var lk = newWinLink('sgd.php?acao=ver&docID='+id,'detalhe'+id,950,650,nome);
				var acao = addAction(urlVar['onclick'],id,nome,data[i].anexado,urlVar['target'],data[i].anexavel);
				$("#res").append('<tr class="c"><td class="cc">'+id+'</td><td class="cc">'+lk+'</td><td class="cc">'+emissor+'</td><td class="cc">'+assunto+'</td><td class="cc">'+acao+'</td></tr>');
			});
			$("#resBusca").append("</table>");
		}else{
			$("#resBusca").html("<center><b>N&atilde;o foi encontrado nenhum documento.</b></center>");
		}
		
	});
	
	$("#btnBuscar").val('Buscar novamente.');
	$("#resBusca").slideDown();
}

function carregaCampos(){
	$("#camposBusca").html("Carregando...");
	tipoString = '';
	var tipoDoc = $("input:checked[type=checkbox]");
	$.each(tipoDoc,function(i){
		tipoString += tipoDoc[i].value + ',';
	});
	
	$.get('sgd_busca.php',{tipoBusca: 'campoSearch' ,docs: tipoString}, function(htmlCampos){
		$("#camposBusca").html(htmlCampos);
	});
}

function checkAll(){
	var checked = $("input:checked[type=checkbox]");
	if(checked.length == 0){
		$("input[type=checkbox]").attr('checked','checked');
		carregaCampos();
		$("#resBusca").slideUp();
	} else {
		resetBusca();
	}
	
}

//adiciona os links referentes a acoes
function addAction(action,id,nome,anexado,target,anexavel){
	action = action.split(",");
	var i = 0, ret = '';
	for(i = 0 ; i < action.length ; i++){
		if(action[i] == 'adicionarCampo') ret += '<a href="#" onclick="addDoc('+id+',\''+nome+'\',\''+target+'\',\'<br>\')">Adicionar Documento</a>';
		if(action[i] == 'adicionar' && !anexado) ret += '<a href="#" onclick="addDoc('+id+',\''+nome+'\',\''+target+'\',\'<br>\')">Adicionar Documento</a>';
		if(action[i] == 'adicionar' && anexado) ret += 'J&aacute; anexado.';
		if(action[i] == 'ver') ret += newWinLink('sgd.php?acao=ver&docID='+id,'detalhe'+id,950,650,'Ver Documento');
		if(action[i] == 'anex'){
			var get = getUrlVars();
			var este = $("#"+'addEste').attr('checked');
			var outro = $("#"+'addOutr').attr('checked');
			if(este){
				if(anexavel == '1')
					ret += '<a class="resEntry" id="anexID'+id+'" href="javascript:anexarDoc('+get['docID']+','+id+');">Anexar a este documento.</a>';
				else
					ret += 'Este documento n&atilde;o pode receber outros documentos como anexo.';
			} else if(outro && !anexado){
					ret += '<a class="resEntry" id="anexID'+id+'" href="javascript:anexarDoc('+id+','+get['docID']+');">Anexar este documento.</a>';
			} else if(!este && ! outro) {
				ret += 'Nenhum tipo de anexa&ccedil;&atilde;o selecionado.';
			} else {
				ret += 'J&aacute; anexado.';
			}
		}
	}
	return ret;
}

function addDoc(id,nome,target,sep){
	window.opener.newDocLink(id,nome,target,'<br>');
	if(confirm("Documento adicionado com sucesso.\nClique OK para fechar a janela de busca."))
		self.close();
}

//cria um link para abertura em nova pagina
function newWinLink(url,name,w,h,label){
	return '<a href="javascript:void(0)" onclick="window.open(\''+url+'\',\''+name+'\',\'width='+w+',height='+h+',scrollbars=yes,resizable=yes\')">'+label+'</a>';
}

function resetBusca(){
	$("input[type=checkbox]").removeAttr('checked');
	carregaCampos();
	$("#resBusca").slideUp();
}

function novaBusca(){
	$(".buscaFormTable").slideDown();
	$(".novaBuscaBtn").hide();
}