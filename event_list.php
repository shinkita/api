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
     if(isset($_POST['user_id'])){
      // print_r($posted_images);die();
      $_query = "SELECT * FROM event_module WHERE user_id='".$_POST['user_id']."' ORDER BY id DESC";
      if($event_query = $db->conn->query($_query))
      {
        $data = array();
        if($event_query->num_rows>0)
        {
            while ($row = $event_query->fetch_assoc()) {

              $row['img'] = (!empty($row['img']) && $row['img'] != '')?$db->mutimedia_dir.$row['img']:'';
              $row['video'] = (!empty($row['video']) && $row['video'] != '')?$db->mutimedia_dir.$row['video']:'';
              $data[] = $row;
            }
        }
        $array = array(
          'status'=>true,
          'data'=>$data
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
