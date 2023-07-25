<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index(){
        //Eloquent ORM
        //$departments = Department::all(); //ดึงข้อมูลมาทั้งหมด

        //Query builder
        $departments = DB::table('departments')->get();
        return view('admin.department.index',compact('departments'));
    }

    public function store(Request $request){
        //ตรวจสอบข้อมูล
        $request->validate([
            'department_name'=>'required|unique:departments|max:255'
            
        ],

        [
            'department_name.required' => 'กรุณาป้อนชิ่อแผนกด้วยครับ',
            'department_name.max' => 'จำนวนอักขระเกินที่กำหนด',
            'department_name.unique' => 'มีชื่อแผนกนี้ในฐานข้อมูลแล้ว'
        ]
    
    );
    /*
    //บันทึกข้อมูล Eloquent ORM
        //dd($request->department_name); //DeBug
        $department = new Department;
        $department->department_name = $request->department_name; //table->field
        $department->user_id = Auth::user()->id;
        $department->save();
    //Eloquent ORM
    */
    //บันทึกข้อมูล Query builder
    $data = array(); //จับคู่ key กับ vaule อ้างอิงตามชื่อฟิล = รีเควสที่ส่งมา ชื่อรีเควส
    $data['department_name'] = $request->department_name;
    $data['user_id'] = Auth::user()->id;

    DB::table('departments')->insert($data);
    return redirect()->back()->with('success','บันทึกข้อมูลเรียบร้อย');
    }
}
