<?php 
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $email  = $_POST['email'];
  $country_code = $_POST['country_code'];
  $mobile = $_POST['mobile'];
  $data = array(
    'table'=>'users',
    'where'=>"`email`='".$email."'",
  );
  $user = $db->get_row($data);
  if(empty($user) || count($user)==0)
  {
  	$result = array(
    'status'=> false,
    'msg'=>'User does not exist!'
  );
  }
  else if($user['activated'] == 1)
  {
    $multimedia_dir = 'mutimedia_dir';
    $data_user = array(
        'table'=>'user_detail',
        'select'=>'user_detail.user_id AS id,user_detail.name AS name,(SELECT profile_pic FROM profile_pic WHERE profile_type=1 AND user_id='.$user['id'].') AS friend_pic,(SELECT profile_pic FROM profile_pic WHERE profile_type=2 AND user_id='.$user['id'].') AS faimily_pic,(SELECT profile_pic FROM profile_pic WHERE profile_type=3 AND user_id='.$user['id'].') AS pro_pic',
        'where'=>'id='.$user['id'].''
        );
//     print_r($data_user);exit;
    $u_detail = $db->get_row($data_user);
  //  print_r($u_detail);
     if($u_detail['friend_pic'] != '' && !empty($u_detail['friend_pic']))
     {
         $u_detail['friend_pic'] = $db->$multimedia_dir.$u_detail['friend_pic'];
     }
     if($u_detail['faimily_pic'] != '' && !empty($u_detail['faimily_pic']))
     {
         $u_detail['faimily_pic'] = $db->$multimedia_dir.$u_detail['faimily_pic'];
     }
     if($u_detail['pro_pic'] != '' && !empty($u_detail['pro_pic']))
     {
         $u_detail['pro_pic'] = $db->$multimedia_dir.$u_detail['pro_pic'];
     }
  	$result = array(
    'status'=> true,
  	'id'=>$user['id'],
    'name'=>$u_detail['name'],
    'friend_pic'=>$u_detail['friend_pic'],
    'faimily_pic'=>$u_detail['faimily_pic'],
    'pro_pic'=>$u_detail['pro_pic'],
    'msg'=>'Account is activated'
  );
  }
  else
  {
  	$result = array(
    'status'=> false,
    'msg'=>'Account is not activated yet!'
  );
  }
  
  echo json_encode($result);
}
?>