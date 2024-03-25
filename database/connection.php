<?php

class Database{

  public static $connection;

  public static function setUpConnection(){
    if(!isset(Database::$connection)){
      Database::$connection = new mysqli("localhost","root","Ms2005j@Neru","swapfy","3306");
    }
  }

  public static function iud($q){
      Database::setUpConnection();
      Database::$connection->query($q);
  }

  public static function search($q){
      Database::setUpConnection();
      $resultset = Database::$connection->query($q);
      return $resultset;
  }

  public static function validatePostData($tableName, $postData, $searchColumns){

    $searchColumnsString = "'" . implode("','", $searchColumns) . "'";

    $result = self::search("SHOW COLUMNS FROM `".$tableName."` WHERE `Field` IN (".$searchColumnsString.")");
    $columns = [];
    while($row = $result->fetch_assoc()){
      $columns[] = $row['Field'];
    }
    

    foreach ($columns as $column){
      if(!isset($postData[$column]) || empty($postData[$column])){
        return false;
      }
    }
    return true;

  }

}

?>