<?php
require_once('Connect_class.php');
if($_SERVER['REQUEST_METHOD']=='POST')
{  
  $db           = new db_connect();
  $user_id      = $_POST['user_id'];
  $post_id      = $_POST['post_id'];
  $like         = 1;
  $date = $db->currentDate->format('Y-m-d H:i:s');
  $get = array(
    'table'=>'post_like',
    'where'=>"`user_id`='".$user_id."' AND `post_id`='".$post_id."'"
  );
  $liked = $db->get_row($get);
  $post_detail = $db->get_row(array('select'=>'post.user_id,post.post_type AS post_type,post.profile_type,profile_type.id AS profile_type_id,profile_type.profile AS profile_text, (SELECT name FROM user_detail WHERE user_id="'.$user_id.'") AS user_name','join'=>'JOIN profile_type ON profile_type.id=post.profile_type','table'=>'post','where'=>"post.id='".$post_id."'"));
  $like_text = ' Liked ';
  //print_r($post_detail);
  if(!empty($liked) && count($liked)>0)
  {
    if($liked['liked']==1)
    {
      $like = 0;        
    }
    else
    {
      $like =1;
    }
    $data = array(
      'table'=>'post_like',
      'data'=>' `liked`='.$like.' ',
      'where'=>"`user_id`='".$user_id."' AND `post_id`='".$post_id."'"
    );
    $result = $db->update($data);
    if($result['status'] == true)
    {
      $result = array('status'=>true);
    }
  }
  else
  {
    $data = array(
      'table'=>'post_like',
      'field'=>'user_id,post_id,liked',
      'values'=>array(array($user_id,$post_id,$like))
    );
    $result = $db->insert($data);
    if($result['status']==true)
    {
      $result = array('status'=>true);
    }
    if($user_id != $post_detail['user_id'])
   { $notify_type = 'like_post';
    $profile_pic = $db->get_row(array('table'=>'profile_pic','where'=>'profile_type="'.$post_detail['profile_type_id'].'" AND user_id="'.$user_id.'"'));
    $query = "INSERT INTO `notification`(`user_id`,`post_id`,`to_user_id`,`profile_type`,`notify_type`,date_time) VALUES(?,?,?,?,?,?)"; 
    $stmt = $db->conn->prepare($query);     
    $stmt->bind_param("iiiiss",$user_id,$post_id,$post_detail['user_id'],$post_detail['profile_type'],$notify_type,$date);
    $stmt->execute();
    $fields = array (
      'to'=>"/topics/via".$post_detail['user_id'],
      'data' => array (
        'id'=>$stmt->insert_id,
        'profile_type_id'=>$post_detail['profile_type_id'],
        'profile_type'=>$post_detail['profile_text'],
        "noti_type"=>$notify_type,
        "user_id"=>$user_id,
        "user_name"=>$post_detail['user_name'],
        'user_pic'=>$db->mutimedia_dir.$profile_pic['profile_pic'],
        'post_id'=>$post_id,
        'post_type'=>$post_detail['post_type'],
      )
    );
   //print_r($fields);
    $stat = $db->sendPushNotification($fields);
  }
  }
  
  echo json_encode($result);
}
?>
