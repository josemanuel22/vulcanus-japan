<?php
	session_start();
	require_once("search_functions.php");
	require_once("historic_functions.php");
	require_once("dbAccess/class.user.php");
	require_once("dbAccess/Dbconfig.php");

	function init_conn() {
		$database = new Database();
		$db = $database->dbConnection();
		return $db;
	}

	// FUNCIONALIDAD CARRITO

	/* Anadir al carrito */
	function addBetToShoppingCart(&$shoppingCart, $idEvent, $idBet, $betAmount){
		$flag = 0;
		// caso de añadir al carrito de la base de datos
		if($_SESSION['user_id'] != "" && $_SESSION['user_id'] != null) {
			$conn = init_conn();
			$user = new USER();
			// Buscamos carrito disponible
			$stmt = $user->search_shoppingCartDB($_SESSION['user_id']);

			//Si no hay carrito cerrado, abrimos uno
			if ($stmt->rowCount() < 1) {
				$user->create_shoppingCartDB($_SESSION['user_id']);
				
				//Buscamos carrito disponible de nuevo
				$stmt->execute();
			}
			// En este punto ya tenemos carrito
			$cart=$stmt->fetch(PDO::FETCH_ASSOC);
			$orderid = $cart['orderid'];


			// Buscamos apuesta a insertar
			$stmt = $conn->prepare("SELECT * FROM clientbets where betid = :betid and optionid = :optionid and customerid = :customerid and orderid=:orderid");
			$stmt->bindParam(':customerid', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->bindParam(':betid', $idEvent, PDO::PARAM_INT);
			$stmt->bindParam(':optionid', $idBet, PDO::PARAM_INT);
			$stmt->bindParam(':orderid', $orderid, PDO::PARAM_INT);
			$stmt->execute();

			if ($stmt->rowCount() == 0) {
				// Si no está en el carrito, la añadimos con esa cantidad
				$stmt2 = $conn->prepare("INSERT INTO clientbets
					(customerid, optionid, bet, ratio, outcome, betid, orderid, clientbetid, confirmed) VALUES
					(:customerid, :optionid, :bet, :ratio, 0, :betid, :orderid, DEFAULT, false)");
				
				$stmt3 = $conn->prepare("SELECT ratio FROM bets NATURAL JOIN optionbet WHERE betid = :betid AND optionid = :optionid");

				$stmt3->bindParam(':betid', $idEvent, PDO::PARAM_INT);
				$stmt3->bindParam(':optionid', $idBet, PDO::PARAM_INT);
				$stmt3->execute();

				$stmt2->bindParam(':customerid', $_SESSION['user_id'], PDO::PARAM_INT);
				$stmt2->bindParam(':optionid', $idBet, PDO::PARAM_STR);
				$stmt2->bindParam(':bet', $betAmount, PDO::PARAM_STR);
				$stmt2->bindParam(':betid', $idEvent, PDO::PARAM_INT);
				$stmt2->bindParam(':orderid', $cart['orderid'], PDO::PARAM_INT);

				$query_ratio = $stmt3->fetch(PDO::FETCH_ASSOC);
				$stmt2->bindParam(':ratio', $query_ratio['ratio'], PDO::PARAM_STR);
				$stmt2->execute();

				$_SESSION['cart']++;
				return 1;
			} else {
				// Si está en el carrito, le sumamos la cantidad
				$stmt = $conn->prepare("UPDATE clientbets
					SET bet=bet+:higher_bet
					WHERE betid = :betid and optionid = :optionid and customerid = :customerid and orderid = :orderid");
				$stmt->bindParam(':higher_bet', $betAmount, PDO::PARAM_INT);
				$stmt->bindParam(':customerid', $_SESSION['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':betid', $idEvent, PDO::PARAM_INT);
				$stmt->bindParam(':optionid', $idBet, PDO::PARAM_INT);
				$stmt->bindParam(':orderid', $orderid, PDO::PARAM_INT);

				$stmt->execute();
				return 0;
			}
		} else {
			// Caso de carrito en memoria
			if ($shoppingCart['idEvents'] == "" || $shoppingCart['idEvents'] == null) {
				$shoppingCart['idEvents'] = array();
				$shoppingCart['idBets'] = array();
			}
			if (!in_array($idEvent, $shoppingCart['idEvents'])) {
				array_push($shoppingCart['idEvents'], $idEvent);

				$index = array_search($idEvent, $shoppingCart['idEvents']);
				if ($shoppingCart['idBets'][$index] == "") {
					$shoppingCart['idBets'][$index] = array();
					array_push($shoppingCart['idBets'][$index], array($idBet, $betAmount));
				} else {
					array_push($shoppingCart['idBets'][$index], array($idBet, $betAmount));
				}

				$_SESSION['cart']++;
				return 1;
			}
			else {
				$index = array_search($idEvent, $shoppingCart['idEvents']);
				foreach($shoppingCart['idBets'][$index] as &$dupla){
					if ($dupla[0] == $idBet) {
						$dupla[1] += $betAmount;
						$flag = 1;
						break;
					}
				}

				if ($flag == 0) {
					array_push($shoppingCart['idBets'][$index], array($idBet, $betAmount));
					$_SESSION['cart']++;
					return 1;
				}
				return 0;
			}
		}
	}

	/* Quitar del carrito */
	function removeBetFromShoppingCart(&$shoppingCart, $idEvent, $idBet, $action){
		// caso de eliminar del carrito de la base de datos
		if($_SESSION['user_id'] != "" && $_SESSION['user_id'] != null) {
			$conn = init_conn();
			// Buscamos carrito disponible, lo tenemos en variable de sesion
			
			// Caso en el que eliminemos por pulsar eliminar
			$stmt = $conn->prepare("SELECT * FROM clientbets WHERE orderid = :orderid");
			$stmt->bindParam(':orderid', $_SESSION['cart_id'], PDO::PARAM_INT);
			$stmt->execute();
			//eliminamos la ultima apuesta del carrito
			$stmt3 = $conn->prepare("DELETE FROM clientbets
				WHERE betid = :betid and optionid = :optionid and orderid = :orderid");
			$stmt3->bindParam(':orderid', $_SESSION['cart_id'], PDO::PARAM_INT);
			$stmt3->bindParam(':betid', $idEvent, PDO::PARAM_INT);
			$stmt3->bindParam(':optionid', $idBet, PDO::PARAM_INT);
			$stmt3->execute();
			
		} else {
			// Caso de eliminar del carrito desde sesion
			$index = array_search($idEvent, $shoppingCart['idEvents']);
			//para saber el incide de la apuesta entre las apuestas que haya para ese evento
			for ($x = 0; ; $x++) {
	    		if ($shoppingCart['idBets'][$index][$x][0] == $idBet) break;
			}
			//contamos el numero de puestas asociadas al evento
			$i = 0;
			foreach($shoppingCart['idBets'][$index] as &$dupla){
				$i++;
			}
			//si la apueta que se va a borrar es la unica para ese evento, lo borramos tambien
			if ($i == 1) {
				array_splice($shoppingCart['idEvents'], $index, 1);
				array_splice($shoppingCart['idBets'][$index], $x, 1);
				array_splice($shoppingCart['idBets'], $index, 1);
			} else {
				array_splice($shoppingCart['idBets'][$index], $x, 1);
			}
		}
		$_SESSION['cart']--;
	}

	// PROCESAR LAS PETICIONES

	if ($_REQUEST['method'] == "add") {
		$ret = addBetToShoppingCart($_SESSION['shoppingCart'], $_REQUEST['idevent'], $_REQUEST['idbet'], $_REQUEST['money']);
		if ($ret == 1) echo "+1";
	} elseif ($_REQUEST['method'] == "rm") {
		removeBetFromShoppingCart($_SESSION['shoppingCart'], $_REQUEST['idevent'], $_REQUEST['idbet'], "rm");
	} elseif ($_REQUEST['method'] == "bet") {
		//Ponemos a true todas las apuestas
		$conn = init_conn();
		$stmt = $conn->prepare("UPDATE clientbets
		                              SET confirmed=true
		                              WHERE orderid = :orderid AND confirmed = false");
		$stmt->bindParam(':orderid', $_SESSION['cart_id'], PDO::PARAM_INT);
		$stmt->execute();

		//Cerramos el carrito
		$stmt = $conn->prepare("UPDATE clientorders
			SET date=now()
			WHERE orderid = :orderid");
		$stmt->bindParam(':orderid', $_SESSION['cart_id'], PDO::PARAM_INT);
		$stmt->execute();

		//Actualizamos variables
		$_SESSION['cart'] = 0;
		$_SESSION['cart_id'] = -1;
		header("Location: index.php");
	}
?>
