<?php

require_once 'Database.php';

    $Server = "localhost";    
  	$User = "root";
  	$DBPassword = "";
  	$Database = "ilearnpos";
  	$Table = 'db_products';
      $Error = "0";

  $Message = '';
  $TxtBarcode = $_POST['temp'];
  	$TxtProductName = '';
  	$TxtWarehouseQty = '';
  	$TxtStoreQty = '';

    $POSDB = new Database($Server,$User,$DBPassword);

    if ($POSDB->Connect()==true)
    {
      $Result = $POSDB->SelectDatabase($Database);
                          
      if($Result == true)
      {   
        FetchInfo($TxtBarcode);
        
      }
      else
      {
        $Message = 'Failed to fetch information!';
        $Error = "1";
      }
    }  
    else
    {
      $Message = 'The database is offline!';
      $Error = "1";    
    } 

  

    $XMLData = '';	
    $XMLData .= ' <output ';
	  $XMLData .= ' Message = ' . '"'.$Message.'"';
    $XMLData .= ' ProductName = ' . '"'.$TxtProductName.'"';
    $XMLData .= ' WarehouseQty = ' . '"'.$TxtWarehouseQty.'"';
    $XMLData .= ' StoreQty = ' . '"'.$TxtStoreQty.'"';
    $XMLData .= ' Error = ' . '"'.$Error.'"';
	$XMLData .= ' />';
	
	//Generate XML output
	header('Content-Type: text/xml');
	//Generate XML header
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	echo '<Document>';    	
	echo $XMLData;
	echo '</Document>';

    function FetchInfo($TxtBarcode){
        $sql;
    
        //Access Global Variables
        global $Error, $POSDB, $Message,$TxtProductName,$TxtWarehouseQty,$TxtStoreQty;
    
          $sql = "SELECT * FROM db_products WHERE barcode='$TxtBarcode'";
    
          $Result = $POSDB->Execute($sql);
          
          $POSQuery = $POSDB->GetRows($sql);                
        
          if($POSQuery)
          {
            $Row = $POSQuery->fetch_array();
            if($Row)
              {        
                $TxtProductName = stripslashes($Row['product_name']);;
                $TxtWarehouseQty = stripslashes($Row['warehouse_stock']);;
                $TxtStoreQty = stripslashes($Row['store_stock']);;
                $Message = "Search completed!";
                $Error = "0"; 
              }else{
                $Message = "No information found. Please try again.";
                $Error = "1";
              }            
          }
      }

  

?>