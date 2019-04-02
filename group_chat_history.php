<?php
//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $group_id      = $_POST['group_id'];  
  $user_id   = $_POST['user_id'];
  //print_r($_POST);exit;
  $data = array(
    'table'=>'group_chat',
    'select'=>"group_chat.id AS id,group_chat.replytomsg_id AS replytomsg_id,group_chat.user_id AS user_id,message,group_chat.chat_type,group_chat.img AS image,group_chat.video AS video,group_chat.document AS document,group_chat.title AS title,group_chat.lng AS lng , group_chat.lat AS lat,group_chat.thumbnail AS thumbnail,group_chat.date_time,user_detail.name AS name,profile_pic.profile_pic AS user_img",
    'join'=>"LEFT JOIN profile_pic ON profile_pic.user_id=group_chat.user_id AND profile_type=3 LEFT JOIN user_detail ON user_detail.user_id=group_chat.user_id LEFT JOIN delete_group_chat ON group_chat.id=delete_group_chat.chat_id AND delete_group_chat.user_id='".$user_id."'",  
    'where'=>"group_id='".$_POST['group_id']."' AND delete_group_chat.chat_id IS NULL AND delete_group_chat.user_id IS NULL ORDER BY group_chat.id ASC "
  );
  $result = array(
    'status'=> true,
    'data'=>$db->get_all($data)
  );
  $multimedia_dir = 'mutimedia_dir';
  $id = array();
  foreach($result['data'] as $imgk=>$img)
  {
    $id[] = $img['id'];
    if(!empty($img['user_img']) && $img['user_img'] != '' && $img['user_img'] != null)
    {
     $result['data'][$imgk]['user_img'] =   $db->$multimedia_dir.$img['user_img']; 
    }
  } 
  if(!empty($id))
  {
    $update = array('table'=>'group_users','data'=>'readed=0','where'=>" user_id='".$_POST['user_id']."' AND group_id='".$_POST['group_id']."'");
    $update = $db->update($update);
  }
  echo json_encode($result);
}
?>
