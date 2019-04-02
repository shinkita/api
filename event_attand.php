<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $post_id = $_POST['post_id'];
  $attand = $db->conn->query("SELECT * FROM event_attand WHERE post_id='".$post_id."' AND user_id='".$user_id."'");
  // print_r($attand);
    if($attand->num_rows <= 0)
    {
      $data_sub = array(
        'table'=>'event_attand',
        'field'=>'user_id,post_id',
        'values'=>array(array($user_id,$post_id))
      );
      $subresult = $db->insert($data_sub);
    }
    else
    {
      $subresult = array(
        'status'=>true,
        'msg'=>'Already attanding'
      );
    }
  echo json_encode($subresult);
}
?>
