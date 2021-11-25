@extends('adminlte::page')
@section('title', 'Activity Code Setup')
@section('content_header')
<h1>Activity Code Setup</h1>
@stop
@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add Activity Code Setup">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="work-hour-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Activity Type</th>
            <th>Customer Type</th>
            <th>Code</th>
            <th>Duplicate Code</th>
            <th>Description</th>
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
            {{ Form::open(array('url'=>'#','id'=>'work-hour-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group row" id="work_hour_type_id">
                    <label for="work_hour_type_id" class="col-sm-3 control-label">Activity Code</label>
                    <div class="col-sm-9">
                        {{ Form::select('work_hour_type_id',[''=>'Please select']+$workHourType,null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="customer_type_id">
                    <label for="customer_type_id" class="col-sm-3 control-label">Customer Type</label>
                    <div class="col-sm-9">
                        {{ Form::select('customer_type_id',[''=>'Please select']+$customerType,null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="code">
                    <label for="code" class="col-sm-3 control-label">Code</label>
                    <div class="col-sm-9">
                        {{ Form::text('code',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="duplicate_code">
                    <label for="duplicate_code" class="col-sm-3 control-label">Duplicate Code</label>
                    <div class="col-sm-9">
                        {{ Form::text('duplicate_code',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group row" id="description">
                    <label for="description" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                        {{ Form::textArea('description',null,array('class'=>'form-control')) }}
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
        var table = $('#work-hour-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
             buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3,4,5]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3,4,5]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2, 3,4,5]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Feedback Lookups');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('work-hour-customer.list') }}",
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
                    data: 'work_hour_type_trashed.name',
                    name: 'work_hour_type_trashed.name'
                },
                {
                    data: 'customer_type_trashed.name',
                    name: 'customer_type_trashed.name'
                },
                 {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'duplicate_code',
                    name: 'duplicate_code'
                },
                 {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        if (o.is_editable == false) {
                        actions += '<a href="#" class="edit-disable {{Config::get('globals.editFontIcon')}}"></a>'
                        }
                        else{
                          actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>';  
                        }
                        @endcan
                        @can('lookup-remove-entries')
                        if (o.is_deletable == false) {
                        actions += '<a href="#" class="edit-disable  {{Config::get('globals.deleteFontIcon')}}"></a>';
                        }
                        else
                        {
                         actions += '<a href="#" class="delete  {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';    
                        }
                        @endcan
                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        /* Feedback Save - Start*/
        $('#work-hour-form').submit(function (e) {
            e.preventDefault();
            if($('#myModal input[name="id"]').val()){
                var message = 'Activity code has been updated successfully';
            }else{
                var message = 'Activity code has been created successfully';
            }
            formSubmit($('#work-hour-form'), "{{ route('work-hour-customer.store') }}", table, e, message);
        });
        /* Feedback Save - End*/


        /* Feedback Edit - Start */
        $("#work-hour-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("work-hour-customer.single",":id") }}';
            var url = url.replace(':id', id);
            $('#work-hour-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="code"]').val(data.code)
                        $('#myModal input[name="duplicate_code"]').val(data.duplicate_code)
                        $('#myModal select[name="work_hour_type_id"]').val(data.work_hour_type_id)
                        $('#myModal select[name="customer_type_id"]').val(data.customer_type_id)
                        $('#myModal textarea[name="description"]').val(data.description)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Activity Code Setup: " + data.work_hour_type.name)
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
        /* Feedback Edit - End */


        /* Feedback Delete - Start */
        $('#work-hour-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('work-hour-customer.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Activity code has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Feedback Delete - End */


    });
</script>
@stop
