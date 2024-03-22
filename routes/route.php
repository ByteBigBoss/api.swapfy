<?php
include "http.php";

class Route {
  
  private static $routes = [
    'GET' => [],
    'POST' => [],
    'PUT' => [],
    'DELETE' => [],
    // Add more request types as needed
  ];
  
  public static function get($url, $callback) {
    self::$routes['GET'][$url] = $callback;
  }

  public static function post($url, $callback) {
    self::$routes['POST'][$url] = $callback;
  }

  public static function put($url, $callback) {
    self::$routes['PUT'][$url] = $callback;
  }

  public static function delete($url, $callback) {
    self::$routes['DELETE'][$url] = $callback;
  }

  public static function handle($url, $requestType) {
    if (isset(self::$routes[$requestType][$url])) {
        $callback = self::$routes[$requestType][$url];

        // Check if the callback is a valid array
        if (is_array($callback) && count($callback) === 2) {
            $class = $callback[0];
            $method = $callback[1];

            // Instantiate the class if it's not already instantiated
            if (is_string($class)) {
                $classInstance = new $class();
            } else {
                $classInstance = $class;
            }

            // Check if the method exists in the class
            if (method_exists($classInstance, $method)) {
                // Call the method
                try {
                    $classInstance->$method();
                } catch (Exception $e) {
                    // Print out any errors
                    echo "Error executing callback: " . $e->getMessage();
                }
            } else {
                // Method does not exist
                echo "500 Internal Server Error: Method not found";
            }
        } else {
            // Invalid callback array structure
            echo "500 Internal Server Error: Invalid callback";
        }
    } else {
        // Handle route not found
        echo "404 Not Found";
    }
}


  
}

?>
