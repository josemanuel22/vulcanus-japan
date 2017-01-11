
<?php
	//require_once("carritoFuncionalidades.php");
	//APUESTAS DEL CARRITO

	function printCartIni() {
	?>
		<div class="bets_event">
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-nonfluid table-hover">
					<thead>
						<tr>
							<th class="col-sm-3">Categoria</th>
							<th class="col-sm-3 CartDetalles">Fecha</th>
							<th class="col-sm-3">Apuesta</th>
							<th class="col-sm-2 CartDetalles">Cuota (€)</th>
							<th class="col-sm-1">Importe (€)</th>
						</tr>
					</thead>
					<tbody>
					<form class="form-horizontal" method="post" action="carritoFuncionalidades.php?method=bet">
					
	<?php
	}



	function printCartBet($id, $categoria, $fecha, $evento, $cuota, $importe) {
	?>


		<tr>
			<td><?php echo "$categoria"?></td>
			<td class="CartDetalles"><?php echo "$fecha"?></td>
			<td><?php echo "$evento"?></td>
			<td class="CartDetalles"><?php echo round($cuota,2);?></td>
			<td><input id="importe<?= @$id ?>" class="form-control money" type="number" min="0" name="importe<?= @$id ?>" value="<?= @$importe ?>" required></td>
			<td><input id="<?= @$id ?>" class="btn btn-danger" name="submit" value="Eliminar" onclick= "rmOfCart(this)" ></td>
		</tr>


	<?php
	}

	function printCartNoMatches() {
	?>
		<p>No hay resultados que mostrar</p>
	<?php
	}

	function printCartEnd() {
	?>

					</tbody>
				</table>
				<input class="btn btn-success" type="submit" name="submit" value="Apostar todo"> <br>
				</form>
			</div>
		</div>
	<?php
	}

	function printCartButton() {
	?>
			<!--<div id="betButton">
				<div class="form-group">
					<div class="col-sm-offset-0">
						<input class="btn btn-success" type="submit" name="submit" value="Apostar todo">
					</div>
				</div>
			</div>
		</form> -->
		<br>
		<button id="mostrarDetalles" class="btn btn-info col-sm-offset-0" onclick="mostrarDetallesApuestaCart()"> Detalles</button>
		
		
	<?php
	}

	//Tabla de historico.php
	function printHistIni() {
	?>
		<div class="bets_event">
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-nonfluid table-hover" onclick="mostrarDetallesApuestaHist()"">
					<thead>
						<tr>
							<th class="col-sm-3">Categoria</th>
							<th class="HistDetalles" class="col-sm-3">Fecha</th>
							<th class="col-sm-3">Apuesta</th>
							<th class="HistDetalles" class="col-sm-2">Cuota (€)</th>
							<th class="col-sm-1">Importe (€)</th>
							<th class="col-sm-2">Ganancia (€)</th>
						</tr>
					</thead>
					<tbody>
	<?php
	}
	


	function printHistBet($categoria, $fecha, $evento, $cuota, $importe) {
	?>
		<tr>
			<td><?php echo "$categoria"?></td>
			<td class="HistDetalles" ><?php echo "$fecha"?></td>
			<td><?php echo "$evento"?></td>
			<td class="HistDetalles"><?php echo round($cuota,2);?></td>
			<td><?php echo round($importe,2);?></td>
			<td class="ganancia"></td>
		</tr>


	<?php
	}

	function printHistEnd() {
	?>
					</tbody>
				</table>
			</div>
		</div>
		<button id="mostrarDetalles" class="btn btn-info col-sm-offset-0" onclick="mostrarDetallesApuestaHist()"> Detalles</button> <br />
	<?php
	}
	?>
