@extends('adminlte::page')
@section('title', 'RFP Award Date')
@section('content_header')

<h1>RFP Award Date</h1>
@stop
@section('content')

<table class="table table-bordered" id="tracking-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Time</th>
            <th>Created Date</th>
            <th>Last Modified Date</th>
            <th>Action</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">RFP Award Date</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'award-date-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group" id="award_dates">
                    <label for="award_dates" class="col-sm-3 control-label">Time (Days)</label><span class="mandatory"></span>
                    <div class="col-sm-9">
                        {{ Form::text('award_dates',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
              
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary','id'=>'mdl_save_change'))}}
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
        var table = $('#tracking-table').DataTable({
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
                        emailContent(table, 'Process Steps');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('rfp-award-date.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 0, "asc" ]],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'DT_RowIndex',name: '', sortable:false,},
                {data: 'award_dates', name: 'award_dates'},
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @endcan
                       
                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }
        $('#award-date-form').submit(function (e) {
            e.preventDefault();
            if($('#award-date-form input[name="id"]').val()){
                var message = 'Award date has been updated successfully';
            }else{
                var message = 'Award date has been created successfully';
            }
            formSubmit($('#award-date-form'), "{{ route('rfp-award-date.store') }}", table, e, message);
        });

           $("#tracking-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("rfp-award-date.single",":id") }}';
            var url = url.replace(':id', id);
            $('#award-date-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="award_dates"]').val(data.award_dates)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Award Date: " + data.award_dates)
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

</script>
@stop