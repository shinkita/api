<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $profile_type = $_POST['profile_type'];
  //$status       = $_POST['status'];
  $title        = addslashes($_POST['title']);
  $type         = $_POST['type'];
  $description  = addslashes($_POST['description']);
  //$location     = $_POST['location'];
  $post_id	=	$_POST['post_id'];
  $event_start  = $_POST['start_time'];
  //$event_end    = $_POST['end_time'];
  $post_type    = 4;
  $activated    = 1;
  //print_r($_POST);exit;
  $date = $db->currentDate->format('Y-m-d H:i:s');
  $data = array(
  	'table'=>'post',
  	'select'=>'event',
  	'where'=>"id='".$post_id."'"
  );
  $event_id = $db->get_row($data);
  if(isset($event_id['event']) &&  $event_id['event'] != '')
  {
  	$event_id = $event_id['event'];
  	$data = array(
      'table'=>'event',
      'data'=>"title='".$title."',description='".$description."',profile_type='".$profile_type."',event_start='".$event_start."',update_date='".$date."'",
      'where'=>"id='".$event_id."'"
    );
    $result = $db->update($data);
      
  }
  else
  {
  	$result = array(
  		'status'=>false,
  		'msg'=>'Something went wrong'
  	);
  }
  
    
  echo json_encode($result);
}
?>
