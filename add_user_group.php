<?php
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  $date = $this->currentDate->format('Y-m-d H:i:s');
  if(isset($_POST['user_id']) && isset($_POST['by_user']) && isset($_POST['group_id']))
  {
    $admin = (isset($_POST['admin']))?$_POST['admin']:0;
    
    if($stmt = $db->conn->prepare("INSERT INTO group_users (user_id,admin,group_id,add_by,date_time) VALUES(?,?,?,?,?) "))
    {
      $stmt->bind_param("iiiss",$_POST['user_id'],$admin,$_POST['group_id'],$_POST['by_user'],$date);                  
      if($stmt->execute())
      {   
        $result = array(
          'status'=>true,
          'msg'=>'user added to group'
        );
        echo json_encode($result);          
      }
      else
      {
        throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
      }
      $stmt->close();      
    }
    else{
      throw new Exception("Prepare query failed group users");
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
