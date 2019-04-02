<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $email      = $_POST['email'];
  $username   = addcslashes($_POST['username']);
  $password   = $_POST['password'];
  $data = array(
    'table'=>'users',
    'where'=>" `email`='".$email."' ",
  );
  $count = $db->count($data);
  if($count>0)
  {
      $result = array(
        'status'=>false,
        'msg'=>'User already exist'
      );
  }
  else
  {
    $data = array(
      'table'=>'users',
      'field'=>'username,email,password,activated',
      'values'=>array(array($username,$email,sha1($password),1))
    );
    $result = $db->insert($data);
    $shadow = array(
        'table'=>'profile_pic',
        'field'=>'user_id,profile_pic,profile_type',
        'values'=>array(array($result['inserted_id'],"",1),array($result['inserted_id'],"",2),array($result['inserted_id'],"",3))
    );
    $shadow1 = $db->insert(array('table'=>'user_detail','field'=>'user_id','values'=>array(array($result['inserted_id']))));
    //print_r($shadow);
    $shadow_result = $db->insert($shadow); 
    //print_r($shadow_result);
  }
  echo json_encode($result);
}
?>
