@extends('adminlte::page')
@section('title', config('app.name', 'Laravel').'-Training Course')
@section('content_header')

<h1>Courses</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Training Course">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Reference Code</th>
            <th>Course Title</th>
            <th>Category</th>
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
                        {{ Form::textarea('course_description',null,array('class' => 'form-control', 'Placeholder'=>'Description', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="course_objectives">
                    <label for="course_objectives" class="col-sm-3 control-label">Course Objective</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('course_objectives',null,array('class' => 'form-control', 'Placeholder'=>'Course Objective' , 'required'=>TRUE)) }}
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
                <div class="form-group" id="course_file">
                    <label for="course_file" class="col-sm-3 control-label">Upload Training Course</label>
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
                <div class="form-group">
                <label for="or" class="col-sm-3 control-label">OR</label>
                </div>
                <div class="form-group" id="course_external_url">
                    <label for="course_external_url" class="col-sm-3 control-label">Insert URL(For External Course)</label>
                    <div class="col-sm-9">
                        {{ Form::text('course_external_url',null,array('class' => 'form-control', 'Placeholder'=>'http://')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="status">
                    <label for="status" class="col-sm-3 control-label">Active</label>
                    <div class="col-sm-9">
                        {{ Form::checkbox('status', 1) }}
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
@stop @section('js')
<script>
    $(function () {
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
            ajax: "{{ route('course.list') }}",
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
                    data: 'reference_code',
                    name: 'reference_code'
                },
                {
                    data: 'course_title',
                    name: 'course_title'
                },
                {
                    data: 'training_category.course_category',
                    name: 'training_category.course_category'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @endcan
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
                url: '{{ route('course.store') }}',
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
            var id = $(this).data('id');
            var url = '{{ route("course.single",":id") }}';
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
            var base_url = "{{ route('course.destroy',':id') }}";
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
                                swal("Alert", "This Course has one or more Employee or Site Profile", "warning");
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
        });
        /* Clear Uploaded File label - End */

    });
</script>
@stop
