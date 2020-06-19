<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="description" content="Página de registro de usuario nuevo">
	<link rel="stylesheet" type="text/css" href="formato_registro.css">
	<link rel="stylesheet" type="text/css" href="formato.css">
	<script type="text/javascript" src="lib/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="lib/jquery/jquery-migrate.min.js"></script>
	<script type="text/javascript" src="validacion.js"></script>
	<title>Registro de usuario - Banco Fortuna $$$</title>
</head>
<body>
	<header>
		<img src="Banner_Fortuna.png" width="1200" height="150">
		<p id="p_iniciar_sesion"><a href="index.php">Iniciar sesión</a></p>
	</header>
	<main>
		<?php 
			if(isset($_POST['email_existe'])){
				echo "<p>El usuario ya esta registrado</p>";
			} 
		?>
		<form action="guarda_bd.php" method="POST" class="formulario" id="formulario_registro" name="form1" onsubmit="return validacion_final();">
			<input type="textbox" name="nom_usuario" placeholder="Nombre de usuario" id="nombre_usuario" class="input_registro" size="100" onkeydown="validar_nombre_usuario()" autocomplete="off" onblur="limpiar_campo()"><p id="mensaje_nombre"></p><br/>
			<input type="password" name="contrasenia" placeholder="Contraseña" id="contra" class="input_registro" size="100" onkeypress="contra_validacion()"><p id="mensaje_contra"></p><br/>
			<input type="password" name="contrasenia2" placeholder="Repita contraseña" id="contra2" class="input_registro" size="100" onchange="contra_iguales(this.value)" onkeypress="contra2_validacion()"><p id="mensaje_contra2"></p><br/>
			<input type="email" name="correo" placeholder="correo electrónico" id="email_usuario" class="input_registro" size="100" onkeydown="validacion_email()"><p id="mensaje_email"></p><br/>
			<input type="number" name="cantidad_deposito" placeholder="Deposito" id="cantidad_deposito" class="input_registro" size="100" max="50000" min="500">
			<fieldset id="fieldset_registro">
				<legend id="legend_sexo">Seleccione el tipo de cuenta</legend>
				<input type="radio" name="tipo_cuenta" id="sm" class="radio_sexo" value="1" ><label for="sm" class="label_sexo">Debito</label>
				<input type="radio" name="tipo_cuenta" id="sh" class="radio_sexo" value="3" ><label for="sh" class="label_sexo">Cuenta de ahorro</label>
				<br/>
			</fieldset>
			<input type="submit" name="registro" value="Registrarse" id="boton_registro">
		</form>
	</main>
	<footer>
		
	</footer>

</body>
</html>