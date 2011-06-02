$(document).ready(function(){
	$(".hid").hide();
	
	$("#buscaForm").submit(function(submit){
		submit.preventDefault();
		var q = "?tipoBusca=cadSearch&tabela="+$("#tabBD").val()+"&campos="+$("#camposBusca").val()+"&labelID="+$("#labelID").val();
		var campos = $("#camposBusca").val();
		var campo = campos.split(",");
		var i = 0, exit = false;
			
		for(i = 0 ; i < campo.length ; i++){
			var cpval = $("#"+campo[i]).val();
			if(cpval == ''){
				limpaCampos();
				alert("Todos os campos devem ser preenchidos!");
				exit = true;
				return;
			}
			q += "&"+campo[i]+"="+cpval;
		};//close each
		if(!exit)
			doBuscaProc(q);
	});//close submit

	$("#cadForm").submit(function(submit){
		var campos = $("#camposBusca").val();
		var campo = campos.split(",");
		var i;
		var obrigatorios = $('input.obrigatorio:text[value=""]');
		
		//verificacao dos campos obrigatorios
		if(obrigatorios[0] != undefined){
			alert("Há campos obrigatórios não preenchidos. Por favor, preencha-os e envie novamente.");
			submit.preventDefault();
		}	
				
		//copiando os campos de busca para o form de envio
		$.each(campo,function(i){
			$("#_"+campo[i]).val($("#"+campo[i]).val());
		});//close each
		
	});//closes submit
 });//close document.ready
 
 function doBuscaProc(q){
	$("#buscabut").val("Buscando...");
	$("#buscabut").attr("disabled","disabled");
	
	limpaCampos();
	
	$.get("sgd_busca.php"+q,function(d){//busca
		//$(".hid").append("sgd_busca.php"+q+"\n"+d);//
		var data = eval(d);
		
		$("#buscabut").attr("disabled","");
		$("#buscabut").val("Enviar");

		if(data.length > 0){
			$("#id").val(data[0].id);
			
			$("a#addObraLink").hide();
			$("a#addDocLink").hide();
			$("a#addEmpresaLink").hide();
			$("#arq1").hide();
			
			copiaCamposBusca();
			
			if(data[0].anexado || !data[0].despachavel)
				hideDesp();
			
			//completar campos
			completa_campos(data[0]);
			
			//completar arquivos anexos
			completa("arqs",data[0]);
			
			//completar documentos
			completa("docs",data[0]);
			
			//completar obra
			completa("obra",data[0]);
			
			//completar empresas
			completa("empresa",data[0]);
			
			//completar os campos
			completa("hist",data[0]);
		}
		$(".retirar").hide();
		$(".hid").slideDown("");
	});//close get
} 

function hideDesp(){//bloqueia o despacho caso o documento esteja com outra pessoa ou anexado.
	$("#para").hide();
	$("#despacho").html("N&atilde;o &eacute; poss&iacute;vel despachar esse documento pois ele est&aacute; anexado a um outro documento ou voc&ecirc; n&atilde;o tem privil&eacute;gios suficientes para realizar esta a&ccedil;&atilde;o.");
	$("#despacho").attr("disabled","disabled");
	$("#submitCad").attr("disabled","disabled");
	$("#rrNumReceb").attr("disabled","disabled");
	$("#rrAnoReceb").attr("disabled","disabled");
	$("#unOrgReceb").attr("disabled","disabled");
}
 
function limpaCampos(){// 'reseta o formulario para repreenchimento
	var campos = $("#camposGerais").val();
	var campoID = campos.split(",");
	$.each(campoID,function(i){
		if($("#"+campoID[i]).attr('type') == 'checkbox' || $("#"+campoID[i]).attr('type') == 'radio')
			$("#"+campoID[i]).attr('checked','');
		else if($("#"+campoID[i]).attr('type') == 'text')
			$("#"+campoID[i]).val('');
		else
			$("#"+campoID[i]).val("nenhum");
	});
	$("input").attr("disabled","");
	$("#para").show();
	$("#despacho").html("Digite o despacho aqui.");
	$("#despacho").attr("disabled","");
	$("div#arqs").html("");
	$("div#docsAnexos").html("");
	$("div#obrasAnexas").html("");
	$("div#emprAnexas").html("");
	$("div#docsAnexosNomes").html("");
	$("div#obrasAnexasNomes").html("");
	$("div#emprAnexasNomes").html("");
	$("div#hist").html("");
	$("a#addDocLink").show();
	$("a#addObraLink").show();
	$("a#addEmpresaLink").show();
	$("#arq1").show();
	$("#id").val("0");
	$("#docsAnexos").val("");
	$("select").attr("disabled","");
}

function copiaCamposBusca(){//na ocasiao do encio do formulario de busca, copia os campos para o formulario de cadastro para passar para as proximas paginas
	var camposBusca = $("#camposBusca").val();
	var campoNome = camposBusca.split(',');
	var i = 0;
	$.each(campoNome,function(){
		$("#_"+campoNome[i]).val($("#"+campoNome[i]).val());
		i++;
	});
}

 //completa os campos do formulario com os dados lidos em ajax
function completa_campos(data){	
	var campos = $("#camposGerais").val();
	var campoID = campos.split(",");
	var i = 0;
	$.each(campoID,function(){
		if($("#"+campoID[i]).attr('type') == 'checkbox'){
			if(data[campoID[i]] == 1){
				$("#"+campoID[i]).attr('checked','checked');
			}
		} else {
			$("#"+campoID[i]).val(convertFromHTML(data[campoID[i]]));
		}
		$("#"+campoID[i]).attr("disabled","disabled");
		i++;
	});
}

//completa os campos genericos de anexo
function completa(tipo,data){
	var dado = data[tipo];
	var linha = '';
	
	$("div#"+tipo).html('');//limpa o campo
	if(dado.length == 0){//verifica se há anexo
		$("div#"+tipo).append("Nenhum adicionado.");
		return;
	}

	var i = 0;
	$.each(dado,function(){
		if(tipo == "obra"){
			//linha = "Obra "+dado[i].id+": "+dado[i].nome+'(Ver detalhes Pend) <input type="hidden" name="obra'+i+'" value="'+dado[i].id+'" /><br />';
			//TODO passando IDs de obra pra prox pag (vide doc)
		}
		if(tipo == "docs"){
			newDocLink(dado[i].id,dado[i].nome,'docsAnexos','<br>');
		}
		if(tipo == "empresa"){
			//linha = '<input type="hidden" name="emp'+i+'" value="'+dado[i].id+'" />';
			//TODO passando IDs de empresa pra prox pag (vide doc)
		}
		if(tipo == "hist"){
			linha = "Em "+dado[i].data+" por "+dado[i].username+": "+dado[i].acao+"<br />";
		}
		if(tipo == "arqs"){
			if(dado.length == 1 && dado[1] == '')
				linha = "Nenhum arquivo anexado.";
			linha = '<a href="files/'+dado[i]+'">'+dado[i]+'</a><br />';
		}
		
		linha += "";
		$("div#"+tipo).append(linha);
		i++;
	});
}