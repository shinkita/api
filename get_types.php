<?php
require_once('Connect_class.php');
$db = new db_connect();
$data = array(
  'table'=>'post_type',
);
$result_sub1 = $db->get_all($data);
$data = array(
    'table'=>'profile_type',
  );
$result_sub = $db->get_all($data);
$result = array(
  'status'=>true,
  'post_type'=>$result_sub1,
  'profile_type'=>$result_sub,
  'msg'=>'success'
);
echo json_encode($result);
?>
