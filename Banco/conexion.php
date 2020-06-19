<?php
	
	$mysqli=new mysqli('localhost',"User_joel","745813J745813","banco"); //servidor, usuario de base de datos, contraseña del usuario, nombre de base de datos
	
	if(mysqli_connect_errno()){
		echo 'Conexion Fallida : ', mysqli_connect_error();
		exit();
	}


?>