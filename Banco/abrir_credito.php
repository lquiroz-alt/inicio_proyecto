<?php
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}
	require_once 'login_mysql.php';

	try{
		$usuario_sesion = $_SESSION["usuario"];
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$id_cuenta = $_SESSION['cuenta_id'];
		$nombre_cliente = $_SESSION['nombre'];
		$nombre_cuenta = $_SESSION['nombre_cuenta'];
		$numero_cuenta = $_SESSION['numero_cuenta'];
		$cantidad_dinero = $_SESSION['dinero'];
		$texto = "";

		$sql = "SELECT c.tipo_cuenta_id FROM cuentas c
				INNER JOIN usuarios u ON u.cliente_id = c.cliente_id
				WHERE u.email = :correo AND c.tipo_cuenta_id = 2";

		$resultado=$base->prepare($sql);
		$resultado->bindValue(":correo", $usuario_sesion);
		$resultado->execute();
		$cuenta_credito = $resultado->rowCount();

		if($cantidad_dinero > 1000 && $cuenta_credito == 0){
			$sql = "SELECT cliente_id FROM cuentas WHERE numero_cuenta = :cuenta";
			$resultado=$base->prepare($sql);
			$verificacion = false;
			$resultado->bindValue(":cuenta", $numero_cuenta);

			$resultado->execute();
			$numero_registro=$resultado->rowCount();
			
			if($numero_registro != 0){
				$obtener_id_cliente = $resultado->fetch(PDO::FETCH_OBJ);
				$verificacion = true;
			}
			$texto = "<p>El banco Fortuna le ofrece un crédito de $10,000.00 pesos</p>
				<input type=button value='Aceptar credito' onclick='apertura_credito(".$id_cuenta.");'/>";

		}else{
			$texto = <<<RAM
				<p>El banco Fortuna no le puede ofrecer un crédito por el momento o ya cuenta con una linea de crédito</p>
RAM;
		}
		
	}catch(PDOException $e){
		print $e->getMessage();
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="description" content="Abrir un credito para el cliente">
	<meta charset="utf-8">
	<script type="text/javascript" src="lib/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="lib/jquery/jquery-migrate.min.js"></script>
	<script type="text/javascript" src="script_tabla.js"></script>
	<link rel="stylesheet" type="text/css" href="formato_sesion.css">
	<link rel="stylesheet" type="text/css" href="formato.css">
	<title>Apertura de credito - <?php  echo $nombre_cliente ?></title>
</head>
<body>
	<header>
		<h3 style="position:  absolute; bottom: 449px;">La fortuna esta en tus manos</h3>
		<img src="Banner_Fortuna.png" width="1300" height="200">
		<h2>Apertura de credito - <?php  echo $nombre_cliente ?></h2>
	</header>
	<main>
		<div id="menu">
			<ul>
				<li><a class="item_menu" href="sesion_usuario.php">Saldos</a></li>
				<li><a class="item_menu" href="historial.php">Historial</a></li>
				<li><a class="item_menu" href="cierre_sesion.php">Cerrar Sesión</a></li>
			</ul>
		</div>
		<div id="abrir_credito_div">
			<?php echo $texto; ?>
		</div>
	</main>
	<footer></footer>
</body>
</html>