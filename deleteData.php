<?php
  require_once 'Database.php';

    $Server = "localhost";    
    $User = "root";
    $DBPassword = "";
    $Database = "products";
    $Table = 'db_products';

  $Message = 'Failed to delete information!';
  $Error="1";

  $TxtBarcode = $_POST['TxtResBarcode'];
    
    $POSDB = new Database($Server,$User,$DBPassword);

    if ($POSDB->Connect()==true)
    {
      $Result = $POSDB->SelectDatabase($Database);
                          
      if($Result == true)
      {   
        deleteInfo();
      }
      else
      {
        $Message = 'Failed to delete information!';
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

    function deleteInfo(){
      global $POSDB,$Message,$Error,$TxtBarcode;

      $sql;

      $sql = "DELETE FROM db_products WHERE barcode='$TxtBarcode'";

      $Result = $POSDB->Execute($sql);

      $Message = 'Successfully deleted!';   
      $Error = "0";
    }
?>
