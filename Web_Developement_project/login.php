<?php 
	session_start();
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
	
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
	<?php do_header_login(); ?>

	<div class="container">
		<form class="form-horizontal" method="post" action="validarLogin.php">
			<div class="form-group">
				<label class="control-label col-sm-3">Nombre de Usuario: </label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="nombre" value="<?= @$_COOKIE['nombre']?>" required>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3">Contraseñas: </label>
				<div class="col-sm-3">
					<input class="form-control" type="password" name="contrasena" required><br>
					<div id="respuestaLogin">
						<?php if (!empty($_SESSION['infoLogin'])) { ?>
							<div class='arrow_box'><label style='display: inline; class='error' for='respuestaLogin'> <?= @$_SESSION['infoLogin']?> </label></div>
						<?php } ?>
					</div>
				</div>
			</div>




			<div class="form-group">
				<div class="col-sm-3 col-sm-offset-2">
					<input class="btn btn-success" type="submit" value=" OK ">
				</div>
			</div>
		</form>
	</div>

	<?php do_footer(); ?>
	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

</body>
</html>
