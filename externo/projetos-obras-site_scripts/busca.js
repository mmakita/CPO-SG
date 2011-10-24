$(document).ready(function(){
	$("#unOrg").autocomplete("../unSearch.php",
		{
			minChars:2,
			matchSubset:1,
			matchContains:true,
			maxCacheLength:20,
			extraParams:{'show':'un'},
			selectFirst:true,
			onItemSelect: function(){
				$("#unOrg").focus();
			}
		});

	$("#buscaObraForm").submit(function(event){
		applyFilter();
		
		event.preventDefault();
	});
});

function applyFilter(){
	//campus
	var attr = $(".campus");
	var campus = '';
	$.each(attr,function(i) {
		if(attr[i].checked)
			campus += attr[i].value + '|';
	});
	if(campus.charAt(campus.length-1) == '|')
		campus = campus.slice(0 , campus.length-1);

	//nome
	var nome = $("#nome").val();
	
	//unOrg
	var unOrg = $("#unOrg").val();
	
	//tipo
	var attr = $(".tipo");
	var tipo = '';
	$.each(attr,function(i) {
		if(attr[i].checked)
			tipo += attr[i].value + '|';
	});
	if(tipo.charAt(tipo.length-1) == '|')
		tipo = tipo.slice(0 , tipo.length-1);
	
	//caract
	var attr = $(".caract");
	var caract = '';
	$.each(attr,function(i) {
		if(attr[i].checked)
			caract += attr[i].value + '|';
	});
	if(tipo.charAt(tipo.length-1) == '|')
		caract = caract.slice(0 , caract.length-1);
	
	
	//area
	var area = '', min = '', max = '';
	
	if(!$("#area1").attr("checked") && $("#area2").attr("checked")) {
		min = $("#a2").val();
	} else if(!$("#area1").attr("checked") && $("#area3").attr("checked")) {
		min = $("#a4").val();
	}
	
	if(!$("#area3").attr("checked") && $("#area2").attr("checked")) {
		max = $("#a3").val();
	} else if($("#area1").attr("checked")) {
		max = $("#a1").val();
	}
	
	if($("#area0").attr("checked")) {
		area = 'N|' + min + '-' + max;
	} else {
		area = min + '-' + max;
	}
	
	var param = {
		'campus'   : campus,
		'nome'     : escape(nome),
		'unOrg'    : unOrg,
		'tipo'     : tipo,
		'caract'   : caract,
		'area'     : area
	}
	//alert('campus:' + campus + "\nnome:" + nome + "\nunOrg:" + unOrg + "\ntipo:" + tipo + "\narea:" + area + "\npav:" + pav + "\nelev:" + elev + "\nrec:" + rec + "\nrec_total:" + rec_total);
	
	document.getElementById('gmapsRes').contentWindow.filterResults(param);
}

function listarObras(obra){
	if(obra.length == 0) {
		$("#listaRes").html('<br /><center><b>N&atilde;o h&aacute; obras com os crit&eacute;rios escolhidos.</b></center>');
	} else {
		$("#listaRes").html('<span style="text-align: right; display:block;"><b>'+obra.length+'</b> obras encontradas com os par&acirc;metros escolhidos.</span>'+
				'<table id="listaObrasRes" width="100%"><tr><td class="c"></td></tr><tr class="c"><td class="c"><b>Nome/Unidade</b></td></tr></table>');
		
		$.each(obra, function(i){
			if(!obra[i].descr.valor)
				var descr = '';
			else
				var descr = obra[i].descr.label+'<br /><br />';
			
			$("#listaObrasRes").append('<tr class="c"><td class="c"><a href="javascript:void(0)" onclick="showObraDet('+obra[i].id+')"><b>' + obra[i].nome + '</b></a><br />' + obra[i].unidade.compl +
			'<div id="obraDet'+obra[i].id+'" style="display:none"> <br />'
			+descr
			+'<b>&Aacute;rea: </b>'+obra[i].area.compl+'<br />'
			+'<b>Caracter&iacute;stica: </b>'+obra[i].caract.label+'<BR />'
			+'<b>Tipo: </b>'+obra[i].tipo.label+'<BR />'
			+'<b>Estado: </b>'+obra[i].estado.label+' </div>'+
			'</td></tr>');
		});
	}
}

function showObraDet(id){
	if($("#obraDet"+id).css("display") == 'none'){
		$("#obraDet"+id).slideDown();
	} else {
		$("#obraDet"+id).slideUp();
	}
}

function showMap(){
	$("#listaRes").hide();
	$("#gmapsRes").show();
	$("#show_list").css('text-decoration','none');
	$("#show_map").css('text-decoration','underline');
}

function showList(){
	$("#listaRes").show();
	$("#gmapsRes").hide();
	$("#show_list").css('text-decoration','underline');
	$("#show_map").css('text-decoration','none');
}
