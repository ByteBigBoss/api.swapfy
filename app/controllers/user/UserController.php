<?php

include __DIR__ . '/../../../database/connection.php';

  class UserController{

    public function registerUser(){
      echo "hello";
    }

    public function login(){
      echo "Login User...";
    }

    public function getUser(){
      $res = Database::search("SELECT * FROM `admin` WHERE `username` ='eondave' ");
      $res_num = $res->num_rows;
      
      if($res_num == 1){
        $user = $res->fetch_assoc();
        echo "Hello ";
        echo($user["username"]);
      }
    }

  }

?>