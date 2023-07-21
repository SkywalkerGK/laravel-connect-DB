<?php

use App\Http\Controllers\DepartmentController;
use App\Models\Department;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        $users = DB::table('users')->get();

        //มาจาก Model User
        //$users = User::all();
        return view('dashboard',compact('users'));
    })->name('dashboard');
});

Route::get('/department/all',[DepartmentController::class,'index'])->name('department');

Route::post('/department/add',[DepartmentController::class,'store'])->name('addDepartment');
