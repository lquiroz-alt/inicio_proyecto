<?php
	require_once 'login_mysql.php';
	try{
		//if($_POST["nombre_usuario"] == '' || $_POST['contrasenia'] == '' || $_POST["correo"] == '' || $_POST["tipo_cuenta"] == '' || $_POST['cantidad_deposito'] == ''){
			//header("location: registro_usuario.php");
		//}else{
			$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
			$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$correo = $_POST["correo"];

			$sql = "SELECT * FROM usuarios WHERE email = :email";
			$resultado=$base->prepare($sql);
			$resultado->bindValue(":email", $correo, PDO::PARAM_STR);
			$resultado->execute();
			$validar_email_existe = (int)$resultado->rowCount();

			if($validar_email_existe > 0){
				$resultado=null;
				header("location: registro_usuario.php");
			}else{
				$sql = "SELECT * FROM usuarios";
				$resultado=$base->prepare($sql);
				$resultado->execute();
				$numero_registro=$resultado->rowCount()+1;

				$nombre = $_POST["nom_usuario"];
				$contra = $_POST["contrasenia"];
				$clave = rand(100,999);
				$cantidad_deposito = $_POST['cantidad_deposito'];
				$tarjeta = "12345678".rand(1000,9999)."".rand(1000,9999);
				$tipo_cuenta = $_POST['tipo_cuenta'];
				$verificar = true;
				$hoy = getdate();
				$sql = "INSERT INTO usuarios (nombre,email,password_cliente,deleted) VALUES (:nombre, :email,:password_cliente,:deleted)";
			
				$resultado=$base->prepare($sql);
				$resultado->bindValue(":nombre", $nombre, PDO::PARAM_STR);
				$resultado->bindValue(":email", $correo, PDO::PARAM_STR);
				$resultado->bindValue(":password_cliente", $contra, PDO::PARAM_STR);
				$resultado->bindValue(":deleted", $verificar, PDO::PARAM_BOOL);
				$resultado->execute();



				$sql = "INSERT INTO cuentas (cliente_id,tipo_cuenta_id, numero_cuenta, mes, anio, dinero, clave, deleted) VALUES ( :cliente_id,:tipo_cuenta_id, :numero_cuenta, :mes, :anio,:dinero, :clave,:deleted)";
				$resultado=$base->prepare($sql);
				$resultado->bindValue(":cliente_id", $numero_registro, PDO::PARAM_INT);
				$resultado->bindValue(":tipo_cuenta_id", $tipo_cuenta, PDO::PARAM_INT);
				$resultado->bindValue(":numero_cuenta", $tarjeta, PDO::PARAM_STR);
				$resultado->bindValue(":mes", 05, PDO::PARAM_INT);
				$resultado->bindValue(":anio", 20, PDO::PARAM_INT);
				$resultado->bindValue(":dinero", $cantidad_deposito, PDO::PARAM_STR);
				$resultado->bindValue(":clave", $clave, PDO::PARAM_INT);
				$resultado->bindValue(":deleted", $verificar, PDO::PARAM_BOOL);
				$resultado->execute();
			

				$resultado=null;
				header("location: index.php");
			}
		//}

	}catch(PDOException $e){
		print $e->getMessage();
	}
	
?>
