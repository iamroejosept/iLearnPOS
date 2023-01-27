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


//generates saleable report excel
if(isset($_REQUEST['saleableitems'])){
$fileName = "saleable.xls"; 
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

//generates view sale report excel
if(isset($_REQUEST['viewsalesreport'])){
$fileName = "viewsalesreport.xls"; 
// Column names 
$fields = array('INVOICE NUMBER', 'PURCHASE DATE', 'TIME', 'CASHIER', 'MEMBER ID', 'NAME', 'LOAN', 'CASH', 'AMOUNT'); 
$excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
$excelData.="Saleable Items". "\n"; 
$excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
// Display column names as first row 
$excelData.= implode("\t", array_values($fields)) . "\n"; 

/*global $con;*/
 
// Fetch records from database 
$query = "SELECT * FROM db_sales where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."'";
$result = $db->query($query);
if($result->num_rows > 0){ 
    // Output each row of the data 
    while($row = $result->fetch_assoc() ){

        $response = array("invoiceno"=>$row['invoiceno'],"sales_date"=>$row['sales_date'],"station"=>$row['station'],"sold_by"=>$row['sold_by'],"member_id"=>$row['member_id'],"member_name"=>"","loan_amount"=>$row['loan_amount'],"discount_amount"=>$row['discount_amount'],"invoice_amount"=>$row['discount_amount']);

        array_walk($response, 'filterData'); 
        
        $excelData .= implode("\t", array_values($response)) . "\n"; 
    } 
}else{ 
    
    $excelData .= 'No records found...'. "\n"; 
} 



}


//generates view detailed report excel
if(isset($_REQUEST['viewdetailedsalesreport'])){
$fileName = "viewdetailedsalesreport.xls"; 
// Column names 
$fields = array('BARCODE', 'PRODUCT NAME', 'QTY', 'PRICE', 'ITEM DISCOUNT(%)', 'AMOUNT'); 
$excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
$excelData.="Detailed Sales Items". "\n"; 
$excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
// Display column names as first row 
// $excelData.= implode("\t", array_values($fields)) . "\n"; 
 
// Fetch records from database 
$query = "SELECT * FROM db_sales where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."'";
$result = $db->query($query);
if($result->num_rows > 0){ 
    // Output each row of the data 
        while($row = $result->fetch_assoc() ){
            $response[] = array('invoiceno'=>$row['invoiceno'],"gross_sale"=>$row['gross_sale'],"discount_amount"=>$row['discount_amount'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"member_id"=>$row['member_id'],"payment_mode"=>$row['payment_mode'],"sales_id"=>$row['sales_id']);
        }

        foreach($response as $key => $val){
            $excelData.= "INVOICE NUMBER:\t" .$val["invoiceno"] ."\t\tCASHIER:\t" .$val["sold_by"] ."\n";
            $excelData.= "INVOICE DATE:\t" .$val["sales_date"] ."\t\tTERMINAL:\t" .$val["station"] ."\n\n";
            $excelData.= implode("\t", array_values($fields)) ."\n";

            $id = array();
            $id = $val["sales_id"]; 
            
            $result = $db->query("SELECT * FROM db_sales_product where sales_id = $id");
    
            while($row = $result->fetch_assoc()){
            $response1 = array("invoiceno"=>" ","product_name"=>" ","quantity"=>$row['quantity'],"sale_price"=>$row['sale_price'],"discount"=>$row['discount'],"amount"=>($row['sale_price']*$row['quantity']),"discount_amount"=>" ","invoice_amount"=>" ");

            array_walk($response1, 'filterData'); 
            
            $excelData .= implode("\t", array_values($response1)) . "\n"; 
            }
            $excelData .= "\n";
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