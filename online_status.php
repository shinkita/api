<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $date = $db->currentDate->format('Y-m-d H:i:s');
  $user_id      = $_POST['user_id'];
    $sql = "online_status='$date'";
    $data  = array(
		'table'	=>'users',
		'data'	=>$sql,
		'where'	=>"id='".$user_id."'"    
            );
    $update = $db->update($data);
    if($update['affected_rows'] > 0)
    {
        $result = array(
            'status' => true,
            'msg'=>"status set"
            );
    } 
    else
    {
        $result = array(
            'status' => false,
            'msg'=>"something went wrong"
            );
    }
  echo json_encode($result);
}
?>
