<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  /*
  ====================================================================================================================
    Calling basic class and initiating the class
  ====================================================================================================================
  */
  require_once('Connect_class.php');
  $db = new db_connect();
  /*
  =====================================================================================================================
    Posting data and checking if the same data is posted
    ['user_id']['title']['profile_type']['img']['video']['description']['event_start']['event_end']
  =====================================================================================================================
  */
  try {
     if(isset($_POST['user_id']) && isset($_POST['event_id'])){
      // print_r($posted_images);die();
      $event_query = $db->conn->query("SELECT participent FROM event_module WHERE id='".$_POST['event_id']."'");
      $event_detail = $event_query->fetch_assoc();
       $participent = json_decode($event_detail['participent']);
      $participent = json_decode(json_encode($participent), true);
      $participent = array_column($participent,'id'); 
      //old code changes as per //harish
       //$participent = array_column($participent,'user_id');
     //print_r($participent);
      $list = $db->friends_list($_POST['user_id']);
      $list = array_diff($list, array($_POST['user_id']));
      $friends = array();
      if(count($list)>0)
      {
        $query = "SELECT user_detail.user_id AS id,name,profile_pic.profile_pic AS user_pic,IF(
        ( SELECT approved FROM friends_list WHERE approved=1 AND (
            (user_id='".$_POST['user_id']."' AND friends_id=user_detail.user_id) OR
            (user_id=user_detail.user_id AND friends_id='".$_POST['user_id']."')
          ) AND profile_type=1 LIMIT 1
        ) =1,1,0
      )AS is_friend, IF(
        ( SELECT approved FROM friends_list WHERE approved=1 AND (
            (user_id='".$_POST['user_id']."' AND friends_id=user_detail.user_id) OR
            (user_id=user_detail.user_id AND friends_id='".$_POST['user_id']."')
          ) AND profile_type=2 LIMIT 1
        ) =1,1,0
      )AS is_faimily, IF(
        ( SELECT approved FROM friends_list WHERE approved=1 AND (
            (user_id='".$_POST['user_id']."' AND friends_id=user_detail.user_id) OR
            (user_id=user_detail.user_id AND friends_id='".$_POST['user_id']."')
          ) AND profile_type=3 LIMIT 1
        ) =1,1,0
      )AS is_professional FROM user_detail JOIN profile_pic ON user_detail.user_id = profile_pic.user_id AND profile_pic.profile_type=3 WHERE user_detail.user_id IN (" . implode(',', $list) . ")";
        $mysql = $db->conn->query($query);
        if($mysql->num_rows > 0)
        {
          while($row = $mysql->fetch_assoc())
          {
            if(isset($row['user_pic']) && $row['user_pic']!= '' )
            {
              $row['user_pic'] = $db->mutimedia_dir.$row['user_pic'];
            }
            //echo $row['id'];
            //print_r($participent);
            $invited = in_array($row['id'], $participent)?True:False;
            $row['invited'] = $invited;
            $friends[] = $row;
          }
        }
      }

      if(isset($friends))
      {
        $array = array(
          'status'=>true,
          'connections'=>$friends
        );
        echo json_encode( $array );
      }
      else
      {
        throw new Exception("Query failed : ".filter_var($db->conn->error,FILTER_SANITIZE_STRING));
      }
    }
    else
    {
      throw new Exception("Missing required field");
    }
  } catch (Exception $e) {
    $array = array(
      'status'=>false,
      'msg'=>$e->getMessage()
    );
    echo json_encode( $array );
  }
 }
?>
