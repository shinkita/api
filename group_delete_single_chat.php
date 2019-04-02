<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['chat_id']))
  {
    $query = "INSERT INTO delete_group_chat (`chat_id`, `user_id`, `date_time`) VALUES('".$_POST['chat_id']."','".$_POST['user_id']."','".$date."')";
    if($stmt = $db->conn->prepare($query))
    {      
      if($stmt->execute())
      {        
        if($stmt->affected_rows>0)
        {
            $data  = array(
            'table' =>'chat',
            'data'  =>" deleted=1 ",
            'where' =>" id='".$_POST['chat_id']."' "
            );
            $update = $db->update($data);
          $result = array(
            'status' => true,
            'msg' => "chat deleted successfully"
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