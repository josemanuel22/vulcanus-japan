<?php
	session_start();
	require_once("printTabla.php");
	require_once("usuarioFuncionalidades.php");
	require_once("./miscelaneos/cabeceras.php");


	if(isset($_REQUEST["cantidad"]) && isset($_REQUEST["id"]) &&  isset($_REQUEST["bet1"])) {

		if(!isset($_SESSION["nApuesta"])) {
			$_SESSION["nApuesta"] = 0;
		}

		$already = false;
		for($i=0;$i<$_SESSION["nApuesta"];$i++) { /*Si la apuesta ya ha sido selecionada aumentamos simplemente la cantidad apostada.*/
			if($_SESSION["evento$i"]==$_REQUEST["id"] && $_SESSION["ratio$i"]==floatval($_REQUEST["bet1"])) {
				echo ($_SESSION["evento$i"]==$_REQUEST["id"]);
				$_SESSION["cantidad$i"] = $_REQUEST["cantidad"]+$_SESSION["cantidad$i"];
				$already = true;
				header("Location: index.php");
			}
		}

		if(!$already) {
			$n = $_SESSION["nApuesta"];
			$_SESSION["cantidad$n"] = $_REQUEST["cantidad"];
			$_SESSION["evento$n"] = $_REQUEST["id"];
			$_SESSION["ratio$n"] = floatval($_REQUEST["bet1"]);
			$_SESSION["nApuesta"]+=1;
			header("Location: index.php");
		}
	}
?>

<html>
	<meta charset="utf-8">
	<!-- Ajuste a la anchura del dispositivo -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title class= "text-capitalize text-justify">WeBstart</title>
	<!--Hojas de estilo.-->
	
	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">

<body>
	
	<?php do_header_carrito(); ?>

	<div class="row">
		<div class="col-sm-8 col-sm-offset-1">
			<?php
				$dineroApostado = 0;
				if(isset($_SESSION['nApuesta'])) {
					printCartIni();
					$file_xml = 'apuestas.xml';
					$xml = simplexml_load_file($file_xml);
					for( $i=0; $i<$_SESSION["nApuesta"]; $i++) {
						$idApuesta = $_SESSION["evento$i"];
						foreach($xml->apuesta as $apuesta) {
							if($idApuesta  == $apuesta->id) {
								printCartBet($i, $apuesta->categoria,$apuesta->fecha, $apuesta->rival1."-".$apuesta->rival2, $_SESSION["ratio$i"], $_SESSION["cantidad$i"]);
								$dineroApostado+= $_SESSION["cantidad$i"];
							}
						} 
					}
				printCartEnd();
				printCartButton();
			}?>				
		</div>
		<div class="col-sm-2">
		<?php if(isset($_SESSION["nombre"])) {
			imprimirBalanceCarrito($_SESSION["nombre"], $dineroApostado);
		} ?>
		</div>
	</div>


	<?php if(isset($_REQUEST["info"]) && $_REQUEST["info"]=="noLogeago" ) { ?>
		<script>
			alert("Tiene que estar usted logeado");
		</script>	
	<?php } ?>
	<?php if(isset($_REQUEST["info"]) && $_REQUEST["info"]=="dineroInsuficiente" ) { ?>
		<script>
			alert("No tiene dinero");
		</script>	
	<?php } ?>


	<script>
	function rmOfCart(elem) {
		var xhttp = new XMLHttpRequest();
		table     = $(elem).closest('table');
		idevent   = table.attr("id");
		idbet     = $(elem).attr("id");
		
		xhttp.onreadystatechange = function () {
			if (xhttp.readyState == 4 && xhttp.status == 200) {
				if ($(elem).closest("tr").siblings().length == 0) {
					$(elem).closest("div[class=bets_event]").remove();
					$("#betButton").remove();
					$("#mostrarDetalles").remove();
				} else {
					$(elem).closest("tr").remove();
				}
			}
		}

		alert("Apuesta eliminada del carrito");
		dir="carritoFuncionalidades.php?method=rm&id=".concat(idbet);
		xhttp.open("GET", dir, true);
		xhttp.send();
	}


	function mostrarDetallesApuestaCart() {
		$(document).ready(function() {
		     $(".CartDetalles").toggle();
		});
	}
	</script>

	<?php do_footer(); ?>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

</body>
</html>


		


