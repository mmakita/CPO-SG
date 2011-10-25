$(document).ready(function(){

	$("#cadNovaObra").submit(function(event){
		verificaObrigatorio(event);
	});
});




function escolherDoc(target){
	window.open('sgd.php?acao=busca_mini&onclick=adicionarCampo&max=1&target='+target,'addDoc','width=750,height=550,scrollbars=yes,resizable=yes');
	$('#'+target+'Nomes').html('Documento Selecionado:');
	$('#'+target).val('');
	//$('#'+target+'Link').html('<a href="javascript:void(0);" onclick="escolherDoc(\''+target+'\')">Utilizar outro documento.</a>');
	validateObra();
}

function updateDocID(target,value){
	$("#"+target+"Names").html("Documento "+value+" selecionado.");
	$("#"+target).val(value);
	
}

function cadastrarDetalhesObra(){
	showCadObraDetForm();
	//window.open('sgo.php?acao=cadDetObra','addDetObra','width=750,height=550,scrollbars=yes,resizable=yes');
}

function newDocInNewWindow(tipo,target,acao){
	window.open('sgd.php?acao='+acao+'_mini&tipoDoc='+tipo+'&target='+target+'&desp=off', 'cadMini', 'width=750,height=550,scrollbars=yes,resizable=yes');
}

function validateObra(){
	if($("#ofir").val() != '')
		$("#row1").css("background-color","#CCFFCC");
}
