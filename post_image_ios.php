<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['profile_type']) && isset($_POST['status']) && isset($_POST['images']))
  {
    $date = $db->currentDate->format('Y-m-d H:i:s');
    $post_img = array();
    $post_type = 2;
    $activated = 1;
    if($images = json_decode($_POST['images']))
    {
      foreach ($images as $key => $value) {
        $posted_images[] = $db->upload_image($value->image_data);
      }
     if($stmt = $db->conn->prepare("INSERT INTO post (user_id,profile_type,post_type,status,images,activated,date_time) VALUES(?,?,?,?,?,?,?)"))
     {
      $stmt->bind_param("iiissis",$_POST['user_id'],$_POST['profile_type'],$post_type,$_POST['status'],json_encode($posted_images),$activated,$date);      
      if($stmt->execute())
      {        
        $db->post_notifiation($_POST['user_id'],$_POST['profile_type'],$stmt->insert_id,$post_type);
          $result = array(
            'status' => true,
            'msg' => "post inserted successfully"
          );
          echo json_encode($result);
          die();
      }
      else
      {
        throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
      }
      $stmt->close();
    }
  }
  else{
    throw new Exception("please check data format");
  }
}
else
{
  throw new Exception("missing required field");
}
}
catch(Exception $ex)
{
  $result = array(
    'status' => false,
    'msg' => $ex->getMessage()
  );
  echo json_encode($result);
  die();
}
?>
