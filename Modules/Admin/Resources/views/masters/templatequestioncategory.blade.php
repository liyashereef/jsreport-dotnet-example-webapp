@extends('adminlte::page')

@section('title', 'Template Questions Category')

@section('content_header')
<h1>Template Questions Category</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" onclick="addnew()">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="template-questions-category-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Description</th>
            <th>Average</th>
            <th>Show under safety dashboard</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Template</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'question-category-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>

                <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}" id="description">
                    <label for="description" class="col-sm-3 control-label">Category Description <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="description">
                        {!! $errors->first('description', '<small class="help-block">:message</small>') !!}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('safety_type') ? 'has-error' : '' }}" id="safety_type">
                    <label for="safety_type" class="col-sm-3 control-label">Show under safety dashboard<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        <select name="safety_type" class="form-control">
                            <option value="" disabled selected>Select</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        {!! $errors->first('safety_type', '<small class="help-block">:message</small>') !!}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('average') ? 'has-error' : '' }}" id="average">
                    <label for="average" class="col-sm-3 control-label">Average <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        <select name="average" class="form-control">
                            <option value="" disabled selected>Select</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                        {!! $errors->first('average', '<small class="help-block">:message</small>') !!}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                <button class="btn btn-primary blue" data-dismiss="modal" aria-hidden="true">Cancel</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop
@section('js')
<script>

    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#template-questions-category-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Question Category');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url":"{{ route('templatequestioncategory.list') }}",
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 1, "asc" ]],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'description', name: 'description'},
                {data: 'average', name: 'average'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        return (o.safety_type)? 'Yes':'No';
                    }
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        /* Posting data to ExperienceController - Start*/
        $('#question-category-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('templatequestioncategory.store') }}";
            var formData = new FormData($('#question-category-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        if (data.created == false) {
                            swal("Saved", "Question category has been updated successfully", "success");
                        }else{
                            swal("Saved", "Question category has been created successfully", "success");
                        }
                        $("#myModal").modal('hide');
                        table.ajax.reload();
                    } else {
                        alert(data);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });
        /* Posting data to ExperienceController - End*/



        $("#template-questions-category-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            $('#question-category-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: "{{route('templatequestioncategory.single')}}",
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="description"]').val(data.description);
                        $('#myModal select[name="average"]').val(data.average);
                        $('#myModal select[name="safety_type"]').val(data.safety_type);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Question Category: " + data.description)
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



        $('#template-questions-category-table').on('click', '.delete', function (e) {
            id = $(this).data('id');
            console.log(e);
            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, remove",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "{{route('templatequestioncategory.destroy')}}",
                    type: 'GET',
                    data: "id=" + id,
                    success: function (data) {
                        if (data.success) {
                            swal("Deleted", "Question category has been deleted successfully", "success");
                            table.ajax.reload();
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

        //To reset the hidden value in the form
        $('#myModal').on('hidden.bs.modal', function () {
            $('#question-category-form').find('input[name="id"]').val('0');
        });

    });
    function addnew() {
        $("#myModal").modal();
        $('#question-category-form').trigger('reset');
        $('#myModal .modal-title').text("Add New Question Category");
        $('#question-category-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
    }
</script>
@stop
