
<?php
	session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Ajuste a la anchura del dispositivo -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title class= "text-capitalize text-justify">WeBstart</title>
	<!--Hojas de estilo.-->
	
	<link rel="stylesheet" type="text/css" href="../../../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../../../css/bootstrap-theme.css">
	<link rel="stylesheet" type="text/css" href="../../../css/style.css">
	<link rel="stylesheet" media="screen" href="bootstrap2.css" />

</head>

<body>
		<!-- header: nombre del sitio, botones de login y registro-->
	<header class="navbar navbar-default navbar-fixed-top">
		<!-- titulo -->
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="index.php">We<i id="brandLetter">B</i>start</a>
			</div>

			<!-- login y registro, cuando colapsa se mostrarán en el botón anterior -->
			<div class="navbar-right">
				<a href="registro.php" class="btn navbar-btn btn-primary ">Registrase</a>
				<a href="login.php" class="btn navbar-btn btn-primary ">Login</a>
			</div>
		</div>
	</header>

	 <div class="container">
        <form class="form-horizontal" method="post" action="validarRegistro.php">
        	<fieldset>
				<legend>
				Complete los siguientes campos:
				</legend>
			</fieldset>


			<div class="form-group">
				<label class="control-label col-sm-4">Nombre: </label>
				<div class="col-sm-4">
					<input class="form-control" id="nombre" type="text" name="nombre" onblur="validarDatos(this.value)" onfocus="limpiar('infoNombre')" value="<?= @$_SESSION['nombre']?>" required >

					<div id="infoNombre">
					<?php if (!empty($_SESSION['nombreInfo'])) { ?>
						<div class="arrow_box"><label style="display: inline;" class="error" for="nombre"> <?= @$_SESSION['nombreInfo']?> </label></div>
					<?php } ?>
					</div>

					<script>
						function validarDatos(str) {
							var xhttp = new XMLHttpRequest();
							xhttp.onreadystatechange = function() {
								if(this.readyState == 4 && this.status == 200) {
									document.getElementById("infoNombre").innerHTML = this.responseText;
								}
							};
							var dir = "./respuestaNombreResgistro.php?q="+str;
							xhttp.open("GET", dir, true);
							xhttp.send();
						}

					</script>				
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">Primer Apellido: </label>
				<div class="col-sm-4">
					<input class="form-control" type="text" name="apellido1" onfocus="limpiar('infoApellido')" value="<?= @$_SESSION['apellido1']?>" required>
					<div id="infoApellido">
						<?php if (!empty($_SESSION['apellido1Info'])) { ?>
							<div class="arrow_box"><label style="display: inline;" class="error" for="apellido1"> <?= @$_SESSION['apellido1Info']?> </label></div>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">Correo Electrónico: </label>
				<div class="col-sm-4">
					<input class="form-control" type="email" name="email" onfocus="limpiar('infoEmail')" value="<?= @$_SESSION['email']?>" required>
					<div id="infoEmail">
						<?php if (!empty($_SESSION['emailInfo'])) { ?>
							<div class="arrow_box"><label style="display: inline;" class="error" for="email"> <?= @$_SESSION['emailInfo']?> </label></div>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">Numero de tarjeta de crédito: </label>
				<div class="col-sm-4">
					<input id="numTarjeta" class="form-control" type="text" name="cuenta" maxlength="16" onkeyup="validarTarjeta(this.value)" onfocus="limpiar('infoCuenta')" required>

					<div id="infoCuenta">
						<?php if (!empty($_SESSION['cuentaInfo'])) { ?>
							<div class="arrow_box"><label style="display: inline;" class="error" for="cuenta"> <?= @$_SESSION['cuentaInfo']?> </label></div>
						<?php } ?>
					</div>

					<script>
						function validarTarjeta(str) {
							if(isNaN(str)) {
								document.getElementById("infoCuenta").innerHTML = "<div class='arrow_box'><label style='display: inline;' class='error' for='cuenta'> Introduzca un n° de tarjeta </label></div>";
							} else {
								document.getElementById("infoCuenta").innerHTML = "";
							}
						}		
					</script>
				</div>
			</div>


			<div class="form-group" id="pwd-container">
				<label class="control-label col-sm-4" for="password">Contraseña: </label>
				<div class="col-sm-4">
					<input id = "password" class="form-control" type="password" name="password" onkeyup="validarContraseña(this.value)" onfocus="limpiar('infoContrasena')" required>
                  	<span class="pwstrength_viewport_progress"></span>
					<br/>
					<div id = "infoContrasena">
						<?php if (!empty($_SESSION['passwordInfo'])) { ?>
							<div class="arrow_box"><label style="display: inline;" class="error" for="password"> <?= @$_SESSION['passwordInfo']?> </label></div>
						<?php } ?>
					</div>					

					<script>
						function validarContraseña(str) {
							if(str.length<8) {
								document.getElementById("infoContrasena").innerHTML = "<div class='arrow_box'><label style='display: inline;' class='error' for='password'> La Contrasena tiene que tener 8 caracteres</label></div>";
								return;
							}
							document.getElementById("infoContrasena").innerHTML = "";
						}		
					</script>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">Repita Contraseña: </label>
				<div class="col-sm-4">
					<input class="form-control" type="password" name="confirm_password" onblur="validarContrasena(this.value)" onfocus="limpiar('infoRepetirContrasena')" required><br/>
					<div id="infoRepetirContrasena">
						<?php if (!empty($_SESSION['confirm_passwordInfo'])) { ?>
							<div class="arrow_box"><label style="display: inline;" class="error" for="confirm_password"> <?= @$_SESSION['confirm_passwordInfo']?> </label></div>
						<?php } ?>
					</div>
				</div>
				<script>
					function validarContrasena(str) {
						if(str != document.getElementById("contrasena").value) {
							document.getElementById("infoRepetirContrasena").innerHTML = "<div class='arrow_box'><label style='display: inline;' class='error' for='confirm_password'> Las contrasenas no coinciden </label></div>";
						} else {
							document.getElementById("infoRepetirContrasena").innerHTML = "";
						}
					}
				</script> 
			</div>

			<script>
				function limpiar(str) {
					document.getElementById(str).innerHTML = "";
				}
			</script>

			<div class="form-group">
				<div class="col-sm-4 col-sm-offset-4">
					<input class="btn btn-success" type="submit" value="OK">
				</div>
			</div>
		</form>
	</div>			

	
	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="../pwstrength.js"></script>
    <script type="text/javascript">
	   jQuery(document).ready(function () {
            "use strict";
            var options = {};

            options.ui = {
                container: "#pwd-container",
                showVerdictsInsideProgressBar: true,
            };
            progressBarExtraCssClasses: "progress-bar-striped active";
            options.common = {
                debug: true,
                minChar: 8,
                usernameField: "#nombre"
            };
            $('#password').pwstrength(options);
        });
	</script>

</body>
</html>
