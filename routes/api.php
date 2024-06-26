<?php

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include "route.php";
include "../app/controllers/user/UserController.php";
include "../app/controllers/user/UserProfileController.php";


// Define routes ------------------------------>>

//http://localhost/api.swapfy/routes/api/login
Route::post('/api/login', [UserController::class, 'login']);

//http://localhost/api.swapfy/routes/api/register
Route::post('/api/register', [UserController::class, 'registerUser']);

//http://localhost/api.swapfy/routes/api/verify
Route::post('/api/verify', [UserController::class, 'saveOTP']);

//http://localhost/api.swapfy/routes/api/check-otp
Route::post('/api/check-otp', [UserController::class, 'checkOTP']);

//http://localhost/api.swapfy/routes/api/forgot-password
Route::post('/api/forgot-password', [UserController::class, 'forgotPassword']);

//http://localhost/api.swapfy/routes/api/change-password
Route::post('/api/change-password', [UserController::class, 'changePassword']);

//http://localhost/api.swapfy/routes/api/change-email
Route::post('/api/change-email', [UserController::class, 'changeEmail']);

//http://localhost/api.swapfy/routes/api/default-avatars
Route::get('/api/default-avatars', [UserProfileController::class, 'getDefaultAvatars']);





// Handle the current request --------------------------------->>
$requestURL = Request::getRequest();
$requestType = Request::getRequestType();
Route::handle($requestURL, $requestType);

?>
