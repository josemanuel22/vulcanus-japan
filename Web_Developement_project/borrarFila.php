<?php		
	$n = $_REQUEST['id'];
	$_SESSION["nApuesta"]=$_SESSION["nApuesta"]-1;
	unset($_SESSION["evento$n"]);
	unset($_SESSION["ratio$n"]);
	unset($_SESSION["cantidad$n"]);
?>