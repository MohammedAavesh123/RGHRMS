<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\Models\Admin\PositionType;
use App\Models\Admin\Department;
use App\Models\Admin\PositionType;
use Validator;
use DB;


class DesignationController extends Controller
{
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $department = department::all();
        $position = DB::table('position_types')->join('departments','departments.id','=','position_types.depart_id')->select('departments.department','position_types.*')->get();
        return view('Admin.designation.list', compact('position','department'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $department = department::all();
        return view('Admin.designation.add', compact('department'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
      $validator = validator::make($input, [
            'depart_id' => 'required',
            'position' => 'required',
            'status' =>'required'
        ]);

      if($validator->fails()){
        return back()->with('error', $validator->errors());
    }
    /*$data = new PositionType;
    $data->depart_id = $request->depart_id;
    $data->position = $request->position;
    
    $data->save();*/
    $data['depart_id'] = $request->depart_id;
    $data['position'] = $request->position;
    $data['status'] = $request->status;
    DB::table('position_types')->insert($data);
   // return back()->with('success','Position has been created successfully.');
    return redirect()->route('designation.index')
            ->with('success','Designation updated successfully.');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       $position = DB::table('position_types')->get();
       return view('Admin.designation.list', compact('position'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $position = DB::table('position_types')->where('id', $id)->first();
        return view('Admin.designation.edit', compact('position'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $this->validate($request,[
            'position' => 'required',
            'status' => 'required',
        ]);  

        $depart = PositionType::find($id);
        $depart->department = $request->department;
        $depart->status = $request->status;
        if($depart->update()){
            return redirect()->route('designation.index')
            ->with('success','Designation updated successfully.');
        }
        else{
            return redirect()->route('designation.edit')
            ->with('error','Designation not update.');
        } 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  
    public function delete($id){
        $delete = DB::table('position_types')->where('id',$id)->delete();
         if($delete){
             return redirect()->route('designation.index')
             ->with('success','Designation deleted successfully.');
         }
         else{
             return redirect()->route('designation.index')
             ->with('error','Designation not deleted.');
         }
     }

     public function ChangedesignationStatus(Request $request){
        $data['position'] =  $request->name_get;
        $data['depart_id']    = $request->depart_get;
        $data['status'] = $request->status_get;
        $position_id = $request->position_id;
        $update_position = PositionType::find($position_id);
        if($update_position){
            PositionType::where('id',$position_id)
            ->update($data);
            return response()->json(['success'=>'Designation change successfully.']); 
        }
        else{
            return response()->json(['success'=>'Designation not change successfully.']); 
        } 
      
     }
}

