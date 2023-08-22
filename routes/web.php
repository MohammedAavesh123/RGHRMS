<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotosController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\PositionController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// -----------------------------login----------------------------------------//
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'authenticate']);
Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');


Route::group(['middleware'=>'auth'],function()
{

Route::get('/profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('profile');
// ----------------------------- main dashboard ------------------------------//
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('em/dashboard', [App\Http\Controllers\HomeController::class, 'emDashboard'])->name('em/dashboard');

// -----------------------------settings----------------------------------------//
Route::get('company/settings/page', [App\Http\Controllers\SettingController::class, 'companySettings'])->name('company/settings/page');
Route::get('roles/permissions/page', [App\Http\Controllers\SettingController::class, 'rolesPermissions'])->name('roles/permissions/page');
Route::post('roles/permissions/save', [App\Http\Controllers\SettingController::class, 'addRecord'])->name('roles/permissions/save');
Route::post('roles/permissions/update', [App\Http\Controllers\SettingController::class, 'editRolesPermissions'])->name('roles/permissions/update');
Route::post('roles/permissions/delete', [App\Http\Controllers\SettingController::class, 'deleteRolesPermissions'])->name('roles/permissions/delete');

// ----------------------------- Department  ------------------------------//
Route::get('department/list/view', [App\Http\Controllers\Admin\DepartmentController::class, 'index'])->name('department.list');
Route::get('department/add', [App\Http\Controllers\Admin\DepartmentController::class, 'add_view'])->name('department.add');
Route::post('department/list/save', [App\Http\Controllers\Admin\DepartmentController::class, 'saveDepartment'])->name('department.list.save');
// Route::get('department/edit/{id}', [App\Http\Controllers\Admin\DepartmentController::class, 'edit'])->name('department.edit');
Route::get('department/update', [App\Http\Controllers\Admin\DepartmentController::class, 'update'])->name('department.update');
Route::get('department/delete/{id}', [App\Http\Controllers\Admin\DepartmentController::class, 'delete'])->name('department.delete');
Route::get('department/status/{status}/{id}', [App\Http\Controllers\Admin\DepartmentController::class, 'ChangeDepartStatus'])->name('change.department.status');

// ----------------------------- Role  ------------------------------//
Route::get('role/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('role.create');
Route::post('role/store', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('role.store');
Route::get('role/list', [App\Http\Controllers\Admin\RoleController::class, 'list'])->name('role.view');
Route::get('role/edit/{id}', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('role.edit');
Route::post('role/update/{id}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('role.update');
Route::get('role/delete/{id}', [App\Http\Controllers\Admin\RoleController::class, 'delete'])->name('role.delete');


//------------------Designation--------------------------//
Route::get('designation/create', [App\Http\Controllers\Admin\DesignationController::class, 'create'])->name('designation.create');
Route::post('designation/store', [App\Http\Controllers\Admin\DesignationController::class, 'store'])->name('designation.store');
Route::get('designation/list', [App\Http\Controllers\Admin\DesignationController::class, 'index'])->name('designation.index');
Route::get('designation/edit/{id}', [App\Http\Controllers\Admin\DesignationController::class, 'edit'])->name('designation.edit');
Route::post('designation/update/{id}', [App\Http\Controllers\Admin\DesignationController::class, 'update'])->name('designation.update');
Route::get('designation/delete/{id}', [App\Http\Controllers\Admin\DesignationController::class, 'delete'])->name('designation.delete');

Route::get('designation/status', [App\Http\Controllers\Admin\DesignationController::class, 'ChangedesignationStatus'])->name('change.designation.status');



});


