<?php
	require_once("historicoFuncionalidades.php");
	require_once("usuarioFuncionalidades.php");
	require_once("./miscelaneos/cabeceras.php");
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Ajuste a la anchura del dispositivo -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title class= "text-capitalize text-justify">WeBstart</title>
	<!--Hojas de estilo.-->
	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>

	<?php  do_header_historico();?>

	<div class="row">
		<div class="col-sm-7 col-sm-offset-0">
			<?php 
			printHistIni();
			cargarHistorico( $_SESSION['nombre'] );
			?>
			<br />
		</div>
			<div class="col-sm-4">
			<?php imprimirUsuarioPerfil($_SESSION['nombre']) ?>
			<br />
				<form class="form-horizontal" method="post" action="historicoFuncionalidades.php?method=addMoney">
					<div class="form-group">
						<label class="control-label col-sm-4">Añadir dinero a la cuenta (€): </label>
						<div class="col-sm-3">
							<input class="form-control" type="number" min="0" name="anadidoCuenta"> 
						</div>
						<div class="col-sm-3">
							<input class="btn btn-success" type="submit" value="Cargar la Cuenta!">
						</div>
					</div>
				</form>
			</div>
	</div>

	<script>
	function mostrarDetallesApuestaHist() {
		$(document).ready(function() {
		     $(".HistDetalles").toggle();
		});
	}
	</script>
	

	<?php do_footer();?>
	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

</body>
</html>
