<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id    = $_POST['user_id'];  
  $friends = $db->friends_list_datail_group_chat($user_id);
      $result = array(
        'status'=> true,
        'data'=>$friends
      );
  echo json_encode($result);
}
?>