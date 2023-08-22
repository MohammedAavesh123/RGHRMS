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
                        <h3 class="page-title">Department View</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">View Department</li>
                        </ul>
                    </div>
                    <div class="col-auto float-right ml-auto">
                        <a href="{{route('department.add')}}" class="btn btn-info add-btn"><i class="fa fa-plus"></i> Add New Department</a>
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
                                    <th>Department Name</th>
                                    <!-- <th>Created By</th> -->
                                    <th>Created Date</th>
                                    <th>Updated Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $data)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{$data->department }}</td>
                                    <td> {{ date('Y-m-d H:i:s', strtotime($data->created_at))}}</td>
                                    <td>{{ date('Y-m-d H:i:s', strtotime($data->updated_at))}}</td>
                                    <td>
                                        @if($data->status == 1)
                                        <a class="btn btn-success" onclick="return confirm('Are you sure change status Inactive ?')" href="{{url('department/status/0')}}/{{$data->id}}">Active</a>
                                        @elseif($data->status == 0)
                                        <a class="btn btn-warning" onclick="return confirm('Are you sure change status Active ?')" href="{{url('department/status/1')}}/{{$data->id}}">Inactive</a>
                                        @endif
                                    </td>
                                     <td>
                                        <button class="btn btn-success open-modal edit" value="{{$data->id}}"  data-id="{{$data->id}}" data-name="{{$data->department}}" data-status="{{$data->status}}">Edit
                                        </button>
                                        <a class="btn btn-danger" onclick="return confirm('Are you sure delete this Item?')" href="{{route('department.delete', $data->id)}}">Delete</a>
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
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="modalFormData" name="modalFormData" class="form-horizontal" novalidate="">
                                <input type="hidden" id="id" name="id" value="{{$data->id}}">
                               <div class="form-group">
                                    <label for="inputLink" class="col-form-label">Department</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control"  name="department"
                                               placeholder="Enter Department"  id="department">
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary edit_modal" id="btn-save" value="add" >Update
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
        
        var id = $(this).data('id');
        var name = $(this).data('name');
        var status = $(this).data('status');
        
        $("#id").val(id);
        $("#department").val(name);
        $("#status").val(status);
    
        jQuery('#linkEditorModal').modal('show');

    });

     jQuery('.edit_modal').click(function () {
     
        var id = $("#id").val();
        var name_get = $("#department").val();
        var status_get = $("#status").val();

           $.ajax({
            type: "GET", 
            dataType: "json", 
            url: '{{route("department.update") }}',
            data: {'id':id,'name_get': name_get,'status_get':status_get}, 
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