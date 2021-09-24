@extends('layouts.app')
@section('content')
<div class="table_title">
  <h4>Course Catalog </h4>
</div>
<nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="nav-mandatory-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-mandatory" aria-selected="true">Mandatory Courses</a>
    <a class="nav-item nav-link" id="nav-recommended-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-recommended" aria-selected="false">Recommended Courses</a>
    {{-- <a class="nav-item nav-link" id="nav-status-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-status" aria-selected="false">Course Status</a> --}}
  </div>
</nav>
{{-- <div class="tab-content" id="nav-tabContent">
  <div class="tab-pane fade show active" id="nav-mandatory" role="tabpanel" aria-labelledby="nav-mandatory-tab"></div>
  <div class="tab-pane fade" id="nav-recommended" role="tabpanel" aria-labelledby="nav-recommended-tab"></div>
  <div class="tab-pane fade" id="nav-status" role="tabpanel" aria-labelledby="nav-status-tab"></div>
</div> --}}

<div class="table-responsive">
  <table class="table table-bordered" id="course-table">
    <thead>
      <tr>
        <th class="sorting" width="10%">Course Id</th>
        <th class="sorting" width="10%">Course Name</th>
        <th class="sorting" width="20%">Course Description</th>
        <th class="sorting" width="10%">Course Category</th>
        <th class="sorting" width="10%">Profile Name</th>
        <th class="sorting" width="10%">Profile Type</th>
        <th class="sorting" width="5%">Register</th>


      </tr>
    </thead>
  </table>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      {{ Form::open(array('url'=>'#','id'=>'course-register-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
      {{ Form::hidden('employee_id', null) }}
      {{ Form::hidden('course_id', null) }}
      {{ Form::hidden('id', null) }}
      <div class="modal-body">
        <div class="form-group" id="employee_no">
          <label for="employee_no" class="col-sm-12 control-label">Employee Number</label>
          <div class="col-sm-12">
           {{ Form::text('employee_no',null,array('class' => 'form-control', 'Placeholder'=>'Employee Number')) }}
           <small class="help-block"></small>
         </div>
       </div>
       <div class="form-group" id="employee_name">
        <label for="employee_name" class="col-sm-12 control-label">Employee Name</label>
        <div class="col-sm-12">
          {{ Form::text('employee_name',null,array('class' => 'form-control', 'Placeholder'=>'Employee Name')) }}
          <small class="help-block"></small>
        </div>
      </div>
      <div class="form-group" id="reference_code">
        <label for="reference_code" class="col-sm-12 control-label">Course Id</label>
        <div class="col-sm-12">
          {{ Form::text('reference_code',null,array('class' => 'form-control', 'Placeholder'=>'Course Id')) }}
          <small class="help-block"></small>
        </div>
      </div>
      <div class="form-group" id="course_name">
        <label for="course_name" class="col-sm-12 control-label">Course Name</label>
        <div class="col-sm-12">
         {{ Form::text('course_name',null,array('class' => 'form-control', 'Placeholder'=>'Course Name')) }}
         <small class="help-block"></small>
       </div>
     </div>
     <div class="form-group" id="registration_date">
      <label for="registration_date" class="col-sm-12 control-label">Registration Date</label>
      <div class="col-sm-12">
        {{ Form::text('registration_date',null,array('class' => 'form-control', 'Placeholder'=>'Registration Date')) }}
        <small class="help-block"></small>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    {{ Form::submit('Save', array('class'=>'button btn submit','id'=>'mdl_save_change'))}}
    {{ Form::button('Cancel', array('class'=>'btn cancel','data-dismiss'=>"modal", 'aria-hidden'=>true))}}
  </div>
  {{ Form::close() }}
</div>
</div>
</div>
@stop @section('scripts')

<script>
  $(document).ready(function() {
    url="{{ route('learningCourse.list','Mandatory') }}"
    datatableLoad(url);
    $('.nav-tabs a').click(function (ev) {
      $('#course-table').dataTable().fnDestroy();
      id=$(this).attr('id')
      if(id=="nav-mandatory-tab") {
        url="{{ route('learningCourse.list','Mandatory') }}"
      }
      else
      {
        url="{{ route('learningCourse.list','Recommended') }}"

      }
      datatableLoad(url);
    });



    $('#course-register-form').submit(function (e) {
      e.preventDefault();
      var $form = $(this);
      url = "{{ route('registerCourse.store') }}";
      var formData = new FormData($('#course-register-form')[0]);
      console.log(formData)
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: 'POST',
        data: formData,
        success: function (data) {
          if (data.success) {
            swal({
              title: "Saved",
              text: "The record has been saved",
              type: "success"},
              function(){
                $("#myModal").modal('hide');
                location.reload(true);
              });
          } else {
            console.log(data);
            swal("Oops", "The record has not been saved", "warning");
          }
        },
        fail: function (response) {
          console.log(response);
          swal("Oops", "Something went wrong", "warning");
        },
        error: function (xhr, textStatus, thrownError) {
          associate_errors(xhr.responseJSON.errors, $form);
        },
        contentType: false,
        processData: false,
      });
    });

    $("#course-table").on("click", ".edit", function (e) {
      var id = $(this).data('id');
      var url = '{{ route("trainingCourse.single",":id") }}';
      var url = url.replace(':id', id);

      $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
          if (data) {
            $('#myModal input[name="id"]').val(data.data.id)
            $('#myModal input[name="course_id"]').val(data.data.course_id)
            $('#myModal input[name="employee_id"]').val(data.user.employee.user_id)
            $('#myModal input[name="reference_code"]').val(data.data.training_course.reference_code)
            $('#myModal input[name="course_name"]').val(data.data.training_course.course_title)

            var months    = ['January','February','March','April','May','June','July','August','September','October','November','December'];


            var dt = new Date();
            var thisMonth = months[dt.getMonth()];
            var date=dt.getDate();
            var year=dt.getFullYear();
            var today=thisMonth+" "+date+","+year

            $('#myModal input[name="registration_date"]').val(today)
            $('#myModal input[name="employee_name"]').val(data.user.first_name+" "+data.user.last_name)
            $('#myModal input[name="employee_no"]').val(data.user.employee.employee_no)
            $("#myModal").modal();
            $('#myModal .modal-title').text("Edit Course: "+ data.data.training_course.course_title)
          } else {
            alert(data);
          }
        },
        error: function (xhr, textStatus, thrownError) {
          alert(xhr.status);
          alert(thrownError);
        },
        contentType: false,
        processData: false,
      });
    });
  });
  function datatableLoad(url)
  {
    var table = $('#course-table').DataTable({
      fixedHeader: true,
      processing: false,
      serverSide: true,
      responsive: true,
      ajax: url,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      order: [
      [1, "desc"]
      ],
      lengthMenu: [
      [10, 25, 50, 100, 500, -1],
      [10, 25, 50, 100, 500, "All"]
      ],

      columns: [{
        data: 'reference_code',
        name: 'reference_code',

      },
      {
        data: 'course_name',
        name: 'course_name',
      },
      {
        data: 'course_description',
        name: 'course_description',
      },
      {
        data: 'course_category',
        name: 'course_category',
      },
      {
        data: 'profile_name',
        name: 'profile_name',
      },
      {
        data: 'profile_type',
        name: 'profile_type',
      },
      {
        data: null,
        sortable: false,
        render: function (o) {
          var actions = '';
          actions += '<a href="#" class="edit fa fa-podcast" data-id=' + o.id + '></a>'
          return actions;
        },
      }
      ]
    });
  }


</script>
<style type="text/css">
.nav-tabs .nav-link.active
{
  background: #007bff !important;
}
</style>
@stop
