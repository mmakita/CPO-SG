$(document).ready(function(){
	$("#despExt").autocomplete("unSearch.php",{minChars:2,matchSubset:1,matchContains:true,maxCacheLength:1,extraParams:{'show':'un'},selectFirst:true,onItemSelect: function(){$("#unOrgReceb").focus();}});	

	clearAll();
		
	$("#para").change(function(){
		clearAll();
		if($("#outr").attr("selected")){
			$("#outro").show();
		}else if($("#ext").attr("selected")){
			$("#despExt").show();
		}else if($("#arq").attr("selected")||$("#solic").attr("selected")){
			
		}else{
			loadNames($("#para option:selected").val());
		}
	});
	
	$("#despacho").click(function(){
<<<<<<< HEAD
		if($("#despacho").val() == 'Digite o despacho aqui.') {
			$("#despacho").html("");
		}
	});
	
	$("#despachoForm").submit(function(){
		if($("#despacho").val() == 'Digite o despacho aqui.') {
			$("#despacho").html("");
		}
	})
=======
		$("#despacho").html("");
	});
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
});

function clearAll(){//inicializa os campos de despacho (V1)
	$("#subp").hide();
	$("#outro").hide();
	$("#despExt").hide();
	$("#subp").val("");
	$("#outro").val("");
	$("#despExt").val("");
}

function loadNames(area){//completa os nomes dos funcionarios de um depto CPO
<<<<<<< HEAD
	$("#subp").html('<option id="_todos" value="_todos" selected="selected" >Todos nesse Depto.</option>');
=======
	$("#subp").html('<option value=""></option>');
	$("#subp").append('<option id="_todos" value="_todos">Todos nesse Depto.</option>');
>>>>>>> 4dd0e794cea62da21cb2ef318d6662dd305d5638
	$.get("unSearch.php?show=pessoas&area="+escape(area),function(d){
		var data = eval(d);
		var i = 0;
		$.each(data,function(){
			$("#subp").append('<option id="'+data[i].id+'" name="'+data[i].id+'" value="'+data[i].id+'">'+data[i].nome+'</option>');
			i++;
		});
	});
	
	$("#subp").show();
}