<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\User_otp;
use Validator;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'phone_numper' => 'required|string|min:11',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'phone_numper' => 'required|string|min:11|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

       
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

         //generate otp for user
         $now=now(); 
         $userOtp= User_otp::create([
             'user_id'=>$user->id,
             'otp'    =>rand(123456,999999),
             'expired_at'=>$now->addMinutes(10),
             ]);
 
         //send  this otp in sms to user by twilio
          $userOtp->sendSms($request->phone_numper); 
        
         //redirect to verfiy route to verfiy the otp
        // return redirect()->route('verify')->with('otp has been sent in your mobile number');
 
        //but this line is commented beacuse we dont have a free twilio phone number

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    public function generateOtp($phone_numper){
        $user=User::where('phone_numper',$phone_numper)->firet();
        $userOtp=User_otp::where('user_id',$user->id)->latest()->first();
        $now=now();

          //if he has otp and not expired
        if($userOtp && $now->isBefore($userOtp->expired_at)){
            return $userOtp;
        }

        //create otp if there is no one
        return User_otp::create([
            'user_id'=>$user->id,
            'otp'    =>rand(123456,999999),
            'expired_at'=>$now->addMinutes(10),
            ]);
    }

   public function verify(Request $request){

        $request->validate([
            'opt'=>'required',
            'user_id'=>'required|exists:users,id'
        ]);

        $userOtp=User_otp::where('user_id',$request->user_id)->where('opt',$request->opt)->first();

        $now=now();
        if(!$userOtp){
            return response()->json([
                'message' => 'your otp is not correct',  
            ]);
        }
        else if($userOtp && $now->isAfter($userOtp->expired_at)){
            return response()->json([
                'message' => 'your otp is expired ',  
            ]);
        }
        else{
           // do what you want 
        }

   }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}