<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Attendance;
use App\Models\Admin\AttendanceActivity;
use App\Models\Admin\EmployeOfficialDetail;
use Session;
use Auth;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use App\Helpers\Helpers;
use DataTables;
use DateTime;
use DB;
Use App\Models\Admin\Shift;
use Carbon\Carbon;
use GeoIP as GeoIP;
use stdClass;

class AdminAttendanceController extends Controller
{

  public function index_old(Request $request){

      // echo "<pre>"; print_r($request->all()); die;
      $d = DB::table('users')->whereNotIn('role_id', ['1']);
      if (!empty($request->employeeName)) {
        $da= $d->where(DB::raw("CONCAT(firstname,' ',lastname)"), 'LIKE', '%' . $request->employeeName . '%')->where('users.status','1')->orderBy('firstname')->get();
      } else {
        $da= $d->get();
      }


      $results_per_page = 70;
      $number_of_result = $da->count();
      $number_of_page = ceil ($number_of_result / $results_per_page);



      if (!isset ($_GET['page']) ) {
        $page = 1;
       } else {
           $page = $_GET['page'];
       }


       $page_first_result = ($page-1) * $results_per_page;


       $employeeData = DB::table('users')->whereNotIn('role_id', ['1'])->offset($page_first_result)->limit($results_per_page);
       if (!empty($request->employeeName)) {

         $data= $employeeData->where(DB::raw("CONCAT(firstname,' ',lastname)"), 'LIKE', '%' . $request->employeeName . '%')
         ->where('users.status',1)->orderBy('firstname')->get();
       } else {

         $data= $employeeData->where('users.status',1)->orderBy('firstname')->get();
       }



      $new = array();
      $employeeID = array();
      $user_id_array = array();
      if (!$data->isEmpty()) {

      foreach ($data as $key => $value) {
         $new['id'] = $value->id;
         $new['firstname'] = $value->firstname;
         $new['lastname'] = $value->lastname;
         $employeeID[] = $new;
         $user_id_array[] = $value->id;
      }
    }
      // if(!empty($request->month) && isset($request->month) || !empty($request->year) && isset($request->year)){
      //   $start_date = $request->year.'-'.$request->month.'-'.'01';
      // }
      // else{
      //   $start_date = date("Y-m-01");
      // }



       $start_date = $request->month ? $request->year.'-'.$request->month.'-'.'01' : date("Y-m-01");
       $end_date = $request->month ? $request->year.'-'.$request->month.'-'.date("t", strtotime($request->year.'-'.$request->month)) : date("Y-m-t");

       $attedencearray = array();

        $req_date_arr = [];
         for ($j=0; $j < count($employeeID); $j++) {
             $req_date_arr[$employeeID[$j]['id']]['name'] = $employeeID[$j]['firstname']." ".$employeeID[$j]['lastname'];
           for ($i=0; $i < date("t", strtotime($end_date)); $i++) {
             if($i == 0) {
               $req_date_arr[$employeeID[$j]['id']]['date'][Carbon::parse($start_date)->format("Y-m-d")] = "";
             }
             else {
               $nex_date = date("Y-m-".$i, strtotime($end_date));
               $req_date_arr[$employeeID[$j]['id']]['date'][Carbon::parse($nex_date)->addDays(1)->format("Y-m-d")] = "";
             }
           }
         }


      $data = DB::table('attendance')
          ->select('attendance.date as date','attendance.*',
            DB::raw("'Present' as title"))
          ->whereIn('employee_id', $user_id_array)
          ->where('attendance.date','>=', date("Y-m-d", strtotime($start_date)))
          ->where('attendance.date','<=', date("Y-m-d", strtotime($end_date)))
          ->orderBy("attendance.date", "ASC")
          ->get();




      // $date = $this->displayDates('2022-04-01', '2022-04-30');
        // echo "<pre>";print_r($data); die;

      if($data->isNotEmpty()) {
        foreach ($data as $key => $value) {

           $wfhData = DB::table('wfh')->where('emp_id', $value->employee_id)->where('approval_status', '=', 'approved')->get();
           $leavesData = DB::table('leaves')->where('emp_id', $value->employee_id)->where('approval_status', '=', 'approved')->get();

          if($wfhData->isNotEmpty()){
            foreach ($wfhData as $key => $v) {
            $wfhData2 = $this->displayDates($v->from_date, $v->to_date);

                if(isset($req_date_arr[$value->employee_id]['date'][$value->date])){
                  if(!empty($wfhData2)){
                      $date = $this->displayDates($v->from_date, $v->to_date);

                      if(!empty($date)){
                        foreach ($date as $key_d => $d) {
                          if($d == $value->date){
                              if ($v->day_type == '2nd_half') {
                                $req_date_arr[$v->emp_id]['date'][$d] = 'P/P(WFH)';
                              }
                              if ($v->day_type == '1nd_half') {
                                $req_date_arr[$v->emp_id]['date'][$d] = 'P(WFH)/P';
                              }
                          }else {
                              $req_date_arr[$value->employee_id]['date'][$value->date] = "P";
                            }
                        }

                      }

                  }
                }
                // dd($leavesData);
                if(!empty($leavesData)){
                  foreach ($leavesData as $key => $v) {
                    $date = $this->displayDates($v->from_date, $v->to_date);
                    if(!empty($date)){
                      foreach ($date as $key_d => $d) {
                        if($d == $value->date){
                            $req_date_arr[$v->emp_id]['date'][$d] = $v->day_type;
                        }
                      }
                    }
                  }
                }

              if(!empty($date)){
                foreach ($date as $key_d => $d) {
                  if($d == $value->date){
                      $req_date_arr[$v->emp_id]['date'][$d] = "WFH";
                  }
                }
              }
            }
          }
          else{
            if(isset($req_date_arr[$value->employee_id]['date'][$value->date])){
              $req_date_arr[$value->employee_id]['date'][$value->date] = "P";
            }
            if(!empty($leavesData)){
              foreach ($leavesData as $key => $v) {
                $date = $this->displayDates($v->from_date, $v->to_date);
                if(!empty($date)){
                  foreach ($date as $key_d => $d) {
                    if($d == $value->date){
                        $req_date_arr[$v->emp_id]['date'][$d] = $v->day_type;
                    }
                  }
                }
              }
            }
          }

        }

      }
      // print_r($req_date_arr);
    //  die;

      // echo "<pre>";print_r($req_date_arr); die;
      return view('Admin.attendance.allAttendance', compact('req_date_arr','number_of_page'));
    }


      public function index(Request $request){

          // echo "<pre>"; print_r($request->all()); die;
          $d = DB::table('users')->whereNotIn('role_id', ['1']);
          if (!empty($request->employeeName)) {
            $da= $d->where(DB::raw("CONCAT(firstname,' ',lastname)"), 'LIKE', '%' . $request->employeeName . '%')->where('users.status','1')->orderBy('firstname')->get();
          } else {
            $da= $d->get();
          }


          $results_per_page = 70;
          $number_of_result = $da->count();
          $number_of_page = ceil ($number_of_result / $results_per_page);



          if (!isset ($_GET['page']) ) {
            $page = 1;
           } else {
               $page = $_GET['page'];
           }


           $page_first_result = ($page-1) * $results_per_page;


           $employeeData = DB::table('users')->whereNotIn('role_id', ['1'])->offset($page_first_result)->limit($results_per_page);
           if (!empty($request->employeeName)) {

             $data= $employeeData->where(DB::raw("CONCAT(firstname,' ',lastname)"), 'LIKE', '%' . $request->employeeName . '%')
             ->where('users.status',1)->orderBy('firstname')->get();
           } else {

             $data= $employeeData->where('users.status',1)->orderBy('firstname')->get();
           }



          $new = array();
          $employeeID = array();
          $user_id_array = array();
          if (!$data->isEmpty()) {

          foreach ($data as $key => $value) {
             $new['id'] = $value->id;
             $new['firstname'] = $value->firstname;
             $new['lastname'] = $value->lastname;
             $employeeID[] = $new;
             $user_id_array[] = $value->id;
          }
        }
          // if(!empty($request->month) && isset($request->month) || !empty($request->year) && isset($request->year)){
          //   $start_date = $request->year.'-'.$request->month.'-'.'01';
          // }
          // else{
          //   $start_date = date("Y-m-01");
          // }



           $start_date = $request->month ? $request->year.'-'.$request->month.'-'.'01' : date("Y-m-01");
           $end_date = $request->month ? $request->year.'-'.$request->month.'-'.date("t", strtotime($request->year.'-'.$request->month)) : date("Y-m-t");

           $attedencearray = array();

            $req_date_arr = [];
             for ($j=0; $j < count($employeeID); $j++) {
                 $req_date_arr[$employeeID[$j]['id']]['name'] = $employeeID[$j]['firstname']." ".$employeeID[$j]['lastname'];
               for ($i=0; $i < date("t", strtotime($end_date)); $i++) {
                 if($i == 0) {
                   $req_date_arr[$employeeID[$j]['id']]['date'][Carbon::parse($start_date)->format("Y-m-d")] = "A";
                 }
                 else {
                   $nex_date = date("Y-m-".$i, strtotime($end_date));
                    $firstDate = date("Y-m", strtotime($nex_date));
                    // echo date('d', strtotime('fifth saturday of'.$firstDate));
                    // echo date('d', strtotime($nex_date))."\n";
                    // if(date('d', strtotime($nex_date)) == 30){
                    //
                    //    echo (date('d', strtotime('fifth saturday of'.$firstDate)) == date('d', strtotime($nex_date))) ? "one" : "zero";
                    //    exit;
                    // }
                    // exit;
                    $req_date_arr[$employeeID[$j]['id']]['date'][Carbon::parse($nex_date)->addDays(1)->format("Y-m-d")] = "A";

                    $req_date_arr[$employeeID[$j]['id']]['leaves_count'] = 0;
                    $req_date_arr[$employeeID[$j]['id']]['wfh_count'] = 0;

                //        if(
                //          date('d', strtotime('first saturday of'.$firstDate)) == date('d', strtotime($nex_date))
                //          || date('d', strtotime('third saturday of'.$firstDate)) == date('d', strtotime($nex_date))
                //          || date('d', strtotime('fifth saturday of'.$firstDate)) == date('d', strtotime($nex_date))
                //          || date('N', strtotime($nex_date)) > 6
                //        ){
                //   $req_date_arr[$employeeID[$j]['id']]['date'][Carbon::parse($nex_date)->addDays(1)->format("Y-m-d")] = "OFF";
                // }else{
                //   $req_date_arr[$employeeID[$j]['id']]['date'][Carbon::parse($nex_date)->addDays(1)->format("Y-m-d")] = "A";
                // }

                 }
               }
             }
// var_dump($req_date_arr);
// exit;


          $data = DB::table('attendance')
              ->select('attendance.date as date','attendance.*',
                DB::raw("'Present' as title"))
              ->whereIn('employee_id', $user_id_array)
              ->where('attendance.date','>=', date("Y-m-d", strtotime($start_date)))
              ->where('attendance.date','<=', date("Y-m-d", strtotime($end_date)))
              ->orderBy("attendance.date", "ASC")
              ->get();


            if($data->isNotEmpty()) {
              foreach ($data as $key => $value) {
                if(isset($req_date_arr[$value->employee_id]['date'][$value->date])){
                       $req_date_arr[$value->employee_id]['date'][$value->date] = "P";
                       // $req_date_arr[$value->employee_id]['date'][$value->date] = "WFH";
                       // $req_date_arr[$value->employee_id]['date'][$value->date] = "P(WFH)/P";
                       // $req_date_arr[$value->employee_id]['date'][$value->date] = "P/P(WFH)";
                       // $req_date_arr[$value->employee_id]['date'][$value->date] = "P(WFH)/A";
                       // $req_date_arr[$value->employee_id]['date'][$value->date] = "A/P(WFH)";
                       // $req_date_arr[$value->employee_id]['date'][$value->date] = "P/A";
                       // $req_date_arr[$value->employee_id]['date'][$value->date] = "A/P";
                       // $req_date_arr[$value->employee_id]['date'][$value->date] = "A";
                       // $req_date_arr[$value->employee_id]['date'][$value->date] = "P(WFH)/A";
                     }
              }
            }

             $leavesData = DB::table('leaves')->whereIn('emp_id', $user_id_array)->where('approval_status', '=', 'approved')->where('is_cancelled','=',0)->get();
             // dd($leavesData);
             $wfhData = DB::table('wfh')->whereIn('emp_id', $user_id_array)->where('approval_status', '=', 'approved')->where('is_cancelled','=',0)->get();

             if(!empty($leavesData)){
               foreach ($leavesData as $key => $v) {
                 $date = $this->displayDates($v->from_date, $v->to_date);
                 // dd($date);
                 if(!empty($date)){
                   foreach ($date as $key_d => $d) {
                     if(isset($req_date_arr[$v->emp_id]['date'][$d])){
                       if($v->day_type == "1st_half"){
                         $v->day_type = "A/P";
                         $req_date_arr[$v->emp_id]['leaves_count'] += 0.5;
                       }else if($v->day_type == "2nd_half"){
                         $v->day_type = "P/A";
                         $req_date_arr[$v->emp_id]['leaves_count'] += 0.5;
                       }else{

                         $v->day_type = "A";
                         $req_date_arr[$v->emp_id]['leaves_count'] += 1;
                       }
                         $req_date_arr[$v->emp_id]['date'][$d] = $v->day_type;

                     }
                   }
                 }
               }
             }

// var_dump($req_date_arr);
// exit;
   if(!empty($wfhData)){
     foreach ($wfhData as $key => $w) {
       $date = $this->displayDates($w->from_date, $w->to_date);
       if(!empty($date)){
         foreach ($date as $key_d => $d) {
           $wType = "";
           if(isset($req_date_arr[$w->emp_id]['date'][$d])){
            if($req_date_arr[$w->emp_id]['date'][$d] == 'P' ){
             if($w->day_type == "1st_half"){
               $wType = "P(WFH)/P";
                  $req_date_arr[$w->emp_id]['wfh_count'] += 0.5;
             }else if($w->day_type == "2nd_half"){
               $wType = "P/P(WFH)";
               $req_date_arr[$w->emp_id]['wfh_count'] += 0.5;
             }else if($w->day_type == "full_day"){
               $wType = "P(WFH)";
               $req_date_arr[$w->emp_id]['wfh_count'] += 1;
             }

              // var_dump($req_date_arr[$w->emp_id]['date']);

              if($req_date_arr[$w->emp_id]['date'][$d] == "A/P"){
                $wType = "A/P(WFH)";
              }
              if($req_date_arr[$w->emp_id]['date'][$d] == "P/A"){
                $wType = "P(WFH)/A";
              }

                      $req_date_arr[$w->emp_id]['date'][$d] = $wType;
                    }
                 }
               }
             }
           }
         }
         $holidatData = DB::table('holiday')
                            // ->where('DATE_FORMAT("start", "m") as formatted_start', $currentMonth)
                            ->whereMonth('start', date('m'))
                            ->whereYear('start', date('Y'))
                            ->get();

         foreach ($req_date_arr as $key_ep => $value_emplo) {
         foreach ($value_emplo['date'] as $key_ee => $value_date) {
            if($req_date_arr[$key_ep]['date'][$key_ee] != 'P'){
                if(date('Y-m-d', strtotime('first saturday of'.$key_ee)) == date('Y-m-d', strtotime($key_ee))
                   || date('Y-m-d', strtotime('third saturday of'.$key_ee)) == date('Y-m-d', strtotime($key_ee))
                   || date('Y-m-d', strtotime('fifth saturday of'.$key_ee)) == date('Y-m-d', strtotime($key_ee))
                   || date('N', strtotime($key_ee)) > 6){
                      $req_date_arr[$key_ep]['date'][$key_ee] = "OFF";

                }
                elseif (!empty($holidatData)){
                   foreach ($holidatData as $key => $holidayDataVal) {
                     // $date = $this->displayDates($holidayDataVal->start, $holidayDataVal->end);
                     $date = date('Y-m-d', strtotime($holidayDataVal->start));
                        if(!empty($date)){
                            if($date == $key_ee){
                                $req_date_arr[$key_ep]['date'][$key_ee] = "OFF";
                            }
                        }
                     }
                 }
            }

         }
         }
// echo "<pre>";print_r($req_date_arr); die;


          return view('Admin.attendance.allAttendance', compact('req_date_arr','number_of_page'));
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

        function isWeekend($date) {
          return (date('N', strtotime($date)) >= 6);
          }

    public function EmployeeActivity(Request $req)
    {

      // $activety = DB::table('attendance_activity')->where('employee_id', $req->id)->where('date', $req->date)->get();
      // return response()->json($activety);

    $activity =DB::table('attendance_activity as ac')
                  ->join('employee_official_details as ed', 'ed.employee_id', '=', 'ac.employee_id')
                  ->join('shift as s', 'ed.shift', '=', 's.id')
                  ->select('ac.*','s.working_hours')
                  ->where('ac.date', $req->date)
                  ->where('ac.employee_id', $req->id)
                  ->get();

      $checkInTime = db::table('attendance')->whereDate('date',$req->date)->where('employee_id', $req->id)->first();

      if(!empty($checkInTime)){
        foreach ($activity as $key => $value) {
          $hrs = floor($checkInTime->minutes / 60);
          $mint = ($checkInTime->minutes % 60);
          $value->check_current = $hrs.".".$mint;
        }
      }


      $currentCheckIn = db::table('attendance_activity')->whereNull('checkout')->whereDate('date',$req->date)->where('employee_id', $req->id)->first();



      $timezone = date_default_timezone_set('Asia/Calcutta');
      $today = Carbon::now()->format('Y-m-d h:i:s a');
      if(!empty($currentCheckIn)){
        $start_date = new DateTime(date('Y-m-d H:i:s', strtotime($currentCheckIn->checkin)));
        $since_start = $start_date->diff(new DateTime(date('Y-m-d H:i:s', strtotime($today))));
        // $hours = $since_start->h.'.'.$since_start->i;
        $hours = $since_start->h;
        $minutes1 = $since_start->i;
        $hours1 = $hours * 60 + $minutes1+$checkInTime->minutes;
        $hrs = floor($hours1 / 60);
        $mint = ($hours1 % 60);

       if($activity->isNotEmpty()) {
         foreach ($activity as $key => $value) {
           $value->check_current = $hrs.".".$mint;
         }
       }
      }

      return response()->json($activity);

    }



    public function AllEmployee(Request $request){
      $currentDate = Carbon::now()->format('Y-m-d');

      $attendance = DB::table('attendance')->where('date',$currentDate)->get();
      $users = DB::table('users')->get();
         // dd($users);

     $employee_id = array();
     foreach ($users as $key => $value) {
       $employee_id[] = $value->id;
     }
            $attendanceData = User::Leftjoin('attendance', function($join)
                            {
                                $currentDate = Carbon::now()->format('Y-m-d');
                                $join->on('attendance.employee_id', '=', 'users.id');
                                $join->where('attendance.date','=',$currentDate);
                          })
                          ->leftjoin('employee_official_details','users.id','employee_official_details.employee_id')
                          ->leftjoin('shift','employee_official_details.shift','shift.id')
                          ->whereNotIn('users.role_id', [1])
                          ->where('users.status',1)
                          ->select('shift.shift_from','shift.shift_to','shift.working_hours','shift.working_days','attendance.*')
                          ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')->GroupBy('users.firstname')->get();

                          $city =  GeoIP::getCity();
                        //dd($attendanceData->all());
                     return view('Admin.attendance.all_employee',compact('attendanceData','users','city'));


    }
}
