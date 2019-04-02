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
     if(isset($_POST['event_id']) && isset($_POST['user_id'])){
        $update_date = date('Y-m-d H:i:s');
        $que = " last_update='".$update_date."' ";
        if(isset($_POST['event_start']) || isset($_POST['event_end']))
        {
           $IpAddr = $db->getRealIpAddr();
           $IpDetail = file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $IpAddr);
           $IpDetail = json_decode($IpDetail);        
           if($IpDetail->geoplugin_timezone == '' || empty($IpDetail->geoplugin_timezone))
           {
              $query = @unserialize(file_get_contents('http://ip-api.com/php/'.$IpAddr));
              $IpDetail->geoplugin_timezone = $query['timezone'];
           }
          
        }
        if(isset($_POST['participent']))
        {
        	$participent = $_POST['participent'];
          $que.= ", participent='".$participent."' ";
        }
        if(isset($_POST['img']) && $_POST['img'] != '' )
        {
            $img = $db->upload_image( $_POST['img']);
            $que.= ", img='".$img."' ";
        }
       /* else
        {
          $que.= ", img='".$_POST['img']."' "; 
        }*/
        if(isset($_POST['title']) && $_POST['title'] != '' )
        {
            $que.= ", title='".$_POST['title']."' ";
        }
        if(isset($_POST['video']) && $_POST['video'] != '' )
        {
            $que.= ", video='".$_POST['video']."' ";
        }
        /*  else
        {
          $que.= ", video='".$_POST['video']."' "; 
        }*/
        if(isset($_POST['description']) && $_POST['description'] != '' )
        {
            $que.= ", description='".$_POST['description']."' ";
        }
        if(isset($_POST['address']) && $_POST['address'] != '' )
        {
            $que.= ", address='".$_POST['address']."' ";
        }
        if(isset($_POST['event_start']) && $_POST['event_start'] != '' )
        {
           $event_start = new DateTime($_POST['event_start'],new DateTimeZone($IpDetail->geoplugin_timezone));
          
           $event_start->setTimezone(new DateTimeZone('GMT'));
           
            $que.= ", event_start='".$event_start->format('Y-m-d H:i:s')."' ";
        }
        if(isset($_POST['event_end']) && $_POST['event_end'] != '' )
        {

           $event_end = new DateTime($_POST['event_end'],new DateTimeZone($IpDetail->geoplugin_timezone));
           $event_end->setTimezone(new DateTimeZone('GMT'));
            $que.= ", event_end='".$event_end->format('Y-m-d H:i:s')."' ";
        }
       $decode = json_decode($participent);
       // print_r($decode);var_dump($_POST);die();
      // print_r($posted_images);die();
      $query = "UPDATE event_module SET ".$que." WHERE id='".$_POST['event_id']."'";
      $querylogs = "logs/editevent_logs_".date('Ymd').".txt";
		  error_log($query, 3,  $querylogs);
      if($db->conn->query($query))
      {
      $array = array(
          'status'=>true,
          'msg'=>'Event updated Successfully ',
        );
        $decode = json_decode($participent);
        $notify_type= 'event_edited';
        $event_id = $_POST['event_id'];
        $profile_type = 3;
        $date = $db->currentDate->format('Y-m-d H:i:s');
        $decode[] = (object) array('id'=>$_POST['user_id']);
        if(count($decode)>0)
        {
          foreach ($decode as $key => $value) {
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
