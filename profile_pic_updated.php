<?php

if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();  
  $user_id      = $_POST['user_id'];
  $profile_type      = $_POST['profile_type'];
  $current_user =  $_POST['current_user'];

  $data = array(
    'table'=>'users',
    'select'=>'user_detail.name AS name,user_detail.dob AS dob,user_detail.gender AS gender,user_profile_about.bio,user_profile_about.phone,user_profile_about.address,user_profile_about.website,user_profile_about.education,user_profile_about.work,user_profile_about.description,user_profile_about.previous_work,profile_pic.profile_pic AS user_pic',
    'join'=>"LEFT JOIN user_profile_about ON users.id=user_profile_about.user_id AND user_profile_about.profile_type='".$profile_type."' LEFT JOIN user_detail ON user_detail.id=users.id LEFT JOIN profile_pic ON profile_pic.user_id=users.id AND profile_pic.profile_type='".$profile_type."'",
    'where'=>'users.id='.$user_id.''
  );
  $result = array(
    'status'=> true,
    'user_data'=>$db->get_row($data)
  );
  $multimedia_dir = 'mutimedia_dir';
  if(!empty($result['user_pic']) && $result['user_pic'] != '')
  {
      $result['user_pic'] = $db->$multimedia_dir.$u_detail['user_pic'];
  }
  if(!empty($result['user_data']['user_pic']) && $result['user_data']['user_pic'] != '')
  {
      $result['user_data']['user_pic'] = $db->$multimedia_dir.$result['user_data']['user_pic'];
  }
  $friend_list = $db->friends_list_datails_one($user_id,$profile_type);
  $result['friend_list'] = $friend_list;
  $post[] = array();
  //print_r($_POST);exit;
  //$friends = $db->friends_lists($user_id,$profie_type);
  //print_r($friends);
  $data = array(
    'table'=>'post',
    'select'=>'post.id,post.user_id,post.post_type,user_detail.name AS username,profile_pic.profile_pic AS profile_pic,(SELECT COUNT(*) FROM post_like WHERE post_id=post.id AND liked=1) AS no_of_likes,(SELECT COUNT(*) FROM comment WHERE post_id=post.id and deleted=0) AS no_of_comment,(SELECT COUNT(*) FROM share_post WHERE post_id=post.id and user_id="'.$user_id.'") AS no_of_share,CASE WHEN(SELECT COUNT(*) FROM post_like WHERE post_id=post.id AND user_id="'.$current_user.'" AND liked=1 )>0 THEN true ELSE false END AS isLikedByMe,CASE WHEN post.status IS NULL THEN "" ELSE post.status END AS status,post.images,CASE WHEN post.video IS NULL THEN "" ELSE post.video END AS video,CASE WHEN post.event IS NULL THEN "" ELSE post.event END AS event , post.date_time ',
    'join'=>'LEFT JOIN event ON event.id=post.event JOIN user_detail ON post.user_id=user_detail.user_id JOIN profile_pic ON post.user_id=profile_pic.user_id AND profile_pic.profile_type=post.profile_type',
    'where'=>'post.user_id='.$user_id.' AND post.profile_type='.$profile_type.' AND post.activated=1 AND post.deleted=0 '.$id_limit.' ORDER BY post.id DESC '
  );
    $result['status']   = true;
    $result['posts']    = $db->get_post_all($data);
    $result['images']   = array();
    foreach ($result['posts'] as $key=>$images)
    {
        if(!empty($images['images']) && count($images['images'])>0)
        {
            foreach ($images['images'] as $img)
            {
                $result['images'][] = $img; 
            }
        }
    }
  echo json_encode($result);
}
?>
