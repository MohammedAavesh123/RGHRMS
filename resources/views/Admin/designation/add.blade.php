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
                        <h3 class="page-title">Designation Add</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Add Designation</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
            <div class="row">
                <div class="col-md-12">
                <form action="{{route('designation.store')}}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Choose a Department:</label>
                                        <select id="department" name="depart_id"  class="form-control">
                                            <option>Choose</option>
                                            @foreach($department as $depart)
                                                <option value="{{$depart->id}}">{{$depart->department}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Position Name</label>
                                        
                                        <input type="text" name="position"   class="form-control">
                                    </div>
                                </div>

                                    <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Status</label>
                                        <select class="select" id="status" name="status">
                                            <option>Select Status</option>
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
           
            
           <!-- <div class="row">
                <div class="col-md-12">

                    <div class="col-md-6">

                        <form method="POST" action="{{route('designation.store')}}">
                            @csrf
                        <label for="department">Choose a Department:</label>
                        <select id="department" name="depart_id">
                            <option>Choose</option>
                            @foreach($department as $depart)
                        <option value="{{$depart->id}}">{{$depart->department}}</option>
                         @endforeach
                        </select>
                        <br>
                        <label for="position">Position Name
                            <input type="text" name="position">
                        </label>
                        <br>
                        <button type="submit" class="btn btn-success">Save</button>
                        </form>
                    </div>
                    
                </div>
            </div>-->
        </div>
        <!-- /Page Content -->
        
    </div>
    <!-- /Page Wrapper -->

@endsection