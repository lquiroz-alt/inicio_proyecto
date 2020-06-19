<?php
	header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
////if validar usuario y el banco
	require_once 'login_mysql.php';
    $cuentau = $_GET["cuentau"]; /// variable usuario
    $cuentat = $_GET["cuentat"];/// varibales de tienda
    $monto = $_GET["monto"];
    $sitio = $_GET["sitio"];
    $descripcion = $_GET["desc"];
	//Buscar y sino esta le mando el valor a capi
	//http://192.168.23.4/Banco/conexion_tienda.php?cuentau=4321628545479415&cuentat=4321&monto=200&sitio=BancoCapi&desc=libro
    //// si exite el usuario resto el dinero y agrego historial
	//// notifico a la tienda
	$rastreo = rand(100000,999999);
	$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
	$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	////Variables para el log
	$sql_log = "";
	$tiempo_INSERT = 0;
	$tiempo_UPDATE = 0;
	$tiempo_SEND  = 0;
	$time_start = 0;
	$time_end = 0;

	$verificar_cuenta_capi = substr($cuentau, 0, 1);

	if($verificar_cuenta_capi != '4' ){

		$sql2 = "SELECT u.nombre, u.email, c.cuenta_id, c.numero_cuenta, c.dinero, c.tipo_cuenta_id FROM usuarios u
		LEFT JOIN cuentas c ON u.cliente_id = c.cliente_id
		WHERE c.numero_cuenta = :numero_cuenta";

	
		$resultado2=$base->prepare($sql2);
		$resultado2->bindValue(":numero_cuenta", $cuentau);
		$resultado2->execute();
		$datos_cliente = $resultado2->fetch(PDO::FETCH_OBJ);
		$numero_registro=$resultado2->rowCount();
		if($numero_registro > 0 && $datos_cliente->dinero > $monto){
			$restar_cliente = $datos_cliente->dinero-$monto;

			$time_start = microtime(true);
			////restar dinero a la cuenta cliente
			$sql = "UPDATE cuentas SET dinero = :resta WHERE numero_cuenta = :num_cuenta";
			$resultado=$base->prepare($sql);
			$resultado->bindValue(":resta", $restar_cliente);
			$resultado->bindValue(":num_cuenta", $datos_cliente->numero_cuenta);

			$resultado->execute();



			$sql2 = "SELECT u.nombre, u.email, c.cuenta_id, c.numero_cuenta, c.dinero, c.tipo_cuenta_id FROM usuarios u
				LEFT JOIN cuentas c ON u.cliente_id = c.cliente_id
				WHERE c.numero_cuenta = :numero_cuenta";
			$resultado2=$base->prepare($sql2);
			$resultado2->bindValue(":numero_cuenta", $cuentat);
			$resultado2->execute();
			$datos_tienda = $resultado2->fetch(PDO::FETCH_OBJ);


			$sumar_tienda = $datos_tienda->dinero+$monto;

			//// sumar dinero a la cuenta tienda
			$sql = "UPDATE cuentas SET dinero = :sumar WHERE numero_cuenta = :num_cuenta";
			$resultado3=$base->prepare($sql);
			$resultado3->bindValue(":sumar", $sumar_tienda);
			$resultado3->bindValue(":num_cuenta", $datos_tienda->numero_cuenta);

			$resultado3->execute();
			$time_end = microtime(true);

			//Tiempo para actualizar
			$tiempo_UPDATE = ((float)$time_end-(float)$time_start);
			$tiempo_actualizar = substr($tiempo_UPDATE, 0, 5);

			$time_start = microtime(true);
			$sql = "INSERT INTO historial_cliente (cuenta_id,descripcion,numero_rastreo,cantidad,sitio,tipo_transferencia_id,cuenta_destino,deleted) VALUES (:cuenta_id,:descripcion,:numero_rastreo,:cantidad,	:sitio,:tipo_transferencia_id,:cuenta_destino,:deleted)";

			//Historia cliente
			$rastreo = rand(100,10000);
			$resultado=$base->prepare($sql);
			$resultado->bindValue(":cuenta_id", $datos_cliente->cuenta_id, PDO::PARAM_INT);
			$resultado->bindValue(":descripcion", $descripcion, PDO::PARAM_STR);
			$resultado->bindValue(":numero_rastreo", $rastreo, PDO::PARAM_INT);
			$resultado->bindValue(":cantidad", $monto, PDO::PARAM_STR);
			$resultado->bindValue(":sitio",$sitio, PDO::PARAM_STR);
			$resultado->bindValue(":tipo_transferencia_id", 3, PDO::PARAM_INT);
			$resultado->bindValue(":cuenta_destino", $datos_tienda->numero_cuenta, PDO::PARAM_STR);
			$resultado->bindValue(":deleted", true, PDO::PARAM_BOOL);
			$resultado->execute();
				
			///Historia tienda
			$resultado4=$base->prepare($sql);
			$resultado4->bindValue(":cuenta_id", $datos_tienda->cuenta_id, PDO::PARAM_INT);
			$resultado4->bindValue(":descripcion", $descripcion, PDO::PARAM_STR);
			$resultado4->bindValue(":numero_rastreo", $rastreo, PDO::PARAM_INT);
			$resultado4->bindValue(":cantidad", $monto, PDO::PARAM_STR);
			$resultado4->bindValue(":sitio", "Cliente: ".$datos_cliente->nombre, PDO::PARAM_STR);
			$resultado4->bindValue(":tipo_transferencia_id", 3, PDO::PARAM_INT);
			$resultado4->bindValue(":cuenta_destino", $datos_cliente->numero_cuenta, PDO::PARAM_STR);
			$resultado4->bindValue(":deleted", true, PDO::PARAM_BOOL);
			$resultado4->execute();
			$time_end = microtime(true);
			$tiempo_INSERT = ((float)$time_end-(float)$time_start);
			$tiempo_insertar = substr($tiempo_INSERT, 0, 5);

			///guardar en el log
			$sql_log = "INSERT INTO log (operacion,tiempo_operacion,cuenta,tipo_transaccion,accion,respuesta) VALUES (:operacion,:tiempo_operacion,:cuenta,:tipo_transaccion, :accion, :respuesta)";
			$resultado=$base->prepare($sql_log);
			$resultado->bindValue(":operacion", "Compra", PDO::PARAM_STR);////Operacion que se hizo
			$resultado->bindValue(":tiempo_operacion", $tiempo_insertar, PDO::PARAM_STR);///// tiempo de la operación
			$resultado->bindValue(":cuenta", $datos_cliente->numero_cuenta, PDO::PARAM_STR);///// cuenta
			$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[3], PDO::PARAM_STR);///// transacciones
			$resultado->bindValue(":accion", "INSERTA", PDO::PARAM_STR);///// accion que realiza
			$resultado->bindValue(":respuesta", "Aceptada", PDO::PARAM_STR); ///respuesta
			$resultado->execute();

			$resultado=$base->prepare($sql_log);
			$resultado->bindValue(":operacion", "Compra", PDO::PARAM_STR);////Operacion que se hizo
			$resultado->bindValue(":tiempo_operacion", $tiempo_actualizar, PDO::PARAM_STR);///// tiempo de la operación
			$resultado->bindValue(":cuenta", $datos_tienda->numero_cuenta, PDO::PARAM_STR);///// cuenta
			$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[3], PDO::PARAM_STR);///// transacciones
			$resultado->bindValue(":accion", "ACTUALIZAR", PDO::PARAM_STR);///// accion que realiza
			$resultado->bindValue(":respuesta", "Aceptada", PDO::PARAM_STR); ///respuesta
			$resultado->execute();

			$resultado = null;
			$resultado2 = null;
			$resultado3 = null;
			$resultado4 = null;

			echo json_encode($rastreo);
		}else{
			$sql_log = "INSERT INTO log (operacion,tiempo_operacion,cuenta,tipo_transaccion,accion,respuesta) VALUES (:operacion,:tiempo_operacion,:cuenta,:tipo_transaccion, :accion, :respuesta)";
			$resultado=$base->prepare($sql_log);
			$resultado->bindValue(":operacion", "Compra", PDO::PARAM_STR);////Operacion que se hizo
			$resultado->bindValue(":tiempo_operacion", "00", PDO::PARAM_STR);///// tiempo de la operación
			$resultado->bindValue(":cuenta", "N/A", PDO::PARAM_STR);///// cuenta
			$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[3], PDO::PARAM_STR);///// transacciones
			$resultado->bindValue(":accion", "CANCELADA", PDO::PARAM_STR);///// accion que realiza
			$resultado->bindValue(":respuesta", "Rechazada", PDO::PARAM_STR); ///respuesta
			$resultado->execute();
			$resultado = null;
			echo json_encode(0);
		}	
	}else{
		$time_start = microtime(true);
		///retorno con json_encode
		///http://192.168.23.8/Banco/conexion_tienda.php?cuentau=4322974106322596&cuentat=1234567885318525&monto=300&sitio=TheBest&desc=videojuego
			$url = "192.168.43.171/buscarusuario/".$cuentau."/".$cuentat."/".$monto."/".$sitio."/".$descripcion;
 			


			/*$ch = curl_init($url);
			$resultado = curl_setopt($ch, CURLOPT_HTTPGET, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$resultado = curl_exec($ch);
			curl_close($ch);*/
			// Creamos un array con los valores que se pasaran por post
			$data = array('parametro1' => "valor1", 'parametro2' => "valor2");
	 
			// Se crea un manejador CURL
			$ch=curl_init ();

			// Se establece la URL y algunas opciones
			curl_setopt($ch, CURLOPT_URL, $url);
			 
			// Indicamos que enviaremos las variables en POST
			curl_setopt($ch, CURLOPT_HTTPGET, 1);
	 
			// Adjuntamos las variables
			//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	 	
			// Indicamos que el resultado lo devuelva curl_exec()
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 		
			// Se obtiene la URL indicada
			$result=curl_exec($ch);
			$time_end = microtime(true);
			$tiempo_SEND = ((float)$time_end-(float)$time_start);
			$tiempo_enviar = substr($tiempo_SEND, 0, 5);

			$sql_log = "INSERT INTO log (operacion,tiempo_operacion,cuenta,tipo_transaccion,accion,respuesta) VALUES (:operacion,:tiempo_operacion,:cuenta,:tipo_transaccion,:accion,:respuesta)";
			$resultado=$base->prepare($sql_log);
			$resultado->bindValue(":operacion", "Compra", PDO::PARAM_STR);////Operacion que se hizo
			$resultado->bindValue(":tiempo_operacion", $tiempo_enviar, PDO::PARAM_STR);///// tiempo de la operación
			$resultado->bindValue(":cuenta", $cuentau, PDO::PARAM_STR);///// cuenta
			$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[3], PDO::PARAM_STR);///// transacciones
			$resultado->bindValue(":accion", "ENVIADA", PDO::PARAM_STR);///// accion que realiza
			$resultado->bindValue(":respuesta", "Esperando", PDO::PARAM_STR); ///respuesta
			$resultado->execute();

			if($result != 0){
				///Si la petición es exitosa sacamos datos de la tienda
             
				$sql2 = "SELECT u.nombre, u.email, c.cuenta_id, c.numero_cuenta, c.dinero, c.tipo_cuenta_id FROM usuarios u
				LEFT JOIN cuentas c ON u.cliente_id = c.cliente_id
				WHERE c.numero_cuenta = :numero_cuenta";	

	
				$resultado2=$base->prepare($sql2);
				$resultado2->bindValue(":numero_cuenta", $cuentat);
				$resultado2->execute();
				$datos_tienda = $resultado2->fetch(PDO::FETCH_OBJ);
				$numero_registro=$resultado2->rowCount();
				if($numero_registro > 0){
					///Guardar la transacción de la tienda
					$time_start = microtime(true);
					$sql = "INSERT INTO historial_cliente (cuenta_id,descripcion,numero_rastreo,cantidad,sitio,tipo_transferencia_id,cuenta_destino,deleted) VALUES (:cuenta_id,:descripcion,:numero_rastreo,:cantidad,	:sitio,:tipo_transferencia_id,:cuenta_destino,:deleted)";
					$resultado=$base->prepare($sql);
					$resultado->bindValue(":cuenta_id", $datos_tienda->cuenta_id, PDO::PARAM_INT);
					$resultado->bindValue(":descripcion", "Deposito", PDO::PARAM_STR);
					$resultado->bindValue(":numero_rastreo", $result, PDO::PARAM_INT);
					$resultado->bindValue(":cantidad", $monto, PDO::PARAM_STR);
					$resultado->bindValue(":sitio", $sitio, PDO::PARAM_STR);
					$resultado->bindValue(":tipo_transferencia_id", 5, PDO::PARAM_INT);
					$resultado->bindValue(":cuenta_destino", $cuentau, PDO::PARAM_STR);
					$resultado->bindValue(":deleted", true, PDO::PARAM_BOOL);
					$resultado->execute();
					///log
					$time_end = microtime(true);
					$tiempo_INSERT = ((float)$time_end-(float)$time_start);
					$tiempo_insertar = substr($tiempo_INSERT, 0, 5);
					///Actualizar el dinero de la tienda
					$sumar_cuenta = $datos_tienda->dinero+$monto;

					///log
					$time_start = microtime(true);
					$sql = "UPDATE cuentas SET dinero = :suma WHERE numero_cuenta = :num_cuenta";
					$resultado=$base->prepare($sql);
					$resultado->bindValue(":suma", $sumar_cuenta );
					$resultado->bindValue(":num_cuenta", $datos_tienda->numero_cuenta);

					$resultado->execute();

					$time_end = microtime(true);
					$tiempo_UPDATE = ((float)$time_end-(float)$time_start);
					$tiempo_actualizar = substr($tiempo_UPDATE, 0, 5);

					$sql_log = "INSERT INTO log (operacion,tiempo_operacion,cuenta,tipo_transaccion,accion,respuesta) VALUES (:operacion,:tiempo_operacion,:cuenta,:tipo_transaccion,:accion,:respuesta)";

					$resultado=$base->prepare($sql_log);
					$resultado->bindValue(":operacion", "Compra", PDO::PARAM_STR);////Operacion que se hizo
					$resultado->bindValue(":tiempo_operacion", $tiempo_insertar, PDO::PARAM_STR);///// tiempo de la operación
					$resultado->bindValue(":cuenta", $cuentau, PDO::PARAM_STR);///// cuenta
					$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[3], PDO::PARAM_STR);///// transacciones
					$resultado->bindValue(":accion", "INSERTA", PDO::PARAM_STR);///// accion que realiza
					$resultado->bindValue(":respuesta", "Aceptada", PDO::PARAM_STR); ///respuesta
					$resultado->execute();


					$resultado=$base->prepare($sql_log);
					$resultado->bindValue(":operacion", "Compra", PDO::PARAM_STR);////Operacion que se hizo
					$resultado->bindValue(":tiempo_operacion", $tiempo_actualizar, PDO::PARAM_STR);///// tiempo de la operación
					$resultado->bindValue(":cuenta", $cuentau, PDO::PARAM_STR);///// cuenta
					$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[3], PDO::PARAM_STR);///// transacciones
					$resultado->bindValue(":accion", "ACTUALIZAR", PDO::PARAM_STR);///// accion que realiza
					$resultado->bindValue(":respuesta", "Aceptada", PDO::PARAM_STR); ///respuesta
					$resultado->execute();

					$resultado = null;
					$resultado2 = null;
					echo json_encode($result);

				}else{
					$sql_log = "INSERT INTO log (operacion,tiempo_operacion,cuenta,tipo_transaccion,accion,respuesta) VALUES (:operacion,:tiempo_operacion,:cuenta,:tipo_transaccion,:accion,:respuesta)";

					$resultado=$base->prepare($sql_log);
					$resultado->bindValue(":operacion", "Compra", PDO::PARAM_STR);////Operacion que se hizo
					$resultado->bindValue(":tiempo_operacion", "00", PDO::PARAM_STR);///// tiempo de la operación
					$resultado->bindValue(":cuenta", $cuentau, PDO::PARAM_STR);///// cuenta
					$resultado->bindValue(":tipo_transaccion", $tipo_transferencia[3], PDO::PARAM_STR);///// transacciones
					$resultado->bindValue(":accion", "CANCELADA", PDO::PARAM_STR);///// accion que realiza
					$resultado->bindValue(":respuesta", "Rechazada", PDO::PARAM_STR); ///respuesta
					$resultado->execute();
					$resultado=null;
					echo 0;
				}
			}else{
				echo 0;
			}
	}
?>

