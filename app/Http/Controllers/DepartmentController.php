<?php

namespace App\Http\Controllers;

use Session;
use App\Department;
use Illuminate\Http\Request;
use App\Http\Requests\AddDepartmentRequest;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    private $_pagi = 2;
    public function __construct(){
        
    }
    public function get_grid(){
        $department = Department::paginate($this->_pagi);
        $countEmployee = array();
        $userIntialized = array();
        foreach($department as $value){
            $countEmployee[] = Department::find($value['id'])->employee()->count();
            $userIntialized[] = Department::find($value['id'])->user()->get()->toArray();
        }
            
        return view("admin.department.grid")->with(["department" => $department, 'countEmployee' => $countEmployee, 'userIntialized' => $userIntialized]);
    }

    public function get_add(){
    	return view("admin.department.add");
    }

    public function post_add(AddDepartmentRequest $request){
        $department = new Department;

        $department->name = $request->name;
        $department->office_phone = $request->phone;
        $department->manager = $request->manager;
        $department->id_intialized = Auth::id();
        $department->save();

    	Session::flash("success", "Create successful");
        return redirect("admin/department/grid");
    }

    public function get_search(Request $request){
        $key = $request->key;
        $department = Department::where('name', 'like', "%$key%")->paginate($this->_pagi);

        $countEmployee = array();
        $userIntialized = array();
        foreach($department as $value){
            $countEmployee[] = Department::find($value['id'])->employee()->count();
            $userIntialized[] = Department::find($value['id'])->user()->get()->toArray();
        }
            
        return view("admin.department.grid")->with(["department" => $department,'search' => $key, 'countEmployee' => $countEmployee, 'userIntialized' => $userIntialized]);
        
    }

    public function get_edit($id){
        $department = Department::find($id)->toArray();
        return view("admin.department.edit")->with(['department' => $department]);
    }
    public function post_edit(Request $request){
        $this->validate($request, [
            "phone" => "required",
            "manager" => "required",
        ]);

        $department = Department::find($request->id);
        $department->office_phone = $request->phone;
        $department->manager = $request->manager;
        $department->save();

        Session::flash("success", "Update successful");
        return redirect("admin/department/grid");
    }

    public function get_delete($id){
        $employee = Department::find($id)->employee()->count();
        if($employee > 0){
            Session::flash("danger", "Sorry, you need to delete employee empty");
        }else{
            Department::destroy($id);
            Session::flash("success", "Successfully deleted");
        }
        return redirect('admin/department/grid');
    }

    public function get_detail($id){
        $department = Department::find($id)->toArray();
        $userIntialized = Department::find($id)->user()->get()->toArray();
        $userName = $userIntialized[0]['name'];
        return view("admin.department.detail")->with(['department' => $department, 'userIntialized' => $userName]);
    }
}
