<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Role;
use Session;
use Auth;
use DB;
use Brian2694\Toastr\Facades\Toastr;
 

class RoleController extends Controller
{
    public function create()
    {
        return view('Admin.role.create');
    }

    public function list(){
        $role = Role::all(); 
        return view('Admin.role.view', compact('role'));
    }

    public function store(Request $request){

            $this->validate($request,[
                'role_type' => 'required',
                'status' => 'required',
            ]);  
 
            $role = new Role;
            $role->role_type = $request->role_type;
            $role->status = $request->status;
            if($role->save()){
                return redirect()->route('role.view')
                ->with('success','New role add successfully.');
            }
            else{
                return redirect()->route('role.create')
                ->with('error','Role not added.');
            } 
    }

    public function edit($id){
        $role = Role::findOrfail($id);
        return view('Admin.role.edit', compact('role'));
    }

    public function update(Request $request, $id){
        $this->validate($request,[
            'role_type' => 'required',
            'status' => 'required',
        ]);  

        $role = Role::find($id);
        $role->role_type = $request->role_type;
        $role->status = $request->status;
        if($role->update()){
            return redirect()->route('role.view')
            ->with('success','Role updated successfully.');
        }
        else{
            return redirect()->route('role.edit')
            ->with('error','Role not update.');
        } 
    }

    public function delete($id){
       $delete = Role::find($id)->delete();
        if($delete){
            return redirect()->route('role.view')
            ->with('success','Role deleted successfully.');
        }
        else{
            return redirect()->route('role.view')
            ->with('error','Role not deleted.');
        }
    }
}
