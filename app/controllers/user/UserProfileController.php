<?php

class UserProfileController 
{


  public function getDefaultAvatars()
  {

    $imgPool = Database::search("SELECT * FROM `image_pool`");
    $imgPool_num = $imgPool->num_rows;

    $imgArray = array();

    for($i = 0; $i < $imgPool_num; $i++){

      $obj = new stdClass();

      $data = $imgPool->fetch_assoc();

      $obj->url = $data['image_url'];
      $obj->name = $data['image_name'];

      array_push($imgArray, $obj);
    }

    echo json_encode($imgArray);

  }



}


?>