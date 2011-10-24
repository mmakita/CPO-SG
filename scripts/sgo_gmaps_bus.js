google.maps.Map.prototype.clearMarkers = function() {
	for(var i=0; i < this.openMarkers.length; i++){
		this.openMarkers[i].setMap(null);
	}
	this.openMarkers = [];
};

google.maps.Map.prototype.clearInfoWindows = function() {
	for(var i=0; i < this.openInfoWindows.length; i++){
		this.openInfoWindows[i].close();
	}
	this.openInfoWindows = [];
};

function showGMap() {
	//conf da lat/lng
    var latlng = new google.maps.LatLng(-22.822,-47.067);
    //opcoes
    var myOptions = {
      zoom: 15,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.HYBRID
    };
    //carrega mapa
    map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);
    map.openMarkers = new Array();
    map.openInfoWindows = new Array();
  }

function filterResults(param){
	//faz chamada assincrona para carregar as obras iniciais
    $.get('sgo_busca.php', {
    	'tipoBusca'  : 'filtro',
    	'resultType' : 'basic',
    	'campus'     : param.campus,
	   	'nome'       : param.nome,
	   	'unOrg'      : param.unOrg,
	   	'tipo'       : param.tipo,
	   	'caract'     : param.caract,
	   	'area'       : param.area,
	   	'pav'        : param.pav,
	   	'elev'       : param.elev,
	   	'rec'        : param.rec,
	   	'rec_total'  : param.rec_total
    }, function(data) {
    	var obras = eval(data);
    	var semLatLng = false;
    	//resetando mapa e alerta de obras sem coordenadas
    	$("#alert").hide();
    	map.clearMarkers();
    	
    	if(obras.length == 0) {
    		$("#alert_noObras").show();
    	} else {
    		$("#alert_noObras").hide();
    	}
    	
    	//para cada obra encontrada, cria um marcador e uma entrada na tabela
	   	$.each(obras, function(i){
	   		//verifica se tem coordenadas para  mostrar o aviso
		   	if(!obras[i].lat && !obras[i].lng) {
			   	semLatLng = true;
			}
		   	
		   	if(!obras[i].caract.abrv)
		   		var icon = 'def';
		   	else
		   		var icon = obras[i].caract.abrv;
		   	
		   	//seta paramentros do marcador
			var latlng = new google.maps.LatLng(obras[i].lat,obras[i].lng);
			var marker = new google.maps.Marker({
				clickable: true,
				position: latlng, 
				map: map,
				icon : "img/icons/"+icon+".png",
				title: html_entity_decode(obras[i].nome)
			});
			
			if(!obras[i].descr.valor)
				var descr = '';
			else
				var descr = obras[i].descr.label;
			
			//adiciona evento para clique
			google.maps.event.addListener(marker, 'click', function(event) {
				map.clearInfoWindows();
				var infowindow = new google.maps.InfoWindow({
					content: '<span style="font-family: arial, sans-serif; font-weight: bold;">'+obras[i].nome+'</span><br />'
						+'<span style="font-family: arial, sans-serif; font-size: 10pt">'+obras[i].unidade.compl+'</span><br /><br />'
						+descr+'<br /><br />'
						+'<a href="javascript:void(0)" onclick="javascript:window.open(\'sgo.php?acao=ver&amp;obraID='+obras[i].id+'\',\'obra_det\',\'width=900,height=650,scrollbars=yes,resizable=yes\')" style="font-family: arial, sans-serif; font-size: 10pt; color: #BE1010;">Ver p&aacute;gina da obra</a>'
				});
				//se clickar no marcador, abre a janela de informacao
				infowindow.open(map,marker);
				map.openInfoWindows.push(infowindow);
			});
			//empilha os marcadores do mapa
			map.openMarkers.push(marker);

	   	});
	   	//adiciona linha na tabela de resultados
		parent.listarObras(obras);
	   	
	   	if(semLatLng)
	   		$("#alert").show();
    });
	
}

function focusCampus(campusName) {
	var coord = new Array();
	
	switch(campusName) {
	case 'unicamp' :	coord['lat'] = -22.822;
						coord['lng'] = -47.067;
						coord['zoom'] = 15;
			  			break;
	
	case 'cotuca' :		coord['lat'] = -22.9023;
						coord['lng'] = -47.0670;
						coord['zoom'] = 19;
		  				break;
	
	case 'cpqba' :		coord['lat'] = -22.7972;
						coord['lng'] = -47.1151;
						coord['zoom'] = 17;
						break;
	
	case 'lim1' :		coord['lat'] = -22.5616;
						coord['lng'] = -47.4241;
						coord['zoom'] = 17;
						break;
	
	case 'fca' :		coord['lat'] = -22.5524;
						coord['lng'] = -47.4289;
						coord['zoom'] = 16;
		  				break;
	
	case 'fop' :		coord['lat'] = -22.7015;
						coord['lng'] = -47.6479;
						coord['zoom'] = 17;
		  				break;

	case 'pircentro' :	coord['lat'] = -22.7275;
						coord['lng'] = -47.6514;
						coord['zoom'] = 19;
				 		break;
	}
	
	var latlng = new google.maps.LatLng(coord['lat'],coord['lng']);
	
	map.setCenter(latlng);
	map.setZoom(coord['zoom']);
}