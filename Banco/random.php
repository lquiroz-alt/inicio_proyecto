<?php
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}

	require_once 'login_mysql.php';
	try{
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


		/////Siguiendo el flujo normal de usuarios_
		////Extraemos primeros datos 
		$numero_ciclos = rand(100, 100000);
		for($i = 0; $i < $numero_ciclos; $i++){
			$cliente = rand(1,10);
			$sql0 = "SELECT email, password_cliente FROM usuarios WHERE cliente_id = :cliente_id";
			$resultado=$base->prepare($sql0);
			$resultado->bindValue(":cliente_id", $cliente);
			$resultado->execute();
			$obtener_datos_cliente = $resultado->fetch(PDO::FETCH_OBJ);


			////// *********************** Controlador.php ***************************
			///// Validar inicio de sesión y tomar tiempo
			$time_start = microtime(true);
			$sql1 = "SELECT * FROM usuarios WHERE email = :usuario AND password_cliente = :contra";
			$resultado=$base->prepare($sql1);
			$resultado->bindValue(":usuario", $obtener_datos_cliente->email);
			$resultado->bindValue(":contra", $obtener_datos_cliente->password_cliente);
			$resultado->execute();
			$time_end = microtime(true);
			$tiempo_promedio_sesion = $tiempo_promedio_sesion+($time_start-$time_end);
			/////Operaciones a realizar
			$operacion_realizar = rand(1,5);
			if($operacion_realizar == 1){////Retiro de dinero
				$retiro = rand(100,1000);


				/////OBTENEMOS numero de cuenta, el dinero y el tipo de cuenta
				$sql0 = "SELECT  c.cuenta_id, c.numero_cuenta, c.dinero, c.tipo_cuenta_id, tc.nombre_cuenta FROM cuentas c
				INNER JOIN usuarios u ON u.cliente_id = c.cliente_id
				INNER JOIN tipo_cuenta tc ON tc.tipo_cuenta_id = c.tipo_cuenta_id
				WHERE u.cliente_id = :cliente_id";
				$resultado=$base->prepare($sql0);
				$resultado->bindValue(":cliente_id", $cliente);
				$resultado->execute();
				$obtener_datos_cuenta = $resultado->fetch(PDO::FETCH_OBJ);

				if ($obtener_datos_cuenta->nombre_cuenta == 'Debito') {/// Es de debito
					///Cuenta a la que haremos el deposito
					$cuenta_destino = rand(1,10);

					////Datos de la cuenta destino
					$sql= "SELECT numero_cuenta, dinero FROM cuentas WHERE cliente_id = :cliente_id";
					$resultado=$base->prepare($sql);
					$resultado->bindValue(":cliente_id", $cuenta_destino);
					$resultado->execute();
					$obtener_datos_cuenta_destino = $resultado->fetch(PDO::FETCH_OBJ);

					$restar_cuenta = $obtener_datos_cuenta->dinero-$retiro;///Hacer resta
					$time_start = microtime(true);
					$sql = "UPDATE cuentas SET dinero = :restar WHERE numero_cuenta = :num_cuenta";
					$resultado=$base->prepare($sql);
					$resultado->bindValue(":restar", $restar_cuenta);
					$resultado->bindValue(":num_cuenta", $obtener_datos_cuenta->numero_cuenta);
					$resultado->execute();

					/////Sumar a cuenta destino
					$sql=  "UPDATE cuentas SET dinero = :sumar WHERE numero_cuenta = :num_cuent"
					$resultado=$base->prepare($sql);
					$resultado->bindValue(":sumar", $restar);
					$resultado->bindValue(":num_cuenta", $obtener_datos_cuenta_destino->numero_cuenta);
					$resultado->execute();


					$time_end = microtime(true);
					$tiempo_debito_retiro_promedio = $tiempo_debito_retiro_promedio+($time_start-$time_end);

					///Insertar en la tabla de historial
					$sql = "INSERT INTO historial_cliente (cuenta_id,descripcion,numero_rastreo,cantidad,sitio,tipo_transferencia_id,cuenta_destino,deleted) VALUES (:cuenta_id,:descripcion,:numero_rastreo,:cantidad,	:sitio,:tipo_transferencia_id,:cuenta_destino,:deleted)";


					//Historia cliente
					$rastreo = rand(100,10000);
					$resultado=$base->prepare($sql);
					$resultado->bindValue(":cuenta_id", $obtener_datos_cuenta->cuenta_id, PDO::PARAM_INT);
					$resultado->bindValue(":descripcion", utf8_encode("Transacción"), PDO::PARAM_STR);
					$resultado->bindValue(":numero_rastreo", $rastreo, PDO::PARAM_INT);
					$resultado->bindValue(":cantidad", $retiro, PDO::PARAM_STR);
					$resultado->bindValue(":sitio","Banco Fortuna", PDO::PARAM_STR);
					$resultado->bindValue(":tipo_transferencia_id", 3, PDO::PARAM_INT);
					$resultado->bindValue(":cuenta_destino", $->numero_cuenta, PDO::PARAM_STR);
					$resultado->bindValue(":deleted", true, PDO::PARAM_BOOL);
					$resultado->execute();




				}else{///// Es de credito

					if($obtener_datos_cuenta->dinero > $retiro){
						$restar_cuenta = $obtener_datos_cuenta->dinero-$retiro;///Hacer resta
						$time_start = microtime(true);
						$sql = "UPDATE cuentas SET dinero = :restar WHERE numero_cuenta = :num_cuenta";
						$resultado=$base->prepare($sql);
						$resultado->bindValue(":restar", $restar_cuenta);
						$resultado->bindValue(":num_cuenta", $obtener_datos_cuenta->numero_cuenta);
						$resultado->execute();
						$time_end = microtime(true);
						$tiempo_credito_retiro_promedio = $tiempo_credito_retiro_promedio+($time_start-$time_end);
					}
					
				}



				/////Deposito a tarjeta
			}else if($operation_realizar == 2){
				$deposito = rand(1000,10000);
				if($obtener_datos_cuenta->dinero > 0){
					if($obtener_datos_cuenta->nombre_cuenta == 'Debito'){
						//// Cuenta que deposita
						$cuenta_deposito = rand(1,10);

						$sql= "SELECT numero_cuenta, dinero FROM cuentas WHERE cliente_id = :cliente_id";
						$resultado=$base->prepare($sql);
						$resultado->bindValue(":cliente_id", $cuenta_deposito);
						$resultado->execute();
						$obtener_dato_deposito_cuenta = $resultado->fetch(PDO::FETCH_OBJ);

						////Depositos de cuenta
						
						

						if($obtener_dato_deposito_cuenta->dinero > $deposito){
							///Restar valor
							$time_start = microtime(true);
							$resta_deposito = $obtener_dato_deposito_cuenta->dinero+$deposito;
							$sql1 = "UPDATE cuentas SET dinero = :sumar WHERE numero_cuenta = :num_cuenta";
							$resultado=$base->prepare($sql1);
							$resultado->bindValue(":sumar", $resta_deposito);
							$resultado->bindValue(":num_cuenta",  $obtener_dato_deposito_cuenta->numero_cuenta);
							$resultado->execute();
							///Sumar la cuenta
							$sumar_credito = $obtener_datos_cuenta->dinero-$deposito;
							$sql = "UPDATE cuentas SET dinero = :restar WHERE numero_cuenta = :num_cuenta";
							$resultado=$base->prepare($sql);
							$resultado->bindValue(":restar", $sumar_credito);
							$resultado->bindValue(":num_cuenta", $obtener_datos_cuenta->numero_cuenta);
							$resultado->execute();
							$time_end = microtime(true);
							$tiempo_debito_deposito_promedio = $tiempo_debito_deposito_promedio + ($time_start-$time_end);


							$sql = "INSERT INTO historial_cliente (cuenta_id,descripcion,numero_rastreo,cantidad,sitio,tipo_transferencia_id,cuenta_destino,deleted) VALUES (:cuenta_id,:descripcion,:numero_rastreo,:cantidad,	:sitio,:tipo_transferencia_id,:cuenta_destino,:deleted)";


							//Historia Insertar a cuenta que deposita
							$rastreo = rand(100,10000);
							$resultado=$base->prepare($sql);
							$resultado->bindValue(":cuenta_id", $obtener_datos_cuenta->cuenta_id, PDO::PARAM_INT);
							$resultado->bindValue(":descripcion", utf8_encode("Deposito"), PDO::PARAM_STR);
							$resultado->bindValue(":numero_rastreo", $rastreo, PDO::PARAM_INT);
							$resultado->bindValue(":cantidad", $deposito, PDO::PARAM_STR);
							$resultado->bindValue(":sitio","Banco Fortuna", PDO::PARAM_STR);
							$resultado->bindValue(":tipo_transferencia_id", 2, PDO::PARAM_INT);
							$resultado->bindValue(":cuenta_destino",$obtener_dato_deposito_cuenta->numero_cuenta, PDO::PARAM_STR);
							$resultado->bindValue(":deleted", true, PDO::PARAM_BOOL);
							$resultado->execute();



							$sql = "INSERT INTO historial_cliente (cuenta_id,descripcion,numero_rastreo,cantidad,sitio,tipo_transferencia_id,cuenta_destino,deleted) VALUES (:cuenta_id,:descripcion,:numero_rastreo,:cantidad,	:sitio,:tipo_transferencia_id,:cuenta_destino,:deleted)";


							//Historia cliente
							$rastreo = rand(100,10000);
							$resultado=$base->prepare($sql);
							$resultado->bindValue(":cuenta_id", $obtener_datos_cuenta->cuenta_id, PDO::PARAM_INT);
							$resultado->bindValue(":descripcion", utf8_encode("Deposito"), PDO::PARAM_STR);
							$resultado->bindValue(":numero_rastreo", $rastreo, PDO::PARAM_INT);
							$resultado->bindValue(":cantidad", $deposito, PDO::PARAM_STR);
							$resultado->bindValue(":sitio","Banco Fortuna", PDO::PARAM_STR);
							$resultado->bindValue(":tipo_transferencia_id", 2, PDO::PARAM_INT);
							$resultado->bindValue(":cuenta_destino",$obtener_dato_deposito_cuenta->numero_cuenta, PDO::PARAM_STR);
							$resultado->bindValue(":deleted", true, PDO::PARAM_BOOL);
							$resultado->execute();
						}else{
							////Significa que no tiene saldo la tarjeta que quiere depositar
						}
						
					}else{
						////Tarjeta de credito
						//// Cuenta que deposita
						$cuenta_deposito = rand(1,10);

						$sql= "SELECT numero_cuenta, dinero FROM cuentas WHERE cliente_id = :cliente_id";
						$resultado=$base->prepare($sql);
						$resultado->bindValue(":cliente_id", $cuenta_deposito);
						$resultado->execute();
						$obtener_dato_deposito_cuenta = $resultado->fetch(PDO::FETCH_OBJ);

						////Depositos de cuenta
						
						

						if($obtener_dato_deposito_cuenta->dinero > $deposito){
							///Restar valor
							$time_start = microtime(true);
							$resta_deposito = $obtener_dato_deposito_cuenta->dinero-$deposito;
							$sql1 = "UPDATE cuentas SET dinero = :restar WHERE numero_cuenta = :num_cuenta";
							$resultado=$base->prepare($sql1);
							$resultado->bindValue(":restar", $resta_deposito);
							$resultado->bindValue(":num_cuenta",  $obtener_dato_deposito_cuenta->numero_cuenta);
							$resultado->execute();
							///Sumar la cuenta
							$sumar_credito = $obtener_datos_cuenta->dinero+$deposito;
							$sql = "UPDATE cuentas SET dinero = :sumar WHERE numero_cuenta = :num_cuenta";
							$resultado=$base->prepare($sql);
							$resultado->bindValue(":sumar", $sumar_credito);
							$resultado->bindValue(":num_cuenta", $obtener_datos_cuenta->numero_cuenta);
							$resultado->execute();
							$time_end = microtime(true);
							$tiempo_debito_deposito_promedio = $tiempo_debito_deposito_promedio + ($time_start-$time_end);





						}else{
							////Significa que no tiene saldo la tarjeta que quiere depositar	
						}
					}
				}
			}

		}
	}catch(PDOException $e){
		print $e->getMessage();
	}
	

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="refresh" content="500">
	<title>Clientes</title>
</head>
<body>
	<h2>Ejecutando clientes :)</h2>
</body>
</html>