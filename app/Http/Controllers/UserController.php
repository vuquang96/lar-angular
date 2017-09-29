<?php

namespace App\Http\Controllers;

use App\User;
use Mail;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Requests;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Support\Facades\Input;


class UserController extends Controller
{
  private $_pagi = 5;
  public function __construct() {
  
  }

	public function get_login(){
		return view("admin.login");
	}

  public function post_login(LoginRequest $request){
    $login = [
      "name" => $request->username,
      "password" => $request->password
    ];
    if(Auth::attempt($login)){
      return redirect("admin/user/grid");
    }else{
      return redirect()->back()->withInput();
    }
  }

  public function get_grid(){
    $users = User::where( 'level','>=', get_level() )->paginate($this->_pagi);
 		return view("admin.administrator.grid", ['users' => $users]);
  }

  public function get_change(){
    return view("admin.administrator.change-pass");
  }

  public function post_change(UserRequest $request){
    
    $user = User::find(Auth::id());
    $hashedPassword = $user->password;
    if (Hash::check($request->current_password, $hashedPassword)) {
      //Change the password
      $user->fill([
          'password' => Hash::make($request->new_password)
      ])->save();

      Session::flash('success', 'Your password has been changed.');
    }else{
      Session::flash('danger', 'The information is incorrect, please enter it again!');
    }
    return back();
  }

  public function get_add(){
    if(get_level() > 1){
      return redirect("admin/user/grid");
    }
 		return view("admin.administrator.add");
  }

  public function post_add(CreateUserRequest $request){
    $user = new User;
    $user->name = strtolower($request->username);
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->level = $request->level;
    $user->id_intialized = Auth::id();
    $user->remember_token = $request->_token;
    $user->save();

    Mail::send('mail.user', ['name'=>$request->username, 'password'=>$request->password, 'level' => $request->level], function ($message) {
      $message->from('newwave@gmail.com', 'Newwave');
      $message->to(Input::get('email'), 'Training');
    });

    Session::flash('success', 'The user has been initialized. Send message successfully!');

    return redirect("admin/user/grid");
  }

  public function logout(){
  	Auth::logout();
    return redirect('login');
  }

  public function get_edit($id){
    $user = User::find($id)->toArray();
   
    if( get_level() >= $user['level'] ){
      return redirect("admin/user/grid");
    }
    return view("admin.administrator.edit")->with(["user" => $user]);
  }

  public function post_edit(Request $request){
    $this->validate($request, [
        "password" => "max:50",
    ]);

    $user = User::find($request->id);
    if($request->password != null){
      $user->password = Hash::make($request->password);
    }
    $user->level = $request->level;
    $user->save();

    Mail::send('mail.edituser', ['name'=>$request->username, 'password'=>$request->password, 'level' => $request->level], function ($message) {
      $message->from('newwave@gmail.com', 'Newwave');
      $message->to(Input::get('email'), 'Training');
    });
    $request->session()->flash('success', 'Update successful');
    
    Session::flash("success", "Update successful");
    return redirect("admin/user/grid");
  }

   public function get_delete($id){
    $userdel = User::find($id)->toArray();
    
    if(get_level() < $userdel['level']){
     $department = User::find($id)->department()->count();
     $employee = User::find($id)->employee()->count();
     if($department && $employee){
        User::destroy($id);
        Session::flash("success", "Successfully deleted");
        return  redirect("admin/user/grid");
     }else{
      Session::flash("danger", "Sorry, you need to delete employee and department empty");
      return redirect("admin/user/grid");
     }
    }
      Session::flash("danger", "Sorry, you do not have permission to remove this person");
      return redirect("admin/user/grid");
  }
}
