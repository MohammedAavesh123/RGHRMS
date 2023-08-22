<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Department;
use Session;
use Auth;
use DB;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Str;


class DepartmentController extends Controller
{
   public function Index(Request $request){ 

        $data = Department::orderBy('id','desc')->get();     
        return view('Admin.department.viewlist',compact('data'));
    }
    public function add_view(){
        return view('Admin.department.add_department');
    }
  
    public function saveDepartment(Request $request){
        $input = $request->all();
        $validated = $request->validate([
            'department' => 'required',
            'status' => 'required',
        ]);

        $dataArray = [
            'department' => $request->department,
            'status' => $request->status
        ];
        if(!empty($dataArray)){
            $res = Department:: create($dataArray);
            if($res){
                return redirect()->route('department.list')->with('success','Department Added Succesfully');
            }else{
                echo "something Wrong";  }
        }

    }

    // public function edit($id){
    //     $d_data = Department::findOrfail($id);

    //     return view('Admin.department.viewlist',compact('d_data'));
    // }

    public function update(Request $request){
           
                $id = $request->id;
                $depart = Department::find($id);
                $data['department'] =  $request->name_get;
                $data['status'] =  $request->status_get;
          
                $res = Department::where('id',$id)->update($data);

        if($res){
            return response()->json(['success'=>'Department Updated  successfully.']); 
        }
        else{
            return response()->json(['success'=>'Department Not Updated .']); 
        } 
    }

    public function delete($id){
        $delete = Department::find($id)->delete();
         if($delete){
             return redirect()->route('department.list')
             ->with('success','Department deleted successfully.');
         }
         else{
             return redirect()->route('department.list')
             ->with('error','Department not deleted.');
         }
     }

     public function ChangeDepartStatus(Request $request,$status,$id){
        
        $depart = Department::find($id); 
        $depart->status = $status;
        $res = $depart->save();
        if($res){
            return redirect()->route('department.list')
            ->with('success','Department Status Updated successfully.');
        }
        else{
            return redirect()->route('department.list')
            ->with('success','Department Status Updated not successfully.'); 
        }
      
     }


}

