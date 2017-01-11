<?php
	session_start();
	require_once 'dbAccess/Dbconfig.php';
	require_once 'dbAccess/class.user.php';

	$_SESSION['nombre']=$_SESSION['apellido1']=$_SESSION['apellido2']=$_SESSION['sexo']=$_SESSION['pais']=$_SESSION['ciudad']=$_SESSION['cp']=$_SESSION['direccion']=$_SESSION['email']=$cuenta="";
	$password=$confirm_password=$_SESSION['user_name']="";

	$_SESSION['nombreInfo']=$_SESSION['apellido1Info']=$_SESSION['apellido2']=$_SESSION['sexoInfo']=$_SESSION['paisInfo']=$_SESSION['ciudadInfo']=$_SESSION['cpInfo']=$_SESSION['direccionInfo']=$_SESSION['emailInfo']=$_SESSION['cuentaInfo']="";
	$_SESSION['passwordInfo']=$_SESSION['confirm_passwordInfo']=$_SESSION['user_nameInfo']="";

	$filename="usuarios";
	$datfile="";
	//nombre apellido1 apellido2 sexo pais ciudad cp direccion mail cuenta
	//user_name password confirm_password

	if(isset($_REQUEST['submit']) != true) { //informacion personal
		echo "hacked";
		header("Location:regUser.php");
	}

	$_SESSION['nombre'] = cleanInput($_REQUEST["nombre"]);
	if (empty($_SESSION['nombre'])) {
		$_SESSION['nombreInfo'] = "El nombre no puede ser vacío";
	} else if (!preg_match("/^[a-zA-Zñ]+$/",$_SESSION['nombre'])) {
		$_SESSION['nombreInfo'] = "El nombre solamente puede tener letras";
	}

	$_SESSION['apellido1'] = cleanInput($_REQUEST["apellido1"]);
	if (empty($_SESSION['apellido1'])) {
		$_SESSION['apellido1Info'] = "El apellido no puede ser vacío";
	} else if (!preg_match("/^[a-zA-Zñ]+$/",$_SESSION['apellido1'])) {
		$_SESSION['apellido1Info'] = "El apellido solamente puede tener letras";
	}

	$_SESSION['apellido2'] = cleanInput($_REQUEST["apellido2"]);
	if (empty($_SESSION['apellido2'])) {
		$_SESSION['apellido2Info'] = "El apellido no puede ser vacío";
	} else if (!preg_match("/^[a-zA-Zñ]+$/",$_SESSION['apellido2'])) {
		$_SESSION['apellido2Info'] = "El apellido solamente puede tener letras";
	}

	$_SESSION['sexo'] = cleanInput($_REQUEST["sexo"]);
	if (empty($_SESSION['sexo'])) {
		$_SESSION['sexoInfo'] = "Debe seleccionar un sexo";
	}

	$_SESSION['pais'] = cleanInput($_REQUEST["pais"]);
	if (empty($_SESSION['pais'])) {
		$_SESSION['paisInfo'] = "El país no puede ser vacío";
	} else if (!preg_match("/^[a-zA-Zñ]+$/",$_SESSION['pais'])) {
		$_SESSION['paisInfo'] = "El país solamente puede tener letras";
	}

	$_SESSION['ciudad'] = cleanInput($_REQUEST["ciudad"]);
	if (empty($_SESSION['ciudad'])) {
		$_SESSION['ciudadInfo'] = "La ciudad no puede ser vacía";
	} else if (!preg_match("/^[a-zA-Zñ]+$/",$_SESSION['ciudad'])) {
		$_SESSION['ciudadInfo'] = "La ciudad solamente puede tener letras";
	}

	$_SESSION['cp'] = cleanInput($_REQUEST["cp"]);
	if (empty($_SESSION['cp'])) {
		$_SESSION['cpInfo'] = "El código postal no puede ser vacío";
	} else if (!preg_match("/^[a-zA-Z0-9]+$/",$_SESSION['cp'])) {
		$_SESSION['cpInfo'] = "El código postal solamente tiene letras y números";
	}

	$_SESSION['direccion'] = cleanInput($_REQUEST["direccion"]);
	if (empty($_SESSION['direccion'])) {
		$_SESSION['direccionInfo'] = "La dirección no puede ser vacía";
	} else if (!preg_match("/^[a-zA-Zñ,.0-9 º]+$/",$_SESSION['direccion'])) {
		$_SESSION['direccionInfo'] = "Dirección no válida (caracteres extraños)";
	}

	$_SESSION['email'] = cleanInput($_REQUEST["email"]);
	if (!filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL)) {
		$_SESSION['emailInfo'] = "El mail debe ser de la forma: ejemplo1@ejemplo2.dominio";
	}

	$cuenta = cleanInput($_REQUEST["cuenta"]);
	if (empty($cuenta)) {
		$_SESSION['cuentaInfo'] = "La tarjeta no puede ser vacía";
	} else if (!preg_match("/^[0-9]{16}$/",$cuenta)) {
		$_SESSION['cuentaInfo'] = "Tarjeta de crédito incorrecta";
	}

		//usuario y password
	$_SESSION['user_name'] = cleanInput($_REQUEST["user_name"]);
	if (empty($_SESSION['user_name'])) {
		$_SESSION['user_nameInfo'] = "El nombre de usuario no puede ser vacío";
	} else if (!preg_match("/^[a-zA-Z0-9ñ]+$/",$_SESSION['user_name'])) {
		$_SESSION['user_nameInfo'] = "El nombre de usuario solamente puede tener letras y números";
	}
	$password = $_REQUEST["password"];
	$confirm_password = $_REQUEST["confirm_password"];
	if (empty($password)) {
		$_SESSION['passwordInfo'] = "La contraseña no puede ser vacía";
	}

	if (empty($confirm_password)) {
		$_SESSION['confirm_passwordInfo'] = "La contraseña no puede ser vacía";
	}

	if (strlen($password) < 8) {
		$_SESSION['passwordInfo'] = "La contraseña tiene que tener al menos 8 caracteres";
	}

	if (strlen($confirm_password) < 8) {
		$_SESSION['confirm_passwordInfo'] = "La contraseña tiene que tener al menos 8 caracteres";
	}

	if ($password != $confirm_password) {
		$_SESSION['confirm_passwordInfo'] = "Las contraseñas no coinciden";
	}

	//si todas las comprobaciones iniciales han ido bien, intentamos crear el usuario
	if ($_SESSION['nombreInfo'] == "" &&
	  $_SESSION['apellido1Info'] == "" &&
	  $_SESSION['apellido2Info'] == "" &&
	  $_SESSION['sexoInfo'] == "" &&
	  $_SESSION['paisInfo'] == "" &&
	  $_SESSION['ciudadInfo'] == "" &&
	  $_SESSION['cpInfo'] == "" &&
	  $_SESSION['direccionInfo'] == "" &&
	  $_SESSION['emailInfo'] == "" &&
	  $_SESSION['cuentaInfo'] == "" &&
	  $_SESSION['passwordInfo'] == "" &&
	  $_SESSION['confirm_passwordInfo'] == "" &&
	  $_SESSION['user_nameInfo'] == "") {
		$user_reg = new USER();
		$user_row = $user_reg->exist($_SESSION['email']);
		if ($user_row != null) {
			$_SESSION['emailInfo'] = "Lo sentimos, el email {$_SESSION['email']} ya está en uso";
			header("Location:regUser.php");
		} else {
			$user_row = null;
			try {
				$user_reg->register($_SESSION['nombre'],
					                $_SESSION['apellido1'],
					                $_SESSION['apellido2'],
					                $_SESSION['sexo'],
					                $_SESSION['pais'],
					                $_SESSION['ciudad'],
					                $_SESSION['cp'],
					                $_SESSION['direccion'],
					                $_SESSION['email'],
					                $cuenta,
					                $_SESSION['user_name'],
					                $password);
				header("Location:index.php");
			} catch(PDOException $ex) {
				echo $ex->getMessage();
				header("Location:registro.php");
			}
		}
	} else {
		header("Location:regUser.php");
	}

	function cleanInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
?>
