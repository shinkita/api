<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['chat_id']) && isset($_POST['user_id']) && isset($_POST['to_user']))
  {
    $date = $db->currentDate->format('Y-m-d H:i:s');
    $share = 'Shared';
    $query = "INSERT INTO `chat`(`user_id`,`to_user_id`,`message`,`img`,`video`,`thumbnail`,`document`,`title`,`lng`,`lat`,`chat_type`,`readed`,`deleted`,`date_time`) SELECT ?,?,`message`,`img`,`video`,`thumbnail`,`document`,`title`,`lng`,`lat`,`chat_type`,`readed`,`deleted`,? FROM `chat` WHERE id=? ";
    if(!($stmt = $db->conn->prepare($query)))
    {
      throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
    }
    if(!$stmt->bind_param("iisi",$_POST['user_id'],$_POST['to_user'],$date,$_POST['chat_id']))
    {
      throw new Exception("binding Error : (" . $stmt->errno . ") " . $stmt->error);
    }      
    if(!$stmt->execute())
    {
      throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
    }
    $array = array(
      'table'=>"chat",
      'where'=>"id='".$_POST['chat_id']."'"
    );
    $user_id = $_POST['user_id'];
    $to_user = $_POST['to_user'];
    $chat_row = $db->get_row($array);
    $user_data = array(
                    'table'=>'user_detail',
                    'select'=>' user_detail.id AS user_id,user_detail.name AS user_name,profile_pic.profile_pic AS user_pic',
                    'join'=>" JOIN profile_pic ON profile_pic.user_id=user_detail.user_id AND profile_pic.profile_type=(CASE WHEN (SELECT approved FROM friends_list WHERE (user_id ='".$user_id."' OR friends_id = '".$user_id."') AND (user_id = '".$to_user."' OR friends_id = '".$to_user."') AND approved=1 AND profile_type=1)=1 THEN 1 WHEN (SELECT approved FROM friends_list WHERE (user_id = '".$user_id."' OR friends_id = '".$user_id."') AND (user_id = '".$to_user."' OR friends_id = '".$to_user."') AND approved=1 AND profile_type=2)=1 THEN 2 ELSE 3 END)  JOIN profile_type ON profile_type.id=profile_pic.profile_type ",
                    'where'=>"user_detail.user_id='".$user_id."' ",
                );
         //print_r($user_data);
        $user = $db->get_row($user_data);
        
        $multimedia_dir = 'mutimedia_dir';
        if(!empty($user['user_pic']))
        { 
            //echo  'this working';
            $user['user_pic'] = $db->mutimedia_dir.$user['user_pic'];
        }
        // print_r($user);
    $notify_type = 'chat_message';
        $fields = array (
            'to'=>"/topics/via".$_POST['to_user'],
            'data' => array (
              'id'=>$stmt->insert_id,
              "noti_type"=>$notify_type,
              "user_id"=>$user['user_id'],
              "user_name"=>$user['user_name'],
              'user_pic'=>$user['user_pic'],
              'message'=>$chat_row['message'],
                'img'=>$chat_row['img'],
                'video'=>$chat_row['video'],
                'thumbnail'=>$chat_row['thumbnail'],
                'lat'=>$chat_row['lat'],
                'lng'=>$chat_row['lng'],
                'document'=>$chat_row['document'],
                'chat_type'=>$chat_row['chat_type'],
                'title'=>$chat_row['title'],
              'date'=>$chat_row['date_time']  
            )
         );
        $stat = $db->sendPushNotification($fields);
    $result = array(
      'status' => true,
      'msg' => "Msg forwarded successfully",
      'inserted_id' => $stmt->insert_id
    );
    echo json_encode($result);
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