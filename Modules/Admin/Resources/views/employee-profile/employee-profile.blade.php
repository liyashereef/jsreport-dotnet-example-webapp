@extends('adminlte::page')
@section('title', config('app.name', 'Laravel').'-Training Employee Profile')
@section('content_header')

<h1>Employee Profile</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Training Employee Profile">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="employee-profile-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Profile Name</th>
            <th>Created Date</th>
            <th>Last Modified Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'employee_profile_form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group" id="profile_name">
                    <label for="profile_name" class="col-sm-3 control-label">Enter Profile Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('profile_name',null,array('class' => 'form-control', 'Placeholder'=>'Profile Name', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="role_id">
                    <label for="role_id" class="col-sm-3 control-label">Select Role Name<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                     {{Form::select('role_id',[''=>'Please Select']+$roles_list,null, ['class' => 'form-control'])}}
                     <small class="help-block"></small>
                 </div>
             </div>

             <div  class="form-group" id="mandatory_course.0">
                <label for="mandatory_course" class="col-sm-3 control-label">Select Mandatory Courses<span class="mandatory">*</span></label>
                <div class="col-sm-9 addNew" >
                   {{Form::select('mandatory_course[]',[''=>'Please Select']+$courses_list,null, ['class' => 'form-control'])}}
                   <small class="help-block"></small>
                   <input type="button" class="button btn btn-primary blue add-mandatory-button" value="Add Other Mandatory Courses" />
                   <input type="button" class="button btn btn-primary blue remove-mandatory-button" value="Remove Mandatory Courses" />
               </div>
           </div>
           <div  class="add-mandatory_course">
           </div>

           <div class="form-group" id="recommended_course.0">
            <label for="recommended_course" class="col-sm-3 control-label">Recommended Courses<span class="mandatory">*</span></label>
            <div class="col-sm-9 addNew" >
               {{Form::select('recommended_course[]',[''=>'Please Select']+$courses_list,null, ['class' => 'form-control'])}}
               <small class="help-block"></small>
               <input type="button" class="button btn btn-primary blue add-recommended-button" value="Add Recommended Training" />
               <input type="button" class="button btn btn-primary blue remove-recommended-button" value="Remove Recommended Courses" />
           </div>
       </div>
       <div  class="add-recommended_course">
       </div>

       <div class="modal-footer">
        {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
        {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
    </div>
    {{ Form::close() }}
</div>
</div>
</div>
</div>
@stop @section('js')
<script>
    $(function () {
      $.fn.dataTable.ext.errMode = 'throw';
      try{
        var table = $('#employee-profile-table').DataTable({
           dom: 'lfrtBip',
           bprocessing: false,
           buttons: [
           {
            extend: 'pdfHtml5',
            text: ' ',
            className: 'btn btn-primary fa fa-file-pdf-o',
            exportOptions: {
                columns: [ 0,1, 2, 3]
            }
        },
        {
            extend: 'excelHtml5',
            text: ' ',
            className: 'btn btn-primary fa fa-file-excel-o',
            exportOptions: {
                columns: [0,1, 2, 3]
            }
        },
        {
            extend: 'print',
            text: ' ',
            className: 'btn btn-primary fa fa-print',
            exportOptions: {
                columns: [ 0,1, 2, 3]
            }
        },
        {
            text: ' ',
            className: 'btn btn-primary fa fa-envelope-o',
            action: function (e, dt, node, conf) {
                emailContent(table, 'Course Category');
            }
        }
        ],
        processing: false,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('employee-profile.list') }}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        order: [
        [1, "asc"]
        ],
        lengthMenu: [
        [10, 25, 50, 100, 500, -1],
        [10, 25, 50, 100, 500, "All"]
        ],
        columns: [{
            data: 'DT_RowIndex',
            name: '',
            sortable:false
        },
        {
            data: 'profile_name',
            name: 'profile_name'
        },
        {
            data: 'created_at',
            name: 'created_at'
        },
        {
            data: 'updated_at',
            name: 'updated_at'
        },
        {
            data: null,
            sortable: false,
            render: function (o) {
                var actions = '';
                actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                @can('lookup-remove-entries')
                actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                @endcan
                return actions;
            },
        }
        ]
    });
    } catch(e){
        console.log(e.stack);
    }
    /*Hide remove button on start*/
    $('.remove-mandatory-button').hide();
    $('.remove-recommended-button').hide();


    /* Course Category Save - Start */
    $('#employee_profile_form').submit(function (e) {
        e.preventDefault();
        if($('#employee_profile_form input[name="id"]').val()){
          var message = 'Employee profile has been updated successfully';
        }else{
          var message = 'Employee profile has been created successfully';
        }
        formSubmit($('#employee_profile_form'), "{{ route('employee-profile.store') }}", table, e, message);
    });
    /* Course Category Save- End */

    /* Course Category Edit - Start */
    $("#employee-profile-table").on("click", ".edit", function (e) {
        var id = $(this).data('id');
        var url = '{{ route("employee-profile.single",":id") }}';
        var url = url.replace(':id', id);
        $('#employee_profile_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                if (data) {
                   $(".remove-mandatory-button, .remove-recommended-button").hide();
                   $('#myModal input[name="id"]').val(data.id)
                   $('#myModal input[name="profile_name"]').val(data.profile_name)
                   $('#myModal select[name="role_id"] option[value="'+data.role_id+'"]').prop('selected',true);
                   var count_recommended=0
                   var count_mandatory=0
                   var mandatory_array = [];
                   var recommended_array = [];
                   $.each(data, function(key,value) {
                    if(value.course_type=='Recommended'){
                        recommended_array[key]=value.course_id
                        count_recommended++;
                    }
                    else if(value.course_type=='Mandatory')
                    {
                     mandatory_array[key]=value.course_id
                     count_mandatory++;
                 }
             });
                   var recommended_array_value = jQuery.grep(recommended_array, function(n){ return (n); });
                   var mandatory_array_value = jQuery.grep(mandatory_array, function(n){ return (n); });
                   $('#myModal select[name="mandatory_course[]"] option[value="'+mandatory_array_value[0]+'"]').prop('selected',true);
                   $('#myModal select[name="recommended_course[]"] option[value="'+recommended_array_value[0]+'"]').prop('selected',true);
                   var mandatory= addMandatory(count_mandatory,mandatory_array_value);
                   var recommended= addRecommended(count_recommended,recommended_array_value);
                   $("#myModal").modal();
                   $('#myModal .modal-title').text("Edit Employee Profile: "+ data.profile_name)
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


    /* Course Category Edit - End */


    /* Add New fields for selecting mandatory courses - Start */
    $(".add-mandatory-button").on("click", function() {
        if ( $('.add-mandatory_course>div').length >= 0 ) {
            $('.remove-mandatory-button').show();
            var len=$('.add-mandatory_course>div').length+1
        }
        addHtmlMandatoryCourse(len);
    });
    /* Add New fields for selecting mandatory courses - End */

    /* Add New fields for selecting Recommended courses - Start */
    $(".add-recommended-button").on("click", function() {
       if ( $('.add-recommended_course>div').length >= 0 ) {
           $('.remove-recommended-button').show();
           var len=$('.add-recommended_course>div').length+1
       }
       addHtmlRecommendedCourse(len);
   });
    /* Add New fields for selecting Recommended courses - End */

    /* Remove fields for mandatory courses - Start */
    $(".remove-mandatory-button").on("click", function() {
       if ( $('.add-mandatory_course>div').length == 1 ) {
        $('.remove-mandatory-button').hide();
    }
    $('.add-mandatory_course>div').last().remove()
});
    /* Remove fields for mandatory courses - End */


    /* Remove fields for mandatory courses - Start */
    $(".remove-recommended-button").on("click", function() {
     if ( $('.add-recommended_course>div').length == 1 ) {
        $('.remove-recommended-button').hide();
    }
    $('.add-recommended_course>div').last().remove()
});
    /* Remove fields for mandatory courses - End */

    /* Course Category Delete - Start */
    $('#employee-profile-table').on('click', '.delete', function (e) {
        var id = $(this).data('id');
        var base_url = "{{ route('employee-profile.destroy',':id') }}";
        var url = base_url.replace(':id', id);
        var message = 'Employee profile has been deleted successfully';
        deleteRecord(url, table, message);
    });
    /* Course Category Delete- End */

    $('.add-new').click(function(){
        $(".course_mandatory, .course_recommended").remove();
        $(".remove-mandatory-button, .remove-recommended-button").hide();
    });
});

/* Mandatory course selectbox HTML Append - Start */
function addHtmlMandatoryCourse(i)
{
   var html_append='<div class="form-group course_mandatory" id="mandatory_course.'+i+'"><label for="mandatory_course" class="col-sm-3 control-label"></label><div class="col-sm-9">{{Form::select('mandatory_course[]',[''=>'Please Select']+$courses_list,null, ['class' => 'form-control'])}}</div><small class="help-block"></small></div>';
   var result=$(html_append).appendTo('.add-mandatory_course');
   console.log(result.length)
   return result;
}
/* Mandatory course selectbox HTML Append - End */

/* Recommended course selectbox HTML Append - Start */
function addHtmlRecommendedCourse(i)
{
    html_append='<div class=" form-group course_recommended"  id="recommended_course.'+i+'"><label for="recommended_course" class="col-sm-3 control-label"></label><div class="col-sm-9">{{Form::select('recommended_course[]',[''=>'Please Select']+$courses_list,null, ['class' => 'form-control'])}}</div><small class="help-block"></small></div>';
    var result=$(html_append).appendTo('.add-recommended_course');
    return result;
}
/* Recommended course selectbox HTML Append - End */


function addMandatory(count,data)
{
    if(data.length>1)
    {
        $('.remove-mandatory-button').show();

    }
    $('.course_mandatory').remove();
    for(i=1;i<count;i++)
    {
      var result=addHtmlMandatoryCourse(i);
      $(result).find('select[name="mandatory_course[]"] option[value="'+data[i]+'"]').prop('selected',true);

  }
  return true;

}
function addRecommended(count,data)
{
    if(data.length>1)
    {

       $('.remove-recommended-button').show();
   }
   $('.course_recommended').remove();
   for(i=1;i<count;i++)
   {
      var result=addHtmlRecommendedCourse(i);
      $(result).find('select[name="recommended_course[]"] option[value="'+data[i]+'"]').prop('selected',true);
  }
  return true;
}

</script>
@stop
