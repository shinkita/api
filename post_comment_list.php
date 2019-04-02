<?php 
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('connect.php');
  $post_id = $_POST['post_id'];
  $user_profile = $_POST['user_profile'];
  $sql = "SELECT comment_id,post_id,user_post_comment.user_id,user_profile_pic.image,comment,is_deleted,`date` from user_post_comment LEFT JOIN user_profile_pic ON user_post_comment.user_id=user_profile_pic.user_id AND user_profile_pic.profile_pic_status LIKE '$user_profile' where post_id='$post_id' AND is_deleted='0'";
  $data = $con->query($sql);
  if($data->num_rows > 0)
  {
    while($row = $data->fetch_assoc())
    {
      $rows[] = $row;
    }
    $result = array(
      'data'=>$rows,
      'status'=>'true'
      );
  }
  else
  {
    $rows[] = array();
    $result = array(
      'data'=>$rows,
      'status'=>'true'
      );
  }
}
$con->close();
echo json_encode($result);
?>