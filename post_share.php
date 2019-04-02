<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['post_id']) && isset($_POST['profile_type']) && isset($_POST['post_type']))
  {
    $share = 'Shared';
    $date = $db->currentDate->format('Y-m-d H:i:s');
    $query = "INSERT INTO `post`(`user_id`,`profile_type`,`method`,`post_type`,`status`,`images`,`video`,`file`,`event`,`lng`,`lat`,`activated`,`date_time`) SELECT ?,?,?,`post_type`,`status`,`images`,`video`,`file`,`event`,`lng`,`lat`,`activated`,? FROM `post` WHERE id=? ";
    if(!($stmt = $db->conn->prepare($query)))
    {
      throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
    }
    if(!$stmt->bind_param("iissi",$_POST['user_id'],$_POST['profile_type'],$share,$date,$_POST['post_id']))
    {
      throw new Exception("binding Error : (" . $stmt->errno . ") " . $stmt->error);
    }      
    if(!$stmt->execute())
    {
      throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
    }
    $share = "INSERT INTO `share_post`(`user_id`,`post_id`,`date_time`) VALUES(?,?,?)";
    if(!($share = $db->conn->prepare($share)))
    {
      throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
    }
    if(!$share->bind_param("iis",$_POST['user_id'],$_POST['post_id'],$date))
    {
      throw new Exception("binding Error : (" . $share->errno . ") " . $share->error);
    }      
    if(!$share->execute())
    {
      throw new Exception("execute Error: (" . $share->errno . ") " . $share->error);
    }
    $db->post_notifiation($_POST['user_id'],$_POST['profile_type'],$stmt->insert_id,$_POST['post_type'],true);
    $result = array(
      'status' => true,
      'msg' => "post shared successfully",
      'inserted_id' => $stmt->insert_id
    );
    echo json_encode($result);
    $share->close();$stmt->close();
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