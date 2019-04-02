<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['chat_id']))
  {    
  		$readed = 1;
		$to_user      = $_POST['to_user_id']; 
		$user_id  = $_POST['user_id'];
      if($stmt = $db->conn->prepare("UPDATE `chat` SET `readed`=? WHERE `id`=?"))
      {
        $stmt->bind_param("ii",$readed,$_POST['chat_id']);      
        if($stmt->execute())
        {        
        //print_r($stmt);
          
            $result = array(
              'status' => true, 
              'msg' => "updated successfully",
			  'user_id'=>$user_id
            );
			
			$notify_type = 'chat_message_read';
			$fields = array (
				'to'=>"/topics/via".$to_user,
				'data' => array (
				  'id'=>$_POST['chat_id'],
				  'user_id'=>$user_id,
				  "noti_type"=>$notify_type,
				  'date'=>$result['date_time']  
				)
			 );
			// echo"<pre>";print_r($fields);echo"</pre>";
			$stat = $db->sendPushNotification($fields);
		//	echo"<pre>";print_r($stat);echo"</pre>";
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