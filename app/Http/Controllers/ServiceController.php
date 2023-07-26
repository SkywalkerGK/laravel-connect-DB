<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(){
        //Eloquent ORM
        //$departments = Department::all(); //ดึงข้อมูลมาทั้งหมด
        //$departments = Department::paginate(2); //แบ่ง กำหนดหมายเลขหน้า

        //Query builder
        //$departments = DB::table('departments')->get();
        //$departments = DB::table('departments')->paginate(2); //แบ่ง กำหนดหมายเลขหน้า
        /*
        $departments = DB::table('departments')
        ->join('users','departments.user_id','users.id')
        ->select('departments.*','users.name')->paginate(2);
        */
        $services=Service::paginate(2);
        return view('admin.service.index',compact('services'));
    }

    public function store(Request $request){
        //ตรวจสอบข้อมูล
        $request->validate([
            'service_name'=>'required|unique:services|max:255',
            'service_image'=>'required|mimes:jpg,jpeg,png',
            
        ],
        [
            'service_name.required' => 'กรุณาป้อนชื่อบริการด้วยครับ',
            'service_name.max' => 'จำนวนอักขระเกินที่กำหนด',
            'service_name.unique' => 'มีชื่อบริการนี้ในฐานข้อมูลแล้ว',
            'service_image.required' => 'กรุณาใส่ภาพประกอบด้วยครับ',
        ]
    );

    //การเข้ารหัสรูปภาพ
    $service_image = $request->file('service_image');
    //gen ชื่อภาพ
    $name_gen = hexdec(uniqid());

    //ดึงนามสกุลไฟล์ภาพ
    $img_ext = strtolower($service_image->getClientOriginalExtension());

    $img_name = $name_gen.'.'.$img_ext;

    //อัพโหลด และบันทึกข้อมูล
    $upload_location = 'image/servies/';
    $full_path = $upload_location.$img_name;
    

    Service::insert([
        'service_name'=>$request->service_name,
        'service_image'=>$full_path,
        'created_at'=>Carbon::now()
    ]);
    $service_image->move($upload_location,$img_name);
    return redirect()->back()->with('success','บันทึกข้อมูลเรียบร้อย');

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
    //$data = array(); //จับคู่ key กับ vaule อ้างอิงตามชื่อฟิล = รีเควสที่ส่งมา ชื่อรีเควส
    //$data['department_name'] = $request->department_name;
    //$data['user_id'] = Auth::user()->id;

    //DB::table('departments')->insert($data);
    
    }
    public function edit($id){
        //dd($id);
        $services = Service::find($id);
        return view('admin.service.edit',compact('services'));

    }
    public function update(Request $request,$id){
        //ตรวจสอบข้อมูล
        $request->validate([
            'service_name'=>'required|max:255',
            'service_image'=>'mimes:jpg,jpeg,png',
        ],
        [
            'service_name.required' => 'กรุณาป้อนชื่อบริการด้วยครับ',
            'service_name.max' => 'จำนวนอักขระเกินที่กำหนด',
        ]
    );


        //การเข้ารหัสรูปภาพ
        $service_image = $request->file('service_image');

        //อัพเดตภาพและชื่อ
        if($service_image){
            //gen ชื่อภาพ
            $name_gen = hexdec(uniqid());

            //ดึงนามสกุลไฟล์ภาพ
            $img_ext = strtolower($service_image->getClientOriginalExtension());

            $img_name = $name_gen.'.'.$img_ext;

            //อัพโหลด และอัพเดตข้อมูล
            $upload_location = 'image/servies/';
            $full_path = $upload_location.$img_name;

            //อัพเดตข้อมูล
            Service::find($id)->update([
                'service_name'=>$request->service_name,
                'service_image'=>$full_path,
            ]);

            //ลบภาพเก่าและอัพเดตใหม่แทนที่
            $old_image = $request->old_image;
            unlink($old_image);
            $service_image->move($upload_location,$img_name);
            return redirect()->route('service')->with('success','อัพเดตภาพเรียบร้อย');

        }
        else{
            //อัพเดตชื่ออย่างเดียว
            Service::find($id)->update([
                'service_name'=>$request->service_name,
            ]);
            return redirect()->route('service')->with('success','อัพเดตชื่อเรียบร้อย');

        }

    $update = Department::find($id)->update([
        'department_name'=>$request->department_name,
        'user_id'=>Auth::user()->id
    ]);
    return redirect()->route('department')->with('success','อัพเดตข้อมูลเรียบร้อย');

}

public function delete($id){
    //ลบภาพ
    $img = Service::find($id)->service_image;
    //dd($img);
    unlink($img);

    //ลบข้อมูลจากฐานข้อมูล
    $delete = Service::find($id)->forceDelete();
    return redirect()->back()->with('success','ลบข้อมูลถาวรเรียบร้อย');
}
}
