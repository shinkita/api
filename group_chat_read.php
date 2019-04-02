<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['group_id']))
  {    
      if($stmt = $db->conn->prepare("UPDATE `group_users` SET `readed`=(`readed` -1) WHERE `user_id`=? AND `group_id`=? AND `readed`>0"))
      {
        $stmt->bind_param("ii",$_POST['user_id'],$_POST['group_id']);      
        if($stmt->execute())
        {        
        //print_r($stmt);
          
            $result = array(
              'status' => true,
              'msg' => "updated successfully"
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