<?php
require('fpdf/fpdf.php');

// Load the Excel file using PHPExcel library
require_once 'phpspreadsheet/vendor/autoload.php';
$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('january5.xlsx');

// Get the first worksheet
$worksheet = $excel->getSheet(0);

// Get the highest row and column
$highestRow = $worksheet ->getHighestRow();
$highestColumn = $worksheet ->getHighestColumn();

// Create the PDF document
$pdf = new FPDF('L','mm','Legal');
$pdf->AddPage('L', [215.9, 330.2]);
$pdf->SetFont('Arial','B',6);


// Loop through the rows and columns of the worksheet
for ($row = 1; $row <= $highestRow; $row++) {
    $columnLoan = 0;
    $columnCash = 0;
    for ($column = 'A'; $column <= $highestColumn; $column++) {
        
        // Get the cell value
        $value = $worksheet->getCell($column.$row)->getCalculatedValue();

        if($column == 'J'){
            $columnLoan = $value;
        }else if($column == "K"){
            $columnCash = $value;
        }

        if($column == 'L'){
            if($columnLoan > 0){
                $value = "Loan";
            }else if ($columnCash > 0){
                $value = "Cash";
            }else{
                $value = "";
            }
        }

        // Set wrap text for the cell
        $worksheet->getCell($column.$row)->getStyle()->getAlignment()->setWrapText(true);
        // Get the cell width
        $cellWidth = $worksheet->getColumnDimension($column)->getWidth();

        if($column == 'B' || $column == 'O'){
            $cellWidth = $cellWidth - 12;
        }
       // Write the cell value to the PDF
       $pdf->Cell($cellWidth+3, 5, $value, 0, 0, 'C', false);
    }
    // Move to the next row
    $pdf->Ln();
}

// Save the PDF
$pdf->Output('sample.pdf', 'F');

?>