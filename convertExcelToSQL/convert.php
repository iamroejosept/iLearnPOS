<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Convert Excel to SQL</title>


    <style type="text/css">
        form {
            text-align: center;
            margin-top: 25vh;
            margin-bottom: 25vh;
        }
    </style>
</head>
<body>
    <form method="post" enctype="multipart/form-data">
        <label for="uploadedFile">Select Excel file</label><br>
        <input type="file" name="file" size="45">
        <input type="submit" name="submit" value="Upload File">
    </form>
</body>
    
    
</html>

<?php
// Include the PHPExcel library
require_once 'phpspreadsheet/vendor/autoload.php';

// Connect to the MySQL database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "sample";
$table = "sample_table";

$conn = mysqli_connect($host, $username, $password, $dbname);

$fileName = '';
if(isset($_POST["submit"]))
{
    /*$fileName = $_POST["file"];*/
    if($_FILES["file"]["name"] != '')
    {

        // Get the temporary file name of the uploaded file
        $tmp_name = $_FILES['file']['tmp_name'];

        $fname = $_FILES['file']['name'];

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmp_name);

        $worksheet = $spreadsheet->getActiveSheet();

        $data = $worksheet->toArray();

        $breakCtr = 0;
        $sql = "INSERT INTO $table (barcode, product_name, quantity, unit_price, amount, invoice_num, member_id, invoice_date, invoice_total, payment_type, grand_total) VALUES \n";
                
        foreach ($data as $row) {

            if($row[0] == 'Invoice No. : '){
                $breakCtr = 5;
            }
            
            if($breakCtr > 0){
                $breakCtr--;
                continue;
            }else{
                

                if(!empty($row[0])){

                    if($row[9] > 0){
                        if ($row[10] > 0) {
                            $row[11] = "Loan and Cash";
                        }else{
                            $row[11] = "Loan";
                        }
                    }else if($row[10] > 0){
                        $row[11] = "Cash";
                    }else{
                        $row[11] = "Cannot be Determined";
                    }

                    $sql .= "('"      . $row[0] . 
                                "', '" . $row[1] . 
                                "', '" . $row[2] . 
                                "', '" . $row[3] . 
                                "', '" . $row[4] . 
                                "', '" . $row[5] . 
                                "', '" . $row[6] .
                                "', '" . $row[7] .
                                "', '" . $row[8] .
                                "', '" . $row[11] .
                                "', '" . $row[12] ."'),\n";
                    /*trim($sql);*/


                    /*echo "''$row[0]'' ";
                    echo "''$row[1]'' ";
                    echo "''$row[2]'' ";
                    echo "''$row[3]'' ";
                    echo "''$row[4]'' ";
                    echo "''$row[5]'' ";
                    echo "''$row[6]'' ";
                    echo "''$row[7]'' ";
                    echo "<br>";*/

                /*if($arrEnd == 0){
                   $sql .= "; \n";
                   
                }
                   $sql .= ", \n";*/
                   
                }
                /*else{
                   $arrEnd --;
                }*/

                
            }
            
        }

        $sql = rtrim($sql);

        $sql = substr_replace($sql,";",-1);

        /*if (mysqli_query($conn, $sql)) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                
            }*/

        

        mysqli_close($conn);

        /*unlink($fileName);*/

        //save file
        $fileName = $fname;
        $handle = fopen($fileName,'w+');
        fwrite($handle,$sql);
        if(fclose($handle)){

             header('Content-Description: File Transfer');
             header('Content-Type: application/octet-stream');
             header('Content-Disposition: attachment; filename=' . basename($fileName));
             header('Content-Transfer-Encoding: binary');
             header('Expires: 0');
             header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($fileName));
                ob_clean();
                flush();
                readfile($fileName);            
                unlink($fileName);
            exit;

             //Alert pop-up if download success

             $message = "Done, Check your backup at your Downloads folder with filename: ".$fileName;



          
        }

    }
}

    
?>

