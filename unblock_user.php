<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
// print_r($_POST);
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['block_user_id']))
  {    
      if($stmt = $db->conn->prepare("UPDATE `block_user` SET `block`=0 WHERE `user_id`=? AND `block_user_id`=?") )
      {
        $stmt->bind_param("ii",$_POST['user_id'],$_POST['block_user_id']);      
        if($stmt->execute())
        {        
        //print_r($stmt);
             $fields = array (
                  'to'=>"/topics/via".$_POST['block_user_id'],
                  'data' => array (
                    'id'=>$_POST['user_id'],
                    "noti_type"=>'block_changed',
                    "user_id"=>$_POST['user_id'],
                    "blocked"=>0
                )
              );
                $stat = $db->sendPushNotification($fields);
            $result = array(
              'status' => true,
              'msg' => "Unblocked successfully"
            );
            echo json_encode($result);
            die();
        }
        else
        {
          throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
        }
        
      }
      else
      {
      	throw new Exception('Prepare failed');
      }
      $stmt->close();
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