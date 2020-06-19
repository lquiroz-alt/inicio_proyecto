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
		
		if(isset($_POST["tipo_cuenta"]) && isset($_POST["cantidad"]) && isset($_POST["numero_cuenta"]) ){
			$tipo_cuenta = $_POST["tipo_cuenta"];
			$numero_cuenta = $_POST["numero_cuenta"];
			$cantidad = $_POST["cantidad"];
		}else{
			$tipo_cuenta = null;
			$numero_cuenta = null;
			$cantidad = null;
		}
	

 		$verificar_cuenta_capi = substr($cuentau, 0, 1);
 		if($verificar_cuenta_capi != '4'){

			if($numero_cuenta != null || $cantidad != null || $tipo_cuenta != null){

				if($tipo_cuenta == 2){
					///Cuenta credito
					$sql = "SELECT tipo_cuenta_id FROM cuentas WHERE numero_cuenta = :num_cuenta LIMIT 1";
					$resultado=$base->prepare($sql);
					$resultado->bindValue(":num_cuenta", $numero_cuenta);
					$resultado->execute();
					$cuenta_credito = $resultado->fetch(PDO::FETCH_OBJ);
					$resultado = null;


					$verificar = true;
					$sql = "SELECT cuenta_id, dinero FROM cuentas WHERE numero_cuenta = :num_cuenta LIMIT 1";
					$resultado=$base->prepare($sql);
					$resultado->bindValue(":num_cuenta", $_SESSION['numero_cuenta']);
					$resultado->execute();
					$cuenta_debito = $resultado->fetch(PDO::FETCH_OBJ);

					if($cuenta_debito->dinero > $cantidad && $cuenta_credito->tipo_cuenta_id == 2){
						$sql = "SELECT c.numero_cuenta, c.dinero FROM cuentas c
						INNER JOIN usuarios u ON u.cliente_id = c.cliente_id
						WHERE c.tipo_cuenta_id = 2 AND u.email = :email LIMIT 1";

						$resultado=$base->prepare($sql);
						$resultado->bindValue(":email", $usuario_sesion);
						$resultado->execute();
						$obtener_datos_cliente = $resultado->fetch(PDO::FETCH_OBJ);

						$restar_debito = $_SESSION['dinero']-$cantidad;

						$sumar_credito = $obtener_datos_cliente->dinero+$cantidad;

						$sql = "UPDATE cuentas SET dinero = :resta WHERE numero_cuenta = :num_cuenta";
						$resultado=$base->prepare($sql);
						$resultado->bindValue(":resta", $restar_debito);
						$resultado->bindValue(":num_cuenta", $_SESSION['numero_cuenta']);

						$resultado->execute();

						$resultado = null;

						$sql = "UPDATE cuentas SET dinero = :suma WHERE numero_cuenta = :num_cuenta";
						$resultado=$base->prepare($sql);
						$resultado->bindValue(":suma", $sumar_credito);
						$resultado->bindValue(":num_cuenta", $numero_cuenta);

						$resultado->execute();

					$sql = "INSERT INTO historial_cliente (cuenta_id,descripcion,numero_rastreo,cantidad,sitio,tipo_transferencia_id,cuenta_destino,deleted) VALUES (:cuenta_id,:descripcion,:numero_rastreo,:cantidad,	:sitio,:tipo_transferencia_id,:cuenta_destino,:deleted)";


						$resultado=$base->prepare($sql);
						$resultado->bindValue(":cuenta_id", $cuenta_debito->cuenta_id, PDO::PARAM_INT);
						$resultado->bindValue(":descripcion", "Pago a tarjeta de credito", PDO::PARAM_STR);
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
				}else if($tipo_cuenta == 1){
					///cuenta debito
					$sql = "SELECT c.cuenta_id, c.tipo_cuenta_id, c.numero_cuenta, c.dinero FROM cuentas c
					LEFT JOIN  usuarios u ON u.cliente_id = c.cliente_id
					WHERE c.tipo_cuenta_id = 2 AND u.nombre = :nombre LIMIT 1";
					$resultado=$base->prepare($sql);
					$resultado->bindValue(":nombre", $_SESSION['nombre']);
					$resultado->execute();
					$cuenta_credito = $resultado->fetch(PDO::FETCH_OBJ);
					$resultado = null;


					$verificar = true;
					$sql = "SELECT cuenta_id, tipo_cuenta_id, dinero FROM cuentas WHERE numero_cuenta = :num_cuenta LIMIT 1";
					$resultado=$base->prepare($sql);
					$resultado->bindValue(":num_cuenta", $_SESSION['numero_cuenta']);
					$resultado->execute();
					$cuenta_debito = $resultado->fetch(PDO::FETCH_OBJ);

					if($cuenta_debito->tipo_cuenta_id == 1 && $cuenta_credito->tipo_cuenta_id == 2 && $cuenta_credito->dinero > $cantidad){
						

						$restar_credito = $cuenta_credito->dinero-$cantidad;

						$sumar_debito = $cuenta_debito->dinero+$cantidad;

						$sql = "UPDATE cuentas SET dinero = :resta WHERE numero_cuenta = :num_cuenta";
						$resultado=$base->prepare($sql);
						$resultado->bindValue(":resta", $restar_credito);
						$resultado->bindValue(":num_cuenta", $cuenta_credito->numero_cuenta);

						$resultado->execute();

						$resultado = null;

						$sql = "UPDATE cuentas SET dinero = :suma WHERE numero_cuenta = :num_cuenta";
						$resultado=$base->prepare($sql);
						$resultado->bindValue(":suma", $sumar_debito);
						$resultado->bindValue(":num_cuenta", $_SESSION["numero_cuenta"]);

						$resultado->execute();

					$sql = "INSERT INTO historial_cliente (cuenta_id,descripcion,numero_rastreo,cantidad,sitio,tipo_transferencia_id,cuenta_destino,deleted) VALUES (:cuenta_id,:descripcion,:numero_rastreo,:cantidad,	:sitio,:tipo_transferencia_id,:cuenta_destino,:deleted)";


						$resultado=$base->prepare($sql);
						$resultado->bindValue(":cuenta_id", $cuenta_credito->cuenta_id, PDO::PARAM_INT);
						$resultado->bindValue(":descripcion", "Deposito a debito", PDO::PARAM_STR);
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
				}else{
					//Cuenta ahorro
					$sql = "SELECT c.cuenta_id, c.tipo_cuenta_id, c.numero_cuenta, c.dinero FROM cuentas c
					LEFT JOIN  usuarios u ON u.cliente_id = c.cliente_id
					WHERE c.tipo_cuenta_id = 2 AND u.nombre = :nombre LIMIT 1";
					$resultado=$base->prepare($sql);
					$resultado->bindValue(":nombre", $_SESSION['nombre']);
					$resultado->execute();
					$cuenta_credito = $resultado->fetch(PDO::FETCH_OBJ);
					$resultado = null;


					$verificar = true;
					$sql = "SELECT cuenta_id, tipo_cuenta_id, dinero FROM cuentas WHERE numero_cuenta = :num_cuenta LIMIT 1";
					$resultado=$base->prepare($sql);
					$resultado->bindValue(":num_cuenta", $_SESSION['numero_cuenta']);
					$resultado->execute();
					$cuenta_ahorro = $resultado->fetch(PDO::FETCH_OBJ);

					if($cuenta_ahorro->tipo_cuenta_id == 3 && $cuenta_credito->tipo_cuenta_id == 2 && $cuenta_credito->dinero > $cantidad){
						

						$restar_credito = $cuenta_credito->dinero-$cantidad;

						$sumar_ahorro = $cuenta_ahorro->dinero+$cantidad;

						$sql = "UPDATE cuentas SET dinero = :resta WHERE numero_cuenta = :num_cuenta";
						$resultado=$base->prepare($sql);
						$resultado->bindValue(":resta", $restar_credito);
						$resultado->bindValue(":num_cuenta", $cuenta_credito->numero_cuenta);

						$resultado->execute();

						$resultado = null;

						$sql = "UPDATE cuentas SET dinero = :suma WHERE numero_cuenta = :num_cuenta";
						$resultado=$base->prepare($sql);
						$resultado->bindValue(":suma", $sumar_ahorro);
						$resultado->bindValue(":num_cuenta", $_SESSION["numero_cuenta"]);

						$resultado->execute();

					$sql = "INSERT INTO historial_cliente (cuenta_id,descripcion,numero_rastreo,cantidad,sitio,tipo_transferencia_id,cuenta_destino,deleted) VALUES (:cuenta_id,:descripcion,:numero_rastreo,:cantidad,	:sitio,:tipo_transferencia_id,:cuenta_destino,:deleted)";


						$resultado=$base->prepare($sql);
						$resultado->bindValue(":cuenta_id", $cuenta_credito->cuenta_id, PDO::PARAM_INT);
						$resultado->bindValue(":descripcion", "Deposito a cuenta de ahorro", PDO::PARAM_STR);
						$resultado->bindValue(":numero_rastreo", rand(1,100000), PDO::PARAM_INT);
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
				}
			}else{	
				echo false;
			}
		}else{

			$url = "192.168.23.3/buscarusuario/".$cuentau."/".$monto."/"."hola"."/"."hola2";
			$ch = curl_init($url);
			$resultado = curl_setopt($ch, CURLOPT_HTTPGET, 1);

		
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$resultado = curl_exec($ch);
			curl_close($ch);
			echo $resultado;
			echo $resultado;
			echo $resultado;
		}	
	}catch(PDOException $e){
		print $e->getMessage();
	}
?>