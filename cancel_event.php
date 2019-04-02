<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  /*
  ====================================================================================================================
    Calling basic class and initiating the class
  ====================================================================================================================
  */
  require_once('Connect_class.php');
  $db = new db_connect();
  /*
  =====================================================================================================================
    Posting data and checking if the same data is posted
    ['user_id']['title']['profile_type']['img']['video']['description']['event_start']['event_end']
  =====================================================================================================================
  */
  try {
     if(isset($_POST['event_id']) && isset($_POST['cancel_reason'])){
      // print_r($posted_images);die();
      $_query = "UPDATE event_module SET cancelled=1, reason='".$_POST['cancel_reason']."' WHERE id='".$_POST['event_id']."'";
      if($event_query = $db->conn->query($_query))
      {
      	$que = "SELECT user_id from event_module_attand WHERE event_id='".$_POST['event_id']."'";
      	$attanding = $db->conn->query($que);
      	$event = $db->conn->query("SELECT * FROM event_module WHERE id='".$_POST['event_id']."'");
      	$event_detail = $event->fetch_assoc();
      	// print_r($event_detail);
      	if($attanding->num_rows > 0)
      	{
      		$date = $db->currentDate->format('Y-m-d H:i:s');
      		$notify_type = 'canceled_event';
      		$profile_type =3;
      		while ($value = $attanding->fetch_assoc()) {
      			// print_r($value);
            $__query = "INSERT INTO `notification`(`user_id`,`event_id`,`event_title`,`to_user_id`,`profile_type`,`notify_type`,`date_time`)
            VALUES('".$event_detail['user_id']."','".$_POST['event_id']."','".$event_detail['title']."','".$value['user_id']."','".$profile_type."','".$notify_type."','".$date."')";
            $snder = $db->get_user_data_id($event_detail['user_id']);
             if($db->conn->query($__query))
            {
              $fields = array (
              'to'=>"/topics/via".$value['user_id'],
              'data' => array (
                'id'=>$db->conn->insert_id,
                "noti_type"=>$notify_type,
                "noti_id"=>$db->conn->insert_id,
                "event_id"=>$event_detail['id'],
                'event_title'=>$event_detail['title'],
                'sender_id'=>$event_detail['user_id'],
                "sender_name"=>$snder['name'],
                "sender_pic"=>$snder['pro_pic'],
                "date_time"=>$date
                )
              );
                      // print_r($fields);
              $stat = $db->sendPushNotification($fields);
            }
          }
      	}
        $data = array();
        $array = array(
          'status'=>true,
          'data'=>"Event cancelled successfully"
        );
        echo json_encode( $array );
      }
      else
      {
        throw new Exception("Query failed : ".filter_var($db->conn->error,FILTER_SANITIZE_STRING));
      }
    }
    else
    {
      throw new Exception("Missing required field");
    }
  } catch (Exception $e) {
    $array = array(
      'status'=>false,
      'msg'=>$e->getMessage()
    );
    echo json_encode( $array );
  }
 }
?>
