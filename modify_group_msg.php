<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{   
  $db = new db_connect();  
  if(isset($_POST['chat_id']) && isset($_POST['msg']) && isset($_POST['user_id']) && isset($_POST['group_id']))
  {    
    //update images
    //event update not supported
    $post_msg = addslashes($_POST['msg']);
      if($stmt = $db->conn->prepare("UPDATE `group_chat` SET `message`=? WHERE `id`=?"))
      {
        $stmt->bind_param("si",$post_msg,$_POST['chat_id']);      
        if($stmt->execute())
        {        
        //print_r($stmt);          
            $result = array(
              'status' => true,
              'msg' => "msg updated",              
            );
            $chat = $db->conn->query("SELECT DISTINCT user_id FROM group_users WHERE group_id='".$_POST['group_id']."'");
            while($row = $chat->fetch_assoc())
            {
              if($row['user_id']!=$_POST['user_id'])
              {
                $chat_detail[] = $row;
              }
            }
            foreach ($chat_detail as $key => $value) {
                $notify_type = 'group_chat_message_edit';
                  $fields = array (
                  'to'=>"/topics/via".$value['user_id'],
                  'data' => array (
                    'id'=>$_POST['chat_id'],
                    "noti_type" => $notify_type,
                    'group_id'=>$_POST['group_id'], 
                    "msg_id"=>$_POST['chat_id'],
                    "msg"=>$_POST['msg'], 
                  )
                );
                $stat = $db->sendPushNotification($fields);    
              }
            
            echo json_encode($result);             
          
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