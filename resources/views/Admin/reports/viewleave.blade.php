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
                        <h5 class="page-title">Leave Application</h5>
                    </div>
                    <div class="col-auto float-right ml-auto">
                      @if(Helpers::checkPermission($userid, $modelName ="Leaves" , "create"))
                          <a href="{{route('leaves.create')}}" class="btn btn-info"><i class="fa fa-plus"></i>Apply Leave</a>
                      @endif
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
                      <p class="m-0">: {{ $availablePl }}</p>
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
            <div class="card p-3">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Leave Type</th>
                    <th scope="col">Day Type</th>
                    <th scope="col">Days</th>
                    <th scope="col">Reason</th>
                    <th scope="col" class="text-right">Approval Status</th>
                  </tr>
                </thead>
                <tbody>

                  @foreach($leavelist as $key => $item)
                  <tr class="textCenter">
                    @if($item->appied_leave_days > 1)
                    <td>
                      <span class="tdDate">{{  date('d-M-Y,D', strtotime($item->from_date)) }}</span>
                      <br> To <br>
                      <span class="tdDate">{{  date('d-M-Y,D', strtotime($item->to_date)) }}</span>
                    </td>
                    @else
                    <td>
                      <span class="tdDate">{{  date('d-M-Y,D', strtotime($item->from_date)) }}</span>
                    </td>
                    @endif
                    <td>{{ ucfirst(str_replace('_', ' ', $item->leave_type)) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->day_type)) }}</td>
                    <td>{{$item->appied_leave_days}}</td>

                    <td>
                        @if (strlen($item->reason) > 20)
                              <span id="dots{{$item->id}}">{{Str::limit($item->reason, 20,'...')}}</span>
                              <!-- <span id="more{{$item->id}}" style="display:none;">{{ substr($item->reason, 20) }}</span> -->
                              <span id="more{{$item->id}}" style="display:none;">{{ $item->reason }}</span>
                              <span class="text-primary"  style="font-size: 14px;cursor: pointer;" onclick="readMore({{$item->id}})" id="myBtn{{$item->id}}">Read more</span>
                        @else
                        {{Str::limit($item->reason, 20)}}
                        @endif
                    </td>

                    @if($item->approval_status =='rejected')
                    <td class="text-right text-danger">
                    @elseif($item->approval_status =='approved' )
                    <td class="text-right text-success">
                    @else
                    <td class="text-right text-primary">
                    @endif

                    {{ ucfirst($item->approval_status) }}

                    @if(time() <= strtotime($item->from_date))

                    @if($item->approval_status != "cancel")
                            @if($item->is_cancelled == 1)
                              <spna class="text-danger"> Cancelled</span>
                            @else
                              <button type="button" name="cancel_leave" data-id="{{$item->id}}" class="btn btn-danger btn-sm cancel_leave" style="color: #fff;">Cancel </button>
                        @endif
                        @endif
                    @elseif($item->is_cancelled == 1)
                        <spna class="text-danger"> Cancelled</span>
                    @endif
                    </td>

                  </tr>
                    @endforeach
                </tbody>
              </table>
            </div>



            </div>
            <!-- /Page Content -->
      </div>


<style>
.tdDate{
  color: #529df4;
}
.table td{
  vertical-align: inherit !important;
}
.textCenter tr{
    min-height: 10em;
    display: table-cell;
    vertical-align: middle;
}
.text-primary{
  color: #6495ed!important;
}
</style>

<script src = "https://code.jquery.com/jquery-3.5.1.slim.min.js" ></script>
<script src= "https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.min.js" > </script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script type="text/javascript">

function readMore(id) {
 var dots = document.getElementById("dots"+id);
 var moreText = document.getElementById("more"+id);
 var btnText = document.getElementById("myBtn"+id);

 if (dots.style.display === "none") {
     dots.style.display = "inline";
     btnText.innerHTML = "Read more";
     moreText.style.display = "none";
 } else {
     dots.style.display = "none";
     btnText.innerHTML = "Read less";
     moreText.style.display = "inline";
 }
}

$(document).ready(function () {
    $(".cancel_leave").on('click',function(){
      var id = $(this).data('id');
      swal({
           title: "",
           text: "Enter your reason",
           content: { element: "textarea" },
           showCancelButton: true,
           closeOnConfirm: false,
           icon: "info",
           buttons: {
             cancel: "Cancel",
             confirm: "Send",
           },
           inputPlaceholder: "Write something"
         }).then(function(reason) {
            if (reason) {
              reason = document.querySelector(".swal-content__textarea").value;
              $.ajax({
                    type: "POST",
                    url: "{{route('leaves.cancel_leave')}}",
                    dataType: "json",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id":id,
                        "reason":reason
                      },
                    success: function (data) {
                          if(data.status == 1)
                          {
                            swal("Leave Cancelled!", "Your leave cancelled.", "success");
                          }
                          else
                          {
                            swal("Error", "There is some problem. Please try again.");
                          }
                          setTimeout(function(){
                            location.reload();
                          },1500);
                      }
                  });
              }
          });
      // swal({
      //   title: "",
      //   text: "Are you sure you want to cancel?",
      //   type: "warning",
      //   showCancelButton: true,
      //   confirmButtonClass: "btn-success",
      //   confirmButtonText: "Yes",
      //   cancelButtonText: "No",
      //   closeOnConfirm: false
      //   },
      //   function(){
      //     $.ajax({
      //       type: "POST",
      //       url: "{{route('leaves.cancel_leave')}}",
      //       dataType: "json",
      //       data: {
      //           "_token": "{{ csrf_token() }}",
      //           "id":id
      //         },
      //       success: function (data) {
      //             if(data.status == 1)
      //             {
      //               swal("Leave Cancelled!", "Your leave cancelled.", "success");
      //             }
      //             else
      //             {
      //               swal("Error", "There is some problem. Please try again.");
      //             }
      //             setTimeout(function(){
      //               location.reload();
      //             },1500);
      //         }
      //     });
      //
      //   });
    });
});
</script>
@endsection
