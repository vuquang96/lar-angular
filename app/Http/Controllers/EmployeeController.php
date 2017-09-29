<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddEmployeeRequest;
use App\Employee;
use App\Department;
use File;
use Session;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    private $_pagi = 2;
    public function __construct(){
        
    }
    public function get_grid($department = "all"){
        if($department == "all"){
            $employee = Employee::paginate($this->_pagi);
        }else{
            $employee = Department::find($department)->employee()->paginate($this->_pagi);
        }
       
        $userIntialized = array();
        $department = array();
        foreach($employee as $value){
            $userIntialized[] = Employee::find($value['id'])->user()->get()->toArray();
            $department[] = Employee::find($value['id'])->department()->get()->toArray();
        } 
        return view("admin.employee.grid")->with(['employee' => $employee, 'userIntialized' => $userIntialized, 'department' => $department]);
    }

    public function get_add(){
        $department = Department::all()->toArray();
    	return view("admin.employee.add")->with(['department' => $department]);
    }

    public function post_add(AddEmployeeRequest $request){
        $fileName = $request->file('photo')->getClientOriginalName();

        $employee = new Employee();
        $employee->name = $request->name;
        $employee->photo = $fileName;
        $employee->job_title = $request->job_title;
        $employee->phone = $request->cellphone;
        $employee->email = $request->email;
        $employee->birthday = $request->birthday;
        $employee->sex = $request->sex;
        $employee->address = $request->address;
        $employee->date_start_work = $request->date_start_work;
        $employee->id_department = $request->department;
        $employee->id_intialized = Auth::id();
        $request->file('photo')->move('resources/uploads', $fileName);
        $employee->save();

        Session::flash("success", "Create successful");
        return redirect("admin/employee/grid");
    }

    public function get_detail($id){
        $employee = Employee::find($id)->toArray();
    	return view("admin.employee.detail")->with(['employee' => $employee]);
    }

    public function get_search(Request $request){
        $key = $request->key;
        $employee = Employee::where('name', 'like', "%$key%")->paginate($this->_pagi);

        $userIntialized = array();
        $department = array();
        foreach($employee as $value){
            $userIntialized[] = Employee::find($value['id'])->user()->get()->toArray();
            $department[] = Employee::find($value['id'])->department()->get()->toArray();
        } 
        return view("admin.employee.grid")->with(['employee' => $employee,'search' => $key, 'userIntialized' => $userIntialized, 'department' => $department]);
    }

    public function get_delete($id){
        Employee::destroy($id);
        Session::flash("success", "Successfully deleted");
        return redirect("admin/employee/grid");
    }

    public function get_edit($id){
        $employee = Employee::find($id)->toArray();
        $department = Department::all()->toArray();
        return view("admin.employee.edit")->with(['employee' => $employee, 'department' => $department]);
    }

    public function post_edit(AddEmployeeRequest $request){
        $employee = Employee::find($request->id);
        if(is_object($request->file('photo'))){
            $img = "resources/uploads/" . $request->photo_old;
            if(File::exists($img)){
                File::delete($img);
            }
            $fileName = $request->file('photo')->getClientOriginalName();
            $request->file('photo')->move('resources/uploads', $fileName);
            $employee->photo = $fileName;
        }
        
        $employee->name = $request->name;
        $employee->job_title = $request->job_title;
        $employee->phone = $request->cellphone;
        $employee->email = $request->email;
        $employee->birthday = $request->birthday;
        $employee->sex = $request->sex;
        $employee->address = $request->address;
        $employee->date_start_work = $request->date_start_work;
        $employee->id_department = $request->department;
        $employee->save();

        Session::flash("success", "Update successful");
        return redirect("admin/employee/grid");
    }
}
