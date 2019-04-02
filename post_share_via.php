<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  //$dir = 'viaspot/';
  $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
  $share_link = $protocol.$_SERVER['SERVER_NAME']. dirname($_SERVER['PHP_SELF']).'/show_post.php?get=';
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $post_id      = $_POST['post_id'];
    $data = array(
      'table'=>'share_post',
      'field'=>'user_id,post_id',
      'values'=>array(array($user_id,$post_id))
    );
    $resul = $db->insert($data);
    //print_r($resul);
    if($resul['status']== true)
    {
      $result = array(
        'status'=>true,
        //'share_link'=>$share_link.$post_id
      );
    }
  echo json_encode($result);
}
?>
