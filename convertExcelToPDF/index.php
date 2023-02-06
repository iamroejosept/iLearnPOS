<?php
require('fpdf/fpdf.php');

// Load the Excel file using PHPExcel library
require_once 'phpspreadsheet/vendor/autoload.php';
$filename = 'asd.xlsx';
$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);

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
        $isBordered = 0;
        $fontsize = 6;
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



       //$pdf->SetFont('Arial','B',8);

       if($column == 'B'){
            if($value == 'Baguio Benguet Community Credit Cooperative'){
                $isBordered = 0;
                $fontsize += 3;
                $cellWidth =270;
            }else if ($value == 'No.56 Cooperative Street, Corner Assumption Road, Baguio City'){
                $isBordered = 0;
                $fontsize += 3;
                $cellWidth =270;
            }else if ($value == 'Sales Report Detailed per invoice'){
                $isBordered = 0;
                $fontsize += 3;
                $cellWidth =270;
            }else if ($value == ' From : 01/05/2023  To: 01/05/2023'){
                $isBordered = 0;
                $fontsize += 3;
                $cellWidth =270;
            }else if($value == ""){
                $isBordered = 0;
            }else{
                $isBordered = 1;
            }
        }else if($value == ""){
            $isBordered = 0;
        }else{
            $isBordered = 1;
        }

        /*if($value == ' Barcode'){
            $pdf->SetFont('Arial','B',$fontsize);
            $pdf->Cell($cellWidth+3, 5, 'Barcode', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'ProdName', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Qty', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Unit Price', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Amount', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Invoice No.', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Member ID', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Invoice Date', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Invoice Time', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Loan Amount', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Cash Amount', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Payment Mode', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Terminal', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Cashier', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Name', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Invoice Amount', $isBordered, 0, 'C', false);
            $pdf->Cell($cellWidth+3, 5, 'Grand Total', $isBordered, 0, 'C', false);
            continue;
        }else{*/
       // Write the cell value to the PDF
            $pdf->SetFont('Arial','B',$fontsize);
            $pdf->Cell($cellWidth+3, 5, $value, $isBordered, 0, 'C', false);
        //}
    }
    // Move to the next row
    $pdf->Ln();
}

// Save the PDF
$pdf->Output($filename .'.pdf', 'F');

?>