<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']) && isset($_POST['group_name']) && isset($_POST['users_list']))
  {
    $group_name = addslashes($_POST['group_name']);
    $posted_image = '';
    $admin =1;
    $date = $db->currentDate->format('Y-m-d H:i:s');
    if(isset($_POST['pic']) && !empty($_POST['pic']))
    {
      $posted_image = $db->upload_image($_POST['pic']);
    }
    if($stmt = $db->conn->prepare("INSERT INTO chat_groups (name,pic,user_id,date_time) VALUES(?,?,?,?)"))
    {
      $stmt->bind_param("ssis",$group_name,$posted_image,$_POST['user_id'],$date); 
      if($stmt->execute())
      {
        $group_id = $stmt->insert_id;
        if($stmt2 = $db->conn->prepare("INSERT INTO group_users (group_id,user_id,admin,date_time) VALUES(?,?,?,?) "))
        {  
          $stmt2->bind_param("iiis",$group_id,$_POST['user_id'],$admin,$date);      
          if($stmt2->execute())
          { 
            $data = array(
              'table'=>'group_users',
              'field'=> 'group_id,user_id,admin,add_by',
            );               
            foreach (json_decode($_POST['users_list']) as $row => $user) {
              
              $data['values'] [] = array($group_id,$user->user_id,0,$_POST['user_id']);            
            }
            if(isset($posted_image) && !empty($posted_image))
            {
              $posted_image = $db->mutimedia_dir.$posted_image;
            }
            else
            {
              $posted_image = '';
            }
            foreach (json_decode($_POST['users_list']) as $row => $user) {
              $fields = array (
                'to'=>"/topics/via".$user->user_id,
                'data' => array (
                  'id'=>$group_id,
                  'group_id'=>$group_id,
                  "noti_type"=>'group_created',
                  "group_name"=>$_POST['group_name'],
                  "group_pic"=>$posted_image,
                  'date'=>$result['date_time']  
                )
             );
            $stat = $db->sendPushNotification($fields);
            }
            //print_r($data);
            $db->insert($data);
            $result = array(
              'status'=>true,
              'msg'=>'Group Created',
              'group_id'=>$group_id,
              'group_pic'=> $posted_image
            );
            echo json_encode($result);          
          }
          else
          {
            throw new Exception("Query execution failed");
          }
         
        }
        else
        {
          throw new Exception("Group user query prepare failed");
          
        }
      }
      else{
        throw new Exception("Group query execution failed");
      }
    }
    else
    {
      throw new Exception("Prepare query failed chat groups");
    }
     $stmt->close();
          $stmt2->close();
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
