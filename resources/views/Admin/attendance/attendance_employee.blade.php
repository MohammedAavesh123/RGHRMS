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
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Attendance</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Attendance</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
                <div id="attendance"></div>
              </div>
            </div>

            <!-- Personal Info Modal -->
            <div id="AttendanceActivity" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Attendance Activity</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                          <div class="row">

                              <div class="col-md-6">
                                  <div class="card att-statistics">
                                      <div class="card-body">
                                          <h5 class="card-title">Statistics</h5>
                                          <div class="stats-list">
                                              <div class="stats-info">

                                                  <!-- <p>Today <strong>3.45 <small>/ <span id="working_hour"></span> hrs</small></strong></p> -->
                                                  <p><span id="dateShow"></span> <strong><span class="check_current"></span> <small>/ <span id="working_hour"></span> hrs</small></strong></p>

                                                  <div class="progress">
                                                      <div class="progress-bar bg-primary" role="progressbar" id="hourCont" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                  </div>
                                              </div>
                                              <!-- <div class="stats-info">
                                                  <p>This Week <strong>28 <small>/ 40 hrs</small></strong></p>
                                                  <div class="progress">
                                                      <div class="progress-bar bg-warning" role="progressbar" style="width:31%" aria-valuenow="31" aria-valuemin="0" aria-valuemax="100"></div>
                                                  </div>
                                              </div>
                                              <div class="stats-info">
                                                  <p>This Month <strong>90 <small>/ 160 hrs</small></strong></p>
                                                  <div class="progress">
                                                      <div class="progress-bar bg-success" role="progressbar" style="width: 62%" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                                                  </div>
                                              </div>
                                              <div class="stats-info">
                                                  <p>Remaining <strong>90 <small>/ 160 hrs</small></strong></p>
                                                  <div class="progress">
                                                      <div class="progress-bar bg-danger" role="progressbar" style="width: 62%" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                                                  </div>
                                              </div>
                                              <div class="stats-info">
                                                  <p>Overtime <strong>4</strong></p>
                                                  <div class="progress">
                                                      <div class="progress-bar bg-info" role="progressbar" style="width: 22%" aria-valuenow="22" aria-valuemin="0" aria-valuemax="100"></div>
                                                  </div>
                                              </div> -->
                                              <div class="mt-5">
                                                  <div class="card p-2 pt-4 mt-5">
                                                      <p>Today Working Hrs: <span class="check_current"></span> Minutes</p>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="card recent-activity">
                                      <div class="card-body">
                                          <h5 class="card-title">Today Activity</h5>
                                          <div class="fixed-card-acticity">
                                            <ul class="res-activity-list" id="activity">
                                              <!-- data in loop -->
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
            <!-- /Personal Info Modal -->
        <!-- /Page Content -->
        <style>
        .fc-day-grid-event{
          text-align: center;
          padding: 3px;
          border: none;
          background: #e3f4db;
        }
        .fc-unthemed td.fc-today {
          /* background: #e3f4db; */
          background: none;
        }
        .fc-title{
          color: #fff;
          font-size: 16px;
        }

        </style>
        <script>
        // Addclss();
        // function Addclss(){
        //  var textColor = document.querySelector('.fc-content').textContent;
        //   if(textColor =="Absent"){
        //     $(".fc-content").css("background-color", "yellow");
        //   }
        // }
        $(document).ready(function () {
            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                }
            });

            var calendar = $('#attendance').fullCalendar({
                editable:false,
                displayEventTime: false,
                header:{
                    left:'prev,next today',
                    center:'title',
                    right:'month,agendaWeek,agendaDay'
                },
                events:"{{ url('/employee-attendance-show') }}",
                selectable:true,
                eventColor: 'green',
                selectHelper: true,
                eventClick:function(event)
                {
                        var date = event.date;
                        // alert(date);
                        $.ajax({
                            url:"{{ url('/employee-attendance-action') }}",
                            type:"POST",
                            data:{
                                date:date,
                            },
                            success:function(response)
                            {
                              console.log(response);
                              $('#dateShow').text(date);
                              var html ='';
                              $("#AttendanceActivity").modal('show');
                              if(response !=''){
                                $.each(response, function(index, item) {
                                  var checkin = item.checkin.substr(-11);
                                  html +='<li><p class="mb-0">Check In</p><p class="res-activity-time"><i class="fa fa-clock-o"></i> '+checkin+'.</p></li>';
                                  if(item.checkout){
                                    var checkout = item.checkout.substr(-11);
                                    html +='<li><p class="mb-0">Check Out</p><p class="res-activity-time"><i class="fa fa-clock-o"></i> '+checkout+'.</p></li>';
                                  }
                              });
                                var working_hour = response[0].working_hours;
                                // hk code
                                // var working_week = response[0].working_hours * 7;
                                // alert(working_week);

                              var working_mint = response[0].working_hours * 60;

                              // var break_mint = response[0].minutes - 60;
                              //  alert(break_mint)
                               // var without_break = working_mint - 60;

                              // var test = 100 / without_break;
                                var test = 100 / working_mint;
                                var check_time = response[0].check_current;

                                // if(check_time > 8){
                                //     var check_time = response[0].check_current - 1;
                                // }

                                var check_mint = response[0].check_current * 60;

                                 // alert(check_mint);
                                $("#hourCont").width(test * check_mint+"%");
                                $('#activity').html(html);
                                $('#working_hour').text(working_hour);
                                $('.check_current').text(check_time);
                              }
                              else{
                                  $('#activity').html('Data not found');
                                  $('#working_hour').text('00.00');
                                  $('.check_current').text('00.00');
                                  $("#hourCont").width("0%");
                              }

                            }
                        })
                }

            });
        });

        demo();
        function demo(){

        }
        </script>

@endsection
