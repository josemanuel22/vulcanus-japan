<?php

	function do_header_index() {
	?>
			<!-- header: nombre del sitio, botones de login y registro-->
		<header class="navbar navbar-fixed-top navbar-default">
			<!-- titulo -->
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="index.php">We<i id="brandLetter">B</i>start</a>
					<?php if(isset($_SESSION['nombre'])) { ?>
						<p> Bienvenido <?= @$_SESSION['nombre'] ?> </p>
					<?php } ?>
				</div>

				<!-- login y registro -->
				<div class="navbar-right ">
					<a href="carrito.php" class="btn btn-default navbar-btn glyphicon glyphicon-shopping-cart"> Carrito</a>
					<?php if(isset($_SESSION['nombre'])) { ?>
						<a href="historico.php" class="btn btn-default navbar-btn btn-success glyphicon glyphicon-user"> Perfil</a>
						<a href="logout.php" onclick="logout()" class="btn navbar-btn btn-danger">Logout</a>
						<script>
							function logout() {
								alert("Hasta luego!");							
							}
						</script>
					<?php } else { ?>
						
						<a href="login.php" class="btn navbar-btn btn-primary ">Login</a>
						<a href="registro.php" class="btn navbar-btn btn-success ">Registrase</a>
					<?php }?>
					
				</div>
			</div>
		</header>
	<?php
	}

	function do_header_login() { ?>
	<!-- header: nombre del sitio, botones de login y registro-->
		<header class="navbar navbar-default navbar-fixed-top">
			<!-- titulo  -->
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="index.php">We<i id="brandLetter">B</i>start</a>
				</div>

				<!-- login y registro -->
				<div class="navbar-right">
					<a href="registro.php" class="btn btn-success navbar-btn">Registrase</a>
					<a href="login.php" class="btn btn-primary navbar-btn">Login</a>
				</div>
			</div>
		</header>
	<?php
	}

	function do_header_carrito() { ?>
			<!-- header: nombre del sitio, botones de login y registro-->
		<header class="navbar navbar-fixed-top navbar-default">
			<!-- titulo -->
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="index.php">We<i id="brandLetter">B</i>start</a>
					<?php if(isset($_SESSION['nombre'])) { ?>
						<p> Bienvenido <?= @$_SESSION['nombre'] ?> </p>
					<?php } ?>
				</div>

				<!-- login y registro -->
				<div class="navbar-right ">
					<?php if(isset($_SESSION['nombre'])) { ?>
						<a href="historico.php" class="btn btn-default navbar-btn btn-success glyphicon glyphicon-user"> Perfil</a>
						<a href="logout.php" onclick="logout()" class="btn navbar-btn btn-danger">Logout</a>
						<script>
							function logout() {
								alert("Hasta luego!");							
							}
						</script>
					<?php } else { ?>
						<a href="login.php" class="btn navbar-btn btn-primary ">Login</a>
						<a href="registro.php" class="btn navbar-btn btn-success ">Registrase</a>
					<?php }?>
				</div>
			</div>
		</header>
	<?php
	}

	function do_header_detalleApuesta() { ?>
		<header class="navbar navbar-default navbar-fixed-top">
			<!-- titulo-->
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="index.php">We<i id="brandLetter">B</i>start</a>
					<?php if(isset($_SESSION['nombre'])) { ?>
						<p> Bienvenido <?= @$_SESSION['nombre'] ?></p>
					<?php } ?>
				</div>

				<!-- login y registro -->
					<?php if(isset($_SESSION['nombre'])) { ?>
						<div class="navbar-right">
							<a href="logout.php" class="btn navbar-btn btn-danger">Logout</a>
						</div>
					<?php } else { ?>
						<div class="navbar-right">
							<a href="login.php" class="btn navbar-btn btn-primary ">Login</a>
							<a href="registro.php" class="btn navbar-btn btn-success ">Registrase</a>
						</div>
					<?php } ?>	
			</div>
		</header>		
	<?php
	}

	function do_header_historico() { ?>
		<!-- header: nombre del sitio, botones de login y registro-->
		<header class="navbar navbar-fixed-top navbar-default">
			<!-- titulo -->
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="index.php">We<i id="brandLetter">B</i>start</a>
					<?php if(isset($_SESSION['nombre'])) { ?>
						<p> Bienvenido <?= @$_SESSION['nombre'] ?> </p>
					<?php } ?>
				</div>

				<!-- login y registro -->
				<div class="navbar-right ">
					<a href="carrito.php" class="btn btn-default navbar-btn glyphicon glyphicon-shopping-cart"> Carrito</a>
					<?php if(isset($_SESSION['nombre'])) { ?>
						<a href="logout.php" onclick="logout()" class="btn navbar-btn btn-danger">Logout</a>
						<script>
							function logout() {
								alert("Hasta luego!");							
							}
						</script>
					<?php } else { ?>	
						<a href="login.php" class="btn navbar-btn btn-primary ">Login</a>
						<a href="registro.php" class="btn navbar-btn btn-success ">Registrase</a>>
					<?php }?>	
				</div>
			</div>
		</header>
	<?php
	}

	function do_header_registro() { ?>
		<!-- header: nombre del sitio, botones de login y registro-->
		<header class="navbar navbar-default navbar-fixed-top">
			<!-- titulo -->
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="index.php">We<i id="brandLetter">B</i>start</a>
				</div>

				<!-- login y registro, cuando colapsa se mostrarán en el botón anterior -->
				<div class="navbar-right">
					<a href="login.php" class="btn navbar-btn btn-primary ">Login</a>
					<a href="registro.php" class="btn navbar-btn btn-success ">Registrase</a>
				</div>
			</div>
		</header>
	<?php
	}

	function do_lateral_menu() { ?>
		<div class="col-sm-2">
			<ul class="nav nav-pills nav-stacked">
				<li> <a onclick="filtrar('Football', -1)">Football</a>
				<li><a onclick="filtrar('Tenis', -1)">Tenis</a></li>
				<li><a onclick="filtrar('Beisbol', -1)">Béisbol</a></li>
				<li class="dropdown"> <a class="dropdown-toggle" data-toggle="dropdown" onclick="filtrar('Baloncesto', -1)">Baloncesto</a>
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

	<?php
	}
	
	function do_carrousel() { ?>
		<!-- Carousel de imagenes -->
		<div id="myCarousel" class="carousel slide" data-ride="carousel">
			<ol class="carousel-indicators">
		    	<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
		    	<li data-target="#myCarousel" data-slide-to="1"></li>
		    	<li data-target="#myCarousel" data-slide-to="2"></li>
		    	<li data-target="#myCarousel" data-slide-to="3"></li>
		  	</ol>
		  	<div class="carousel-inner" role="listbox">
				<div class="item active">
			    	<img src="./statics/soccer.jpeg" alt="Chania">
				</div>
				<div class="item">
						<img src="./statics/tennis.jpg" alt="Chania">
				</div>
				<div class="item">
						<img src="./statics/basketball.jpg" alt="Chania">
				</div>
			</div>

			  <!-- Left and right controls -->
			<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		 		<span class="sr-only">Previo</span>
		  	</a>
		  	<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
		    	<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		    	<span class="sr-only">Siguiente</span>
		  	</a>
		</div>
	<?php 
	}
	
	function do_footer() { ?>
		<footer class="navbar navbar-left navbar-default navbar-fixed-bottom">
			<?php if($_SERVER['PHP_SELF'] == "/1402_NoFernandez_deFrutos/index.php") {?>
			<div> Numero de usuarios conectados: <span id="myBanner"><span/> </div>
			<?php } ?>
			<small class="text-muted text-left"><p>Jose Manuel de Frutos Porras. Grupo 1402 Practica 2 SI</p></small>
		</footer>
	<?php
	}?>

	<script>

	function banner() {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if(this.readyState == 4 && this.status == 200) {
				document.getElementById("myBanner").innerHTML = this.responseText;
			}
		};
		var dir = "./banner.php";
		xhttp.open("GET", dir, true);
		xhttp.send();
	}
	</script>



