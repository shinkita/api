<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['to_user_id']))
  {
    $date = $db->currentDate->format('Y-m-d H:i:s');
    $query = "INSERT INTO delete_user_chat (`chat_id`, `user_id`,`date_time`) SELECT chat.id,'".$_POST['user_id']."','".$date."' FROM `chat` LEFT JOIN delete_user_chat ON chat.id=delete_user_chat.chat_id AND delete_user_chat.user_id='".$_POST['user_id']."' WHERE delete_user_chat.chat_id IS NULL AND delete_user_chat.user_id IS NULL AND (chat.user_id='".$_POST['user_id']."' OR chat.to_user_id='".$_POST['user_id']."') AND (chat.user_id='".$_POST['to_user_id']."' OR chat.to_user_id='".$_POST['to_user_id']."')";	
    // echo $query;
   if($stmt = $db->conn->prepare($query))
    {      
      if($stmt->execute())
      {        
          $result = array( 
            'status' => true, 
            'msg' => "chat deleted successfully"
          );
          echo json_encode($result);
          die();
      }
      else
      {
        throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
      }
      $stmt->close();
    }
    else
    {
      throw new Exception('Prepare command failed');
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