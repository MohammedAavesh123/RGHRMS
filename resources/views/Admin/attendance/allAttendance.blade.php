@extends('layouts.master')
@section('content')


    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">All Employees Attendance</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Attendance</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <?php
              $year = @$_GET["year"] != "" ? $_GET["year"] : date("Y");
              $month = @$_GET["month"] != "" ? $_GET["month"] : date("m");
              $name = @$_GET["employeeName"] != "" ? $_GET["employeeName"] :'';

            ?>

            <!-- Search Filter -->
            <form action="{{ route('allattendance.list') }}" method="get">

            <div class="row filter-row">
                <div class="col-sm-6 col-md-3">
                    <div class="form-group form-focus">
                        <input type="text" name="employeeName" value="{{$name}}" class="form-control floating">
                        <label class="focus-label">Employee Name</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group form-focus select-focus">
                        <select class="select floating" name="month">
                            <option value="">Select Month</option>
                            @for($i=1; $i<=12;$i++)
                                <option value="{{strlen($i) == 1 ? '0'.$i : $i}}" {{$i == (int)$month ? 'selected' : ''}} >{{date("M", strtotime($year."-".$i))}}</option>
                            @endfor
                        </select>
                        <label class="focus-label">Select Month</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group form-focus select-focus">
                        <select class="select floating" name="year">
                            <option value="">Select Year</option>
                            <option value="2022" @if (date('Y') == '2022') selected="selected" @endif>2022</option>
                            <option value="2021" @if (date('Y') == '2021') selected="selected" @endif>2021</option>
                            <option value="2020" @if (date('Y') == '2020') selected="selected" @endif>2020</option>
                            <option value="2019" @if (date('Y') == '2019') selected="selected" @endif>2019</option>
                        </select>
                        <label class="focus-label">Select Year</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <button type="submit" class="btn btn-success btn-block"> Search </button>
                </div>
            </div>
            </form>
            <!-- /Search Filter -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table custom-table table-striped table-nowrap mb-0">
                            <thead>
                              @if($req_date_arr)
                                <tr>
                                    <th style="">Employee</th>
                                    @foreach($req_date_arr[array_keys($req_date_arr)[0]]["date"]  as $key => $val)
                                    <th class="border">{{date('M', strtotime($key))}} {{date('d', strtotime($key))}}<br>
                                      <small>{{date('D', strtotime($key))}}</small>
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                              @foreach($req_date_arr  as $key => $val)
                                <tr>
                                  <td>{{$val['name']}}</td>
                                  @foreach($val["date"] as $d_key => $d_value)
                                  <?php $firstDate = date("Y-m", strtotime($d_key)); ?>
                                    @if(date("Y-m-d") < date("Y-m-d", strtotime($d_key)))
                                     <td class="border text-center"></td>
                                    @else
                                     @if($d_value =="P")

                                       <td class="border text-center">

                                        @if (!empty($val['from_date']))
                                          <a class="text-dark ViewActivity" data-date="{{$d_key}}" data-id="{{$key}}" href="javascript:void(0);" data-toggle="modal" data-target="#attendance_info">
                                            <div class=" mb-0 pt-2 pb-2 cardChange present1">WFH/P</div>
                                          </a>
                                        @else
                                        <a class="text-dark ViewActivity" data-date="{{$d_key}}" data-id="{{$key}}" href="javascript:void(0);" data-toggle="modal" data-target="#attendance_info">
                                          <div class=" mb-0 p-2 cardChange present">P</div>
                                        </a>
                                        @endif
                                      </td>
                                      @elseif(date('N', strtotime($d_key)) > 6 )
                                      <td class="border text-center" style="background-color:#fcf8e3;">
                                        <a class="text-dark" href="javascript:void(0);">
                                          <div class=" mb-0 p-2 cardChange present">OFF</div>
                                        </a>
                                      </td>
                                      @elseif(date('d', strtotime('first saturday of'.$firstDate)) == date('d', strtotime($d_key)))
                                      <td class="border text-center" style="background-color:#fcf8e3;">
                                        <a class="text-dark" href="javascript:void(0);">
                                          <div class=" mb-0 p-2 cardChange present">OFF</div>
                                        </a>
                                      </td>
                                      @elseif(date('d', strtotime('third saturday of'.$firstDate)) == date('d', strtotime($d_key)))
                                      <td class="border text-center" style="background-color:#fcf8e3;">
                                        <a class="text-dark" href="javascript:void(0);">
                                          <div class=" mb-0 p-2 cardChange present">OFF</div>
                                        </a>
                                      </td>
                                       @else
                                      <td class="border text-center">
                                        <a class="text-dark" href="javascript:void(0);">
                                          <div class=" mb-0 p-2 cardChange absent">A</div>
                                        </a>
                                      </td>
                                    @endif
                                  @endif
                                  @endforeach
                                </tr>
                              @endforeach
                              @else
                              <tr>
                                  <th>Data not found</th>
                              </tr>
                              @endif
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation">
                      <ul class="pagination mt-4">
                        <li class="page-item">
                          <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">Previous</span>
                          </a>
                        </li>
                        <?php

                        for($page = 1; $page<= $number_of_page; $page++) {
                            echo '<li class="page-item"><a class="page-link" href ="all-employee-attendance?page=' . $page . '&employeeName=' . $name . '&month=' . $month . '&year=' . $year . '">' . $page . ' </a></li>';

                        }
                        ?>
                        <li class="page-item">
                          <?php
                          echo '<li class="page-item"><a class="page-link" href ="all-employee-attendance?page=' . $page  . '&employeeName=' . $name . '&month=' . $month . '&year=' . $year . '">Next </a></li>';
                          ?>

                        </li>
                      </ul>
                    </nav>
                </div>
            </div>
        </div>
        <!-- /Page Content -->

        <!-- Attendance Modal -->
        <div class="modal custom-modal fade" id="AllAttendance_info" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Attendance Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card punch-status">
                                    <div class="card-body">
                                        <h5 class="card-title">Timesheet <small class="text-muted" id="dateInfo"></small></h5>
                                        <!-- <div class="punch-det">
                                            <h6>Punch In at</h6>
                                            <p>Wed, 11th Mar 2019 10.00 AM</p>
                                        </div> -->
                                        <div class="punch-info">
                                            <div class="punch-hours">
                                                <span id="workingHour"></span>
                                            </div>
                                        </div>
                                        <!-- <div class="punch-det">
                                            <h6>Punch Out at</h6>
                                            <p>Wed, 20th Feb 2019 9.00 PM</p>
                                        </div> -->
                                        <div class="statistics">
                                            <div class="row">
                                                <div class="col-md-6 col-6 text-center">
                                                    <div class="stats-box">
                                                        <p>Break</p>
                                                        <h6>1.21 hrs</h6>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-6 text-center">
                                                    <div class="stats-box">
                                                        <p>Overtime</p>
                                                        <h6>0 hrs</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card recent-activity">
                                    <div class="card-body">
                                        <h5 class="card-title">Activity</h5>
                                        <div class="fixed-card-acticity">
                                        <ul class="res-activity-list" id="CheckInActivity">
                                        </ul>
                                      </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Attendance Modal -->

    </div>
    <!-- Page Wrapper -->
    <style>
    .cardChange{
      font-size: 10px;
      width: 40px;
      text-align: center;
      margin-left: auto;
      margin-right: auto;
    }
    .present{
      border-bottom:2px solid green;
    }
    .present1{
      border-bottom:2px solid #1673d7;
    }
    .absent{
      border-bottom:2px solid red;
    }
    </style>



<script>

$('body').on('click', '.ViewActivity', function (event) {

  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    event.preventDefault();
    var id = $(this).data('id');
    var date = $(this).data('date');
    var dayFormat = moment(date).format('ddd, DD MMM YYYY');

      $.ajax({
         type:'POST',
         url:"{{ route('employee.activity') }}",
         data:{id:id, date:date},
         success:function(data){
           var html ='';
           $.each(data, function(index, item) {
             var checkin = item.checkin.substr(-11);
             html +='<li><p class="mb-0">Check In</p><p class="res-activity-time"><i class="fa fa-clock-o"></i> '+checkin+'.</p></li>';
             if(item.checkout){
               var checkout = item.checkout.substr(-11);
               html +='<li><p class="mb-0">Check Out</p><p class="res-activity-time"><i class="fa fa-clock-o"></i> '+checkout+'.</p></li>';
             }
         });

          $('#AllAttendance_info').modal('show');
          $("#workingHour").text(data[0].check_current +' '+'hrs');
          $("#dateInfo").text(dayFormat);
          $('#CheckInActivity').html(html);

         }
      });


});

</script>
@endsection
