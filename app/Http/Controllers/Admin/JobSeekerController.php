<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use DB;
use Mail;
use Auth;
use DataTables;
use Carbon;
use App\Models\Admin\JobSeeker;
use App\Models\Admin\Jobs;
use App\Models\Interviewschedule;
use App\Models\Admin\Employee;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use App\Mail\SendMailable;
use App\Helpers\Helpers;
use File;
use Session;


class JobSeekerController extends Controller{

    public function list(Request $request, $id=''){
        $userid = Auth::user()->id;
        if ($id == '') {
            $job_seeker = JobSeeker::join('jobs', 'job_seekers.job_id', '=', 'jobs.id')
                ->select('job_seekers.*','jobs.job_title as Job_title')
                ->where('job_seekers.deleted_at', NULL)
                ->get();
        }else {
            $job_seeker = JobSeeker::join('jobs', 'job_seekers.job_id', '=', 'jobs.id')
                ->select('job_seekers.*','jobs.job_title as Job_title')
                ->where('job_seekers.deleted_at', NULL)
                ->where('job_seekers.job_id', $id)
                ->get();
        }

        if ($request->ajax()) {

                return Datatables::of($job_seeker)
               ->addIndexColumn()
               ->addColumn('job_id', function ($result) {
                   return $result->Job_title;
               })
               ->addColumn('interview_status', function ($result) {
                 $interview_count = Interviewschedule::where('job_seeker_id',$result->id)->where('deleted_at',Null)->get();

                 $InterStatus = '';
                           foreach ($interview_count as $key => $value) {
                             if(!empty($value->interview_status)){
                                // echo '<pre>';print_r($value);
                                if ($value->interview_status == 'pending') {
                                       $InterStatus .= '<span class="p-2 text-info" title="Pending"><i class="fa fa-check"></i></span>';
                                    }
                                    if ($value->interview_status == 'selected') {
                                       $InterStatus .= '<span class="p-2 text-success" title="Selected"><i class="fa fa-check"></i></span>';
                                    }
                                    if ($value->interview_status == 'rejected') {
                                       $InterStatus .= '<span class="p-2 text-danger" title="Rejected"><i class="fa fa-check"></i></span>';
                                    }
                                    if ($value->interview_status == 'on_hold') {
                                       $InterStatus .= '<span class="p-2 text-warning" title="On Hold"><i class="fa fa-check"></i></span>';
                                    }
                                  }
                                  else{
                                      $InterStatus .= 'not found status';
                                  }
                                }


                 return '<span style="display: inline-block;">'.$InterStatus.'</span>';
               })
               ->addColumn('updated_by', function ($result) {
                    return $result->Job_title;
                })
               ->addColumn('job_id', function ($result) {
                      $Job_title = '';
                          $Job_title.='<a href="'.route('posts.show', $result->job_id).'" title="View Jobs Details"  class="view  " ><span style="color:#2181bc;"><u>'. $result->Job_title .'</u></span></a>';
                      return $Job_title;
                })
                ->addColumn('created_at', function ($result) {
                    return date('M d Y h:i', strtotime($result->created_at));
                })
                ->addColumn('updated_at', function ($result) {
                    return date('M d Y h:i', strtotime($result->updated_at));
                })
               ->addColumn('status', function ($result) {
                        $statuschange = '';
                            if ($result->status == 'pending') {
                                $statuschange .= 'Pending';
                            } elseif ($result->status == 'on_hold') {
                                $statuschange .= 'On Hold';
                            } elseif ($result->status == 'selected') {
                                $statuschange .= 'Selected';
                            } elseif ($result->status == 'confirm') {
                                $statuschange .= 'Confirm';
                            } elseif ($result->status == 'rejected') {
                                $statuschange .= 'Rejected';
                            }
                   return $statuschange;
               })
               ->addColumn('is_seen', function ($result) {
                        $seenStatus = '';
                            if ($result->is_seen == 0) {
                                $seenStatus .= 'Unreviewed';
                            } elseif ($result->is_seen == 1) {
                                $seenStatus .= 'Reviewed';
                            }
                   return $seenStatus;
               })
               ->addColumn('experience', function ($result) {
                        $experience = $result->experience. " yr";

                   return $experience;
               })
               ->addColumn('action', function ($result) {
                    $button = '';
                    $button.='<a href="'.route('job_seeker.show', $result->id).'" class="btn btn-sm btn-outline-info view mx-1"><i class="fa fa-eye"> </i> View</a>';
                    $button.='<a href="'.route('job_seeker.schedule', $result->id).'" style="margin:3px;" class="btn btn-sm btn-outline-primary"><i class="fa fa-calendar"></i> Interview</a>';
                    $button.='<a href="'.route('job_seeker.editjobapplicants', $result->id).'" style="margin:3px;" class="btn btn-sm btn-outline-primary"><i class="fa fa-pencil"></i> Edit</a>';
                    if($result->status == 'confirm')
                    {
                      if($result->is_moved_as_employee == 0)
                      {
                        $button.='<a href="'.route('employee.create', $result->id).'" style="margin:3px;" class="btn btn-sm btn-outline-success"><i class="fa fa-user"></i> Move To Employee</a>';
                      }
                      else
                      {
                        $button.='<button type="button" style="margin:3px;" class="btn btn-sm btn-outline-success"><i class="fa fa-user"></i> Moved As Employee</button>';
                      }
                    }

                    return $button;
               })
                ->escapeColumns([])
                ->make(true);
        }
        return view('Admin.job_seekers.view',compact('job_seeker','id'));
    }

        public function show(Request $request){

            $jobSeekerData = JobSeeker::join('jobs','jobs.id','=','job_seekers.job_id')
            ->where('job_seekers.id',$request->id)
            ->select('job_seekers.*','jobs.job_title')
            ->first();
            $interview_count = Interviewschedule::where('job_seeker_id',$jobSeekerData->id)->get();

            if($jobSeekerData){

                $seenStatus = JobSeeker::where('id',$request->id)->update(['is_seen'=>'1']);
                return response()->view('Admin.job_seekers.viewJobApplicant',compact('jobSeekerData','interview_count'));
            }else{
                return response()->view('Admin.job_seekers.view')->with('error','data not found.');
            }

        }
        public function ChangeJobSeekerStatus(Request $request,$status,$id){
            $role = JobSeeker::find($id);
            $role->status = $status;
            $res = $role->save();
            if($res){
                return redirect()->route('job_seeker.view')
                ->with('success','Job Seeker Status Updated successfully.');
            }
            else{
                return redirect()->route('role.view')
                ->with('success','Job Seeker Status Updated not successfully.');
            }

         }
         public function schedule(Request $request,$id){
              $job_seeker_id = $id;
              if ($job_seeker_id) {
                  $res = DB::table('admin_notification')->where('job_seeker_id', $id)->update(['is_read'=>1]);
              }
              $todaty =  Carbon\Carbon::now()->format('Y/m/d h:i','Asia/Kolkata');
              // dd($todaty);
              $interviewSehdule = Interviewschedule::join('users', 'interview_schedule.interviewer_id', '=', 'users.id')
              ->join('job_seekers', 'interview_schedule.job_seeker_id', '=', 'job_seekers.id')
              ->join('round_types', 'interview_schedule.round_type_id', '=', 'round_types.id')
              ->select(
                  'interview_schedule.*',
                  'users.firstname as interviewer_firstname',
                  'users.middlename as interviewer_middlename','users.id as interviewer_id',
                  'users.lastname as interviewer_lastname',
                  'job_seekers.firstname','job_seekers.lastname',
                  'round_types.type as interview_type'
              )
              ->where('interview_schedule.job_seeker_id', $id)
              ->orderBy('updated_at', 'DESC')
              ->get();
              $jobSeekerData = JobSeeker::join('jobs','jobs.id','=','job_seekers.job_id')
              ->where('job_seekers.id',$id)
              ->select('job_seekers.*','jobs.job_title','jobs.department','jobs.designation')
              ->first();
              $interview_status = DB::table('interview_schedule')
              ->where('job_seeker_id',$id)
              // ->where('interview_status','rejected')
              ->orderBy('updated_at','desc')
              ->first();
              // dd($interview_status->interview_status);
              $interviewRounds = DB::table('round_types')
              ->where('round_types.deleted_at',NULL)
              ->where('round_types.status',1)
              ->get();
              $interviewersData = DB::table('round_types')->where('deleted_at',NULL)->first();
              $interviewersName = explode(',', !empty($interviewersData->interviewers_id) ? $interviewersData->interviewers_id : '');
              $interviewers = DB::table('users')->WhereIn('id', $interviewersName)->get();

              return view('Admin.job_seekers.schedule',compact('interviewSehdule','interviewRounds','interviewers','jobSeekerData','job_seeker_id','interview_status','todaty'));
         }
         public function edit(Request $request){
            // dd($reuquest->all());
            $editInterviewSehdule = Interviewschedule::findOrfail($request->id);
            // dd('here');
            if ($editInterviewSehdule) {
                return response()->json(['status'=>200, 'success'=>'Interview Schedule Updated successfully.','data'=>$data]);
            }
            else {
                return response()->json(['status'=>404, 'error'=>'Role not found.']);
            }
        }
        public function update(Request $request){
            $jobdata = Jobs::where('jobs.status', '=', 1)->first();
            $job_seeker = Jobseeker::where('id',$jobdata->job_id)->get();

           $esixt = DB::table('interview_schedule')
                    ->where('job_seeker_id', $request->job_seeker_id)
                    ->where('round_type_id',$request->round_type_id)
                    ->count();

            $validated = $request->validate([
                'job_seeker_id' => 'required',
                'interviewer_id' => 'required',
                'interview_time' => 'required|after:today',
                'interview_mode' => 'required',
            ]);

            if($esixt > 0){
                return json_encode(['error'=>'The Interview round is already exits.']);
            }

            $interviewRound = new Interviewschedule;
            $interviewRound->job_seeker_id = $request->job_seeker_id;
            $interviewRound->interviewer_id = $request->interviewer_id;
            $interviewRound->interview_time = $request->interview_time;
            $interviewRound->round_type_id = $request->round_type_id;
            $interviewRound->status = '1';
            $interviewRound->feedback = '';

            if($interviewRound->update()){
                return redirect()->route('job_seeker.view')
                ->with('success','Interview Round added successfully.');
            }
            else{
                return redirect()->route('job_seeker.create')
                ->with('error','interview Round not added.');
            }
            return view('admin.job_seekers.job_applicants',compact('jobdata','job_seeker'));
        }
        public function jobApplicants(Request $request){
          $todaty =  Carbon\Carbon::now()->format('Y-m-d h:i:s a','Asia/Kolkata');
            $jobdata = Jobs::where('jobs.start_date', '<=','jobs.end_date' )
            ->join('departments','jobs.department','=','departments.id')
            ->join('salary','jobs.salary','=','salary.id')
            ->select('departments.department as department_name','salary.salary_from as minsalary','salary.salary_to as maxsalary','jobs.*')
            ->where('jobs.status','=','1')
            ->where('end_date','>',$todaty)
            ->get();
            $count = $jobdata->count();
            return view('Admin.job_seekers.job_applicants',compact('jobdata','count'));
        }

        public function create(Request $request){

          // dd($request->all());
            $job_seeker = Jobseeker::where('id',$request->job_seeker_id)->first();
            $jobseekerrCheckValidation = Interviewschedule::where([['job_seeker_id',$request->job_seeker_id],['interview_status','pending']])->count();
            $jobseekerrCheckValidationHold = Interviewschedule::where([['job_seeker_id',$request->job_seeker_id],['interview_status','on_hold']])->count();
            $interviewer_name = DB::table('users')->where('id', $request->interviewer_id)->first();
            $interview_round = DB::table('round_types')->where('id', $request->round_type_id)->first();
            // dd($interview_round);
            if($jobseekerrCheckValidation > 0){
                return json_encode(['danger'=>'The Interview round is already under-proess.']);
            }elseif($jobseekerrCheckValidationHold > 0){
                return json_encode(['danger'=>'The Interview round is already on hold.']);
            }else{
            $esixt = DB::table('interview_schedule')->where('job_seeker_id', $request->job_seeker_id)->where('round_type_id',$request->round_type_id)->count();
            $Job_seekerData = DB::table('job_seekers')->where('id', $request->job_seeker_id)->first();
            $validated = $request->validate([
                'job_seeker_id' => 'required',
                'interviewer_id' => 'required',
                'interview_time' => 'required|after:today',
                'round_type_id' => 'required',
                'interview_mode' => 'required',
            ]);

            if($esixt > 0){
                return json_encode(['error'=>'The Interview round is already exits.']);
            }

            $interviewRound = new Interviewschedule;
            $interviewRound->job_seeker_id = $request->job_seeker_id;
            $interviewRound->interviewer_id = $request->interviewer_id;
            $interviewRound->interview_time = $request->interview_time;
            $interviewRound->round_type_id = $request->round_type_id;
            $interviewRound->interview_mode = $request->interview_mode;
            $interviewRound->interview_status = $request->interview_status;
            $interviewRound->is_send = (($request->is_send !== 1) ? $request->is_send == '' : '0');
            $interviewRound->feedback = '';
            if($interviewRound->save()){
              // dd($interviewRound);
                $messege['employee_id'] = $interviewRound->interviewer_id;
                $messege['job_seeker_id'] = $interviewRound->job_seeker_id;
                $messege['firstname'] = $Job_seekerData->firstname;

                $companyDetails = DB::table('company_details')->where('id', 1)->first();
                $message = array(
                    'email' => $job_seeker->email,
                    'name' => $Job_seekerData->firstname." ".$Job_seekerData->lastname,
                    'interview_time' => $request->interview_time,
                    'company_short_name' =>$companyDetails->short_name,
                    'address' => $companyDetails->address,
                    'phone' => $companyDetails->phone,
                    'companyemail' => $companyDetails->email,
                    'mode' => $request->interview_mode,
                    'interview_round' => $interview_round->type,
                    'interviewer_name' => $interviewer_name->firstname." ".$interviewer_name->lastname
                );
                if ($request->is_send == 1) {
                  Mail::send('Admin.job_seekers.interviewAddMail', $message, function ($m) use ($message){

                    $m->from($message['companyemail'], $message['company_short_name']);
                    $m->to($message['email'])->subject("Interview Schedule");

                  });
                }
                return redirect()->route('job_seeker.view')
                ->with('success','Interview Round added successfullyy.');
            }
            else{
                return redirect()->route('job_seeker.create')
                ->with('error','interview Round not added.');
            }
        }
            return view('admin.job_seekers.job_applicants',compact('jobdata','job_seeker'));
        }

        public function editModel(Request $req){
            // dd($req->all());


            $data = DB::table('interview_schedule')
            ->join('users','users.id','=','interview_schedule.interviewer_id')
            ->join('round_types','round_types.id','=','interview_schedule.round_type_id')
            ->where('interview_schedule.id',$req->id)
            ->select('interview_schedule.*','users.firstname','users.lastname','round_types.type')
            ->first();



            // $interdata = db::table('interview_schedule')->where('id',$req->id)->first();
            // dd($data);
            return response()->json(['data' => $data]);
        }
        public function scheduleUpdate(Request $request){

            $job_seeker = Jobseeker::where('id',$request->job_seeker_id)->first();
            $interviewer_name = DB::table('users')->where('id', $request->interviewer_id)->first();
            $interview_round = DB::table('round_types')->where('id', $request->round_type_id)->first();
            $Job_seekerData = DB::table('job_seekers')->where('id', $request->job_seeker_id)->first();
            $esixt = DB::table('interview_schedule')->where('job_seeker_id','!==', $request->job_seeker_id)->where('round_type_id',$request->round_type_id)->count();
            $validated = $request->validate([
                'interviewer_id' => 'required',
                'interview_time' => 'required',
                'round_type_id' => 'required|unique:interview_schedule,id,'.$request->round_type_id,
                'interview_mode' => 'required',
                'interview_status' => 'required',
            ]);
            if($esixt > 0){
                return json_encode(['error'=>'The Interview round is already exits.']);
            }
            $interviewRound = Interviewschedule::find($request->id);
            $interviewRound->interviewer_id = $request->interviewer_id;
            $interviewRound->interview_time = $request->interview_time;
            $interviewRound->interview_mode = $request->interview_mode;
            $interviewRound->interview_status = $request->interview_status;
            $interviewRound->feedback = $request->feedback;
            $interviewRound->is_send = (($request->is_send !== 1) ? $request->is_send == '' : '0');
            if($interviewRound->update()){
              $messege['employee_id'] = $interviewRound->interviewer_id;
              $messege['job_seeker_id'] = $interviewRound->job_seeker_id;
              $messege['firstname'] = $Job_seekerData->firstname;

              $companyDetails = DB::table('company_details')->where('id', 1)->first();
              if ($request->is_send == 1) {
                  $message = array(
                      'email' => $job_seeker->email,
                      'name' => $Job_seekerData->firstname." ".$Job_seekerData->lastname,
                      'interview_time' => $request->interview_time,
                      'company_short_name' =>$companyDetails->short_name,
                      'address' => $companyDetails->address,
                      'phone' => $companyDetails->phone,
                      'companyemail' => $companyDetails->email,
                      'mode' => $request->interview_mode,
                      'interview_round' => $interview_round->type,
                      'interviewer_name' => $interviewer_name->firstname." ".$interviewer_name->lastname
                  );
                  Mail::send('Admin.job_seekers.interviewUpdateMail', $message, function ($m) use ($message){

                    $m->from($message['companyemail'], $message['company_short_name']);
                    $m->to($message['email'])->subject("Interview Schedule");

                  });
              }
                return redirect()->route('job_seeker.view')->with('success','Interview Round Updated successfully.');
            }
            else{
                return redirect()->back()->with('error','interview Round not Updated.');
            }
            return view('admin.job_seekers.job_applicants',compact('jobdata','job_seeker'));

        }
        public function addJobApplicants() {
            $userid = Auth::user()->id;
            $jobs = DB::table('jobs')->where('deleted_at','=',null)->get();
            $experience = DB::table('experience')->where('deleted_at','=',null)->get();
          //  dd($experience);
            // ->selectRaw('CONCAT(users.expFromYear, " ", users.expToYear) as full_name')
            return view('Admin.job_seekers.createJobApplicants', compact('jobs','experience'));
        }

        public function editJobApplicants($id) {
            $userid = Auth::user()->id;
            $job_applicantsData = JobSeeker::findOrFail($id);
            $jobs = DB::table('jobs')->where('deleted_at','=',null)->get();

            return view('Admin.job_seekers.editJobApplicants', compact('jobs','job_applicantsData'));
      }

        public function storeJobApplicants(Request $request) {
          // dd($request->all());
            if(empty($request->post_id)){
                $this->validate($request,[
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'email' => 'required|email|unique:job_seekers,email,'.$request->post_id,
                    'mobile' => 'required|min:9|max:14|unique:job_seekers,mobile,'.$request->post_id,
                    'experience' => 'required',
                    'location' => 'required',
                    'current_ctc' => 'required | numeric',
                    'expectation' => 'required | numeric',
                    'source' => 'required',
                    'resume' => 'required|mimes:doc,docx,pdf',
                    'status' => 'required',
                    'job_id' => 'required'
                ]);
            }
            else {
                $this->validate($request,[
                  'firstname' => 'required',
                  'lastname' => 'required',
                  'email' => 'required|email|unique:job_seekers,email,'.$request->post_id,
                  'mobile' => 'required|min:9|max:14|unique:job_seekers,mobile,'.$request->post_id,
                  'experience' => 'required',
                  'location' => 'required',
                  'current_ctc' => 'required | numeric',
                  'expectation' => 'required | numeric',
                  'source' => 'required',
                  'status' => 'required',
                  'job_id' => 'required'
              ]);
            }
            if(empty($request->post_id)){
              $jobApplicant = new jobSeeker;

                if($request->hasfile('resume')){
                      $file = $request->file('resume');
                      $name = $file->getClientOriginalName();
                      $rename = str_replace(".pdf","-",$name);
                      $extension=$file->getClientOriginalExtension();
                      $destinationPath = public_path('/uploads/resume');
                      if(!File::isDirectory($destinationPath)){
                          File::makeDirectory($destinationPath, 0777, true, true);
                      }
                      $filename = $rename.time().'.'.$extension;
                      $upload = $file->move($destinationPath, $filename);
                }
                $jobApplicant->firstname = $request->firstname;
                $jobApplicant->lastname = $request->lastname;
                $jobApplicant->email = $request->email;
                $jobApplicant->mobile	 = $request->mobile;
                $jobApplicant->resume = $filename;
                $jobApplicant->experience = $request->experience;
                $jobApplicant->location = $request->location;
                $jobApplicant->current_ctc = $request->current_ctc;
                $jobApplicant->expectation = $request->expectation;
                $jobApplicant->about_job_seeker = $request->about_job_seeker;
                $jobApplicant->source = $request->source;
                $jobApplicant->status = $request->status;
                $jobApplicant->job_id = $request->job_id;
                if($jobApplicant->save()){
                    return redirect()->route('job_seeker.list')
                    ->with('success','New Job Applicant added successfully.');
                }
                else{
                    return redirect()->route('posts.create')
                    ->with('error','Job Applicant not added.');
                }
              }
              else{
                if ( $request->hasfile('resume')){

                      $file = $request->file('resume');
                      $name = $file->getClientOriginalName();
                      $rename = str_replace(".pdf","-",$name);
                      $extension=$file->getClientOriginalExtension();
                      $destinationPath = public_path('/uploads/resume');
                      $filename = $rename.time().'.'.$extension;
                      $upload = $file->move($destinationPath, $filename);
                      $jobApplicant['resume'] = $filename;

                }
                if ($request->status == 'selected'){
                  $companyDetails = DB::table('company_details')->where('id', 1)->first();
                  $message = array(
                      'name' => $request->firstname." ". $request->lastname,
                      'company_short_name' =>$companyDetails->short_name,
                      'address' => $companyDetails->address,
                      'phone' => $companyDetails->phone,
                      'companyemail' => $companyDetails->email,
                      'email' => $request->email
                  );
                  Mail::send('Admin.job_seekers.finalMial', $message, function ($m) use ($message){

                    $m->from($message['companyemail'], $message['company_short_name']);
                    $m->to($message['email'])->subject("Interview Final Update");

                  });
                }

                $jobApplicant['firstname'] = $request->firstname;
                $jobApplicant['lastname'] = $request->lastname;
                $jobApplicant['email'] = $request->email;
                $jobApplicant['mobile']	 = $request->mobile;
                $jobApplicant['experience'] = $request->experience;
                $jobApplicant['location'] = $request->location;
                $jobApplicant['current_ctc'] = $request->current_ctc;
                $jobApplicant['expectation'] = $request->expectation;
                $jobApplicant['expectation'] = $request->expectation;
                $jobApplicant['about_job_seeker'] = $request->about_job_seeker;
                $jobApplicant['source'] = $request->source;
                $jobApplicant['status'] = $request->status;
                $jobApplicant['job_id'] = $request->job_id;
                $affected = DB::table('job_seekers')->where('id', $request->post_id)->update($jobApplicant);

                if($affected == 1){
                    return redirect()->route('job_seeker.list')
                    ->with('success','Job Applicant updated successfully.');
                }
                else{
                    return redirect()->back()
                    ->with('error','Job Applicant not updated.');
                }
            }
        }
        public function getInterviewers(Request $request){
          // dd($request->all());
          $data = DB::table('round_types')->where('id', $request->id)->first();
          if(!empty($data)){
            $interviewer_id = explode(',', $data->interviewers_id);
            $interviewersData = DB::table('users')->WhereIn('id',$interviewer_id)->get();
            return response()->json($interviewersData);
          }
          else{

            return response()->json(['error'=>'not found']);
          }
        }
}
