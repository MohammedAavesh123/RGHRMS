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

class WfhController extends Controller
{
    public function index()  {
      $userid = Auth::user()->id;
      if(Helpers::checkPermission($userid, $modelName ="LeaveReports" , "read")){
        $wfhlist = Wfh::where('emp_id', $userid)->orderBy('id', 'desc')->get();
        $totalwfhData =  DB::table('wfh')
         ->select(DB::raw('SUM(CASE WHEN day_type = "full_day" THEN appied_wfh_days*1 ELSE appied_wfh_days*0.5 END) AS taken_wfh'))
         ->where('from_date', '>=', date('Y-01-01'))
         ->where('to_date', '<=', date('Y-m-t'))
         ->where('approval_status', 'approved')
         ->where('emp_id', $userid)
         ->get();

         $wfhData = DB::table("users")
                     ->join('employee_official_details', 'users.id','employee_official_details.employee_id')
                     ->where('employee_official_details.employee_id',$userid)
                     ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')
                     ->select('employee_official_details.wfh')
                     ->first();

         $totalwfh = (float) (@$totalwfhData[0]->taken_wfh ?? 0);
           // $availablewfh = $wfhData->wfh;
         $availablewfh = 2;
         $availablewfh = $availablewfh-$totalwfh > 0 ? $availablewfh-$totalwfh : 0;

        return view('Admin.wfh.viewwfh', compact('wfhlist','totalwfh','availablewfh'));
      }
      else{
        return redirect('/permission-denied');
      }
    }

    public function checkMail(){


            $message = array();
            $message['cc'] = "pinky@rglabs.net";
            $message['to'] = "akshay.s@rglabs.net";
            $message['email'] = "akshay.s@rglabs.net";
            $message['subject'] = "Test";

		Mail::send('Admin.reports.checkMail', $message, function ($m) use ($message){

			//$mailto:m->from('support@vision11.in', 'Vision11');
      $m->from("rginfotechtest@gmail.com");
      $m->cc($message['cc']);

			$m->to($message['email'])->subject($message['subject']);

		});
exit;
      $message = array();
      $message['cc'] = "pinky@rglabs.net";
      $message['to'] = "akshay.s@rglabs.net";
      Mail::send('Admin.reports.checkMail', $message, function ($m) use ($message){
        $m->from(HR_EMAIL_ADDRESS);
        $m->cc($message['cc']);
        $m->to($message['to'])->subject("Leave application");
      });
    }


    public function cancel_wfh(Request $request)
    {
        $wfhcancel = Wfh::find($request->id);
        $wfhcancel->is_cancelled =  1;
        if($wfhcancel->update()){
            return json_encode(array("status"=>1));
        }
        else{
            return json_encode(array("status"=>0));
        }

    }

    public function create()
    {
       $id = Auth::User()->id;

       $tlid = DB::table('users')->where('id', $id)->pluck('team_leader_id');

        $tlName = DB::table('users')->where('id', $tlid)->select('id','firstname', 'lastname','personal_email','email')->first();
        $tlname = DB::table('users')->get();

        $totalLeaveData =  DB::table('wfh')
         ->select(DB::raw('SUM(CASE WHEN day_type = "full_day" THEN appied_wfh_days*1 ELSE appied_wfh_days*0.5 END) AS taken_wfh'))
         ->where('from_date', '>=', date('Y-01-01'))
         ->where('to_date', '<=', date('Y-m-t'))
         ->where('approval_status', 'approved')
         ->where('emp_id', $id)
         ->get();

         $wfhData = DB::table("users")
                     ->join('employee_official_details', 'users.id','employee_official_details.employee_id')
                     ->where('employee_official_details.employee_id',$id)
                     ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')
                     ->select('employee_official_details.wfh')
                     ->first();

         $totalLeave = (float) (@$totalLeaveData[0]->taken_wfh ?? 0);
          // $availablePL = $wfhData->wfh;
         $availablePL = 2;
         $availablePL = $availablePL-$totalLeave > 0 ? $availablePL-$totalLeave : 0;

       return view('Admin.wfh.addwfh', compact('tlName','tlname','availablePL','totalLeave'));
    }

    public function hrwfhcreate()
    {
       $id = Auth::User()->id;
       $tlid = DB::table('users')->where('id', $id)->pluck('team_leader_id');
       $tlName = DB::table('users')->where('id', $tlid)->select('id','firstname', 'lastname','personal_email','email')->first();
        $tlname = DB::table('users')->get();

       return view('Admin.wfh.addhrwfh', compact('tlName','tlname'));
    }

    public function store(Request $request)
    {

      $empEmail = Auth::User()->email;
      if(empty($empEmail)){
        $empEmail = Auth::User()->personal_email;
      }
      $emp_id = Auth::User()->id;
      $name = Auth::User()->firstname.' '. Auth::user()->lastname;

      $validated = $request->validate([
          'from_date' => 'required',
          'to_date' => 'required',
          // 'leave_type' => 'required',
          'day_type' => 'required',
          // 'tl_name' => 'required',

          'reason' => 'required',
          'appied_wfh_days' => 'required',

      ]);

      $currentDate = date("Y-m-d");
      $wfh = New Wfh;
      $wfh->emp_id = $emp_id;
      $wfh->manager_id = $request->manager_id;
      $wfh->from_date =date("Y-m-d",strtotime($request->from_date));
      $wfh->to_date = date("Y-m-d",strtotime($request->to_date));
      // $wfh->wfh_type = $request->wfh_type;
      $wfh->appied_wfh_days = $request->appied_wfh_days;
      $wfh->day_type = $request->day_type;
      $wfh->reason = $request->reason;
      $wfh->created_at  = $currentDate;
      $wfh->updated_at = $currentDate;




      // $wfh_type = ucwords(str_replace("_", " ", $request->wfh_type));
      $day_type = ucwords(str_replace("_", " ", $request->day_type));

      $message = array(
          'cc' => $request->tl_email,
          'from_email' => $empEmail,
          'name' => $name,
          'manager_id' => $request->manager_id,
          'from_date' =>  $request->from_date,
          'to_date' => $request->to_date,
          // 'wfh_type' => $wfh_type,
          'day_type' => $day_type,
          'appied_wfh_days' => $request->appied_wfh_days,
          'reason' => $wfh->reason
      );

      if($wfh->save()){
       // echo $message['from_email']; exit;
        Mail::send('Admin.reports.wfhMailTemplate', $message, function ($m) use ($message){
          $m->from($message['from_email']);
           $m->cc($message['cc']);
          $m->to(HR_EMAIL_ADDRESS)->subject("Work From Home application");
        });
        // \Mail::to('mohhamadhakam@gmail.com')->send(new \App\Mail\MyTestMail($message));
          return redirect()->route('wfh.view')->with('success','WFH application sent succesfully');
      }
      else{
          return redirect()->route('wfh.view')->with('error','WFH not sent.');
      }

    }

    public function hrwfhStore(Request $request)
    {

      // test
      $empEmail = Auth::User()->email;
      if(empty($empEmail)){
        $empEmail = Auth::User()->personal_email;
      }
      // $emp_id = Auth::User()->id;


      $validated = $request->validate([
          'from_date' => 'required',
          'to_date' => 'required',
          // 'leave_type' => 'required',
          'day_type' => 'required',
          'tl_name' => 'required',
          'emp_id' => 'required',

          'reason' => 'required',
          'appied_wfh_days' => 'required',

      ]);

      $currentDate = date("Y-m-d");
      $hrwfh = New Wfh;
      // $hrwfh->emp_id = $emp_id;
      $hrwfh->emp_id = $request->emp_id;
      $hrwfh->manager_id = $request->manager_id;
      $hrwfh->from_date =  date("Y-m-d",strtotime($request->from_date));
      $hrwfh->to_date =  date("Y-m-d",strtotime($request->to_date));
      // $hrwfh->hrwfh_type = $request->hrwfh_type;
      $hrwfh->appied_wfh_days = $request->appied_wfh_days;
      $hrwfh->day_type = $request->day_type;
      $hrwfh->reason = $request->reason;
      $hrwfh->created_at  = $currentDate;
      $hrwfh->updated_at = $currentDate;
      // $hrwfh_type = ucwords(str_replace("_", " ", $request->hrwfh_type));
      $day_type = ucwords(str_replace("_", " ", $request->day_type));

      $message = array(
          'cc' => $request->tl_email,
          'from_email' => $empEmail,
          'manager_id' => $request->manager_id,
          'from_date' =>  $request->from_date,
          'to_date' => $request->to_date,
          // 'hrwfh_type' => $hrwfh_type,
          'day_type' => $day_type,
          'appied_wfh_days' => $request->appied_wfh_days,
          'reason' => $hrwfh->reason
      );

      if($hrwfh->save()){
        Mail::send('Admin.reports.wfhMailTemplate', $message, function ($m) use ($message){
          $m->from($message['from_email']);
          $m->cc($message['cc']);
          $m->to('hakam@rglabs.net')->subject("Work From Home application");
        });
          return redirect()->route('wfh.all_request')->with('success','WFH application sent succesfully');
      }
      else{
          return redirect()->route('wfh.all_request')->with('error','WFH not sent.');
      }

    }






    public function wfhRequest(Request $request){
        $userid = Auth::user()->id;
      $status = '';

      $wfh = DB::table('wfh')
                ->join('users','users.id','=','wfh.emp_id')
                ->select('wfh.*', 'users.firstname')
                ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as added_by');

      if(isset($request->status) && !empty($request->status))
      {
        $status  = $request->status;
        $wfh->where('wfh.approval_status','=',$request->status);
      }

      if(Auth::user()->role_id !=1 && Auth::user()->role_id !=3)
      {
        $wfh->where("wfh.manager_id",'=',$userid );
      }
      $wfh->where("wfh.is_cancelled",'=',0);
      $wfh->orderBy('wfh.id', 'desc')->get();


      if ($request->ajax()) {

         return Datatables::of($wfh)
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
                     if($result->approval_status == 'approved')
                     {
                         return  '<span class="text-success">Approved</span>';
                     }
                     elseif ($result->approval_status == 'rejected') {
                        return  '<span class="text-danger">Rejected</span>';
                     }
                     else
                     {
                        return '<span style="color: #6495ed">Pending</span>';
                     }
            })
            ->addColumn('action', function ($result) {
                 $userid = Auth::user()->id;
                 return '<a href="'.route('wfh.edit_show', $result->id).'" class="btn btn-outline-primary mx-1 btn-sm" data-id="'.$result->id.'"><i class="fa fa-eye"></i> View/Edit</a>';
            })
             ->escapeColumns([])
             ->make(true);
         //}//end
     }
      return view('Admin.wfh.AllwfhRequest',compact('status'));
    }



    public function editShow($id)
    {

      $requestData = DB::table('wfh')
                    ->join('users','users.id','=','wfh.emp_id')
                    ->where('wfh.id', $id)
                    ->select('wfh.*', 'users.firstname','users.email','users.personal_email')
                    ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as employee')
                    ->orderBy('wfh.id', 'desc')
                    ->get();
                    // dd($requestData['0']->manager_id);


    $tlname   =  DB::table('users')->where('id',$requestData['0']->manager_id)->get();

       return view('Admin.wfh.editwfh', compact('requestData', 'id','tlname'));
    }

    public function WfhApprovelStatus(Request $request)
    {

      $role_id = Auth::User()->role_id;

        if($role_id == 5)
        {
            $wfhstatus = Wfh::find($request->id);
            $wfhstatus->is_approved_byTL =  1;
            $wfhstatus->approved_status_byTL =  $request->status;
            if($wfhstatus->update()){
                return redirect()->route('wfh.all_request')
                ->with('success','WFH update successfully.');
            }
            else{
                return redirect()->route('wfh.all_request')
                ->with('error','WFH not update.');
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
          $leaverecord = DB::table('wfh')->join('users','users.id','=','wfh.emp_id')->where('wfh.id','=',$request->id)->select('wfh.*','users.firstname','users.lastname')->first();

          $message = array(
            'cc' => $request->tl_email,
            'to' => $request->user_email,
            'status' => $request->status,
            'leaverecord' => $leaverecord
          );

          $wfhstatus = Wfh::find($request->id);
          $wfhstatus->approval_status =  $request->status;

            if($wfhstatus->update()){
              Mail::send('Admin.reports.wfhApproveRejected', $message, function ($m) use ($message){
                  $m->from(HR_EMAIL_ADDRESS);
                  $m->cc($message['cc']);
                  $m->to($message['to'])->subject("Work From Home application");
              });
                return redirect()->route('wfh.all_request')
                ->with('success','WFH update successfully.');
            }
            else{
                return redirect()->route('wfh.all_request')
                ->with('error','WFH not update.');
            }
        }

      // $wfhstatus = Wfh::find($request->id);
      // $wfhstatus->approval_status =  $request->status;
      //
      //
      //   if($wfhstatus->update()){
      //     // echo "string";exit;
      //       return redirect()->route('wfh.all_request')
      //       ->with('success','WFH update successfully.');
      //   }
      //   else{
      //       return redirect()->route('wfh.all_request')
      //       ->with('error','WFH not update.');
      //   }

    }

    public function getEmployee(Request $req){

        $data['positions'] = DB::table('users')
        ->where('team_leader_id', $req->id)->get(["firstname","lastname","id"]);
         // dd($data['positions']);

        return response()->json($data);
    }
}
