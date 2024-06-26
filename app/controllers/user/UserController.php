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

            $cookie = Database::search("SELECT * FROM `cookies` WHERE `user_id` IN (SELECT `user_id` FROM `user` WHERE `email`='" . $email . "')");
            $cookie_row = $cookie->num_rows;


            // Get current datetime
            $currentDatetime = date('Y-m-d H:i:s');



            if ($rememberme == "true") {

              // Add 30 days to the current datetime
              $expireDatetime = date('Y-m-d H:i:s', strtotime('+30 days'));

              if ($cookie_row == 1) {
                Database::iud("UPDATE `cookies` SET `expiration_date` = '" . $expireDatetime . "'");
              } else {
                Database::iud("INSERT INTO `cookies`(`user_id`,`expiration_date`,`created_at`) VALUES('" . $data["user_id"] . "', '" . $expireDatetime . "', '" . $currentDatetime . "')");
              }

              $obj->msg = "success";
              $obj->rememberme = "true";
              $obj->user = $data;
              echo json_encode($obj);
            } else if ($rememberme == "remember") {
              $obj->msg = "success";
              $obj->rememberme = "remember";
              echo json_encode($obj);
            } else {

              $expireDatetime = date('Y-m-d H:i:s', strtotime('+1 days'));

              if ($cookie_row == 1) {
                Database::iud("UPDATE `cookies` SET `expiration_date` = '" . $expireDatetime . "'");
              } else {
                Database::iud("INSERT INTO `cookies`(`user_id`,`expiration_date`,`created_at`) VALUES('" . $data["user_id"] . "', '" . $expireDatetime . "', '" . $currentDatetime . "')");
              }

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


  public function forgotPassword($request)
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
        } else if (strlen($password) < 5 || strlen($password) > 20) {
          $obj->msg = "Password Must Contain 5 to 20 Characters.";
          echo json_encode($obj);
        } else {

          $rs = Database::search("SELECT * FROM `user` WHERE `email`='" . $email . "'");
          $row = $rs->num_rows;

          if ($row == 1) {

            Database::iud("UPDATE `user` SET `password`='" . $password . "' WHERE `email`='" . $email . "' ");
            $obj->msg = "success";
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


  public function changePassword($request)
  {

    if ($request !== null) {

      $email = $request["email"];
      $currentPassword = $request["currentPassword"];
      $newPassword = $request["newPassword"];
      $username = $request["username"];

      $obj = new stdClass();

      if (!empty($email) && !empty($username)) {

        $rs = Database::search("SELECT * FROM `user` WHERE `email` = '" . $email . "' AND `username`='" . $username . "'");
        $row = $rs->num_rows;

        if ($row == 1) {

          $user = $rs->fetch_assoc();

          if ($user["password"] === $currentPassword) {

            Database::iud("UPDATE `user` SET `password`='" . $newPassword . "' WHERE `email`='" . $email . "' AND `username` = '" . $username . "'");

            $cookie = Database::search("SELECT * FROM `cookies` WHERE `user_id` IN (SELECT `user_id` FROM `user` WHERE `email`='" . $email . "')");
            $cookie_row = $cookie->num_rows;


            // Get current datetime
            $currentDatetime = date('Y-m-d H:i:s');



            if ($cookie_row == 1) {

              $data = $cookie->fetch_assoc();

              $expireDatetime = $data["expiration_date"];

              // Calculate the difference in seconds between the expire datetime and the current datetime
              $difference = strtotime($expireDatetime) - strtotime($currentDatetime);

              // Convert the difference to days
              $daysLeft = floor($difference / (60 * 60 * 24)); // Convert seconds to days


              $obj->msg = "success";
              $obj->password = $newPassword;
              $obj->expire = $daysLeft;
              echo json_encode($obj);
            } else {
              $obj->msg = "success";
              $obj->password = $newPassword;
              $obj->expire = 2;
              echo json_encode($obj);
            }
          } else {
            $obj->msg = "Wrong Password, please try again.";
            echo json_encode($obj);
          }
        } else {
          $obj->msg = "No user found with this email address.";
          echo json_encode($obj);
        }
      } else {
        $obj->msg = "Request currepted, Please try again later.";
        echo json_encode($obj);
      }
    } else {
      echo "Request faild: Null Data Object";
    }
  }

  public function changeEmail($request)
  {

    if ($request !== null) {

      $email = $request["email"];
      $newEmail = $request["newEmail"];
      $username = $request["username"];

      $obj = new stdClass();

      if (!empty($email) && !empty($newEmail) && !empty($username)) {

        if (!strlen($newEmail) > 100) {
          $obj->msg = "Email Address must Contain LOWER THAN 100 characters.";
          echo json_encode($obj);
        } else if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
          $obj->msg = "Invalid Email Address.";
          echo json_encode($obj);
        } else {

          $rs = Database::search("SELECT * FROM `user` WHERE `email` = '" . $email . "' AND `username`='" . $username . "'");
          $row = $rs->num_rows;

          if ($row == 1) {

            $user = $rs->fetch_assoc();

            if ($user["email"] === $email) {

              Database::iud("UPDATE `user` SET `email`='" . $newEmail . "' WHERE `email`='" . $email . "' AND `username` = '" . $username . "'");

              $cookie = Database::search("SELECT * FROM `cookies` WHERE `user_id` IN (SELECT `user_id` FROM `user` WHERE `username`='" . $username . "')");
              $cookie_row = $cookie->num_rows;


              // Get current datetime
              $currentDatetime = date('Y-m-d H:i:s');

              if ($cookie_row == 1) {

                $data = $cookie->fetch_assoc();

                $expireDatetime = $data["expiration_date"];

                // Calculate the difference in seconds between the expire datetime and the current datetime
                $difference = strtotime($expireDatetime) - strtotime($currentDatetime);

                // Convert the difference to days
                $daysLeft = floor($difference / (60 * 60 * 24)); // Convert seconds to days


                $obj->msg = "success";
                $obj->email = $newEmail;
                $obj->expire = $daysLeft;
                echo json_encode($obj);
              } else {
                $obj->msg = "success";
                $obj->email = $newEmail;
                $obj->expire = 2;
                echo json_encode($obj);
              }
            } else {
              $obj->msg = "Wrong email, please try again.";
              echo json_encode($obj);
            }
          } else {
            $obj->msg = "No user found with this email address.";
            echo json_encode($obj);
          }
        }
      } else {
        $obj->msg = "Request currepted, Please try again later.";
        echo json_encode($obj);
      }
    } else {
      echo "Request faild: Null Data Object";
    }
  }


  // CLASS END------------>>
}
