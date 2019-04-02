<?php 
if($_SERVER['REQUEST_METHOD']=='POST')
{
	 
  require_once('Connect_class.php');
  $db = new db_connect();
  $email  = $_POST['email'];
 
  $login_type = $_POST['login_type'];
 
  $data = array(
    'table'=>'users',
    'where'=>"`email`='".$email."'",
  );
  $user = $db->get_row($data);
 
  if(empty($user) || count($user)==0)
  {
  	$result = array(
    'status'=> false,
    'msg'=>'User does not exist'
  );
  }
 else
  {
	  $u_detail = $db->get_user_data_id($user['id']);
                    $date = $db->currentDate->format('Y-m-d H:i:s');
                    $query['table'] = 'users';
                    $query['data'] = " login_time='" . $date . "', login_count=login_count+1 ";
                    $query['where'] = " id='" . $u_detail['id'] . "'";
                    $db->update($query);
                    $result = array(
                        'status' => true,
                        'data' => $u_detail,
                        'msg' => 'login success'
                    );
  /* 	$result = array(
    'status'=> true,
    'msg'=>'User exist!'
  ); */
 
  }
  
  echo json_encode($result);
}
?>