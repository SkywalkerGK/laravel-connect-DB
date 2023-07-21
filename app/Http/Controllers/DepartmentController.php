<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index(){
        return view('admin.department.index');
    }

    public function store(Request $request){
        //ตรวจสอบข้อมูล
        $request->validate([
                'department_name'=>'required|unique:departments|max:255'
            
        ],

        [
            'department_name.required' => 'กรุณาป้อนค่าตารางด้วยครับ',
            'department_name.max' => 'จำนวนอักขระเกินที่กำหนด'
        ]
    
    );
        //dd($request->department_name); //DeBug
        //บันทึกข้อมูล
        $department = new Department;
        $department->department_name = $request->department_name; 
        $department->user_id = Auth::user()->id;
        $department->save();
        return redirect()->back()->with('success','บันทึกข้อมูลเรียบร้อย');
    }
}
