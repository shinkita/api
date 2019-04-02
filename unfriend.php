<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['friend_id']) && isset($_POST['profile_type']))
  {
    $apporoved = 0;
    if($stmt = $db->conn->prepare("DELETE FROM friends_list WHERE profile_type=? AND (user_id=? OR friends_id=? ) AND (user_id=? OR friends_id=? )"))
    {
      $stmt->bind_param("iiiii",$_POST['profile_type'],$_POST['user_id'],$_POST['user_id'],$_POST['friend_id'],$_POST['friend_id']);      
      if($stmt->execute())
      {        
        if($stmt->affected_rows>0)
        {
          $result = array(
            'status' => true,
            'msg' => "unfriend successfully"
          );
          echo json_encode($result);
          die();
        }
        else
        {
          throw new Exception("Process incomplete");
        }
      }
      else
      {
        throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
      }
      $stmt->close();
    }
    else
    {
      throw new Exception('prepare statement failed');
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