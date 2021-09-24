
{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Candidate Security Awareness')

@section('content_header')
<h1>Candidate Security Awareness</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New Security Awareness">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="security-awareness-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Security Awareness</th>
            <th>Order Sequence</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Candidate Brand Awareness</h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'security-awareness-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('id', null) }}

                <div class="modal-body">
                    <div class="form-group {{ $errors->has('answer') ? 'has-error' : '' }}" id="answer">
                        <label for="name" class="col-sm-3 control-label">Security Awareness</label>
                        <div class="col-sm-9">
                            {{ Form::text('answer',null,array('class'=>'form-control','required'=>true)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="order_sequence">
                        <label for="order_sequence" class="col-sm-3 control-label">Order Sequence Number</label>
                        <div class="col-sm-9">
                            {{ Form::number('order_sequence',null,array('class'=>'form-control','min'=>1)) }}
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
        var table = $('#security-awareness-table').DataTable({
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
                        emailContent(table, 'Time Off Request Type');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('candidate-security-awareness.list') }}",
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
                {data: 'answer', name: 'answer'},
                {data: 'order_sequence', name: 'order_sequence'},
                {data: null,
                    orderable: false,
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

        /* Save Request Type - Start*/
        $('#security-awareness-form').submit(function (e) {
            e.preventDefault();
            if($('#security-awareness-form input[name="id"]').val()){
                var message = 'Security awareness has been updated successfully';
            }else{
                var message = 'Security awareness has been created successfully';
            }
            formSubmit($('#security-awareness-form'), "{{ route('candidate-security-awareness.store') }}", table, e, message);
        });
        /* Save Request Type - End*/

         /* Editing Request Type - Start */
        $("#security-awareness-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("candidate-security-awareness.single",":id") }}';
            var url = url.replace(':id', id);
            $('#security-awareness-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    console.log(data);
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="answer"]').val(data.answer);
                        $('#myModal input[name="order_sequence"]').val(data.order_sequence);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Awareness Type: ")
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
        /* Editing Request Type - End */

        /* Request Type delete - Start */
        $('#security-awareness-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('candidate-security-awareness.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Security awareness has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Request Type delete - End */

    });
</script>
@stop
