<?php 
session_start();
$host = "localhost";    /* Host name */
$user = "root";         /* User */
$password = "";         /* Password */
$dbname = "bbcccpos";   /* Database name */

// Create connection
$con = mysqli_connect($host, $user, $password,$dbname);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

include ('../functions/supplier.php');
if(isset($_REQUEST['viewsalesreport'])){
    $query = "SELECT * FROM db_sales where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."'";
    $result = mysqli_query($con,$query);
    
    while($row = mysqli_fetch_array($result) ){

        $response[] = array("invoiceno"=>$row['invoiceno'],"cash_amount"=>$row['cash_amount'],"loan_amount"=>$row['loan_amount'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"member_id"=>$row['member_id'],"payment_mode"=>$row['payment_mode']);
    }
 ?>
 <div><a href="contents/excel_sales_report.php?viewsalesreport=viewsalesreport&datefrom=<?php echo dashDate($_REQUEST['datefrom']); ?>&dateto=<?php echo dashDate($_REQUEST['dateto']); ?>">Export to Excel </div>
  
 <table  id="file-datatable" class="table table-hover mb-0 text-md-nowrap key-buttons border-bottom">
										<thead>
											<tr>
												<th></th>
												<th>Invoice Number</th>
												<th>Purchase Date</th>
												<th>Time</th>
                                                <th>Terminal</th>
                                                <th>Cashier</th>
												<th>Member ID</th>
												<th>Name </th>
                                                <th>Loan</th>
												<th>Cash</th>
												<th>Amount (₱)</th>
											</tr>
										</thead>
										<tbody>
                                            <?php 
                                            $totalsale=0;
                                            $totalcash=0;
                                            $totalloan=0;
                                            $no=1;
                                            foreach($response as $key => $value) 
{ ?>
											<tr>
												<th scope="row"><?php $no++;?></th>
												<td><?php echo $value['invoiceno']?></td>
												<td><?php echo $value['sales_date']?></td>
                                                <td><?php echo $value['sales_date']?></td>
                                                <td><?php echo $value['station']?></td>
                                                <td><?php echo get_user($value['sold_by']);?></td>
                                                <td><?php echo $value['member_id'];?></td>
												<td><?php echo get_member($value['member_id']);?></td>
                                               
												<td><?php echo number_format($value['loan_amount'],2);?></td>
												<td><?php echo number_format($value['cash_amount'],2);?></td>
                                                <td><?php echo number_format($value['cash_amount']+$value['loan_amount'],2);?></td>
											</tr>
                                            <?php $totalsale+=$value['cash_amount']+$value['loan_amount'];
                                            $totalcash+=$value['cash_amount'];$totalloan+=$value['loan_amount'];

                                         } ?>
											<tr>
												<th></th>
												<td></td>
												<td></td>
												<td></td>
                                                <td></td>
												<td></td>
                                                <td></td>
                                                <th>Total >>></th>
												<th><?php echo number_format($totalloan,2);?></th>
                                                <th><?php echo number_format($totalcash,2);?></th>
												<th><?php echo number_format($totalsale,2);?></th>
											</tr>
										</tbody>
									</table>
 <? 



}
if(isset($_REQUEST['viewdetailedsalesreport'])){
    $query = "SELECT * FROM db_sales where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."'";
    $result = mysqli_query($con,$query);
    
    while($row = mysqli_fetch_array($result) ){

        $response[] = array("invoiceno"=>$row['invoiceno'],"gross_sale"=>$row['gross_sale'],"discount_amount"=>$row['discount_amount'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"member_id"=>$row['member_id'],"payment_mode"=>$row['payment_mode'],"sales_id"=>$row['sales_id']);
    }
 ?>
 <div><a href="contents/excel_sales_report.php?viewdetailedsalesreport=viewdetailedsalesreport&datefrom=<?php echo dashDate($_REQUEST['datefrom']); ?>&dateto=<?php echo dashDate($_REQUEST['dateto']); ?>">Export to Excel </div>
  
<table  id="file-datatable" class="table table-hover mb-0 text-md-nowrap key-buttons border-bottom">
<?php 
                                            $totalsale=0;
                                            foreach($response as $key => $value) 
{ ?>
										<thead>
											<tr>
												<th colspan="2">Invoice Number: <?php echo $value['invoiceno']?></th>
												<th>Cashier: <?php echo get_user($value['sold_by']);?></th>
												<th></th>
												<th></th>
                                             
                                              
											</tr>
                                            <tr>
												<th colspan="2">Invoice Date: <?php echo $value['sales_date']?></th>
												<th>Terminal: <?php echo $value['station']?></th>
												<th></th>
												<th></th>
                                               
                                              
											</tr>
										</thead>
										<tbody>
                                        <tr>
												<th>Barcode</th>
												<th>Product Name</th>
												<th>Qty</th>
												<th>Price</th>
                                                <th>Item Discount(%) </th>
                                                <th>Amount</th>
                                              
											</tr>
                                            <?php
                                            $invoiceamount=0;
                                            $query = "SELECT * FROM db_sales_product where sales_id = '".$value['sales_id']."'";
    $result = mysqli_query($con,$query);
    
    while($row = mysqli_fetch_array($result) ){ ?>
											<tr>
                                                 <td><?php get_barcode($row['product_id'])?></td>
												<td><?php get_prodname($row['product_id'])?></td>
												<td><?php echo $row['quantity']?></td>
                                                <td><?php echo number_format($row['sale_price'],2)?></td>
                                                <td><?php echo $row['discount']."%";?></td>
                                                <td><?php 
                                                $amount=$row['quantity']*$row['sale_price'];
                                                echo number_format($amount,2);?></td>
                                                </tr>
                                                            <?php  
                                                        $invoiceamount+=$amount;
                                                        }       ?>
                                                        <tr>
                                                 <td></td>
                                                 <td></td>
												<td></td>
												<td></td>
                                                <th>Discount(-)</th>
                                                <td><?php echo  number_format($value['discount_amount'],2);?></td>
                                                </tr>
                                                <tr>
                                                <tr>
                                                 <td></td>
                                                 <td></td>
												<td></td>
												<td></td>
                                                <th>Invoice Amount</th>
                                                <td><?php echo  number_format($invoiceamount-$value['discount_amount'],2);?></td>
                                                </tr>
                                                <tr>
												
											
                                                <td colspan="6"></td>
                                                </tr>
										</tbody>
                                        <? 



}



?>

									</table>
 <? 



}

if(isset($_REQUEST['saleable_items'])){
    $query = "SELECT SUM(db_sales_product.quantity) AS quantity_sum,SUM(db_sales_product.sale_price) AS amount_sum, db_sales_product.product_id,db_sales_product.sale_price FROM db_sales,db_sales_product where  db_sales.sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND  db_sales.sales_date <=  '".dashDate($_REQUEST['dateto'])."' AND db_sales_product.sales_id=db_sales.sales_id GROUP by db_sales_product.product_id ORDER by quantity_sum DESC";
    $result = mysqli_query($con,$query);
       
    
?><div><a href="contents/excel_sales_report.php?saleableitems=saleableitems&datefrom=<?php echo dashDate($_REQUEST['datefrom']); ?>&dateto=<?php echo dashDate($_REQUEST['dateto']); ?>">Export to Excel </div>
    <table id="file-datatable" class="border-top-0  table table-bordered text-nowrap key-buttons border-bottom">
    <thead>
        <tr>
            <th></th>
            <th>Barcode</th>
            <th>Product Name</th>
            <th>Pieces</th>
           
            <th>Amount (₱)</th>
        </tr>
    </thead>
    <tbody>
        <?php   $no=1;
        while($row = mysqli_fetch_array($result) ){
          
 ?>
        <tr>
            <th scope="row"><?php echo $no++;?></th>
            <td><?php echo get_barcode($row['product_id']);?></td>
            <td><?php echo get_prodname($row['product_id']);?></td>
            <td><?php echo $row['quantity_sum'];?></td>
            <td><?php echo number_format($row['sale_price']*$row['quantity_sum'],2);?></td>
          
        </tr>
        <?php  } ?>
    </tbody>
</table>
<? 



}
if(isset($_REQUEST['dataid'])){
    echo get_prodname($_REQUEST['dataid']);
    ?>
 <table class="table table-hover mb-0 text-md-nowrap">
    <thead>
        <tr>
            <th></th>
            <th>Invoice No</th>
            <th>Date</th>
            <th>Time</th>
            <th>Mem. No</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Disc</th>
           
            <th>Amount (₱)</th>
        </tr>
    </thead>
    <tbody>
    <?php $no=1;$total=0;
      $query = "SELECT * FROM db_sales,db_sales_product where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."' AND db_sales.sales_id=db_sales_product.sales_id AND db_sales_product.product_id='".$_REQUEST['dataid']."'";
      $result = mysqli_query($con,$query);
      
      while($row = mysqli_fetch_array($result) ){
    ?>
        <tr>
            <th scope="row"><?php echo $no++;?></th>
            <td><?php echo $row['invoiceno'];?></td>
            <td><?php echo $row['sales_date'];?></td>
            <td><?php echo $row['invoiceno'];?></td>
            <td><?php echo $row['member_id'];?></td>
            <td><?php echo get_member($row['member_id']);?></td>
            <td><?php echo $row['quantity'];
            $totalquantity+=$row['quantity'];
            ?></td>
            <td><?php echo $row['discount'];?></td>
            <td><?php 
         
            $amount=$row['quantity']*$row['sale_price'];
            echo number_format($amount,2);
            $total+=$amount;?>
        </td>
          
        </tr>
     <?php } ?>
     <tr>
                                                 <td></td>
												<td></td>
												<td></td>
                                                <td></td>
												<td></td>
                                                <th>Total>></th>
                                                <th><?php echo $totalquantity;?></th>
												<td></td>
                                                
                                               
                                                <th><?php echo  number_format($total,2);?></th>
                                                </tr>
    </tbody>
</table>

<?php
}

if(isset($_REQUEST['viewtallyreport'])){
   
                                     $t=time()+28800;
                                     $thedate=date("Y-m-d",$t);
                                    
                                     $query = "SELECT * FROM db_sales WHERE sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."' group by sales_date";
                                     $result = mysqli_query($con,$query);
    
                                     while($row = mysqli_fetch_array($result) ){
										$response[] = array("sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by']);
                                    }?>
 <table class="table table-hover mb-0 text-md-nowrap">
    <thead>
        <tr>
            <th></th>
            <th>Date</th>
           
           
            <th>Total (₱)</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total=0;
        foreach($response as $key => $value) 
        {
        ?>
        <tr>
        <td> </td>
        <td><?php echo $value['sales_date'];?> </td>
        <?php $query = "SELECT sum(cash_amount) as totalcash,station,sum(loan_amount) as totalloan,sold_by FROM db_sales where sales_date = '".$value['sales_date']."' group by sold_by ASC";
         $result = mysqli_query($con,$query);
         while($row = mysqli_fetch_array($result) ){?>
         <td><?php echo $row['totalcash']+$row['totalloan'];?></td>
        <?php 
    $total+=$row['totalcash']+$row['totalloan'];
    } ?>
        <td><?php echo $total;?></td>
        </tr>
       <?php } ?>
    </tbody>
</table>       
<?php
}

if(isset($_REQUEST['viewprofitreport'])){
    $query = "SELECT * FROM db_sales where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."'";
    $result = mysqli_query($con,$query);
    
    while($row = mysqli_fetch_array($result) ){

        $response[] = array("invoiceno"=>$row['invoiceno'],"cash_amount"=>$row['cash_amount'],"total_sales"=>$row['total_sales'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"total_cost"=>$row['total_cost'],"payment_mode"=>$row['payment_mode']);
    }
 ?>
 <table class="table table-hover mb-0 text-md-nowrap">
										<thead>
											<tr>
												<th></th>
												<th>Invoice Number</th>
												<th>Purchase Date</th>
												
                                                <th>Terminal</th>
                                                <th>Amount (₱)</th>
												<th>Cost (₱)</th>
												<th>Profit (₱)</th>
                                                <th>% Markup</th>
											
											</tr>
										</thead>
										<tbody>
                                            <?php 
                                           
                                            $totalloan=0;
                                            $no=1;
                                            foreach($response as $key => $value) 

{  $profit=0;
                                            $markup=0;?>    
											<tr>
												<th scope="row"><?php $no++;?></th>
												<td><?php echo $value['invoiceno']?></td>
												<td><?php echo $value['sales_date']?></td>
                                                  <td><?php echo $value['station']?></td>
                                                  <td><?php echo number_format($value['total_sales'],2);?></td>
                                               
												
												<td><?php echo number_format($value['total_cost'],2);?></td>
                                                <td><?php 
                                                $profit=$value['total_sales']-$value['total_cost'];
                                                echo number_format($profit,2);?></td>
                                                <td><?php 
                                                $markup=100*($profit/$value['total_cost']);
                                                echo number_format($markup,2)."%";?></td>
											</tr>
                                            <?php

                                         } ?>
										
										</tbody>
									</table>
 <?php 
}

if(isset($_REQUEST['viewtransferreport'])){
  
    $query = "SELECT * FROM db_transfers where transfer_date >= '".dashDate($_REQUEST['datefrom'])."' AND transfer_date <=  '".dashDate($_REQUEST['dateto'])."'";
    $result = mysqli_query($con,$query);
    while($row = mysqli_fetch_array($result) ){

     $response[] = array("product_id"=>$row['product_id'],"quantity"=>$row['quantity'],"current_price"=>$row['current_price'],"area"=>$row['area']);
    }

?>
<table class="table table-hover mb-0 text-md-nowrap">
										<thead>
											<tr>
												<th></th>
												<th>Barcode</th>
												<th>Item Description</th>
												<th>Quantity</th>
                                                <th>Price</th>
                                                <th>Amount</th>
												
											</tr>
										</thead>
										<tbody>
                                            <?php 
                                           $no=1;$total=0;
                                            foreach($response as $key => $value) 
{ ?>
											<tr>
												<th scope="row"><?php $no++;?></th>
												<td><?php get_barcode($value['product_id']);?></td>
												<td><?php get_prodname($value['product_id']);?></td>
                                                <td><?php echo $value['quantity']?></td>
                                                <td><?php echo $value['current_price']?></td>
                                                <td><?php echo number_format($value['quantity']*$value['current_price'],2) ?></td>
                                               
											</tr>
                                           <?php
$total+=($value['quantity']*$value['current_price']);
                                         } ?>
											<tr>
												<th scope="row"><?php $no++;?></th>
												<td></td>	<td></td>	<td></td>
												
                                                <th>Total Amount</th>
                                                <td><?php echo number_format($total,2) ?></td>
                                               
											</tr>
										</tbody>
									</table>
 
<?php
}

if(isset($_REQUEST['viewpurchasereturnreport'])){
  
    $query = "SELECT * FROM db_purchase_return where return_date >= '".dashDate($_REQUEST['datefrom'])."' AND return_date <=  '".dashDate($_REQUEST['dateto'])."'";
    $result = mysqli_query($con,$query);
    while($row = mysqli_fetch_array($result) ){

     $response[] = array("return_id"=>$row['id'],"supplier_id"=>$row['supplier_id'],"return_area"=>$row['return_area'],"return_date"=>$row['return_date']);
    }

?>
<?php 
                                           $no=1;$total=0;
                                            foreach($response as $key => $value) 
{ 
    ?>
<?php switch($value['return_area']){
  case 1: echo "Warehouse<br>";break;
  case 2: echo "Selling area<br>";break;
  default: "";  
}?>
Supplier: <?php supplier($value['supplier_id']);?>
<br>
PR No. <?php echo $value['return_id']."&nbsp;&nbsp;";?> <?php  echo $value['return_date']."<br>";?>
<table class="table table-hover mb-0 text-md-nowrap">
										<thead>
                                        <tr>
												<th>Barcode</th>
												<th>Product Name</th>
												<th>Quantity</th>
												<th>Unit Cost</th>
												<th>Amount (₱)</th>
												<th>Remarks</th>
                                              
												
											</tr>
										</thead>
										<tbody>
                                            

    <?php
    $total=0;
    $query2 = "SELECT * FROM db_prod_purchase_return where return_id= '".$value['return_id']."'";
    $result2 = mysqli_query($con,$query2);
    while($row2= mysqli_fetch_array($result2) ){
    $total+=($row2['quantity']*$row2['price']);
    ?>
											<tr>
												
                                            <td><?php get_barcode($row2['item_id']);?></td>	
                                            <td><?php get_prodname($row2['item_id']);?></td>	
                                            <td><?php echo $row2['quantity'];?></td>
                                            <td><?php echo $row2['price'];?></td>	
                                            <td><?php echo number_format($row2['quantity']*$row2['price'],2);?></td>	
                                            <td></td>
                                               
											</tr>

                                           <?php

                                         }?>
											<tr>
												
                                                	<td></td><td></td><td></td><td>Total: </td><td><?php echo number_format($total,2);?></td><td></td>
                                                </tr>  
										</tbody>
									</table>
 
<?php
}}

if(isset($_REQUEST['viewmemsalesreport'])){
    $query = "SELECT * FROM db_sales where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."' AND member_id='".$_REQUEST['memberid']."'";
    $result = mysqli_query($con,$query);
    
    while($row = mysqli_fetch_array($result) ){

        $response[] = array("invoiceno"=>$row['invoiceno'],"cash_amount"=>$row['cash_amount'],"loan_amount"=>$row['loan_amount'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"member_id"=>$row['member_id'],"payment_mode"=>$row['payment_mode']);
    }
 ?>
 <table  id="file-datatable" class="table table-hover mb-0 text-md-nowrap key-buttons border-bottom">
										<thead>
											<tr>
												<th></th>
												<th>Invoice Number</th>
												<th>Purchase Date</th>
												<th>Time</th>
                                                <th>Terminal</th>
                                                <th>Cashier</th>
												<th>Member ID</th>
												<th>Name </th>
                                                <th>Loan</th>
												<th>Cash</th>
												<th>Amount (₱)</th>
											</tr>
										</thead>
										<tbody>
                                            <?php 
                                            $totalsale=0;
                                            $totalcash=0;
                                            $totalloan=0;
                                            $no=1;
                                            foreach($response as $key => $value) 
{ ?>
											<tr>
												<th scope="row"><?php $no++;?></th>
												<td><?php echo $value['invoiceno']?></td>
												<td><?php echo $value['sales_date']?></td>
                                                <td><?php echo $value['sales_date']?></td>
                                                <td><?php echo $value['station']?></td>
                                                <td><?php echo get_user($value['sold_by']);?></td>
                                                <td><?php echo $value['member_id'];?></td>
												<td><?php echo get_member($value['member_id']);?></td>
                                               
												<td><?php echo number_format($value['loan_amount'],2);?></td>
												<td><?php echo number_format($value['cash_amount'],2);?></td>
                                                <td><?php echo number_format($value['cash_amount']+$value['loan_amount'],2);?></td>
											</tr>
                                            <?php $totalsale+=$value['cash_amount']+$value['loan_amount'];
                                            $totalcash+=$value['cash_amount'];$totalloan+=$value['loan_amount'];

                                         } ?>
											<tr>
												<th></th>
												<td></td>
												<td></td>
												<td></td>
                                                <td></td>
												<td></td>
                                                <td></td>
                                                <th>Total >>></th>
												<th><?php echo number_format($totalloan,2);?></th>
                                                <th><?php echo number_format($totalcash,2);?></th>
												<th><?php echo number_format($totalsale,2);?></th>
											</tr>
										</tbody>
									</table>
 <?php 



}

if(isset($_REQUEST['viewmemdetailedreport'])){
    $query = "SELECT * FROM db_sales where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."' AND member_id='".$_REQUEST['memberid']."'";
    $result = mysqli_query($con,$query);
    
    while($row = mysqli_fetch_array($result) ){

        $response[] = array("invoiceno"=>$row['invoiceno'],"gross_sale"=>$row['gross_sale'],"discount_amount"=>$row['discount_amount'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"member_id"=>$row['member_id'],"payment_mode"=>$row['payment_mode'],"sales_id"=>$row['sales_id']);
    }
 ?>
<table  id="file-datatable" class="table table-hover mb-0 text-md-nowrap key-buttons border-bottom">
<?php 
                                            $totalsale=0;
                                            foreach($response as $key => $value) 
{ ?>
										<thead>
											<tr>
												<th colspan="2">Invoice Number: <?php echo $value['invoiceno']?></th>
												<th>Cashier: <?php echo get_user($value['sold_by']);?></th>
												<th></th>
												<th></th>
                                             
                                              
											</tr>
                                            <tr>
												<th colspan="2">Invoice Date: <?php echo $value['sales_date']?></th>
												<th>Terminal: <?php echo $value['station']?></th>
												<th></th>
												<th></th>
                                               
                                              
											</tr>
										</thead>
										<tbody>
                                        <tr>
												<th>Barcode</th>
												<th>Product Name</th>
												<th>Qty</th>
												<th>Price</th>
                                                <th>Item Discount(%) </th>
                                                <th>Amount</th>
                                              
											</tr>
                                            <?php
                                            $invoiceamount=0;
                                            $query = "SELECT * FROM db_sales_product where sales_id = '".$value['sales_id']."'";
    $result = mysqli_query($con,$query);
    
    while($row = mysqli_fetch_array($result) ){ ?>
											<tr>
                                                 <td><?php get_barcode($row['product_id'])?></td>
												<td><?php get_prodname($row['product_id'])?></td>
												<td><?php echo $row['quantity']?></td>
                                                <td><?php echo number_format($row['sale_price'],2)?></td>
                                                <td><?php echo $row['discount']."%";?></td>
                                                <td><?php 
                                                $amount=$row['quantity']*$row['sale_price'];
                                                echo number_format($amount,2);?></td>
                                                </tr>
                                                            <?php  
                                                        $invoiceamount+=$amount;
                                                        }       ?>
                                                        <tr>
                                                 <td></td>
                                                 <td></td>
												<td></td>
												<td></td>
                                                <th>Discount(-)</th>
                                                <td><?php echo  number_format($value['discount_amount'],2);?></td>
                                                </tr>
                                                <tr>
                                                <tr>
                                                 <td></td>
                                                 <td></td>
												<td></td>
												<td></td>
                                                <th>Invoice Amount</th>
                                                <td><?php echo  number_format($invoiceamount-$value['discount_amount'],2);?></td>
                                                </tr>
                                                <tr>
												
											
                                                <td colspan="6"></td>
                                                </tr>
										</tbody>
                                        <?php 



}



?>

									</table>
 <?php 



}
if(isset($_REQUEST['viewreturns'])){
    $query = "SELECT * FROM db_sales,db_sales_product where db_sales.sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND db_sales.sales_date <=  '".dashDate($_REQUEST['dateto'])."' AND db_sales.sales_id=db_sales_product.sales_id AND db_sales_product.trans_type='2'";
    $result = mysqli_query($con,$query);
    
    while($row = mysqli_fetch_array($result) ){

        $response[] = array("invoiceno"=>$row['invoiceno'],"gross_sale"=>$row['gross_sale'],"discount_amount"=>$row['discount_amount'],"station"=>$row['station'],"sales_date"=>$row['sales_date'],"sold_by"=>$row['sold_by'],"member_id"=>$row['member_id'],"payment_mode"=>$row['payment_mode'],"sales_id"=>$row['sales_id']);
    }
 ?>
<table  id="file-datatable" class="table table-hover mb-0 text-md-nowrap key-buttons border-bottom">
<?php 
                                            $totalsale=0;
                                            foreach($response as $key => $value) 
{ ?>
										<thead>
											<tr>
												<th colspan="2">Invoice Number: <?php echo $value['invoiceno']?></th>
												<th>Cashier: <?php echo get_user($value['sold_by']);?></th>
												<th></th>
												<th></th>
                                             
                                              
											</tr>
                                            <tr>
												<th colspan="2">Invoice Date: <?php echo $value['sales_date']?></th>
												<th>Terminal: <?php echo $value['station']?></th>
												<th></th>
												<th></th>
                                               
                                              
											</tr>
										</thead>
										<tbody>
                                        <tr>
												<th>Barcode</th>
												<th>Product Name</th>
												<th>Qty</th>
												<th>Price</th>
                                                <th>Item Discount(%) </th>
                                                <th>Amount</th>
                                              
											</tr>
                                            <?php
                                            $invoiceamount=0;
                                            $query = "SELECT * FROM db_sales_product where sales_id = '".$value['sales_id']."'";
    $result = mysqli_query($con,$query);
    
    while($row = mysqli_fetch_array($result) ){ ?>
											<tr>
                                                 <td><?php get_barcode($row['product_id'])?></td>
												<td><?php get_prodname($row['product_id'])?></td>
												<td><?php echo $row['quantity']?></td>
                                                <td><?php echo number_format($row['sale_price'],2)?></td>
                                                <td><?php echo $row['discount']."%";?></td>
                                                <td><?php 
                                                $amount=$row['quantity']*$row['sale_price'];
                                                echo number_format($amount,2);?></td>
                                                </tr>
                                                            <?php  
                                                        $invoiceamount+=$amount;
                                                        }       ?>
                                                        <tr>
                                                 <td></td>
                                                 <td></td>
												<td></td>
												<td></td>
                                                <th>Discount(-)</th>
                                                <td><?php echo  number_format($value['discount_amount'],2);?></td>
                                                </tr>
                                                <tr>
                                                <tr>
                                                 <td></td>
                                                 <td></td>
												<td></td>
												<td></td>
                                                <th>Invoice Amount</th>
                                                <td><?php echo  number_format($invoiceamount-$value['discount_amount'],2);?></td>
                                                </tr>
                                                <tr>
												
											
                                                <td colspan="6"></td>
                                                </tr>
										</tbody>
                                        <?php 



}



?>

									</table>
 <?php 



}
if(isset($_REQUEST['viewpurchaseinvoice'])){
$_SESSION['purchase_id']=$_REQUEST['purchase_id'];
}
if(isset($_REQUEST['archievepurchaseinvoice'])){

    $sql = "UPDATE db_purchase SET purchase_status='2' WHERE purchase_id='".$_REQUEST['purchase_id']."'";

if ($con->query($sql) === TRUE) {
  
} else {
 
}
   // $_SESSION['purchase_id']=$_REQUEST['purchase_id'];
    }
if(isset($_REQUEST['viewdeliveryreport'])){
        $query = "SELECT * FROM db_delivery where rr_date >= '".dashDate($_REQUEST['datefrom'])."' AND rr_date <=  '".dashDate($_REQUEST['dateto'])."'";
        $result = mysqli_query($con,$query);
        
        while($row = mysqli_fetch_array($result) ){
    
            $response[] = array("delivery_id"=>$row['delivery_id'],"invoiceno"=>$row['invoice_no'],"rr_date"=>$row['rr_date'],"supplier_id"=>$row['supplier_id'],"subtotal"=>$row['subtotal'],"net_amount"=>$row['net_amount'],"payment_status"=>$row['payment_status']);
        }
     ?>
     <table  id="file-datatable" class="table table-hover mb-0 text-md-nowrap key-buttons border-bottom">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Invoice Number</th>
                                                    <th>RR Date</th>
                                                    <th>Supplier</th>
                                                    <th>Subtotal (₱)</th>
                                                    <th>Net Amount (₱)</th>
                                                    <th>Payment Status</th>
                                                    <th>View</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $totalsale=0;
                                                $totalcash=0;
                                                $totalloan=0;
                                                $no=1;
                                                foreach($response as $key => $value) 
    { ?>
                                                <tr>
                                                    <th scope="row"><?php $no++;?></th>
                                                    <td><?php echo $value['invoiceno']?></td>
                                                    <td><?php echo $value['rr_date']?></td>
                                                    <td><?php supplier($value['supplier_id']);?></td>
                                                    <td><?php echo $value['subtotal']?></td>
                                                    <td><?php echo $value['net_amount']?></td>
                                                    <td><?php echo ($value['payment_status']=="1") ? "Paid" : "";?></td>
                                                    <td><button type="button" class="btn btn-primary" id="Delivery<?php echo $no;?>">Action</button>
                                                    <input type="hidden" value="<?php echo $value['delivery_id'];?>" id="delivery_id<?php echo $no;?>">						
						
						<script>
$(document).ready(function(){
       													 $("#Delivery<?php echo $no;?>").click(function(){
 
														jQuery.ajax({
                                                            url: "contents/viewdelivery.php",
                                                            data:"delivery_id="+$("#delivery_id<?php echo $no;?>").val(),
                                                            type: "POST",
                                                            success:function(data){
																window.location.replace("index.php?page=deliverydetailed");
															    },
                                                                error:function (){}
                                                            });
        });
    });
</script>				
                                                </td>
                                                </tr>
                                                <?php // $totalsale+=$value['cash_amount']+$value['loan_amount'];
                                                //$totalcash+=$value['cash_amount'];$totalloan+=$value['loan_amount'];
    
                                             } ?>
                                                <tr>
                                                    <th></th>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    
                                                    <th>Total >>></th>
                                                    <th><?php echo number_format($totalloan,2);?></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </tbody>
                                        </table>
     <?php 
    
    
    
    }

    if(isset($_REQUEST['dataid'])){
    echo get_prodname($_REQUEST['dataid']);
    ?>
 <table class="table table-hover mb-0 text-md-nowrap">
    <thead>
        <tr>
            <th></th>
            <th>Invoice No</th>
            <th>Date</th>
            <th>Time</th>
            <th>Mem. No</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Disc</th>
           
            <th>Amount (₱)</th>
        </tr>
    </thead>
    <tbody>
    <?php $no=1;$total=0;
      $query = "SELECT * FROM db_sales,db_sales_product where sales_date >= '".dashDate($_REQUEST['datefrom'])."' AND sales_date <=  '".dashDate($_REQUEST['dateto'])."' AND db_sales.sales_id=db_sales_product.sales_id AND db_sales_product.product_id='".$_REQUEST['dataid']."'";
      $result = mysqli_query($con,$query);
      
      while($row = mysqli_fetch_array($result) ){
    ?>
        <tr>
            <th scope="row"><?php echo $no++;?></th>
            <td><?php echo $row['invoiceno'];?></td>
            <td><?php echo $row['sales_date'];?></td>
            <td><?php echo $row['invoiceno'];?></td>
            <td><?php echo $row['member_id'];?></td>
            <td><?php echo get_member($row['member_id']);?></td>
            <td><?php echo $row['quantity'];
            $totalquantity+=$row['quantity'];
            ?></td>
            <td><?php echo $row['discount'];?></td>
            <td><?php 
         
            $amount=$row['quantity']*$row['sale_price'];
            echo number_format($amount,2);
            $total+=$amount;?>
        </td>
          
        </tr>
     <?php } ?>
     <tr>
                                                 <td></td>
												<td></td>
												<td></td>
                                                <td></td>
												<td></td>
                                                <th>Total>></th>
                                                <th><?php echo $totalquantity;?></th>
												<td></td>
                                                
                                               
                                                <th><?php echo  number_format($total,2);?></th>
                                                </tr>
    </tbody>
</table>

<?php
}

if(isset($_REQUEST['rice_item'])){
    echo get_prodname($_REQUEST['dataid']);
    $query1 = "SELECT * FROM db_products where product_type='".$_REQUEST['product_type']."'";
    $result1 = mysqli_query($con,$query1);
    while($row1 = mysqli_fetch_array($result1) ){
        $query2 = "SELECT warehouse_stock,store_stock FROM db_products WHERE product_id='".$row1['product_id']."'";
        $result2 = mysqli_query($con,$query2);
        $row2 = mysqli_fetch_array($result2); 
        $totalpos=$row2['warehouse_stock']+$row2['warehouse_stock'];
    ?>
 <table class="table table-hover mb-0 text-md-nowrap">
    <thead>
    <tr>
            <th><h6><?php echo $row1['product_name'];?></h6></th>
            <th></th>
            <th></th>
          
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
           
        </tr>
        <tr>
            <th></th>
            <th>Invoice No</th>
         
            <th>Mem. No</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Disc</th>
            <th>Payment</th>
            <th>Amount (₱)</th>
        </tr>
    </thead>
    <tbody>
    <?php $no=1;$total=0;$totalcash=0; $totalloan=0;$totalqcash=0;$totalqloan=0;$totalquantity=0;
   
      $query = "SELECT * FROM db_sales,db_sales_product where sales_date = '".dashDate($_REQUEST['datefrom'])."' AND db_sales.sales_id=db_sales_product.sales_id AND db_sales_product.product_id='".$row1['product_id']."'";
      $result = mysqli_query($con,$query);
      
      while($row = mysqli_fetch_array($result) ){
    ?>
        <tr>
            <th scope="row"><?php echo $no++;?></th>
            <td><?php echo $row['invoiceno'];?></td>
          
            <td><?php echo $row['member_id'];?></td>
            <td><?php echo get_member($row['member_id']);?></td>
            <td><?php echo $row['quantity'];
            $totalquantity+=$row['quantity'];
            ?></td>
            <td><?php echo $row['discount'];?></td>
            <td><?php echo $row['payment_mode'];?></td>
            <td><?php 
         
            $amount=$row['quantity']*$row['sale_price'];
            echo number_format($amount,2);
            $total+=$amount;
            $totalcash+=$row['cash_amount'];
            $totalloan+=$row['loan_amount'];
            if($row['payment_mode']=='cash'){ $totalqcash+=$row['quantity'];}
            if($row['payment_mode']=='loan'){ $totalqloan+=$row['quantity'];}
            ?>
        </td>
          
        </tr>
     <?php } ?>
     <tr>
                                              
												<td>Cash:</td>
                                                <td><?php echo $totalqcash;?></td>
												
                                                <td></td>
												<td></td>
                                                <th>Total>></th>
                                                <th><?php echo $totalquantity;?></th>
												<td></td>
                                                
                                               
                                                <th><?php echo  number_format($total,2);?></th>
                                                </tr>
                                                <tr>
                                              
                                              <td>Charge:</td>
                                              <td><?php echo $totalqloan;?></td>
                                             
                                              <td></td>
                                              <td></td>
                                              <th>Loan >></th>
                                              <th></th>
                                              <td></td>
                                              
                                             
                                              <th><?php echo  number_format($totalloan,2);?></th>
                                              </tr>
                                              <tr>
                                              
                                              <td>POS:<?php echo $totalpos;?></td>
                                           
                                              <td></td>
                                              <td></td>
                                              <td></td>
                                              <th>Cash >></th>
                                              <th></th>
                                              <td></td>
                                              
                                             
                                              <th><?php echo  number_format($totalcash,2);?></th>
                                              </tr>
    </tbody>
</table>
<br>
<?php
}}
?>
