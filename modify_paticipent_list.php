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
     if(isset($_POST['event_id']) && isset($_POST['new_member']) && isset($_POST['modified_member'])){
      if(isset($_POST['new_member']))
      {
      	$participent = $_POST['new_member'];
      }
       $decode = json_decode($participent);
       // print_r($decode);var_dump($_POST);die();
      // print_r($posted_images);die();
      $query = "UPDATE event_module SET participent='".$_POST['modified_member']."' WHERE id='".$_POST['event_id']."'";
      if($db->conn->query($query))
      {
        $event_query = $db->conn->query("SELECT * FROM event_module WHERE id='".$_POST['event_id']."'");
        $event_detail = $event_query->fetch_assoc();
      $array = array(
          'status'=>true,
          'msg'=>'Pariticipent list updated',
        );
        $decode = json_decode($participent);
        $notify_type= 'invitation_received';
        $event_id = $db->conn->insert_id;
        $profile_type = 3;
        $date = $db->currentDate->format('Y-m-d H:i:s');
       // $decode[] = (object) array('id'=>$_POST['user_id']);
       
       // $participent=json_decode($participent);
     // $decode = (object) array('id'=>$participent);
       //print_r($decode);
        if(count($decode)>0)
        {
            
          foreach ($decode as $key => $value) {
           // echo 'tested by sarvesh';
             // echo $value->id;
             // echo 'tested by sarvesh1';
               //echo $value;
            $_query = "INSERT INTO `notification`(`user_id`,`event_id`,`event_title`,`to_user_id`,`profile_type`,`notify_type`,`date_time`)
            VALUES('".$event_detail['user_id']."','".$event_detail['id']."','".$event_detail['title']."','".$value->id."','".$profile_type."','".$notify_type."','".$date."')";
            $snder = $db->get_user_data_id($event_detail['user_id']);
             if($db->conn->query($_query))
            {
              $fields = array (
              'to'=>"/topics/via".$value->id,
              'data' => array (
                'id'=>$db->conn->insert_id,
                "noti_type"=>$notify_type,
                "noti_id"=>$db->conn->insert_id,
                "event_id"=>$event_id,
                'event_title'=>$_POST['title'],
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
        }


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
