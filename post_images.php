<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  // print_r($_POST['images']);
  $user_id      = $_POST['user_id'];
  $profile = $_POST['profile_type'];
  $status       = addslashes($_POST['status']);;
  $images       = json_decode($_POST['images']);
  // var_dump($images);
  $post_type    = 2;
  $activated    = 1;
  $path = $db->upload_dir;  
  $posted_images = array();
  foreach ($images as $key => $value) {
    $image_name = time().''.$user_id.''.rand().'.jpeg';  
    $imageStr = base64_decode($value->image_data);      
    $image = imagecreatefromstring($imageStr);
    if($image  !== false)
    {
      header('Content-Type: image/jpeg');
      imagejpeg($image, __DIR__.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$image_name);
      imagedestroy($image);
    }
    $posted_images[] = $image_name;
  }
  //print_r($_POST);exit;
  // print_r($posted_images);
  $pro = explode(",", $profile);
            foreach ($pro as $key => $value) {
  $data = array(
    'table'=>'post',
    'field'=>'user_id,profile_type,post_type,status,images,activated',
    'values'=>array(array($user_id,$value,$post_type,$status,json_encode($posted_images),$activated))
  );
  $result = $db->insert($data);
  if($result['status']==true)
  {
    $db->post_notifiation($_POST['user_id'],$value,$result['inserted_id'],$post_type);
  }
}
  echo json_encode($result);
}
?>
