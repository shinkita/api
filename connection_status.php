<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
if($_SERVER['REQUEST_METHOD']=='POST')
{
	require_once('Connect_class.php');
	$db = new db_connect();
	$user_id    = $_POST['user_id_1'];  
	$to_user_id    = $_POST['user_id_2'];  
		$sql = "SELECT * FROM friends_list WHERE approved=1 AND (user_id='".$user_id."' OR friends_id='".$user_id."') AND (user_id='".$to_user_id ."' OR friends_id='".$to_user_id ."')";
		$msql = $db->conn->query($sql);
		$isProfessional= $isFamily = $isFriend = "false";
		if ($msql->num_rows > 0) {
			while ($row = $msql->fetch_assoc()) {
					if($row['profile_type'] == 1)
					$isFriend="true";
					if($row['profile_type'] == 2)
					$isFamily="true";
					if($row['profile_type'] == 3)
					$isProfessional="true";				   
			}
		}	
		
      $result = array(
        'status'=> true,
        'data'=>array(
        'isFriend'=>$isFriend,
        'isFamily'=>$isFamily,
        'isProfessional'=>$isProfessional
    )
      );

  echo json_encode($result);
}
?>