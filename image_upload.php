<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once('Connect_class.php');
    $db = new db_connect();
    $image_post = $_POST['image'];    
    $path = $db->upload_dir;  
    $image_name = time() . '' . rand() . '.jpeg';
    $imageStr = base64_decode($image_post) or die('base 64 not coverted');      
    $image = imagecreatefromstring($imageStr) or die('image creat from string is failed');
    if($image  !== false)
    {
      header('Content-Type: image/jpeg');
      imagejpeg($image, __DIR__.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$image_name) or die('image save is failed');
      imagedestroy($image) or die('image destroy is failed');
      echo json_encode(array('status'=>'true','image_name'=>$db->mutimedia_dir.$image_name));
    }
    else
    {
        echo json_encode(array('status'=>'false','msg'=>'image uplaod failed'));
    }    
}
?>
