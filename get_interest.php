<?php
require_once('Connect_class.php');
$db = new db_connect();
$data = array(
  'table'=>' interest_category_tbl',
);
$result_sub = $db->get_all($data);

foreach($result_sub as $result)
{

if($result['status']==1)  
$final_result[]=array('interest_id'=>$result['id'],'interest_name'=>$result['category_name']);
}
 
$result = array(
  'status'=>true,
  'interest_type'=>$final_result,
  'msg'=>'success'
);
echo json_encode($result);
?>
