<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $data = array(
        'table'=>'notification',
        'select'=>'DISTINCT notification.id as id,notification.post_id AS post_id,IF(post.method="Shared",true,false) AS share, post.post_type AS post_type,notification.user_id AS user_id,user_detail.name as user_name,user_detail.gender as gender,profile_pic.profile_pic AS user_pic,notify_type AS noti_type,notification.profile_type AS profile_type_id, profile_type.profile AS profile_type,notification.event_id AS event_id,notification.event_title AS event_title,notification.date_time AS date_time,user_detail.user_id AS test_user_id,users.email AS test_email_id',
        'join'=>"JOIN users ON notification.to_user_id=users.id JOIN friends_list ON friends_list.user_id=notification.user_id OR friends_list.friends_id=notification.user_id JOIN profile_type ON notification.profile_type=profile_type.id  AND profile_type.id=friends_list.profile_type JOIN user_detail ON user_detail.user_id=notification.user_id JOIN profile_pic ON profile_pic.user_id=notification.user_id AND profile_pic.profile_type=notification.profile_type 
        LEFT JOIN post ON post.id=notification.post_id LEFT JOIN event_module_attand ON event_module_attand.event_id=notification.event_id AND event_module_attand.user_id='".$_POST['user_id']."'",
        'where'=>'to_user_id='.$user_id.' AND notification.user_id!='.$user_id.' AND users.deleted=0 AND users.activated=1 AND notification.seen=0 ORDER BY notification.id DESC LIMIT 40',
      );
  $data = $db->get_all($data);
  $friend_request = array(
        'table'=>'notification',
        'select'=>'DISTINCT notification.id as id,notification.post_id AS post_id, post.post_type AS post_type,notification.user_id AS user_id,user_detail.name as user_name,user_detail.gender as gender,profile_pic.profile_pic AS user_pic,notify_type AS noti_type,notification.profile_type AS profile_type_id, profile_type.profile AS profile_type,notification.date_time AS date_time',
        'join'=>"JOIN users ON notification.to_user_id=users.id JOIN friends_list ON friends_list.user_id=notification.user_id OR friends_list.friends_id=notification.user_id JOIN profile_type ON notification.profile_type=profile_type.id  AND profile_type.id=friends_list.profile_type JOIN user_detail ON user_detail.user_id=notification.user_id JOIN profile_pic ON profile_pic.user_id=notification.user_id AND profile_pic.profile_type=notification.profile_type LEFT JOIN post ON post.id=notification.post_id ",
        'where'=>'to_user_id='.$user_id.' AND notification.user_id!='.$user_id.' AND users.deleted=0 AND users.activated=1 AND notification.seen=0 AND notify_type="friend_request_receive" ORDER BY notification.id DESC',
      );
  $friend_request = $db->get_all($friend_request);  
  foreach ($data as $key => $value) {
    if(! in_array($value['id'],array_column($friend_request, 'id')))
    {
      $friend_request[] = $value;
    }
  }
  // echo count($db->get_all($data));
      // $result = array(
      //   'status'=> true,
      //   'data'=>array_merge($data,$friend_request)
      // );
      $result = array(
        'status'=> true,
        'data'=>$friend_request
      );
  echo json_encode($result);
}
?>
