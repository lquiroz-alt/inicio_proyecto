<?php
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}
	$_SESSION["monitoreo_r"] = 0;
	$_SESSION["transacciones_r"] = 0;
	$_SESSION["transacciones"] = 0;
	$_SESSION["monitoreo"] = 0;

	require_once 'login_mysql.php';
	try{
		$nivel = $_SESSION["nivel"];
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$numero_ciclos = rand(100, 100000);

	}catch(PDOException $e){
		print $e->getMessage();
	}
	

?>

<!DOCTYPE html>
<html>
<head>
	<meta name="description" content="Historial e información de cliente">
	<meta charset="utf-8">
	<script type="text/javascript" src="lib/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="lib/jquery/jquery-migrate.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/footable.standalone.css">
	<link rel="stylesheet" type="text/css" href="css/footable.standalone.min.css">
	<script type="text/javascript" src="script.js"></script>
	<script type="text/javascript" src="script_tabla.js"></script>
	<link rel="stylesheet" type="text/css" href="formato_historial.css">
	<link rel="stylesheet" type="text/css" href="formato_sesion.css">
	<link rel="stylesheet" type="text/css" href="formato_log_administrador.css">
	<title>Fortuna - <?php if($nivel == 1){ echo " LOG ADMINISTRADOR"; }else{ echo $obtener_datos_cliente->nombre; }?></title>
</head>
<body>
	<header>
		<h3 style="position:  absolute; bottom: 449px;">La fortuna esta en tus manos</h3>
		<img src="Banner_Fortuna.png" width="1300" height="200">
		<h2>LOG ADMINISTRADOR</h2>
		<h3>Bienvenido(a): <?php if($nivel == 1){ echo "ADMINISTRADOR"; }else{ echo $obtener_datos_cliente->nombre; } ?></h3>	
	</header>
	<main>
		<div id="menu">
			<?php 
				if($nivel == 1){
					echo "<ul>
							<li><a class='item_menu' href='sesion_usuario.php'>Saldos</a></li>
							<li><a class='item_menu' href='historial_admin.php'>Historial admin</a></li>
							<li><a class='item_menu' href='log_administrador.php'>Log</a></li>
							<li><a class='item_menu' href='cierre_sesion.php'>Cerrar Sesión</a></li>
						</ul>";
				}else{
					echo "<ul>
							<li><a class='item_menu' href='sesion_usuario.php'>Saldos</a></li>
							<li><a class='item_menu' href='historial.php'>Historial</a></li>
							<li><a class='item_menu' href='abrir_credito.php'>Abrir credito</a></li>
							<li><a class='item_menu' href='cierre_sesion.php'>Cerrar Sesión</a></li>
						</ul>";
				}
			 ?>
		</div>
		<div id="div_ex">
			<table>
				<tbody>
					<tr>
						<td><a href="depositar.php">DEPOSITAR</a></td>
						<td><a href="retiro.php">RETIRAR</a></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="opciones_log">
			<ul>
				<li>
					<button id="boton_depositar">Generar log excel - Depositos</button>
					<ul>
						<li><a style="color:rgb(255,0,0);" href="excel_insert_depositar.php" class="ocultos1">INSERT</a></li>
						<li><a style="color:rgb(255,0,0);" href="excel_update_depositar.php" class="ocultos1">UPDATE</a></li>
					</ul>
				</li>
				<li>
					<button id="boton_retiro" onclick="mostrar_opciones();">Generar log excel - Retiros</button>
					<ul>
						<li><a style="color:rgb(255,0,0);" href="excel_insert_retiro.php" class="ocultos">INSERT</a></li>
						<li><a style="color:rgb(255,0,0);" href="excel_update_retiro.php" class="ocultos">UPDATE</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</main>

</body>
</html>