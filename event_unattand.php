<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $post_id = $_POST['event_id'];
  $attand = $db->conn->query("DELETE FROM event_module_attand WHERE event_id='".$post_id."' AND user_id='".$user_id."'");
   $event_query = $db->conn->query("SELECT * FROM event_module WHERE id='".$_POST['event_id']."'");
        $event_detail = $event_query->fetch_assoc();
  $snder = $db->get_user_data_id($_POST['user_id']);
  // print_r($snder);
  $date = $db->currentDate->format('Y-m-d H:i:s');
  $_query = "INSERT INTO `notification`(`user_id`,`event_id`,`event_title`,`to_user_id`,`profile_type`,`notify_type`,`date_time`)
            VALUES('".$snder['id']."','".$_POST['event_id']."','".$event_detail['title']."','".$event_detail['user_id']."','3','revoked_invitation','".$date."')";
            // echo $_query;
            $db->conn->query($_query);	
   $fields = array (
              'to'=>"/topics/via".$event_detail['user_id'],
              'data' => array (
                'id'=>$post_id,
                "noti_type"=>"revoked_invitation",
                "event_id"=>$_POST['event_id'],
                'event_title'=>$event_detail['title'],
                'sender_id'=>$snder['id'],
                "sender_name"=>$snder['name'],
                "sender_pic"=>$snder['pro_pic'],
                "date_time"=>$date
                )
              );
                      // print_r($fields);
              $stat = $db->sendPushNotification($fields);
  // print_r($attand);
      $subresult = array(
        'status'=>true,
        'msg'=>'Successfully removed'
      );
  echo json_encode($subresult);
}
?>
