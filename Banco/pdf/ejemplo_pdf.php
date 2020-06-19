<?php
require('fpdf/fpdf.php');

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
$pdf->Cell(1,1,'*NOMBRE: ______________________________________________________________ ');
$pdf->Ln(10);
$pdf->Cell(1,1,'*CUENTA: _______________________________________________________________ ');
$pdf->Ln(10);
$pdf->Cell(1,1,'*CANTIDAD: _____________________________________________________________ ');
$pdf->Ln(25);
$pdf->SetFont('','B',10);
//$pdf->Cell(20);
$pdf->Cell(10,10,'ID',1,0,'C');
$pdf->Cell(25,10,'CUENTA ID',1,0,'C');
$pdf->Cell(35,10,'DESCRIPCION',1,0,'C');
$pdf->Cell(45,10,'NUMERO DE RASTREO',1,0,'C');
$pdf->Cell(25,10,'CANTIDAD',1,0,'C');
$pdf->Cell(50,10,'TIPO DE TRANSFERENCIA',1,0,'C');


$pdf->Output();

?>