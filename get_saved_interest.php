<?php
require_once('Connect_class.php');
$db = new db_connect();

$user_id      = $_POST['user_id'];
$data = array(
  'table'=>' interest_category_tbl',
);
 

$saveddata = array(
  'table'=>' user_detail ',
  'where' => " `user_id` = '" . $user_id . "'",
);
$result_saveddata = $db->get_all($saveddata);
$interest_id=$result_saveddata[0]['interest'];
					$interest_id= str_replace(",", "','", $interest_id); 
$data1 = array(
  'table'=>' interest_category_tbl',
  'where' => "`id` in('" . $interest_id . "')",
);

$result_sub1 = $db->get_all($data1);
$result_sub = $db->get_all($data);
 

foreach($result_sub1 as $result)
{

if($result['status']==1)  
$saved_interest[]=array('interest_id'=>$result['id'],'interest_name'=>$result['category_name']);
}

$i=0;
foreach($result_sub as $result)
{
/*if($result['status']==1 && $saved_interest[$i]['interest_id']!=$result['id'])*/ 
$matched_saved_interest =array_column($saved_interest, 'interest_id');
 
if(in_array($result['id'], $matched_saved_interest) == false )  

$final_result[]=array('interest_id'=>$result['id'],'interest_name'=>$result['category_name']);
 
$i++;
}
 if(empty($final_result))
	 $final_result=[];
	 if(empty($saved_interest))
	$saved_interest=[];
	 
 
$result = array(
  'status'=>true,
  'saved_interest_type'=>$saved_interest,
  'interest_type'=>$final_result,
  'msg'=>'success'
);
echo json_encode($result);
?>
