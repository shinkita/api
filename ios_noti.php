<?php 
// $fields = array(
//     'to' => "/topics/via31",
//     'notification' => array('title' => 'This is title', 'body' => 'This is body'),
//     'data' => array('message' => 'testing to id 31')
// );
$fields = array(
    'to' => "/topics/via31",
    'data'=>array(
        "id"=>"1810",
        "noti_type"=>"updated_status",
        "user_id"=>"46",
        "user_name"=>"Harry"
    ),
    "notification"=>array('title' => 'This is title', 'body' => '')
);
$url = 'https://fcm.googleapis.com/fcm/send';
        $fields = json_encode($fields);
        $headers = array(
            'Authorization: key=' . "AAAAaiUMGs8:APA91bGRWtp6arKBhGhavmBj5MJP42oy6bF61sMVKga8JrRbeMXp46M9SUHmnBaQ5puy0R0ub48igkfIGDGG4BqfkNsZq_SNB3BEWDNKNkJ1t0Sp5Bv7fJIvSn6wro7f8py45BteOo1O",
            'Content-Type: application/json');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        $result = (array) json_decode($result);  
        print_r($result);      
        //echo $fields;
        //echo json_encode($result);die();
        curl_close($ch);

        //and return the result 
        return $result;
?>