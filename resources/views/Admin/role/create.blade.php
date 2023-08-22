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
                        <h3 class="page-title">Role Add</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Add Role</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
            <div class="row">
                <div class="col-md-12">
                <form action="{{ route('role.store') }}" method="POST">
                            @csrf
                            <!-- <input type="hidden" name="rec_id" value="{{ Session::get('rec_id') }}"> -->
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Role name</label>
                                        <input type="text" class="form-control" name="role_type" value="{{old('role_type')}}" placeholder="Enter Role name"> 
                                        @if ($errors->has('role_type'))
                                            <span class="text-danger">{{ $errors->first('role_type') }}</span>
                                        @endif 
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Status</label>
                                        <select class="select" id="status" name="status">
                                            <option value=""> -- select status --</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        @if ($errors->has('status'))
                                            <span class="text-danger">{{ $errors->first('status') }}</span>
                                        @endif 
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                            </div>
                        </form> 
                </div>
            </div>
        </div>
    <!-- /Page Content -->

</div>
@endsection