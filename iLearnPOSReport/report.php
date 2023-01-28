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

if(isset($_REQUEST['dataid'])){
    $fileName = "dataid.xls"; 
    $excelData="Baguio-Benguet Community Credit Cooperative". "\n"; 
    $excelData.="Data ID". "\n"; 
    $excelData.=$_REQUEST['datefrom']." - ".$_REQUEST['dateto']."\n\n"; 
    // Display column names as first row 
    // $excelData.= implode("\t", array_values($fields)) . "\n"; 

    $query1 = "SELECT product_name FROM db_products WHERE product_id='".$_REQUEST['dataid']."'";
    $result1 = mysqli_query($con,$query1);
    $row1 = mysqli_fetch_array($result1); 

    $excelData.= $row1['product_name'] ."\n\n";

    // Column names 
    $fields = array('INVOICE NO', 'DATE', 'TIME', 'MEM. NO', 'NAME', 'QUANTITY', 'DISC', 'AMOUNT'); 
    $excelData.= implode("\t", array_values($fields)) ."\n";

    $total=0;
     
    // Fetch records from database 
    $query = "SELECT * FROM db_sales,db_sales_product where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."' AND db_sales.sales_id=db_sales_product.sales_id AND db_sales_product.product_id='".$_REQUEST['dataid']."'";
    $result = $db->query($query);
    if($result->num_rows > 0){ 
        $totalquantity = 0;
        $total = 0;
        // Output each row of the data 
            while($row = $result->fetch_assoc() ){
                $query2 = "SELECT fname,mname,lname FROM db_member WHERE bbcc_id='".$row['member_id']."'";
                $result2 = mysqli_query($con,$query2);
                $row2 = mysqli_fetch_array($result2); 

                $totalquantity+=$row['quantity'];
                $amount=$row['quantity']*$row['sale_price'];
                $total+=$amount;

                //time value is non existent
                $time = "no time";

                $response1 = array("invoiceno"=>$row['invoiceno'],"sales_date"=>$row['sales_date'],"time"=>$time,"member_id"=>$row['member_id'],"name"=>$row2['lname'].", ".$row2['fname']." ".$row2['mname'],"quantity"=>$row['quantity'],"discount"=>$row['discount'],"amount"=>number_format($amount,2));
                
                array_walk($response1, 'filterData'); 
                    
                $excelData .= implode("\t", array_values($response1)) . "\n"; 
            }
            
            $excelData.= "TOTAL:\t" .$totalquantity."\t\t".number_format($total,2);
    
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

header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData; 



exit;
?>