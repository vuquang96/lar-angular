<?php

namespace App\Http\Controllers;

use IlluminateHttpRequest;
use Illuminate\Http\Request;
use AppHttpRequests;
use AppHttpControllersController;
use JWTAuth;
use App\User;
use Tymon\JWTAuthExceptions\JWTException;

class AuthenticateController extends Controller
{
    private $user;
	public function __construct(User $user){
		//$this->middleware('jwt.auth', ['except' => ['authenticate']]);
        $this->user = $user;
	}
    public function index()
    {
        // Retrieve all the users in the database and return them
	    $users = User::all();
	    return $users;
    }    

    public function authenticate(Request $request)
    {
    	$credentials = $request->all();

        //$credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getUserId();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function register(Request $request){
        $user = $this->user->create([
          'name' => $request->get('name'),
          'email' => $request->get('email'),
          'password' => bcrypt($request->get('password'))
        ]);
        return response()->json(['status'=>true,'message'=>'User created successfully','data'=>$user]);
    }
    
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        $token = null;
        try {
           if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['invalid_email_or_password'], 422);
           }
        } catch (JWTAuthException $e) {
            return response()->json(['failed_to_create_token'], 500);
        }
        return response()->json(compact('token'));
    }
    public function getAuthUser(Request $request){
        $user = JWTAuth::toUser($request->token);
        return response()->json(['result' => $user]);
    }

    public function get_grid(Request $request){
        $user = JWTAuth::toUser($request->token);
        $users = User::where( 'level','>=', $user->level )->get();
        return response()->json(['result' => $users]);
    }

    public function get_detail(Request $request){
        $id = $request->id;
        $user = User::where( 'id', $id )->first();
        return response()->json(['result' => $user]);
    }
    public function update(Request $request){
        $userCurrent = JWTAuth::toUser($request->token);
        $id = $request->id;
        $user = User::where( 'id', $id )->first();
        if($userCurrent->level >= $user->level){
            return response()->json(['result' => "false"]);
        }

        $user->name = $request->name;
        $user->level = $request->level;
        if($request->password){
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return response()->json(['result' => "true"]);
    }

    public function delete(Request $request){
        $userCurrent = JWTAuth::toUser($request->token);
        $id = $request->id;
        $user = User::where( 'id', $id )->first();
        if($userCurrent->level >= $user->level){
            return response()->json(['result' => "false"]);
        }

        $user->delete();
        return response()->json(['result' => "true"]);
    }
}
