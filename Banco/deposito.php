<?php
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}
	require_once 'login_mysql.php';
	try{
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$usuario_sesion = $_SESSION["usuario"];
		$cuenta_saldo = array('cuenta'=>'','cantidad'=>'', 'verificar'=>'');
		if(isset($_POST["tipo_cuenta"]) && isset($_POST["cantidad"]) && isset($_POST["numero_cuenta"]) ){
			$tipo_cuenta = $_POST["tipo_cuenta"];
			$numero_cuenta = $_POST["numero_cuenta"];
			$cantidad = $_POST["cantidad"];

		}else{
			$tipo_cuenta = null;
			$numero_cuenta = null;
			$cantidad = null;
		}
	

		if($numero_cuenta != null || $cantidad != null || $tipo_cuenta != null){

			$sql = "SELECT c.cuenta_id, c.numero_cuenta, c.dinero FROM cuentas c
						INNER JOIN usuarios u ON u.cliente_id = c.cliente_id
						WHERE c.tipo_cuenta_id = 1 AND u.email = :email LIMIT 1";

			$resultado=$base->prepare($sql);
			$resultado->bindValue(":email", $usuario_sesion);
			$resultado->execute();
			$obtener_datos_cliente = $resultado->fetch(PDO::FETCH_OBJ);

			$sumar = $obtener_datos_cliente->dinero+$cantidad;
			$sql = "UPDATE cuentas SET dinero = :suma WHERE numero_cuenta = :num_cuenta";
			$resultado=$base->prepare($sql);
			$resultado->bindValue(":suma", $sumar);
			$resultado->bindValue(":num_cuenta", $numero_cuenta);

			$resultado->execute();

			$sql = "INSERT INTO historial_cliente (cuenta_id,descripcion,numero_rastreo,cantidad,sitio,tipo_transferencia_id,cuenta_destino,deleted) VALUES (:cuenta_id,:descripcion,:numero_rastreo,:cantidad,	:sitio,:tipo_transferencia_id,:cuenta_destino,:deleted)";


			$resultado=$base->prepare($sql);
			$resultado->bindValue(":cuenta_id", $obtener_datos_cliente->cuenta_id, PDO::PARAM_INT);
			$resultado->bindValue(":descripcion", "Deposito debito", PDO::PARAM_STR);
			$resultado->bindValue(":numero_rastreo", rand(100,10000), PDO::PARAM_INT);
			$resultado->bindValue(":cantidad", $cantidad, PDO::PARAM_STR);
			$resultado->bindValue(":sitio", "Banco Fortuna", PDO::PARAM_STR);
			$resultado->bindValue(":tipo_transferencia_id", 2, PDO::PARAM_INT);
			$resultado->bindValue(":cuenta_destino", $numero_cuenta, PDO::PARAM_STR);
			$resultado->bindValue(":deleted", true, PDO::PARAM_BOOL);
			$resultado->execute();
			$resultado = null;
			echo true;
		}else{
			echo false;
		}


	}catch(PDOException $e){
		print $e->getMessage();
	}


?>