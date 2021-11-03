
{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Job Requisition Reasons')

@section('content_header')
<h1>Job Requisition Reasons</h1>
@stop

@section('content')
<div id="message"></div>
<!--<div class="add-new" data-title="Add New Job Requisition Reason">Add <span class="add-new-label">New</span></div>-->
<table class="table table-bordered" id="reason-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Reason</th>
            <th>Last Modified Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Reasons for Open Positions List</h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'reason-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('id', null) }}
                <div class="modal-body">
                    <div class="form-group" id="reason">
                        <label for="name" class="col-sm-3 control-label">Reasons for Job Requisition</label>
                        <div class="col-sm-9">
                            {{ Form::text('reason',null,array('class'=>'form-control')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                    {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
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
        var table = $('#reason-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
             buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Job Requisition Reasons');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('job-requisition-reason.list') }}",
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
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'reason', name: 'reason'},
                {data: 'updated_at', name: 'updated_at'},
                {data: null,
                    orderable: false,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        if(o.id=={{ $terminate_id }} || o.id=={{ $resignate_id }}){
                            @can('edit_masters')
                            actions += '<a class="{{Config::get('globals.editFontIcon')}} edit-disable" title="Unable to edit" ></a>'
                            @endcan
                        }
                        else{
                            @can('edit_masters')
                            actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>';
                           @endcan
                        }
                        return actions;
                        }
                    },
                
            ]
        });
        } catch(e){
            console.log(e.stack);
        }


        /* Save Job Requisition Reason - Start*/
        $('#reason-form').submit(function (e) {
            e.preventDefault();
            if($('#reason-form input[name="id"]').val()){
                var message = 'Job requisition reason has been updated successfully';
            }else{
                var message = 'Job requisition reason has been created successfully';
            }
            formSubmit($('#reason-form'), "{{ route('job-requisition-reason.store') }}", table, e, message);
        });
        /* Save Job Requisition Reason - End*/

         /* Editing Job Requisition Reason - Start */
        $("#reason-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("job-requisition-reason.single",":id") }}';
            var url = url.replace(':id', id);
            $('#reason-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="reason"]').val(data.reason);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Reason: " + data.reason)
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
        /* Editing Job Requisition Reason - End */
    });
</script>
@stop
