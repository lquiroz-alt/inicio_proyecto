<?php
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}
	require_once 'login_mysql.php';
	try{
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if(isset($_POST["id_cliente"])){
			$id_cliente = $_POST["id_cliente"];
		}else{
			$id_cliente = null;
		}

		if($id_cliente != null){
			$sql = "DELETE FROM cuentas WHERE cuenta_id = :cuenta_id";

			$resultado=$base->prepare($sql);
			$resultado->bindValue(":cuenta_id", $id_cliente);
			$resultado->execute();

			echo true;
		}else{
			echo false;
		}


	}catch(PDOException $e){
		print $e->getMessage();
	}


?>