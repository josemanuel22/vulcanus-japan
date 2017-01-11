
function recogerSelecionado() {
	var elementos = document.getElementsByName("filtroCategoria");
	for(var i=0; i<elementos.length; i++) {
			if(elementos[i].checked) {
			var v = elementos[i].value;
		}
	}

	elementos = document.getElementsByName("ganancia");
	for(var i=0; i<elementos.length; i++) {
			if(elementos[i].checked) {
			var g = elementos[i].value;
		}
	}
	if(typeof v == 'undefined') {
		v="cualquiera";
	}
	if(typeof g == 'undefined') {
		g=-1;
	}
	filtrar(v,g);
}


function filtrar(categoria, ganancia) {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if(this.readyState == 4 && this.status == 200) {
				myFunction(this, categoria, ganancia);
			}
		};
		var dir = "./apuestas.xml";
		xhttp.open("GET", dir, true);
		xhttp.send();
}

function myFunction(xml, categoria, ganancia) {
	var xmlDoc = xml.responseXML;
	var table = 	"<thead>\
						<tr>\
							<th class='col-sm-2'>Categoria</th>\
							<th class='col-sm-2'>Fecha</th>\
							<th class='col-sm-2'>Evento</th>\
							<th class='col-sm-2'>ratio</th>\
						</tr>\
					</thead>";
	var x = xmlDoc.getElementsByTagName("apuesta");

	for(i=0;i<x.length;i++) {
		empateFlag = false;
		empate = "";
		ratio1 = x[i].getElementsByTagName("victoria1")[0].childNodes[0].nodeValue;
		ratio2 = x[i].getElementsByTagName("victoria2")[0].childNodes[0].nodeValue;
		if(!x[i].attributes[0].value.localeCompare("empatable")) {
			empateFlag = ganancia<x[i].getElementsByTagName("empate")[0].childNodes[0].nodeValue;
			empate = "<input type='radio' name='apuesta'>"+x[i].getElementsByTagName("empate")[0].childNodes[0].nodeValue+"€ X"+"</input>"+"<br />";
		}
		

		var categoriaFlag =false;
		if(!categoria.localeCompare('cualquiera') ) {
			categoriaFlag =true;
		} else if(!categoria.localeCompare(x[i].getElementsByTagName("categoria")[0].childNodes[0].nodeValue)) {
			categoriaFlag =true;
		}

		if( categoriaFlag && (ganancia < ratio1 || ganancia < ratio2 || empateFlag)) {
			rival1 = x[i].getElementsByTagName("rival1")[0].childNodes[0].nodeValue;
			rival2 = x[i].getElementsByTagName("rival2")[0].childNodes[0].nodeValue;
			id = x[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;

			table +="<tr><td>"+
			x[i].getElementsByTagName("categoria")[0].childNodes[0].nodeValue
			+ "</td><td>" +
			x[i].getElementsByTagName("fecha")[0].childNodes[0].nodeValue
			+"</td><td>"+
			"<a href='./detalleApuesta.php?evento="+id+"'>"+rival1+"-"+rival2+"</a>"
			+"</td><td>"+
			"<input type='radio' name='apuesta'>"+ratio1+"€ "+rival1+"</input>"+"<br />"
			+empate
			+"<input type='radio' name='apuesta'>"+ratio2+"€ "+rival2+"</input>"+"<br />"
			+ "</td></tr>";
		}
	}

	document.getElementById("tablaResultados").innerHTML = table;
}

function buscarPorNombre() {	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if(this.readyState == 4 && this.status == 200) {
			
			busquedaPorNombre(this);
		}
	};
	var dir = "./apuestas.xml";
	xhttp.open("GET", dir, true);
	xhttp.send();
}

function busquedaPorNombre(xml) {
	
	var xmlDoc = xml.responseXML;
	
	var table =	"<thead>\
					<tr>\
						<th class='col-sm-2'>Categoria</th>\
						<th class='col-sm-2'>Fecha</th>\
						<th class='col-sm-2'>Evento</th>\
						<th class='col-sm-2'>ratio</th>\
					</tr>\
				</thead>";
	
	var x = xmlDoc.getElementsByTagName("apuesta");
	busqueda = document.getElementById("buscador").value;
	if(busqueda.length == 0) {
		busqueda = "@";
	}
	
	for(i=0;i<x.length;i++) {
		rival1 = x[i].getElementsByTagName("rival1")[0].childNodes[0].nodeValue;
		rival2 = x[i].getElementsByTagName("rival2")[0].childNodes[0].nodeValue;
		var re = new RegExp(busqueda, "i");
		if( re.test(rival1) || re.test(rival2)) {
			empate = "";
			if(!x[i].attributes[0].value.localeCompare("empatable")) {
				empate = "<input type='radio' name='apuesta'>"+x[i].getElementsByTagName("empate")[0].childNodes[0].nodeValue+"€ X"+"</input>"+"<br />";
			}
			
			ratio1 = x[i].getElementsByTagName("victoria1")[0].childNodes[0].nodeValue;
			ratio2 = x[i].getElementsByTagName("victoria2")[0].childNodes[0].nodeValue;
			id = x[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
			
			table +="<tr><td>"+
			x[i].getElementsByTagName("categoria")[0].childNodes[0].nodeValue
			+ "</td><td>" +
			x[i].getElementsByTagName("fecha")[0].childNodes[0].nodeValue
			+"</td><td>"+
			"<a href='./detalleApuesta.php?evento="+id+"'>"+rival1+"-"+rival2+"</a>"
			+"</td><td>"+
			"<input type='radio' name='apuesta'>"+ratio1+"€ "+rival1+"</input>"+"<br />"
			+empate
			+"<input type='radio' name='apuesta'>"+ratio2+"€ "+rival2+"</input>"+"<br />"
			+ "</td></tr>";
		}
	}
	document.getElementById("tablaResultados").innerHTML = table;
}