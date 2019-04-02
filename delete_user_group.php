<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['group_id']))
  {
    
    if($stmt = $db->conn->prepare("DELETE FROM group_users WHERE group_id=? AND user_id=?"))
    {
      $stmt->bind_param("ii",$_POST['group_id'],$_POST['user_id']);                  
      if($stmt->execute())
      {   
        $result = array(
          'status'=>true,
          'msg'=>'user deleted from group'
        );
        echo json_encode($result);          
      }
      else
      {
        throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
      }
      $stmt->close();      
    }
    else{
      throw new Exception("Prepare query failed group users");
    }
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
