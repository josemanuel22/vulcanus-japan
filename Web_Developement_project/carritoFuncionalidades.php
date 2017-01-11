
<?php
	session_start();
	require_once("usuarioFuncionalidades.php");

	function eliminarApuestaDelCarrito($idbet) {
		$n = $idbet;
		$_SESSION["nApuesta"]=$_SESSION["nApuesta"]-1;
		unset($_SESSION["evento$n"]);
		unset($_SESSION["ratio$n"]);
		unset($_SESSION["cantidad$n"]);
	}

	function apostarCarrito($alias) {
		$users = dir("./usuarios");
		$cantidadTotalApostada = 0;
		while( false!== ($entry = $users->read()) ) {
			if($alias == $entry) {
				if(!file_exists ("./usuarios/".$alias."/historial.xml")) {
					$xml = new DomDocument('1.0', 'UTF-8');
					$root = $xml->createElement('historial');
					$root = $xml->appendChild($root);
					for($i=0;$i<$_SESSION["nApuesta"];$i++) {
						$apuesta =$xml->createElement('apuesta');
						$apuesta =$root->appendChild($apuesta);
						$idApuesta=$xml->createElement('id',$_SESSION["evento$i"]);
						$idApuesta =$apuesta->appendChild($idApuesta);
						$ratio=$xml->createElement('ratio',$_SESSION["ratio$i"]);
						$ratio =$apuesta->appendChild($ratio);
						$cantidad = $xml->createElement('cantidad',$_REQUEST["importe$i"]);
						$cantidad = $apuesta->appendChild($cantidad);
						$cantidadTotalApostada+=floatval($_REQUEST["importe$i"]);
					}
					if($cantidadTotalApostada > dineroDelUsuario($alias)) {
						return "dineroInsuficiente";
					} else {
						setDineroUsuario($alias,floatval(dineroDelUsuario($alias))-floatval($cantidadTotalApostada));
					}
					$xml->formatOutput = true;
					$strings_xml = $xml->saveXML(); 
					$xml->save("./usuarios/".$alias."/historial.xml"); 
				} else {
					$xml = new DOMDocument();
					$xml->load("./usuarios/".$alias."/historial.xml");
					$root = $xml->documentElement;
					for($i=0;$i<$_SESSION["nApuesta"];$i++) {
						$apuesta =$xml->createElement('apuesta');
						$apuesta =$root->appendChild($apuesta);
						$idApuesta=$xml->createElement('id',$_SESSION["evento$i"]);
						$idApuesta =$apuesta->appendChild($idApuesta);
						$ratio=$xml->createElement('ratio',$_SESSION["ratio$i"]);
						$ratio =$apuesta->appendChild($ratio);
						$cantidad = $xml->createElement('cantidad',$_REQUEST["importe$i"]);
						$cantidad = $apuesta->appendChild($cantidad);
						$cantidadTotalApostada+=floatval($_REQUEST["importe$i"]);
					}
					if($cantidadTotalApostada > dineroDelUsuario($alias)) {
						return "dineroInsuficiente";
					} else {
						setDineroUsuario($alias, floatval(dineroDelUsuario($alias))-floatval($cantidadTotalApostada));
					}
					$xml->formatOutput = true;
					$strings_xml = $xml->saveXML();
					$xml->save("./usuarios/".$alias."/historial.xml");  
				}
				break;
			}
		}
	}

	if($_REQUEST['method'] == 'rm') {
		eliminarApuestaDelCarrito($_REQUEST["id"]);
	} else if($_REQUEST['method'] == 'bet'){
		if(isset($_SESSION["nombre"])) {
			$err = apostarCarrito($_SESSION["nombre"]);
			if($err == "dineroInsuficiente") {
				header('Location: ./carrito.php?info=dineroInsuficiente');
				return;
			}
			while($_SESSION["nApuesta"]>=0) { //Eliminamos todas las apuestas guardadas en sesion. Ya estan en la base de datos.
				eliminarApuestaDelCarrito($_SESSION["nApuesta"]);
			}
			unset($_SESSION["nApuesta"]);
			header('Location: ./index.php');
		} else { 
			header('Location: ./carrito.php?info=noLogeago');
		}
	}
?>