<?php
session_start();

class USER {
	private $conn;

	public function __construct() {
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
	}

	public function runQuery($sql) {
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}

	public function exist($username) {
		try {
			$stmt = $this->conn->prepare("SELECT customerid FROM customers WHERE
			                              username=:username");
			echo "$username";
			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
			$stmt->execute();

			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);

			if ($stmt->rowCount() == 1) {
				return $userRow;
			} else {
				return null;
			}
		} catch(PDOException $ex) {
			echo $ex->getMessage();
		}
	}

	public function register($firstname, $lastname, $lastname2,
	                         $gender, $country, $city, $zip,
	                         $address1, $email, $creditcard,
	                         $username, $password) {
		try {
			$pass = $password;
			$stmt = $this->conn->prepare("INSERT INTO customers
				(customerid, firstname, lastname, lastname2,
				 gender, country, city, zip,
				 address1, email, creditcard,
				 username, password) VALUES
			    (DEFAULT, :firstname, :lastname, :lastname2,
			     :gender, :country, :city, :zip,
			     :address1, :email, :creditcard,
			     :username, md5(:password))");


			$stmt->bindparam(":firstname",  $firstname,  PDO::PARAM_STR);
			$stmt->bindparam(":lastname",   $lastname,   PDO::PARAM_STR);
			$stmt->bindparam(":lastname2",  $lastname2,  PDO::PARAM_STR);
			$stmt->bindparam(":gender",     $gender,     PDO::PARAM_STR);
			$stmt->bindparam(":country",    $country,    PDO::PARAM_STR);
			$stmt->bindparam(":city",       $city,       PDO::PARAM_STR);
			$stmt->bindparam(":zip",        $zip,        PDO::PARAM_STR);
			$stmt->bindparam(":address1",   $address1,   PDO::PARAM_STR);
			$stmt->bindparam(":email",      $email,      PDO::PARAM_STR);
			$stmt->bindparam(":creditcard", $creditcard, PDO::PARAM_STR);
			$stmt->bindparam(":username",   $username,   PDO::PARAM_STR);
			$stmt->bindparam(":password",   $password,   PDO::PARAM_STR);

			$stmt->execute();
			return $stmt;
		} catch(PDOException $ex) {
			echo $ex->getMessage();echo "hola";
		}
	}

	public function login($username, $password) {
		try {
			$user = new USER();
			$stmt = $this->conn->prepare("SELECT email, username, password, credit, customerid FROM customers WHERE
			                              username=:username and password=md5(:password);");
			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
			$stmt->bindParam(':password', $password, PDO::PARAM_STR);
			$stmt->execute();

			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);

			if ($stmt->rowCount() != 1) {
				$_SESSION['s_passwordInfo'] = "No exite un usuario con ese password";
				header("Location: login.php");
				exit;
			} else {
				/* password correcta, tendremos que hacer todas las operaciones antes del login */
				$_SESSION['saldo']       = $userRow['credit'];
				$_SESSION['s_user_name'] = $userRow['username'];
				$_SESSION['user_id']     = $userRow['customerid'];

				// Buscamos carrito disponible en la DB
				$stmt = $this->search_shoppingCartDB($_SESSION['user_id']);

				//Si no hay carrito cerrado, abrimos uno
				if ($stmt->rowCount() < 1) {
					$this->create_shoppingCartDB($_SESSION['user_id']);
					
					//Buscamos carrito disponible de nuevo
					$stmt->execute();
				}

				// En este punto ya tenemos carrito
				$cart=$stmt->fetch(PDO::FETCH_ASSOC);
				$_SESSION['cart_id'] = $cart['orderid'];

				if ($_SESSION['shoppingCart'] != "" && $_SESSION['shoppingCart'] != null) {
					//recorremos el carrito en sesion y lo guardamos en la BD
					$stmt2 = $this->conn->prepare("INSERT INTO clientbets
						(customerid, optionid, bet, ratio, outcome, betid, orderid, clientbetid, confirmed) VALUES
						(:customerid, :optionid, :bet, :ratio, 0, :betid, :orderid, DEFAULT, false)");

					$stmt3 = $this->conn->prepare("SELECT ratio FROM bets NATURAL JOIN optionbet WHERE betid = :betid AND optionid =:optionid");

					foreach ($_SESSION['shoppingCart']['idEvents'] as $betId) {
						//en ['idEvents'] estan las betid
						$stmt2->bindParam(':customerid', $_SESSION['user_id'], PDO::PARAM_INT);
						$stmt2->bindParam(':orderid', $_SESSION['cart_id'], PDO::PARAM_INT);
						$stmt2->bindParam(':betid', $betId, PDO::PARAM_INT);

						$stmt3->bindParam(':betid', $betId, PDO::PARAM_INT);

						$index = array_search($betId, $_SESSION['shoppingCart']['idEvents']);
						foreach ($_SESSION['shoppingCart']['idBets'][$index] as $dupla) {
							$stmt3->bindParam(':optionid', $dupla[0], PDO::PARAM_STR);
							$stmt3->execute();
							$query_ratio = $stmt3->fetch(PDO::FETCH_ASSOC);

							$stmt2->bindParam(':optionid', $dupla[0], PDO::PARAM_STR);
							$stmt2->bindParam(':bet',      $dupla[1], PDO::PARAM_STR);
							$stmt2->bindParam(':ratio',    $query_ratio['ratio'], PDO::PARAM_STR);
							$stmt2->execute();
						}
					}

					//matamos carrito
					unset($_SESSION['shoppingCart']);
					$_SESSION['shoppingCart'] = null;
				}

				//tengamos carrito o no, obtenemos el numero total de apuestas
				$stmt = $this->conn->prepare("SELECT count(*) FROM clientbets WHERE
				                              orderid = :orderid;");
				$stmt->bindParam(':orderid', $_SESSION['cart_id'], PDO::PARAM_STR);
				$stmt->execute();
				$row=$stmt->fetch();

				$_SESSION['cart']=$row['count'];
				header("location:index.php");
				exit;
			}
		} catch(PDOException $ex) {
			echo $ex->getMessage();
		}
	}

	public function is_logged() {
		if($_SESSION['s_user_name'] == "") {
			return false;
		}
		return true;
	}

	public function logout() {
		session_destroy();
		$_SESSION['s_user_name'] = "";
	}

	public function search_shoppingCartDB($userid) {
		$stmt = $this->conn->prepare("SELECT orderid FROM clientorders WHERE date is NULL and customerid = :customerid");
		$stmt->bindParam(':customerid', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->execute();

		return $stmt;
	}

	public function create_shoppingCartDB($userid) {
		$stmt = $this->conn->prepare("INSERT INTO clientorders
		                             (customerid, date, orderid, totalamount, totaloutcome) VALUES
		                             (:customerid, NULL, DEFAULT, 0, 0)");
		$stmt->bindParam(':customerid', $_SESSION['user_id'], PDO::PARAM_INT);
					echo "hi";

		$stmt->execute();

		return $stmt;
	}

	public function updateBalance() {
		$stmt = $this->conn->prepare("SELECT credit FROM customers WHERE customerid = :customerid");
		$stmt->bindParam(':customerid', $_SESSION['user_id'], PDO::PARAM_STR);
		$stmt->execute();
		
		$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$_SESSION['saldo'] = $userRow['credit'];
	}
}
?>
