<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="description" content="Página de inicio de sesión">
	<link rel="stylesheet" type="text/css" href="formato.css">
	<link rel="stylesheet" type="text/css" href="formato_registro.css">
	<script type="text/javascript" src="validacion.js"></script>
	<script type="text/javascript" src="lib/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="lib/jquery/jquery-migrate.min.js"></script>
	<title>BANCO FORTUNA $$$ - Inicio</title>
</head>
<body>
	<header id="encabezado_inicio">
		<img src="Banner_Fortuna.png" width="1300" height="200">
	</header>
	<main>
		<div id="division_formulario">
			<form action="controlador.php" method="POST" class="formulario">
				<fieldset id="fiel_user">
					<legend>Ingresa a tu cuenta</legend>
					<div id="div_user">
						<label for="usuario">Usuario</label><br/><input type="textbox" name="usuario" id="email_usuario" placeholder="nombre_usuario" size="40" onkeydown="validacion_email()" autocomplete="off">
						<p id="mensaje_email"></p>
						<br/>
						<label for="contra">Contraseña</label><br/><input type="password" name="contra" id="contra" placeholder="contraseña" size="40" autocomplete="off" ><br/>
						<p id="mensaje_contra"></p>
						<input type="submit" name="cuenta" value="mi cuenta">
					</div>
				</fieldset>
				
			</form>
			<a href="registro_usuario.php">¿Aun no tienes cuenta?</a>
		</div>
			<section id="informacion">
				<h3>Banco Fortuna </h3>
				<p> Banco Fortuna es una institución de banca múltiple con sede en la Ciudad de México, integrante de Grupo Financiero Banamex, la cual es subsidiaria de Citicorp Holdings, la que, a su vez, es subsidiaria indirecta de Citigroup.1​ Es el segundo mayor banco en México, con una participación en el mercado de 18.5% en activos, 16.7% en cartera de crédito y 17% en depósitos bancarios.5​6​
				Su creación en 1884, constituye el surgimiento del primer gran banco privado en México7​ con funciones de banco de Estado y banco comercial:5​ fungía como agente del gobierno federal en la negociación y recontratación de deuda externa y el cobro de obligaciones fiscales, a la vez que realizaba emisiones de papel moneda —actividad concesionada a la banca comercial ante la ausencia de un banco central emisor—, captaba el ahorro del público y otorgaba financiamientos.
				Luego de un periodo de estatización bancaria en los años ochenta, Banamex fue adquirido en 1991 por Acciones y Valores de México (Accival), encabezada por Roberto Hernández Ramírez y Alfredo Harp Helú, integrándose el Grupo Financiero Banamex-Accival (Banacci). En 2001, Banacci es vendido a Citigroup y fusionado con las subsidiarias de Grupo Financiero Citibank con presencia en México, con lo que se conforma el Grupo Financiero Banamex —su actual denominación— del cual Banco Nacional de México realiza las operaciones de banca y crédito.1</p>
		</section>
		<div id="imagen_div">
			<img src="tarjeta.jpg" width="400" height="200">
		</div>
		
	</main>
	<footer>
		
	</footer>


</body>
</html>