<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $to_user = $_POST['to_user_id'];
  $data = array(
    'table'=>'chat',
    'select'=>"chat.id AS id,chat.user_id AS user_id,chat.status AS delivery_status,chat.replyto_msg_id AS reply_msg_id,message,chat_type,chat.img AS image,chat.video AS video,chat.document AS document,chat.title AS title,chat.lng AS lng , chat.lat AS lat,chat.thumbnail AS thumbnail,chat.date_time,user_detail.name AS name,profile_pic.profile_pic AS user_img",
    'join'=>"LEFT JOIN profile_pic ON profile_pic.user_id=chat.user_id AND profile_type=(CASE WHEN (SELECT approved FROM friends_list WHERE (user_id = '".$user_id."' OR friends_id = '".$user_id."') AND (user_id = '".$to_user."' OR friends_id = '".$to_user."') AND approved=1 AND profile_type=1 LIMIT 1)=1 THEN 1 WHEN (SELECT approved FROM friends_list WHERE (user_id = '".$user_id."' OR friends_id = '".$user_id."') AND (user_id = '".$to_user."' OR friends_id = '".$to_user."') AND approved=1 AND profile_type=2 LIMIT 1)=1 THEN 2 ELSE 3 END) LEFT JOIN user_detail ON user_detail.user_id=chat.user_id LEFT JOIN delete_user_chat ON chat.id=delete_user_chat.chat_id AND delete_user_chat.user_id='".$user_id."'",  
    'where'=>"(chat.user_id='".$user_id."' OR chat.to_user_id='".$user_id."') AND (chat.user_id='".$to_user."' OR chat.to_user_id='".$to_user."') AND delete_user_chat.chat_id IS NULL AND delete_user_chat.user_id IS NULL ORDER BY chat.id ASC "
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
    $update = array('table'=>'chat','data'=>'readed=1 , status=3','where'=>" id IN (".implode(',', $id).")");
    $update = $db->update($update);
  }
  echo json_encode($result);
}
?>
