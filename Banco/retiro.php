<?php
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}

	require_once 'login_mysql.php';
	try{
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$numero_ciclos = rand(100, 100000);
		$restar = 0;
		$time_start = 0;
		$time_end = 0;
		$sql_retiro = "";
		$sql_inserta = "";
		$sql_log = "";
		$tiempo_INSERT = 0;
		$tiempo_UPDATE = 0;
		$rastreo = 0;

		for($i = 0; $i < 1200; $i++){
			$cuenta_retira = rand(1,16);
			$retiro = rand(100,1000);
			$cuenta_destino = rand(1,16);

			$sql_cuenta = "SELECT c.cuenta_id, c.numero_cuenta, c.dinero, t.nombre_cuenta FROM cuentas c
				INNER JOIN tipo_cuenta t ON t.tipo_cuenta_id = c.tipo_cuenta_id
				WHERE c.cuenta_id = :cuenta_id";
			$resultado=$base->prepare($sql_cuenta);
			$resultado->bindValue(":cuenta_id", $cuenta_retira);
			$resultado->execute();
			$obtener_datos_cliente = $resultado->fetch(PDO::FETCH_OBJ);

			if($obtener_datos_cliente->dinero > 10000){
				///Retirar saldo
				$restar = $obtener_datos_cliente->dinero - $retiro;
				$time_start = microtime(true);
				$sql_retiro = "UPDATE cuentas SET dinero = :restar WHERE cuenta_id = :cuenta_id";
				$resultado=$base->prepare($sql_retiro);
				$resultado->bindValue(":restar", $restar);
				$resultado->bindValue(":cuenta_id", $cliente);
				$resultado->execute();
				$time_end = microtime(true);

				$tiempo_UPDATE = ((float)$time_end-(float)$time_start);
				$tiempo_actualizar = substr($tiempo_UPDATE, 0, 5);

				$time_start = microtime(true);
				$sql_inserta = "INSERT INTO historial_cliente (cuenta_id,descripcion,numero_rastreo,cantidad,sitio,tipo_transferencia_id,cuenta_destino,deleted) VALUES (:cuenta_id,:descripcion,:numero_rastreo,:cantidad,	:sitio,:tipo_transferencia_id,:cuenta_destino,:deleted)";


				//Historia cliente deposita
				$rastreo = rand(100,10000);
				$resultado=$base->prepare($sql_inserta);
				$resultado->bindValue(":cuenta_id", $obtener_datos_cliente->cuenta_id, PDO::PARAM_INT);
				$resultado->bindValue(":descripcion", "Retiro de efectivo", PDO::PARAM_STR);
				$resultado->bindValue(":numero_rastreo", $rastreo, PDO::PARAM_INT);
				$resultado->bindValue(":cantidad", $deposito, PDO::PARAM_STR);
				$resultado->bindValue(":sitio","Banco Fortuna", PDO::PARAM_STR);
				$resultado->bindValue(":tipo_transferencia_id", 2, PDO::PARAM_INT);
				$resultado->bindValue(":cuenta_destino", "N/A", PDO::PARAM_STR);
				$resultado->bindValue(":deleted", true, PDO::PARAM_BOOL);
				$resultado->execute();

				$time_end = microtime(true);
				$tiempo_INSERT = ((float)$time_end-(float)$time_start);
				$tiempo_insertar = substr($tiempo_INSERT, 0, 5);


				$sql_log = "INSERT INTO log (operacion,tiempo_operacion,cuenta,tipo_transaccion,accion,respuesta) VALUES (:operacion,:tiempo_operacion,:cuenta,:tipo_transaccion,:accion,:respuesta)";
				$resultado=$base->prepare($sql_log);
				$resultado->bindValue(":operacion", "Retiro", PDO::PARAM_STR);////Operacion que se hizo
				$resultado->bindValue(":tiempo_operacion", $tiempo_insertar, PDO::PARAM_STR);///// tiempo de la operación
				$resultado->bindValue(":cuenta", $obtener_datos_cliente->nombre_cuenta, PDO::PARAM_STR);///// cuenta
				$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[1], PDO::PARAM_STR);///// transacciones
				$resultado->bindValue(":accion", "INSERTA", PDO::PARAM_STR);///// accion que realiza
				$resultado->bindValue(":respuesta", "Aceptada", PDO::PARAM_STR); ///respuesta
				$resultado->execute();

				$resultado=$base->prepare($sql_log);
				$resultado->bindValue(":operacion", "Retiro", PDO::PARAM_STR);////Operacion que se hizo
				$resultado->bindValue(":tiempo_operacion", $tiempo_actualizar, PDO::PARAM_STR);///// tiempo de la operación
				$resultado->bindValue(":cuenta", $obtener_datos_cliente->nombre_cuenta, PDO::PARAM_STR);///// cuenta
				$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[1], PDO::PARAM_STR);///// transacciones
				$resultado->bindValue(":accion", "ACTUALIZAR", PDO::PARAM_STR);///// accion que realiza
				$resultado->bindValue(":respuesta", "Aceptada", PDO::PARAM_STR); ///respuesta
				$resultado->execute();
				$resultado = null;

				echo "
					<div id='tabla_depositar'>
						<table class='footable'>
							<thead>
								<th>Nombre</th>
								<th>Tiempos</th>
							</thead>
						<tbody>
							<tr>
								<td><label>Tiempo actualizar: </label></td><td><label> ".$tiempo_actualizar."</label></td>
							</tr>
							<tr>
								<td><label>Tiempo insertar: </label></td><td><label>".$tiempo_insertar."</label></td>
							</tr>
					</tbody>
				</table>
		</div>";
			}else{
				///Rechazar operación
				$sql_log = "INSERT INTO log (operacion,tiempo_operacion,cuenta,tipo_transaccion,accion,respuesta) VALUES (:operacion,:tiempo_operacion,:cuenta,:tipo_transaccion,:accion,:respuesta)";
				$resultado=$base->prepare($sql_log);
				$resultado->bindValue(":operacion", "Retiro", PDO::PARAM_STR);////Operacion que se hizo
				$resultado->bindValue(":tiempo_operacion", "00" ,PDO::PARAM_STR);///// tiempo de la operación
				$resultado->bindValue(":cuenta", $obtener_datos_cliente->nombre_cuenta, PDO::PARAM_STR);///// cuenta
				$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[2], PDO::PARAM_STR);///// transacciones
				$resultado->bindValue(":accion", "CANCELADA", PDO::PARAM_STR);///// accion que realiza
				$resultado->bindValue(":respuesta", "Rechazada", PDO::PARAM_STR); ///respuesta
				$resultado->execute();
				$resultado =null;


			}

		}

	}catch(PDOException $e){
		print $e->getMessage();
	}
	

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="refresh" content="20">
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
	<title>Fortuna - <?php if($nivel == 1){ echo " LOG ADMINISTRADOR"; }else{ echo $obtener_datos_cliente->nombre; }?></title>
</head>
<body>
	<h2>Ejecutando clientes: Retiros :)</h2>
	
</body>
</html>