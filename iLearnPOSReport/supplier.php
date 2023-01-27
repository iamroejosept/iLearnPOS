<?PHP
/*
function format_date($date){
   $date2=date_format($date,"M d, Y");
   return $date2;  
}*/

    function supplier($supplier_id) {
        global $host, $user, $password, $dbname, $con;
        $query = "SELECT supplier_name FROM db_supplier WHERE supplier_id='".$supplier_id."'";
        $result = mysqli_query($con,$query);
        $row = mysqli_fetch_array($result); 
        echo  $row['supplier_name'];
    }
    function get_user_role($user_id) {
      global $host, $user, $password, $dbname, $con;
      $query = "SELECT user_role FROM db_users WHERE userid='".$user_id."'";
      $result = mysqli_query($con,$query);
      $row = mysqli_fetch_array($result); 
      return  $row['user_role'];
  }
  function display_user_role($user_role) {
   global $host, $user, $password, $dbname, $con;
   $query = "SELECT role_name FROM db_role WHERE role_id='".$user_role."'";
   $result = mysqli_query($con,$query);
   $row = mysqli_fetch_array($result); 
   return  $row['role_name'];
}
     function sidemenu($pageurl,$cat_id) {
        global $host, $user, $password, $dbname, $con;
        $query = "SELECT * FROM db_pages WHERE page_url='".$pageurl."'";
        $result = mysqli_query($con,$query);
        $row = mysqli_fetch_array($result); 
        if($row['page_cat']==$cat_id){echo 'active';}
    
     }
     function displaymenu($cat_id,$pagerole) {
        global $host, $user, $password, $dbname, $con;
        $query = "SELECT * FROM db_pages,db_role_page WHERE db_role_page.role_id='".$pagerole."' AND db_pages.page_cat='".$cat_id."' AND db_role_page.page_id=db_pages.page_id";
        $result = mysqli_query($con,$query);
        $row = mysqli_fetch_array($result); 
        if($result->num_rows>0){
              return $row['page_cat'];
            } 
          
     }
     function displaypage($pageurl,$pagerole) {
        global $host, $user, $password, $dbname, $con;
        $query = "SELECT * FROM db_pages WHERE page_url='".$pageurl."'";
        $result = mysqli_query($con,$query);
        $row = mysqli_fetch_array($result); 
        $query2 = "SELECT * FROM db_role_page WHERE role_id='".$pagerole."' AND page_id='".$row['page_id']."'";
        $result2 = mysqli_query($con,$query2);
        
        if($result2->num_rows>0){
               include 'contents/'.$row['page_file']; 
            } else {
               include 'contents/charts.php'; 
            }
          
     }
       
     function pagecheck($page_id,$pagerole) {
        global $host, $user, $password, $dbname, $con;
        $query = "SELECT * FROM db_role_page WHERE role_id='".$pagerole."' AND page_id='".$page_id."'";
        $result = mysqli_query($con,$query);
        $row = mysqli_fetch_array($result); 
        if($result->num_rows>0){
            echo "checked";
            } 
          
     }
     function pageeditcheck($page_id,$pagerole) {
      global $host, $user, $password, $dbname, $con;
      $query = "SELECT * FROM db_role_page WHERE role_id='".$pagerole."' AND page_id='".$page_id."' and edit_role='1'";
      $result = mysqli_query($con,$query);
      $row = mysqli_fetch_array($result); 
      if($result->num_rows>0){
          echo "checked";
          } 
        
   }
   function pageeditread($pageid,$pagerole) {
      global $host, $user, $password, $dbname, $con;
    // $query = "SELECT * FROM db_pages WHERE page_url='".$pageurl."'";
       // $result = mysqli_query($con,$query);
      //  $row = mysqli_fetch_array($result); 
        $query2 = "SELECT * FROM db_role_page WHERE role_id='".$pagerole."' AND page_id='".$pageid."' and edit_role='1'";
        $result2 = mysqli_query($con,$query2);
      $row2 = mysqli_fetch_array($result2); 
      if($result2->num_rows<1){
          echo "readonly";
          } 
        
   }
     function savelogs($action,$userid) {
        global $host, $user, $password, $dbname, $con;
        $sql2 = "INSERT INTO db_logs(action_log,action_by)
        VALUES ('".$action."','".$userid."')";
        if ($con->query($sql2) === TRUE) {}            
        }

   function dashDate($date){
         return date("Y-m-d", strtotime($date) );
     }
     function get_member($member_id){
      global $host, $user, $password, $dbname, $con;
      $query = "SELECT fname,mname,lname FROM db_member WHERE bbcc_id='".$member_id."'";
      $result = mysqli_query($con,$query);
      $row = mysqli_fetch_array($result); 
      echo  $row['lname'].", ".$row['fname']." ".$row['mname'];
  }
  function get_member2($member_id){
   global $host, $user, $password, $dbname, $con;
   $query = "SELECT fname,mname,lname FROM db_member WHERE bbcc_id='".$member_id."'";
   $result = mysqli_query($con,$query);
   $row = mysqli_fetch_array($result); 
   $lname=str_split($row['lname'],2);
   $fname=str_split($row['fname'],2);
   $mname=str_split($row['mname'],1);
   echo  $lname[0]."****, ".$fname[0]."**** ".$mname[0]."****";
}
  function get_user($user_id){
   global $host, $user, $password, $dbname, $con;
   $query = "SELECT * FROM db_users WHERE userid='".$user_id."'";
   $result = mysqli_query($con,$query);
   $row = mysqli_fetch_array($result); 
   echo  $row['fname']." ".$row['lname'];
}
function get_cashier($user_id){
   global $host, $user, $password, $dbname, $con;
   $query = "SELECT * FROM db_users WHERE userid='".$user_id."'";
   $result = mysqli_query($con,$query);
   $row = mysqli_fetch_array($result); 
   return  $row['fname'];
}
function get_barcode($prod_id){
   global $host, $user, $password, $dbname, $con;
   $query = "SELECT barcode FROM db_products WHERE product_id='".$prod_id."'";
   $result = mysqli_query($con,$query);
   $row = mysqli_fetch_array($result); 
   echo  $row['barcode'];
}
function get_prodname($prod_id){
   global $host, $user, $password, $dbname, $con;
   $query = "SELECT product_name FROM db_products WHERE product_id='".$prod_id."'";
   $result = mysqli_query($con,$query);
   $row = mysqli_fetch_array($result); 
   echo  $row['product_name'];
}
function barcode($prod_id){
   global $host, $user, $password, $dbname, $con;
   $query = "SELECT barcode FROM db_products WHERE product_id='".$prod_id."'";
   $result = mysqli_query($con,$query);
   $row = mysqli_fetch_array($result); 
   return $row['barcode'];
}
function prodname($prod_id){
   global $host, $user, $password, $dbname, $con;
   $query = "SELECT product_name FROM db_products WHERE product_id='".$prod_id."'";
   $result = mysqli_query($con,$query);
   $row = mysqli_fetch_array($result); 
   return  $row['product_name'];
}     
?>