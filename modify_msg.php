<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{   
  $db = new db_connect();  
  if(isset($_POST['chat_id']) && isset($_POST['msg']) && isset($_POST['user_id']) )
  {    
    //update images
    //event update not supported
    $post_msg = addslashes($_POST['msg']);
      if($stmt = $db->conn->prepare("UPDATE `chat` SET `message`=? WHERE `id`=?"))
      {
        $stmt->bind_param("si",$post_msg,$_POST['chat_id']);      
        if($stmt->execute())
        {        
        //print_r($stmt);
          if($stmt->affected_rows>0)
          {
            $result = array(
              'status' => true,
              'msg' => "msg updated",              
            );
            $chat = $db->conn->query("SELECT to_user_id FROM chat WHERE id='".$_POST['chat_id']."'");
            $chat_detail = $chat->fetch_assoc();
            $notify_type = 'chat_message_edit';
        $fields = array (
            'to'=>"/topics/via".$chat_detail['to_user_id'],
            'data' => array (
              'id'=>$_POST['chat_id'],
              "noti_type" => $notify_type,
              'user_id'=>$_POST['user_id'],
              "msg_id"=>$_POST['chat_id'],
              "msg"=>$_POST['msg'], 
            )
         );
        $stat = $db->sendPushNotification($fields);
            echo json_encode($result);            
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
      }
      else
      {

            throw new Exception("prepare statement failed"); 
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