<?php
$ip = "184.105.178.226"; // the IP address to query
$query = @unserialize(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
print_r($query);
?>