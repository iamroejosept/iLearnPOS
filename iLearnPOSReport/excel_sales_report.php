<?php 
// Load the database configuration file 
// Database configuration 
$dbHost     = "localhost"; 
$dbUsername = "root"; 
$dbPassword = ""; 
$dbName     = "bbcccpos"; 
 
// Create database connection 
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName); 
 
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
$fileName = "saleable.xls"; 
if(isset($_REQUEST['saleableitems'])){

// Column names 
$fields = array('BARCODE', 'PRODUCT NAME', 'PIECES', 'AMOUNT'); 
$excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
$excelData.="Saleable Items". "\n"; 
$excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
// Display column names as first row 
$excelData.= implode("\t", array_values($fields)) . "\n"; 
 
// Fetch records from database 
$query = $db->query("SELECT SUM(db_sales_product.quantity) AS quantity_sum,SUM(db_sales_product.sale_price) AS amount_sum, db_sales_product.product_id,db_sales_product.sale_price FROM db_sales,db_sales_product where  db_sales.sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND  db_sales.sales_date <=  '".dashDate($_REQUEST['dateto'])."' AND db_sales_product.sales_id=db_sales.sales_id GROUP by db_sales_product.product_id ORDER by quantity_sum DESC"); 
if($query->num_rows > 0){ 
    // Output each row of the data 
    while($row = $query->fetch_assoc()){ 
        //$status = ($row['replenish'] == 1)?'Active':'Inactive'; 
        $query2 = "SELECT * FROM db_products WHERE product_id='".$row['product_id']."'";
        $result2 = mysqli_query($db,$query2);
        $row2 = mysqli_fetch_array($result2); 
        $lineData = array("barcode"=>$row2['barcode'],"product_name"=>$row2['product_name'],"pieces"=>$row['quantity_sum'],"amount"=>number_format($row['sale_price']*$row['quantity_sum'],2));
        array_walk($lineData, 'filterData'); 
        $excelData .= implode("\t", array_values($lineData)) . "\n"; 
    } 
}else{ 
    
    $excelData .= 'No records found...'. "\n"; 
} 

}
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData; 

exit;
?>