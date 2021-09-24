
@extends('layouts.app')
@section('content')


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
@stop @section('scripts')
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
                        actions += '<a href="{{route("course-learner.view",'') }}/' + o.id + '" class="edit fa fa-pencil" data-id=' + o.id + '></a>'

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
        });
        /* Clear Uploaded File label - End */

    });
</script>
@stop
