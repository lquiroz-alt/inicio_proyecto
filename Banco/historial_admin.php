<?php
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}
	require_once 'login_mysql.php';
	try{
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		

		if(isset($_SESSION["tipo_cuenta_filtros_admin"]) && $_SESSION["tipo_cuenta_filtros_admin"] > 0){
			/////Filtrados
			$sql = "SELECT h.historial_id, c.numero_cuenta, h.descripcion, h.numero_rastreo, h.cantidad,t.nombre_transferencia, h.sitio,h.tiempo_registro FROM `historial_cliente` h
			INNER JOIN `cuentas` c ON c.cuenta_id = h.cuenta_id
			INNER JOIN `tipo_transferencia` t ON t.tipo_transferencia_id = h.tipo_transferencia_id
			LEFT JOIN usuarios u ON u.cliente_id = c.cliente_id
			WHERE c.tipo_cuenta_id = :tipo_cuenta_id";

			$resultado=$base->prepare($sql);
			$resultado->bindValue(":tipo_cuenta_id", $_SESSION['tipo_cuenta_filtros_admin']);
			$resultado->execute();
			$linea_tabla = array();
			$index_t = 0;
			$suma_totales = 0;
			while($fila = $resultado->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)){
				$formato_cantidad = number_format($fila[4],2,'.',',');
				$suma_totales = $fila[4]+$suma_totales;
				$linea_tabla[$index_t] = "<tr class='renglon_historial'><td class='renglon'>".$fila[0]."</td><td class='renglon'>".$fila[1]."</td><td class='renglon'>".$fila[2]."</td><td class='renglon'>".$fila[3]."</td><td class='renglon'>$ ".$formato_cantidad."</td><td class='renglon'>".$fila[5]."</td><td class='renglon'>".$fila[6]."</td><td class='renglon'>".$fila[7]."</td></td><td class='renglon'><button id='borrar'".$index_t." class='boton_formato' onclick='borrar_registro(".$fila[0].");'>Borrar</button></td></tr>";
				$index_t++;
			}
			$formato_cantidad = number_format($suma_totales,2,'.',',');
			$_SESSION['tipo_cuenta_filtros_admin'] = 0;
			$resultado=null;
		}else{
			////Todos
			$sql = "SELECT h.historial_id, c.numero_cuenta, h.descripcion, h.numero_rastreo, h.cantidad, t.nombre_transferencia, h.sitio, h.tiempo_registro FROM `historial_cliente` h
			INNER JOIN `cuentas` c ON c.cuenta_id = h.cuenta_id
			INNER JOIN `tipo_transferencia` t ON t.tipo_transferencia_id = h.tipo_transferencia_id
			LEFT JOIN usuarios u ON u.cliente_id = c.cliente_id";

			$resultado=$base->prepare($sql);
			$resultado->execute();
			$linea_tabla = array();
			$index_t = 0;
			$suma_totales = 0;
			while($fila = $resultado->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)){
				$formato_cantidad = number_format($fila[4],2,'.',',');
				$suma_totales = $fila[4]+$suma_totales;
				$linea_tabla[$index_t] = "<tr class='renglon_historial'><td class='renglon'>".$fila[0]."</td><td class='renglon'>".$fila[1]."</td><td class='renglon'>".$fila[2]."</td><td class='renglon'>".$fila[3]."</td><td class='renglon'>$ ".$formato_cantidad."</td><td class='renglon'>".$fila[5]."</td><td class='renglon'>".$fila[6]."</td><td class='renglon'>".$fila[7]."</td></td><td class='renglon'><button id='borrar'".$index_t." class='boton_formato' onclick='borrar_registro(".$fila[0].");'>X</button></td></tr>";
				$index_t++;
				}
			$formato_cantidad = number_format($suma_totales,2,'.',',');
			$resultado=null;
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
	<link rel="stylesheet" type="text/css" href="">
	<script type="text/javascript" src="lib/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="lib/jquery/jquery-migrate.min.js"></script>
	<script type="text/javascript" src="js/footable.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/footable.standalone.css">
	<link rel="stylesheet" type="text/css" href="css/footable.standalone.min.css">
	<script type="text/javascript" src="script_tabla.js"></script>
	<link rel="stylesheet" type="text/css" href="formato_historial.css">
	<link rel="stylesheet" type="text/css" href="formato_sesion.css">
	<title>Historial - <?php echo $_SESSION['nombre']; ?></title>
</head>
<body>
	<header id="header_historial">
		<img src="Banner_Fortuna.png" width="1300" height="200">
	</header>
	<main>
		<div id="menu2">
			<?php 
				if($_SESSION["nivel"] == 1){
					echo "<ul>
						<li><a class='item_menu' href='sesion_usuario.php'>Saldos</a></li>
						<li><a class='item_menu' href='historial_admin.php'>Historial admin</a></li>
						<li><a class='item_menu' href='excel_historial_admin.php'>Descargar en Excel</a></li>
						<li>
							<a class='item_menu' href='pdf_historial_admin.php'>Descargar en PDF</a>
						</li>
						<li>
							<a class='item_menu' href='log_administrador.php'>Log</a>
						</li>
					</ul>";
				}else{
					echo "<ul>
						<li><a class='item_menu' href='sesion_usuario.php'>Saldos</a></li>
						<li><a class='item_menu' href='historial.php'>Historial</a></li>
						<li><a class='item_menu' href='excel_historial.php'>Descargar en Excel</a></li>
						<li>
							<a class='item_menu' href='pdf_historial.php'>Descargar en PDF</a>
						</li>
						<li><a class='item_menu' href='cierre_sesion.php'>Cerrar Sesión</a></li>
					</ul>";
				}
			 ?>	
		</div>
		<div id="filtros" class="filtros_class">
			<input type="radio" name="tipo_cuenta" id="debito" class="radio_sexo" value="1" ><label for="debito" class="label_sexo">Debito</label>
			<input type="radio" name="tipo_cuenta" id="credito" class="radio_sexo" value="2" ><label for="credito" class="label_sexo">credito</label>
			<input type="radio" name="tipo_cuenta" id="ahorro" class="radio_sexo" value="3" ><label for="ahorro" class="label_sexo">Cuenta de ahorro</label>
			<input type="radio" name="tipo_cuenta" id="todo" class="radio_sexo" value="0" ><label for="todo" class="label_sexo">Todos</label>
			<button id="nombre" onclick="filtrar_admin();">Filtrar</button>
		</div>
		<div id="tabla_historial">
			<table class="footable">
				<thead>
					<tr>
						<th>ID Historial</th>
						<th>Numero de cuenta</th>
						<th>Descripción</th>
						<th>Numero de rastreo</th>
						<th>Cantidad</th>
						<th>Tipo de transferencia</th>
						<th>Sitio</th>
						<th>Fecha</th>
						<th>Acción</th>
					</tr>
				</thead>
				<tbody><?php for($aux_i = 0; $aux_i < count($linea_tabla); $aux_i++){echo $linea_tabla[$aux_i];} ?></tbody>
				<tfoot id="clase_footer">
					<tr>
						<td></td> 
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td>Totales: </td>
						<td><?php echo $formato_cantidad; ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
			
	</main>
	<footer>
		<address>Leonardo Quiroz y Fernando Aguilar</address>

	</footer>

</body>
</html>