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



    //Done, please recheck
    if(isset($_REQUEST['viewdetailedsalesreport'])){
        $fileName = "viewdetailedsalesreport.xls"; 
        // Column names 
        $fields = array('BARCODE', 'PRODUCT NAME', 'QTY', 'PRICE', 'ITEM DISCOUNT(%)', 'AMOUNT'); 
        $excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
        $excelData.="View Detailed Sales Items". "\n"; 
        $excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
        // Display column names as first row 
        // $excelData.= implode("\t", array_values($fields)) . "\n"; 
         
        // Fetch records from database 
        $query = "SELECT * FROM db_sales where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."'";
        $result = mysqli_query($con,$query);
        if(mysqli_num_rows($result) > 0){ 
            // Output each row of the data 
            while($row = mysqli_fetch_array($result) ){

                $response[] = array("invoiceno"=>$row['invoiceno'],"gross_sale"=>$row['gross_sale'],"discount_amount"=>$row['discount_amount'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"member_id"=>$row['member_id'],"payment_mode"=>$row['payment_mode'],"sales_id"=>$row['sales_id']);
            }
                $totalsale=0;
                foreach($response as $key => $value){
                    $excelData .= "Invoice Number: \t";
                    $excelData .= $value['invoiceno'] ."\t";
                    $excelData.= "\t";
                    $excelData.= "\t";
                    $excelData.= "Cashier: \t";
                    $query1 = "SELECT * FROM db_users WHERE userid='".$value['sold_by']."'";
                    $result1 = mysqli_query($con,$query1);
                    $row1 = mysqli_fetch_array($result1); 
                    if(!empty($row1['fname'])){
                        $excelData .=  $row1['fname'].", ";
                    }else{
                        $excelData .=  "";
                    }
                    if(!empty($row1['lname'])){
                        $excelData .=  $row1['lname']." \n";
                    }else{
                        $excelData .=  "\n";
                    }

                    $excelData .= "Invoice Date: \t";
                    $excelData .= $value['sales_date'] ."\t";
                    $excelData.= "\t";
                    $excelData.= "\t";
                    $excelData .= "Terminal: \t";
                    $excelData .= $value['station'] ."\n\n";

                    $excelData.= implode("\t", array_values($fields)) . "\n";

                    $invoiceamount=0;
                    $query = "SELECT * FROM db_sales_product where sales_id = '".$value['sales_id']."'";
                    $result = mysqli_query($con,$query);
                    
                    while($row = mysqli_fetch_array($result) ){ 
                        $query2 = "SELECT barcode,product_name FROM db_products WHERE product_id='".$row['product_id']."'";
                        $result2 = mysqli_query($con,$query2);
                        $row2 = mysqli_fetch_array($result2); 
                        $excelData .=  $row2['barcode']."\t";
                        $excelData .=  $row2['product_name'] ."\t";

                        if(empty($row['quantity'])){
                            $row['quantity'] = 0;
                            $excelData .= $row['quantity']."\t";
                        }else{
                            $excelData .=  $row['quantity']."\t";
                        }

                        if(empty($row['sale_price'])){
                            $row['sale_price'] = 0;
                            $excelData .= $row['sale_price']."\t";
                        }else{
                            $excelData .= number_format($row['sale_price'],2)."\t";
                        }

                        if(empty($row['discount'])){
                            $row['discount'] = 0;
                            $excelData .=  $row['discount']."\t";
                        }else{
                            $excelData .=  $row['discount']."% \t";
                        }
                        
                        $amount=$row['quantity']*$row['sale_price'];

                        $excelData .=  number_format($amount,2)." \n";

                        $invoiceamount+=$amount;
                    }

                    $excelData.= "\t";
                    $excelData.= "\t";
                    $excelData.= "\t";
                    $excelData.= "\t";
                    $excelData .=  "Discount(-) \t";
                    $excelData .=  number_format($value['discount_amount'],2) ." \n";
                    $excelData.= "\t";
                    $excelData.= "\t";
                    $excelData.= "\t";
                    $excelData.= "\t";
                    $excelData .=  "Invoice Amount \t";
                    $excelData .=  number_format($invoiceamount-$value['discount_amount'],2) ." \n";
                    $excelData.= "\n";

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
    $excelData.="View Delivery Report". "\n"; 
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

//Done
if(isset($_REQUEST['viewdeliveryreport'])){
    $fileName = "viewdeliveryreport.xls"; 
    $excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
    $excelData.="View Delivery Report". "\n"; 
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

            // Column names 
            $fields = array('INVOICE NUMBER', 'RR DATE', 'SUPPLIER', 'SUBTOTAL', 'NET AMOUNT', 'PAYMENT STATUS'); 
            $excelData.= implode("\t", array_values($fields)) ."\n";

            foreach($response as $key => $val){
                $query1 = "SELECT supplier_name FROM db_supplier WHERE supplier_id='".$val['supplier_id']."'";
                $result1 = mysqli_query($con,$query1);
                $row1 = mysqli_fetch_array($result1); 

                $response1 = array("invoiceno"=>$val['invoiceno'],"rr_date"=>$val['rr_date'],"supplier_id"=>$row1['supplier_name'],"subtotal"=>$val['subtotal'],"net_amount"=>$val['net_amount'],"payment_status"=>($val['payment_status']=="1") ? "Paid" : "");

                array_walk($response1, 'filterData'); 
                    
                $excelData .= implode("\t", array_values($response1)) . "\n";
                
            }
            $excelData .= "\n";

    
    }
}

//Done
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

//Done
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

//done - please recheck
if(isset($_REQUEST['viewtallyreport'])){
$fileName = "viewtallyreport.xls"; 
// Column names 
$fields = array('Date', 'Total'); 
$excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
$excelData.="View Tally Report". "\n"; 
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
$excelData.="View Profit Report". "\n"; 
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
$excelData.="View Purchase Return Report". "\n"; 
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

            $excelData .= "\tTotal: \t".number_format($total,2)."\n\n";

        

        }
    
        /*array_walk($lineData, 'filterData'); 
        $excelData .= implode("\t", array_values($lineData)) . "\n"; */
     
}else{ 
    
    $excelData .= 'No records found...'. "\n"; 
} 

}

//done - please recheck
if(isset($_REQUEST['viewmemsalesreport'])){
$fileName = "viewmemsalesreport.xls"; 
// Column names 
$fields = array('Invoice Number', 'Purchase Date','Time','Terminal','Cashier','Member ID','Name','Loan','Cash','Amount' ); 
$excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
$excelData.="View Me Sales Report". "\n"; 
$excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
// Display column names as first row 
$excelData.= implode("\t", array_values($fields)) . "\n";

 
// Fetch records from database ".$_REQUEST['dataid']."
$query = "SELECT * FROM db_sales where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."' AND member_id='".$_REQUEST['memberid']."'";
$result = mysqli_query($con,$query);
if(mysqli_num_rows($result) > 0){ 
    // Output each row of the data 
    while($row = mysqli_fetch_array($result) ){

        $response[] = array("invoiceno"=>$row['invoiceno'],"cash_amount"=>$row['cash_amount'],"loan_amount"=>$row['loan_amount'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"member_id"=>$row['member_id'],"payment_mode"=>$row['payment_mode']);
    }

        $totalsale=0;
        $totalcash=0;
        $totalloan=0;
        $no=1;
        foreach($response as $key => $value)
        {
            $excelData .= $value['invoiceno']."\t";
            $excelData .= $value['sales_date']."\t";
            $excelData .= $value['sales_date']."\t";
            $excelData .= $value['station']."\t";

            if($value['sold_by'] == null){
                $excelData .= " \t";
            }else{
                $query = "SELECT * FROM db_users WHERE userid='".$value['sold_by']."'";
                $result = mysqli_query($con,$query);
                $row = mysqli_fetch_array($result); 
                $excelData .= $row['fname']." ".$row['lname']."\t";
            }

            $excelData .=  $value['member_id']."\t";

            $query = "SELECT fname,mname,lname FROM db_member WHERE bbcc_id='".$value['member_id']."'";
            $result = mysqli_query($con,$query);
            $row = mysqli_fetch_array($result); 
            if(!empty($row['lname'])){
                $excelData .=  $row['lname'].", ";
            }else{
                $excelData .=  "";
            }

            if(!empty($row['fname'])){
                $excelData .=  $row['fname']." ";
            }else{
                $excelData .=  "";
            }

            if(!empty($row['mname'])){
                $excelData .=  $row['mname']." \t";
            }else{
                $excelData .=  "\t";
            }

            $excelData .=  number_format($value['loan_amount'],2)."\t";
            $excelData .=  number_format($value['cash_amount'],2)."\t";
            $excelData .=  number_format($value['cash_amount']+$value['loan_amount'],2)."\n";

            $totalsale+=$value['cash_amount']+$value['loan_amount'];
            $totalcash+=$value['cash_amount'];$totalloan+=$value['loan_amount'];
    
        }
        $excelData .= "\t";
        $excelData .= "\t";
        $excelData .= "\t";
        $excelData .= "\t";
        $excelData .= "\t";
        $excelData .= "\t";
        $excelData .= "Total: >>> \t";
        $excelData .= number_format($totalloan,2)."\t";
        $excelData .= number_format($totalcash,2)."\t";
        $excelData .= number_format($totalsale,2)."\t";

    
        /*array_walk($lineData, 'filterData'); 
        $excelData .= implode("\t", array_values($lineData)) . "\n"; */
     
}else{ 
    
    $excelData .= 'No records found...'. "\n"; 
} 

}


//done - please recheck
if(isset($_REQUEST['rice_item'])){
$fileName = "rice_item.xls"; 
// Column names 
$fields = array('No.','Invoice No', 'Mem. No','Name','Quantity','Disc','Payment','Amount'); 
$excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
$excelData.="Rice Item". "\n"; 
$excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
// Display column names as first row 


 
// sample query for test
/*$query1 = "SELECT * FROM db_products where product_type='2'";*/

//orig query
$query1 = "SELECT * FROM db_products where product_type='".$_REQUEST['product_type']."'";
    $result1 = mysqli_query($con,$query1);
if(mysqli_num_rows($result1) > 0){ 
    // Output each row of the data 
    while($row1 = mysqli_fetch_array($result1) ){
        $query2 = "SELECT warehouse_stock,store_stock FROM db_products WHERE product_id='".$row1['product_id']."'";
        $result2 = mysqli_query($con,$query2);
        $row2 = mysqli_fetch_array($result2); 
        $totalpos=$row2['warehouse_stock']+$row2['warehouse_stock'];

        $excelData .= $row1['product_name']."\n";
        $excelData.= implode("\t", array_values($fields)) . "\n";

        $no=1;$total=0;$totalcash=0; $totalloan=0;$totalqcash=0;$totalqloan=0;$totalquantity=0;
        $query = "SELECT * FROM db_sales,db_sales_product where sales_date = '".dashDate($_REQUEST['datefrom'])."' AND db_sales.sales_id=db_sales_product.sales_id AND db_sales_product.product_id='".$row1['product_id']."'";

        // sample query for test
        /*$query = "SELECT * FROM db_sales,db_sales_product where sales_date ='2023-01-25' AND db_sales.sales_id=db_sales_product.sales_id AND db_sales_product.product_id='".$row1['product_id']."'";*/
        $result = mysqli_query($con,$query);

        while($row = mysqli_fetch_array($result) ){
            $excelData .= $no++ ."\t";
            $excelData .= $row['invoiceno'] ."\t";
            $excelData .= $row['member_id'] ."\t";
            
            $query = "SELECT fname,mname,lname FROM db_member WHERE bbcc_id='".$row['member_id']."'";
            $result = mysqli_query($con,$query);
            $row99 = mysqli_fetch_array($result); 
            if(!empty($row99['lname'])){
                $excelData .=  $row99['lname'].", ";
            }else{
                $excelData .=  "";
            }

            if(!empty($row99['fname'])){
                $excelData .=  $row99['fname']." ";
            }else{
                $excelData .=  "";
            }

            if(!empty($row99['mname'])){
                $excelData .=  $row99['mname']." \t";
            }else{
                $excelData .=  "\t";
            }

            $excelData .=  $row['quantity']." \t";
            $totalquantity+=$row['quantity'];

            $excelData .= $row['discount'] ."\t";
            $excelData .= $row['payment_mode'] ."\t";

            $amount=$row['quantity']*$row['sale_price'];
            $excelData .= number_format($amount,2) ."\t";

            $total+=$amount;
            $totalcash+=$row['cash_amount'];
            $totalloan+=$row['loan_amount'];

            if($row['payment_mode']=='cash'){ 
                $totalqcash+=$row['quantity'];
            }
            if($row['payment_mode']=='loan'){
                $totalqloan+=$row['quantity'];
            }

        }
        $excelData .= "\n";

        $excelData .= "Cash: \t";
        $excelData .= $totalqcash ." \t";
        $excelData .= "\t";
        $excelData .= "\t";

        $excelData .= "Total>> \t";
        $excelData .= $totalquantity ."\t";
        $excelData .= "\t";

        $excelData .= number_format($total,2) ."\n";

        $excelData .= "Charge: \t";
        $excelData .= $totalqloan ." \t";

        $excelData .= "\t";
        $excelData .= "\t";
        $excelData .= "Loan >> \t";
        $excelData .= "\t";
        $excelData .= "\t";

        $excelData .= number_format($totalloan,2) ." \n";

        $excelData .= "POS: \t";
        $excelData .= $totalpos ." \t";

        $excelData .= "\t";
        $excelData .= "\t";
        $excelData .= "Cash >> \t";
        $excelData .= "\t";
        $excelData .= "\t";

        $excelData .= number_format($totalcash,2) ." \n\n";
    }
    
     
}else{ 
    
    $excelData .= 'No records found...'. "\n"; 
} 

}


//generates view sale report excel
if(isset($_REQUEST['viewsalesreport'])){
    $fileName = "viewsalesreport.xls"; 
    // Column names 
    $fields = array('INVOICE NUMBER', 'PURCHASE DATE', 'TIME','TERMINAL', 'CASHIER', 'MEMBER ID', 'NAME', 'LOAN', 'CASH', 'AMOUNT'); 
    $excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
    $excelData.="View Sales Report". "\n"; 
    $excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
    // Display column names as first row 
    $excelData.= implode("\t", array_values($fields)) . "\n"; 
    
    /*global $con;*/
     
    // Fetch records from database 
    $query = "SELECT * FROM db_sales where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."'";
    $result = mysqli_query($con,$query);
    if(mysqli_num_rows($result) > 0){ 
        // Output each row of the data 

        while($row = mysqli_fetch_array($result) ){

            $response[] = array("invoiceno"=>$row['invoiceno'],"cash_amount"=>$row['cash_amount'],"loan_amount"=>$row['loan_amount'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"member_id"=>$row['member_id'],"payment_mode"=>$row['payment_mode']);
        }

        $totalsale=0;
        $totalcash=0;
        $totalloan=0;
        $no=1;
        foreach($response as $key => $value){
            $excelData .= $value['invoiceno'] ."\t";
            $excelData .= $value['sales_date'] ."\t";
            $excelData .= $value['sales_date'] ."\t";
            $excelData .= $value['station'] ."\t";

            $query = "SELECT * FROM db_users WHERE userid='".$value['sold_by']."'";
            $result = mysqli_query($con,$query);
            $row = mysqli_fetch_array($result); 
            if(!empty($row['fname'])){
                $excelData .=  $row['fname'].", ";
            }else{
                $excelData .=  "";
            }
            if(!empty($row['lname'])){
                $excelData .=  $row['lname']." \t";
            }else{
                $excelData .=  "\t";
            }

            $excelData .= $value['member_id'] ."\t";

            $query = "SELECT fname,mname,lname FROM db_member WHERE bbcc_id='".$value['member_id']."'";
            $result = mysqli_query($con,$query);
            $row99 = mysqli_fetch_array($result); 
            if(!empty($row99['lname'])){
                $excelData .=  $row99['lname'].", ";
            }else{
                $excelData .=  "";
            }

            if(!empty($row99['fname'])){
                $excelData .=  $row99['fname']." ";
            }else{
                $excelData .=  "";
            }

            if(!empty($row99['mname'])){
                $excelData .=  $row99['mname']." \t";
            }else{
                $excelData .=  "\t";
            }

            $excelData .= number_format($value['loan_amount'],2) ."\t";
            $excelData .= number_format($value['cash_amount'],2) ."\t";
            $excelData .= number_format($value['cash_amount']+$value['loan_amount'],2) ."\n";

            $totalsale+=$value['cash_amount']+$value['loan_amount'];
            $totalcash+=$value['cash_amount'];$totalloan+=$value['loan_amount'];
        }

        $excelData .=  "\t";
        $excelData .=  "\t";
        $excelData .=  "\t";
        $excelData .=  "\t";
        $excelData .=  "\t";
        $excelData .=  "\t";
        $excelData .=  "Total >>> \t";
        $excelData .= number_format($totalloan,2) ."\t";
        $excelData .= number_format($totalcash,2) ."\t";
        $excelData .= number_format($totalsale,2) ."\t";

        
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