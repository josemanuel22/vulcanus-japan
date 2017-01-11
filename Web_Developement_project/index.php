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
	
	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css"> 
	<link rel="stylesheet" type="text/css" href="css/font-awesome.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body onLoad="setInterval('banner()',3000);">
	<!-- header: nombre del sitio, botones de login y registro-->

	<?php do_header_index(); ?>

	<!-- Menu Lateral -->
	<div class="row">
		<?php do_lateral_menu(); ?>
		<div class="col-sm-8">
			<div class="container">
				<div class="col-sm-offset-1">
					<?php do_carrousel(); ?>
					<br>
					<div class="row">
						<div class="col-sm-6">
							<div class="input-group">
								<div class="input-group-btn search-panel">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										<span>Filtrar</span> <span class="caret"></span>
									</button>
									<ul class="dropdown-menu" role="menu">
										<li><input type="radio" name="filtroCategoria" onclick="recogerSelecionado()" value = "Football">Futbol</li>
										<li><input type="radio" name="filtroCategoria" onclick="recogerSelecionado()" value = "Tenis">Tennis</li>
										<li><input type="radio" name="filtroCategoria" onclick="recogerSelecionado()" value = "Baloncesto">Baloncesto</li>
										<li class="divider"></li>
										<li><input type="radio" name="ganancia" onclick="recogerSelecionado()" value=5> 5€ X 1€> </li>
										<li><input type="radio" name="ganancia" onclick="recogerSelecionado()" value=4> 3€ X 1€ > </li>
										<li><input type="radio" name="ganancia" onclick="recogerSelecionado()" value=2> 2€ X 1€> </li>
										<li><input type="radio" name="ganancia" onclick="recogerSelecionado()" value=1.5> 1.5€ X 1€> </li>
									</ul>
								</div>
								<input id="buscador" class="form-control" type="text" placeholder="Apuesta" oninput="buscarPorNombre()">
								<span class="input-group-btn">
									<button class="btn btn-default" type="button" onclick="buscarPorNombre()"><span class="glyphicon glyphicon-search"></span></button>
								</span>
							</div>
						</div>
					</div>
					<br/>
					<div class="row">
						<div class="col-sm-8 col-sm-offset-0">
							<div class="table-responsive">
								<table id="tablaResultados" class="table table-bordered table-striped table-nonfluid table-hover">
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

				

	<?php do_footer(); ?> 
																									
	
	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/busquedasYfiltros.js"></script>

</body>
</html>
