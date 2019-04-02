<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['profile_type']) && isset($_POST['pic']))
  {
    $data = array(
      'table'=>'profile_pic',
      'select'=>'profile_pic',
      'where'=>"user_id='".$_POST['user_id']."' AND profile_type='".$_POST['profile_type']."'"
    );
    $res = $db->get_row($data);
    // print_r($res);
    if(empty($res['profile_pic']) || $res['profile_pic']==='')
    {
      $image_name = $db->upload_image($_POST['pic']);
      if(!($stmt = $db->conn->prepare("UPDATE `profile_pic` SET `profile_pic`=? WHERE `user_id`=? AND `profile_type`=?")))
      {
        //unlink($db->upload_dir.DIRECTORY_SEPARATOR.$image_name);
        throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
      }
      if(!$stmt->bind_param("sii",$image_name,$_POST['user_id'],$_POST['profile_type']))
      {
        //unlink($db->upload_dir.DIRECTORY_SEPARATOR.$image_name);
        throw new Exception("binding Error : (" . $stmt->errno . ") " . $stmt->error);
      }     
      if(!$stmt->execute())
      {         
        //unlink($db->upload_dir.DIRECTORY_SEPARATOR.$image_name);
        throw new Exception("execute Error : (" . $stmt->errno . ") " . $stmt->error);
      } 
      $result = array(
        'status' => true,
        'profile_pic'=>$db->mutimedia_dir.$image_name,
        
        'affected_rows' => $stmt->affected_rows,
        'msg' => 'Succefully updated' 
      );
      $db->profile_pic_updated($_POST['user_id'],$_POST['profile_type'],'profile_pic_updated');
      echo json_encode($result);
      $stmt->close();
    }
    else
    {      
     $image_name = $res['profile_pic'];
      unlink($db->upload_dir.DIRECTORY_SEPARATOR.$image_name);
      $imageStr = base64_decode($_POST['pic']);      
      $image = imagecreatefromstring($imageStr);
      header('Content-Type: image/jpeg');
      imagejpeg($image, __DIR__.DIRECTORY_SEPARATOR.$db->upload_dir.DIRECTORY_SEPARATOR.$image_name);
      imagedestroy($image);
      $result = array(
        'status' => true,
        'profile_pic'=>$db->mutimedia_dir.$image_name,
        'msg' => 'Succefully updated' 
      );
      $db->profile_pic_updated($_POST['user_id'],$_POST['profile_type'],'profile_pic_updated');
      echo json_encode($result);
      die();
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