<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['profile_type']) && isset($_POST['title']) && isset($_POST['status']) && isset($_POST['file_name']))
  {
    $post_type = 6;
    $activated = 1;
    $post_status = addslashes($_POST['status']);
    $post_titile = addslashes($_POST['title']);
    $date = $db->currentDate->format('Y-m-d H:i:s');
    $query = "INSERT INTO `post`(`user_id`,`profile_type`,`post_type`,`status`,`title`,`file`,`activated`,`date_time`) VALUES (?,?,?,?,?,?,?,?)";
    if(!($stmt = $db->conn->prepare($query)))
    {
      throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
    }
    $profile = $_POST['profile_type']; 
    $pro = explode(",", $profile);
    foreach ($pro as $key => $value) {
     if(!$stmt->bind_param("iiisssis",$_POST['user_id'],$value,$post_type,$post_status,$post_titile,$_POST['file_name'],$activated,$date))
    {
      throw new Exception("binding Error : (" . $stmt->errno . ") " . $stmt->error);
    }      
    if(!$stmt->execute())
    {
      throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
    }
      $db->post_notifiation($_POST['user_id'],$value,$stmt->insert_id,$post_type);
    }
    $result = array(
      'status' => true,
      'msg' => "post inserted successfully",
      'inserted_id' => $stmt->insert_id
    );
    echo json_encode($result);
    $stmt->close();
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