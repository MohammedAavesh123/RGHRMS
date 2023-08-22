<div class="official_details_from">
  <form action="{{ route('employee.official_detail') }}"  enctype="multipart/form-data" id="save_officialDetail_form" method="POST">
      @csrf
      <input type="hidden" name="emp_id" value="{{$Employee->id}}"/>
      <div class="row rowheight">
          <div class="col-md-3">
              <label>Shift :</label>
          </div>
          <div class="col-md-6">
            <select class="select form-control" id="shift" name="shift">
              <option value="">Select Shift</option>
              @foreach ($shift as $shift_val)
              <option value="{{$shift_val->id}}" <?php if(isset($EmployeOfficialDetail->shift) && ($EmployeOfficialDetail->shift ==$shift_val->id) ){  echo "selected";} else if(old("shift") == $shift_val->id ){ echo "selected"; } ?>>
                  {{$shift_val->shift}} ({{$shift_val->shift_from}} - {{$shift_val->shift_to}})</option>
              @endforeach
            </select>
            <span class="text-danger" id="shiftErr"></span>
          </div>
      </div>
      <div class="row rowheight">
          <div class="col-md-3">
              <label>Salary :</label>
          </div>
          <div class="col-md-6">
              <input type="text" autocomplete="off" name="salary" value="{{isset($EmployeOfficialDetail->salary)?$EmployeOfficialDetail->salary:''}}" id="salary" class="form-control"/>
              <span class="text-danger" id="salaryErr"></span>
          </div>
      </div>
      <div class="row rowheight">
      <div class="col-md-3">
          <label>PL :</label>
      </div>
      <div class="col-md-6">
          <input type="text" autocomplete="off" name="pl" value="{{isset($EmployeOfficialDetail->pl)?$EmployeOfficialDetail->pl:''}}" id="pl" class="form-control"/>
          <span class="text-danger" id="plErr"></span>
      </div>
  </div>
  <div class="row rowheight">
      <div class="col-md-3">
          <label>WFH :</label>
      </div>
      <div class="col-md-6">
          <input type="text" autocomplete="off" name="wfh" value="{{isset($EmployeOfficialDetail->wfh)?$EmployeOfficialDetail->wfh:''}}" id="wfh" class="form-control"/>
          <span class="text-danger" id="wfhErr"></span>
      </div>
  </div>
      <div class="row rowheight">
          <div class="col-md-3">
          </div>
          <div class="col-md-6">
              <span class="text-danger" id="blankErr"></span>
          </div>
      </div>

      <div class="row">
          <div class="col-md-9"></div>
          <div class="col-md-3">
              <button type="submit" name="save_official_detail" id="save_official_detail" class="btn btn-success" style="float: right;">Save</button>
          </div>
      </div>
  </form>
</div>
<hr>
<div class="official_detail">
    <div class="row rowheight">
        <div class="col-md-3">
            <label>Shift :</label>
        </div>
        <div class="col-md-4">
              @if(isset($EmployeOfficialDetail->shift) && !empty($EmployeOfficialDetail->shift))
                {{$EmployeOfficialDetail->shiftname}} ( {{$EmployeOfficialDetail->shift_from}} - {{$EmployeOfficialDetail->shift_to}} )
              @else
                <p>Not Updated</p>
              @endif
        </div>
    </div>
    <div class="row rowheight">
        <div class="col-md-3">
            <label>Salary :</label>
        </div>
        <div class="col-md-4">

             <p>{{isset($EmployeOfficialDetail->salary)? $EmployeOfficialDetail->salary.' Rs. / month' :'Not Updated'}}</p>
        </div>
    </div>

    <div class="row rowheight">
        <div class="col-md-3">
          <label>Profile Image : <br/>( File Size Max : 2MB)</label>
        </div>
        <div class="col-md-4">
          <form action="{{ route('employee.official_detail') }}"  enctype="multipart/form-data" id="Image_officialDetail_form" method="POST">
              @csrf
              <input type="hidden" name="emp_id" value="{{$Employee->id}}"/>
              <span class="text-danger" id="ProfileErr"></span>
          <div class="avatar-upload">
              <div class="avatar-edit">

                  <input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" />
                  <label for="imageUpload"></label>
              </div>
              <div class="avatar-preview">
                @if(empty($Employee->avatar))
                <div id="imagePreview" onclick="showimageProfile('imagePreview')" style="background-image: url('{{ URL::to('public/assets/img/user.jpg') }}');">
                </div>
                @else
                <img src="{{ URL::to('public/profile/'. $Employee->avatar) }}"  onclick="showimageProfile('imagePreview')" id="imagePreview"/>
                <!-- <div id="imagePreview" onclick="showimageProfile('imagePreview')" style="background-image: url('{{ URL::to('public/profile/'. $Employee->avatar) }}');">
                </div> -->
                @endif

              </div>
          </div>
        </form>
              <!-- <p>Not Updated</p> -->
        </div>
    </div>
</div>
<div id="myModalProfile" class="modal">
  <span class="closeP">&times;</span>
  <img class="modal-content" id="img01Profile">
  <div id="captionProfile"></div>
</div>
<style>

#imagePreview {
  cursor: pointer;
  transition: 0.3s;
}

#imagePreview :hover {opacity: 0.7;}

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: absolute; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image */
#captionProfile {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation */
.modal-content, #captionProfile {
  -webkit-animation-name: zoom;
  -webkit-animation-duration: 0.6s;
  animation-name: zoom;
  animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
  from {-webkit-transform:scale(0)}
  to {-webkit-transform:scale(1)}
}

@keyframes zoom {
  from {transform:scale(0)}
  to {transform:scale(1)}
}

/* The Close Button */
.closeP {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.closeP:hover,
.closeP:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content {
    width: 100%;
  }
}

.avatar-upload {
  position: relative;
  max-width: 205px;
}
.avatar-upload .avatar-edit {
    position: absolute;
    z-index: 1;
    top: -8px;
    left: 37%;
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
  top: 6px;
  left: 0;
  right: 0;
  text-align: center;
  margin: auto;
}
.avatar-upload .avatar-preview {
  width: 100px;
  height: 100px;
  position: relative;
  border-radius: 100%;
  border: 6px solid #f8f8f8;
  box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
}
.avatar-upload .avatar-preview > img {
  width: 100%;
  height: 100%;
  border-radius: 100%;
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
}

</style>
<script>
function showimageProfile(id)
{
	//to show image as popup for employee document start code
	// Get the modal
	var modal = document.getElementById("myModalProfile");

	// Get the image and insert it inside the modal - use its "alt" text as a caption
	var img = document.getElementById(id);
	var  modalImg = document.getElementById("img01Profile");
	var captionText = document.getElementById("captionProfile");
	img.onclick = function(){

		modal.style.display = "block";
		modalImg.src = this.src;
		captionText.innerHTML = this.alt;
	}

	// Get the <span> element that closes the modal
	var span = document.getElementsByClassName("closeP")[0];
	// When the user clicks on <span> (x), close the modal
	span.onclick = function() {
	modal.style.display = "none";
	}
}


function readURL(input) {
  if (input.files && input.files[0]) {
     var reader = new FileReader();
     reader.onload = function (e) {
         // $("#imagePreview").css(
         //     "background-image",
         //     "url(" + e.target.result + ")"
         // );
         $("#imagePreview").attr("src", e.target.result);
         $("#imagePreview").hide();
         $("#imagePreview").fadeIn(650);
     };
     reader.readAsDataURL(input.files[0]);
  }
}
$("#imageUpload").change(function () {
  var fd = new FormData($('#Image_officialDetail_form')[0]);
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
      setTimeout(function(){
            // Check file selected or not
            if(files.length > 0 ){
               fd.append('image',files[0]);
             }
            $.ajax({
              url: '{{ route('employee.official_detail') }}',
              method: 'POST',
              data: fd,
              processData: false,
              // dataType: 'json',
              contentType: false,
              beforeSend:function(){
              },
              success:function(data){
                console.log(data);
                if(data.error){
                  $("#msgsss").html(data.error);
                }
                else{
                  toastr.success("Profile updated successfully.");

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
      },500);
  }
});
$(document).ready(function(){

  $(document).on("click","#save_official_detail",function(){

    var salary = $("#salary").val();
    var shift = $("#shift").val();
    var shift = $("#pl").val();
  var shift = $("#wfh").val();

    if(salary=='' && shift=='')
    {
      $("#blankErr").text("At least one field is required.");
    }
    else {
        $("#blankErr").text("");
    }

  });

  $("#salary").on("keyup", function(){
      var valid = /^\d{0,8}(\.\d{0,2})?$/.test(this.value),
          val = this.value;
      $("#blankErr").text("");
      if(!valid){
          console.log("Invalid input!");
          $("#blankErr").text("Invalid input!");
          this.value = val.substring(0, val.length - 1);
      }
  });
  $("#pl").on("keyup", function(){
    var valid = /^\d{0,8}(\.\d{0,2})?$/.test(this.value),
        val = this.value;
    $("#blankErr").text("");
    if(!valid){
        console.log("Invalid input!");
        $("#blankErr").text("Invalid input!");
        this.value = val.substring(0, val.length - 1);
    }
});
$("#wfh").on("keyup", function(){
    var valid = /^\d{0,8}(\.\d{0,2})?$/.test(this.value),
        val = this.value;
    $("#blankErr").text("");
    if(!valid){
        console.log("Invalid input!");
        $("#blankErr").text("Invalid input!");
        this.value = val.substring(0, val.length - 1);
    }
});

  $(document).on('submit', '#save_officialDetail_form',function(e){
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
      },
      success:function(data){
        if(data.error){
          $("#msgsss").html(data.error);
        }
        else{
          toastr.success("Detail updated successfully.");
          setTimeout(function(){
            window.location.hash="Official_Details";
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

</script>
