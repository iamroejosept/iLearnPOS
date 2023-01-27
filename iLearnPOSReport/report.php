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

if(isset($_REQUEST['viewmemdetailedreport'])){
    $fileName = "viewmemdetailedreport.xls"; 
    $excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
    $excelData.="View Me Detailed Report". "\n"; 
    $excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
    // Display column names as first row 
    // $excelData.= implode("\t", array_values($fields)) . "\n"; 
     
    // Fetch records from database 
    $query = "SELECT * FROM db_sales where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."' AND member_id='".$_REQUEST['memberid']."'";
    $result = $db->query($query);
    if($result->num_rows > 0){ 
        // Output each row of the data 
            while($row = $result->fetch_assoc() ){
                $response[] = array("invoiceno"=>$row['invoiceno'],"gross_sale"=>$row['gross_sale'],"discount_amount"=>$row['discount_amount'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"member_id"=>$row['member_id'],"payment_mode"=>$row['payment_mode'],"sales_id"=>$row['sales_id']);
            }

            foreach($response as $key => $val){
                $invoiceamount=0;

                $query1 = "SELECT * FROM db_users WHERE userid='".$val['sold_by']."'";
                $result1 = mysqli_query($con,$query1);
                $row1 = mysqli_fetch_array($result1); 

                $excelData.= "INVOICE NUMBER:\t" .$val["invoiceno"] ."\t\tCASHIER:\t" .$row1['fname']." ".$row1['lname'] ."\n";
                $excelData.= "INVOICE DATE:\t" .$val["sales_date"] ."\t\tTERMINAL:\t" .$val["station"] ."\n\n";

                // Column names 
                $fields = array('BARCODE', 'PRODUCT NAME', 'QTY', 'PRICE', 'ITEM DISCOUNT (%)', 'AMOUNT'); 
                $excelData.= implode("\t", array_values($fields)) ."\n";

                $result = $db->query("SELECT * FROM db_sales_product where sales_id = '".$val['sales_id']."'");
    
                while($row = $result->fetch_assoc()){
                    $amount=$row['quantity']*$row['sale_price'];

                    $querys = "SELECT barcode,product_name FROM db_products WHERE product_id='".$row['product_id']."'";
                    $results = mysqli_query($con,$querys);
                    $rows = mysqli_fetch_array($results); 

                    $response1 = array("barcode"=>$rows['barcode'],"product_name"=>$rows['product_name'],"quantity"=>$row['quantity'],"sale_price"=>number_format($row['sale_price'],2),"discount"=>$row['discount'],"amount"=>number_format($amount,2));

                    $invoiceamount+=$amount;

                    array_walk($response1, 'filterData'); 
                    
                    $excelData .= implode("\t", array_values($response1)) . "\n"; 
                }

                $excelData.= "DISCOUNT:\t" .number_format($val['discount_amount'],2)."\t\t";
                $excelData.= "INVOICE AMOUNT:\t" .number_format($invoiceamount-$val['discount_amount'],2)."\n\n";
                
            }
            $excelData .= "\n";

    
    }
}


header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData; 



exit;
?>