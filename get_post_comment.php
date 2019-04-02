<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  //$user_id      = $_POST['user_id'];
  $postid       = $_POST['post_id'];
  $user_id  = $_POST['user_id'];
  //print_r($_POST);exit;
  $sub = array(      
    'table'=>'post',
    'select'=>'profile_type',      
    'where'=>" `id`='".$postid."' ",
  );
  $p_type = $db->get_row($sub)['profile_type'];
  $data = $db->get_post_comment($postid,0);
  // $que = "SELECT DISTINCT user_id,friends_id FROM friends_list WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND approved=1";
  //   $list[] = $user_id;
  //   $msql = $db->conn->query($que);
  //   if ($msql->num_rows > 0) {
  //       while ($row = $msql->fetch_assoc()) {
  //           if (!in_array($row['friends_id'], $list)) {
  //               $list[] = $row['friends_id'];
  //           }
  //           if (!in_array($row['user_id'], $list)) {
  //               $list[] = $row['user_id'];
  //           }
  //       }
  //   }    
  //   $list = array_diff($list, array($user_id));
  //   $query = "SELECT user_id,name,profile_pic.profile_pic JOIN profile_pic ON profile_pic.user_id=user_detail.user_id FROM user_detail WHERE user_id IN (" . implode(',', $list) . ") AND ";
  // echo $p_type;
    $fir_list = array();
    $fir_list = $db->friends_list_datails($user_id, $p_type);
    // $fir_list = $db->friends_list_datail($user_id);
    //print_r($fir_list);
  $result = array(
    'status'=> true,
    'data'=>$data
  );
  $result['friends_list'] = $fir_list;
  echo json_encode($result);
}
?>
