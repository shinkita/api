<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']))
  {
    $date = date('Y-m-d H:i:s');
    $backup = "INSERT INTO b_users(user_id,email,password,mobile,country_code,activated,deleted,online_status,temp_process,login_count,login_time,log_out,date_time) SELECT id,email,password,mobile,country_code,activated,deleted,online_status,temp_process,login_count,login_time,log_out,date_time FROM users WHERE id = '".$_POST['user_id']."'";
    
    ############delete from  user detail table###############
	$backup_user_detail = "INSERT INTO b_user_detail(user_id,name,dob,gender,interest,date_time) SELECT user_id,name,dob,gender,interest,date_time  FROM user_detail WHERE user_id = '".$_POST['user_id']."'";
	if($db->conn->query($backup_user_detail))
	{
	    //$friendstmt = $db->conn->prepare("DELETE FROM user_detail WHERE `user_id`=?");
  //  $friendstmt->bind_param("i",$_POST['user_id']); 
    // $friendstmt->execute();
	 
	 ############delete from  Friend List table###############
	 $backup_friend = "INSERT INTO b_friends_list(user_id,profile_type,approved,friends_id,date_time) SELECT user_id,profile_type,approved,friends_id,date_time FROM friends_list WHERE friends_id = '".$_POST['user_id']."'";
	 
	 $friendstmt1 = $db->conn->prepare("DELETE FROM friends_list WHERE `friends_id`=?");
    $friendstmt1->bind_param("i",$_POST['user_id']); 
     $friendstmt1->execute();
	}
	
    if($db->conn->query($backup))
    {
      if($stmt = $db->conn->prepare("DELETE FROM users WHERE `id`=?"))
      {
          ##########code for disabled all post################
          $stmt1 = $db->conn->prepare("UPDATE post SET deleted=1  WHERE user_id=?");
		  $stmt1->bind_param("i",$_POST['user_id']); 
           $stmt1->execute();
          
        $stmt->bind_param("i",$_POST['user_id']);      
        if($stmt->execute())
        {        
        //print_r($stmt);
            $result = array(
              'status' => true,
              'msg' => "Account deleted updated successfully",              
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
    else
    {
      throw new Exception("Somethig went wrong");
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
