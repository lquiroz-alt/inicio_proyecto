<?php
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}
	require_once 'login_mysql.php';
	try{
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$nivel = $_SESSION["nivel"];
		if($nivel == 1){
			///ADMINISTRADOR
			$usuario_sesion = $_SESSION["usuario"];
			$sql = "SELECT u.nombre, c.numero_cuenta, c.dinero, t.nombre_cuenta, c.cuenta_id  
			FROM usuarios u
			INNER JOIN cuentas c
			ON u.cliente_id = c.cliente_id
			INNER JOIN tipo_cuenta t
			ON c.tipo_cuenta_id = t.tipo_cuenta_id";

			$resultado2=$base->prepare($sql);
			$resultado2->execute();


			$sql2 = "SELECT nombre FROM usuarios WHERE nombre = 'admin' LIMIT 1";
			$resultado=$base->prepare($sql2);
			$resultado->execute();
			$obtener_datos_cliente = $resultado->fetch(PDO::FETCH_OBJ);
			$linea_tabla = array();
			$index_t = 0;
			while($fila = $resultado2->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)){
				$formato_cantidad = number_format($fila[2],2,'.',',');
				$linea_tabla[$index_t] = "<tr><td class='renglon'>".$fila[0]."</td><td class='renglon'>".$fila[1]."</td><td class='renglon'>".$fila[3]."</td><td class='renglon'>".$formato_cantidad."</td><td class='renglon'><button id='borrar'".$fila[4]."  onclick='borrar_cuenta(".$fila[4].");'>X</button></td></tr>";
				$index_t++;
			}

			$resultado = null;
			$resultado2 = null;

		}else{
			///USUARIO NORMAL
			$usuario_sesion = $_SESSION["usuario"];
			$sql="SELECT u.nombre, c.numero_cuenta, c.dinero, t.nombre_cuenta, c.cuenta_id, c.clave, c.mes, c.anio FROM usuarios u
			INNER JOIN cuentas c
			ON u.cliente_id = c.cliente_id
			INNER JOIN tipo_cuenta t
			ON c.tipo_cuenta_id = t.tipo_cuenta_id
			WHERE u.email = :usuario";

			$sql2="SELECT u.nombre, c.numero_cuenta, c.dinero, t.nombre_cuenta, c.cuenta_id, c.clave, c.mes, c.anio FROM usuarios u
			INNER JOIN cuentas c
			ON u.cliente_id = c.cliente_id
			INNER JOIN tipo_cuenta t
			ON c.tipo_cuenta_id = t.tipo_cuenta_id
			WHERE u.email = :usuario AND c.tipo_cuenta_id = 1";
			$resultado=$base->prepare($sql2);
			$resultado->bindValue(":usuario", $usuario_sesion);
			$resultado->execute();
			$obtener_datos_cliente = $resultado->fetch(PDO::FETCH_OBJ);

			$resultado2=$base->prepare($sql);
			$resultado2->bindValue(":usuario", $usuario_sesion);
			$resultado2->execute();

			$linea_tabla = array();
			$index_t = 0;
			while($fila = $resultado2->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)){
				$formato_cantidad = number_format($fila[2],2,'.',',');
				$linea_tabla[$index_t] = "<tr><td class='renglon'>".$fila[1]."</td><td class='renglon'>".$fila[3]."</td><td class='renglon'>".$fila[5]."</td><td class='renglon'>".$fila[6]."</td><td class='renglon'>".$fila[7]."</td><td class='renglon'>".$formato_cantidad."</td></tr>";
				$index_t++;
			}

			
			$_SESSION['cuenta_id'] = $obtener_datos_cliente->cuenta_id;
			$_SESSION['nombre'] = $obtener_datos_cliente->nombre;
			$_SESSION['nombre_cuenta'] = $obtener_datos_cliente->nombre_cuenta;
			$_SESSION['numero_cuenta'] = $obtener_datos_cliente->numero_cuenta;
			$_SESSION['dinero'] = $obtener_datos_cliente->dinero;

			$resultado = null;
			$resultado2 = null;

		}
		


	}catch(PDOException $e){
		print $e->getMessage();
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="description" content="Historial e información de cliente">
	<meta charset="utf-8">
	<script type="text/javascript" src="lib/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="lib/jquery/jquery-migrate.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/footable.standalone.css">
	<link rel="stylesheet" type="text/css" href="css/footable.standalone.min.css">
	<script type="text/javascript" src="script.js"></script>
	<link rel="stylesheet" type="text/css" href="formato_sesion.css">
	<title>Fortuna - <?php if($nivel == 1){ echo "ADMINISTRADOR"; }else{ echo $obtener_datos_cliente->nombre; }?></title>
</head>
<body>
	<header>
		<h3 style="position:  absolute; bottom: 449px;">La fortuna esta en tus manos</h3>
		<img src="Banner_Fortuna.png" width="1300" height="200">
		<h3>Bienvenido(a): <?php if($nivel == 1){ echo "ADMINISTRADOR"; }else{ echo $obtener_datos_cliente->nombre; } ?></h3>	
	</header>
	<main>
		<div id="menu">
			<?php 
				if($nivel == 1){
					echo "<ul>
							<li><a class='item_menu' href='sesion_usuario.php'>Saldos</a></li>
							<li><a class='item_menu' href='historial_admin.php'>Historial admin</a></li>
							<li><a class='item_menu' href='log_administrador.php'>Log</a></li>
							<li><a class='item_menu' href='cierre_sesion.php'>Cerrar Sesión</a></li>
						</ul>";
				}else{
					echo "<ul>
							<li><a class='item_menu' href='sesion_usuario.php'>Saldos</a></li>
							<li><a class='item_menu' href='historial.php'>Historial</a></li>
							<li><a class='item_menu' href='abrir_credito.php'>Abrir credito</a></li>
							<li><a class='item_menu' href='cierre_sesion.php'>Cerrar Sesión</a></li>
						</ul>";
				}
			 ?>
		</div>
			<?php
				if($nivel == 1){
					echo "
					<table id='tabla_informacion'>
						<thead>
							<tr>
								<th>Cliente</th>
								<th>Numero de la cuenta</th>
								<th>Tipo de cuenta</th>
								<th>Importe</th>
								<th>Acción</th>
							</tr>
						</thead>
						<tbody>";
						for($aux_i = 0; $aux_i < count($linea_tabla); $aux_i++){echo $linea_tabla[$aux_i];}
					echo "
						</tbody>
						<tfoot>
						</tfoot>
					</table>";
				}else{
					echo "
					<table id='tabla_informacion'>
						<thead>
							<tr>
								<th>Numero de la cuenta</th>
								<th>Tipo de cuenta</th>
								<th>Clave</th>
								<th>Mes</th>
								<th>Año</th>
								<th>Importe</th>
							</tr>
						</thead>
						<tbody>";

						for($aux_i = 0; $aux_i < count($linea_tabla); $aux_i++){echo $linea_tabla[$aux_i];}
					echo "
						</tbody>
						<tfoot>
						</tfoot>
					</table>";		
				}
		?>
		</main>
		<footer>
			<div id="transferencias" class="trans_class">
				<h2 style="color:rgb(255,255,255);">Realizar una transferencia a la tarjeta de crédito o debito</h2>
				<p style="color:rgb(255,255,255); font-size: 18px;">Selecciona el tipo de cuenta:</p>
				<input type="radio" name="tipo_cuenta" id="debito" class="radio_sexo" value="1" ><label for="debito" class="label_sexo">Debito</label>
				<input type="radio" name="tipo_cuenta" id="credito" class="radio_sexo" value="2" ><label for="credito" class="label_sexo">credito</label>
				<input type="radio" name="tipo_cuenta" id="ahorro" class="radio_sexo" value="3" ><label for="ahorro" class="label_sexo">Cuenta de ahorro</label>

				<br/>
				<input type="text" id="numero_cuenta" name="numero_cuenta" placeholder="numero de cuenta" /> 
				<input type="number" id="monto" name="monto" value="0.00" /> 
				<button id="credito" onclick="transferencia_credito();">Transferir</button>
				<button id="deposito" onclick="deposito_debito();">DEPOSITO A DEBITO</button>
			</div>
		</footer>
		<address>Leonardo Quiroz y Fernando Aguilar</address>
	</body>
</html>