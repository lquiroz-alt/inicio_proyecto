<?php
	require('pdf/fpdf.php');
	require_once 'login_mysql.php';
	session_start();
	if(!isset($_SESSION["usuario"])){
		header("location: index.php");
	}
	
	try{

		
		$base=new PDO("mysql:host=www.app-deporte.com; dbname=".$db_database, $db_username, $db_password);
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "SELECT h.historial_id, c.numero_cuenta, h.descripcion, h.numero_rastreo, h.cantidad, t.nombre_transferencia, h.tiempo_registro FROM `historial_cliente` h
			INNER JOIN `cuentas` c ON c.cuenta_id = h.cuenta_id
			INNER JOIN `tipo_transferencia` t ON t.tipo_transferencia_id = h.tipo_transferencia_id
			WHERE h.cuenta_id = :cuenta_id";
		
		$resultado=$base->prepare($sql);
		$resultado->bindValue(":cuenta_id", $_SESSION['cuenta_id']);
		$resultado->execute();
		
		$formato_cantidad = number_format($_SESSION['dinero'],2,'.',',');
	
		$pdf=new FPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',15);
		$pdf->Ln(40);
		$pdf->Cell(70);
		$pdf->Cell(55,10,'  BANCO FORTUNA',1,1);
		$pdf->Image("icono_banco.png",10,10,-575,35);
		$pdf->Cell(120);
		$pdf->Image("Banner_Fortuna.png",130,10,-350,30);
		$pdf->Ln(10);
		$pdf->Cell(30);
		$pdf->Cell(130,10,'REPORTE DE TRANSACCIONES REALIZADAS',1,1);
		$pdf->Ln(25);
		$pdf->SetFont('','B',12);
		$pdf->Cell(1,1,'*NOMBRE:  '.$_SESSION['nombre']);
		$pdf->Ln(10);
		$pdf->Cell(1,1,'*CUENTA: '.$_SESSION['numero_cuenta']);
		$pdf->Ln(10);
		$pdf->Cell(1,1,'*CANTIDAD: '.$formato_cantidad);
		$pdf->Ln(25);
		$pdf->SetFont('Arial','B',8);
		//$pdf->Cell(20);
		$pdf->Cell(10,10,'ID',1,0,'C');
		$pdf->Cell(30,10,'CUENTA ID',1,0,'C');
		$pdf->Cell(55,10,'DESCRIPCION',1,0,'C');
		$pdf->Cell(25,10,'# RASTREO',1,0,'C');
		$pdf->Cell(30,10,'CANTIDAD',1,0,'C');
		$pdf->Cell(45,10,'TIPO DE TRANSFERENCIA',1,1,'C');
		$suma_cantidad = 0;
		$pdf->SetFont('Arial','',8);
		while($fila = $resultado->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)){
			$formato_cantidad = number_format($fila[4],2,'.',',');
			$pdf->Cell(10,10,$fila[0],1,0,'C');
			$pdf->Cell(30,10,$fila[1],1,0,'C');
			$pdf->Cell(55,10,utf8_decode($fila[2]),1,0,'C');
			$pdf->Cell(25,10,$fila[3],1,0,'C');
			$pdf->Cell(30,10,$formato_cantidad,1,0,'C');
			$pdf->Cell(45,10,utf8_decode($fila[5]),1,1,'C');
			$suma_cantidad = $fila[4]+$suma_cantidad;
		}
		$formato_cantidad = number_format($suma_cantidad,2,'.',',');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'Totales: ',1,0,'C');
		$pdf->Cell(45,10,$formato_cantidad,1,1,'C');
		$pdf->Output();

	$resultado = null;
	}catch(PDOException $e){
		print $e->getMessage();
	}
?>