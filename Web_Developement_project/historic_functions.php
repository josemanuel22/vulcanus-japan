<?php
	session_start();
	require_once("search_functions.php");
	//select customerid, optionid, betid, category, winneropt, betdesc from customers natural join clientbets natural join bets where customerid = 3318
	function addBetToHistoric($idEvent, $idBet, $importe, $estado){
		$xml=simplexml_load_file('data/eventos.xml') or die("Error: Cannot create object");

		foreach($xml->children() as $evento) {
			$attrevento = $evento->attributes();

			if ($attrevento['id'] == $idEvent) {
				foreach ($evento->apuestas->children() as $apuesta) {
					$attrapuesta = $apuesta->attributes();
					if ($attrapuesta['id'] == $idBet) {
						$categoria     = $evento->categoria;
						$fecha         = $evento->fecha;
						$nombre        = $evento->nombre;
						$nombreapuesta = $apuesta->nombreapuesta;
						$ratioapuesta  = $apuesta->ratioapuesta;
					}
				}
				break;
			}
		}


		$filename = "usuarios/".$_SESSION['s_user_name']."/historic.xml";
		if (!(file_exists($filename))) {
			touch($filename);
			chmod($filename,0777);
			$xmlhistoric = new SimpleXMLElement('<historic/>');
		} else {
			$xmlhistoric = simplexml_load_file($filename);
		}

		//$datfile = fopen($filename, "aw");

		//<evento>
		//	<fecha>3/10/2015</fecha>
		//	<categoria>Tenis</categoria>
		//	<nombre>Nadal vs Federer</nombre>
		//	<nombreapuesta>Nadal gana el partido</nombreapuesta>
		//	<ratioapuesta>1,1</ratioapuesta>
		//	<importe>10</importe>
		//	<estado>-</estado>
		//</evento>

		$evento = $xmlhistoric->addChild('evento');
		$evento->addChild('categoria', "$categoria");
		$evento->addChild('fecha', "$fecha");
		$evento->addChild('nombre', "$nombre");
		$evento->addChild('nombreapuesta', "$nombreapuesta");
		$evento->addChild('ratioapuesta', "$ratioapuesta");
		$evento->addChild('importe', "$importe");
		$evento->addChild('estado', "$estado");

		$xmlhistoric->asXML($filename);
	}

?>
