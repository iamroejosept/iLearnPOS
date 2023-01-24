<?php 
	date_default_timezone_set('Asia/Manila');

	$Server = "localhost";    
  	$User = "root";
  	$DBPassword = "";
  	$Database = "sample";
  	$Table = 'db_products';

  	$Message = 'asd';
  	$TxtBarcode = 'asd';
  	$TxtProductName = 'asd';
  	$TxtWarehouseQty = 'asd';
  	$TxtStoreQty = 'asd';

  	$conn = mysqli_connect($Server, $User, $DBPassword, $Database);

    /*$barcode = $_POST['TxtBarcode'];*/
  	$barcode = '4800361384391';

  	search($barcode, $Table);


  	/*$XMLData = '';	
	$XMLData .= ' <output ';
	$XMLData .= ' Message = ' . '"'.$Message.'"';
	$XMLData .= ' Barcode = ' . '"'.$TxtBarcode.'"';
	$XMLData .= ' ProductName = ' . '"'.$TxtProductName.'"';
	$XMLData .= ' WarehouseQty = ' . '"'.$TxtWarehouseQty.'"';
  	$XMLData .= ' StoreQty = ' . '"'.$TxtStoreQty.'"';
	$XMLData .= ' />';

	//Generate XML output
	header('Content-Type: text/xml');
	//Generate XML header
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"<!--  -->';
	echo '<Document>';    	
	echo $XMLData;
	echo '</Document>';*/

  		

  		function search($barcode, $Table){
  			$sql;
  			global $conn, $Message, $TxtBarcode, $TxtProductName, $TxtWarehouseQty, $TxtStoreQty;
  			$sql = "SELECT * FROM $Table WHERE barcode = '$barcode'";
	  		$result = mysqli_query($conn, $sql);

	  		if($result){
	  			$row = mysqli_fetch_row($result);

		  			echo "Barcode: $row[1] <br>";
		  			echo "Product Name : $row[2] <br>";
		  			echo "Warehouse Quantity: $row[11] <br>";
		  			echo "Store Quantity: $row[12] <br>";

		  			  	$TxtBarcode = $row[1];
  						$TxtProductName = $row[2];
  						$TxtWarehouseQty = $row[11];
  						$TxtStoreQty = $row[12];

		  		$Message = "Search Success";

	  		}else{
	  			$Message = "No data Found";
	  		}
  		}

 ?>