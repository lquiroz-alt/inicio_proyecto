<?php

 
	require_once 'login_mysql.php';
	///Busca capi
	//$url = "http://diferentede.com/hola.txt";
	//$ch = curl_init($url);
	$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
	$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//variables que necesito
	$sql_log = "";
	$tiempo_INSERT = 0;
	$tiempo_UPDATE = 0;
	$tiempo_SEND  = 0;
	$time_start = 0;
	$time_end = 0;

	$numero_cuenta = $_GET["cuenta_usuario"];
	$monto = $_GET["monto"];
	$sitio = $_GET["sitio"];
	$descripcion = $_GET["descripcion"];
	$ras = rand(100000,999999);
	
	//Generar numero de rastreo
	//Tipo de transferencia
	$sql = "SELECT cuenta_id, numero_cuenta, dinero FROM cuentas WHERE numero_cuenta = :numero_cuenta";
	$resultado=$base->prepare($sql);
	$resultado->bindValue(":numero_cuenta", $numero_cuenta);
	$resultado->execute();
	$buscar_cuenta = $resultado->fetch(PDO::FETCH_OBJ);
	$numero_registros = $resultado->rowCount();
	if( $numero_registros > 0 && $buscar_cuenta->dinero > $monto){
		///Encontre al usuario, entonces resto dinero
		$time_start = microtime(true);
		$restar_cuenta = $buscar_cuenta->dinero-$monto;
		$sql = "UPDATE cuentas SET dinero = :resta WHERE numero_cuenta = :num_cuenta";
		$resultado=$base->prepare($sql);
		$resultado->bindValue(":resta", $restar_cuenta);
		$resultado->bindValue(":num_cuenta", $buscar_cuenta->numero_cuenta);

		$resultado->execute();

		$time_end = microtime(true);
		$tiempo_UPDATE = ((float)$time_end-(float)$time_start);
		$tiempo_actualizar = substr($tiempo_UPDATE, 0, 5);

		/// Se mete en el 
		
		$time_start = microtime(true);
		$sql = "INSERT INTO historial_cliente (cuenta_id,descripcion,numero_rastreo,cantidad,sitio,tipo_transferencia_id,cuenta_destino,deleted) VALUES (:cuenta_id,:descripcion,:numero_rastreo,:cantidad,	:sitio,:tipo_transferencia_id,:cuenta_destino,:deleted)";
		$resultado=$base->prepare($sql);
		$resultado->bindValue(":cuenta_id", $buscar_cuenta->cuenta_id, PDO::PARAM_INT);
		$resultado->bindValue(":descripcion", "Transacción", PDO::PARAM_STR);
		$resultado->bindValue(":numero_rastreo",$ras, PDO::PARAM_INT);
		$resultado->bindValue(":cantidad", $monto, PDO::PARAM_STR);
		$resultado->bindValue(":sitio", $sitio, PDO::PARAM_STR);
		$resultado->bindValue(":tipo_transferencia_id", 3, PDO::PARAM_INT);
		$resultado->bindValue(":cuenta_destino", $numero_cuenta, PDO::PARAM_STR);
		$resultado->bindValue(":deleted", true, PDO::PARAM_BOOL);
		$resultado->execute();
		$time_end = microtime(true);
		$tiempo_INSERT = ((float)$time_end-(float)$time_start);
		$tiempo_insertar = substr($tiempo_INSERT, 0, 5);


		$sql_log = "INSERT INTO log (operacion,tiempo_operacion,cuenta,tipo_transaccion,accion,respuesta) VALUES (:operacion,:tiempo_operacion,:cuenta,:tipo_transaccion, :accion, :respuesta)";
		$resultado=$base->prepare($sql_log);
		$resultado->bindValue(":operacion", "Compra", PDO::PARAM_STR);////Operacion que se hizo
		$resultado->bindValue(":tiempo_operacion", $tiempo_insertar, PDO::PARAM_STR);///// tiempo de la operación
		$resultado->bindValue(":cuenta", $buscar_cuenta->numero_cuenta, PDO::PARAM_STR);///// cuenta
		$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[3], PDO::PARAM_STR);///// transacciones
		$resultado->bindValue(":accion", "INSERTA", PDO::PARAM_STR);///// accion que realiza
		$resultado->bindValue(":respuesta", "Aceptada", PDO::PARAM_STR); ///respuesta
		$resultado->execute();

		$resultado=$base->prepare($sql_log);
		$resultado->bindValue(":operacion", "Compra", PDO::PARAM_STR);////Operacion que se hizo
		$resultado->bindValue(":tiempo_operacion", $tiempo_actualizar, PDO::PARAM_STR);///// tiempo de la operación
		$resultado->bindValue(":cuenta", $buscar_cuenta->numero_cuenta, PDO::PARAM_STR);///// cuenta
		$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[3], PDO::PARAM_STR);///// transacciones
		$resultado->bindValue(":accion", "ACTUALIZAR", PDO::PARAM_STR);///// accion que realiza
		$resultado->bindValue(":respuesta", "Aceptada", PDO::PARAM_STR); ///respuesta
		$resultado->execute();


		$resultado = null;

		echo json_encode($ras);
	}else{
		$sql_log = "INSERT INTO log (operacion,tiempo_operacion,cuenta,tipo_transaccion,accion,respuesta) VALUES (:operacion,:tiempo_operacion,:cuenta,:tipo_transaccion, :accion, :respuesta)";
		$resultado=$base->prepare($sql_log);
		$resultado->bindValue(":operacion", "Compra", PDO::PARAM_STR);////Operacion que se hizo
		$resultado->bindValue(":tiempo_operacion", "00", PDO::PARAM_STR);///// tiempo de la operación
		$resultado->bindValue(":cuenta", $buscar_cuenta->numero_cuenta, PDO::PARAM_STR);///// cuenta
		$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[3], PDO::PARAM_STR);///// transacciones
		$resultado->bindValue(":accion", "CANCELADA", PDO::PARAM_STR);///// accion que realiza
		$resultado->bindValue(":respuesta", "RECHAZADA", PDO::PARAM_STR); ///respuesta
		$resultado->execute();
		$resultado=null;
        echo json_encode(0);
	}
	//retornar valor a capi

?>