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

//generates dataid report excel
if(isset($_REQUEST['dataid'])){
$fileName = "dataid.xls"; 
// Column names 
$fields = array('Invoice No', 'Date', 'Time', 'Mem. No', 'Name', 'Quantity', 'Disc', 'Amount (₱)'); 
$excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
$excelData.="Data ID". "\n"; 
$excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
// Display column names as first row 
$excelData.= implode("\t", array_values($fields)) . "\n"; 
 
// Fetch records from database ".$_REQUEST['dataid']."
$query = "SELECT * FROM db_sales,db_sales_product where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."' AND db_sales.sales_id=db_sales_product.sales_id AND db_sales_product.product_id='".$_REQUEST['dataid']."'";
$query = $db->query($query);
if($query->num_rows > 0){ 
    // Output each row of the data 
    while($row = $query->fetch_assoc()){ 
        //$status = ($row['replenish'] == 1)?'Active':'Inactive'; 
        $member_name = " ";
        $totalQuantity = 0;
        $amount = 1;
        $lineData = array("invoiceno"=>$row['invoiceno'],
            "sales_date"=>$row['sales_date'],
            "invoiceno"=>$row['invoiceno'],
            "member_id"=>$row['member_id'],
            "member"=>$member_name,
            "member_id"=>$row['quantity'],
            "total_quantity"=>$totalQuantity + $row['quantity'],
            "amount"=> $amount * $row['sale_price'],
        );
        array_walk($lineData, 'filterData'); 
        $excelData .= implode("\t", array_values($lineData)) . "\n"; 
    } 
}else{ 
    
    $excelData .= 'No records found...'. "\n"; 
} 

}


//done - please recheck
if(isset($_REQUEST['viewtallyreport'])){
$fileName = "viewtallyreport.xls"; 
// Column names 
$fields = array('Date', 'Total'); 
$excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
$excelData.="Data ID". "\n"; 
$excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
// Display column names as first row 
$excelData.= implode("\t", array_values($fields)) . "\n"; 

$t=time()+28800;
$thedate=date("Y-m-d",$t);
 
// Fetch records from database ".$_REQUEST['dataid']."
$query = "SELECT * FROM db_sales WHERE sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."' group by sales_date";
$result = mysqli_query($con,$query);
if(mysqli_num_rows($result) > 0){ 
    // Output each row of the data 
    while($row = mysqli_fetch_array($result) ){
        //$status = ($row['replenish'] == 1)?'Active':'Inactive'; 
        $response[] = array("sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by']);
    }
        $total=0;
        foreach($response as $key => $value) {
            
            $excelData .= $value['sales_date'] ."\t";

            $query = "SELECT sum(cash_amount) as totalcash,station,sum(loan_amount) as totalloan,sold_by FROM db_sales where sales_date = '".$value['sales_date']."' group by sold_by ASC";
            $result = mysqli_query($con,$query);
            while($row = mysqli_fetch_array($result) ){
                $total+=$row['totalcash']+$row['totalloan'];
                $excelData .= $row['totalcash']+$row['totalloan'] . "\t";
                $excelData .= $total . "\n";
                                
            }

        }
    
        /*array_walk($lineData, 'filterData'); 
        $excelData .= implode("\t", array_values($lineData)) . "\n"; */
     
}else{ 
    
    $excelData .= 'No records found...'. "\n"; 
} 

}



//done - please recheck
if(isset($_REQUEST['viewprofitreport'])){
$fileName = "viewprofitreport.xls"; 
// Column names 
$fields = array('Invoice Number', 'Purchase Date','Terminal','Amount','Cost','Profit', '% Markup'); 
$excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
$excelData.="Data ID". "\n"; 
$excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
// Display column names as first row 
$excelData.= implode("\t", array_values($fields)) . "\n"; 
 
// Fetch records from database ".$_REQUEST['dataid']."
$query = "SELECT * FROM db_sales where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."'";
$result = mysqli_query($con,$query);
if(mysqli_num_rows($result) > 0){ 
    // Output each row of the data 
    while($row = mysqli_fetch_array($result) ){

        $response[] = array("invoiceno"=>$row['invoiceno'],"cash_amount"=>$row['cash_amount'],"total_sales"=>$row['total_sales'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"total_cost"=>$row['total_cost'],"payment_mode"=>$row['payment_mode']);
    }
        $totalloan=0;
        $no=1;
        foreach($response as $key => $value) {
            $profit=0;
            $markup=0;
            $no++;
            $excelData .= $value['invoiceno'] ."\t";
            $excelData .= $value['sales_date'] ."\t";
            $excelData .= $value['station'] ."\t";
            $excelData .= $value['total_sales'] ."\t";
            $excelData .= number_format($value['total_cost'],2)."\t";
            $profit= floatval($value['total_sales'])-floatval($value['total_cost']);
            $excelData .= number_format($profit,2)."\t";

            //php throws DivisionByZeroError if total_cost is 0
            if($value['total_cost'] == 0){
                $markup= 0;
            }else{
                $markup=100*($profit/$value['total_cost']);
            }
            
            $excelData .= number_format($markup,2)."% \n";

        }
    
        /*array_walk($lineData, 'filterData'); 
        $excelData .= implode("\t", array_values($lineData)) . "\n"; */
     
}else{ 
    
    $excelData .= 'No records found...'. "\n"; 
} 

}

//done - please recheck
if(isset($_REQUEST['viewpurchasereturnreport'])){
$fileName = "viewpurchasereturnreport.xls"; 
// Column names 
$fields = array('Barcode', 'Product Name','Quantity','Unit Cost','Amount','Remarks'); 
$excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
$excelData.="Data ID". "\n"; 
$excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
// Display column names as first row 

 
// Fetch records from database ".$_REQUEST['dataid']."
$query = "SELECT * FROM db_purchase_return where return_date >= '".dashDate($_REQUEST['datefrom'])."' AND return_date <=  '".dashDate($_REQUEST['dateto'])."'";
$result = mysqli_query($con,$query);
if(mysqli_num_rows($result) > 0){ 
    // Output each row of the data 
    while($row = mysqli_fetch_array($result) ){

     $response[] = array("return_id"=>$row['id'],"supplier_id"=>$row['supplier_id'],"return_area"=>$row['return_area'],"return_date"=>$row['return_date']);
    }

        $no=1;$total=0;
        foreach($response as $key => $value) 
        {
            switch($value['return_area']){
                  case 1: $excelData .= "Warehouse\n";break;
                  case 2: $excelData .= "Selling area\n";break;
                  default: "";
            }

            $query = "SELECT supplier_name FROM db_supplier WHERE supplier_id='" .$value['supplier_id'] ."'";
            $result = mysqli_query($con,$query);
            $row = mysqli_fetch_array($result);
            $excelData .= $row['supplier_name']."\t";
            $excelData .= $value['return_id']."\t";
            $excelData .= $value['return_date']."\n";
            $excelData.= implode("\t", array_values($fields)) . "\n"; 


            $query2 = "SELECT * FROM db_prod_purchase_return where return_id= '".$value['return_id']."'";
            $result2 = mysqli_query($con,$query2);

            while($row2= mysqli_fetch_array($result2) ){

                $total+=($row2['quantity']*$row2['price']);

                $query = "SELECT barcode FROM db_products WHERE product_id='".$row2['item_id']."'";
                $result = mysqli_query($con,$query);
                $row = mysqli_fetch_array($result); 
                $excelData .= $row['barcode']."\t";

                $query = "SELECT product_name FROM db_products WHERE product_id='".$row2['item_id']."'";
                $result = mysqli_query($con,$query);
                $row = mysqli_fetch_array($result); 
                $excelData .=  $row['product_name']."\t";

                $excelData .= $row2['quantity']."\t";
                $excelData .=$row2['price']."\t";
                $excelData .=number_format($row2['quantity']*$row2['price'],2)."\n";

            }

            $excelData .= "Total: \t".number_format($total,2)."\n\n";

        

        }
    
        /*array_walk($lineData, 'filterData'); 
        $excelData .= implode("\t", array_values($lineData)) . "\n"; */
     
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