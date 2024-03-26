<?php

include __DIR__ . '/../../../database/connection.php';

class UserController
{

  public function registerUser($request)
  {

    if ($request !== null) {

      $fname =  $request["first_name"];
      $lname = $request["last_name"];
      $username = $request["username"];
      $email = $request["email"];
      $password = $request["password"];
      $gender_id = $request["gender_id"];

      $tableName = "user";
      $searchColumns = ['first_name', 'last_name', 'username', 'email', 'password', 'gender_id'];

      if (Database::validatePostData($tableName, $request, $searchColumns)) {

        $checkUsername = Database::search("SELECT * FROM `user` WHERE `username`='" . $username . "'");
        $check_num = $checkUsername->num_rows;

        if (!strlen($fname) > 45) {
          echo ("First Name Must Contain LOWER THAN 45 characters.");
        } else if (!strlen($lname) > 45) {
          echo ("Last Name Must Contain LOWER THAN 45 characters.");
        } else if (!strlen($username) > 50) {
          echo ("Username Must Contain LOWER THAN 45 characters.");
        } else if ($check_num > 0) {
          echo ("Username Already Exists.");
        } else if (!strlen($email) > 100) {
          echo ("Email Address must Contain LOWER THAN 100 characters.");
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          echo ("Invalid Email Address.");
        } else if (strlen($password) < 5 || strlen($password) > 20) {
          echo ("Password Must Contain 5 to 20 Characters.");
        } else {

          $result = Database::search("SELECT * FROM `user` WHERE `email`='" . $email . "' OR `username`='" . $username . "'");
          $rows = $result->num_rows;

          if ($rows > 0) {
            echo ("User with the smae Email Address or same Username already exists.");
          } else {

            $d = new DateTime();
            $tz = new DateTimeZone("Asia/Colombo");
            $d->setTimezone($tz);
            $date = $d->format("Y-m-d H:i:s");

            Database::iud("INSERT INTO `user`
            (`first_name`,`last_name`,`username`,`email`,`password`,`created_at`,`gender_id`,`status_id`) 
            VALUES('" . $fname . "','" . $lname . "','" . $username . "','" . $email . "','" . $password . "','" . $date . "','" . $gender_id . "','1')");

            echo ("success");
          }
        }
      } else {
        echo "Error: Invalid POST data";
      }
    } else {
      echo "Request faild: Null Data Object";
    }
  }

  public function login($request)
  {

    if ($request !== null) {

      $email = $request["email"];
      $password = $request["password"];
      $rememberme = $request["rememberme"];

      $tableName = "user";
      $searchColumns = ['email', 'password'];

      if (Database::validatePostData($tableName, $request, $searchColumns)) {

        $obj = new stdClass();


        if (!strlen($email) > 100) {
          $obj->msg = "Email Address must Contain LOWER THAN 100 characters.";
          echo json_encode($obj);
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $obj->msg = "Invalid Email Address.";
          echo json_encode($obj);
        } else if (strlen($password) < 5 || strlen($password) > 20) {

          $obj->msg = "Password Must Contain 5 to 20 Characters.";
          echo json_encode($obj);
        } else {

          $rs = Database::search("SELECT * FROM `user` WHERE `email`='" . $email . "' AND `password`='" . $password . "'");
          $row = $rs->num_rows;

          if ($row == 1) {

            $data = $rs->fetch_assoc();



            if ($rememberme == "true") {
              $obj->msg = "success";
              $obj->rememberme = "true";
              $obj->user = $data;
              echo json_encode($obj);
            } else if ($rememberme == "remember") {
              $obj->msg = "success";
              $obj->rememberme = "remember";
              echo json_encode($obj);
            } else {
              $obj->msg = "success";
              $obj->rememberme = "false";
              $obj->user = $data;
              echo json_encode($obj);
            }
          } else {
            $obj->msg = "Invalid Email or Password";
            echo json_encode($obj);
          }
        }
      } else {
        echo "Error: Invalid POST data";
      }
    } else {
      echo "Request faild: Null Data Object";
    }
  }

  public function saveOTP($request)
  {
    if ($request !== null) {

      $email = $request["email"];
      $otp = $request["verify_code"];

      $tableName = "user";
      $searchColumns = ['email', 'verify_code'];


      if (Database::validatePostData($tableName, $request, $searchColumns)) {

        $obj = new stdClass();

        if (!strlen($email) > 100) {
          $obj->msg = "Email Address must Contain LOWER THAN 100 characters.";
          echo json_encode($obj);
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $obj->msg = "Invalid Email Address.";
          echo json_encode($obj);
        } else if (!strlen($otp) === 6) {
          $obj->msg = "Invalid OTP.";
          echo json_encode($obj);
        } else {

          $rs = Database::search("SELECT * FROM `user` WHERE `email`='" . $email . "'");
          $row = $rs->num_rows;

          if ($row == 1) {

            Database::iud("UPDATE `user` SET `verify_code`='" . $otp . "'");
            $obj->msg = "success";
            $obj->otp = $otp;
            echo json_encode($obj);
          } else {
            $obj->msg = "No user found with this email address.";
            echo json_encode($obj);
          }
        }
      } else {
        echo "Error: Invalid POST data";
      }
    } else {
      echo "Request faild: Null Data Object";
    }
  }



  public function checkOTP($request)
  {
    if ($request !== null) {

      $email = $request["email"];
      $otp = $request["verify_code"];


      $tableName = "user";
      $searchColumns = ['email', 'verify_code'];


      if (Database::validatePostData($tableName, $request, $searchColumns)) {

        $obj = new stdClass();

        if (!strlen($email) > 100) {
          $obj->msg = "Email Address must Contain LOWER THAN 100 characters.";
          echo json_encode($obj);
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $obj->msg = "Invalid Email Address.";
          echo json_encode($obj);
        } else if (!strlen($otp) === 6) {
          $obj->msg = "Invalid OTP.";
          echo json_encode($obj);
        } else {

          $rs = Database::search("SELECT * FROM `user` WHERE `email`='" . $email . "'");
          $row = $rs->num_rows;

          if ($row == 1) {

            $userData = $rs->fetch_assoc();

            if ($userData["verify_code"] === $otp) {

              $obj->msg = "success";
              echo json_encode($obj);

            } else {
              $obj->msg = "Invalid OTP";
              echo json_encode($obj);
            }

          } else {
            $obj->msg = "No user found with this email address.";
            echo json_encode($obj);
          }
        }
      } else {
        echo "Error: Invalid POST data";
      }
    } else {
      echo "Request faild: Null Data Object";
    }
  }


  public function changePassword($request)
  {

    if ($request !== null) {

      $email = $request["email"];
      $password = $request["password"];

      $tableName = "user";
      $searchColumns = ['email', 'password'];


      if (Database::validatePostData($tableName, $request, $searchColumns)) {

        $obj = new stdClass();

        if (!strlen($email) > 100) {
          $obj->msg = "Email Address must Contain LOWER THAN 100 characters.";
          echo json_encode($obj);
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $obj->msg = "Invalid Email Address.";
          echo json_encode($obj);
        }else if (strlen($password) < 5 || strlen($password) > 20) {
          $obj->msg = "Password Must Contain 5 to 20 Characters.";
          echo json_encode($obj);
        } else {

          $rs = Database::search("SELECT * FROM `user` WHERE `email`='" . $email . "'");
          $row = $rs->num_rows;

          if ($row == 1) {

            Database::iud("UPDATE `user` SET `password`='" . $password . "'");
            $obj->msg = "success";
            echo json_encode($obj);

          } else {
            $obj->msg = "No user found with this email address.";
            echo json_encode($obj);
          }

        }

      }else{
        echo "Error: Invalid POST data";
      }

    } else {
      echo "Request faild: Null Data Object";
    }

  }


  public function getUser()
  {
    $res = Database::search("SELECT * FROM `admin` WHERE `username` ='eondave' ");
    $res_num = $res->num_rows;

    if ($res_num == 1) {
      $user = $res->fetch_assoc();
      echo "Hello ";
      echo ($user["username"]);
    }
  }
}
