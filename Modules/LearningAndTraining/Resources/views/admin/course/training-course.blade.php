
@extends('layouts.app')
<style>
    .delete {
        margin-left: 10px;
        margin-right: 10px;
    }
</style>
@section('content')

    <div class="table_title">
        <h4>Course List</h4> <br>
    </div>

<div class="add-new" data-title="Add New Training Course">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Course Title</th>
            <th>Due Date</th>
            <th>Reference Code</th>
            <th>Category</th>
            <th>Duration </th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            {{ Form::hidden('reference_code',null) }}
            <div class="modal-body">
                {{-- <div class="form-group" id="reference_code">
                    <label for="reference_code" class="col-sm-3 control-label">Reference Code</label>
                    <div class="col-sm-9">
                        {{ Form::text('reference_code',null,array('class' => 'form-control','required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div> --}}
                <div class="form-group" id="course_title">
                    <label for="course_title" class="col-sm-3 control-label">Course Title</label>
                    <div class="col-sm-9">
                        {{ Form::text('course_title',null,array('class' => 'form-control', 'Placeholder'=>'Course Title', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="course_description">
                    <label for="course_description" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('course_description',null,array('class' => 'form-control', 'Placeholder'=>'Description', 'rows' => 5, 'cols' => 40, 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="course_objectives">
                    <label for="course_objectives" class="col-sm-3 control-label">Course Objective</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('course_objectives',null,array('class' => 'form-control', 'Placeholder'=>'Course Objective', 'rows' => 5, 'cols' => 40,  'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="training_category_id">
                    <label for="training_category_id" class="col-sm-3 control-label">Category<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::select('training_category_id', [null=>'Please Select'] +  $categoryList, null, ['class' => 'form-control']) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="course_image">
                    <label for="course_image" class="col-sm-3 control-label">Course Image<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        <img src="" style="display: none;width: 50%;" id="course_image_src">
                        {{ Form::file('course_image') }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="course_duration">
                    <label for="course_duration" class="col-sm-6 control-label"> Course Duration (In Minutes)</label>
                    <div class="col-sm-9">
                        {{ Form::text('course_duration',null,array('class' => 'form-control', 'Placeholder'=>'Course Duration')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="course_due_date">
                    <label for="course_due_date" class="col-sm-3 control-label">Define Due Date </label>
{{--                        <span  class="control-label" >{{ Form::checkbox('due_date_check', 1) }}</span>--}}
{{--                    <div class="col-sm-9" id="course_due_date">--}}
                    <div class="col-sm-9" >
                        {{Form::text('course_due_date',null,array('class'=>'form-control datepicker','id'=>'datepicker','placeholder'=>"Due Date",'max'=>"2900-12-31",'readonly'=>"readonly"))}}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="col-md-4">
                        <div class="form-group d-flex justify-content-start align-items-center" id="status">
                            <label for="status" class="control-label mb-0 pr-2">Active</label>
                            <div class="">
                                {{ Form::checkbox('status', 1) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group d-flex justify-content-start align-items-center" id="add_to_course_library">
                            <label for="status" class="control-label mb-0 pr-2">Do not add to course library</label>
                            <div class="">
                                {{ Form::checkbox('add_to_course_library', 1) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                    </div>
                </div>





            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop @section('scripts')
<script>
    $(function () {
        $("select[name=training_category_id]").select2({
            dropdownParent: $("#myModal .modal-content"),
        });
          $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#table').DataTable({
               dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4,5,6]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2, 3,4,5,6]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4,5,6]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Course');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('admin.course.list') }}",
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
                    data: 'course_title',
                    name: 'course_title'
                },
                {
                    data: 'course_due_date',
                    name: 'course_due_date'
                },
                {
                    data: 'reference_code',
                    name: 'reference_code'
                },
                {
                    data: 'training_category.course_category',
                    name: 'training_category.course_category'
                },
                {
                    data: 'course_duration',
                    name: 'course_duration'
                },
                {
                    //data: 'employee.user.first_name',
                    data:null,render:function(o){
                        if(o.status == 1)
                        {
                            return 'Active'
                        }else{
                            return 'Inactive'
                        }
                        orderable: false
                    },
                    name:'status'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="edit fa fa-pencil" title="Edit" data-id=' + o.id + '></a>'
{{--                        @can('lookup-remove-entries')--}}
                        actions += '<a href="#" class="delete fa fa-trash-o" title="Delete"  data-id=' + o.id + '></a>';
{{--                        @endcan--}}

                        var url = '{{ route("learningandtraining.course-content-admin",'') }}';
                        var exam_url='{{ route("learningandtraining.exam-questions-settings",'') }}';
                        actions += '<a href="'+url+"/"+ o.id +'" class="view fa fa-eye" title="View Details" data-id=' + o.id + '></a>';
                       actions += '   <a href="'+exam_url+"/"+ o.id +'" class="view fa fa-question" data-id=' + o.id + ' title="Test"></a>';
                        return actions;
                    },
                }
            ]
        });
         } catch(e){
            console.log(e.stack);
        }

        /* Course Category Save - Start */
        var bar = $('.bar');
        var percent = $('.percent');
        $('#form').submit(function (e) {
            e.preventDefault();
            var $form = $('#form');
            var formData = new FormData($form[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    var percentVal = '0%';
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            bar.width(percentComplete + '%')
                            percent.html(percentComplete + '%');
                            if (percentComplete === 100) {
                                console.log('completed');
                            }
                        }
                    }, false);

                    return xhr;
                },
                url: '{{ route('admin.course.store') }}',
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        console.log(data)
                        if(data.data.created == false){
                            swal("Saved", "Course has been updated successfully \n Reference code is "+ data.data.reference_code, "success");
                        }else{
                            swal("Saved", "Course has been created successfully \n Reference code is "+ data.data.reference_code, "success");
                        }
                        $("#form")[0].reset();
                        bar.width('0%')
                        percent.html('0%');
                        $("#myModal").modal('hide');
                        if (table != null) {
                            table.ajax.reload();
                        }
                    } else {
                        console.log('else',data);
                    }
                },
                fail: function (response) {
                    console.log(response);
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                    bar.width('0%')
                    percent.html('0%');
                },
                contentType: false,
                processData: false,
            });
        });
        /* Course Category Save- End */

        /* Course Category Edit - Start */
        $("#table").on("click", ".edit", function (e) {
            $("#course_image_src").hide();
            var id = $(this).data('id');
            var url = '{{ route("admin.course.single",":id") }}';
            var url = url.replace(':id', id);
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="course_title"]').val(data.course_title)
                        $('#myModal input[name="reference_code"]').val(data.reference_code)
                        $('#myModal textarea[name="course_description"]').val(data.course_description)
                        $('#myModal textarea[name="course_objectives"]').val(data.course_objectives)
                        $('#myModal select[name="training_category_id"]').val(data.training_category_id)
                        $('#myModal input[name="course_duration"]').val(data.course_duration)

                        if(data.course_file != null){
                            $('#myModal #course_file_name').text(data.course_file)
                            $('#myModal #course_file_name').css('font-weight',500)
                        }
                        if(data.course_external_url != null){
                            $('#myModal input[name="course_external_url"]').val(data.course_external_url)
                        }
                        if(data.status){
                            $('#myModal input[name="status"]').prop( "checked", true );
                        }else{
                            $('#myModal input[name="status"]').prop( "checked", false );
                        }

                        if(data.add_to_course_library){
                            $('#myModal input[name="add_to_course_library"]').prop( "checked", true );
                        }else{
                            $('#myModal input[name="add_to_course_library"]').prop( "checked", false );
                        }

                        if(data.course_image != null){
                            $("#course_image_src").show();
                            $("#course_image_src").attr("src", "{{asset('LearningAndTraining/course_images')}}"+"/"+data.course_image);
                        }
                        // if(data.course_due_date != null){
                        //     $('#myModal input[name="course_due_date"]').val(data.course_due_date)
                        //     $('#myModal input[name="due_date_check"]').prop( "checked", true );
                        //     $("#course_due_date").show();
                        // } else {
                        //     $('#myModal input[name="due_date_check"]').prop( "checked", false );
                        //     $("#course_due_date").hide();
                        // }
                        if(data.course_due_date != null){
                           $('#myModal input[name="course_due_date"]').val(data.course_due_date);
                        }
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Course: "+ data.course_title)
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

         /* Course Category Delete - Start */
        $('#table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('admin.course.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action. Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function (data) {
                            if (data.success) {
                                swal("Deleted", "Course has been deleted successfully", "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else {
                                swal("Alert", "This course has one or more employee or site profile", "warning");
                            }
                        },
                        error: function (xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        },
                        contentType: false,
                        processData: false,
                    });
                });
        });
        /* Course Category Delete- End */

        /* Clear Uploaded File label - Start */
        $('.add-new').click(function(){
           $('#course_file_name').text('');
           $("#myModal").modal();
            var title = $(this).data('title');
            $("#myModal").modal();
            $('#myModal form').trigger('reset');
            $('#myModal').find('input[type=hidden]').val('');
            $('#myModal .modal-title').text(title);
            $('#myModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#myModal input[name="due_date_check"]').prop( "checked", false );
            //$("#course_due_date").hide();
            $("#course_image_src").hide();
        });
        /* Clear Uploaded File label - End */

        //  $('input[name="due_date_check"]').click(function(){
        //
        //     if($(this).prop("checked") == true){
        //         $("#course_due_date").show();
        //     }
        //     else if($(this).prop("checked") == false){
        //         $("#course_due_date").hide();
        //     }
        // });



    });


</script>
<script>
$(function () {
    $(".datepicker").datepicker();
    $(".datepicker").datepicker("setDate", new Date())
});
</script>
@stop


