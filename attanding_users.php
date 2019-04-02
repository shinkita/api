<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $post_id = $_POST['event_id'];
  $attand = $db->conn->query("SELECT user_id FROM event_module_attand WHERE event_id='".$post_id."'");
  $event_query = $db->conn->query("SELECT user_id FROM event_module WHERE id='".$post_id."'");
  $event_master = $event_query->fetch_assoc();
  $user = array();
  while ($row = $attand->fetch_assoc()) {
  	// print_r($row);
  	$data_user = array(
                'table' => 'user_detail',
                'select' => 'user_detail.user_id AS id,user_detail.name AS name,(SELECT profile_pic FROM profile_pic WHERE profile_type=1 AND user_id=' . $row['user_id'] . ') AS friend_pic,(SELECT profile_pic FROM profile_pic WHERE profile_type=2 AND user_id=' . $row['user_id'] . ') AS faimily_pic,(SELECT profile_pic FROM profile_pic WHERE profile_type=3 AND user_id=' . $row['user_id'] . ') AS pro_pic,
                IF(
	                (
	                	SELECT approved FROM friends_list WHERE (user_id = '.$event_master['user_id'].' OR friends_id = '.$event_master['user_id'].') AND (user_id = '.$row['user_id'].' OR friends_id = '.$row['user_id'].') AND profile_type=1 AND approved=1
	            	)=1,1,0 
	            )	AS isfriends,
	            IF(
	                (
	                	SELECT approved FROM friends_list WHERE (user_id = '.$event_master['user_id'].' OR friends_id = '.$event_master['user_id'].') AND (user_id = '.$row['user_id'].' OR friends_id = '.$row['user_id'].') AND profile_type=2 AND approved=1
	            	)=1,1,0
	            )  AS isfaimily,
	             IF(
	                (
	                	SELECT approved FROM friends_list WHERE (user_id = '.$event_master['user_id'].' OR friends_id = '.$event_master['user_id'].') AND (user_id = '.$row['user_id'].' OR friends_id = '.$row['user_id'].') AND profile_type=3 AND approved=1
	            	)=1,1,0
	            )	 AS isprofessional	
	            	',
                'where' => 'user_id=' . $row['user_id'] . ''
            );
  	 if ($u_detail = $db->get_row($data_user)) {

	    if ($u_detail['friend_pic'] != '' && !empty($u_detail['friend_pic'])) {
	        $u_detail['friend_pic'] = $db->mutimedia_dir . $u_detail['friend_pic'];
	    }
	    if ($u_detail['faimily_pic'] != '' && !empty($u_detail['faimily_pic'])) {
	        $u_detail['faimily_pic'] = $db->mutimedia_dir . $u_detail['faimily_pic'];
	    }
	    if ($u_detail['pro_pic'] != '' && !empty($u_detail['pro_pic'])) {
	        $u_detail['pro_pic'] = $db->mutimedia_dir . $u_detail['pro_pic'];
	    }
	    // print_r($u_detail);
	}    
    $user[] = $u_detail;

  }
  // print_r($user);
      $subresult = array(
        'status'=>true,
        'users'=>$user
      );
  echo json_encode($subresult);
}
?>
