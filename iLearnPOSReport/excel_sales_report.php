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

if(isset($_REQUEST['saleableitems'])){
$fileName = "saleableitems.xls"; 
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

//generates view delivery report excel
//Done
if(isset($_REQUEST['viewdeliveryreport'])){
    $fileName = "viewdeliveryreport.xls"; 
    // Column names 
    $fields = array('INVOICE NUMBER', 'RR DATE', 'SUPPLIER', 'SUBTOTAL', 'NET AMOUNT', 'PAYMENT STATUS'); 
    $excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
    $excelData.="Delivery Report". "\n"; 
    $excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
    // Display column names as first row 
    // $excelData.= implode("\t", array_values($fields)) . "\n"; 
     
    // Fetch records from database 
    $query = "SELECT * FROM db_delivery where rr_date >= '".dashDate($_REQUEST['datefrom'])."' AND rr_date <=  '".dashDate($_REQUEST['dateto'])."'";
    $result = $db->query($query);
    if($result->num_rows > 0){ 
        // Output each row of the data 
            while($row = $result->fetch_assoc() ){
                $response[] = array("delivery_id"=>$row['delivery_id'],"invoiceno"=>$row['invoice_no'],"rr_date"=>$row['rr_date'],"supplier_id"=>$row['supplier_id'],"subtotal"=>$row['subtotal'],"net_amount"=>$row['net_amount'],"payment_status"=>$row['payment_status']);
            }
    
            $excelData.= implode("\t", array_values($fields)) ."\n";
            foreach($response as $key => $val){

                $supplierquery = "SELECT supplier_name FROM db_supplier WHERE supplier_id='".$val['supplier_id']."'";
                $supplierresult = mysqli_query($con,$supplierquery);
                $supplierrow = mysqli_fetch_array($supplierresult); 
                
                
                $response1 = array("invoiceno"=>$val['invoiceno'],"rr_date"=>$val['rr_date'],"supplier_name"=>$supplierrow['supplier_name'],"subtotal"=>$val['subtotal'],"net_amount"=>$val['net_amount'],"payment_status"=>($val['payment_status']=="1") ? "Paid" : "");
    
                array_walk($response1, 'filterData'); 
                
                $excelData .= implode("\t", array_values($response1)) . "\n"; 
            }
    
    
    }
}

//Done
if(isset($_REQUEST['viewreturns'])){
    $fileName = "viewreturns.xls"; 
    $excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
    $excelData.="View Returns". "\n"; 
    $excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
    // Display column names as first row 
    // $excelData.= implode("\t", array_values($fields)) . "\n"; 
     
    // Fetch records from database 
    $query = "SELECT * FROM db_sales,db_sales_product where db_sales.sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND db_sales.sales_date <=  '".dashDate($_REQUEST['dateto'])."' AND db_sales.sales_id=db_sales_product.sales_id AND db_sales_product.trans_type='2'";
    $result = $db->query($query);
    if($result->num_rows > 0){ 
        // Output each row of the data 
            while($row = $result->fetch_assoc() ){
                $response[] = array("invoiceno"=>$row['invoiceno'],"gross_sale"=>$row['gross_sale'],"discount_amount"=>$row['discount_amount'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"member_id"=>$row['member_id'],"payment_mode"=>$row['payment_mode'],"sales_id"=>$row['sales_id']);
            }

            foreach($response as $key => $val){
                $invoiceamount = 0;

                $query1 = "SELECT * FROM db_users WHERE userid='".$val['sold_by']."'";
                $results = mysqli_query($con,$query1);
                $rows = mysqli_fetch_array($results); 

                $excelData.= "INVOICE NUMBER:\t" .$val["invoiceno"] ."\t\tCASHIER:\t" .$rows['fname']." ".$rows['lname'] ."\n";
                $excelData.= "INVOICE DATE:\t" .$val["sales_date"] ."\t\tTERMINAL:\t" .$val["station"] ."\n\n";

                // Column names 
                $fields = array('BARCODE', 'PRODUCT NAME', 'QTY', 'PRICE', 'ITEM DISCOUNT (%)', 'AMOUNT'); 
                $excelData.= implode("\t", array_values($fields)) ."\n";

                $result = $db->query("SELECT * FROM db_sales_product where sales_id = '".$val['sales_id']."'");
    
                while($row = $result->fetch_assoc()){
                    $query = "SELECT barcode,product_name FROM db_products WHERE product_id='".$row['product_id']."'";
                    $result = mysqli_query($con,$query);
                    $row1 = mysqli_fetch_array($result); 

                    $amount=$row['quantity']*$row['sale_price'];

                    $response1 = array("barcode"=>$row1['barcode'],"product_name"=>$row1['product_name'],"quantity"=>$row['quantity'],"sale_price"=>number_format($row['sale_price'],2),"discount"=>$row['discount'],"amount"=>number_format($amount,2));

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