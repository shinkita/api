<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $postid       = $_POST['post_id'];
  $data = array(
    'table'=>'post',
    'select'=>'post.id,post.user_id,post.post_type,post.method,user_detail.name AS username,profile_pic.profile_pic AS profile_pic,(SELECT COUNT(*) FROM post_like WHERE post_id=post.id AND liked=1) AS no_of_likes,(SELECT COUNT(*) FROM comment WHERE post_id=post.id and deleted=0) AS no_of_comment,(SELECT COUNT(*) FROM share_post WHERE post_id=post.id and user_id=post.user_id) AS no_of_share,CASE WHEN(SELECT COUNT(*) FROM post_like WHERE post_id=post.id AND user_id="'.$user_id.'" AND liked=1 )>0 THEN true ELSE false END AS isLikedByMe,CASE WHEN post.status IS NULL THEN "" ELSE post.status END AS status,CASE WHEN post.images IS NULL THEN "" ELSE post.images END AS images,CASE WHEN post.file IS NULL THEN "" ELSE post.file END AS file,CASE WHEN post.title IS NULL THEN "" ELSE post.title END AS title,CASE WHEN post.video IS NULL THEN "" ELSE post.video END AS video,CASE WHEN post.event IS NULL THEN "" ELSE post.event END AS event ,(CASE WHEN post.lng IS NULL THEN "" ELSE post.lng END ) AS lng,(CASE WHEN post.lat IS NULL THEN "" ELSE post.lat END ) AS lat , post.date_time ',
    'join'=>"LEFT JOIN event ON event.id=post.event JOIN user_detail ON post.user_id=user_detail.user_id JOIN profile_pic ON post.user_id=profile_pic.user_id AND profile_pic.profile_type=post.profile_type",
    'where'=>"post.id='".$postid."' AND post.deleted=0"
  );
  $data_one = $db->get_post_row($data);
  if($data_one['post_type'] == '4')
  {
    $query = $db->conn->query("SELECT * FROM event_attand WHERE post_id='".$data_one['id']."'");
    $sub = $db->conn->query("SELECT * FROM event_attand WHERE post_id='".$data_one['id']."' AND user_id='".$user_id."'");
    $data_one['is_i_attanding'] = "".$sub->num_rows."";
    $data_one['attanding'] = "".$query->num_rows.""; 
  }
  if(count($data_one)>0)
  {
  $result = array(
    'status'=> true,
    'data'=>$data_one
  );
}
else
{
  $result = array(
    'status'=> false,
    'msg'=> 'Data not found'
  );
  
}
echo json_encode($result);
}
?>
