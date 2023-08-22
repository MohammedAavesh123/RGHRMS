<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Employee;
use App\Models\Admin\Department;
use App\Models\Admin\PositionType;
use App\Models\Admin\Role;
use App\Models\Admin\UserAdditionInfo;
use App\Models\Admin\Bankdetail;
use App\Models\Admin\Document;
use App\Models\Admin\FamilyInfo;
use App\Models\Admin\JobSeeker;
use App\Models\Admin\Jobs;
use App\Models\Admin\Mailtemplate;
use App\Models\Admin\Sendmail;
use App\Models\Admin\EmployeOfficialDetail;
use App\Http\Requests;
use App\Models\User;
use Session;
use Auth;
use DB;
use DataTables;
use Hash;
use Mail;
use Carbon\Carbon;
use App\Helpers\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use File;

class EmployeeController extends Controller
{
    //show employee add page
    public function create(Request $request)
    {
        $userid = Auth::user()->id;
        if(Helpers::checkPermission($userid, $modelName ="Employee" , "create")){

          $departments = Department::all();
          $candidate_info = array();
          if(!empty($request->id))
          {
              $candidates = JobSeeker::findOrfail($request->id);
              if(!empty($candidates))
              {
                $candidate_info = JobSeeker::where('job_seekers.id',$request->id)
                                ->join('jobs','jobs.id','=','job_seekers.job_id')
                                ->select("job_seekers.*","jobs.designation as jobdesignation","jobs.department as jobdepartment","jobs.id as job_id")
                                ->first();
              }
          }

          $already_exist_employee = Employee::where('users.deleted_at',NULL)->join('role_type_users','role_type_users.id','=','users.role_id')->where('users.role_id','=','5')->orWhere('users.role_id','=','1')->select('users.*','role_type_users.role_type')->get();
          $roles = Role::all();
          return view('Admin.employee.create',compact("departments","already_exist_employee","candidate_info","roles"));
        }
        else{
              return redirect('/permission-denied');
        }
    }

    //update employee documents
    public function document(Request $request)
    {
        $this->validate($request,[
            //'document_type' => 'required',
            'Previous_Company_Offer_Letter' => 'file|mimes:jpeg,png,doc,pdf,docx',
            'Previous_Company_Experience_Letter' => 'file|mimes:jpeg,png,doc,pdf,docx',
            'Adhaar_Card' => 'file|mimes:jpeg,png,doc,pdf,docx',
            'PAN_Card' => 'file|mimes:jpeg,png,doc,pdf,docx',
            'Salary_Slip' => 'file|mimes:jpeg,png,doc,pdf,docx'
        ]);

        $document_array = array();

        $destinationPath = public_path('/uploads/document');
        if(!File::isDirectory($destinationPath)){
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        if($request->hasfile('Previous_Company_Offer_Letter')){
            $file = $request->file('Previous_Company_Offer_Letter');
            $name = $file->getClientOriginalName();
            $rename = str_replace(".pdf","-",$name);
            $extension=$file->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/document');
            $filename = $rename.time().'.'.$extension;
            $upload = $file->move($destinationPath, $filename);

            $document_array['Previous Company Offer Letter'] = $filename;
        }
        if($request->hasfile('Previous_Company_Experience_Letter')){
            $file = $request->file('Previous_Company_Experience_Letter');
            $name = $file->getClientOriginalName();
            $rename = str_replace(".pdf","-",$name);
            $extension=$file->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/document');
            $filename = $rename.time().'.'.$extension;
            $upload = $file->move($destinationPath, $filename);
            $document_array['Previous Company Experience Letter'] = $filename;
        }
        if($request->hasfile('Adhaar_Card')){
            $file = $request->file('Adhaar_Card');
            $name = $file->getClientOriginalName();
            $rename = str_replace(".pdf","-",$name);
            $extension=$file->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/document');
            $filename = $rename.time().'.'.$extension;
            $upload = $file->move($destinationPath, $filename);
            $document_array['Adhaar Card'] = $filename;
        }
        if($request->hasfile('PAN_Card')){
            $file = $request->file('PAN_Card');
            $name = $file->getClientOriginalName();
            $rename = str_replace(".pdf","-",$name);
            $extension=$file->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/document');
            $filename = $rename.time().'.'.$extension;
            $upload = $file->move($destinationPath, $filename);
            $document_array['PAN Card'] = $filename;
        }
        if($request->hasfile('Salary_Slip')){
            $file = $request->file('Salary_Slip');
            $name = $file->getClientOriginalName();
            $rename = str_replace(".pdf","-",$name);
            $extension=$file->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/document');
            $filename = $rename.time().'.'.$extension;
            $upload = $file->move($destinationPath, $filename);
            $document_array['Salary Slip'] = $filename;
        }
        $flag = 0;
        foreach($document_array as $document_type => $filename)
        {
          $Document = Document::where('emp_id', '=',  $request->emp_id)->where('type','=',$document_type)->first();
          if ($Document === null)
          {
              $Document = new Document;
              $Document->emp_id = $request->emp_id;
              $Document->document_url = $filename;
              $Document->type = $document_type;
              if($Document->save())
              {
                  $flag = 1;
              }
          }
          else
          {
              $Document = Document::find($Document->id);
              $Document->emp_id = $request->emp_id;
              $Document->document_url = $filename;
              $Document->type = $document_type;
              if($Document->update())
              {
                  $flag = 1;
              }
          }
        }
        if($flag == 1)
        {
          return redirect()->route('employee.show',$request->emp_id)
                   ->with('success','Document uploaded successfully.');
        }

        /*if($request->hasfile('document')){
            $file = $request->file('document');
            $name = $file->getClientOriginalName();
            $rename = str_replace(".pdf","-",$name);
            $extension=$file->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/document');
            $filename = $rename.time().'.'.$extension;
            $upload = $file->move($destinationPath, $filename);
        }*/
        // $Document = Document::where('emp_id', '=',  $request->emp_id)->where('type','=',$request->document_type)->first();
        // if ($Document === null)
        // {
        //     $Document = new Document;
        //     $Document->emp_id = $request->emp_id;
        //     $Document->document_url = $filename;
        //     $Document->type = $request->document_type;
        //     if($Document->save())
        //     {
        //         return redirect()->route('employee.show',$request->emp_id)
        //         ->with('success','Document uploaded successfully.');
        //     }
        // }
        // else
        // {
        //     $Document = Document::find($Document->id);
        //     $Document->emp_id = $request->emp_id;
        //     $Document->document_url = $filename;
        //     $Document->type = $request->document_type;
        //     if($Document->update())
        //     {
        //         return redirect()->route('employee.show',$request->emp_id)
        //         ->with('success','Document uploaded successfully.');
        //     }
        // }
    }

    //update official_detail
    public function official_detail(Request $request)
    {
        $input['image'] ="";
        if ($image = $request->file('image')) {
            $user = User::find($request->emp_id);

            $destinationPath = 'public/profile/';
            if(!File::isDirectory($destinationPath)){
                File::makeDirectory($destinationPath, 0777, true, true);
            }
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['image'] = "$profileImage";
            $user->avatar = $input['image'];

            if($user->update()){
              return redirect()->route('employee.show',$request->emp_id)
              ->with('success','Profile updated successfully.');
            }
         }
        $shift = $request->shift;
        $salary = $request->salary;
        $pl = $request->pl;
        $wfh = $request->wfh;
        $EmployeOfficialDetail = EmployeOfficialDetail::where('employee_id', '=',  $request->emp_id)->first();
        if ($EmployeOfficialDetail === null)
        {
            $OfficialDetail = new EmployeOfficialDetail;
            $OfficialDetail->employee_id = $request->emp_id;
            $OfficialDetail->shift = $shift;
            $OfficialDetail->salary = $salary;
            $OfficialDetail->pl = $pl;
          $OfficialDetail->wfh = $wfh;
            if($OfficialDetail->save())
            {
              return redirect()->route('employee.show',$request->emp_id)
              ->with('success','Details updated successfully.');
            }
        }
        else
        {
            $OfficialDetail = EmployeOfficialDetail::find($EmployeOfficialDetail->id);
            $OfficialDetail->employee_id = $request->emp_id;
            $OfficialDetail->shift = $shift;
            $OfficialDetail->salary = $salary;
            $OfficialDetail->pl = $pl;
            $OfficialDetail->wfh = $wfh;
            if($OfficialDetail->update())
            {
              return redirect()->route('employee.show',$request->emp_id)
              ->with('success','Details updated successfully.');
            }
        }

    }

    //update employee bank details
    public function bank_detail(Request $request)
    {

        $this->validate($request,[
            'bank_name' => 'required',
            'bank_account_holder' => 'required|string|max:15',
            'bank_account_number' => 'required|digits_between:10,16',
            'ifsc_code' => 'required'
        ]);

        $Bankdetail = Bankdetail::where('emp_id', '=',  $request->emp_id)->first();
        if ($Bankdetail === null)
        {
            $Bankdetail = new Bankdetail;
            $Bankdetail->emp_id = $request->emp_id;
            $Bankdetail->bank_name = $request->bank_name;
            $Bankdetail->bank_account_holder = $request->bank_account_holder;
            $Bankdetail->bank_account_number = $request->bank_account_number;
            $Bankdetail->ifsc_code = $request->ifsc_code;
            if($Bankdetail->save())
            {
                return redirect()->route('employee.show',$request->emp_id)
                ->with('success','Bank detail updated successfully.');
            }
        }
        else
        {
            $Bankdetail = Bankdetail::find($Bankdetail->id);
            $Bankdetail->emp_id = $request->emp_id;
            $Bankdetail->bank_name = $request->bank_name;
            $Bankdetail->bank_account_holder = $request->bank_account_holder;
            $Bankdetail->bank_account_number = $request->bank_account_number;
            $Bankdetail->ifsc_code = $request->ifsc_code;
            if($Bankdetail->update())
            {
                return redirect()->route('employee.show',$request->emp_id)
                ->with('success','Bank detail updated successfully.');
            }
        }
    }

    //get single employee record
    public function show(Request $request,$id){

      $userid = Auth::user()->id;
      if(Helpers::checkPermission($userid, $modelName ="Employee" , "read")){
        $Employee = Employee::where('users.id',$id)->where('users.deleted_at','=',NULL)->orderBy('id','DESC')
                    ->join('role_type_users','role_type_users.id','=','users.role_id')
                    ->leftjoin('users as mainuser', 'mainuser.id', '=', 'users.team_leader_id')
                    ->leftjoin('users as AddedUser', 'AddedUser.id', '=', 'users.added_by')
                    ->join('departments', 'departments.id', '=', 'users.department_id')
                    ->join('position_types', 'position_types.id', '=', 'users.designation_id')
                    ->select('users.*','role_type_users.role_type as user_type','departments.department as departmentname','position_types.position as designation')
                    ->selectRaw('CONCAT(mainuser.firstname, " ",mainuser.middlename," ", mainuser.lastname) as teamleader')
                    ->selectRaw('CONCAT(AddedUser.firstname, " ",AddedUser.middlename," ", AddedUser.lastname) as addedby')
                    ->first();

        $UserAdditionInfo = UserAdditionInfo::where('employee_id',$id)->first();
        $bankDetails = Bankdetail::where('emp_id',$id)->first();
        $FamilyInfo = FamilyInfo::where('employee_id',$id)->where('deleted_at','=',NULL)->get();

        $Documentss = Document::where('emp_id',$id)->get();
        $Document = array();
        if(count($Documentss)>0)
        {
          foreach($Documentss as $doc)
          {
              $Document[$doc->type] = $doc;
          }
        }

        $departments = Department::all();
        $already_exist_employee = Employee::where('users.deleted_at',NULL)->join('role_type_users','role_type_users.id','=','users.role_id')->where('users.role_id','=','5')->orWhere('users.role_id','=','1')->select('users.*','role_type_users.role_type')->get();
        $roles = Role::all();
        $shift = DB::table('shift')->where('status', 1)->get();
        $EmployeOfficialDetail = EmployeOfficialDetail::where('employee_id', '=',  $id)
                                ->leftjoin("shift","shift.id",'=',"employee_official_details.shift")
                                ->select('employee_official_details.*','shift.shift as shiftname','shift.shift_from','shift.shift_to')
                                ->first();

        return view('Admin.employee.show',compact("EmployeOfficialDetail","shift","departments","Document","FamilyInfo","already_exist_employee","Employee","roles","UserAdditionInfo","bankDetails"));
      }
      else{
            return redirect('/permission-denied');
      }
    }

    //function to get all employee record
    public function list(Request $request){

      // echo "string";die;
      $userid = Auth::user()->id;

       if(Helpers::checkPermission($userid, $modelName ="Employee" , "read")){
        $Employees = Employee::where('users.deleted_at','=',NULL)->orderBy('users.id','DESC')
                        ->join('role_type_users','role_type_users.id','=','users.role_id')
                        ->leftjoin('users as AddedUser', 'AddedUser.id', '=', 'users.added_by')
                        // ->select('users.*','role_type_users.role_type as user_type',DB::raw("CONCAT('AddedUser.name','AddedUser.name','AddedUser.name') AS addedby"))
                        ->select('users.*','role_type_users.role_type as user_type','AddedUser.firstname as addedfirstname','AddedUser.middlename as addedmiddlename','AddedUser.lastname as addedlastname')
                        //->selectRaw('CONCAT(AddedUser.firstname," ",AddedUser.middlename," ", AddedUser.lastname) as addedby')
                        ->where('users.id', '!=', auth()->id());

        if(Auth::user()->role_id !=1 && Auth::user()->role_id!=3)
        {
            $Employees->where('users.team_leader_id',$userid);
        }
        //$Employees->orderBy('users.created_at','desc')->get();
        $Employees->get();

        //use datatable for list
        if ($request->ajax()) {
                return Datatables::of($Employees)
                ->addIndexColumn()
                ->addColumn('fullname', function ($result) {

                  if($result->job_id == '')
                  {
                    return $result->firstname.' '.$result->middlename.' '.$result->lastname;
                  }
                  else
                  {
                    return '<a href="'.route('posts.show', $result->job_id).'" title="View Job">'.$result->firstname.' '.$result->middlename.' '.$result->lastname.'</a>';
                  }
                })
                ->addColumn('email', function ($result) {
                    return ($result->email!='')?$result->email:$result->personal_email;
                })
                ->addColumn('user_type', function ($result) {
                    return $result->user_type;
                })
                ->addColumn('added_by', function ($result) {
                    //return $result->addedby;
                    if($result->addedmiddlename=='')
                    {
                      return $result->addedfirstname.' '.$result->addedlastname;
                    }
                    else
                    {
                        return $result->addedfirstname.' '.$result->addedmiddlename.' '.$result->addedlastname;
                    }

                })
                ->addColumn('status', function ($result) {
                  $userid = Auth::user()->id;
                  if(Helpers::checkPermission($userid, $modelName ="Employee" , "write")){
                    if($result->status == 1)
                    {
                        return  '<button class="btn btn-success status_change btn-sm" type="button" onclick="return changeStatus1(0,\'Inactive\','.$result->id.')">Active</button>';
                    }else
                    {
                        return '<button class="btn btn-warning status_change btn-sm" onclick="return changeStatus1(1,\'Active\','.$result->id.')" type="button" style="color:#fff;">Inactive</button>';
                    }
                  }
                  else{
                    if($result->status == 1)
                    {
                        return 'Active';
                    }else
                    {
                        return 'InActive';
                    }
                  }
                })
                ->addColumn('action', function ($result) {
                  $userid = Auth::user()->id;
                   $button = '';

                   //if(Helpers::checkPermission($userid, $modelName ="Employee" , "write") || Helpers::checkPermission($userid, $modelName ="Employee" , "read")){
                   if(Helpers::checkPermission($userid, $modelName ="Employee" , "write")){
                    $button.='<a href="'.route('employee.show', $result->id).'" class="btn btn-outline-primary mx-1 btn-sm" data-id="'.$result->id.'"><i class="fa fa-eye"></i> View/Edit</a>';

                  }

                  if(Helpers::checkPermission($userid, $modelName ="Employee" , "delete")){
                      $button.='<a class="btn btn-outline-danger btn-sm" onclick="return confirm(';
                      $button.="'Are you sure?'";
                      $button.=');" style="margin:3px;" href="'.route('employee.delete', $result->id).'"><i class="fa fa-trash"></i> Delete</a>';

                  }
                  if($button=='')
                  {
                    return '-';
                  }
                  else {
                    return $button;
                  }


                      /*  $button = '';
                        $button.='<a href="'.route('employee.show', $result->id).'" class="btn btn-outline-primary mx-1" data-id="'.$result->id.'"><i class="fa fa-eye"></i> View/Edit</a>';
                        $button.='<a class="btn btn-outline-danger" onclick="return confirm(';
                        $button.="'Are you sure?'";
                        $button.=');" style="margin:3px;" href="'.route('employee.delete', $result->id).'"><i class="fa fa-trash"></i> Delete</a>';
                        return $button;*/
                })
                ->escapeColumns([])
                ->make(true);
        }

        return view('Admin.employee.view',compact('Employees'));
      }
      else{
          return redirect('/permission-denied');
        }
    }

    //function to add and update employee information
    public function store(Request $request){

        if($request->email!='')
        {
          if($request->employee_id == '')
          {
            $this->validate($request,[
                'firstname' => 'required|string|min:3|max:20',
                'lastname' => 'required',
                'personal_email' => 'required|email',
                'email' => 'required|email',
                'password' => 'required|min:6|max:15',
                'mobile' => 'required',
                'gender' => 'required',
                'role_id' => 'required',
                'date_of_birth' => 'required',
                'date_of_joining' => 'required',
                'department_id' => 'required',
                'designation_id' => 'required',
                'status' => 'required',
            ]);
          }
          else
          {
            $this->validate($request,[
                'firstname' => 'required|string|min:3|max:20',
                'lastname' => 'required',
                'personal_email' => 'required|email',
                'email' => 'required|email',
                //'password' => 'required|min:6|max:15',
                'mobile' => 'required',
                'gender' => 'required',
                'role_id' => 'required',
                'date_of_birth' => 'required',
                'date_of_joining' => 'required',
                'department_id' => 'required',
                'designation_id' => 'required',
                'status' => 'required',
            ]);
          }

        }
        else {

          $this->validate($request,[
              'firstname' => 'required|string|min:3|max:20',
              'lastname' => 'required',
              'personal_email' => 'required|email',
            /*  'email' => 'required|email',
              'password' => 'required',*/
              'mobile' => 'required',
              'gender' => 'required',
              'role_id' => 'required',
              'date_of_birth' => 'required',
              'date_of_joining' => 'required',
              'department_id' => 'required',
              'designation_id' => 'required',
              'status' => 'required',
          ]);
        }



        if($request->employee_id == '')
        {

            $user = Employee::where('personal_email', '=',  $request->personal_email)->where('deleted_at','=',NULL)->first();
            if ($user === null) {
                $Employee = new Employee;
              //  $Employee->name = $request->firstname.' '.$request->middlename.' '.$request->lastname;
                $Employee->firstname = $request->firstname;
                $Employee->middlename = $request->middlename;
                $Employee->lastname = $request->lastname;
                $Employee->personal_email = $request->personal_email;
                $Employee->email = $request->email;
                $Employee->password = ($request->password != '')?Hash::make($request->password):'';
                $Employee->mobile = $request->mobile;
                $Employee->gender = $request->gender;
                $Employee->role_id = $request->role_id;
                $Employee->team_leader_id = ($request->team_leader_id!='')? $request->team_leader_id: 1;
                $Employee->date_of_birth = $request->date_of_birth;
                $Employee->date_of_joining = $request->date_of_joining;
              //  $Employee->experience = $request->experience;
                $Employee->department_id = $request->department_id;
                $Employee->designation_id = $request->designation_id;
                $Employee->status = $request->status;
                $Employee->added_by = $request->rec_id;
                $Employee->job_id = $request->job_id;
                $Employee->candidate_id = $request->candidate_id;
                if($Employee->save()){

                    //save data to bank detail
                    $Bankdetail = new Bankdetail;
                    $Bankdetail->emp_id = $Employee->id;
                    $Bankdetail->save();

                    //save data to user additional info
                    $UserAdditionInfo = new UserAdditionInfo;
                    $UserAdditionInfo->employee_id = $Employee->id;
                    $UserAdditionInfo->save();

                    if($request->candidate_id!='')
                    {
                        $candidates = JobSeeker::findOrfail($request->candidate_id);
                        $candidates->is_moved_as_employee = 1;
                        $candidates->update();
                    }

                    if($request->email=='')
                    {

                      $setpassword_link = url('/setpassword/'.base64_encode($Employee->id));

                      $mailtemplate = Mailtemplate::where('template_key','add_employee')->first();
                      $mailcontent = $mailtemplate->template_html;

                      $mailcontent = str_replace('[Name]',$request->firstname.' '.$request->middlename.' '.$request->lastname,$mailcontent);
                      $mailcontent = str_replace('[Set Password]','<a  href="'.$setpassword_link.'"><button class="reset-buttn">Set Password</button></a>',$mailcontent);
                      $mailcontent = str_replace('[Set Password Link]',$setpassword_link,$mailcontent);

                      $sendmail = new Sendmail;
                      $sendmail->subject = $mailtemplate->subject;
                      $sendmail->mailcontent = $mailcontent;
                      $sendmail->from_address = EMAIL_FROM_ADDRESS;
                      $sendmail->to_address = $request->personal_email;
                      $sendmail->save();

                      /*$message = array(
                        'employee_name'=>$request->firstname.' '.$request->middlename.' '.$request->lastname,
                        'employee_id'=>base64_encode($Employee->id),
                        'personal_email'=>$request->personal_email,
                        'setPasswordURL' => url('/setpassword/'.base64_encode($Employee->id))
                      );


                      Mail::send('Admin.employee.mail', $message, function ($m) use ($message){
                        $m->from('info@gameking11.com', "RGINFOTECH");
                        $m->to($message['personal_email'])->subject("Welcome to RG Infotech");
                      });
                      */
                    }
                    return redirect()->route('employee.view')
                    ->with('success','New Employee add successfully.'); //redirect with message
                }
                else{
                    return redirect()->route('employee.create')
                    ->with('error','Employee not added.');
                }
            }
            else
            {
                return redirect()->route('employee.create')->withInput()->with('error', 'Employee already exists with this email.');
            }
        }
        else
        {
          $user = Employee::where('personal_email', '=',  $request->personal_email)->where('id','!=',$request->employee_id)->where('deleted_at','=',NULL)->first();

          if ($user === null) {
                  $Employee = Employee::find($request->employee_id);
                //  $Employee->name = $request->firstname.' '.$request->middlename.' '.$request->lastname;
                  $Employee->firstname = $request->firstname;
                  $Employee->middlename = $request->middlename;
                  $Employee->lastname = $request->lastname;
                  $Employee->personal_email = $request->personal_email;
                  $Employee->email = $request->email;
                  $Employee->mobile = $request->mobile;
                  $Employee->gender = $request->gender;
                  $Employee->role_id = $request->role_id;
                  $Employee->team_leader_id = ($request->team_leader_id!='')? $request->team_leader_id: 1;
                  $Employee->date_of_birth = $request->date_of_birth;
                  $Employee->date_of_joining = $request->date_of_joining;
                //  $Employee->experience = $request->experience;
                  $Employee->department_id = $request->department_id;
                  $Employee->designation_id = $request->designation_id;
                  $Employee->status = $request->status;
                  $Employee->added_by = $request->rec_id;
                  $Employee->date_of_leaving = $request->date_of_leaving;

                  $UserAdditionInfo = UserAdditionInfo::where('employee_id', '=',  $request->employee_id)->first();
                  if ($UserAdditionInfo === null)
                  {
                      $UserAdditionInfo = new UserAdditionInfo;
                      $UserAdditionInfo->employee_id = $request->employee_id;
                      $UserAdditionInfo->alternate_mobile = $request->alternate_mobile;
                      $UserAdditionInfo->marital_status = $request->marital_status;
                      $UserAdditionInfo->c_address = $request->c_address;
                      $UserAdditionInfo->c_city = $request->c_city;
                      $UserAdditionInfo->c_state = $request->c_state;
                      $UserAdditionInfo->c_pincode = $request->c_pincode;
                      $UserAdditionInfo->c_country = $request->c_country;
                      $UserAdditionInfo->c_district = $request->c_district;
                      $UserAdditionInfo->p_address = $request->p_address;
                      $UserAdditionInfo->p_city = $request->p_city;
                      $UserAdditionInfo->p_state = $request->p_state;
                      $UserAdditionInfo->p_pincode = $request->p_pincode;
                      $UserAdditionInfo->p_country = $request->p_country;
                      $UserAdditionInfo->p_district = $request->p_district;
                      $UserAdditionInfo->save();
                  }
                  else
                  {
                      $UserAdditionInfo = UserAdditionInfo::find($UserAdditionInfo->id);
                      $UserAdditionInfo->employee_id = $UserAdditionInfo->employee_id;
                      $UserAdditionInfo->alternate_mobile = $request->alternate_mobile;
                      $UserAdditionInfo->marital_status = $request->marital_status;
                      $UserAdditionInfo->c_address = $request->c_address;
                      $UserAdditionInfo->c_city = $request->c_city;
                      $UserAdditionInfo->c_state = $request->c_state;
                      $UserAdditionInfo->c_pincode = $request->c_pincode;
                      $UserAdditionInfo->c_country = $request->c_country;
                      $UserAdditionInfo->c_district = $request->c_district;
                      $UserAdditionInfo->p_address = $request->p_address;
                      $UserAdditionInfo->p_city = $request->p_city;
                      $UserAdditionInfo->p_state = $request->p_state;
                      $UserAdditionInfo->p_pincode = $request->p_pincode;
                      $UserAdditionInfo->p_country = $request->p_country;
                      $UserAdditionInfo->p_district = $request->p_district;
                      $UserAdditionInfo->update();
                  }
                  $family_infoId = $request->family_infoId;
                  $family_name = $request->family_name;
                  $family_relationship = $request->family_relationship;
                  $family_phone = $request->family_phone;
                  for($i=0;$i<count($family_infoId);$i++)
                  {
                      if($family_infoId[$i]=='')
                      {
                          $FamilyInfo = new FamilyInfo;
                          $FamilyInfo->employee_id = $request->employee_id;
                          $FamilyInfo->family_name = $family_name[$i];
                          $FamilyInfo->family_relationship = $family_relationship[$i];
                          $FamilyInfo->family_phone = $family_phone[$i];
                          $FamilyInfo->save();
                      }
                      else
                      {
                          $FamilyInfo = FamilyInfo::find($family_infoId[$i]);
                          $FamilyInfo->employee_id = $request->employee_id;
                          $FamilyInfo->family_name = $family_name[$i];
                          $FamilyInfo->family_relationship = $family_relationship[$i];
                          $FamilyInfo->family_phone = $family_phone[$i];
                          $FamilyInfo->update();
                      }
                  }

                  if($Employee->update()){
                      return redirect()->route('employee.show',$request->employee_id)
                      ->with('success','Employee updated successfully.');
                  }
                  else{
                      return redirect()->route('employee.edit')
                      ->with('error','Employee not update.');
                  }
            }
            else
            {

                return redirect()->route('employee.show',$request->employee_id)->withInput()->with('error', 'Employee already exists with this email.');
            }
        }
    }

    //function to change employe status (active/Inactive)
    public function changeStatusEmployee(Request $request)
    {
        $status_val =  $request->status_val;
        $employee_id = $request->employee_id;
        $Employee = Employee::find($employee_id);
        $status = array(
            'status'=>$status_val
        );
        if(!empty($Employee)){
            Employee::where('id',$employee_id)->update($status);
            return response()->json(['success'=>'Employee status changed.']);
        }else{
            return response()->json(['error'=>'Employee status not changed.']);
        }
    }

    //Delete family inforamtion of an employee
    public function deleteFamilyInfo(Request $request)
    {
        $remove_id = $request->remove_id;

        $FamilyInfo = FamilyInfo::find($remove_id);
        //$FamilyInfo->deleted_at = date('Y-m-d H:i:s');
        if($FamilyInfo->delete())
        {
          return response()->json(['success'=>'Family Detail Deleted.']);
        }
        else {
            return response()->json(['error'=>'Family Detail not Deleted.']);
        }
    }

    //Delete inforamtion of an employee
    public function delete($id){
      $userid = Auth::user()->id;
      if(Helpers::checkPermission($userid, $modelName ="Employee" , "delete")){
        $delete = Employee::find($id);
        $delete->deleted_at = date('Y-m-d H:i:s');
        if($delete->update()){
            return redirect()->route('employee.view')
            ->with('success','Employee deleted successfully.');
        }
        else{
            return redirect()->route('employee.view')
            ->with('error','Employee not deleted.');
        }
      }
      else{
            return redirect('/permission-denied');
      }
    }

    //get designtion list based on department
    public function getDesignationByDepartment(Request $request)
    {
        $request->department_id;
        $all_designation = PositionType::where('depart_id',$request->department_id)->get();
        return $all_designation;
    }

    public function setpassword(Request $request,$id)
    {

        $employee_id =  base64_decode($id);
        $setpassword_val = Employee::where('id',$employee_id)->select('setpassword')->first();
        if($setpassword_val->setpassword==0)
        {
          return view('Admin.employee.setpassword',compact('employee_id'));
        }
        else {
          return redirect()->route('login');
        }
    }
    public function changeNewPassword(Request $request)
    {
        $this->validate($request,[
            'newPassword' => 'required|min:6|max:15',
            'confirmPassword' => 'required|same:newPassword'
        ]);
         Hash::make($request->confirmPassword);
        $request->employee_id;

        $Employee = Employee::find($request->employee_id);
        $Employee->password = Hash::make($request->confirmPassword);
        $Employee->setpassword = 1;

        if($Employee->update()){
            return redirect()->route('login')->with('success','New Password set successfully.');;
        }
    }

    public function resetpassword(Request $request,$id)
    {

        $employee_id =  base64_decode($id);
        return view('Admin.employee.resetpassword',compact('employee_id'));

    }

    public function changeResetPassword(Request $request)
    {
        $this->validate($request,[
            'newPassword' => 'required|min:6|max:15',
            'confirmPassword' => 'required|same:newPassword'
        ]);

        $Employee = Employee::find($request->employee_id);
        $Employee->password = Hash::make($request->confirmPassword);
        $Employee->setpassword = 1;

        if($Employee->update()){
          return redirect()->route('employee.show',$request->employee_id)
          ->with('success','Password reset successfully.');
        }
    }
}
