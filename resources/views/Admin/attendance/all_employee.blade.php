@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
<?php $userid = Auth::user()->id; ?>

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <!-- Page Content -->
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Attendance</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Attendance </li>
                        </ul>
                    </div>

                    <!-- <div class="col-auto float-right ml-auto">
                        <a href="{{ route('interviews.create') }}" class="btn btn-info"><i class="fa fa-plus"></i> Add Interview Round</a>
                    </div> -->


                </div>
            </div>
            <!-- /Page Header -->
            <div class="row">
                <div class="col-md-12">
                @if(Session::has('success'))
                    <p class="alert {{ Session::get('alert-class', 'alert-success') }}">{{ Session::get('success') }}</p>
                    @endif
                     <div class="table-responsive">
                       <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
   <thead>
     <tr>
       <th class="th-sm text-center">Date</th>
       <th class="th-sm text-center">Name</th>
        <th class="th-sm text-center">Shift Time</th>
       <th class="th-sm text-center">Attendance</th>
       <th class="th-sm text-center">Status</th>
       <th class="th-sm text-center">Check In</th>
       <th class="th-sm text-center">Check Out</th>
       <th class="th-sm text-center">Action</th>
     </tr>
   </thead>
   <tbody>


               <?php $today_date =  Carbon\Carbon::now()->format('Y-m-d');?>
                    @foreach($attendanceData as $key =>  $value)
                         <?php
                           if($value->date == NULL){
                             $badge = 0;
                             $shiftTime_At = $value->shift_from;
                             $shiftTime_To = $value->shift_to;

                           }
                           else{
                             $badge = 1;
                             if($badge == 1){
                               if(!empty($value->checkout)){
                                 $checkout = date('h:i:s a', strtotime($value->checkout));
                               }
                               else{
                                 $checkout = " - ";
                               }
                               $today_date =  Carbon\Carbon::now()->format('d-m-Y');
                                 $name = $value->full_name;
                                $created_at = !empty($value->created_at) ? $value->created_at : '' ;
                                $shiftTime_At = !empty($value->shift_from) ? $value->shift_from : '';

                                $shiftTime_To = !empty($value->shift_from) ? $value->shift_to : '';
                                $Timebadge = '';
                                // dd($shiftTime_At);
                                $enddate =  date('h:i A ', strtotime($shiftTime_At) + (15*60));
                               // echo $enddate;
                                // $shiftTime_At_ontime_limit = date('h:i:s a', strtotime($shiftTime_At) + (15*60));
                                // echo $shiftTime_At."<br/>";

                                $checkInTime = date('h:i A', strtotime(!empty($value->checkin) ? $value->checkin : ''));

                                // echo $checkInTime;
                                $shiftTime_limit = date('h:i A', strtotime(!empty($enddate) ? $enddate : ''));
                                           // Too Late
                                   // echo"<br>".$shiftTime_limit;
                                $break_1_start = Carbon\Carbon::createFromFormat('h:i A', $checkInTime);
                                $break_1_ends = Carbon\Carbon::createFromFormat('h:i A', $shiftTime_At);
                                // echo"<br>".$break_1_start;
                                // echo"<br>".$break_1_ends;
                                $hours_diff_mint =$break_1_ends->diff($break_1_start)->format('%h:%i')."Hours";
                                // echo"<br>".$hours_diff_mint;
                                       // late
                                // $break_1_start = Carbon\Carbon::createFromFormat('H:i a', $checkInTime);
                                // $break_1_ends = Carbon\Carbon::createFromFormat('H:i a', $shiftTime_At);
                                // $hours_diff_mint_late =$break_1_ends->diff($break_1_start)->format('%h.%i')." Hours";
                                // dd($hours_diff_mint);

                                // echo strtotime($checkInTime);exit;
                                // echo "$checkInTime=$shiftTime_At=$shiftTime_limit";exit;

                                // echo date("Y-m-d H:i:s", strtotime($value->checkin))."=".date("Y-m-d H:i:s", strtotime($value->shift_from))."===<br>";

                                // $value->checkin = empty($value->checkin) ? "0000-00-00 00-00-00" : $value->checkin;
                                // $value->shift_from = empty($value->shift_from) ? "0000-00-00 00-00-00" : $value->shift_from;
                                $value->shift_from = date("Y-m-d", strtotime($today_date))." ".$value->shift_from;
                                $early = "";
                                $onTime = "";
                                $lateTime_mint = "";



                                if(date("Y-m-d H:i:s", strtotime($value->checkin)) < date("Y-m-d H:i:s", strtotime($value->shift_from))) {
                                  $early = $hours_diff_mint;
                                  // dd('gggg');
                                  // echo "late";

                                   // echo"<br> late ".$lateTime_mint;
                                  //dd($data);
                                }
                                elseif((date("Y-m-d H:i:s", strtotime($value->checkin)) > date("Y-m-d H:i:s", strtotime($value->shift_from))) && (date("Y-m-d H:i:s", strtotime($value->checkin)) < date("Y-m-d H:i:s", strtotime("+15 minutes", strtotime($value->shift_from))))) {
                                  // dd('here');
                                       // $lateTime_mint = $hours_diff_mint;
                                       $onTime = $hours_diff_mint;
                                       // echo"<br> onTime1 ".$onTime;
                                }
                                else if (date("Y-m-d H:i:s", strtotime($value->checkin)) > date("Y-m-d H:i:s", strtotime("+15 minutes", strtotime($value->shift_from)))) {
                                  $lateTime_mint = $hours_diff_mint;
                                }
                                else{
                                  // dd('gggvvv');
                                  $early = $checkInTime." Early";
                                  // echo"<br>".$early;
                                    //dd($Timebadge);
                                }

                             }
                           }?>

                           <tr>
                               <td class="text-center">{{ $today_date }}</td>
                               <td class="text-center">{{ $value->full_name }}</td>

                               @if($badge == 1)
                                 <td class="text-center">{{$shiftTime_At}} -  {{$shiftTime_To}}</td>
                                 <td class="text-center"><span class="badge badge-warning badge-pill">Present</span></td>
                                 @if(strlen($early) > 1)
                                   <td class="text-center">{{$hours_diff_mint}}<span class="badge badge-success badge-pill float-right w-20 mt-1">Early</span></td>
                                 @elseif(strlen($onTime) > 1)
                                   <td class="text-center">{{$hours_diff_mint}}<span class="badge badge-success badge-pill float-right w-20 mt-1">On Time</span></td>
                                 @elseif(strlen($lateTime_mint) > 1)
                                   <td class="text-center">{{$hours_diff_mint}}<span class="badge badge-warning badge-pill float-right w-20 mt-1">Late</span></td>
                                 @else
                                   <td></td>
                                 @endif
                                 <td class="text-center">{{$checkInTime}} </td>
                                 <td class="text-center">{{$checkout}}</td>
                                 <td class="text-center"><a  href="{{url('showmap/'.$value->employee_id)}}" class="btn btn-primary  btn-sm"  data-toggle="tooltip" data-placement="bottom" title="Show Location" style="margin:3px;"> {{$city}}</a><a  href="{{url('attendance-edit/'.$value->id)}}" class="btn btn-primary  btn-sm"  data-toggle="tooltip" data-placement="bottom" title="Attendance Edit" style="margin:3px;">Edit</a></td>
                               @else
                                 <td class="text-center">{{$shiftTime_At}} -  {{$shiftTime_To}}</td>
                                 <td class="text-center"><span class="badge badge-danger badge-pill">Absent</span></td>
                                 <td class="text-center"> - </td>
                                 <td class="text-center"> - </td>
                                 <td class="text-center"> - </td>
                                 <td class="text-center"> - </td>
                               @endif
                             </tr>
                   @endforeach
                </tbody>

                    </table>
                       </div>
                     </div>
                 </div>
             </div>
         <!-- /Page Content -->

     </div>

       <script type="text/javascript">
       $(document).ready(function () {
           $('#dtBasicExample').DataTable();
           $('.dataTables_length').addClass('bs-select');
           });
       </script>
     <!-- <script type="text/javascript">
       $(document).ready(function() {
         $.fn.dataTable.ext.errMode = 'none';
         $('#datatable').DataTable({
             order: [4,'desc'],
             "processing": true,
             "serverSide": true,
             ajax: "{{ route('attendance.info') }}",
             // console.log(data);
             "columns": [
                 {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false},
                 {data: 'created_at', name: 'created_at'},
                 {data: 'employee_id', name: 'employee_id'},
                 {data: 'full_name', name: 'full_name'},
                 {data: 'attendance_status', name: 'attendance_status'},
                 {data: 'CheckTime', name: 'CheckTime'},
                 {data: 'checkin', name: 'checkin'},
                 {data: 'checkout', name: 'checkout'},
                 {data: 'action', name: 'action', orderable: false, searchable: false},
             ],
             columnDefs: [ {
                 'targets': [0], /* column index [0,1,2,3]*/
                 'bSortable': false, /* true or false */
             }]
         });
       });
     </script> -->
     @endsection
