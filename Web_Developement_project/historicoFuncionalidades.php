<?php
	session_start();
	require_once("printTabla.php");

	function cargarHistorico($alias) {
		$file_xml = 'apuestas.xml';
		$apuestasXml = simplexml_load_file($file_xml);
		$users = dir("./usuarios");
		if(file_exists ("./usuarios/".$alias."/historial.xml")) {
			$historicXml = simplexml_load_file("./usuarios/".$alias."/historial.xml");
			foreach( $historicXml->apuesta as $apuestaUsuario ) {
				$idApuesta = $apuestaUsuario->id;
				$ratioApuesta = $apuestaUsuario->ratio;
				$cantidadApuesta = $apuestaUsuario->cantidad;
				foreach($apuestasXml->apuesta as $apuesta) {
					if((int)$idApuesta  == (int)$apuesta->id) {
						printHistBet($apuesta->categoria,$apuesta->fecha, $apuesta->rival1."-".$apuesta->rival2, (float) $ratioApuesta, (float)$cantidadApuesta);
						break;
					}
				}
			}
		}
		printHistEnd();
	}

	function anadirDineroACuenta($alias) {
		$datoUser=fopen("./usuarios/".$alias."/datos.dat","r+");
		rewind($datoUser);
		for($i=0;$i<5;$i++) {
			fgets($datoUser);
		}
		$t = ftell($datoUser);
		$dinero=fgets($datoUser);
		echo $dinero;
		$dinero = floatval($dinero)+floatval($_REQUEST["anadidoCuenta"]);
		fseek($datoUser,$t);
		fwrite($datoUser,$dinero);
		fclose($datoUser);
		header("Location: historico.php");

	}

	if(isset($_REQUEST['method']) &&$_REQUEST['method'] == 'addMoney') {
		anadirDineroACuenta($_SESSION["nombre"]);
	}
?>