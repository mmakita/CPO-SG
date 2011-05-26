$(document).ready(function(){
	$("#novoForm").submit(function(submit){
		var obrigatorios = $('input.obrigatorio:text[value=""]');
		
		//verificacao dos campos obrigatorios
		if(obrigatorios[0] != undefined){
			alert("Há campos obrigatórios não preenchidos. Por favor, preencha-os e envie novamente.");
			submit.preventDefault();
		}	
	});//closes submit
 });//close document.ready