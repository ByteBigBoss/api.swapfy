<?php

include "../../../database/connection.php";

$res = Database::search("SELECT * FROM `admin` WHERE `username` ='eondave' ");
$res_num = $res->num_rows;

if($res_num == 1){
  $user = $res->fetch_assoc();
  echo "Hello ";
  echo($user["username"]);
}



?>