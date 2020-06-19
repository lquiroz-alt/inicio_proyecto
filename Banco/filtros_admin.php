<?php
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}
	require_once 'login_mysql.php';
	try{
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$_SESSION["tipo_cuenta_filtros_admin"] = $_POST["tipo_cuenta"];

		echo true;


	}catch(PDOException $e){
		print $e->getMessage();
	}
?>