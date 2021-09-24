
@extends('layouts.app')
@section('content')

<div class="add-new" data-title="Add New Training Course">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Course Title</th>
            <th>Content Title</th>
            <th>Content Type</th>
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
            {{ Form::hidden('course_id', null) }}
            {{ Form::hidden('reference_code',null) }}
            <div class="modal-body">
                <div class="form-group" id="course_id">
                    <label for="course_id" class="col-sm-3 control-label">Course </label>
                    <div class="col-sm-9">

                        {{ Form::text('course_name',$course->course_title,array('class' => 'form-control', 'Placeholder'=>'Content Title', 'readonly'=>"readonly")) }}

                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="content_type_id">
                    <label for="content_type_id" class="col-sm-3 control-label">Content Type</label>
                    <div class="col-sm-9">
                        {{ Form::select('content_type_id', [null=>'Please Select'] +  $courseContentList, null, ['class' => 'form-control']) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="course_title">
                    <label for="course_title" class="col-sm-3 control-label">Content Title</label>
                    <div class="col-sm-9">
                        {{ Form::text('content_title',null,array('class' => 'form-control', 'Placeholder'=>'Content Title', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="course_file">
                    <label for="course_file" class="col-sm-3 control-label">Upload Course content</label>
                    <div class="col-sm-9">
                        {{ Form::file('course_file') }}
                        <label id="course_file_name"></label>
                         <style>
                            .progress {
                                position: relative;
                                width: 100%;
                                padding: 1px;
                                border-radius: 3px;
                                margin-top: 1%
                            }

                            .bar {
                                background-color: #F48452;
                                width: 0%;
                                height: 25px;
                                border-radius: 3px;
                            }

                            .percent {
                                position: absolute;
                                display: inline-block;
                                top: 1%;
                                left: 48%;
                                color: #003A63;
                            }
                        </style>
                        <small class="help-block"></small>
                        <div class="progress">
                            <div class="bar"></div>
                            <div class="percent">0%</div>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="status">
                    <label for="status" class="col-sm-3 control-label">Disable Fast Foward</label>
                    <div class="col-sm-9">
                        {{ Form::checkbox('fast_forward', 1) }}
                        <small class="help-block"></small>
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
          $.fn.dataTable.ext.errMode = 'throw';
        try{

            var base_url = "{{ route('learningandtraining.course.content-lists',':id') }}";
            var url = base_url.replace(':id', {{$id}});
        var table = $('#table').DataTable({
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
                        emailContent(table, 'Course');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
{{--            ajax: "{{ route('admin.course-content.list') }}",--}}
            ajax: url,
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
                    data: 'training_courses.course_title',
                    name: 'training_courses.course_title'
                },
                {
                    data: 'content_title',
                    name: 'content_title'
                },
                {
                    data: 'course_content_types.type',
                    name: 'course_content_types.type'
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
                url: '{{ route('admin.course-content.store') }}',
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        console.log(data)
                        if(data.data.created == false){
                            swal("Saved", "Course content has been updated successfully \n Reference code is "+ data.data.reference_code, "success");
                        }else{
                            swal("Saved", "Course content has been created successfully \n Reference code is "+ data.data.reference_code, "success");
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
            var id = $(this).data('id');
            var url = '{{ route("admin.course-content.single",":id") }}';
            var url = url.replace(':id', id);
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        // $('#myModal select[name="course_id"]').val(data.course_id)
                        $('#myModal input[name="content_title"]').val(data.content_title)
                        $('#myModal select[name="content_type_id"]').val(data.content_type_id)
                       
                        
                        if(data.value != null){
                            $('#myModal #course_file_name').text(data.value)
                            $('#myModal #course_file_name').css('font-weight',500)
                        }
                        if(data.course_external_url != null){
                            $('#myModal input[name="course_external_url"]').val(data.course_external_url)
                        }
                        if(data.fast_forward){
                            $('#myModal input[name="fast_forward"]').prop( "checked", true );
                        }else{
                            $('#myModal input[name="fast_forward"]').prop( "checked", false );
                        }
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Course Content: ")
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
            var base_url = "{{ route('admin.course-content.destroy',':id') }}";
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
                                swal("Deleted", "Course content has been deleted successfully", "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else {
                                swal("Alert", "Cannot able to delete course content", "warning");
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

            $('#myModal input[name="course_id"]').val({{$id}});
        });
        /* Clear Uploaded File label - End */

    });
</script>
@stop
