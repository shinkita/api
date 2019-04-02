<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $post_id = $_POST['event_id'];
  $attand = $db->conn->query("SELECT * FROM event_module_attand WHERE event_id='".$post_id."' AND user_id='".$user_id."'");
  // print_r($attand);
    if($attand->num_rows <= 0)
    {
      $data_sub = array(
        'table'=>'event_module_attand',
        'field'=>'user_id,event_id',
        'values'=>array(array($user_id,$post_id))
      );
      $subresult = $db->insert($data_sub);
      $event_query = $db->conn->query("SELECT * FROM event_module WHERE id='".$_POST['event_id']."'");
      $event_detail = $event_query->fetch_assoc();
      $profile_type = 3;
      $date =  $db->currentDate->format('Y-m-d H:i:s');
      $notify_type = 'invitation_accepted';
      $_query = "INSERT INTO `notification`(`user_id`,`event_id`,`event_title`,`to_user_id`,`profile_type`,`notify_type`,`date_time`)
            VALUES('".$_POST['user_id']."','".$post_id."','".$event_detail['title']."','".$event_detail['user_id']."','".$profile_type."','".$notify_type."','".$date."')";
            $snder = $db->get_user_data_id($_POST['user_id']);
      $__query = "UPDATE notification SET seen=1 WHERE event_id='".$post_id."' AND to_user_id='".$user_id."' AND notify_type='invitation_received'";
      $notyfi_seen = $db->conn->query($__query);      
             if($db->conn->query($_query))
            {
              $fields = array (
              'to'=>"/topics/via".$event_detail['user_id'],
              'data' => array (
                'id'=>$db->conn->insert_id,
                "noti_type"=>$notify_type,
                "noti_id"=>$db->conn->insert_id,
                "event_id"=>$post_id,
                'event_title'=>$event_detail['title'],
                'sender_id'=>$_POST['user_id'],
                "sender_name"=>$snder['name'],
                "sender_pic"=>$snder['pro_pic'],
                "date_time"=>$date
                )
              );
              // print_r($fields);
              $stat = $db->sendPushNotification($fields);
            }
    }
    else
    {
      $subresult = array(
        'status'=>true,
        'msg'=>'Already attanding'
      );
    }
  echo json_encode($subresult);
}
?>
