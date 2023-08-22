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
                        <h3 class="page-title">Position View</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">View Position</li>
                        </ul>
                    </div>
                    <div class="col-auto float-right ml-auto">
                        <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_indicator"><i class="fa fa-plus"></i> Add New Designation</a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
            <div class="row">
                <div class="col-md-12">
                @if(Session::has('success'))
                    <p class="alert {{ Session::get('alert-class', 'alert-success') }}">{{ Session::get('success') }}</p>
                    @endif
                     <div class="table-responsive">
                        <table class="table table-striped custom-table mb-0 datatable">
                            <thead>
                                <tr>
                                    <th style="width: 30px;">S.no</th>
                                    <th>Position</th>
                                    <th>Department Name</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($position as $key => $data)

                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $data->position }}</td>
                                    <td>{{ $data->department }}</td>
                                    <td>
                                        <div class="dropdown action-label">
                                            <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-dot-circle-o text-success"></i> Active
                                            </a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-success"></i> Active</a>
                                                <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-danger"></i> Inactive</a>
                                            </div>
                                        </div>
                                    </td>
                                     <td>
                                        <button class="btn btn-info open-modal edit" value="{{$data->id}}" data-name="{{$data->position}}" data-depart_id="{{$data->depart_id}}" data-status="{{$data->status}}"data-position_id="{{$data->id}}">Edit
                                        </button>
                                        <a class="btn btn-danger" onclick="return confirm('Are you sure?')" href="{{route('designation.delete', $data->id)}}">Delete</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <!-- /Page Content -->
    </div>



    <div class="modal fade" id="linkEditorModal" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="linkEditorModalLabel">Edit</h4>
                        </div>
                        <div class="modal-body">
                            <form id="modalFormData" name="modalFormData" class="form-horizontal" novalidate="">
                             
                                <input type="hidden" id="position_id" name="position_id">
                               <div class="form-group">
                                        <label class="col-form-label">Choose a Department:</label>
                                        <select id="department" name="depart_id"  class="form-control">
                                            <option>Choose</option>
                                            @foreach($department as $depart)
                                                <option value="{{$depart->id}}">{{$depart->department}}</option>
                                            @endforeach
                                        </select>
                                    </div>


                               <div class="form-group">
                                    <label for="inputLink" class="col-form-label">Position</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control"  name="position"
                                               placeholder="Enter Position"  id="position_name">
                                    </div>
                                </div>


                                    <div class="form-group">
                                        <label class="col-form-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">Select Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                       
                                    </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary edit_modal" id="btn-save" value="add" >Save changes
                            </button>
                            <input type="hidden" id="link_id" name="link_id" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
 <script type="text/javascript">jQuery(document).ready(function($){
    ////----- Open the modal to CREATE a link -----////
    jQuery('.edit').click(function () {
        var name = $(this).data('name');
        var depart = $(this).data('depart_id');
        var status = $(this).data('status');
        var position_id = $(this).data('position_id')



        
        $("#position_name").val(name);
        $("#department").val(depart);
        $("#status").val(status);
        $("#position_id").val(position_id);



        jQuery('#linkEditorModal').modal('show');

    });

    
     jQuery('.edit_modal').click(function () {
     

        var name_get = $("#position_name").val();
        var depart_get = $("#department").val();
        var status_get = $("#status").val();
        
        var position_id = $("#position_id").val();
        
         
           $.ajax({
            type: "GET", 
            dataType: "json", 
            url: '{{ route("change.designation.status") }}',
            data: {'name_get': name_get, 'depart_get': depart_get,'status_get':status_get,'position_id':position_id}, 
            success: function(data){ 
              toastr.success(data.success);
              jQuery('#linkEditorModal').modal('hide');
              setTimeout(function(){
                    location.reload();
              }, 300);
            } 

        }); 

         });
     });
</script>      

@endsection