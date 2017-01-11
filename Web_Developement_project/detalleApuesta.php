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
	<link rel="stylesheet" type="text/css" href="css/style.css">
	
</head>

<body>
	<!-- header: nombre del sitio, botones de login y registro-->
	<?php do_header_detalleApuesta();?>

	<!--Menu Lateral. Categorias y SubCategorias. -->
	<div class="row">
		<div class="col-sm-2">
			<ul class="nav nav-pills nav-stacked">
				<li class="dropdown"> <a class="dropdown-toggle" data-toggle="dropdown" href="#">Football</a>
    				<ul class="dropdown-menu">
			      		<li><a href="#">Ligua BBVA</a></li>
			      		<li><a href="#">Ligua Adelante</a></li>
			      		<li><a href="#">Premier League</a></li>
			    	</ul></li>
				<li><a href="#">Tenis</a></li>
				<li><a href="#">Béisbol</a></li>
				<li class="dropdown"> <a class="dropdown-toggle" data-toggle="dropdown" href="#">Baloncesto</a>
    				<ul class="dropdown-menu">
			      		<li><a href="#">NBA</a></li>
			      		<li><a href="#">Ligua Endesa</a></li>
			    	</ul></li>
				<li><a href="#">Balonmano</a></li>
				<li><a href="#">Ciclismo</a></li>
				<li><a href="#">Fórmula 1</a></li>
				<li><a href="#">Rugby</a></li>
			</ul>
		</div>

		<!-- Tabla de apuestas -->

		<?php
			$file_xml = 'apuestas.xml';
			$idApuesta = $_REQUEST['evento'];
			$xml = simplexml_load_file($file_xml);
			foreach($xml->apuesta as $apuesta) {
				if($idApuesta  == $apuesta->id) { ?>
					<div class="col-sm-8">
						<table class="table table-responsive table-hover table-bordered table-nonfluid">
							<thead>
								<tr>
									<th class="col-sm-1">Deporte</th>
									<th class="col-sm-1">Fecha</th>
									<th class="col-sm-3">Evento</th>
									<th class="col-sm-3">ratio</th>
									<th class="col-sm-1">Cantidad Apostada (€)</th>
									<th class="col-sm-1"></th>			
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><?= @$apuesta->categoria ?></td>
									<td class="danger"><?= @$apuesta->fecha ?></td>
									<td><?= @$apuesta->rival1."-".$apuesta->rival2 ?></td>
									<td>
										<form method="post" action="carrito.php?id=<?= @$idApuesta?>">
											<input type="radio" name="bet1" value="<?= @$apuesta->ratio->victoria1?>" required><?= @$apuesta->rival1." ".$apuesta->ratio->victoria1."€"?><br/>
											<?php if($apuesta->attributes()[0]=="empatable") { ?>
											<input type="radio" name="bet1" value="<?= @$apuesta->ratio->empate?>"> <?= @"X ".$apuesta->ratio->empate."€"?> <br/>
											<?php } ?>
											<input type="radio" name="bet1" value="<?= @$apuesta->ratio->victoria2?>"><?= @$apuesta->rival2." ".$apuesta->ratio->victoria2."€"?><br/>
									</td>
									<td>
										<div class="form-group">
											<div class="col-sm-15 col-sm-offset-4">
												<input class="form-control" type="number" min="0" name="cantidad" required>
											</div>
										</div>
									</td>
									<td>
										<div class="form-group">
											<div class="col-sm-4 col-sm-offset-4">
												<input class="btn btn-success" type="submit" value="Listo">
											</div>
										</div>
									</td>
									</form>
								</tr>
							</tbody>
						</table>
					</div>
				</div>	
	<?php			}
			} ?>
	

	<?php do_footer(); ?>
	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>

