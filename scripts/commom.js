
/*=============================================
  FUNCOES PARA GERENCIAMENTO DE FORMULARIOS
=============================================*/
function closeDialog(){//fecha todos os dialogos
	$(".dialog").hide();
}

function showDialog(id){//abre um dialogo
	$("#"+id).show();
}
	
function showInputFile(i){//mostra outro campo para anexo de arquivo
	$("#arq"+(i-1)).attr("onclick",";");
	$("#fileUpCell").append('<br /><input type="file" id="arq'+i+'" name="arq'+i+'" onclick="showInputFile('+(i+1)+')" />');
}


function html_entity_decode(str) {
	var ta=document.createElement("textarea");
	ta.innerHTML=str.replace(/</g,"&lt;").replace(/>/g,"&gt;");
	return ta.value;
}

function convertFromHTML(a){
	a = a + '';
	a = a.replace("&aacute;","á");
	a = a.replace("&atilde;","ã");
	a = a.replace("&agrave;","à");
	a = a.replace("&acirc;","â");
	a = a.replace("&eacute;","é");
	a = a.replace("&egrave;","è");
	a = a.replace("&ecirc;","ê");
	a = a.replace("&iacute;","í");
	a = a.replace("&igrave;","ì");
	a = a.replace("&oacute;","ó");
	a = a.replace("&ograve;","ò");
	a = a.replace("&ocirc;","ô");
	a = a.replace("&otilde;","õ");
	a = a.replace("&uacute;","ú");
	a = a.replace("&uuml;","ü");
	a = a.replace("&ccedil;","ç");
	a = a.replace("&Aacute;;","Á");
	a = a.replace("&Atilde;","Ã");
	a = a.replace("&Agrave;","À");
	a = a.replace("&Acirc;","Â");
	a = a.replace("&Eacute;","É");
	a = a.replace("&Egrave;","È");
	a = a.replace("&Ecirc;","Ê");
	a = a.replace("&Iacute;","Í");
	a = a.replace("&Igrave;","Ì");
	a = a.replace("&Oacute;","Ó");
	a = a.replace("&Ograve;","Ò");
	a = a.replace("&Ocirc;","Ô");
	a = a.replace("&Otilde;","Õ");
	a = a.replace("&Uacute;","Ú");
	a = a.replace("&Uuml;","Ü");
	a = a.replace("&Ccedil;","Ç");
	return a;
}

/*function verificaPadrao(valor,campo){
	var expr;
	if(campo == "numero_pr"){
		expr = /01 P-[0-9]{5}-[0-9]{4}/;
	}
	return expr.test(valor);
}*/

/*=================================
FUNCOES DE GERENCIAMENTO DE JANELAS
==================================*/

function getUrlVars(){
	// Get variaveis por JS;
	var vars = [], pedaco;
	if(window.location.href.indexOf('#') == -1){
		var inteira = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	} else{
		var inteira = window.location.href.slice(window.location.href.indexOf('?') + 1, window.location.href.indexOf('#')).split('&');
	}
	for (var i = 0; i < inteira.length; i++){
		pedaco = inteira[i].split('=');
		pedaco[1] = unescape(pedaco[1]);
		vars.push(pedaco[0]);
		vars[pedaco[0]] = pedaco[1];
	}
	return vars;
}

/*=========================================
FUNCOES PARA GERENCIAMENTO DE TABELAS/LINKS
=========================================*/

function showDesp(i){
	$('#desp'+i).slideDown();
}

function newEmprCadLink(data,nome){
	$.get('empresa.php?acao=cad&data='+data,function(suc){
		if(suc == '0'){
			alert("Erro ao cadastrar empresa");
		}else{
			id = suc;
			newEmprLink(id,nome);
		}
	});
	
}

function newEmprLink(id,nome){//monta link para empresa anexa
	if($("#emprAnexas").val().length == 0)
		$("#empresa").html("");
	$("#empresa").append('&nbsp;&nbsp;'+newWinLink('empresa.php?acao=ver&id='+id,'empresa'+id,950,650,nome)+'&nbsp;&nbsp;,');
	$("#emprAnexas").val($("#emprAnexas").val()+id+',');
}

function newDocLink(id,nome,target,sep){//monta um link de documento anexo
	if($("#"+target).val().length == 0)
		$("#docs").html("");
	$("#"+target+"Nomes").append(newWinLink('sgd.php?acao=ver&docID='+id,'detalhe'+id,950,650,nome)+newDelBut(id,target,'<br>')+'<br>');
	if($("#"+target).val().length == 0){
		$("#"+target).val($("#"+target).val()+id);
	} else {
		$("#"+target).val($("#"+target).val()+','+id);
	}
	
	if(target == 'ofir' || target == 'saa') {
		validateDoc(target);
	}
}

function newDelBut(id,target,sep){//cria o link para remocao do documento
	return ' <a href="javascript:void(0)" class="retirar" onclick="delDoc('+id+',\''+target+'\',\''+sep+'\')">[Retirar]</a>';
}

function delDoc(id,target,sep){//faz a remocao do documento
	var ids = $("#"+target).val().split(',');
	var nomes = $("#"+target+"Nomes").html().split(sep);
	var i = 0, idsNovos = '', nomesNovos = '';
	for(i = 0 ; i < ids.length ; i++){//varre o array de documentos e ignora o elemento a ser excluido
		if(ids[i] != id){
			idsNovos += ids[i] + ',';
			nomesNovos += nomes[i] + sep;
		}
	}
	$("#"+target).val(idsNovos.slice(0,idsNovos.length - 1));
	$("#"+target+"Nomes").html(nomesNovos);
}

function newWinLink(url,name,w,h,label){//monta o link para abrir em nova janela
	return '<a href="javascript:void(0)" id="'+name+'" onclick="window.open(\''+url+'\',\''+name+'\',\'width='+w+',height='+h+',scrollbars=yes,resizable=yes\')">'+label+'</a>';
}














function escolherDoc(target){
	window.open('sgd.php?acao=busca_mini&onclick=adicionarCampo&max=1&target='+target,'addDoc','width=750,height=550,scrollbars=yes,resizable=yes');
	$('#'+target+'Nomes').html('Documento Selecionado:');
	$('#'+target).val('');
	if(target == 'ofir') validateObra();
}

function newDocInNewWindow(tipo,target,acao){
	window.open('sgd.php?acao='+acao+'_mini&tipoDoc='+tipo+'&targetInput='+target+'&desp=off', 'cadMini', 'width=750,height=550,scrollbars=yes,resizable=yes');
	$('#'+target+'Nomes').html('Documento Selecionado:');
	$('#'+target).val('');
}

function updateDocID(target,value){
	$("#"+target+"Names").html("Documento "+value+" selecionado.");
	$("#"+target).val(value);
}