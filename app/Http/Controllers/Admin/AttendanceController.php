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
use stdClass;
use GeoIP as GeoIP;
// use PulkitJalan\GeoIP\GeoIP;
class AttendanceController extends Controller
{

    public function index(Request $request){
      $currentDate = Carbon::now()->format('Y-m-d');
      $attendance = '';
     $attendance = DB::table('attendance')->where('date',$currentDate)->get();
     // dd($attendance);
     if($attendance  == Null){
       return redirect()->back()->with('danger','No Present Employees Today');
     } else {
     $employee_id = array();

     foreach ($attendance as $key => $value) {
       $employee_id[] = $value->employee_id;
     }

     $attendanceData = "";
       $auth_id = Auth::User()->id;
     if($request->persent ==1){

       $persent = DB::table("users")
                     ->join('attendance','users.id','attendance.employee_id')
                     ->join('employee_official_details', 'attendance.employee_id','employee_official_details.employee_id')
                     ->join('shift','employee_official_details.shift','shift.id')
                     ->whereIn('users.id',$employee_id)
                     ->where('attendance.date','=',$currentDate)
                     ->select('attendance.*','shift.shift_from','shift.shift_to','shift.working_hours','shift.working_days')
                     ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')
                     ->orderBy('attendance.created_at','desc');
                     if(Auth::User()->role_id == 1){
                       $persent->whereNotIn('role_id', [1]);
                     }
                     if(Auth::User()->role_id == 5){
                       $persent->where('users.team_leader_id', $auth_id);
                     }
                     $attendanceData = $persent->get();


                // dd($attendanceData);
              $lat = $attendanceData[0]->latitude;
              $lon = $attendanceData[0]->longitude;
              // dd($lon);
              $badge= 1;

              $Timebadge ='';
              $button ='';

              // $geocode=file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=20.0063,77.006&sensor=false');
              //
              // $output= json_decode($geocode);
              // dd($output);
              //
              // echo $output->results[0]->formatted_address;

              // die;

              $geolocation = $lat.','.$lon;
              // $request = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$geolocation.'&sensor=false';
            // $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$geolocation.'&sensor=true&key=AIzaSyDZWa1BERSX95mIDynhepLkj2caYtQ6eE4';
            //   $file_contents = file_get_contents($request);
            //   $json_decode = json_decode($file_contents);
            //   dd($json_decode);

              $city =  GeoIP::getCity();
            // dd($attendanceData);
            return view('Admin.attendance.attendance_index',compact('attendanceData','badge','city'));
          }
        }
    if($request->absent ==2){
        $absent = DB::table("users")
                     ->join('employee_official_details','users.id','employee_official_details.employee_id')
                     ->join('shift','employee_official_details.shift','shift.id')
                     ->whereNotIn('users.id',$employee_id)
                     ->select('shift.shift_from','shift.shift_to','shift.working_hours','shift.working_days','employee_official_details.*')
                     ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name');
                     if(Auth::User()->role_id == 1){
                       $absent->whereNotIn('role_id', [1]);
                     }
                     if(Auth::User()->role_id == 5){
                       $absent->where('users.team_leader_id', $auth_id);
                     }
                     $attendanceData = $absent->get();
                     $badge = 2;
                     // dd($attendanceData);
                     return view('Admin.attendance.attendance_index',compact('attendanceData','badge'));


     }
    }

    public function autocheckout(){

      // $currentDate = Carbon::now()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');


      $emp_att_data = DB::table("users")
                      ->join('attendance','users.id','attendance.employee_id')
                      ->join('attendance_activity','users.id','attendance_activity.employee_id')
                      ->join('employee_official_details', 'attendance_activity.employee_id','employee_official_details.employee_id')
                      ->join('shift','employee_official_details.shift','shift.id')
                      ->where('attendance_activity.date','=',$yesterday)
                      ->where('attendance.date','=',$yesterday)
                      ->whereNull('attendance.checkout')
                      ->whereNull('attendance_activity.checkout')
                      ->whereNotIn('role_id', [1])
                      ->select('attendance.*','attendance_activity.*','shift.shift_from','shift.shift_to','shift.working_hours','shift.working_days')
                      ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')->get();

                      $yesterday = Carbon::yesterday()->addHours(4);
                      $newDateTime = date('Y-m-d h:i ', strtotime($yesterday));

                          foreach ($emp_att_data as $key => $value) {
                            $id = $value->employee_id;
                              $data = [
                                  'checkout' => $value->shift_to,
                              ];
                              if(!empty($data)){
                                  $attendance =   DB::table('attendance')->where('employee_id',$id)->where('attendance.date','<=',$newDateTime)->update($data);
                                  $attendance_activity =   DB::table('attendance_activity')->where('employee_id',$id)->where('attendance_activity.date','<=',$newDateTime)->update($data);
                              }
                              else{
                                return redirect()->back();
                              }
                          }

    }
    public function LastWeekRecord(){
        $emp_data =  DB::table("users")
                ->join('attendance','users.id','attendance.employee_id')
                ->whereNotIn('role_id', [1])
                ->whereBetween('attendance.date', [
                       \carbon\Carbon::now()->subdays(2)->format('Y-m-d'),
                       \carbon\Carbon::now()->addDays()->format('Y-m-d')
                    ])
                ->select('attendance.*')
                ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')->orderBy('attendance.created_at','DESC')->get();


                return view('Admin.attendance.Last_Week_Record_List',compact('emp_data'));

    }
    public function Attendance_Edit($id,$date){
      // $user = Attendance::findOrfail($id);
      $user = Attendance::whereDate('date',$date)->where('id',$id)->first();
      $emp_id  = $user->employee_id;
      // $from_activity = AttendanceActivity::where('id',$emp_id)->first();
      $emp_data =  DB::table("users")
                  ->join('attendance_activity','users.id','attendance_activity.employee_id')
                  ->whereDate('attendance_activity.date',$date)
                  ->where('attendance_activity.employee_id',$emp_id)
                  ->select('attendance_activity.*')
                  ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')
                  ->orderBy('attendance_activity.created_at','desc')->first();

                return view('Admin.attendance.attendance_edit_form',compact('emp_data'));

      }
      public function Attendance_Update(Request $request){
          // dd($request->all());
          $validatedData = $request->validate([
           'checkin' => 'required',
         ]);
         $employee_id = $request->emp_id;
         $attendance_id = $request->id;

         // dd($employee_id);
         //dd($request->checkout);
         $to = \Carbon\Carbon::parse($request->checkin);
         $from = \Carbon\Carbon::parse($request->checkout);

         $working_minutes = $to->diffInMinutes($from);


         if(empty($request->checkout)){
           $currentdate = date('Y-m-d h:i:s');
           $from = \Carbon\Carbon::parse($currentdate);
           $working_minutes = $to->diffInMinutes($from);
         }


        $shift_Data = EmployeOfficialDetail::join('shift','shift.id','employee_official_details.shift')
                                            ->join('users','users.id','employee_official_details.employee_id')
                                            ->where('employee_official_details.employee_id','=',$employee_id)
                                            ->select('shift.shift','shift.shift_from','shift.shift_to','shift.working_hours','shift.working_days','employee_official_details.*')
                                            ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')->first();
                                            //dd($shift_Data);

        $time = $shift_Data->working_hours;

        $shifts_minutes = $time * 60;
        $shifts_extra_minutes = $shifts_minutes + 30;

        // dd($shifts_extra_minutes);

        if($shifts_extra_minutes < $working_minutes) {
                $extra_hours = $shifts_extra_minutes - $working_minutes;

              // $extra_minutes str_replace("-"," ",$extra_hours);
              $extra_minutes = str_replace('-', ' ', $extra_hours);
        }
        elseif($shifts_minutes > $working_minutes){
            $lake_hours = $shifts_minutes - $working_minutes;
        }
        $extra_minutes= !empty($extra_minutes) ? $extra_minutes : NULL;
        $lake_minutes = !empty($lake_hours) ? $lake_hours : NULL ;

        $activity_data = ['checkin'=>$request->checkin,'checkout'=>$request->checkout,'minutes'=>$working_minutes];

        $lastId = DB::table('attendance_activity')->where('employee_id', $request->emp_id)->whereDate('date',$request->date)->orderBy('id', 'DESC')->first();

        if(empty($request->checkout)){
          DB::table('attendance_activity')->where('id', $lastId->id)->update(['checkin'=>$request->checkin]);
          $data =['checkin'=>$request->checkin];
          DB::table('attendance')->where('employee_id', $request->emp_id)->whereDate('date',$request->date)->update($data);
        }
        else{

          DB::table('attendance_activity')->where('id', $lastId->id)->update($activity_data);
          $mints = DB::table('attendance_activity')
          ->select(DB::raw('SUM(minutes) AS minu'))
          ->where('employee_id', $request->emp_id)
          ->whereDate('date',$request->date)
          ->first();
          $working_minutes = $mints->minu;


          $data =['checkin'=>$request->checkin,'checkout'=>$request->checkout,'minutes'=>$working_minutes,'extra_minutes'=>$extra_minutes,'lake_minutes'=>$lake_minutes];
          DB::table('attendance')->where('employee_id', $request->emp_id)->whereDate('date',$request->date)->update($data);
        }



        return redirect()->route('attendance.lastweekrecord')->with('success','Record Updated Successfully');

      }

    public function ShowMap(Request $request ,$id){

      $latlong = Attendance::where('employee_id',$id)->select('latitude','longitude')->orderBy('created_at','desc')->first();
       // dd($latlong);
      return view('Admin.maps',compact('latlong'));
    }



    public function employee_attendance_store(Request $request){
      //dd($request->all());
              $geoip = new GeoIP();
                // $userip = $this->getUserIpAddr();
                $ip2 = $request->ip();
                // dd($ip2);
                $ip = $_SERVER['REMOTE_ADDR'];

                $apiURL = 'https://freegeoip.app/json/'.$ip;

                // Create a new cURL resource with URL
                $ch = curl_init($apiURL);

                // Return response instead of outputting
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // Execute API request
                $apiResponse = curl_exec($ch);

                // Close cURL resource
                curl_close($ch);

                // Retrieve IP data from API response
                $ipData = json_decode($apiResponse, true);
                  $device_id = $ipData['ip'];
                  $lat = $ipData['latitude'];
                  $lon = $ipData['longitude'];
                // $city = $ipData->city;



            $id = Auth::user()->id;
            $timezone = date_default_timezone_set('Asia/Calcutta');
            $today = Carbon::now()->format('Y-m-d h:i:s a');
            $currentDate = Carbon::now()->format('Y-m-d');
            $currentTime = Carbon::now()->format('Y-m-d h:i:s');

            // $latitude =  GeoIP::getLatitude();
            // $longitude = GeoIP::getLongitude();
            $latitude = $lat;
            $longitude = $lon;
            // dd($longitude);
            // $city = $ipData->city;

            $checkTime = db::table('attendance_activity')
              ->whereDate('date',$currentDate)->where('employee_id', $id)->where('checkout','!=',NULL)->count();
            //  dd($checkTime);
              $checkin_Limit = EmployeOfficialDetail::join('shift','shift.id','employee_official_details.shift')
                                                    ->join('users','users.id','employee_official_details.employee_id')
                                                    ->where('users.id',$id)
                                                    ->select('shift.Checkin_Checkout_Limit')
                                                    ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')->first();

            if($checkTime <= $checkin_Limit->Checkin_Checkout_Limit || $checkin_Limit->Checkin_Checkout_Limit == 0 ){

                      $employee_data = Attendance::where('attendance.employee_id',$id)
                      ->whereDate('attendance.date',$currentDate)->first();

                      $multiCheck = DB::table('attendance_activity')->where('date', $currentDate)->whereNull('checkout')->where('employee_id', $id)
                      ->orderBy('id', 'desc')->get();


                      if(empty($employee_data)){
                        $data =['employee_id'=>$id,'checkin'=>$today,'date'=>$currentDate,'latitude'=>$latitude,'longitude'=>$longitude,'divice_id'=>$device_id];
                          //dd($data);
                        DB::table('attendance')->insert($data);
                        DB::table('attendance_activity')->insert($data);

                        return redirect()->back();

                      }
                      elseif(!$multiCheck->isEmpty()){
                        // dd('sdfdsafs');
                        $checkTime = db::table('attendance_activity')
                        ->whereDate('date',$currentDate)->where('employee_id', $id)->where('checkout',NULL)->first();

                      //  dd($checkTime);

                        $start_date = new DateTime(date('Y-m-d H:i:s', strtotime($checkTime->checkin)));
                        $since_start = $start_date->diff(new DateTime(date('Y-m-d H:i:s', strtotime($today))));
                        // $hours = $since_start->h.'.'.$since_start->i;
                        // dd($since_start);
                        $hours = $since_start->h;
                        $minutes1 = $since_start->i;
                        $hours1 = $hours * 60 + $minutes1;

                         $data =['checkout'=>$today,'minutes'=>$hours1];
                         // 'latitude'=>$latitude,'longitude'=>$longitude
                         //dd($data);
                         DB::table('attendance_activity')
                            ->where('employee_id', $id)
                            ->whereNull('checkout')->update($data);


                        $totalHour  = DB::table('attendance_activity')
                                        ->select(DB::raw('SUM(minutes) AS minutes'))
                                        ->where('employee_id', $id)->whereDate('date', $currentDate)
                                        ->first();

                         $working_minutes = $totalHour->minutes;
                              // $hours = intdiv($minutes, 60).':'. ($minutes % 60);
                          $shift_Data = EmployeOfficialDetail::join('shift','shift.id','employee_official_details.shift')
                                                              ->join('users','users.id','employee_official_details.employee_id')
                                                              ->where('employee_official_details.employee_id','=',$id)
                                                              ->select('shift.shift','shift.shift_from','shift.shift_to','shift.working_hours','shift.working_days')
                                                              ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')->first();
                                                              //dd($shift_Data);

                          $time = $shift_Data->working_hours;

                          $shifts_minutes = $time * 60;
                          // dd($shifts_minutes);
                          $shifts_extra_minutes = $shifts_minutes + 30;
                          if($shifts_extra_minutes < $working_minutes) {
                                  $extra_hours = $shifts_extra_minutes - $working_minutes;
                                // $extra_minutes str_replace("-"," ",$extra_hours);
                                $extra_minutes = str_replace('-', ' ', $extra_hours);
                          }
                          elseif($shifts_minutes > $working_minutes){
                              $lake_hours = $shifts_minutes - $working_minutes;
                          }

                          $data['minutes'] = $working_minutes;
                          $data['extra_minutes'] = !empty($extra_minutes) ? $extra_minutes : NULL;
                          $data['lake_minutes'] = !empty($lake_hours) ? $lake_hours : NULL ;

                          $total_lake_minutes  = DB::table('attendance')->select(DB::raw('SUM(lake_minutes) AS lake_minutes'))
                                ->where('employee_id', $id)
                                ->first();

                          DB::table('attendance')->whereDate('date',$currentDate)->where('employee_id', $id)->update($data);
                          return redirect()->back();

                      }
                      else{

                        $data =['employee_id'=>$id,'checkin'=>$today,'date'=>$currentDate,'latitude'=>$latitude,'longitude'=>$longitude,'divice_id'=>$device_id];
                        DB::table('attendance_activity')->insert($data);
                        return redirect()->back();
                      }

            } else{

              return redirect()->back()->with('message','Your Check In Limit is Expire');

            }


    }

    public function attendanceViewEmployee(Request $request){
      $id = Auth::user()->id;
        if($request->ajax()) {
          // dd($request->all());
          $data = DB::table('attendance')
              ->select('attendance.date as date',DB::raw("'Present' as title"))
              ->where('employee_id', $id)
              ->whereBetween('attendance.date', [$request->start, $request->end])
              ->orderBy("attendance.date", "ASC")
              ->get();
               // dd($data);
          $date_1 = Carbon::parse($request->start);
          $date_2 = Carbon::parse($request->end);
          $date_diff = $date_2->diffInDays($date_1);
          $req_date_arr = [];
          for ($i=0; $i < $date_diff; $i++) {
            if($i == 0) {
              $req_date_arr[] = Carbon::parse($date_1)->format("Y-m-d");
            }
            else {
              $req_date_arr[] = Carbon::parse(array_reverse($req_date_arr)[0])->addDays(1)->format("Y-m-d");
            }
          }

          $date_arr = [];
          if($data->isNotEmpty()) {
            foreach ($data as $key => $value) {
              $date_arr[]=$value->date;
            }
            for ($i=0; $i < count($req_date_arr); $i++) {
              if(!in_array($req_date_arr[$i], $date_arr)){
                if(date("Y-m-d") > date("Y-m-d", strtotime($req_date_arr[$i]))) {
                  $add_date = new stdClass();
                  $add_date->date = $req_date_arr[$i];
                  $add_date->title = $this->AlternativeWeekend($req_date_arr[$i]) ? "Week Off" :  "Absent";
                  $this->AlternativeWeekend($req_date_arr[$i]) ? ($add_date->color = "purple") :  ($add_date->color = "#dc3545");
                  $data[] = $add_date;
                }
              }
            }
          }
          else {
            for ($i=0; $i < count($req_date_arr); $i++) {
              if(date("Y-m-d") >= date("Y-m-d", strtotime($req_date_arr[$i]))) {
                $add_date = new stdClass();
                $add_date->date = $req_date_arr[$i];
                $add_date->title = $this->AlternativeWeekend($req_date_arr[$i]) ? "Week Off" :  "Absent";
                $this->AlternativeWeekend($req_date_arr[$i]) ? ($add_date->color = "purple") :  ($add_date->color = "#dc3545");
                $data[] = $add_date;
              }
            }
          }
        
          return response()->json($data);
          }

           return view('Admin.attendance.attendance_employee');
    }

    // function isWeekend($date) {
    // return (date('N', strtotime($date)) > 6);
    // }

    function AlternativeWeekend($date){
      $firstDate = date("Y-m", strtotime($date));
      if(date('N', strtotime($date)) > 6){
        return true;
      }
      if( date('d', strtotime('first saturday of'.$firstDate)) == date('d', strtotime($date)) ) {
        return true;
      }
      if( date('d', strtotime('third saturday of'.$firstDate)) == date('d', strtotime($date)) ) {
        return true;
      }
      else{
        return false;
      }
    }


    public function ViewEmployeeAction(Request $request){
      $id = Auth::User()->id;
      $date = $request->date;

      // $activity =DB::table('attendance_activity')
      //           ->join('employee_official_details', 'attendance_activity.employee_id', '=', 'attendance_activity.employee_id')
      //           ->join('shift', 'employee_official_details.shift', '=', 'shift.id')
      //           ->select('attendance_activity.*','shift.working_hours')
      //           ->whereDate('attendance_activity.date', $date)
      //           ->where('attendance_activity.employee_id', $id)
      //           ->get();
      $activity =DB::table('attendance_activity as ac')
                ->join('employee_official_details as ed', 'ed.employee_id', '=', 'ac.employee_id')
                ->join('shift as s', 'ed.shift', '=', 's.id')
                ->select('ac.*','s.working_hours')
                ->where('ac.date', $date)
                ->where('ac.employee_id', $id)
                ->get();



                // dd($activity);



      $checkInTime = db::table('attendance')->whereDate('date',$date)->where('employee_id', $id)->first();

      if(!empty($checkInTime)){
        foreach ($activity as $key => $value) {
          $hrs = floor($checkInTime->minutes / 60);
          $mint = ($checkInTime->minutes % 60);
          $value->check_current = $hrs.".".$mint;
        }
      }

      $currentCheckIn = db::table('attendance_activity')->whereNull('checkout')->whereDate('date',$date)->where('employee_id', $id)->first();

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
    public function viewAllEmployeeAttedance(){
        return view('Admin.attendance.allAttendance');
    }

    public function working_time_count(){

      // attendance calculations Total Working Time

         $users_data = DB::table('users')->where('status',1)->get();
         $users_id = array();
         foreach ($users_data as $key => $value) {
           $users_id[] = $value->id;
         }
         $total_minutes  = DB::table('attendance')
                               ->join('users','users.id','attendance.employee_id')
                               ->whereBetween('attendance.date', [
                                          \carbon\Carbon::now()->subdays(3)->format('Y-m-d'),
                                          \carbon\Carbon::now()->addDays()->format('Y-m-d')
                                       ])->whereIn('employee_id',$users_id)
                                    ->select('attendance.employee_id')
                                    ->selectRaw('SUM(minutes) AS total_working_minutes,
                                         SUM(extra_minutes) AS total_extra_minutes,
                                         SUM(lake_minutes) AS total_lake_minutes,
                                         CONCAT(users.firstname, " ", users.lastname) as full_name')
                                   ->GroupBy('attendance.employee_id')->get();

                        // dd($total_minutes);

           $lake_minutes = array();
           foreach ($total_minutes as $key => $value) {

               if($value->total_lake_minutes >= 35){

                 $minutes=$value->total_lake_minutes;

                   $hours = intdiv($minutes, 60).':'. ($minutes % 60);
                   // dd($hours);
                 $lake_minutes = array(
                       "employee_id" => $value->employee_id,
                       "lake_minutes" => $hours,
                       "employee_name" => $value->full_name,
                       "message" => "Your " .$hours. " Workking Hours Remaining in Working Hours"
                 );
                 // dd($lake_minutes);
                 $notification = Helpers::Addnotification($lake_minutes);
               }
               elseif($value->total_lake_minutes <= 35){
                 // echo "else";
                 $lake_minutes ["employee_id"] = $value->employee_id;
                 $lake_minutes ["employee_name"] = $value->full_name;
               }
           }
         // attendance calculations end

    }
    // public function per_employee_attend($emp_id,$date){
    //   return view('Admin.attendance.attendance_employee');
    // }

    // public function absent_employee(Request $request){
    //
    //   $currentDate = Carbon::now()->format('Y-m-d');
    //
    //  $attendance = DB::table('attendance')->where('date',$currentDate)->get();
    //
    //  $employee_id = array();
    //  foreach ($attendance as $key => $value) {
    //    $employee_id[] = $value->employee_id;
    //  }
    //   $absent = DB::table("users")
    //           // ->join('employee_official_details','users.id','employee_official_details.employee_id')
    //           // ->join('shift','employee_official_details.shift','shift.id')
    //           ->whereNotIn('users.id',$employee_id)->whereNotIn('role_id', [1])
    //           // ->select('shift.shift_from','shift.shift_to','shift.working_hours','shift.working_days')
    //           ->selectRaw('CONCAT(users.firstname, " ", users.lastname) as full_name')
    //           ->get();
    //
    //
    //           return view('Admin.attendance.absent_index',compact('absent'));
    //
    // }

    // public function employee_index(Request $request){
    //     $check_button='';
    //     $currentDate = Carbon\Carbon::now()->format('Y-m-d');
    //     $id = Auth::user()->id;
    //     $employee_data = Attendance::where('attendance.employee_id',$id)
    //     ->whereDate('attendance.date',$currentDate)->first();
    //       if(!empty($employee_data)){
    //         $check_button = 2;
    //       }
    //       else{
    //         $check_button = 1;
    //       }
    //       dd($employee_data);
    //     return view('Employees.dashboard',compact('check_button','employee_data'));
    // }




}
