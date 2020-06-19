<?php

//Incluimos librería y archivo de conexión
	require 'Classes/PHPExcel.php';
	require_once 'login_mysql.php';
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}
	
	//Consulta
	//$sql = "SELECT id, nombre, precio, existencia FROM productos";
	//$resultado = $mysqli->query($sql);
	$usuario_sesion = $_SESSION["usuario"];
	$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
	$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$fila = 16; //Establecemos en que fila inciara a imprimir los datos
	$sql = "SELECT h.historial_id, c.numero_cuenta, h.descripcion, h.numero_rastreo, h.cantidad, t.nombre_transferencia, h.tiempo_registro FROM `historial_cliente` h
			INNER JOIN `cuentas` c ON c.cuenta_id = h.cuenta_id
			INNER JOIN `tipo_transferencia` t ON t.tipo_transferencia_id = h.tipo_transferencia_id
			WHERE c.numero_cuenta = :numero_cuenta";

	$resultado=$base->prepare($sql);
	$resultado->bindValue(":numero_cuenta", $_SESSION['numero_cuenta']);
	$resultado->execute();
		
	$formato_cantidad = number_format($_SESSION['dinero'],2,'.',',');


	
	$gdImage = imagecreatefrompng('Banner_Fortuna.png');//Logotipo
	
	//Objeto de PHPExcel
	$objPHPExcel  = new PHPExcel();
	
	//Propiedades de Documento
	$objPHPExcel->getProperties()->setCreator("FERNANDO AGUILAR")->setDescription("REPORTE DE TRANSACCIONES");
	
	//Establecemos la pestaña activa y nombre a la pestaña
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setTitle("Transacciones");
	
	$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
	$objDrawing->setName('Logotipo');
	$objDrawing->setDescription('Logotipo');
	$objDrawing->setImageResource($gdImage);
	$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
	$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
	$objDrawing->setHeight(50);
	$objDrawing->setCoordinates('A1');
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
	
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
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->applyFromArray($estiloTituloReporte);
 	$objPHPExcel->getActiveSheet()->setCellValue('C8', " NOMBRE: ");
 	$objPHPExcel->getActiveSheet()->setCellValue('D8', $_SESSION['nombre']); 
 	$objPHPExcel->getActiveSheet()->getStyle('C8')->getAlignment()->setWrapText(true);
 	$objPHPExcel->getActiveSheet()->setCellValue('C10', " CUENTA: "); 
 	$objPHPExcel->getActiveSheet()->setCellValue('D10',$_SESSION['numero_cuenta']); 
 	$objPHPExcel->getActiveSheet()->getStyle('C10')->getAlignment()->setWrapText(true);
 	$objPHPExcel->getActiveSheet()->setCellValue('C12', " CANTIDAD: ");
 	$objPHPExcel->getActiveSheet()->setCellValue('D12',$_SESSION['dinero']); 
 	$objPHPExcel->getActiveSheet()->getStyle('C12')->getAlignment()->setWrapText(true);
 	$objPHPExcel->getActiveSheet()->getStyle('C8')->applyFromArray($estilo);
 	$objPHPExcel->getActiveSheet()->getStyle('C10')->applyFromArray($estilo);
 	$objPHPExcel->getActiveSheet()->getStyle('C12')->applyFromArray($estilo);
 	$objPHPExcel->getActiveSheet()->getStyle('D8')->applyFromArray($estilo);
 	$objPHPExcel->getActiveSheet()->getStyle('D10')->applyFromArray($estilo);
 	$objPHPExcel->getActiveSheet()->getStyle('D12')->applyFromArray($estilo);
	$objPHPExcel->getActiveSheet()->getStyle('A15:F15')->applyFromArray($estiloTituloColumnas);
	
	$objPHPExcel->getActiveSheet()->setCellValue('B3', 'REPORTE DE TRANSACCIONES');
	$objPHPExcel->getActiveSheet()->mergeCells('B3:F3');
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->setCellValue('A15', 'ID');
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->setCellValue('B15', 'CUENTA ID');
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$objPHPExcel->getActiveSheet()->setCellValue('C15', 'DESCRIPCION');
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$objPHPExcel->getActiveSheet()->setCellValue('D15', 'NUMERO DE RASTREO');
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$objPHPExcel->getActiveSheet()->setCellValue('E15', 'CANTIDAD');
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
	$objPHPExcel->getActiveSheet()->setCellValue('F15', 'TIPO DE TRANSFERENCIA');
	
	//Recorremos los resultados de la consulta y los imprimimos
	$suma_cantidad = 0;
	while($filas = $resultado->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)){
			$formato_cantidad = number_format($filas[4],2,'.',',');
			$suma_cantidad = $filas[4]+$suma_cantidad;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $filas[0]);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $filas[1]);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,utf8_encode($filas[2]));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $filas[3]);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, $formato_cantidad);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$fila, utf8_encode($filas[5]));
		
			$fila++; //Sumamos 1 para pasar a la siguiente fila
		}
		$fila = $fila-1;
		$formato_cantidad = number_format($suma_cantidad,2,'.',',');
		$objPHPExcel->getActiveSheet()->setCellValue('E'.($fila+1), 'Totales: ');
		$objPHPExcel->getActiveSheet()->setCellValue('F'.($fila+1), $formato_cantidad);
	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="Transacciones.xls"');
	header('Cache-Control: max-age=0');
		
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

?>