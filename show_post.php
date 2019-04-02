<?php
if($_SERVER['REQUEST_METHOD']=='GET')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $postid       = $_GET['get'];
  $data = array(
    'table'=>'post',
    'select'=>'post.id,post.user_id,user_detail.name AS username,profile_pic.profile_pic AS profile_pic',
    'join'=>"LEFT JOIN event ON event.id=post.event JOIN user_detail ON post.user_id=user_detail.user_id JOIN profile_pic ON post.user_id=profile_pic.user_id AND profile_pic.profile_type=post.profile_type",
    'where'=>'post.id='.$postid.''
  );
  $result = array(
    'status'=> true,
    'data'=>$db->get_post_row($data)
  );
  echo json_encode($result);
}
?>
