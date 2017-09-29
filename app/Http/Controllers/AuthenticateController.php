<?php

namespace App\Http\Controllers;

use IlluminateHttpRequest;
use Illuminate\Http\Request;
use AppHttpRequests;
use AppHttpControllersController;
use JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;

class AuthenticateController extends Controller
{
	public function __construct(){
		$this->middleware('jwt.auth', ['except' => ['authenticate']]);
	}
    public function index()
    {
        // Retrieve all the users in the database and return them
	    $users = User::all();
	    return $users;
    }    

    public function authenticate(Request $request)
    {
    	/*$temp = $request->all();
    		echo "<pre>";
    		print_r($temp);
    		echo "<pre>";
*/        $credentials = $request->only('email', 'password');

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
}
