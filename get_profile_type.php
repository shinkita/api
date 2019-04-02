<?php
require_once('Connect_class.php');
$db = new db_connect();
$data = array(
  'table'=>'profile_type',
);
$result = $db->get_all($data);
  $result = array(
    'status'=>true,
    'data'=>$result,
    'msg'=>'success'
  );
echo json_encode($result);
?>
