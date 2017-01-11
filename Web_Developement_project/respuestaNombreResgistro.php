
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
<link rel="stylesheet" type="text/css" href="css/style.css">

<?php
	$alias = $_REQUEST["q"];
	$users = dir("./usuarios");
	$message = "";
	while( false!== ($entry = $users->read()) ) {
		if($alias == $entry) {
			$message = "<div class='arrow_box'><label style='display: inline;' class='error' for='nombre'> Lo sentimos ese nombre ya esta cogido </label></div>";
			break;
		}
	}
	echo $message;
?>

