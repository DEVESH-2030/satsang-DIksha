<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can signup API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});
Route::post('signin', 'API\UserController@signin'); 	 	// -----   Sign In user
Route::post('signup', 'API\UserController@signup');	 		// -----   Sign Up User
Route::group(['middleware' => 'auth:api'], function(){
Route::post('details', 'API\UserController@details');    	// -  Get the all details about User 
});

Route::post('uploadFilesAndImages', 'API\UserController@uploadFilesAndImages');	//----  use to upload files
Route::get('get-locations','API\UserController@location');	//----  use to choose/get the location  

Route::post('sendOtpforSignUpandRegister', 'API\UserController@sendOtpforSignUpandRegister');		//-- send OTP on mobile No		  
Route::post('sendSms', 'API\UserController@sendSms')->name('mobile');  // -- send SMS
Route::post('submitOtpAndVerificationforRegister','API\UserController@submitOtpAndVerificationforRegister');  //---- Submit And Verify OTP
Route::post('otpGenerateByMobilenumber', 'API\UserController@otpGenerateByMobilenumber');		
Route::post('forgotPasswordandOtpgenerate', 'API\UserController@forgotPasswordandOtpgenerate');  //-- route use for forgot Password
Route::post('userlogin', 'API\UserController@userlogin');
Route::post('optVerificationforResetAndChangePassword', 'API\UserController@optVerificationforResetAndChangePassword');
