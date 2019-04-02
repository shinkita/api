<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $friend_id   = $_POST['to_user_id'];
  $profile_type   = $_POST['profile_type'];
  $noti_id  = $_POST['noti_id'];
    //$result1['status'] = true;
    $data_up = array(
      'table'=>'friends_list',
      'data'=>'approved=127',
      'where'=>'(user_id="'.$friend_id.'" OR friends_id="'.$friend_id.'") AND (user_id="'.$user_id.'" OR friends_id="'.$user_id.'") AND profile_type="'.$profile_type.'"'
    );  
    $result1 = $db->update($data_up);
    $noti_update = array(
        'table'    =>'notification',
        'data'      =>'seen=1',
        'where'     =>'(id="'.$noti_id.'")'
    );
    $update_notification  = $db->update($noti_update);
      $data = array(
        'table'=>'users',
        'select'=>' users.id AS user_id,user_detail.name AS username,profile_type.id AS profile_id,profile_type.profile AS profile_type,profile_pic.profile_pic AS userimage',
        'join'=>' JOIN user_detail ON users.id=user_detail.user_id JOIN profile_pic ON profile_pic.user_id=users.id JOIN profile_type ON profile_type.id=profile_pic.profile_type ',
        'where'=>"users.id='".$user_id."' AND profile_pic.profile_type='$profile_type'",
      );
      $result = $db->get_row($data);
      //print_r($result);
      $multimedia_dir = 'mutimedia_dir';
      if(isset($result['userimage']) && !empty($result['userimage']))
      {
         
          $result['userimage'] = $db->$multimedia_dir.$result['userimage'];
      }
      $notify_type = 'friend_request_reject';
      $noti_data = array(
        'table'=>'notification',
        'field'=>'user_id,to_user_id,profile_type,notify_type',
        'values'=>array(array($user_id,$friend_id,$profile_type,$notify_type))
      );
      $res = $db->insert($noti_data);
      $fields = array (
            'to'=>"/topics/via".$friend_id,
            'data' => array (
              'id'=>$res['inserted_id'],
              "noti_type"=>$notify_type,
              "user_id"=>$result['user_id'],
              "user_name"=>$result['username'],
              "profile_type_id"=>$result['profile_id'],
              "profile_type"=>$result['profile_type'],
              'user_pic'=>$result['userimage'],
            )
         );
      $stat = $db->sendPushNotification($fields);
      $status = array(
        'status'=>true,
        'msg'=>'Friend request rejected'
      );
      //$fields = json_encode($fields);
  echo json_encode($status);
}
?>