
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
	<link rel="stylesheet" media="screen" href="css2/examples/bootstrap3/bootstrap3.css" />
	<script src="validarRegistro.js"></script>
</head>

<body>
		<!-- header: nombre del sitio, botones de login y registro-->
	<?php do_header_registro(); ?>

	 <div class="container">
        <form class="form-horizontal" id="register-form" method="post" action="validarRegistro.php" onsubmit="return validacion(this)">
        	<fieldset>
				<legend>
				Complete los siguientes campos:
				</legend>
			</fieldset>


			<div class="form-group">
				<label class="control-label col-sm-4" for="nombre">Nombre: </label>
				<div class="col-sm-4">
					<input class="form-control" id="nombre" type="text" name="nombre" onblur="validarNombre(this.value)" onfocus="limpiar('infoNombre')" value="<?= @$_SESSION['nombre']?>" required >

					<div id="infoNombre">
					<?php if (!empty($_SESSION['nombreInfo'])) { ?>
						<div class="arrow_box"><label style="display: inline;" class="error" for="nombre"> <?= @$_SESSION['nombreInfo']?> </label></div>
					<?php } ?>
					</div>	
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">Primer Apellido: </label>
				<div class="col-sm-4">
					<input id="apellido1" class="form-control" type="text" name="apellido1" onfocus="limpiar('infoApellido1')" value="<?= @$_SESSION['apellido1']?>" required>
					<div id="infoApellido1">
						<?php if (!empty($_SESSION['apellido1Info'])) { ?>
							<div class="arrow_box"><label style="display: inline;" class="error" for="apellido1"> <?= @$_SESSION['apellido1Info']?> </label></div>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">Segundo Apellido: </label>
				<div class="col-sm-4">
					<input id="apellido2" class="form-control" type="text" name="apellido2" onfocus="limpiar('infoApellido2')" value="<?= @$_SESSION['apellido2']?>" required>
					<div id="infoApellido2">
						<?php if (!empty($_SESSION['apellido2Info'])) { ?>
							<div class="arrow_box"><label style="display: inline;" class="error" for="apellido2"> <?= @$_SESSION['apellido2Info']?> </label></div>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="row">
					<label class="control-label col-sm-3" for="sexo">Sexo: </label>
					<div class="col-sm-offset-3">
						<div class="radio-inline">
							<div class="controls col-sm-3">
								<div class="col-sm-3">
									<label class="radio">
										<input type="radio" name="sexo" value="M" checked>Hombre
									</label>
								</div>
								<div class="col-sm-3">
									<label class="radio">
										<input type="radio" name="sexo" value="F">Mujer
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3" for="pais">País: </label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="pais" value="<?= @$_SESSION['pais']?>">
					<?php if (!empty($_SESSION['paisInfo'])) { ?>
						<div class="arrow_box"><label style="display: inline;" class="error" for="pais"> <?= @$_SESSION['paisInfo']?> </label></div>
					<?php } ?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3" for="ciudad">Ciudad: </label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="ciudad" value="<?= @$_SESSION['ciudad']?>">
					<?php if (!empty($_SESSION['ciudadInfo'])) { ?>
						<div class="arrow_box"><label style="display: inline;" class="error" for="ciudad"> <?= @$_SESSION['ciudadInfo']?> </label></div>
					<?php } ?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3" for="cp">Código Postal: </label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="cp" value="<?= @$_SESSION['cp']?>">
					<?php if (!empty($_SESSION['cpInfo'])) { ?>
						<div class="arrow_box"><label style="display: inline;" class="error" for="cp"> <?= @$_SESSION['cpInfo']?> </label></div>
					<?php } ?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3" for="direccion">Dirección: </label>
				<div class="col-sm-3">
					<input class="form-control" type="text" name="direccion" value="<?= @$_SESSION['direccion']?>">
					<?php if (!empty($_SESSION['direccionInfo'])) { ?>
						<div class="arrow_box"><label style="display: inline;" class="error" for="direccion"> <?= @$_SESSION['direccionInfo']?> </label></div>
					<?php } ?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">Correo Electrónico: </label>
				<div class="col-sm-4">
					<input id = "email" class="form-control" type="email" name="email" onfocus="limpiar('infoEmail')" value="<?= @$_SESSION['email']?>" required>
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
				<label class="control-label col-sm-4" for="confirm_password">Repita Contraseña: </label>
				<div class="col-sm-4">
					<input id="confirm_password"class="form-control" type="password" name="confirm_password" onblur="validarContrasena(this.value)" onfocus="limpiar('infoRepetirContrasena')" required><br/>
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
					<input class="btn btn-success" type="submit" name="submit" value="OK">
				</div>
			</div>
		</form>
	</div>			

	<?php do_footer();?>
	
	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="./css2/examples/pwstrength.js"></script>
    <script type="text/javascript">
	   jQuery(document).ready(function () {
            "use strict";
	        var options = {};
	        options.ui = {
	            container: "#pwd-container",
	            showVerdictsInsideProgressBar: true,
	            viewports: {
	        		progress: ".pwstrength_viewport_progress"
	   			},
	    		progressBarExtraCssClasses: "progress-bar-striped active"
	        };
	        
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
