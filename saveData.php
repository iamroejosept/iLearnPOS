<?php
  require_once 'Database.php';

    $Server = "localhost";    
    $User = "root";
    $DBPassword = "";
    $Database = "ilearnpos";
    $Table = 'db_products';

  $Message = 'Failed to save information!';
  $Error="1";

  $TxtBarcode = $_POST['TxtResBarcode'];
  $TxtWarehouseQty = $_POST['TxtWarehouseQty'];
  $TxtStoreQty = $_POST['TxtStoreQty'];
    
    $POSDB = new Database($Server,$User,$DBPassword);

    if ($POSDB->Connect()==true)
    {
      $Result = $POSDB->SelectDatabase($Database);
                          
      if($Result == true)
      {   
        SaveInfo();
      }
      else
      {
        $Message = 'Failed to save information!';
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
    $XMLData .= ' Error = ' . '"'.$Error.'"';
    $XMLData .= ' />';
    
    //Generate XML output
    header('Content-Type: text/xml');
    //Generate XML header
    echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
    echo '<Document>';    	
    echo $XMLData;
    echo '</Document>';

    function SaveInfo(){
      global $POSDB,$Message,$Error,$TxtBarcode,$TxtWarehouseQty,$TxtStoreQty;

      $sql;

      $sql = "UPDATE db_products SET warehouse_stock='$TxtWarehouseQty', store_stock='$TxtStoreQty' WHERE barcode='$TxtBarcode'";

      $Result = $POSDB->Execute($sql);

      $Message = 'Successfully saved!';   
      $Error = "0";
    }
?>
