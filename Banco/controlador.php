<?php
	require_once 'login_mysql.php';
	if(isset($_POST['cuenta'])){
		try{
			$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
			$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT * FROM usuarios WHERE email = :usuario AND password_cliente = :contra";
			$resultado=$base->prepare($sql);
			$usuario = htmlentities(addslashes($_POST['usuario']));
			$contra = htmlentities(addslashes($_POST['contra']));
			
			$resultado->bindValue(":usuario", $usuario);
			$resultado->bindValue(":contra", $contra);

			$resultado->execute();
			$numero_registro=$resultado->rowCount();
			
			if($numero_registro != 0){
				session_start();
				$_SESSION["usuario"] = $_POST["usuario"];
				$_SESSION["nivel"] = 0;
				if($usuario == 'admin_banco@fortuna.com'){
					$_SESSION["nivel"] = 1;
 				}
				header("location: sesion_usuario.php");
			}else{
				
				header("location: index.php");
			}

			
		}catch(PDOException $e){
			die("ERROR: ". $e->getMessage());
		}
	}else{
		header("location: index.php");
	}
	
?>