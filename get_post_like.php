<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  //$user_id      = $_POST['user_id'];
  $postid       = $_POST['post_id'];
  $user_id      = $_POST['user_id'];
  $data = $db->get_post_like($postid,$user_id);
  $result = array(
    'status'=> true,
    'data'=>$data
  );
  echo json_encode($result);
}
?>
