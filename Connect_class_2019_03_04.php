<?php
error_reporting(E_ALL);
ini_set("display_errors", 0);


use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;


Class db_connect {

    var $upload_dir;
    var $mutimedia_dir;
    var $api_dir;
    var $conn;
    var $timezone;
    var $currentDate;

    public function __construct() {
      $this->conn = new mysqli('localhost', 'vsadmin_viaspot', 'Welcome90#@!', "vsadmin_new_viaspot");
		//$this->conn = new mysqli('localhost', 'root', '', "vsadmin_new_viaspot");
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        $this->upload_dir =    'viaspot_users';
        $this->api_dir = 'https://' . $_SERVER['SERVER_NAME'] . '/vsdeveloper/api/';
        $this->mutimedia_dir = 'https://' . $_SERVER['SERVER_NAME'] . '/vsdeveloper/api/viaspot_users/';
        $this->currentDate = new DateTime();
        $this->currentDate->setTimezone(new DateTimeZone('GMT'));
        // if(isset($_POST))
        // {
        //     foreach ($_POST as $key => $value) {
        //         if(is_string($value))
        //         {
        //             if($key != 'images' && $key != 'image' && $key!='pic')
        //             {
        //                 $_POST[$key] = filter_var($value, FILTER_SANITIZE_STRING);
        //             }
        //         }
        //     }
        // }
    }

    public function count($data) {
        $query = "SELECT COUNT(*) AS count ";
        $query .= "FROM " . $data['table'] . " ";
        if (isset($data['where']) && !empty($data['where']) && count($data['where']) > 0) {
            $query .= "WHERE " . $data['where'] . " ";
        }
        //echo $query;
        if ($result = $this->conn->query($query)) {
            $count = $result->fetch_assoc();
            $count = $count['count'];
        } else {
            die('Database Query Faild: ' . $this->conn->error);
        }
        return $count;
    }

    public function insert($data) {
        // $date = date('Y-m-d H:i:s');
        $date = $this->currentDate->format('Y-m-d H:i:s');
        $query = "INSERT INTO " . $data['table'] . " ( ";
        $query .= $data['field'] . ",date_time) VALUES";
        //print_r($data);
        foreach ($data['values'] as $key => $value) {
            //print_r(array_keys($data['values']));

            $end_key = end(array_keys($data['values']));
            //echo $end_key;die();
            if ($end_key == $key) {
                $seprate = " ";
            } else {
                $seprate = ",";
            }
            $val = implode("','", $value);
            $query .= "('" . $val . "','" . $date . "')" . $seprate;
        }
        // echo $this->timezone;
        // echo $query;
         // exit;
        if ($this->conn->query($query) === TRUE) {
            $result = array(
                'status' => true,
                'date_time' => $date,
                'inserted_id' => $this->conn->insert_id,
                'msg' => 'Values successfully inserted'
            );
        } else {
            $result = array(
                'status' => false,
                'date_time' => $date,
                'msg' => 'Database Query Faild: ' . $this->conn->error
            );
        }
        return $result;
    }

    public function update($data) {
        $query = "UPDATE " . $data['table'] . " ";
        $query .= "SET " . $data['data'] . " ";
        if (isset($data['where']) && !empty($data['where']) && count($data['where']) > 0) {
            $query .= "WHERE " . $data['where'] . " ";
        }
        //echo $query;
        if ($this->conn->query($query)) {
            $result = array(
                'status' => true,
                'affected_rows' => $this->conn->affected_rows,
                'msg' => 'Succefully updated'
            );
        } else {
            die('Database Query Faild: ' . $this->conn->error);
        }
        return $result;
    }

    public function delete($data) {

    }

    public function get_row($data) {
        $query = '';
        if (isset($data['select']) && !empty($data['select'])) {
            $query .= "SELECT " . $data['select'] . " ";
        } else {
            $query .= "SELECT * ";
        }
        $query .= "FROM " . $data['table'] . " ";
        if (isset($data['join'])) {
            $query .= " " . $data['join'] . " ";
        }
        if (isset($data['where']) && !empty($data['where']) && count($data['where']) > 0) {
            $query .= "WHERE " . $data['where'] . " ";
        }
        //echo $query;
        if ($result = $this->conn->query($query)) {
            $get_row = array();
            if ($result->num_rows > 0) {
                $get_row = $result->fetch_assoc();
            }
        } else {
            die('Database Query Faild: ' . $this->conn->error);
        }
        return $get_row;
    }

    public function get_all($data) {
        $query = '';
        if (isset($data['select']) && !empty($data['select'])) {
            $query .= "SELECT " . $data['select'] . " ";
        } else {
            $query .= "SELECT * ";
        }
        $query .= "FROM " . $data['table'] . " ";
        if (isset($data['join'])) {
            $query .= " " . $data['join'] . " ";
        }
        if (isset($data['where']) && !empty($data['where']) && count($data['where']) > 0) {
            $query .= "WHERE " . $data['where'] . " ";
        }
         //echo $query;
        if ($result = $this->conn->query($query)) {
            $rows = array();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if (isset($row['profile_pic'])) {
                        if (!empty($row['profile_pic']) && $row['profile_pic'] != '') {
                            //$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
                            $row['profile_pic'] = $this->mutimedia_dir . $row['profile_pic'];
                        }
                    } else if (isset($row['user_pic'])) {
                        if (!empty($row['user_pic']) && $row['user_pic'] != '') {
                            //$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
                            $row['user_pic'] = $this->mutimedia_dir . $row['user_pic'];
                        }
                    }
                    $rows[] = $row;
                }
            }
        } else {
            die('Database Query Faild: ' . $this->conn->error);
        }
        return $rows;
    }

    public function get_post_all($data) {
        $query = '';
        if (isset($data['select']) && !empty($data['select'])) {
            $query .= "SELECT " . $data['select'] . " ";
        } else {
            $query .= "SELECT * ";
        }
        $query .= "FROM " . $data['table'] . " ";
        if (isset($data['join'])) {
            $query .= " " . $data['join'] . " ";
        }
        if (isset($data['where']) && !empty($data['where']) && count($data['where']) > 0) {
            $query .= "WHERE " . $data['where'] . " ";
        }
         // echo $query;
        if ($result = $this->conn->query($query)) {
            $rows = array();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if (!empty($row['event']) && $row['event'] != '') {
                        $data = array(
                            'table' => 'event',
                            'select' => 'title,description,event_start',
                            'where' => 'event.id=' . $row['event'] . ' '
                        );
                        $row['event'] = self::get_row($data);
                    }
                    //print_r($row);
                    if ($row['images'] != null) {
                        $images = array();
                        $image = json_decode($row['images']);
                        foreach ($image as $key => $val) {
                            //$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
                            $images[] = array('image_data' => $this->mutimedia_dir . $val);
                        }
                        $row['images'] = $images;
                    } else {
                        $row['images'] = array();
                    }
                    if (!empty($row['video'])) {
                        //$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
                        $row['video'] = $this->mutimedia_dir . $row['video'];
                    }
                    $row['isLikedByMe'] = ($row['isLikedByMe'] == 1) ? true : false;
                    //$row['isLikedByMe'] = int() $row['isLikedByMe'];
                    if ($row['profile_pic'] != '' && !empty($row['profile_pic'])) {
                        $row['profile_pic'] = $this->mutimedia_dir . $row['profile_pic'];
                    }
                    if ($row['file']!=null)
                    {
                        $row['file'] = $this->mutimedia_dir . $row['file'];
                    }
                    // $row['username'] = 'username';
                    // $row['userimage'] = '';
                    // $row['no_of_likes'] = 10;
                    //$row['no_of_share'] = 10;
                    //$row['no_of_comment'] = 10;
                    $rows[] = $row;
                }
            }
        } else {
            die('Database Query Faild: ' . $this->conn->error);
        }
        return $rows;
    }

    public function get_post_row($data) {
        $query = '';
        if (isset($data['select']) && !empty($data['select'])) {
            $query .= "SELECT " . $data['select'] . " ";
        } else {
            $query .= "SELECT * ";
        }
        $query .= "FROM " . $data['table'] . " ";
        if (isset($data['join'])) {
            $query .= " " . $data['join'] . " ";
        }
        if (isset($data['where']) && !empty($data['where']) && count($data['where']) > 0) {
            $query .= "WHERE " . $data['where'] . " ";
        }
        //echo $query;
        if ($result = $this->conn->query($query)) {
            $row = array();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (!empty($row['event']) && $row['event'] != '') {
                    $data = array(
                        'table' => 'event',
                        'select' => 'title,description,event_start',
                        'where' => 'event.id=' . $row['event'] . ' '
                    );
                    $row['event'] = self::get_row($data);
                }
                //print_r($row);
                if ($row['images'] != null) {
                    $image = json_decode($row['images']);
                    foreach ($image as $key => $val) {
                        //$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
                        $images[] = array('image_data' => $this->mutimedia_dir . $val);
                    }
                    $row['images'] = $images;
                } else {
                    $row['images'] = array();
                }
                if (!empty($row['video'])) {
                    //$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
                    $row['video'] = $this->mutimedia_dir . $row['video'];
                }
                $row['isLikedByMe'] = ($row['isLikedByMe'] == 1) ? true : false;
                //$row['isLikedByMe'] = int() $row['isLikedByMe'];
                if ($row['profile_pic'] != '' && !empty($row['profile_pic'])) {
                    $row['profile_pic'] = $this->mutimedia_dir . $row['profile_pic'];
                }
                if ($row['file']!=null)
                    {
                        $row['file'] = $this->mutimedia_dir . $row['file'];
                    }
                // $row['username'] = 'username';
                // $row['userimage'] = '';
                // $row['no_of_likes'] = 10;
                //$row['no_of_share'] = 10;
                //$row['no_of_comment'] = 10;
            }
        } else {
            die('Database Query Faild: ' . $this->conn->error);
        }
        return $row;
    }

    public function get_query($query) {

    }

    public function get_all_query($query) {

    }

    public function friends_list($user_id) {
        $que = "SELECT DISTINCT user_id,friends_id FROM friends_list  JOIN users usa ON usa.id=user_id JOIN users usb ON usb.id =friends_id WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND approved=1 AND usa.activated=1 AND usa.deleted=0 AND usb.activated=1 AND usb.deleted=0";
        // die();
        $list[] = $user_id;
        $msql = $this->conn->query($que);
        if ($msql->num_rows > 0) {
            while ($row = $msql->fetch_assoc()) {
                if (!in_array($row['friends_id'], $list)) {
                    $list[] = $row['friends_id'];
                }
                if (!in_array($row['user_id'], $list)) {
                    $list[] = $row['user_id'];
                }
            }
        }
        return $list;
    }

    public function friends_list_datail($user_id) {
        $que = "SELECT DISTINCT user_id,friends_id FROM friends_list  JOIN users usa ON usa.id=user_id JOIN users usb ON usb.id =friends_id WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND approved=1 AND usa.activated=1 AND usa.deleted=0 AND usb.activated=1 AND usb.deleted=0";
        $list[] = $user_id;
        $msql = $this->conn->query($que);
        if ($msql->num_rows > 0) {
            while ($row = $msql->fetch_assoc()) {
                if (!in_array($row['friends_id'], $list)) {
                    $list[] = $row['friends_id'];
                }
                if (!in_array($row['user_id'], $list)) {
                    $list[] = $row['user_id'];
                }
            }
        }
        $list = array_diff($list, array($user_id));
        $query = "SELECT user_detail.user_id AS id,name,profile_pic.profile_pic AS user_pic FROM user_detail JOIN profile_pic ON user_detail.user_id = profile_pic.user_id AND profile_pic.profile_type=(CASE WHEN (SELECT approved FROM friends_list WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND (user_id = user_detail.user_id OR friends_id = user_detail.user_id) AND approved=1 AND profile_type=1 LIMIT 1)=1 THEN 1 WHEN (SELECT approved FROM friends_list WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND (user_id = user_detail.user_id OR friends_id = user_detail.user_id) AND approved=1 AND profile_type=2 LIMIT 1)=1 THEN 2 ELSE 3 END) WHERE user_detail.user_id IN (" . implode(',', $list) . ")";
        //$query = "SELECT user_id FROM user_detail WHERE user_detail.user_id IN (".implode(',',$list).")";
        //echo $query;
        $msql1 = $this->conn->query($query);
        $data = array();
        if ($msql1->num_rows > 0) {
            while ($row = $msql1->fetch_assoc()) {
                if (!empty($row['user_pic']) || $row['user_pic'] != '') {
                    $row['user_pic'] = $this->mutimedia_dir . $row['user_pic'];
                }
                $data[] = $row;
            }
        }
        return $data;
    }
    public function friends_list_datail_group_chat($user_id) {

        $query = "SELECT DISTINCT group_users.group_id AS id,chat_groups.name AS group_name,chat_groups.pic AS group_pic, group_chat.message AS msg,group_chat.chat_type AS chat_type,group_chat.date_time AS chat_date_time, group_users.readed AS unread FROM group_users JOIN chat_groups ON group_users.group_id=chat_groups.id AND chat_groups.deleted=0 LEFT JOIN group_chat ON group_chat.group_id=group_users.group_id AND group_chat.id=(SELECT MAX(group_chat.id) FROM group_chat WHERE group_chat.group_id=group_users.group_id AND deleted=0) WHERE group_users.user_id='".$user_id."' ORDER BY group_users.group_id";
        $msql1 = $this->conn->query($query);
        $data = array();
        if ($msql1->num_rows > 0) {
            while ($row = $msql1->fetch_assoc()) {
                if($row['msg'] == NULL)
                    {
                        $row['msg'] = '';
                    }
                    if($row['chat_date_time'] == NULL)
                    {
                        $row['chat_date_time'] = '';
                    }
                     if($row['chat_type'] == NULL)
                    {
                        $row['chat_type'] = '';
                    }
                //print_r($row);
                if (!empty($row['pic']) || $row['pic'] != '') {
                    $row['pic'] = $this->mutimedia_dir . $row['pic'];
                }
                if (!empty($row['group_pic']) || $row['group_pic'] != '') {
                    $row['group_pic'] = $this->mutimedia_dir . $row['group_pic'];
                }
                $data[] = $row;
            }
        }
        return $data;
    }
    public function friends_list_datail_chat($user_id) {
        $que = "SELECT DISTINCT user_id,friends_id FROM friends_list  JOIN users usa ON usa.id=user_id JOIN users usb ON usb.id =friends_id WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND approved=1 AND usa.activated=1 AND usa.deleted=0 AND usb.activated=1 AND usb.deleted=0 ";
        $list[] = $user_id;
        $msql = $this->conn->query($que);
        if ($msql->num_rows > 0) {
            while ($row = $msql->fetch_assoc()) {
                if (!in_array($row['friends_id'], $list)) {
                    $list[] = $row['friends_id'];
                }
                if (!in_array($row['user_id'], $list)) {
                    $list[] = $row['user_id'];
                }
            }
        }
        $list = array_diff($list, array($user_id));
        $list = implode(',', $list);

        $date = $this->currentDate->format('Y-m-d H:i:s');
        $ime = date('Y-m-d H:i:s', strtotime($date,"-1 minute"));
        /*
        $query = "SELECT user_detail.id AS id, user_detail.name AS name, profile_pic.profile_pic AS user_pic, chat.message AS msg, chat.chat_type AS chat_type, chat.date_time AS chat_date_time , IF(users.online_status>'" . $ime . "','true','false') AS online_status, (SELECT COUNT(id) FROM chat WHERE readed=0 AND deleted=0 AND chat.user_id=user_detail.user_id AND chat.to_user_id='".$user_id."') AS unread ,users.online_status AS online_time FROM user_detail LEFT JOIN chat ON user_detail.user_id=chat.user_id AND chat.id=(SELECT chat.id FROM chat LEFT JOIN delete_user_chat ON chat.id=delete_user_chat.chat_id AND delete_user_chat.user_id='".$user_id."' ORDER BY chat.id DESC LIMIT 1) LEFT JOIN profile_pic ON profile_pic.user_id=chat.user_id AND profile_pic.profile_type=(CASE WHEN (SELECT approved FROM friends_list WHERE (user_id ='" . $user_id . "' OR friends_id = '" . $user_id . "') AND (user_id = user_detail.user_id OR friends_id = user_detail.user_id) AND approved=1 AND profile_type=1)=1 THEN 1 WHEN (SELECT approved FROM friends_list WHERE (user_id = '" . $user_id . "' OR friends_id = '" . $user_id . "') AND (user_id = user_detail.user_id OR friends_id = user_detail.user_id) AND approved=1 AND profile_type=2)=1 THEN 2 ELSE 3 END) LEFT JOIN users ON users.id=chat.user_id WHERE user_detail.user_id IN ($list)";
        */

        $query = "SELECT user_detail.user_id AS id, user_detail.name AS name, profile_pic.profile_pic AS user_pic, (SELECT COUNT(id) FROM chat WHERE readed=0 AND deleted=0 AND chat.user_id=user_detail.user_id AND chat.to_user_id='".$user_id."') AS unread , IF(
            users.online_status>'" . $ime . "','true','false') AS online_status ,users.online_status AS online_time
            FROM user_detail
            LEFT JOIN users ON users.id=user_detail.user_id LEFT JOIN profile_pic ON profile_pic.user_id=user_detail.user_id AND profile_pic.profile_type=(CASE WHEN (SELECT approved FROM friends_list WHERE (user_id ='" . $user_id . "' OR friends_id = '" . $user_id . "') AND (user_id = user_detail.user_id OR friends_id = user_detail.user_id) AND approved=1 AND profile_type=1 LIMIT 1)=1 THEN 1 WHEN (SELECT approved FROM friends_list WHERE (user_id = '" . $user_id . "' OR friends_id = '" . $user_id . "') AND (user_id = user_detail.user_id OR friends_id = user_detail.user_id) AND approved=1 AND profile_type=2 LIMIT 1)=1 THEN 2 ELSE 3 END) WHERE user_detail.user_id IN ($list)";
        $msql1 = $this->conn->query($query);
        $data = array();
        if ($msql1->num_rows > 0) {
            while ($row = $msql1->fetch_assoc()) {
               $qt = "SELECT chat.message AS msg, chat.chat_type AS chat_type, chat.date_time AS chat_date_time FROM chat LEFT JOIN delete_user_chat ON chat.id=delete_user_chat.chat_id AND delete_user_chat.user_id='".$user_id."' WHERE chat.user_id='".$row['id']."' AND to_user_id='".$user_id."' AND delete_user_chat.chat_id IS NULL AND delete_user_chat.user_id IS NULL ORDER BY chat.id DESC LIMIT 1";
                $msg = $this->conn->query($qt);
                if($msg->num_rows>0)
                {
                    $msg = $msg->fetch_assoc();
                    $row['msg'] = $msg['msg'];
                    $row['chat_type'] = $msg['chat_type'];
                    $row['chat_date_time'] = $msg['chat_date_time'];
                }
                else
                {
                    $row['msg'] =$row['chat_type'] = $row['chat_date_time'] = NULL;
                }
                if (!empty($row['user_pic']) || $row['user_pic'] != '') {
                    $row['user_pic'] = $this->mutimedia_dir . $row['user_pic'];

                }
                $data[] = $row;
            }
        }
        return $data;
    }

    public function friends_list_datails($user_id, $profile_type) {
        $que = "SELECT DISTINCT user_id,friends_id FROM friends_list  JOIN users usa ON usa.id=user_id JOIN users usb ON usb.id =friends_id WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND approved=1 AND usa.activated=1 AND usa.deleted=0 AND usb.activated=1 AND usb.deleted=0 AND profile_type='$profile_type'";
        $list[] = $user_id;
        $msql = $this->conn->query($que);
        if ($msql->num_rows > 0) {
            while ($row = $msql->fetch_assoc()) {
                if (!in_array($row['friends_id'], $list)) {
                    $list[] = $row['friends_id'];
                }
                if (!in_array($row['user_id'], $list)) {
                    $list[] = $row['user_id'];
                }
            }
        }
        $list = array_diff($list, array($user_id));
        $query = "SELECT user_detail.user_id AS id,name,profile_pic.profile_pic AS user_pic FROM user_detail JOIN profile_pic ON user_detail.user_id = profile_pic.user_id AND profile_pic.profile_type=(CASE WHEN (SELECT approved FROM friends_list WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND (user_id = user_detail.user_id OR friends_id = user_detail.user_id) AND approved=1 AND profile_type=1 LIMIT 1)=1 THEN 1 WHEN (SELECT approved FROM friends_list WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND (user_id = user_detail.user_id OR friends_id = user_detail.user_id) AND approved=1 AND profile_type=2 LIMIT 1)=1 THEN 2 ELSE 3 END) WHERE user_detail.user_id IN (" . implode(',', $list) . ")";
        //$query = "SELECT user_id FROM user_detail WHERE user_detail.user_id IN (".implode(',',$list).")";
        //echo $query;
        $msql1 = $this->conn->query($query);
        $data = array();
        if ($msql1->num_rows > 0) {
            while ($row = $msql1->fetch_assoc()) {
                if (!empty($row['user_pic']) || $row['user_pic'] != '') {
                    $row['user_pic'] = $this->mutimedia_dir . $row['user_pic'];
                }
                $data[] = $row;
            }
        }
        return $data;
    }

    public function friends_list_datails_one($user_id, $profile_type) {
        $que = "SELECT DISTINCT user_id,friends_id FROM friends_list  JOIN users usa ON usa.id=user_id JOIN users usb ON usb.id =friends_id WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND approved=1 AND usa.activated=1 AND usa.deleted=0 AND usb.activated=1 AND usb.deleted=0 AND profile_type='$profile_type'";
        $list[] = $user_id;
        $msql = $this->conn->query($que);
        if ($msql->num_rows > 0) {
            while ($row = $msql->fetch_assoc()) {
                if (!in_array($row['friends_id'], $list)) {
                    $list[] = $row['friends_id'];
                }
                if (!in_array($row['user_id'], $list)) {
                    $list[] = $row['user_id'];
                }
            }
        }
        $list = array_diff($list, array($user_id));
        // $query = "SELECT user_detail.user_id AS id,name,profile_pic.profile_pic AS user_pic,(SELECT COUNT(DISTINCT friends_list.user_id) FROM friends_list WHERE (friends_list.user_id = user_detail.user_id OR friends_list.friends_id = user_detail.user_id) AND approved=1 AND profile_type='$profile_type' ) AS friends_count FROM user_detail JOIN friends_list ON friends_list.user_id=user_detail.user_id OR friends_list.friends_id=user_detail.user_id JOIN profile_pic ON user_detail.user_id = profile_pic.user_id AND profile_pic.profile_type='" . $profile_type . "' WHERE user_detail.user_id IN (" . implode(',', $list) . ")";
        //$query = "SELECT user_id FROM user_detail WHERE user_detail.user_id IN (".implode(',',$list).")";
        //echo $query;
        $query = "SELECT users.id AS id, user_detail.name AS name,(SELECT COUNT(DISTINCT friends_list.user_id) FROM friends_list WHERE (friends_list.user_id = users.id OR friends_list.friends_id = users.id) AND approved=1 AND profile_type='$profile_type' ) AS friends_count, profile_pic.profile_pic AS user_pic FROM users JOIN user_detail ON user_detail.user_id=users.id JOIN profile_pic ON users.id = profile_pic.user_id AND profile_pic.profile_type='" . $profile_type . "' WHERE users.id IN (".implode(',',$list).") AND users.activated=1";
        $msql1 = $this->conn->query($query);
        $data = array();
        if ($msql1->num_rows > 0) {
            while ($row = $msql1->fetch_assoc()) {
                if (!empty($row['user_pic']) || $row['user_pic'] != '') {
                    $row['user_pic'] = $this->mutimedia_dir . $row['user_pic'];
                }
                $data[] = $row;
            }
        }
        //print_r($data);die();
        return $data;
    }

    public function friends_lists($user_id, $profile_type) {
        $que = "SELECT DISTINCT user_id,friends_id FROM friends_list  JOIN users usa ON usa.id=user_id JOIN users usb ON usb.id =friends_id WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND approved=1 AND usa.activated=1 AND usa.deleted=0 AND usb.activated=1 AND usb.deleted=0 AND profile_type='" . $profile_type . "'";
        // die();
        $list[] = $user_id;
        $msql = $this->conn->query($que);
        if ($msql->num_rows > 0) {
            while ($row = $msql->fetch_assoc()) {
                if (!in_array($row['friends_id'], $list)) {
                    $list[] = $row['friends_id'];
                }
                if (!in_array($row['user_id'], $list)) {
                    $list[] = $row['user_id'];
                }
            }
        }
        return $list;
    }

    public function get_post_comment($post_id, $root) {
        $query = "SELECT comment.id,comment.post_id,ud.name as userName,pp.profile_pic AS path,comment.user_id,comment,mention_list,comment.date_time FROM comment LEFT JOIN profile_pic pp ON comment.user_id=pp.user_id LEFT JOIN user_detail ud ON comment.user_id=ud.user_id LEFT JOIN post ON post.id=comment.post_id  WHERE post_id='$post_id' AND root_id='$root' AND pp.profile_type=post.profile_type AND comment.deleted=0 AND post.deleted=0";
        //echo $query;
        if ($mysql = $this->conn->query($query)) {
            $rows = array();
            if ($mysql->num_rows > 0) {
                while ($row = $mysql->fetch_assoc()) {

                    $row['nested'] = self::get_post_comment($row['post_id'], $row['id']);
                    $row['mention_list'] = json_decode($row['mention_list']);
                    //$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
                    $row['path'] = $this->mutimedia_dir . $row['path'];
                    unset($row['post_id']);
                    $rows[] = $row;
                }
            }
            return $rows;
        } else {
            die('Database Query Faild: ' . $this->conn->error);
        }
    }

    public function get_post_like($post_id, $user_id) {
        $query = "SELECT post_like.user_id AS user_id, user_detail.name AS username, profile_pic.profile_pic AS userimage FROM post  LEFT JOIN post_like ON post.id=post_like.post_id JOIN user_detail ON post_like.user_id=user_detail.user_id INNER JOIN profile_pic ON profile_pic.user_id=post_like.user_id WHERE post.id='$post_id' AND profile_pic.profile_type=post.profile_type AND post_like.liked=1";
        //echo $query;
        if ($mysql = $this->conn->query($query)) {
            $rows = array();
            if ($mysql->num_rows > 0) {
                while ($row = $mysql->fetch_assoc()) {
                    //$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
                    $row['userimage'] = $this->mutimedia_dir . $row['userimage'];

                    $que = "SELECT id FROM friends_list WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND (user_id = '$row[user_id]' OR friends_id = '$row[user_id]') AND approved=1";
                    $msql = $this->conn->query($que);
                    $row['is_friend'] = 0;
                    if ($msql->num_rows > 0) {
                        $friend = $msql->fetch_all();
                        $row['is_friend'] = 1;
                    }
                    $rows[] = $row;
                }
            }
            return $rows;
        } else {
            die('Database Query Faild: ' . $this->conn->error);
        }
    }

    public function get_notification($user_id) {
        $data = array(
            'table' => 'notification',
            'select' => 'notification.id as id,notification.user_id AS user_id,notify_type,notification.date_time AS date_time',
            'join' => 'LEFT JOIN friends_list ON friends_list.user_id=notification.user_id',
            'where' => 'to_user_id=' . $user_id . ' AND friends_list.approved!=0'
        );
        $result = array(
            'status' => true,
            'data' => $db->get_all($data)
        );
    }

    public function sendPushNotification($fields) {
        $ios = self::iospushnotification($fields);
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = json_encode($fields);
        $headers = array(
            'Authorization: key=' . "AIzaSyCm9B6EWi96oL4fM6ULgO_QmgC5MKIdVrE",
            'Content-Type: application/json');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        $result = (array) json_decode($result);
        //echo $fields;
        //echo json_encode($result);die();
        curl_close($ch);

        //and return the result
        return $result;
    }
    public function iospushnotification($fields)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
         $fields['notification'] =  array('title' => 'Notification', 'body' => 'Recieved','sound' => 'default');
         
        if(isset($fields['data']['post_type']))
        {
            if(isset($fields['data']['post_type']) && $fields['data']['post_type'] !='')
            {
        $data = array(
          'table'=>'post_type',
          'where'=>"id=".$fields['data']['post_type'].""
        );
        $result = self::get_all($data);
        $post_type ='';
        if(isset($result['type']) && $result['type'] != '')
        $post_type = $result['type'];
        }
        }
        switch ($fields['data']['noti_type']) {
            case 'friend_request_receive':
                $fields['notification']['title'] = $fields['data']['user_name'];$fields['notification']['body'] = $fields['data']['user_name']." wants to add in your ".$fields['data']['profile_type']." network";
                break;
            case 'updated_status':
                $fields['notification']['title'] = $fields['data']['user_name'];$fields['notification']['body'] = $fields['data']['user_name']." added a new ".$post_type." in ".$fields['data']['profile_type']." network";
                break;
            case 'friend_request_reject':
                $fields['notification']['title'] = $fields['data']['user_name'];$fields['notification']['body'] = $fields['data']['user_name']." reject friend request in ".$fields['data']['profile_type']." network";
                break;
            case 'share_location':
                $fields['notification']['title'] = $fields['data']['user_name'];$fields['notification']['body'] = $fields['data']['user_name']." share location in ".$fields['data']['profile_type']." network";
                break;
            case 'like_post':
                $fields['notification']['title'] = $fields['data']['user_name'];$fields['notification']['body'] = $fields['data']['user_name']." like in ".$fields['data']['profile_type']." network";
                break;
            case 'comment_post':
                $fields['notification']['title'] = $fields['data']['user_name'];$fields['notification']['body'] = $fields['data']['user_name']." commented ".$fields['data']['profile_type']." network";
                break;
            case 'mention_comment':
                $fields['notification']['title'] = $fields['data']['user_name'];$fields['notification']['body'] = $fields['data']['user_name']." mentioned you ".$fields['data']['profile_type']." network";
                break;
            case 'profile_pic_updated':
                $fields['notification']['title'] = $fields['data']['user_name'];$fields['notification']['body'] = $fields['data']['user_name']." updated ".$fields['data']['profile_type']." network";
                break;
            default:
                //$fields['notification']['title'] = $fields['data']['user_name'];$fields['notification']['body'] = $fields['data']['user_name']." ";
               $fields['notification']['title'] =$fields['data']['sender_name']; $fields['notification']['body'] ='';
               $fields['notification']['subtitle'] ="Invites you to join ". $fields['data']['event_title']." event. ";$fields['notification']['category'] = $fields['data']['noti_type'];
                $fields['notification']['content-available'] = 1;
                 $fields['notification']['mutable-content'] = 1;
                
                 //$fields['aps']['title'] ='test by iphone';$fields['aps']['body'] = $fields['data']['user_name']." ";$fields['aps']['category'] = "INVITATION";
                //$fields['aps']['content-available'] = 1;
                //$fields['aps']['mutable-content'] = 1;
                 
                
                  
                break;
        }
        $fields = json_encode($fields);
        
        $querylogs = "logs/notificationiphone_logs_".date('Ymd').".txt";
		  error_log($fields, 3,  $querylogs);
         
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
        //echo $fields;
        //echo json_encode($result);die();
        curl_close($ch);
        


//exit;
        //and return the result
        return $result;
    }
    public function send_msg_international($country_code,$mobile, $msg) {
         $url = "http://int.itrlabs.com/api/sendhttp.php?authkey=189826AX7sUbxD55a421aa6&mobiles=".$mobile."&message=".$msg."&sender=Vispot&route=4&country=".$country_code;
        return $sms_send = file_get_contents($url); 
    }

    public function send_msg_national($mobile, $msg) {
        $url = "http://trans.itrlabs.com/api/mt/SendSMS?user=shinkita&password=123456&senderid=Vispot&channel=Trans&DCS=0&flashsms=0&number=$mobile&text=$msg&route=27";
        return $sms_send = file_get_contents($url);
        ;
    }

    public function int_msg_send_Request($param) {
        $url = $param['url'];
        $postData = $param['postData'];

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
                //,CURLOPT_FOLLOWLOCATION => true
        ));


        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        //get response
        $output = curl_exec($ch);

        //Print error if any
        if (curl_errno($ch)) {
            return curl_error($ch);
        }

        curl_close($ch);

        return $output;
    }

    public function send_mail($user_mail, $email) {
        require_once __DIR__ . '/PHPMailer/src/Exception.php';
        require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/PHPMailer/src/SMTP.php';
        $mail = new PHPMailer(true);
        try {
            //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'viaspot.com;viaspot.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'welcome@viaspot.com';                 // SMTP username
            $mail->Password = 'writeus@viaspot';                           // SMTP password
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                                    // TCP port to connect to
            //Recipients
            $mail->setFrom('welcome@viaspot.com', 'Viaspot.com');
            $mail->addAddress($user_mail);     // Add a recipient
            //$mail->addAddress();               // Name is optional
            $mail->addReplyTo('welcome@viaspot.com', '');

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $email['subject'];
            $mail->Body = $email['body'];
            $mail->AltBody = $email['altbody'];

            $mail->send();
            return $result = "Message successfully sent!";
        } catch (Exception $e) {
            //echo $e;
            return $result = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
        }
    }

    public function get_user_data_id($id) {
        try {
            $data_user = array(
                'table' => 'user_detail',
                'select' => 'user_detail.user_id AS id,user_detail.interest AS interest_id ,user_detail.name AS name,(SELECT profile_pic FROM profile_pic WHERE profile_type=1 AND user_id=' . $id . ') AS friend_pic,(SELECT profile_pic FROM profile_pic WHERE profile_type=2 AND user_id=' . $id . ') AS faimily_pic,(SELECT profile_pic FROM profile_pic WHERE profile_type=3 AND user_id=' . $id . ') AS pro_pic',
                'where' => 'user_id=' . $id . ''
            );
            if ($u_detail = self::get_row($data_user)) {
                if ($u_detail['friend_pic'] != '' && !empty($u_detail['friend_pic'])) {
                    $u_detail['friend_pic'] = $this->mutimedia_dir . $u_detail['friend_pic'];
                }
                if ($u_detail['faimily_pic'] != '' && !empty($u_detail['faimily_pic'])) {
                    $u_detail['faimily_pic'] = $this->mutimedia_dir . $u_detail['faimily_pic'];
                }
                if ($u_detail['pro_pic'] != '' && !empty($u_detail['pro_pic'])) {
                    $u_detail['pro_pic'] = $this->mutimedia_dir . $u_detail['pro_pic'];
                }
                $result = array(
                    'status' => true,
                    'id' => $id,
                    'name' => $u_detail['name'],
                    'friend_pic' => $u_detail['friend_pic'],
                    'faimily_pic' => $u_detail['faimily_pic'],
                    'pro_pic' => $u_detail['pro_pic'],
					'interest_id' => $u_detail['interest_id'],
                );
                return $result;
            } else {
                throw new Exception("Database query failed");
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
    function getRealIpAddr()
    {
        if(!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    function upload_image($base64)
    {
        try{
            $image_name = md5(time().uniqid()).'.jpeg';
            $imageStr = base64_decode($base64);
            $image = imagecreatefromstring($imageStr);
            if($image  !== false)
            {
              header('Content-Type: image/jpeg');
              imagejpeg($image, __DIR__.DIRECTORY_SEPARATOR.$this->upload_dir.DIRECTORY_SEPARATOR.$image_name);
              imagedestroy($image);
              return $image_name;
            }
            else
            {
                throw new Exception("Image upload failed");
            }
        }
        catch(Exception $ex){
            $result = array(
                'status' => false,
                'msg' => $ex->getMessage()
            );
            echo json_encode($result);
            die();
        }

    }

    function post_notifiation($user_id,$profile_type,$inserted_id,$post_type,$share = false)
    {
        try{
            $query = "SELECT users.id,user_detail.name,profile_type.id AS profile_id, profile_type.profile AS profile, profile_pic.profile_pic FROM users JOIN user_detail ON users.id=user_detail.user_id JOIN profile_pic ON profile_pic.user_id=users.id JOIN profile_type ON profile_type.id=profile_pic.profile_type WHERE users.id=? AND profile_pic.profile_type=?";
            if(!($stmt = $this->conn->prepare($query)))
            {
                throw new Exception("Select prepare Error: (" . $this->conn->errno . ") " . $this->conn->error);
            }
            if(!$stmt->bind_param("ii",$user_id,$profile_type))
            {
                throw new Exception("Select binding Error : (" . $stmt->errno . ") " . $stmt->error);
            }
            if(!$stmt->execute())
            {
                throw new Exception("Select execute Error: (" . $stmt->errno . ") " . $stmt->error);
            }
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            $lists = self::friends_lists($user_id,$profile_type);
            $notify_type = 'updated_status';
            $date = $this->currentDate->format('Y-m-d H:i:s');
            foreach ($lists as $key => $value) {
                $query = "INSERT INTO `notification`(`user_id`,`post_id`,`to_user_id`,`profile_type`,`notify_type`,date_time) VALUES(?,?,?,?,?,?)";
                if(!($stmt = $this->conn->prepare($query)))
                {
                    throw new Exception("Notification prepare Error: (" . $this->conn->errno . ") " . $this->conn->error);
                }
                if(!$stmt->bind_param("iiiiss",$user_id,$inserted_id,$value,$profile_type,$notify_type,$date))
                {
                    throw new Exception("Notification binding Error : (" . $stmt->errno . ") " . $stmt->error);
                }
                if(!$stmt->execute())
                {
                    throw new Exception("Notification execute Error: (" . $stmt->errno . ") " . $stmt->error);
                }
                $fields = array (
                  'to'=>"/topics/via".$value,
                  'data' => array (
                    'id'=>$stmt->insert_id,
                    "noti_type"=>$notify_type,
                    "user_id"=>$user_id,
                    "user_name"=>$user['name'],
                    'post_id'=>$inserted_id,
                    'post_type'=>$post_type,
                    "profile_type_id"=>$user['profile_id'],
                    "profile_type"=>$user['profile'],
                    'user_pic'=>$this->mutimedia_dir.$user['profile_pic'],
                    'share'=>$share,
                )
              );
                // print_r($fields);
                $stat = self::sendPushNotification($fields);
            }
        }
        catch(Exception $ex){
            $result = array(
                'status' => false,
                'msg' => $ex->getMessage()
            );
            echo json_encode($result);
        }
    }
    function profile_pic_updated($user_id,$profile_id,$noti_type)
    {
        try{
            $notify_type = $noti_type;
            $profile_type = $profile_id;
            $inserted_id = 0;
            $query = "SELECT users.id,user_detail.name,user_detail.gender,profile_type.id AS profile_id, profile_type.profile AS profile, profile_pic.profile_pic FROM users JOIN user_detail ON users.id=user_detail.user_id JOIN profile_pic ON profile_pic.user_id=users.id JOIN profile_type ON profile_type.id=profile_pic.profile_type WHERE users.id=? AND profile_pic.profile_type=?";
            if(!($stmt = $this->conn->prepare($query)))
            {
                throw new Exception("Select prepare Error: (" . $this->conn->errno . ") " . $this->conn->error);
            }
            if(!$stmt->bind_param("ii",$user_id,$profile_type))
            {
                throw new Exception("Select binding Error : (" . $stmt->errno . ") " . $stmt->error);
            }
            if(!$stmt->execute())
            {
                throw new Exception("Select execute Error: (" . $stmt->errno . ") " . $stmt->error);
            }
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $lists = self::friends_lists($user_id,$profile_type);
             $lists = array_diff($lists, array($user_id));
            foreach ($lists as $key => $value) {
                $query = "INSERT INTO `notification`(`user_id`,`post_id`,`to_user_id`,`profile_type`,`notify_type`) VALUES(?,?,?,?,?)";
                if(!($stmt = $this->conn->prepare($query)))
                {
                    throw new Exception("Notification prepare Error: (" . $this->conn->errno . ") " . $this->conn->error);
                }
                if(!$stmt->bind_param("iiiis",$user_id,$inserted_id,$value,$profile_type,$notify_type))
                {
                    throw new Exception("Notification binding Error : (" . $stmt->errno . ") " . $stmt->error);
                }
                if(!$stmt->execute())
                {
                    throw new Exception("Notification execute Error: (" . $stmt->errno . ") " . $stmt->error);
                }
                $fields = array (
                  'to'=>"/topics/via".$value,
                  'data' => array (
                    'id'=>$stmt->insert_id,
                    "noti_type"=>$notify_type,
                    "user_id"=>$user_id,
                    "user_name"=>$user['name'],
                    "gender"=>$user['gender'],
                    "profile_type_id"=>$user['profile_id'],
                    "profile_type"=>$user['profile'],
                    'user_pic'=>$this->mutimedia_dir.$user['profile_pic'],
                )
              );
                $stat = self::sendPushNotification($fields);
            }
        }
        catch(Exception $ex){
            $result = array(
                'status' => false,
                'msg' => $ex->getMessage()
            );
            echo json_encode($result);
        }
    }
    function __destruct()
    {
        $this->conn->close();
        $this->conn = null;
    }
}

?>
