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
                            <li class="breadcrumb-item active">Add Job Appplicant</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row">
                <div class="col-md-12">
                <form action="{{ route('job_seeker.storejobapplicants') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="added_by" value="{{ Auth::id() }}">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control{{($errors->first('firstname') ? " form-error" : "")}}" name="firstname" value="{{old('firstname')}}" placeholder="Enter First Name" autocomplete="off">
                                        @if ($errors->has('firstname'))
                                            <span class="text-danger">{{ $errors->first('firstname') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class=" form-control{{($errors->first('lastname') ? " form-error" : "")}}" name="lastname" value="{{ old('lastname') }}" placeholder="Enter Last Name" autocomplete="off">
                                        @if ($errors->has('lastname'))
                                            <span class="text-danger">{{ $errors->first('lastname') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Email <span class="text-danger">*</span></label>
                                        <input type="text" class=" form-control{{($errors->first('email') ? " form-error" : "")}}" name="email" value="{{ old('email') }}" placeholder="Enter Email" autocomplete="off">
                                        @if ($errors->has('email'))
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Mobile <span class="text-danger">*</span></label>
                                        <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" min="0" minlength="4" maxlength="18" class=" form-control{{($errors->first('mobile') ? " form-error" : "")}}" name="mobile" value="{{ old('mobile') }}" placeholder="Enter Mobile" autocomplete="off">
                                        @if ($errors->has('mobile'))
                                            <span class="text-danger">{{ $errors->first('mobile') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">

                                        <label class="col-form-label">Experience <span class="text-danger">*</span></label>
                                        <select class="form-control" name="experience" id="experience">
                                          <option value="">Select Experience</option>
                                            @foreach ($experience as $exp)
                                                <option {{ (old('experience') == $exp->id) ? 'selected': ''; }} value="{{ $exp->expFromYear }} - {{ $exp->expToYear }}"> {{ $exp->expFromYear }} - {{ $exp->expToYear }}yrs </option>
                                            @endforeach
                                        </select>
                                          @if ($errors->has('experience'))
                                            <span class="text-danger">{{ $errors->first('experience') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Jobs <span class="text-danger">*</span></label>
                                        <select class="form-control" name="job_id" id="job_id">
                                          <option value="">Select Job</option>
                                            @foreach ($jobs as $job)
                                                <option {{ (old('job_id') == $job->id) ? 'selected': ''; }} value="{{ $job->id }}">{{ $job->job_title }}</option>
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
                                        <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" min="0" class=" form-control{{($errors->first('current_ctc') ? " form-error" : "")}}" name="current_ctc" value="{{ old('current_ctc') }}" placeholder="Enter Experience" autocomplete="off">
                                        @if ($errors->has('current_ctc'))
                                            <span class="text-danger">{{ $errors->first('current_ctc') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Expected CTC <span class="text-danger">*</span></label>
                                        <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" min="0" class=" form-control{{($errors->first('expectation') ? " form-error" : "")}}" name="expectation" value="{{ old('expectation') }}" placeholder="Enter Expected CTC" autocomplete="off">
                                        @if ($errors->has('expectation'))
                                            <span class="text-danger">{{ $errors->first('expectation') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Location <span class="text-danger">*</span></label>
                                        <input type="text" class=" form-control{{($errors->first('location') ? " form-error" : "")}}" name="location" value="{{ old('location') }}" placeholder="Enter Location" autocomplete="off">
                                        @if ($errors->has('location'))
                                            <span class="text-danger">{{ $errors->first('location') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option {{ (old('status') == "pending") ? 'selected': ''; }} value="pending">Pending</option>
                                            <option {{ (old('status') == "on_hold") ? 'selected': ''; }} value="on_hold">On Hold</option>
                                            <option {{ (old('status') == "selected") ? 'selected': ''; }} value="selected">Selected</option>
                                            <option {{ (old('status') == "confirm") ? 'selected': ''; }} value="confirm">Confirm</option>
                                            <option {{ (old('status') == "rejected") ? 'selected': ''; }} value="rejected">Rejected</option>
                                        </select>
                                        @if ($errors->has('status'))
                                            <span class="text-danger">{{ $errors->first('status') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Source <span class="text-danger">*</span></label>
                                        <input type="text" class=" form-control{{($errors->first('source') ? " form-error" : "")}}" name="source" value="{{ old('source') }}" placeholder="Enter Source" autocomplete="off">
                                        @if ($errors->has('source'))
                                            <span class="text-danger">{{ $errors->first('source') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Resume <span class="text-danger">*</span></label>
                                        <input type="file" class=" form-control{{($errors->first('resume') ? " form-error" : "")}}" name="resume" value="{{ old('resume') }}" placeholder="Enter Resume" autocomplete="off" accept=".doc,.pdf">
                                        @if ($errors->has('resume'))
                                            <span class="text-danger">{{ $errors->first('resume') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="col-form-label">About Job Applicant</label>
                                        <textarea cols="4" type="file" class=" form-control{{($errors->first('about_job_seeker') ? " form-error" : "")}}" name="about_job_seeker" placeholder="Enter About Job Applicant" autocomplete="off">{{ old('about_job_seeker') }}</textarea>
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

@endsection
