<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $profile = $_POST['profile_type'];
  //$status       = $_POST['status'];
  $title        = addslashes($_POST['title']);
  $type         = $_POST['type'];
  $description  = addslashes($_POST['description']);
  //$location     = $_POST['location'];
  $event_start  = $_POST['start_time'];
  $event_end  	= $_POST['end_time'];
  //$event_end    = $_POST['end_time'];
  $post_type    = 4;
  $activated    = 1;
  //print_r($_POST);exit;
  $pro = explode(",", $profile);
    foreach ($pro as $key => $value) {
  $data_sub = array(
    'table'=>'event',
    'field'=>'user_id,title,description,profile_type,event_start,event_end',
    'values'=>array(array($user_id,$title,$description,$value,$event_start,$event_end))
  );
  $subresult = $db->insert($data_sub);
  //print_r($subresult);
  if($subresult['status'] === true)
  {
    $data = array(
      'table'=>'post',
      'field'=>'user_id,profile_type,post_type,status,event,activated',
      'values'=>array(array($user_id,$value,$post_type,NULL,$subresult['inserted_id'],$activated))
    );
    $result = $db->insert($data);
      if($result['status']==true)
      {
        $db->post_notifiation($_POST['user_id'],$value,$result['inserted_id'],$post_type);
                // echo json_encode($result);
                // die();
      }
  }
  else
  {
    $result = $subresult;
  }
}
  echo json_encode($result);
}
?>
