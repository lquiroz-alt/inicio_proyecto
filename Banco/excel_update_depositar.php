<?php
	session_start();
	require_once 'login_mysql.php';
	//Incluimos librería y archivo de conexión
	require 'Classes/PHPExcel.php';
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}

	try{
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$usuario_sesion = $_SESSION["usuario"];
		$P_tiempo = 20;
		$fila = 16; //Establecemos en que fila inciara a imprimir los datos
		$sql = "SELECT fecha, operacion, tiempo_operacion, cuenta, tipo_transaccion, accion, respuesta FROM log WHERE accion='ACTUALIZAR' AND tipo_transaccion = 'Deposito'";
		
		$resultado2=$base->prepare($sql);
		$resultado2->execute();
		$num_transacciones = $resultado2->rowCount();
		$gdImage = imagecreatefrompng('Banner_Fortuna.png');//Logotipo
	
		//Objeto de PHPExcel
		$objPHPExcel  = new PHPExcel();
		
		//Propiedades de Documento
		$objPHPExcel->getProperties()->setCreator("FERNANDO AGUILAR")->setDescription("LOG de UPDATE - Depositos");
		
		//Establecemos la pestaña activa y nombre a la pestaña
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle("log");
		
		$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
		$objDrawing->setName('Logotipo');
		$objDrawing->setDescription('Logotipo');
		$objDrawing->setImageResource($gdImage);
		$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
		$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
		$objDrawing->setHeight(50);
		$objDrawing->setCoordinates('A1');
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

		$tiempo_servicio = 0;
		while($sum = $resultado2->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)){
			$tiempo_servicio = (float)$sum[2]+$tiempo_servicio;
		}
		$tiempo_servicio2 = $tiempo_servicio/$num_transacciones;
		$formato_cantidad = number_format($tiempo_servicio2,6,'.',',');
		
		$estiloTituloReporte = array(
	    'font' => array(
		'name'      => 'Arial',
		'bold'      => true,
		'italic'    => false,
		'strike'    => false,
		'size' =>18
	    ),
	    'fill' => array(
		'type'  => PHPExcel_Style_Fill::FILL_SOLID
		),
	    'borders' => array(
		'allborders' => array(
		'style' => PHPExcel_Style_Border::BORDER_NONE
		)
	    ),
	    'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
	    )
		);
		
		$estiloTituloColumnas = array(
	    'font' => array(
		'name'  => 'Arial',
		'bold'  => true,
		'size' =>12,
		'color' => array(
		'rgb' => 'FFFFFF'
		)
	    ),
	    'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('rgb' => '538DD5')
	    ),
	    'borders' => array(
		'allborders' => array(
		'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	    ),
	    'alignment' =>  array(
		'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
	    )
		);
		
		$estiloInformacion = new PHPExcel_Style();
		$estiloInformacion->applyFromArray( array(
	    'font' => array(
		'name'  => 'Arial',
		'color' => array(
		'rgb' => '000000'
		)
	    ),
	    'fill' => array(
		'type'  => PHPExcel_Style_Fill::FILL_SOLID
		),
	    'borders' => array(
		'allborders' => array(
		'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	    ),
		'alignment' =>  array(
		'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
	    )
		));

		$estilo = array(
	    'font'  => array(
	        'bold'  => true,
	        'size'  => 12,
	        'name'  => 'Arial'
	    ));

		$tasa_arrivo = $num_transacciones/$P_tiempo;
		$utilizacion = ($tasa_arrivo*$formato_cantidad)*100;//Tiempo de servicio
		$demanda_servicio = $num_transacciones*$formato_cantidad; //Tiempo de servicio
		$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->applyFromArray($estiloTituloReporte);
 		$objPHPExcel->getActiveSheet()->setCellValue('C8', " NOMBRE: ");
 		$objPHPExcel->getActiveSheet()->setCellValue('D8', "ADMINISTRADOR"); 
 		$objPHPExcel->getActiveSheet()->getStyle('C8')->getAlignment()->setWrapText(true); 
 		$objPHPExcel->getActiveSheet()->setCellValue('C10', " Tiempo de monitoreo: "); 
 		$objPHPExcel->getActiveSheet()->setCellValue('D10', $P_tiempo." segundos"); ///pendiente
 		$objPHPExcel->getActiveSheet()->getStyle('C10')->getAlignment()->setWrapText(true);
 		$objPHPExcel->getActiveSheet()->setCellValue('C12', " Numero de transacciones: ");
 		$objPHPExcel->getActiveSheet()->setCellValue('D12', $num_transacciones);///Pendiente; 
 		$objPHPExcel->getActiveSheet()->setCellValue('F8', " Tasa de arrivo: ");
 		$objPHPExcel->getActiveSheet()->setCellValue('G8', $tasa_arrivo." peticiones/segundos");
 		$objPHPExcel->getActiveSheet()->setCellValue('F10', " Tiempo de servicio: ");
 		$objPHPExcel->getActiveSheet()->setCellValue('G10', $formato_cantidad." segundos");
 		$objPHPExcel->getActiveSheet()->setCellValue('F12', " Utilización: ");
 		$objPHPExcel->getActiveSheet()->setCellValue('G12', $utilizacion."%");
 		$objPHPExcel->getActiveSheet()->setCellValue('F14', " Demanda de servicio: ");
 		$objPHPExcel->getActiveSheet()->setCellValue('G14', $demanda_servicio." segundos");
 		$objPHPExcel->getActiveSheet()->getStyle('C12')->getAlignment()->setWrapText(true);
 		$objPHPExcel->getActiveSheet()->getStyle('C8')->applyFromArray($estilo);
 		$objPHPExcel->getActiveSheet()->getStyle('C10')->applyFromArray($estilo);
 		$objPHPExcel->getActiveSheet()->getStyle('C12')->applyFromArray($estilo);
 		$objPHPExcel->getActiveSheet()->getStyle('D8')->applyFromArray($estilo);
 		$objPHPExcel->getActiveSheet()->getStyle('D10')->applyFromArray($estilo);
 		$objPHPExcel->getActiveSheet()->getStyle('D12')->applyFromArray($estilo);
		$objPHPExcel->getActiveSheet()->getStyle('A15:G15')->applyFromArray($estiloTituloColumnas);
	
		$objPHPExcel->getActiveSheet()->setCellValue('B3', 'LOG de ACTUALIZAR - Depositos');
		$objPHPExcel->getActiveSheet()->mergeCells('B3:G3');
	
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$objPHPExcel->getActiveSheet()->setCellValue('A15', 'FECHA DE INICIO');
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->setCellValue('B15', 'OPERACIÓN');
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
		$objPHPExcel->getActiveSheet()->setCellValue('C15', 'TIEMPO DE OPERACIÓN');
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$objPHPExcel->getActiveSheet()->setCellValue('D15', 'CUENTA');
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->getActiveSheet()->setCellValue('E15', 'TIPO DE TRANSACCIÓN');
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		$objPHPExcel->getActiveSheet()->setCellValue('F15', 'ACCIÓN');
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
		$objPHPExcel->getActiveSheet()->setCellValue('G15', 'RESPUESTA');

		$sql = "SELECT fecha, operacion, tiempo_operacion, cuenta, tipo_transaccion, accion, respuesta FROM log WHERE accion='ACTUALIZAR' AND tipo_transaccion = 'Deposito'";
		$resultado=$base->prepare($sql);
		$resultado->execute();
	while($filas = $resultado->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)){

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $filas[0]);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $filas[1]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $filas[2]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $filas[3]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, $filas[4]);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$fila, $filas[5]);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$fila, $filas[6]);
		
		$fila++; //Sumamos 1 para pasar a la siguiente fila
	}
	$fila = $fila-1;
	$objPHPExcel->getActiveSheet()->setCellValue('E'.($fila+1), 'Tiempo de servicio: ');
	$objPHPExcel->getActiveSheet()->setCellValue('F'.($fila+1), $formato_cantidad." segundos");
	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="update_depositos.xls"');
	header('Cache-Control: max-age=0');
		
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	$_SESSION["monitoreo"] = 0;
	$_SESSION["transacciones"] = 0;


	}catch(PDOException $e){
		print $e->getMessage();
	}
	
?>