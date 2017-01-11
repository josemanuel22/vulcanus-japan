<?php

	session_start();
	require_once("dbAccess/Dbconfig.php");
	require_once("dbAccess/class.user.php");

	$password=$_SESSION['s_user_name']="";

	$_SESSION['s_passwordInfo']=$_SESSION['s_user_nameInfo']="";

	$filename="usuarios";
	$datfile="";

	if (!isset($_REQUEST['submit'])){
		echo "hacked";
	} else{
		try {
			$user = new USER();
			$user->login(cleanInput($_REQUEST['nombre']), cleanInput($_REQUEST['contrasena']));
			$user = null;
		} catch(PDOException $e) {
			echo $e->getMessage();
			$_SESSION['s_passwordInfo'] = "Problema de conexión.";
			header("location:index.php");
		}
	}

	function cleanInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
?>
