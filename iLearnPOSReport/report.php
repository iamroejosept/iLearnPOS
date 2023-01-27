<?php 
// Load the database configuration file 
// Database configuration 
$dbHost     = "localhost"; 
$dbUsername = "root"; 
$dbPassword = ""; 
$dbName     = "bbcccpos"; 
 
// Create database connection 
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName); 
// Create connection
$con = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName); 
 
// Check connection 
if ($db->connect_error) { 
    die("Connection failed: " . $db->connect_error); 
}
function dashDate($date){
    return date("Y-m-d", strtotime($date) );
}
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 
// Excel file name for download 
$month=date('F', mktime(0, 0, 0, $_REQUEST['month'], 10));
//$fileName = "saleable.xls"; 

if(isset($_REQUEST['viewtransferreport'])){
    $fileName = "viewtransferreport.xls"; 
    $excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
    $excelData.="View Transfer Report". "\n"; 
    $excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
    // Display column names as first row 
    // $excelData.= implode("\t", array_values($fields)) . "\n"; 
     
    // Fetch records from database 
    $query = "SELECT * FROM db_transfers where transfer_date >= '".dashDate($_REQUEST['datefrom'])."' AND transfer_date <=  '".dashDate($_REQUEST['dateto'])."'";
    $result = $db->query($query);
    if($result->num_rows > 0){ 
        // Output each row of the data 
            while($row = $result->fetch_assoc() ){
                $response[] = array("product_id"=>$row['product_id'],"quantity"=>$row['quantity'],"current_price"=>$row['current_price'],"area"=>$row['area']);
            }

            // Column names 
            $fields = array('BARCODE', 'ITEM DESCRIPTION', 'QUANTITY', 'PRICE', 'AMOUNT'); 
            $excelData.= implode("\t", array_values($fields)) ."\n";

            $total=0;

            foreach($response as $key => $val){
                $query1 = "SELECT barcode,product_name FROM db_products WHERE product_id='".$val['product_id']."'";
                $result1 = mysqli_query($con,$query1);
                $row1 = mysqli_fetch_array($result1); 

                $response1 = array("barcode"=>$row1['barcode'],"product_name"=>$row1['product_name'],"quantity"=>$val['quantity'],"current_price"=>$val['current_price'],"amount"=>number_format($val['quantity']*$val['current_price'],2));

                array_walk($response1, 'filterData'); 
                    
                $excelData .= implode("\t", array_values($response1)) . "\n"; 

                $total+=($val['quantity']*$val['current_price']);
                
            }
            $excelData.= "TOTAL AMOUNT:\t" .number_format($total,2)."\t\t";
            $excelData .= "\n";

    
    }
}


header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData; 



exit;
?>