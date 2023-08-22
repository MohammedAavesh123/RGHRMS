<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Leaves;
use App\Models\Admin\Wfh;
use Session;
use Auth;
use DB;
use DataTables;
use Brian2694\Toastr\Facades\Toastr;
use App\Helpers\Helpers;
use Mail;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()  {
      $userid = Auth::user()->id;
      if(Helpers::checkPermission($userid, $modelName ="Leaves" , "read")){
        $leavelist = leaves::where('emp_id', $userid)->orderBy('id', 'desc')->get();

        $approvedLeavelist = Leaves::where('emp_id', $userid)
        ->where('approval_status','approved')
        ->where('is_cancelled','0')
        ->orderBy('id', 'desc')
        ->get();

        
             $OFF_days = array();
             $OFF_days[] = date('Y-m-d',strtotime('first saturday of this month'));
             $OFF_days[] = date('Y-m-d',strtotime('third saturday of this month'));

             $allLeavereqstForCurrentMonth=0;
             foreach($approvedLeavelist as $Leave_rqst)
             {
               $allLeavedates = $this->displayDates($Leave_rqst->from_date, $Leave_rqst->to_date);
               $current_month = date('m');
               //$current_month = 04;
               foreach($allLeavedates as $one_Leave_date)
               {
                   $one_Leave_date1 = date('m',strtotime($one_Leave_date));
                   $day = date('D',strtotime($one_Leave_date));
                   //if($one_Leave_date1 == $current_month && (!in_array($one_Leave_date,$OFF_days) || $day!='Sun'))
                   if($one_Leave_date1 == $current_month && $day!='Sun')
                   {
                     if(!in_array($one_Leave_date,$OFF_days))
                     {
                       //  $allLeavereqstForCurrentMonth[] = $one_Leave_date;
                         if($Leave_rqst->day_type == 'full_day')
                         {
                             $allLeavereqstForCurrentMonth+=1;
                         }
                         else
                         {
                             $allLeavereqstForCurrentMonth = $allLeavereqstForCurrentMonth+0.5;
                         }
                     }
                   }
               }
             }
             //$countOFcurrentmonth_Leave_request = count($allLeavereqstForCurrentMonth);
              $countOFcurrentmonth_Leave_request = $allLeavereqstForCurrentMonth;

          $totalLeave = (float)$countOFcurrentmonth_Leave_request;
          $availableleave = 1;
          $availableleave = $availableleave-$totalLeave;
            // @@@@@@@@@@@@@   Aavesh @@@@@@@@
            $plData = DB::table("users")
                                 ->join('employee_official_details', 'users.id','employee_official_details.employee_id')
                                 ->where('employee_official_details.employee_id',$userid)
                                 ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')
                                 ->select('employee_official_details.pl')
                                 ->first();
          $availablePl = $plData->pl;
        //dd($availablePl);
          $availablePl = $availablePl-$totalLeave > 0 ? $availablePl-$totalLeave : 0;
            // @@@@@@@@@@@@@   Aavesh @@@@@@@@
           // dd($availablePl);
          // echo "<pre>"; print_r($totalleave);die;


        return view('Admin.reports.viewleave', compact('leavelist','totalLeave','availableleave','availablePl'));
      }
      else{
        return redirect('/permission-denied');
      }
    }

    function displayDates($date1, $date2, $format = 'Y-m-d' ) {
      $dates = array();
      $current = strtotime($date1);
      $date2 = strtotime($date2);
      $stepVal = '+1 day';
      while( $current <= $date2 ) {
         $dates[] = date($format, $current);
         $current = strtotime($stepVal, $current);
      }
      return $dates;
    }
    public function create()
    {
       $id = Auth::User()->id;
        $tlid = DB::table('users')->where('id', $id)->pluck('team_leader_id');
        $tlName = DB::table('users')->where('id', $tlid)->select('id','firstname', 'lastname','personal_email','email')->first();

       $tlname = DB::table('users')->get();
       $approvedLeavelist = Leaves::where('emp_id', $id)
       ->where('approval_status','approved')
       ->where('is_cancelled','0')
       ->orderBy('id', 'desc')
       ->get();

            $OFF_days = array();
            $OFF_days[] = date('Y-m-d',strtotime('first saturday of this month'));
            $OFF_days[] = date('Y-m-d',strtotime('third saturday of this month'));

            $allLeavereqstForCurrentMonth=0;
            foreach($approvedLeavelist as $Leave_rqst)
            {
              $allLeavedates = $this->displayDates($Leave_rqst->from_date, $Leave_rqst->to_date);
              $current_month = date('m');
              //$current_month = 04;
              foreach($allLeavedates as $one_Leave_date)
              {
                  $one_Leave_date1 = date('m',strtotime($one_Leave_date));
                  $day = date('D',strtotime($one_Leave_date));
                  //if($one_Leave_date1 == $current_month && (!in_array($one_Leave_date,$OFF_days) || $day!='Sun'))
                  if($one_Leave_date1 == $current_month && $day!='Sun')
                  {
                    if(!in_array($one_Leave_date,$OFF_days))
                    {
                      //  $allLeavereqstForCurrentMonth[] = $one_Leave_date;
                        if($Leave_rqst->day_type == 'full_day')
                        {
                            $allLeavereqstForCurrentMonth+=1;
                        }
                        else
                        {
                            $allLeavereqstForCurrentMonth = $allLeavereqstForCurrentMonth+0.5;
                        }
                    }
                  }
              }
            }
            //$countOFcurrentmonth_Leave_request = count($allLeavereqstForCurrentMonth);
             $countOFcurrentmonth_Leave_request = $allLeavereqstForCurrentMonth;

         $totalLeave = (float)$countOFcurrentmonth_Leave_request;
         $availableleave = 1;
         $availableleave = $availableleave-$totalLeave;
         // @@@@@@@ Aavesh @@@@@@@@
         $plData = DB::table("users")
                   ->join('employee_official_details', 'users.id','employee_official_details.employee_id')
                   ->where('employee_official_details.employee_id',$id)
                   ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')
                   ->select('employee_official_details.pl')
                   ->first();

                   // $totalLeave = (float) (@$totalLeaveData[0]->taken_leave ?? 0);
                 // $availablePL = (int) (date('m'));
                  $availablePL = $plData->pl;
                 $availablePL = $availablePL-$totalLeave > 0 ? $availablePL-$totalLeave : 0;
           // @@@@@@@ Aavesh @@@@@@@@
       return view('Admin.reports.addleave', compact('tlName','tlname','availableleave','totalLeave','availablePL'));
    }

    public function cancel_leave(Request $request)
    {
        $leavescancel = leaves::find($request->id);
        $leavescancel->is_cancelled =  1;
        $leavescancel->cancelled_reason =  $request->reason;
        if($leavescancel->update()){
            return json_encode(array("status"=>1));
        }
        else{
            return json_encode(array("status"=>0));
        }

    }

    public function store(Request $request)
    {
      // dd($request->all());
      $empEmail = Auth::User()->email;
      if(empty($empEmail)){
        $empEmail = Auth::User()->personal_email;
      }
      $emp_id = Auth::User()->id;
        $name = Auth::User()->firstname.' '. Auth::user()->lastname;


      $validated = $request->validate([
          'from_date' => 'required',
          'to_date' => 'required',
          'leave_type' => 'required',
          'day_type' => 'required',
          'reason' => 'required',
          'appied_leave_days' => 'required',
      ]);

      $currentDate = date("Y-m-d");
      $leave = New leaves;
      $leave->emp_id = $emp_id;
      $leave->manager_id = $request->manager_id;
      $leave->from_date =date("Y-m-d",strtotime($request->from_date));
      $leave->to_date = date("Y-m-d",strtotime($request->to_date));
      $leave->leave_type = $request->leave_type;
      if ($request->day_type == '1st_half' || $request->day_type == '2st_half'){
          $total_leave = $request->appied_leave_days / 2;
          $leave->appied_leave_days = $total_leave;
      }else{
          $leave->appied_leave_days = $request->appied_leave_days;
      }
      $leave->day_type = $request->day_type;
      $leave->reason = $request->reason;
      $leave->created_at  = $currentDate;
      $leave->updated_at = $currentDate;
      $leave->type_apply = 'Leave';
// dd($leave));

      $leave_type = ucwords(str_replace("_", " ", $request->leave_type));
      $day_type = ucwords(str_replace("_", " ", $request->day_type));

      $message = array(
          'cc' => $request->tl_email,
          'from_email' => $empEmail,
          'name' => $name,
          'manager_id' => $request->manager_id,
          'from_date' =>  date("Y-m-d",strtotime($request->from_date)),
          'to_date' => date("Y-m-d",strtotime($request->to_date)),
          'leave_type' => $leave_type,
          'day_type' => $day_type,
          'appied_leave_days' => $request->appied_leave_days,
          'reason' => $leave->reason
      );

      if($leave->save()){
        Mail::send('Admin.reports.leaveMailTemplate', $message, function ($m) use ($message){
          $m->from($message['from_email']);
          // $m->cc($message['cc']);
          $m->to(HR_EMAIL_ADDRESS)->subject("Leave application for ". $message['leave_type']);
        });
        $notification = Helpers::Addnotification($leave);

          return redirect()->route('leaves.view')->with('success','Leave application sent succesfully');
      }
      else{
          return redirect()->route('leaves.view')->with('error','Leave not sent.');
      }

    }

    public function leaveRequest(Request $request){
        $userid = Auth::user()->id;
        $status = '';
        $leaves = DB::table('leaves')
            ->join('users','users.id','=','leaves.emp_id')
            ->select('leaves.*', 'users.firstname')
            ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as added_by');

        if(isset($request->status) && !empty($request->status))
        {
          $status  = $request->status;
          if($status != "is_cancelled")
          {
            $leaves->where('leaves.approval_status','=',$request->status);
          }
        }

        if(Auth::user()->role_id !=1 && Auth::user()->role_id !=3 && Auth::user()->role_id !=8)
        {

          $leaves->where("leaves.manager_id",'=',$userid );
        }

        if($status == "is_cancelled")
        {
          $leaves->where("leaves.is_cancelled",'=',1);
        }

        $leaves->orderBy('leaves.id', 'desc')->get();


      // $leaves = DB::table('leaves')
      //             ->join('users','users.id','=','leaves.emp_id')
      //             ->select('leaves.*', 'users.firstname')
      //             ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as added_by')
      //             ->orderBy('leaves.id', 'desc')
      //             ->get();

      if ($request->ajax()) {

         return Datatables::of($leaves)
            ->addIndexColumn()
             ->addColumn('employeeName', function ($result) {
                return $result->added_by;
            })
            ->addColumn('status', function ($result) {
              $userid = Auth::user()->id;
              return 'InActive';

                //return $result->status;
            })
            ->addColumn('from_date', function ($result) {
             return date('d M,Y', strtotime($result->from_date));
            })

             ->addColumn('to_date', function ($result) {
                  return date('d M,Y', strtotime($result->to_date));
            })
            ->addColumn('day_type', function ($result) {
                 return ucfirst(str_replace('_', ' ',$result->day_type));
           })


            ->addColumn('status', function ($result) {
              $userid = Auth::user()->id;
                  if ($result->is_cancelled == 1) {
                      return  '<span class="text-danger">Cancelled</span>';
                  }else{
                      if($result->approval_status == 'approved')
                      {
                          return  '<span class="text-success">Approved</span>';
                      }
                      elseif ($result->approval_status == 'rejected') {
                         return  '<span class="text-danger">Rejected</span>';
                      }
                      elseif ($result->approval_status == 'cancel') {
                         return  '<span class="text-danger">Cancel By OG</span>';
                      }
                      else
                      {
                         return '<span style="color: #6495ed">Pending</span>';
                      }
                  }

            })
            ->addColumn('action', function ($result) {
                 $userid = Auth::user()->id;
                 return '<a href="'.route('leaves.edit_show', $result->id).'" class="btn btn-outline-primary mx-1 btn-sm" data-id="'.$result->id.'"><i class="fa fa-eye"></i> View/Edit</a>';
            })
             ->escapeColumns([])
             ->make(true);
         //}//end
     }
      return view('Admin.reports.AllleaveRequest',compact('status'));
    }

    public function editShow($id)
    {

      $requestData = DB::table('leaves')
                    ->join('users','users.id','=','leaves.emp_id')
                    ->where('leaves.id', $id)
                    ->select('leaves.*', 'users.firstname','users.email','users.personal_email')
                    ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as employee')
                    ->orderBy('leaves.id', 'desc')
                    ->get();
                    // dd($requestData);


        $tlname   =  DB::table('users')->where('id',$requestData['0']->manager_id)->get();

       return view('Admin.reports.editleave', compact('requestData','id','tlname'));
    }

    public function checkMail(){


            $message = array();
            $message['cc'] = "agarwalpinky666@gmail.com";
            $message['to'] = "akshay.s@rglabs.net";
            $message['email'] = "goyalpinky715@gmail.com";
            $message['subject'] = "Test";

		Mail::send('Admin.reports.checkMail', $message, function ($m) use ($message){

			//$mailto:m->from('support@vision11.in', 'Vision11');
      $m->from("rginfotechtest@gmail.com");
      $m->cc($message['cc']);

			$m->to($message['email'])->subject($message['subject']);

		});
exit;
      $message = array();
      // $message['cc'] = "pinky@rglabs.net";
      $message['to'] = "vahid.khan@rglabs.net";
      Mail::send('Admin.reports.checkMail', $message, function ($m) use ($message){
        $m->from(HR_EMAIL_ADDRESS);
        $m->cc($message['cc']);
        $m->to($message['to'])->subject("Leave application");
      });
    }

    public function LeaveApprovelStatus(Request $request)
    {
        $role_id = Auth::User()->role_id;
        if($role_id == 5)
        {
            $leavestatus = leaves::find($request->id);
            $leavestatus->is_approved_byTL =  1;
            $leavestatus->approved_status_byTL =  $request->status;
            if($leavestatus->update()){
                return redirect()->route('leaves.all_request')
                ->with('success','Leaves update successfully.');
            }
            else{
                return redirect()->route('leaves.all_request')
                ->with('error','Leaves not update.');
            }
        }
        else
        {

            $empEmail = Auth::User()->email;
            if(empty($empEmail)){
              $empEmail = Auth::User()->personal_email;
            }
            $emp_id = Auth::User()->id;
            $name = Auth::User()->firstname.' '. Auth::user()->lastname;

            //$leaverecord = leaves::join('users.id','=','leaves.emp_id')->where('leaves.id','=',$request->id)->select('leaves.*','users.firstname')->first();
            $leaverecord = DB::table('leaves')->join('users','users.id','=','leaves.emp_id')->where('leaves.id','=',$request->id)->select('leaves.*','users.firstname','users.lastname')->first();

            $message = array(
              'cc' => $request->tl_email,
              'to' => $request->user_email,
              'status' => $request->status,
              'leaverecord' => $leaverecord
            );

            $leavestatus = leaves::find($request->id);
            $leavestatus->approval_status =  $request->status;
            if($leavestatus->update()){
              Mail::send('Admin.reports.leaveApproveRejected', $message, function ($m) use ($message){
                $m->from(HR_EMAIL_ADDRESS);
                $m->cc($message['cc']);
                $m->to($message['to'])->subject("Leave application");
              });
                return redirect()->route('leaves.all_request')
                ->with('success','Leaves update successfully.');
            }
            else{
                return redirect()->route('leaves.all_request')
                ->with('error','Leaves not update.');
            }
        }

        // $wfhstatus = leaves::find($request->id);
        // $wfhstatus->approval_status =  $request->status;
        //
        // if($wfhstatus->update()){
        //     return redirect()->route('leaves.all_request')
        //     ->with('success','Leaves update successfully.');
        // }
        // else{
        //     return redirect()->route('leaves.all_request')
        //     ->with('error','Leaves not update.');
        // }

    }


}
