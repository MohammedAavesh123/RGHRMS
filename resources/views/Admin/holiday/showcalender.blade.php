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
                        <h3 class="page-title">Holiday</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('role.view') }}">Holiday view</a></li>
                            <li class="breadcrumb-item active"> Holiday</li>
                        </ul>
                    </div>
                </div>
            </div>
            <h3 class="page-title mt-3"></h3>
            <div id="calendar"></div>
        </div>

        <div class="col py-3">
            <!-- <a  href="{{ route('holiday.list') }}" class="btn btn-danger"> Back</a> -->
        </div>
    <!-- /Page Content -->

</div>
<style>
 .fc-day-grid-event{
  text-align: left;
  padding: 3px;
  background: #2181bc;
}
</style>


<script>

$(document).ready(function () {

    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
        }
    });

    var calendar = $('#calendar').fullCalendar({
        editable:false,
        displayEventTime: false,
        header:{
            left:'prev,next today',
            center:'title',
            right:'month,agendaWeek,agendaDay'
        },
        events:"{{ url('/holiday') }}",
        selectable:true,
        selectHelper: true,
        select:function(start, end, allDay)
        {
          <?php if (Helpers::checkPermission($userid, $modelName ="Holiday" , "create")){ ?>
            var title = prompt('Event Title:');
            if(title)
            {
                var start =moment(start, 'Y-MM-DD HH:mm:ss').format('Y-MM-DD HH:mm:ss');
                var end =moment(end, 'Y-MM-DD HH:mm:ss').format('Y-MM-DD HH:mm:ss');

                $.ajax({
                    url:"{{ url('/holiday/action') }}",
                    type:"POST",
                    data:{
                        title: title,
                        start: start,
                        end: end,
                        type: 'add'
                    },
                    success:function(data)
                    {
                        calendar.fullCalendar('refetchEvents');
                        alert("Event Created Successfully");
                    }
                })
            }
          <?php } ?>
        },
        editable:true,
        eventResize: function(event, delta)
        {
          <?php if (Helpers::checkPermission($userid, $modelName ="Holiday" , "write")){ ?>
            var start =moment(event.start, 'Y-MM-DD HH:mm:ss').format('Y-MM-DD HH:mm:ss');
            var end =moment(event.end, 'Y-MM-DD HH:mm:ss').format('Y-MM-DD HH:mm:ss');
            var title = event.title;
            var id = event.id;
            $.ajax({
                url:"{{ url('/holiday/action') }}",
                type:"POST",
                data:{
                    title: title,
                    start: start,
                    end: end,
                    id: id,
                    type: 'update'
                },
                success:function(response)
                {
                    calendar.fullCalendar('refetchEvents');
                    alert("Event Updated Successfully");
                }
            })
          <?php } ?>
        },

        eventClick:function(event)
        {
          <?php if (Helpers::checkPermission($userid, $modelName ="Holiday" , "delete")){ ?>
            if(confirm("Are you sure you want to remove it?"))
            {
                var id = event.id;
                $.ajax({
                    url:"{{ url('/holiday/action') }}",
                    type:"POST",
                    data:{
                        id:id,
                        type:"delete"
                    },
                    success:function(response)
                    {
                        calendar.fullCalendar('refetchEvents');
                        alert("Event Deleted Successfully");
                    }
                })
            }
            <?php } ?>
        }
    });





});

</script>
@endsection
