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
     if(isset($_POST['event_id'])){
      $query = "SELECT user_id FROM event_module_attand WHERE event_id='".$_POST['event_id']."'";
      if($response = $db->conn->query($query))
      {

          $event_query = $db->conn->query("SELECT * FROM event_module WHERE id='".$_POST['event_id']."'");
          $event_detail = $event_query->fetch_assoc();
          $snder = $db->get_user_data_id($event_detail['user_id']);
          $profile_type = 3;
          $notify_type = 'remind_invitation';
          $date = date('Y-m-d H:i:s');
        if( $response->num_rows > 0)
        {
          while($row = $response->fetch_assoc())
          {
            // print_r($event_detail);

            $_query = "INSERT INTO `notification`(`user_id`,`event_id`,`event_title`,`to_user_id`,`profile_type`,`notify_type`,`date_time`)
            VALUES('".$event_detail['user_id']."','".$event_detail['id']."','".$event_detail['title']."','".$row['user_id']."','".$profile_type."','".$notify_type."','".$date."')";
             if($db->conn->query($_query))
            $fields = array (
            'to'=>"/topics/via".$row['user_id'],
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
        $array =  array(
          'status'=>true,
          'msg'=>'Notification sended'
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
