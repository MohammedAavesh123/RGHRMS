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
                        <h3 class="page-title">Job Applicant</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('job_seeker.view') }}">Job Applicant</a></li>
                            <li class="breadcrumb-item active">Edit Job Applicant</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row">
                <div class="col-md-12">
                  @if(Session::has('success'))
                      <p class="alert {{ Session::get('alert-class', 'alert-success') }}">{{ Session::get('success') }}</p>
                  @endif
                  @if(Session::has('error'))
                      <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('error') }}</p>
                  @endif
                <form action="{{ route('job_seeker.storejobapplicants') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $job_applicantsData->id }}">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="firstname" value="{{old('firstname', $job_applicantsData->firstname)}}" placeholder="Enter First Name" autocomplete="off">
                                        @if ($errors->has('firstname'))
                                            <span class="text-danger">{{ $errors->first('firstname') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="lastname" value="{{old('lastname', $job_applicantsData->lastname)}}" placeholder="Enter Last Name" autocomplete="off">
                                        @if ($errors->has('lastname'))
                                            <span class="text-danger">{{ $errors->first('lastname') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Email <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="email" value="{{old('email', $job_applicantsData->email)}}" placeholder="Enter Email" autocomplete="off">
                                        @if ($errors->has('email'))
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Mobile <span class="text-danger">*</span></label>
                                        <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="form-control" name="mobile" value="{{old('mobile', $job_applicantsData->mobile)}}" placeholder="Enter Mobile" autocomplete="off">
                                        @if ($errors->has('mobile'))
                                            <span class="text-danger">{{ $errors->first('mobile') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Experience <span class="text-danger">*</span></label>                                        
                                        <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="form-control" name="experience" value="{{old('experience', $job_applicantsData->experience)}}" placeholder="Enter Experience " autocomplete="off">
                                        @if ($errors->has('experience'))
                                            <span class="text-danger">{{ $errors->first('experience') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Jobs <span class="text-danger">*</span></label>
                                        <select class="form-control" name="job_id" id="job_id">
                                            <option value="">Select Jobs</option>
                                            @foreach ($jobs as $job)
                                                <option class="" <?php if($job_applicantsData->job_id == $job->id){ echo "selected"; } ?> value="{{ $job->id }}">{{ $job->job_title }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('job_id'))
                                            <span class="text-danger">{{ $errors->first('job_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Current CTC <span class="text-danger">*</span></label>
                                        <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="form-control" name="current_ctc" value="{{old('current_ctc', $job_applicantsData->current_ctc)}}" placeholder="Enter Current CTC " autocomplete="off">
                                        @if ($errors->has('current_ctc'))
                                            <span class="text-danger">{{ $errors->first('current_ctc') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Expected CTC <span class="text-danger">*</span></label>
                                        <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="form-control" name="expectation" value="{{old('expectation', $job_applicantsData->expectation)}}" placeholder="Enter Expected CTC" autocomplete="off">
                                        @if ($errors->has('expectation'))
                                            <span class="text-danger">{{ $errors->first('expectation') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Location <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="location" value="{{old('location', $job_applicantsData->location)}}" placeholder="Enter Location " autocomplete="off">
                                        @if ($errors->has('location'))
                                            <span class="text-danger">{{ $errors->first('location') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Source <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="source" value="{{old('source', $job_applicantsData->source)}}" placeholder="Enter Source " autocomplete="off">
                                        @if ($errors->has('source'))
                                            <span class="text-danger">{{ $errors->first('source') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status" id=status class="form-control">
                                            <option <?php if($job_applicantsData->status == ""){ echo "selected"; } ?> value="">Select Status</option>
                                            <option <?php if($job_applicantsData->status == "pending"){ echo "selected"; } ?> value="pending">Pending</option>
                                            <option <?php if($job_applicantsData->status == "on_hold"){ echo "selected"; } ?> value="on_hold">On Hold</option>
                                            <option <?php if($job_applicantsData->status == "selected"){ echo "selected"; } ?> value="selected">Selected</option>
                                            <option <?php if($job_applicantsData->status == "confirm"){ echo "selected"; } ?> value="confirm">Confirm</option>
                                            <option <?php if($job_applicantsData->status == "rejected"){ echo "selected"; } ?> value="rejected">Rejected</option>
                                        </select>
                                        @if ($errors->has('status'))
                                            <span class="text-danger">{{ $errors->first('status') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Resume</label>
                                        <a title="Resume Download" download href="{{ isset($job_applicantsData->resume) ? URL::to('public/uploads/resume/'.$job_applicantsData->resume) : '#'}}" class="btn btn-sm btn-outline-primary pull-right" target="blank"><i class="fa fa-download"></i></a>
                                        <a title="Open Resume In New Tab" href="{{ isset($job_applicantsData->resume) ? URL::to('public/uploads/resume/'.$job_applicantsData->resume) : '#'}}" class="btn btn-sm btn-outline-primary pull-right" target="blank"><i class="fa fa-eye"></i></a>

                                        <input type="file" class="form-control" name="resume" value="{{old('resume', $job_applicantsData->resume)}}" placeholder="Select Resume " autocomplete="off" accept=".doc,.pdf">
                                        <input name="oldresume" id="oldresume" type="hidden" value="{{ $job_applicantsData->resume }}">

                                        @if ($errors->has('resume'))
                                            <span class="text-danger">{{ $errors->first('resume') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="col-form-label">About Job Applicant</label>
                                        <textarea rows="4" type="file" class=" form-control{{($errors->first('about_job_seeker') ? " form-error" : "")}}" name="about_job_seeker" placeholder="Enter About Job Applicant" autocomplete="off">{{ $job_applicantsData->about_job_seeker }}</textarea>
                                        @if ($errors->has('about_job_seeker'))
                                            <span class="text-danger">{{ $errors->first('about_job_seeker') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <a href="{{ route('job_seeker.list') }}" type="button" class="btn btn-danger">Cancel</a>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    <!-- /Page Content -->

</div>

<script>
$(document).ready(function() {
    // Add Time function call
    test();
    // Add Time function call
    (function(){
       var td = $('#department').val();
       $("#designation").html('');
    $.ajax({
    url:"{{url('get-positions')}}",
    type: "POST",
    data: {
    id: td,
    _token: '{{csrf_token()}}'
    },
    dataType : 'json',
    success: function(result){
    $('#designation').html('<option value="">Select Designation</option>');
        $.each(result.positions,function(key,value){
        $("#designation").append('<option value="'+value.id+'">'+value.position+'</option>');
        });
        }
    });
})();

function test() {
    var depart_id = $(".department").val();
       $("#designation").html('');
       $.ajax({
        url:"{{url('get-positions')}}",
        type: "POST",
        data: {
        id: depart_id,
        _token: '{{csrf_token()}}'
        },
        dataType : 'json',
        success: function(result){
            $('#designation').html('<option value="">Select Designation</option>');
            $.each(result.positions,function(key,value){
            $("#designation").append('<option value="'+value.id+'">'+value.position+'</option>');
            });
            }
            });
        }
});
</script>
@endsection
