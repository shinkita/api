<?php
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', true);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once('Connect_class.php');
    $db = new db_connect();
    $email = $_POST['email'];
    $password = $_POST['password'];    
    try {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $data = array(
                'table' => 'users',
                'where' => "`email`='" . $email . "'",
            );
            $user = $db->get_row($data);            
            if (!empty($user) && count($user) > 0) {
                if ($user['activated'] == 0) {
                    throw new Exception("User not activated");
                } else if ($user['password'] !== sha1($password)) {
                    throw new Exception("Incorrect password");
                } else {
                    $u_detail = $db->get_user_data_id($user['id']);
					$interest_id= $u_detail['interest_id'];
					$interest_id= str_replace(",", "','", $interest_id); 
					$data = array(
  'table'=>' interest_category_tbl',
  'where' => "`id` in('" . $interest_id . "')",
);
$result_sub = $db->get_all($data);

foreach($result_sub as $result)
{

if($result['status']==1)  
$final_result[]=array('interest_id'=>$result['id'],'interest_name'=>$result['category_name']);
}
 
                    $date = $db->currentDate->format('Y-m-d H:i:s');
                    $query['table'] = 'users';
                    $query['data'] = " login_time='" . $date . "', login_count=login_count+1 ";
                    $query['where'] = " id='" . $u_detail['id'] . "'";
                    $db->update($query);
                    $result = array(
                        'status' => true,
                        'data' => $u_detail,
						'interest_type'=>$final_result,
                        'msg' => 'login success'
                    );
                    echo json_encode($result);
                    die();
                }
            } else {
                throw new Exception("User does not exist");
            }
        } else {
            throw new Exception("Missing email or password");
        }
    } catch (Exception $ex) {
        $result = array(
            'status' => false,
            'msg' => $ex->getMessage()
        );
        echo json_encode($result);
        die();
    }
}
?>