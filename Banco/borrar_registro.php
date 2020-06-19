<?php
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}
	require_once 'login_mysql.php';
	$id_historial = $_POST["historial"];
	try{
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "DELETE FROM historial_cliente WHERE historial_id = :historia";

		$resultado=$base->prepare($sql);
		$resultado->bindValue(":historia",$id_historial);
		$resultado->execute();

		echo json_encode(1);
	}catch(PDOException $e){
		print $e->getMessage();
	}


?>