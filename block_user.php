<?php
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['block_user_id']))
  {    
      if($stmtone = $db->conn->prepare("SELECT * FROM block_user WHERE user_id=? AND block_user_id=?"))
      {
        $stmtone->bind_param('ii',$_POST['user_id'],$_POST['block_user_id']);
        if($stmtone->execute())
        {
          $row = $stmtone->get_result();
          $blocked = $row->fetch_assoc();
        }
        else{
          throw new Exception("Query execution failed");
        }
      } else{
        throw new Exception("prepare command failed");
      }
      $stmtone->close();
      $block = 1;
      if(empty($blocked))
      {
        $query = "INSERT INTO block_user(`block`,`user_id`,`block_user_id`) VALUES(?,?,?)";
      }
      else
      {
        $query = "UPDATE `block_user` SET `block`=?  WHERE `user_id`=? AND `block_user_id`=?"; 
      }
      if($stmt = $db->conn->prepare($query)) 
      {
        $stmt->bind_param("iii",$block,$_POST['user_id'],$_POST['block_user_id']);      
        if($stmt->execute())
        {        
        //print_r($stmt)
                  $fields = array (
                  'to'=>"/topics/via".$_POST['block_user_id'],
                  'data' => array (
                    'id'=>$_POST['user_id'],
                    "noti_type"=>'block_changed',
                    "user_id"=>$_POST['user_id'],
                    "blocked"=>$block
                )
              );
            $stat = $db->sendPushNotification($fields);
            $result = array(
              'status' => true,
              'msg' => "blocked successfully"
            );
            echo json_encode($result);
            die();
          
        }
        else
        {
          throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
        }
        
      }
      else
      {
      	throw new Exception('Prepare failed');
      }
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