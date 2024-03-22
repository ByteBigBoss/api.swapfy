<?php

class Request {

  public static function getRequest() {
    $basePath = '/api.swapfy/routes';
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = rtrim(str_replace($basePath, '', $uri), '/');
    return $path;
}
  
  public static function getRequestType() {
      return $_SERVER['REQUEST_METHOD'];
  }

  public static function redirect($location) {
    header('Location: '.$location);
    exit();
  }

  }

?>