<?php
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}
	require_once 'login_mysql.php';
	try{
		$cuenta_saldo = array('cuenta'=>'','cantidad'=>'', 'verificar'=>'');
		$usuario_sesion = $_SESSION["usuario"];
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$cuenta_saldo["verificar"] = true;
		$sql = "SELECT cliente_id FROM usuarios WHERE email = :correo";
		$resultado=$base->prepare($sql);
		$resultado->bindValue(":correo", $usuario_sesion);
		$resultado->execute();
		$numero_registro=$resultado->fetch(PDO::FETCH_OBJ);
		$clave = rand(100,999);


		$sql = "SELECT c.tipo_cuenta_id FROM cuentas c
				INNER JOIN usuarios u ON u.cliente_id = c.cliente_id
				WHERE u.email = :correo AND c.tipo_cuenta_id = 2";

		$resultado=$base->prepare($sql);
		$resultado->bindValue(":correo", $usuario_sesion);
		$resultado->execute();
		$cuenta_credito = $resultado->rowCount();
		

		if($cuenta_credito == 0){
			$cantidad_deposito = 10000;
			$tarjeta = "11112222".rand(1000,9999)."".rand(1000,9999);
			$tipo_cuenta = 2;

			$sql = "INSERT INTO cuentas (cliente_id,tipo_cuenta_id, numero_cuenta, mes, anio, dinero, clave, deleted) VALUES ( :cliente_id,:tipo_cuenta_id, :numero_cuenta, :mes, :anio, :dinero, :clave,:deleted)";
			$resultado=$base->prepare($sql);
			$resultado->bindValue(":cliente_id", $numero_registro->cliente_id, PDO::PARAM_INT);
			$resultado->bindValue(":tipo_cuenta_id", $tipo_cuenta, PDO::PARAM_INT);
			$resultado->bindValue(":numero_cuenta", $tarjeta, PDO::PARAM_STR);
			$resultado->bindValue(":mes", 05, PDO::PARAM_INT);
			$resultado->bindValue(":anio", 20, PDO::PARAM_INT);
			$resultado->bindValue(":dinero", $cantidad_deposito, PDO::PARAM_STR);
			$resultado->bindValue(":clave", $clave, PDO::PARAM_INT);
			$resultado->bindValue(":deleted", $cuenta_saldo["verificar"], PDO::PARAM_BOOL);
			$resultado->execute();
			$resultado = null;
			$cuenta_saldo["cuenta"] = $tarjeta;
			$cuenta_saldo["cantidad"] = 10000; 
			echo json_encode($cuenta_saldo);

		}else{
			$cuenta_saldo["verificar"] = false;
			echo json_encode($cuenta_saldo);
		}
		$resultado = null;
	}catch(PDOException $e){
		print $e->getMessage();
	}
?>