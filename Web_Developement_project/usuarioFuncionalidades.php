<?php
	function imprimirUsuarioPerfil($alias) {
		$datoUser=fopen("./usuarios/".$alias."/datos.dat","r");
		fgets($datoUser);
		fgets($datoUser);
		$apellido =	fgets($datoUser);
		$mail =	fgets($datoUser);
		$nCuenta=fgets($datoUser);
		$dinero = fgets($datoUser);

		$nCuentaIni = substr($nCuenta,0,4);
		$nCuentaFin = substr($nCuenta,11,4);
		fclose($datoUser);
		?>
		<table class="table table-bordered table-striped table-nonfluid">
			<thead>
				<th><?= @$alias." ".$apellido?></th>
			</thead>
			<tbody>
				<tr><td>email: </td><td><?= @$mail?></td></tr>
				<tr><td>N° de Cuenta: </td><td><?= @$nCuentaIni.'****'.$nCuentaFin ?></td></tr>
				<tr><td>Dinero en Cuenta: </td><td><?= $dinero."€"?></td></tr>
			</tbody>
		</table>
	<?php }

	function imprimirBalanceCarrito($alias, $dineroApostado) {
		$datoUser=fopen("./usuarios/".$alias."/datos.dat","r");
		fgets($datoUser);
		fgets($datoUser);
		$apellido =	fgets($datoUser);
		$mail =	fgets($datoUser);
		$nCuenta=fgets($datoUser);
		$dinero = fgets($datoUser);
		fclose($datoUser);
		?>
		<table class="table table-bordered table-striped table-nonfluid">
			<thead>
				<th><?= @$alias." ".$apellido?></th>
			</thead>
			<tbody>
				<tr><td>Dinero en Cuenta: </td><td><?= $dinero."€"?></td></tr>
				<tr><td>Dinero Apostado: </td><td><?= $dineroApostado."€"?></td></tr>
				<tr><td>Balance: </td><td id="balance"><?= ($dinero-$dineroApostado)."€"?></td></tr>
			</tbody>
		</table>
	<?php }

	function dineroDelUsuario($alias) {
		$datoUser=fopen("./usuarios/".$alias."/datos.dat","r");
		fgets($datoUser);
		fgets($datoUser);
		$apellido =	fgets($datoUser);
		$mail =	fgets($datoUser);
		$nCuenta=fgets($datoUser);
		$dinero = fgets($datoUser);
		fclose($datoUser);
		return $dinero;
	}

	function setDineroUsuario($alias, $dinero) {
		$datoUser=fopen("./usuarios/".$alias."/datos.dat","r+");
		rewind($datoUser);
		for($i=0;$i<5;$i++) {
			fgets($datoUser);
		}
		fwrite($datoUser,$dinero);
		fclose($datoUser);
	}
?>