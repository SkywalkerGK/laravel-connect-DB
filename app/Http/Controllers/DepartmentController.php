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
        //$departments = Department::paginate(2); //แบ่ง กำหนดหมายเลขหน้า

        //Query builder
        //$departments = DB::table('departments')->get();
        //$departments = DB::table('departments')->paginate(2); //แบ่ง กำหนดหมายเลขหน้า
        $departments = DB::table('departments')
        ->join('users','departments.user_id','users.id')
        ->select('departments.*','users.name')->paginate(2);
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

    public function edit($id){
        //dd($id);
        $department = Department::find($id);
        return view('admin.department.edit',compact('department'));

    }

    public function update(Request $request,$id){
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

            $update = Department::find($id)->update([
                'department_name'=>$request->department_name,
                'user_id'=>Auth::user()->id
            ]);
            return redirect()->route('department')->with('success','อัพเดตข้อมูลเรียบร้อย');

    }
}
