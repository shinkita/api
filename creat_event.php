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
     if(isset($_POST['user_id']) && isset($_POST['title']) && isset($_POST['img']) && isset($_POST['video']) && isset($_POST['description']) && isset($_POST['event_start']) && isset($_POST['event_end']) && isset($_POST['address'])){
      if(isset($_POST['participent']))
      {
      	$participent = $_POST['participent'];
      }
      $img = '';
      if(isset($_POST['img']) && $_POST['img'] != '' )
      {
          $img = $db->upload_image( $_POST['img']);
      }
       $decode = json_decode($participent);
       $IpAddr = $db->getRealIpAddr();
       $IpDetail = file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $IpAddr);
       $IpDetail = json_decode($IpDetail);        
       if(!isset($IpDetail->geoplugin_timezone) || $IpDetail->geoplugin_timezone == '' || empty($IpDetail->geoplugin_timezone))
       {
       		$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$IpAddr));
       		$IpDetail->geoplugin_timezone = $query['timezone'];	
       }
       $event_start = new DateTime($_POST['event_start'],new DateTimeZone($IpDetail->geoplugin_timezone));
       $event_end = new DateTime($_POST['event_end'],new DateTimeZone($IpDetail->geoplugin_timezone));
       $event_start->setTimezone(new DateTimeZone('GMT'));
       $event_end->setTimezone(new DateTimeZone('GMT'));
       // print_r($decode);var_dump($_POST);die();
      // print_r($posted_images);die();
       $date = $db->currentDate->format('Y-m-d H:i:s');
      $query = "INSERT INTO event_module (user_id,title,img,video,address,participent,description,event_start,event_end,last_update,date_time)
      VALUES ('".$_POST['user_id']."','".addslashes($_POST['title'])."','". $img."','".$_POST['video']."','".addslashes($_POST['address'])."','".$participent."','".addslashes($_POST['description'])."','".$event_start->format('Y-m-d H:i:s')."','".$event_end->format('Y-m-d H:i:s')."','".$date."','".$date."')";
      if($db->conn->query($query))
      {
      $array = array(
          'status'=>true,
          'msg'=>'Event generated Successfully ',
        );
        $decode = json_decode($participent);
        $notify_type= 'invitation_received';
        $event_id = $db->conn->insert_id;
        $profile_type = 3;
        $date = $db->currentDate->format('Y-m-d H:i:s');
        $decode[] = (object) array('id'=>$_POST['user_id']);
        if(count($decode)>0)
        {
          foreach ($decode as $key => $value) {
			  
			  $querylogs = "logs/createevent_logs_".date('Ymd').".txt";
               $logdata=$event_id."|".$value->id  ;
		  error_log($logdata, 3,  $querylogs);
		  
            $_query = "INSERT INTO `notification`(`user_id`,`event_id`,`event_title`,`to_user_id`,`profile_type`,`notify_type`,`date_time`)
            VALUES('".$_POST['user_id']."','".$event_id."','".$_POST['title']."','".$value->id."','".$profile_type."','".$notify_type."','".$date."')";
            $snder = $db->get_user_data_id($_POST['user_id']);
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
