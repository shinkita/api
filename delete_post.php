<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['post_id']))
  {
    $deleted = 1;
    if($stmt = $db->conn->prepare("UPDATE post SET deleted=? WHERE id=? AND user_id=?"))
    {
      $stmt->bind_param("iii",$deleted,$_POST['post_id'],$_POST['user_id']);      
      if($stmt->execute())
      {        
        if($stmt->affected_rows>0)
        {
          $result = array(
            'status' => true,
            'msg' => "post deleted successfully"
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
      echo 'dasdas';
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