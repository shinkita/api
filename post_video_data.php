<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $profile = $_POST['profile_type'];
  $status       = addcslashes($_POST['status']);
  $vidio        = $_POST['video_name'];
  $post_type    = 3;
  $activated    = 1;
  //print_r($_POST);exit;
  $pro = explode(",", $profile);
  foreach ($pro as $key => $value) {
    $data = array(
    'table'=>'post',
    'field'=>'user_id,profile_type,post_type,status,video,activated',
    'values'=>array(array($user_id,$value,$post_type,$status,$vidio,$activated))
    );
    $result = $db->insert($data);
    if($result['status']==true)
    {
      $db->post_notifiation($_POST['user_id'],$value,$result['inserted_id'],$post_type);
    }
  }
  echo json_encode($result);
}
?>
