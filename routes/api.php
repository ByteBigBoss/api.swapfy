<?php

include "route.php";
include "../app/controllers/user/UserController.php";


// Define routes
Route::get('/api', [UserController::class, 'registerUser']);

Route::get('/api/login', [UserController::class, 'login']);
Route::get('/api/getuser', [UserController::class, 'getUser']);




// Handle the current request
$requestURL = Request::getRequest();
$requestType = Request::getRequestType();
Route::handle($requestURL, $requestType);

?>
