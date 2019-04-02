<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $data = array(
        'table'=>'event_module',
        'select'=>'event_module.*,user_detail.name AS username,profile_pic.profile_pic AS profile_pic,(SELECT COUNT(id) FROM event_module_attand WHERE event_id=event_module.id) AS attanding,(SELECT COUNT(id) FROM event_module_attand WHERE event_id=event_module.id AND user_id="'.$user_id.'") AS is_i_attanding',
        'join'=>"JOIN user_detail ON event_module.user_id=user_detail.user_id JOIN profile_pic ON event_module.user_id=profile_pic.user_id AND profile_pic.profile_type=3",
        'where'=>'event_module.user_id='.$user_id.'  ORDER BY event_module.id DESC LIMIT 40',
      );
  $data = $db->get_all($data);
  foreach ($data as $key => $value) {
   if(!empty($value['img']) && $value['img'] != '')
    $data[$key]['img'] = (!empty($value['img']) && $value['img'] != '')?$db->mutimedia_dir.$value['img']:'';           
    $data[$key]['video'] = (!empty($value['video']) && $value['video'] != '')?$db->mutimedia_dir.$value['video']:'';  
  }
      $result = array(
        'status'=> true,
        'data'=>$data
      );
  echo json_encode($result);
}
?>
