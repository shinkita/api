<?php
require_once('Connect_class.php');
$db = new db_connect();
 $feed_id      = $_POST['feed_id'];
$data = array(
  'table'=>' account_tbl',
  'where'=>'id='.$feed_id 
);
$result= $db->get_all($data);
$mutimedia_image_dir = 'https://' . $_SERVER['SERVER_NAME'] . '/new_admin/viaspot_users/images/';
$mutimedia_video_dir = 'https://' . $_SERVER['SERVER_NAME'] . '/new_admin/viaspot_users/images/';
  $img = (!empty($result[0]['image']) && $result[0]['image'] != '')?$mutimedia_image_dir.$result[0]['image']:'';
$video = (!empty($result[0]['video']) && $result[0]['video'] != '')?$mutimedia_video_dir.$result[0]['video']:'';
$feed_result=array('account_name'=>$result[0]['account_name'],'category'=>$result[0]['category'],'payment_status'=>$result[0]['payment_status'],'account_status'=>$result[0]['account_status'],'img'=>$img,'video'=> $video,'description'=> $result[0]['description']);
 

 
$result = array(
  'status'=>true,
  'data'=>$feed_result,
  'msg'=>'success'
);
echo json_encode($result);
?>
