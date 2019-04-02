  <?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db           = new db_connect();
  $user_id      = $_POST['user_id'];
  $post         = $_POST['post_id'];
  $comment      = addslashes($_POST['comment']);
  $root         = $_POST['comment_id'];
  $mention_list      = $_POST['mention_list'];
  $date = $db->currentDate->format('Y-m-d H:i:s');
  $data = array(
    'table'=>'comment',
    'field'=>'user_id,post_id,comment,mention_list,root_id',
    'values'=>array(array($user_id,$post,$comment,$mention_list,$root))
  );
  $result = $db->insert($data);
  $post_detail = $db->get_row(array('select'=>'post.user_id,post.post_type AS post_type,post.profile_type,profile_type.id AS profile_type_id,profile_type.profile AS profile_text, (SELECT name FROM user_detail WHERE user_id="'.$user_id.'") AS user_name','join'=>'JOIN profile_type ON profile_type.id=post.profile_type','table'=>'post','where'=>"post.id='".$post."'"));
  $notify_type = 'comment_post';
    $profile_pic = $db->get_row(array('table'=>'profile_pic','where'=>'profile_type="'.$post_detail['profile_type_id'].'" AND user_id="'.$user_id.'"'));
    if($user_id != $post_detail['user_id'])
    {
    $query = "INSERT INTO `notification`(`user_id`,`post_id`,`to_user_id`,`profile_type`,`notify_type`,`date_time`) VALUES(?,?,?,?,?,?)"; 
    if(!($stmt = $db->conn->prepare($query)))
    {
        echo 'query prepare failed';die();
    }
    if(!$stmt->bind_param("iiiiss",$user_id,$post,$post_detail['user_id'],$post_detail['profile_type'],$notify_type,$date))
    {
      echo 'binding failed';die();
    }
    if(!$stmt->execute())
    {
      echo 'Query execution failed';
      die();
    }
    
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
        'post_id'=>$post,
        'post_type'=>$post_detail['post_type'],
      )
    );
  //print_r($fields);
    $stat = $db->sendPushNotification($fields);
    $stmt->close();
    }
    $notify_type = 'mention_comment';
    foreach (json_decode($mention_list) as $key => $value) {
    $query = "INSERT INTO `notification`(`user_id`,`post_id`,`to_user_id`,`profile_type`,`notify_type`,`date_time`) VALUES(?,?,?,?,?,?)"; 
    $stmt = $db->conn->prepare($query);     
    $stmt->bind_param("iiiiss",$user_id,$post,$post_detail['user_id'],$post_detail['profile_type'],$notify_type,$date);
    $stmt->execute();
    
      $fields = array (
      'to'=>"/topics/via".$value->user_id,
      'data' => array (
        'id'=>$stmt->insert_id,
        'profile_type_id'=>$post_detail['profile_type_id'],
        'profile_type'=>$post_detail['profile_text'],
        "noti_type"=>$notify_type,
        "user_id"=>$user_id,
        "user_name"=>$post_detail['user_name'],
        'user_pic'=>$db->mutimedia_dir.$profile_pic['profile_pic'],
        'post_id'=>$post,
        'post_type'=>$post_detail['post_type'],
      )
    );
      // echo json_encode($fields);
      $db->sendPushNotification($fields);
      $stmt->close();
    }
  echo json_encode($result);
}
?>
