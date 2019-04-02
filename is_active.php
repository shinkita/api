<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']))
  {    
    $activated = 0;
    $query = "SELECT IF(`deleted`=1,0,`activated`) AS `activated` FROM `users` WHERE `id`='".$_POST['user_id']."'";
    if($stmt = $db->conn->query($query))
    {         

        if($stmt->num_rows>0)
        {
          // $fetch_row = $stmt->get_result();
          $row = $stmt->fetch_assoc();
          $activated = $row['activated'];
        }        
          $result = array(
            'status' => true,
            'activated' => $activated
          );
          echo json_encode($result);
    }
    else
    {
      echo 'prepare statement failed';
    }
    // $stmt->close();
  }
  else
  {
    throw new Exception("missing required field");
  }
}
catch(Exception $ex)
{
  $result = array(
    'status' => false,
    'msg' => $ex->getMessage()
  );
  echo json_encode($result);
  die();
}
?>