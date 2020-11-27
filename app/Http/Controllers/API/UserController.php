<?php

namespace App\Http\Controllers\API;

use DB;
use Image;
use App\User; 
use Validator;
use App\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{

            public $successStatus = 200;


    /*-------------- 'Done'   API for FILE UPLOAD        ---------------*/
    public function uploadFilesAndImages(Request $request)
    {   
        $validator = Validator::make($request->all(),[
            'image'     => 'required|file:users',
        ]);
        if($validator->fails()){
            return response()->json([
                'response_code'     => 401,
                'error'             => $validator->errors(), 
            ]);
        }
        if($request->hasFile('image'))     
           {
                $image_tmp =Input::file('image');
                if($image_tmp->isValid())
                {
                    $extension =$image_tmp->getClientOriginalExtension();
                    $filename =rand(111,99999).'.'.$extension;  
                    $id_path = 'file/user'; 
                    //-------  store files ---------- //
                    $request->image->move(public_path($id_path), $filename); 
                    $upload = $id_path.'/'.$filename;   
                    $user->image=$filename;
                    // $success['Token Password'] =  $user->createToken('MyApp')-> accessToken; 
                    $user->save();
                }
                    $user->image=$filename;
                    $user->save();
                    return response()->json(['Upload & Save Your File / Image'=>$upload ], $this-> successStatus); 
            }                  
    }
    /*--------------   END API File Upload       ---------------*/


    /*--------- 'Done'    API FOR SIGN UP / USER REGISTER      ------------*/
    
    public function signup(Request $request) 
    { 
        $validator = Validator::make($request->all(), 
            [ 
                'name'          => 'required', 
                'email'         => 'required|email', 
                'password'      => 'required', 
                'c_password'    => 'required|same:password', 
                'mobile'        => 'required|unique:users|min:10|numeric',
                'image'         => 'required|file:users',
                // 'location'   => 'required'
            ]);
        // ------    use Validation  -
        if($validator->fails())
            { 
                return response()->json(['error'=>$validator->errors()], 401);            
            }
                $data = $request->all();
                $user = new User;
                $user->name=$data['name'];
                $user->email=$data['email'];
                $user->password=$data['password'];
                $user->c_password=$data['c_password'];
                $user->mobile=$data['mobile'];
                // $user->location=$data['location_id'];

                    if($request->hasFile('image'))     // -------    Uploade_files is use to check file name into postman--
                    {
                        $image_tmp =Input::file('image'); // upload Image in database  ----
                        if($image_tmp->isValid())
                        {
                            $extension =$image_tmp->getClientOriginalExtension();
                            $filename =rand(111,99999).'.'.$extension;   //-- Create Name & Extension when upload any file 
                            $id_path = 'file/user';  // --- Get the file path to Store files  ---- //
                            //-------  store files ---------- //
                            $request->image->move(public_path($id_path), $filename);  // -- Move the file when upload complete
                            $user1 = $id_path.'/'.$filename;   // ---  Save Data / file into User folder as-" file & filename" --
                        }
                    } 
                $input = $request->all();            
                $user->image=$filename;
                $success['Token Password'] =  $user->createToken('MyApp')-> accessToken; 
                $user->save();
                $input['password'] = bcrypt($input['password']);  //  ----- Verify only Confirm Passwpord  ---

                return response()->json(
                    [
                        'Token Generate Successfully'                =>$success ,
                        'result'                                     =>$user, 
                        'message'                                    =>'SignUp / Register User Successfully' ,
                        'response_code'                              =>'200',
                        'Upload and save your File / Image'          =>$user1 
                    ], $this-> successStatus); 
    }
    /*--------------     END HERE SIGN UP / USER REGISTER API       ------------*/



    /*-------------- 'Done'     API For SIGN IN    -----------*/      
   
    public function signin()
    { 
        if(Auth::attempt([
                'email'         => request('email'), 
                'password'      => request('password')
            ]))
        { 
            $user = Auth::user(); 
            $success['Token Password'] =  $user->createToken('MyApp')-> accessToken; 
            return response()->json(['success' => $success], $this-> successStatus); 
        }else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
    /*--------------   END SIGN IN / LOGIN API       ---------------*/

    
    /*-------------- 'Done'   API FOR GET & FETCH ALL DETAILS ANY USER USEING TOKEN KEY    ----------*/
    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    } 
    /*----------     END HERE DETAILS API  -----------*/


    /*---------- 'Done'   API For Fetch All Location  ----------*/
    public function location()
    {
        try{
                $location =Location::count();
                if($location > 0){
                    $result=Location::get();
                    return \Response::json([
                        'message'           => 'All locations fetch  successfully',
                        'result'            =>$result,
                        'response_code'     => 200,
                        'status'            => 1
                    ], 200);
                }else{
                    return \Response::json([
                        'message'           => 'location not found ',
                        'response_code'     => 400,
                        'result'            =>'',
                        'status'            => 0
                    ], 200);
                }
            }
            catch(Exception $e){
                    return \Response::json([
                        'message'          => 'error message', 
                        'status'           => 0,
                        'result'           => $e->getMessage()
                    ], HttpResponse::HTTP_CONFLICT);
            }
    }
    /*---------  End Location API  ----------*/



    //-----------  'Done' -------- API for Send OTP ----------//
    public function sendOtpforSignUpandRegister(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'mobile' => 'required|min:10|numeric',
         // 'otp' => 'required|min:4|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'response_code'     => 401,
                    'response_message'  => $validator->errors()
                ],200);
        }else{
                try{
                    $mobile = $request->input('mobile');
                    $count = User::where(['mobile'=>$mobile])->count();
                    if($count > 0)
                    {
                        $result =DB::table('users')->select('users.*')->where(['mobile'=>$mobile])->first();
                        if($result){   
                        $mobo =$result->mobile;
                        // $otp = $this->sendSms($mobo);    //--- This is use to send OTP on mobile Number (Phone)
                        $otp = rand('0000','9999');         //--- This is also send otp on Mobile but which stored in database  
                        if($otp){
                            User::where('mobile',$mobo)->update(['otp' => $otp]);
                            // $mobo->save();
                            // $otp->save();
                            return \Response::json([
                                'id'                    =>$result->id,
                                'status'                => '1',
                                'response_code'         =>'200',
                                'response_message'      => 'Generate OTP Successfuly on Your mobile number',
                                'User Details:'         =>$result,
                                'New OTP'               => $otp,
                            ],200);
                        }else{
                            return \Response::json([
                                'response_message'      => 'Something went wrong!', 
                                'status'                => 0
                            ],400);
                        }
                        }else{
                            return \Response::json([
                                'response_message'      => 'User account was deactivate. Please contact to admin!',
                                'response_code'         => 403, 
                                'status'                => 0
                            ],200);
                        }
                    }else{
                            return \Response::json([
                                'status'                 => 0,
                                'result'                 =>'',
                                'response_code'          => 204,
                                'response_message'       => 'Your Mobile number is not registered'
                            ], 200);
                        }
                }catch(Exception $e)
                {
                            return \Response::json([
                                'response_message'       => 'error message', 
                                'status'                 => 0, 
                                'result'                 => $e->getMessage()
                            ], HttpResponse::HTTP_CONFLICT);
                }
           }
    }
    //------------------------   End of send otp ------ complete


    //-----------------Done  Api for Send SMS on user Mobile --------------------//
    public function sendSms($mobile)
    {
        try
        {
            $curl = curl_init();
            $rand = rand('1000','9999');
            $message = $mobile.' OPT for verify your MObile Number'.$rand ;
            curl_setopt_array($curl, array(
            CURLOPT_URL             => "http://2factor.in/API/V1/1064bf98-02a9-11eb-9fa5-0200cd936042/SMS/$mobile/$rand",
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => "GET",
            CURLOPT_POSTFIELDS      => "",
            CURLOPT_HTTPHEADER      => array("content-type: application/x-www-form-urlencoded"),));
            $response = curl_exec($curl);
            //print_r($response); exit;
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) 
            {
                return $err;
            } 
            else 
            {
                return $rand;
            }
        } 
            catch (Exception $e) 
            {
                return $e->getMessage();
            } 
    }  //--------------  End SendSMS Api ---------------------//



    //--------------------  API for Submit OTP ----------------// 
    public function submitOtpAndVerificationforRegister(Request $request)
    {
        $validator = Validator::make($request->all(),[
           'otp' => 'required|min:4|numeric'
        ]);
        if ($validator->fails()){
            return response()->json([
                'response_code'     => 401,
                'response_message'  => $validator->errors()
            ],200);
        }else{
              try{
                  $verify = User::where('otp',$request->otp)->first();
                    if($verify){   
                        $data = User::select('id')->where('otp',$request->otp)->first();
                        $id=$data['id'];
                        $verify->verify_otp = 1;
                        $verify->update();
                        return \Response::json([
                            'status'                => 1 ,
                            'Database id'           =>$id, 
                            'response_code'         => 200, 
                            'result Verified_id'    =>$verify->id,
                            'response_message'      => 'Your OTP is veirifed successfuly!',
                            // 'Your OTP Is :' =>'verify_otp'
                        ],200);
                    }else{
                        return \Response::json([
                            'status'           => 0,
                            'error'            => 'Incorrect OTP!', 
                            'response_message' => 'Please enter correct OTP'
                        ],401);  
                    }
              }catch(Exception $e){
                    return \Response::json([
                        'response_message'      => 'error response_message', 
                        'status'                => 0, 
                        'result'                => $e->getMessage()
                    ], HttpResponse::HTTP_CONFLICT);
                }
        }
    }   //-----------------   End Submit OPT Api  ------------------//


    //----------------Done Api for Forgot Password  ------------------------//    
    public function forgotPasswordandOtpgenerate(Request $request)
    {   
        $mobilenumber = $request->mobile;
        $u_email = $request->email;
        if(empty($mobilenumber)) {
            return response()->json([
                'message'       => 'required is mobile number', 
                'response-code' => 401,
            ]);
        }
        if(empty($u_email)) {
                return response()->json([
                    'response_message' => 'required is email',
                    'response_code'    => 401,
                ]);    
        }
            $user = User::where('mobile',$mobilenumber)->orWhere('email',$u_email)->count();
            if($user == 1) {
                $otpsendmobile = $user;
                $otp = $this->sendSms($otpsendmobile);
                $userData = User::where('mobile',$mobilenumber)->orWhere('email',$u_email)->first();
                $storeotp = array('otp'=>$otp);
                $updateotp = User::where('mobile',$mobilenumber)->orWhere('email',$u_email)->update($storeotp);
                if($updateotp) {
                    return response()->json([
                        'response_message'          => 'Send OPT on your email or mobile number',
                        'response_code'             => 200,
                        'mobile'                    => $userData->mobile,
                        'email'                     => $userData->email,
                        'Your generated OTP is:'    => (string)$otp,
                    ]);
                }else{
                    return response()->json([
                        'response_message'  => 'Failed otp',
                        'response_code'     => 200,
                        ]);
                }
            }   else{
                    return response()->json([
                        'response_message'  => 'Your mobile is not register',
                        'response_code'     => 200,
                    ]);
                }
    } 
    //---------------   End Forgot Password api------------------//


    //---------------  Otp Verification for Change Password ---------------//
    public function optVerificationforResetAndChangePassword(Request $request)
    {
       // $validator = Validator::make($request->all(),[
       //      // 'mobile'        => 'required|numeric',
       //      // 'old_password'      => 'required',
       //      // 'c_password'    => 'required|same:password',
       //      // 'otp'           => 'required',
       // ]);
       // if($validator->fails()) {
       //      return response()->json([
       //          'response_code'     =>401,
       //          'response_message'  =>$validator->errors(),
       //      ], 200);
       // }
       $mobile           = $request->mobile;
       $old_password     = $request->password;
       $new_password     = $request->new_password;
       $confirm_password = $request->c_password;
       // $new_password->save();
       $otp = $request->otp;
       if(empty($mobile)) {
            return response()->json([
                'response_message' => 'required is mobile number !',
                'response_code'    => 401,
            ]);
       }
       if(empty($old_password)) {
            return response()->json([
                'response_message' => 'required is old password !',
                'response_code'    => 401,
            ]);
       }
       if(empty($new_password)) {
            return response()->json([
                'response_message' => 'required is New Password !',
                'response_code'    => 401,
            ]);
       }
        if(empty($confirm_password)) {
            return response()->json([
                'response_message' => 'required is Confirm Password !',
                'response_code'    => 401, 
            ]);
       }
       if(empty($otp)){
            return response()->json([
                'response_message' => 'required your OTP',
                'response_code'    => 401,  
            ]);
       }
       else{
            $verifymobilestore = User::where('mobile',$mobile)->first();

            $forgotPasswordotp = $verifymobilestore->otp;

            $generateNewPassword = array('password' => Hash::make($new_password),);

            if($forgotPasswordotp == $otp) {

                if($new_password == $confirm_password) {
                    $resetpassword = User::where('mobile',$mobile)->update($generateNewPassword);

                    if($resetpassword) {
                        return response()->json([
                            'status'           => 1,  
                            'response_code'    => 200,
                            'response_message' => 'Reset Your Password Successfully...!',
                        ]);
                    }else {
                        return response()->json([
                            'status'           => 0, 
                            'response_code'    => 401,      
                            'response_message' => 'Update password field!',
                        ], 200);
                    }

                }else {
                    return response()->json([
                        'status'            => 0,
                        'response_code'     => 401,
                        'response_message'  => 'Password and c_password are different!',
                    ], 200);
                }
            }else {
                return response()->json([
                    'status'            => 0,
                    'response_code'     => 401,
                    'response_message'  => 'Invalid OTP!',
                ], 200);
            }
        }
    }     
    //---------------  Otp Verification for Change Password ---------------//

    
    //---------------- Api for Login  ------------------//
    public function userlogin(Request $request)
    {
        if(Auth::attempt([
                'mobile'         => request('mobile'), 
                'password'      => request('password')
            ]))
        { 
            $user = Auth::user(); 
            $success['Token Password'] =  $user->createToken('MyApp')-> accessToken; 
            return response()->json([
                'response_meassage' => 'Login Successfully....!',
                'response_code'     => 200,
                'status'            => 1,
                'user Details'      => $user,
                'success'           => $success,
            ], $this-> successStatus); 
        }else{ 
            return response()->json([
                'response_message'  => 'Something went wrong !',
                'error'             => 'Please try again',
                'response_code'     => 401,
                'status'            => 0,
            ], 401); 
        } 
    }
    //------------------ End Login APi   ------------------//




}
