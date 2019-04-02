<?php
require_once('Connect_class.php');
try{
	$db = new db_connect();  
	if(isset($_POST['user_id']) && isset($_POST['group_id']))
	{		
/* 			$query = "UPDATE `group_chat` SET deleted=1 WHERE `group_id`=? AND user_id=?";
		//echo $query;
			if(!($stmt = $db->conn->prepare($query)))
			{
				throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
			}
			if(!$stmt->bind_param("ii",$_POST['group_id'],$_POST['user_id']))
			{
				throw new Exception("binding Error : (" . $stmt->errno . ") " . $stmt->error);
			}      
			if(!$stmt->execute())
			{ 
				throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
			}
			$stmt->close(); */
			

    
    if($stmt = $db->conn->prepare("UPDATE group_chat SET deleted=1 WHERE group_id=? AND user_id=?"))
    {
      $stmt->bind_param("ii",$_POST['group_id'],$_POST['user_id']);                  
      if($stmt->execute())
      {   
        $result = array(
          'status'=>true,
          'msg'=>'chat cleared from group for user'
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
$query2 = "UPDATE `group_users` SET `readed`=0 WHERE `group_id`=? ";
			if(!($stmt = $db->conn->prepare($query)))
			{
				throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
			}
			if(!$stmt->bind_param("i",$_POST['group_id']))
			{
				throw new Exception("binding Error : (" . $stmt->errno . ") " . $stmt->error);
			}      
			if(!$stmt->execute())
			{
				throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
			}
			$stmt->close();
			$result = array(
				'status' => true,
				'msg' => 'successful'
			);
			echo json_encode($result);
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
