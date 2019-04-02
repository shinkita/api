<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once('Connect_class.php');
    $db = new db_connect();
    $name = addslashes($_POST['name']);
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $p_pic_fr = $_POST['pic_friend'];
    $p_pic_family = $_POST['pic_family'];
    $p_pic_pro = $_POST['pic_pro']; 
    $mobile = $_POST['mobile'];
    $path = $db->upload_dir;
    $country_code = $_POST['country_code'];
    try {
        if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['dob']) && isset($_POST['gender']) && isset($_POST['pic_friend']) && isset($_POST['pic_family']) && isset($_POST['pic_pro']) && isset($_POST['country_code'])) {
            $m_query='';
            if(isset($_POST['mobile']) && $_POST['mobile']!= '')
            {
                $m_query = "OR (country_code='" . $country_code . "' AND mobile='" . $mobile . "')";
            }
            $data = array(
                'table' => 'users',
                'where' => "`email`='" . $email . "' ".$m_query
            );
            $user = $db->get_row($data);
            if (!empty($user) && count($user)>0) {
                throw new Exception("User already exist");
                die();
            }
            $data = array(
                'table' => 'users',
                'field' => 'email,password,activated,mobile,country_code',
                'values' => array(array($email, sha1($password), 1, $mobile, $country_code))
            );
            $result = $db->insert($data);            
            function upload_pic($profile_pic, $path, $user_id) {
                $image_name = time() . '' . $user_id . '' . rand() . '.jpeg';
                $imageStr = base64_decode($profile_pic);      
                $image = imagecreatefromstring($imageStr);
                if($image  !== false)
                {
                  header('Content-Type: image/jpeg');
                  imagejpeg($image, __DIR__.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$image_name);
                  imagedestroy($image);
              }
              
                return $image_name;
            
        }
        $friend_pic = $family_pic = $pro_pic = '';
        if(!empty($p_pic_fr) && $p_pic_fr!='')
        {
            $friend_pic = upload_pic($p_pic_fr, $path, $result['inserted_id']);    
        }
        if(!empty($p_pic_family) && $p_pic_family!='')
        {
            $family_pic = upload_pic($p_pic_family, $path, $result['inserted_id']);
        }
        if(!empty($p_pic_pro) && $p_pic_pro!='')
        {
            $pro_pic = upload_pic($p_pic_pro, $path, $result['inserted_id']);    
        }        
        $shadow = array(
            'table' => 'profile_pic',
            'field' => 'user_id,profile_pic,profile_type',
            'values' => array(array($result['inserted_id'], $friend_pic, 1), array($result['inserted_id'], $family_pic, 2), array($result['inserted_id'], $pro_pic, 3))
        );
        $shadow1 = $db->insert(array('table' => 'user_detail', 'field' => 'user_id,name,dob,gender', 'values' => array(array($result['inserted_id'], $name, $dob, $gender))));
        $shadow_result = $db->insert($shadow);
            //print_r($shadow_result);
        $shadow3 = array(
            'table' => 'user_profile_about',
            'field' => 'user_id,profile_type,bio,phone,address,website,education,work,description,previous_work',
            'values' => array(array($result['inserted_id'], 1, '', '', '', '', '', '', '', ''), array($result['inserted_id'], 2, '', '', '', '', '', '', '', ''), array($result['inserted_id'], 3, '', '', '', '', '', '', '', ''))
        );
        $shadow3 = $db->insert($shadow3); 
        $final_result = $db->get_user_data_id($result['inserted_id']);
        $final_result['msg'] = 'User created succesfully';
        echo json_encode($final_result);
        die();
    } else {
        throw new Exception('Any required field missing');
    }
} catch (Exception $e) {
    $result = array(
        'status' => false,
        'msg' => $e->getMessage()
    );
    echo json_encode($result);
    die(); 
}
}
?>


