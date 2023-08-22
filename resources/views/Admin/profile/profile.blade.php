@extends('layouts.master')
@section('content')


    <div class="page-wrapper">
        <!-- Page Content -->
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Profile</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ul>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            <!-- /Page Header -->
            <div class="card mb-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-view">
                                <div class="profile-img-wrap">
                                  @if(empty(Auth::user()->avatar))
                                  <div class="profile-img">
                                      <a href="#">
                                          <img src="public/assets/img/user.jpg">
                                      </a>
                                  </div>
                                  @else
                                  <div class="profile-img">
                                      <a href="#">
                                          <img src="{{ URL::to('public/profile/'. Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}">
                                      </a>
                                  </div>
                                  @endif

                                </div>
                                <div class="profile-basic">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="profile-info-left">
                                                <h3 class="user-name m-t-0 mb-0">{{Auth::user()->firstname}} {{Auth::user()->middlename}} {{Auth::user()->lastname}}</h3>
                                               <!-- <div class="staff-id">Employee ID : {{Auth::user()->rec_id}}</div>-->
                                                <div class="small doj text-muted">Date of Join : {{Auth::user()->date_of_joining}}</div>
                                                  <!-- <a href="{{route('forget.password.get')}}"><button type="button"  class="btn btn-primary mt-3 ml-2 ">Reset Password</button></a> -->
                                                <!--<div class="staff-msg"><a class="btn btn-custom" href="chat.html">Send Message</a></div>-->
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <ul class="personal-info">

                                            @if(!empty($user->mobile))
                                            <li>
                                                <div class="title">Phone:</div>
                                                <div class="">{{Auth::user()->mobile}}</div>
                                            </li>
                                            @else
                                            <li>
                                                <div class="title">Phone:</div>
                                                <div class="">N/A</div>
                                            </li>
                                            @endif
                                            @if(!empty($user->email))
                                            <li>
                                                    <div class="title">Email:</div>
                                                    <div class="">{{Auth::user()->email}}</div>
                                                </li>
                                            @else
                                            <li>
                                                    <div class="title">Personal Email:</div>
                                                    <div class="">{{Auth::user()->personal_email}}</div>
                                                </li>
                                            @endif
                                            @if(!empty($user->date_of_birth))
                                            <li>
                                                    <div class="title">Birthday:</div>
                                                    <div class="">{{Auth::user()->date_of_birth}}</div>
                                            </li>
                                            @else
                                            <li>
                                                    <div class="title">Birthday:</div>
                                                    <div class="">N/A</div>
                                            </li>
                                            @endif
                                            @if(!empty($user->gender))
                                            <li>
                                                    <div class="title">Gender:</div>
                                                    <div class="">
                                                        @if(Auth::user()->gender==1)
                                                            Male
                                                        @elseif(Auth::user()->gender == 2)
                                                            Female
                                                        @else
                                                            Others
                                                        @endif
                                                    </div>
                                            </li>
                                            @else
                                            <li>
                                                    <div class="title">Gender:</div>
                                                    <div class="text">N/A</div>
                                            </li>
                                            @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="pro-edit"><a data-target="#profile_info" data-toggle="modal" class="edit-icon" href="#"><i class="fa fa-pencil"></i></a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="card tab-box">
                <div class="row user-tabs">
                    <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                        <ul class="nav nav-tabs nav-tabs-bottom">
                            <li class="nav-item"><a href="#emp_profile" data-toggle="tab" class="nav-link active">Profile</a></li>
                        </ul>
                    </div>
                </div>
            </div> -->

            <div class="tab-content">
                <!-- Profile Info Tab -->
                <div id="emp_profile" class="pro-overview tab-pane fade show active">
                    <div class="row">
                        <div class="col-md-6 d-flex">
                            <div class="card profile-box flex-fill">
                                <div class="card-body">
                                    <h3 class="card-title">Personal Informations
                                      <!-- <a href="#" class="edit-icon" data-toggle="modal" data-target="#personal_info_modal"><i class="fa fa-pencil"></i></a> -->
                                    </h3>
                                    <ul class="personal-info">
                                        <li>
                                            <div class="title">Department </div>
                                            <div class="">{{ !empty($usersDeatils->department) ? $usersDeatils->department : '-'; }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Designation </div>
                                            <div class="">{{ !empty($usersDeatils->position) ? $usersDeatils->position : '-'; }}</div>
                                        </li>
                                        <li>
                                            <?php
                                                // dd($usersDeatils->shift_from);
                                                if(!empty($usersDeatils->shift_from)){
                                                      $shift_timing =  $usersDeatils->shift_from.'-'.$usersDeatils->shift_to;
                                                }

                                                  // dd($shift_timing);
                                                ?>
                                              <div class="title">Shift</div>
                                              <div class="">{{ !empty($usersDeatils->shift) ? $usersDeatils->shift.' ('.$shift_timing.')' : '-'; }}</div>
                                          </li>
                                        <li>
                                            <div class="title">Personal Email</div>
                                            <div class="">{{ !empty($usersDeatils->personal_email) ? $usersDeatils->personal_email : '-'; }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Alternative Mobile Number</div>
                                            <div class="">{{ !empty($usersDeatils->alt_mobile) ? $usersDeatils->alt_mobile : '-'; }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Nationality</div>
                                            <div class="">Indian</div>
                                        </li>
                                        <!-- <li>
                                            <div class="title">Religion</div>
                                            <div class="">Christian</div>
                                        </li>
                                        <li>
                                            <div class="title">Marital status</div>
                                            <div class="">Married</div>
                                        </li>
                                        <li>
                                            <div class="title">Employment of spouse</div>
                                            <div class="">No</div>
                                        </li>
                                        <li>
                                            <div class="title">No. of children</div>
                                            <div class="">2</div>
                                        </li> -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-md-6 d-flex">
                            <div class="card profile-box flex-fill">
                                <div class="card-body">
                                    <h3 class="card-title">Emergency Contact

                                      @if($role_type == 'Employee')
                                      <a href="#" class="edit-icon" data-toggle="modal" data-target="#emergency_contact_modal"><i class="fa fa-pencil"></i></a>
                                      @endif

                                    </h3>
                                     <h5 class="section-title">Primary</h5>

                                    <ul class="personal-info">
                                        <li>
                                            <div class="title">Name</div>
                                            <div class="text">{{ !empty($family_info->family_name) ? $family_info->family_name : '-'; }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Relationship</div>
                                            <div class="text">{{ !empty($family_info->family_relationship) ? $family_info->family_relationship : '-'; }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Phone </div>
                                            <div class="text">{{ !empty($family_info->family_phone) ? $family_info->family_phone : '-'; }}</div>
                                        </li>
                                    </ul>

                                </div>
                            </div>
                        </div> -->
                        <div class="col-md-6 d-flex">
                            <div class="card profile-box flex-fill">
                                <div class="card-body">
                                    <h3 class="card-title">Bank information</h3>
                                    <ul class="personal-info">
                                        <li>
                                            <div class="title">Bank name</div>
                                            <div class="">{{ !empty($bank_info->bank_name) ? $bank_info->bank_name : '-'; }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Account Holder Name.</div>
                                            <div class="">{{ !empty($bank_info->bank_account_holder	) ? $bank_info->bank_account_holder	 : '-'; }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Bank Accout No.</div>
                                            <div class="">{{ !empty($bank_info->bank_account_number	) ? $bank_info->bank_account_number	 : '-'; }}</div>
                                        </li>
                                        <li>
                                            <div class="title">IFSC Code</div>
                                            <div class="">{{ !empty($bank_info->ifsc_code	) ? $bank_info->ifsc_code	 : '-'; }}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- <div class="col-md-6 d-flex">
                            <div class="card profile-box flex-fill">
                                <div class="card-body">
                                    <h3 class="card-title">Family Informations


                                    </h3>
                                    <div class="table-responsive">
                                        <table class="table table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Relationship</th>
                                                    <th>Phone</th>
                                                  <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                              @foreach($family_info1 as $familyMamber)
                                                <tr>
                                                    <td>{{ !empty($familyMamber->family_name) ? $familyMamber->family_name : ''; }}</td>
                                                    <td>{{ !empty($familyMamber->family_relationship) ? $familyMamber->family_relationship : ''; }}</td>
                                                    <td>{{ !empty($familyMamber->family_phone) ? $familyMamber->family_phone : ''; }}</td>
                                                   <td class="text-right">
                                                        <div class="dropdown dropdown-action">
                                                            <a aria-expanded="false" data-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i class="material-icons">more_vert</i></a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a href="#" class="dropdown-item"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                                <a href="#" class="dropdown-item"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>

                </div>
                <!-- /Profile Info Tab -->

            </div>

        </div>
        <!-- /Page Content -->
         <!-- Profile Modal -->
         <div id="profile_info" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Profile Edit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form name="profileForm" id="profileForm" method="post" action="{{ route('profile.edit') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                  <div class="container">
                                  <div class="avatar-upload">
                                      <div class="avatar-edit">
                                          <input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                          <label for="imageUpload"></label>
                                      </div>
                                      <div class="avatar-preview">
                                        @if(empty(Auth::user()->avatar))
                                        <div id="imagePreview" style="background-image: url('public/assets/img/user.jpg');">
                                        </div>
                                        @else
                                        <div id="imagePreview" style="background-image: url('{{ URL::to('public/profile/'. Auth::user()->avatar) }}');">
                                        </div>
                                        @endif

                                      </div>
                                  </div>
                              </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-6">
                                            <span class="text-danger" id="ProfileErr"></span>
                                        </div>
                                        <div class="col-md-3"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>First name</label>
                                                <input type="text" class="form-control" id="firstname" name="firstname" value="{{ Auth::user()->firstname }}" placeholder="Enter your first name">
                                               <span class="text-danger" id="firstnameErr"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Middle name</label>
                                                <input type="text" class="form-control" id="middlename" name="middlename" value="{{ Auth::user()->middlename }}" placeholder="Enter your middle name">
                                               <span class="text-danger" id="middlenameErr"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Last name</label>
                                                <input type="text" class="form-control" id="lastname" name="lastname" value="{{ Auth::user()->lastname }}" placeholder="Enter your last name">
                                               <span class="text-danger" id="lastnameErr"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="text" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" readonly>
                                                <span class="text-danger" id="emailErr"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Mobile Number</label>
                                                    <input class="form-control" type="mobile" id="mobile" name="mobile" value="{{ Auth::user()->mobile }}" placeholder="Enter your mobile number" readonly>
                                                    <span class="text-danger" id="mobileErr"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="submit-section">
                                <button type="submit" onclick="saveForm()\" class="btn submit btn-success" id="submit">Submit</button>
                                <button id="cancel" onclick="cancelForm()" class="btn btn-danger Close"  type="button" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- /Profile Modal -->

         <!-- Profile Modal -->
         <div id="profile_info" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Sakil Profile Information</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="profile-img-wrap edit-img">
                                        <img class="inline-block" src="{{ URL::to('/assets/images/'. Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}">
                                        <div class="fileupload btn">
                                            <span class="btn-text">edit</span>
                                            <input class="upload" type="file" id="upload" name="upload">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Full Name</label>
                                                <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}">
                                                <input type="hidden" class="form-control" id="rec_id" name="rec_id" value="{{ Auth::user()->rec_id }}">
                                                <input type="hidden" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Birth Date</label>
                                                <div class="cal-icon">
                                                    <input class="form-control datetimepicker" type="text" id="birthDate" name="birthDate">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Gender</label>
                                                <select class="select form-control" id="gender" name="gender">
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" class="form-control" id="address" name="address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>State</label>
                                        <input type="text" class="form-control" id="state" name="state">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Country</label>
                                        <input type="text" class="form-control" id="" name="country">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pin Code</label>
                                        <input type="text" class="form-control" id="pin_code" name="pin_code">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" class="form-control" id="phoneNumber" name="phone_number">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Department <span class="text-danger">*</span></label>
                                        <select class="select" id="department" name="department">
                                            <option selected disabled>Select Department</option>
                                            <option value="Web Development">Web Development</option>
                                            <option value="IT Management">IT Management</option>
                                            <option value="Marketing">Marketing</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Designation <span class="text-danger">*</span></label>

                                        <select class="select" id="" name="designation">
                                            <option selected disabled>Select Designation</option>
                                            <option value="Web Designer">Web Designer</option>
                                            <option value="Web Developer">Web Developer</option>
                                            <option value="Android Developer">Android Developer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Reports To <span class="text-danger">*</span></label>
                                        <select class="select" id="" name="reports_to">
                                            <option selected disabled>-- select --</option>

                                        </select>
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
        </div>
        <!-- /Profile Modal -->


        <!-- Personal Info Modal -->
        <div id="personal_info_modal" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Personal Information</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Passport No</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Passport Expiry Date</label>
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tel</label>
                                        <input class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nationality <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Religion</label>
                                        <div class="cal-icon">
                                            <input class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Marital status <span class="text-danger">*</span></label>
                                        <select class="select form-control">
                                            <option>-</option>
                                            <option>Single</option>
                                            <option>Married</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Employment of spouse</label>
                                        <input class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No. of children </label>
                                        <input class="form-control" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button class="btn btn-primary submit-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Personal Info Modal -->

        <!-- Family Info Modal -->
        <div id="family_info_modal" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> Family Informations</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-scroll">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Family Member <a href="javascript:void(0);" class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Name <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="text">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Relationship <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="text">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Date of birth <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="text">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Phone <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="text">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Education Informations <a href="javascript:void(0);" class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Name <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="text">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Relationship <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="text">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Date of birth <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="text">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Phone <span class="text-danger">*</span></label>
                                                    <input class="form-control" type="text">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="add-more">
                                            <a href="javascript:void(0);"><i class="fa fa-plus-circle"></i> Add More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button class="btn btn-primary submit-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Family Info Modal -->

        <!-- Emergency Contact Modal -->
        <div id="emergency_contact_modal" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Emergency Contact</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                      <form action="{{ route('profile.emergencycontact') }}" method="POST" id="emergencycontact" enctype="multipart/form-data">
                          @csrf
                            <input type="hidden" name="emp_id" value="{{$id}}"/>
                            <input type="hidden" name="emergency_contact_id" value="{{ !empty($family_info->id) ? $family_info->id : ''; }}"/>
                            <div class="card">
                                <div class="card-body">
                                    <!-- <h3 class="card-title">Primary Contact</h3> -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Name <span class="text-danger">*</span></label>
                                                <input name="family_name" id="family_name" value="{{ !empty($family_info->family_name) ? $family_info->family_name : ''; }}" type="text" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Relationship <span class="text-danger">*</span></label>
                                                <input name="family_relationship" value="{{ !empty($family_info->family_relationship) ? $family_info->family_relationship : ''; }}" id="family_relationship" class="form-control" type="text" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Phone <span class="text-danger">*</span></label>
                                                <input name="family_phone" value="{{ !empty($family_info->family_phone) ? $family_info->family_phone : ''; }}" id="family_phone" onkeyup="numOnly(this)" required onblur="numOnly(this)" class="form-control" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title">Primary Contact</h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Name <span class="text-danger">*</span></label>
                                                <input name="family_name" id="family_name" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Relationship <span class="text-danger">*</span></label>
                                                <input name="family_relationship" id="family_relationship" class="form-control" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Phone <span class="text-danger">*</span></label>
                                                <input name="family_phone" id="family_phone" class="form-control" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Phone 2</label>
                                                <input name="family_phone2" id="family_phone2" class="form-control" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <div class="submit-section">
                                <button class="btn btn-primary submit-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Emergency Contact Modal -->

        <!-- Education Modal -->
        <div id="education_info" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> Education Informations</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-scroll">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Education Informations <a href="javascript:void(0);" class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-focus focused">
                                                    <input type="text" value="Oxford University" class="form-control floating">
                                                    <label class="focus-label">Institution</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus focused">
                                                    <input type="text" value="Computer Science" class="form-control floating">
                                                    <label class="focus-label">Subject</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus focused">
                                                    <div class="cal-icon">
                                                        <input type="text" value="01/06/2002" class="form-control floating datetimepicker">
                                                    </div>
                                                    <label class="focus-label">Starting Date</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus focused">
                                                    <div class="cal-icon">
                                                        <input type="text" value="31/05/2006" class="form-control floating datetimepicker">
                                                    </div>
                                                    <label class="focus-label">Complete Date</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus focused">
                                                    <input type="text" value="BE Computer Science" class="form-control floating">
                                                    <label class="focus-label">Degree</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus focused">
                                                    <input type="text" value="Grade A" class="form-control floating">
                                                    <label class="focus-label">Grade</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Education Informations <a href="javascript:void(0);" class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-focus focused">
                                                    <input type="text" value="Oxford University" class="form-control floating">
                                                    <label class="focus-label">Institution</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus focused">
                                                    <input type="text" value="Computer Science" class="form-control floating">
                                                    <label class="focus-label">Subject</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus focused">
                                                    <div class="cal-icon">
                                                        <input type="text" value="01/06/2002" class="form-control floating datetimepicker">
                                                    </div>
                                                    <label class="focus-label">Starting Date</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus focused">
                                                    <div class="cal-icon">
                                                        <input type="text" value="31/05/2006" class="form-control floating datetimepicker">
                                                    </div>
                                                    <label class="focus-label">Complete Date</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus focused">
                                                    <input type="text" value="BE Computer Science" class="form-control floating">
                                                    <label class="focus-label">Degree</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus focused">
                                                    <input type="text" value="Grade A" class="form-control floating">
                                                    <label class="focus-label">Grade</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="add-more">
                                            <a href="javascript:void(0);"><i class="fa fa-plus-circle"></i> Add More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button class="btn btn-primary submit-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Education Modal -->

        <!-- Experience Modal -->
        <div id="experience_info" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Experience Informations</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-scroll">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Experience Informations <a href="javascript:void(0);" class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-focus">
                                                    <input type="text" class="form-control floating" value="Digital Devlopment Inc">
                                                    <label class="focus-label">Company Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus">
                                                    <input type="text" class="form-control floating" value="United States">
                                                    <label class="focus-label">Location</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus">
                                                    <input type="text" class="form-control floating" value="Web Developer">
                                                    <label class="focus-label">Job Position</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus">
                                                    <div class="cal-icon">
                                                        <input type="text" class="form-control floating datetimepicker" value="01/07/2007">
                                                    </div>
                                                    <label class="focus-label">Period From</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus">
                                                    <div class="cal-icon">
                                                        <input type="text" class="form-control floating datetimepicker" value="08/06/2018">
                                                    </div>
                                                    <label class="focus-label">Period To</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Experience Informations <a href="javascript:void(0);" class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-focus">
                                                    <input type="text" class="form-control floating" value="Digital Devlopment Inc">
                                                    <label class="focus-label">Company Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus">
                                                    <input type="text" class="form-control floating" value="United States">
                                                    <label class="focus-label">Location</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus">
                                                    <input type="text" class="form-control floating" value="Web Developer">
                                                    <label class="focus-label">Job Position</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus">
                                                    <div class="cal-icon">
                                                        <input type="text" class="form-control floating datetimepicker" value="01/07/2007">
                                                    </div>
                                                    <label class="focus-label">Period From</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-focus">
                                                    <div class="cal-icon">
                                                        <input type="text" class="form-control floating datetimepicker" value="08/06/2018">
                                                    </div>
                                                    <label class="focus-label">Period To</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="add-more">
                                            <a href="javascript:void(0);"><i class="fa fa-plus-circle"></i> Add More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button class="btn btn-primary submit-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Experience Modal -->

        <!-- /Page Content -->
    </div>
 <script>
 function readURL(input) {
if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
        $("#imagePreview").css(
            "background-image",
            "url(" + e.target.result + ")"
        );
        $("#imagePreview").hide();
        $("#imagePreview").fadeIn(650);
    };
    reader.readAsDataURL(input.files[0]);
}
}
$("#imageUpload").change(function () {
  var fd = new FormData($('#profileForm')[0]);
  var files = $('#imageUpload')[0].files;
  var sizeKB = files[0].size;
  if(sizeKB > 2000000)
  {
    $("#ProfileErr").text("Please select file size under 2MB");
  }
  else
  {
      $("#ProfileErr").text("");
      readURL(this);
  }
  //readURL(this);
});


 </script>

    <script>
$(document).ready(function(){
  $(document).on('submit','#emergencycontact', function(e){

		e.preventDefault();
		var form = this;
		 $.ajax({
			url: $(form).attr('action'),
			method: $(form).attr('method'),
			data: new FormData(form),
			processData: false,
			// dataType: 'json',
			contentType: false,
			beforeSend:function(){
				$('#Previous_Company_Offer_LetterErr').addClass('d-none');
				$('#Previous_Company_Experience_LetterErr').addClass('d-none');
        $('#Adhaar_CardErr').addClass('d-none');
        $('#PAN_CardErr').addClass('d-none');
        $('#Salary_SlipErr').addClass('d-none');
			},
			success:function(data){
        console.log(data);
				if(data.error){
					$("#msgsss").html(data.error);
				}
				else{
					//$(form)[0].reset();
					toastr.success("Emergency Contact Updated Successfully");
					setTimeout(function(){
						location.reload();
					}, 500);
				}

			},
			error:function(data){
				var errors = data.responseJSON;
				if($.isEmptyObject(errors) == false){
					$.each(errors.errors, function (key, value){
						var ErrorID = '#' + key + 'Err';
						$(ErrorID).removeClass("d-none");
						$(ErrorID).text(value);
					});
				}
			}
		 });
	 });
});

    function numOnly(selector){
  selector.value = selector.value.replace(/[^0-9]/g,'');
}
     function cancelForm() {
        $('#profileForm')[0].reset();
        $('#firstnameErr').addClass('d-none');
        $('#middlenameErr').addClass('d-none');
        $('#lastnameErr').addClass('d-none');
        $('#emailErr').addClass('d-none');
        $('#birthDateErr').addClass('d-none');
        $('#personal_emailErr').addClass('d-none');
        $('#mobileErr').addClass('d-none');
       }

     $(function(){
         $('#profileForm').on('submit', function(e){
             e.preventDefault();
             var form = this;
             $.ajax({
                 url: $(form).attr('action'),
                 method: $(form).attr('method'),
                 data: new FormData(form),
                 processData: false,
                 dataType: 'json',
                 contentType: false,
                 beforeSend:function(){
                   $('#firstnameErr').addClass('d-none');
                   $('#middlenameErr').addClass('d-none');
                   $('#lastnameErr').addClass('d-none');
                   $('#emailErr').addClass('d-none');
                   $('#birthDateErr').addClass('d-none');
                   $('#personal_emailErr').addClass('d-none');
                   $('#mobileErr').addClass('d-none');
                 },
                 success:function(data){
                    $(form)[0].reset();
                      window.location.reload(); //Reload the page
                      Toaster.show("Profile edit successfully.");
                 },
                 error: function(data){
                    var errors = data.responseJSON;
                    if($.isEmptyObject(errors) == false){
                    $.each(errors.errors, function (key, value){
                        var ErrorID = '#' + key + 'Err';
                        $(ErrorID).removeClass("d-none");
                        $(ErrorID).text(value);

                    });
                 }
               }
             });
         });
     })

        </script>



        <style>

.avatar-upload {
  position: relative;
  max-width: 205px;
  margin: 20px auto;
}
.avatar-upload .avatar-edit {
  position: absolute;
  right: 45px;
  z-index: 1;
  top: 10px;
}
.avatar-upload .avatar-edit input {
  display: none;
}
.avatar-upload .avatar-edit input + label {
  display: inline-block;
  width: 34px;
  height: 34px;
  margin-bottom: 0;
  border-radius: 100%;
  background: #ffffff;
  border: 1px solid transparent;
  box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
  cursor: pointer;
  font-weight: normal;
  transition: all 0.2s ease-in-out;
}
.avatar-upload .avatar-edit input + label:hover {
  background: #f1f1f1;
  border-color: #d6d6d6;
}
.avatar-upload .avatar-edit input + label:after {
  content: "\f040";
  font-family: "FontAwesome";
  color: #757575;
  position: absolute;
  top: 10px;
  left: 0;
  right: 0;
  text-align: center;
  margin: auto;
}
.avatar-upload .avatar-preview {
  width: 150px;
  height: 150px;
  position: relative;
  border-radius: 100%;
  border: 6px solid #f8f8f8;
  box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
}
.avatar-upload .avatar-preview > div {
  width: 100%;
  height: 100%;
  border-radius: 100%;
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
}
 </style>

@endsection
