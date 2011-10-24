$(document).ready(function(){
	$("#nome").keyup(function(e){
		sugereObra();
	});
});


function sugereObra() {
	if($("#nome").val().length >= 3){
		$.get('sgo_busca.php',{
			tipoBusca:    "sugestao", 
			nome:         escape($("#nome").val()),
			unOrg:        ''
		}, function(data){
			var obras = eval(data);
			
			if(obras.length == 0) {
				$("#sugestoesObra").show();
				$("#sugestoesObra").html('<b>Nenhuma obra encontrada</b><br />');
			} else {
				$("#sugestoesObra").show();
				$("#sugestoesObra").html('Obras encontradas:<br /><table style="width:100%" id="obraNomes"></table>');
				
				$.each(obras,function(i){
					$("#obraNomes").append('<tr class="c"><td class="c">'+obras[i].codigo+' - '+obras[i].nome+' ('+obras[i].unidade.sigla+')</td><td class="c" style="width: 125px;"><a href="javascript:void(0)" id="link_'+obras[i].id+'" onclick="atribObra('+obras[i].id+',\''+obras[i].nome+'\')">Atribuir a esta obra</a></td></tr><br />');
				});
			}
		});
	}
}


function atribObra(obraID,obraNome){
	$("#link_"+obraID).html("Aguarde...");
	//var onclick = $("#link_"+obraID).attr("onclick");
	$("#link_"+obraID).attr("onclick","void(0)");
	var getVars = getUrlVars();
	
	
	$.get('sgd.php',{
		acao  : 'atribObraAjax',
		docID : getVars['docID'],
		obraID: obraID
	},function(d){
		var fb = eval(d);
		if(fb[0].success == 'true') {
			$("#sugestoesObra").html("Atribui&ccedil;&atilde;o bem sucedida!");
			$("#obraAtual").html('Este documento est&aacute; realcionado &agrave; obra: <a href="javascript:void(0)" onclick="javascript:window.open(\'sgo.php?acao=ver&amp;obraID='+obraID+'\',\'obra_det\',\'width=900,height=650,scrollbars=yes,resizable=yes\')">'+obraNome+'</a>');
		} else {
			$("#link_"+obraID).html("Erro. Tentar novamente.");
			$("#link_"+obraID).attr("onclick","atribObra("+obraID+","+obraNome+")");
		}
	});
}

function editVal(campoNome){
	$("#"+campoNome+"_val").hide();
	$("#"+campoNome+"_edit").show();
	$("#"+campoNome+"_link").html("Salvar");
	$("#"+campoNome+"_link").attr("href","javascript:saveVal('"+campoNome+"')");
}

function saveVal(campoNome){
	
	$.get('sgd.php',{
		acao   : 'edit',
		docID  : $("#docID").html(),
		campo  : campoNome,
		newVal : escape($("#"+campoNome).val())
	},function(d){
		var fb = eval(d);
		if(fb[0].success == 'true'){
			var href = 'javascript:editVal(\''+campoNome+'\')';
			var msg = 'Salvo!';
			$("#"+campoNome+"_val").html($("#"+campoNome).val());
			$("#"+campoNome+"_edit").hide();
			$("#"+campoNome+"_val").show();
		} else {
			var href = 'javascript:saveVal(\''+campoNome+'\')';
			var msg = 'Erro. Tentar Novamente.';
		}
		
		$("#"+campoNome+"_link").html(msg);
		$("#"+campoNome+"_link").attr("href",href);
	});
	
}

function anexarDoc(filhoID,paiID){
	var ID = '';
	if($("#"+'addEste').attr('checked')) {
		ID = paiID;
	} else if($("#"+'addOutr').attr('checked')) {
		ID = filhoID;
	}
	$("#anexID"+ID).html('Anexando...');
	$("#anexID"+ID).attr('href','javascript: void(0);');
	
	$.get('sgd.php',{
		acao    : 'saveAnex',
		docID   : ID,
		filhoID : filhoID,
		paiID   : paiID
	}, function(d){
		d = eval(d);
		
		if(d[0].success == 'true' && ID == paiID){
			var entries = $("a.resEntry");
			$.each(entries,function(i){
				entries[i].innerHTML ='Anexado a outro doc.';
				entries[i].attributes[2].nodeValue='javascript: void(0);';
			});
			$("#anexID"+ID).html('Anexado com sucesso!');
		} else if(d[0].success == 'true' && ID == filhoID){
			$("#anexID"+ID).html('Anexado com sucesso!');
		} else {
			$("#anexID"+ID).html('Falha. Tentar Novamente');
			$("#anexID"+ID).attr('href','javascript: anexarDoc('+filhoID+','+paiID+');');
		}
	});
}

function showAlert(){
	$("#alert").show();
	$("#c2").slideDown();
	resetBusca();
}

function hideAlert(){
	$("#alert").hide();
	$("#c2").slideDown();
	resetBusca();
}