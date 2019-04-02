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
     if(isset($_POST['event_id'])){
      // print_r($posted_images);die();
        $data = array(
        'table'=>'event_module',
        'select'=>'event_module.*,user_detail.name AS username,profile_pic.profile_pic AS profile_pic,(SELECT COUNT(id) FROM event_module_attand WHERE event_id=event_module.id) AS attanding,(SELECT COUNT(id) FROM event_module_attand WHERE event_id=event_module.id AND user_id=event_module.user_id) AS is_i_attanding',
        'join'=>"LEFT JOIN event_module_attand ON event_module_attand.event_id=event_module.id JOIN user_detail ON event_module.user_id=user_detail.user_id JOIN profile_pic ON event_module.user_id=profile_pic.user_id AND profile_pic.profile_type=3",
        'where'=>'event_module.id="'.$_POST['event_id'].'" ',
      );
  		$data = $db->get_row($data);

              $data['img'] = (!empty($data['img']) && $data['img'] != '')?$db->mutimedia_dir.$data['img']:'';
              $data['video'] = (!empty($data['video']) && $data['video'] != '')?$db->mutimedia_dir.$data['video']:'';
              $data['profile_pic'] = (!empty($data['profile_pic']) && $data['profile_pic'] != '')?$db->mutimedia_dir.$data['profile_pic']:'';
              $array = array(
              'status'=>true,
              'data'=>$data
            );
            echo json_encode( $array );
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
