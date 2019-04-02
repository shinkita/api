<?php
//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $profile_type = $_POST['profile_type'];
  $postid       = $_POST['post_id'];
  $limit        = 10;
  //print_r($_POST);exit;
  if($postid>0 and !empty($postid))
  {
    $id_limit =  'AND post.id < '.$postid.'';
  }
  else {
    $id_limit = '';
  }
  $friends = $db->friends_lists($user_id,$profile_type);
  // print_r($friends);
  $data = array(
    'table'=>'post',
    'select'=>'post.id,post.user_id,post.post_type,post.method,user_detail.name AS username,profile_pic.profile_pic AS profile_pic,IF(post.post_type=4,(SELECT COUNT(id) FROM event_attand WHERE post_id=post.id),NULL) AS attanding,IF(post.post_type=4,(SELECT COUNT(id) FROM event_attand WHERE post_id=post.id AND user_id="'.$user_id.'"),NULL) AS is_i_attanding,(SELECT COUNT(*) FROM post_like WHERE post_id=post.id AND liked=1) AS no_of_likes,(SELECT COUNT(*) FROM comment WHERE post_id=post.id and deleted=0) AS no_of_comment,(SELECT COUNT(*) FROM share_post WHERE post_id=post.id ) AS no_of_share,(CASE WHEN(SELECT COUNT(*) FROM post_like WHERE post_id=post.id AND user_id="'.$user_id.'" AND liked=1 ) > 0 THEN true ELSE false END) AS isLikedByMe,(CASE WHEN post.status IS NULL THEN "" ELSE post.status END) AS status,(CASE WHEN post.images IS NULL THEN "" ELSE post.images END ) AS images,(CASE WHEN post.file IS NULL THEN "" ELSE post.file END ) AS file,(CASE WHEN post.video IS NULL THEN "" ELSE post.video END ) AS video,(CASE WHEN post.title IS NULL THEN "" ELSE post.title END ) AS title,(CASE WHEN post.event IS NULL THEN "" ELSE post.event END ) AS event ,(CASE WHEN post.lng IS NULL THEN "" ELSE post.lng END ) AS lng,(CASE WHEN post.lat IS NULL THEN "" ELSE post.lat END ) AS lat , post.date_time ',
    'join'=>'LEFT JOIN event ON event.id=post.event JOIN user_detail ON post.user_id=user_detail.user_id JOIN profile_pic ON post.user_id=profile_pic.user_id AND profile_pic.profile_type=post.profile_type',
    'where'=>'post.user_id IN ('.implode(',',$friends).') AND post.profile_type='.$profile_type.' AND post.activated=1 AND post.deleted=0 '.$id_limit.' ORDER BY post.id DESC LIMIT '.$limit
  );
  $result = array(
    'status'=> true,
    'data'=>$db->get_post_all($data)
  );
  echo json_encode($result);
}
?>
