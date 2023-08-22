@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <!-- Page Content -->
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Leave Reports</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('posts.list') }}">Leave Reports</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            <div class="row">
              <div class="col-12 col-lg-3">
                <div class="card">
                <div class="card-body">
                  <div class="text-center">
                    <h1 class="text-warning">Leaves</h1>
                  </div>
                  <div class="row mt-4">
                    <div class="col-6">
                      <p class="m-0">Available</p>
                    </div>
                    <div class="col-6">
                      <!-- <p class="m-0">: {{ $availableleave }}</p> -->
                      <p class="m-0">: {{ $availablePL }}</p>
                    </div>
                    <div class="col-6">
                      <p class="m-0">Taken</p>
                    </div>
                    <div class="col-6">
                      <p class="m-0">: {{ $totalLeave }}</p>
                    </div>

                  </div>
                </div>
              </div>
              </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                <form action="{{ route('leaves.store') }}" method="POST" id="leaveApply" name="leaveApply">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Team Leader Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{ @$tlName->firstname; }} {{ @$tlName->lastname; }}" readonly>
                                        @if ($errors->has('tl_name'))
                                            <span class="text-danger">{{ $errors->first('tl_name') }}</span>
                                        @endif
                                        <!-- <label class="col-form-label">Team Leader Name <span class="text-danger">*</span></label>

                                        <select class="select form-control" id="tl_name" name="tl_name">

                                          <option value="">Select Team Leader Name</option>
                                            @foreach($tlname as $name)

                                              <option value="{{$name->firstname}} {{$name->lastname}}" {{(old('tl_name') == $name?'selected':'')}} >{{$name->firstname}} {{$name->lastname}}</option>
                                            @endforeach
                                        </select> -->

                                        @if ($errors->has('tl_name'))
                                            <span class="text-danger">{{ $errors->first('tl_name') }}</span>
                                        @endif
                                        <input type="hidden" class="form-control" name="manager_id" value="{{ @$tlName->id; }}">
                                        @if(!empty($tlName->email))
                                        <input type="hidden" class="form-control" name="tl_email" value="{{ @$tlName->email; }}">
                                        @else
                                        <input type="hidden" class="form-control" name="tl_email" value="{{ @$tlName->personal_email; }}">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                     <div class="form-group">
                                      <label class="col-form-label">From Date <span class="text-danger">*</span></label>
                                       <input autocomplete="off" type="text" class="Datepicker form-control from_date" value="{{ old('from_date') }}" id="from_date" name="from_date" placeholder="Select From Date">
                                       @if ($errors->has('from_date'))
                                       <span class="text-danger">{{ $errors->first('from_date') }}</span>
                                       @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="col-form-label">To Date <span class="text-danger">*</span></label>
                                            <input autocomplete="off" class="form-control to_date" type="text" value="{{ old('to_date') }}" name="to_date" id="to_date" placeholder="Select To Date">
                                            @if ($errors->has('to_date'))
                                            <span class="text-danger">{{ $errors->first('to_date') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Leave Type <span class="text-danger">*</span></label>
                                        <select class=" form-control{{($errors->first('leave_type') ? " form-error" : "")}}" id="leave_type" name="leave_type">
                                            <option value="">Select Leave Type</option>
                                           <!-- <option value="casual_leave" @if (old('leave_type') == 'casual_leave') selected="selected" @endif>Casual Leave</option>
                                           <option value="sick_leave" @if (old('leave_type') == 'sick_leave') selected="selected" @endif>Sick Leave</option> -->
                                           <option value="paid_leave" @if (old('leave_type') == 'paid_leave') selected="selected" @endif>Paid Leaves</option>
                                           <option value="other" @if (old('leave_type') == 'other') selected="selected" @endif>Other</option>
                                        </select>
                                        @if ($errors->has('leave_type'))
                                            <span class="text-danger">{{ $errors->first('leave_type') }}</span>
                                        @endif
                                    </div>
                                </div>

                       <div class="col-sm-4">
                       <label class="col-form-label">Leave (Days) <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="appied_leave_days" id="days" onkeyup="numOnly(this)" onblur="numOnly(this)"  placeholder="Enter Leave (Days)" >
                        @if ($errors->has('appied_wfh_days'))
                        <span class="text-danger">{{ $errors->first('appied_wfh_days') }}</span>
                        @endif
                    </div>



                      <div class="col-sm-4">
                       <label class="col-form-label">Day Type<span class="text-danger">*</span></label>
                        <select class=" form-control" id="day_type" name="day_type">
                          <option value="">Select Day Type</option>
                         <option value="full_day" @if (old('day_type') == 'full_day') selected="selected" @endif>Full Day</option>
                         <option value="1st_half" @if (old('day_type') == '1st_half') selected="selected" @endif>1st Half Day</option>
                         <option value="2nd_half" @if (old('day_type') == '2nd_half') selected="selected" @endif>2nd Half Day</option>
                         <!-- <option value="Late check in" @if (old('day_type') == 'quarter') selected="selected" @endif>Late check in</option>
                         <option value="Early check out" @if (old('day_type') == 'quarter') selected="selected" @endif>Early check out</option> -->
                        </select>
                         @if ($errors->has('day_type'))
                         <span class="text-danger">{{ $errors->first('day_type') }}</span>
                         @endif
                    </div>
                     <div class="col-sm-12">
                         <div class="form-group">
                        <label class="col-form-label">Leave Reason <span class="text-danger">*</span></label>
                          <textarea class="w-100 form-control" id="reason" name="reason">{{ old('reason') }}</textarea>
                                @if ($errors->has('reason'))
                                    <span class="text-danger">{{ $errors->first('reason') }}</span>
                                @endif
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <a href="{{ route('leaves.view') }}" type="button" class="btn btn-danger">Cancel</a>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    <!-- /Page Content -->
</div>
<script src = "https://code.jquery.com/jquery-3.5.1.slim.min.js" ></script>
<script src= "https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.min.js" > </script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script>
$(document).ready(function () {
     $('#leaveApply').validate({
      rules: {
        from_date: {
          required: true,
        },
        to_date: {
          required: true,
        },
        leave_type: {
          required: true,
        },
        appied_leave_days: {
          required: true,
        },
        day_type: {
          required: true,
        },
        reason: {
          required: true,
        }
      },
      messages: {
        from_date: {
          required: 'Please enter from date.',
        },
        to_date: {
          required: 'Please enter to date.',
        },
        leave_type: {
          required: 'Select leave type.',
        },
        appied_leave_days: {
          required: 'Please enter applied for days.',
        },
        day_type: {
          required: 'Please enter days type',
        },
        reason: {
          required: 'Please enter reason.',
        }
      },
      submitHandler: function (form) {
        var av ="{{$availableleave}}";
        var takeLeave = $("#days").val();
        // var av = 4;
        // var takeLeave = 5;
        var max = 3;
        var minus = takeLeave - av;
        var minPl = takeLeave - max;

if(max <= av){
	if(takeLeave <= max){
    swal({
      title: "",
      text: "You have "+takeLeave+" PL for leave.",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-success",
      confirmButtonText: "Apply",
      closeOnConfirm: false
      },
      function(){
        swal("Leave Apply!", "Your leave apply has been sent.", "success");
        form.submit();
      });
    // if(confirm("You have only "+takeLeave+". Are you sure want to apply leave?")) {
    //   form.submit();
    // }
    // else {
    //   return false;
    // }
    }
	else{
    swal({
      title: "",
      text: "You have Only "+max+" PL and "+minPl+" casaul leave.",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-success",
      confirmButtonText: "Apply",
      closeOnConfirm: false
      },
      function(){
        swal("Leave Apply!", "Your leave apply has been sent.", "success");
        form.submit();
      });
    // if(confirm("You have Only "+max+" PL and "+minPl+" casaul leave. Are you sure want to apply leave?")) {
    //   form.submit();
    // }
    // else {
    //   return false;
    // }
}
}
else{
	if(av < takeLeave){
    swal({
      title: "",
      text: "You have Only "+av+" PL and "+minus+" casaul leave",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-success",
      confirmButtonText: "Apply",
      closeOnConfirm: false
      },
      function(){
        swal("Leave Apply!", "Your leave apply has been sent.", "success");
        form.submit();
      });
    // if(confirm("You have Only "+av+" PL and "+minus+" casaul leave. Are you sure want to apply leave?")) {
    //   form.submit();
    // }
    // else {
    //   return false;
    // }
  }
  else{
    swal({
      title: "",
      text: "You have a "+takeLeave+" PL for leave",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-success",
      confirmButtonText: "Apply",
      closeOnConfirm: false
      },
      function(){
        swal("Leave Apply!", "Your leave apply has been sent.", "success");
        form.submit();
      });
        }
      }
      }
    });
  });
</script>

<script>
// function checkMsg() {
//   var availableleave ="<?php echo $availableleave; ?>";
//   var takeLeave = $("#days").val();
//
// alert(availableleave);
//
// }

$(document).ready(function() {
    $("#from_date").datepicker({
        dateFormat: "dd-mm-yy",
        // minDate: 0,
        onSelect: function (date) {
            var date2 = $('#from_date').datepicker('getDate');
            date2.setDate(date2.getDate() + 0);
            $('#to_date').datepicker('setDate', date2);
            $('#to_date').datepicker('option', 'minDate', date2);
            calculate();
        }
    });
    $('#to_date').datepicker({
        dateFormat: "dd-mm-yy",
        onClose: function () {
            var dt1 = $('#from_date').datepicker('getDate');
            var dt2 = $('#to_date').datepicker('getDate');
            calculate();
            if (dt2 <= dt1) {
                var minDate = $('#to_date').datepicker('option', 'minDate');
                $('#to_date').datepicker('setDate', minDate);
            }
        }
    });


    $('.from_date').datepicker().bind("change", function () {
        var minValue = $(this).val();
        minValue = $.datepicker.parseDate("dd-mm-yy", minValue);
        $('.to_date').datepicker("option", "minDate", minValue);
        calculate();
    });
    $('.to_date').datepicker().bind("change", function () {
        var maxValue = $(this).val();
        maxValue = $.datepicker.parseDate("dd-mm-yy", maxValue);
        $('.from_date').datepicker("option", "maxDate", maxValue);
        calculate();
    });
 calculate();
  function calculate() {
    var d1 = $('.from_date').datepicker('getDate');
    var d2 = $('.to_date').datepicker('getDate');
    var oneDay = 24*60*60*1000;
    var diff = 0;
    if (d1 && d1) {

      diff = Math.round(Math.abs((d2.getTime() - d1.getTime())/(oneDay)));
      $("#days").val(diff+1);
    }
  }


});

function numOnly(selector){
  selector.value = selector.value.replace(/[^0-9]/g,'');
}



</script>

<style type="text/css">
  #ui-datepicker-div {
    background: white !important;
    padding: 10px !important;
    width: 300px !important;
    }
  .ui-icon-circle-triangle-e{
    float: right!important;
  }
  .ui-state-disabled{
    color: lightgray!important;
    background: lightgray!important;
  }
  .ui-state-default{
    color: #000!important;
  }
  /* #leave_type-error, #day_type-error{
      position: absolute;
      bottom: 0px;
      left: 20px;
  } */


</style>
@endsection
