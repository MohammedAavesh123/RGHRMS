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

     @foreach($attendanceData as $value)
     <?php
      $shiftTime_At = $value->shift_from;
      $shiftTime_To = $value->shift_to;
       $today_date =  Carbon\Carbon::now()->format('d-m-Y');
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
         $Timebadge = '';
         // dd($shiftTime_At);
         $enddate =  date('h:i:s a', strtotime($shiftTime_At) + (15*60));

         $checkInTime = date('h:i:s a', strtotime(!empty($value->checkin) ? $value->checkin : ''));


         $shiftTime_limit = date('h:i:s a', strtotime(!empty($enddate) ? $enddate : ''));
         // dd($shiftTime_limit);
                    // Too Late
         $break_1_start = Carbon\Carbon::createFromFormat('H:i a', $checkInTime);
         $break_1_ends = Carbon\Carbon::createFromFormat('H:i a', $shiftTime_At);
         $hours_diff_mint =$break_1_ends->diff($break_1_start)->format('%h.%i')." Minutes";

                // late
         $break_1_start = Carbon\Carbon::createFromFormat('H:i a', $checkInTime);
         $break_1_ends = Carbon\Carbon::createFromFormat('H:i a', $shiftTime_At);
         $hours_diff_mint_late =$break_1_ends->diff($break_1_start)->format('%h.%i')." Minutes";
        // dd($hours_diff_mint);
        //dd($checkInTime);
         if($checkInTime > $shiftTime_limit ){

            $LimitlateTime = $hours_diff_mint;
            // dd($LimitlateTime);
            if(!empty($LimitlateTime)){

                $Timebadge.= $LimitlateTime;
                    // dd($Timebadge);

           }
           //dd($data);
         }
         elseif($checkInTime < $shiftTime_limit && $checkInTime > $shiftTime_At ){
           // dd('here');
                $lateTime_mint = $hours_diff_mint_late;
                 $Timebadge.=$lateTime_mint;



         }
         elseif($checkInTime == $shiftTime_limit){
           $onTime =  $checkInTime;
            $Timebadge.=$onTime;
         }
         else{
           // dd('gggvvv');
           $early = $checkInTime." Early";
             //dd($Timebadge);
         }
      }

     ?>
     @if($badge == 1)
     <tr>
       <td class="text-center">{{ $today_date }}</td>
       <td class="text-center">{{ $name }}</td>
       <td class="text-center">{{$shiftTime_At}} -  {{$shiftTime_To}}</td>
       <td class="text-center"><span class="badge badge-warning badge-pill">Persent</span></td>
             @if(!empty($onTime))
               <td class="text-center">{{$hours_diff_mint_late}}<span class="badge badge-success badge-pill float-right w-20 mt-1">On Time</span></td>
             @elseif(!empty($LimitlateTime))
              <td class="text-center">{{$hours_diff_mint_late}}<span class="badge badge-warning badge-pill float-right w-20 mt-1">Late</span></td>
             <!-- $LimitlateTime -->
             @elseif(!empty($early))
              <td class="text-center">{{$hours_diff_mint_late}}<span class="badge badge-success badge-pill float-right w-20 mt-1">Early</span></td>
             @else
               <td class="text-center">{{$hours_diff_mint_late}}<span class="badge badge-danger badge-pill float-right w-20 mt-1">Late</span></td>
             @endif
       <td class="text-center">{{$checkInTime}} </td>
       <td class="text-center">{{$checkout}}</td>
       <td><a  href="{{url('showmap/'.$value->employee_id)}}" class="btn btn-primary  btn-sm"  data-toggle="tooltip" data-placement="bottom" title="Show Location" style="margin:3px;"> {{$city}}</a></td>
     </tr>
     @else
     <tr>
       <td class="text-center">{{ $today_date }}</td>
       <td class="text-center">{{ $value->full_name }}</td>
       <td class="text-center">{{$shiftTime_At}} -  {{$shiftTime_To}}</td>
        <td class="text-center"><span class="badge badge-danger badge-pill">Absent</span></td>
       <td class="text-center">-</td>
       <td class="text-center">- </td>
       <td class="text-center">-</td>
       <td class="text-center">-</td>
     </tr>
     @endif
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
