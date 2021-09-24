@extends('adminlte::page')
@section('title', 'Score Criteria')
@section('content_header')
<h3>Match Score Criteria</h3>
@stop
@section('content')
<table class="table table-bordered  responsive nowrap" id="score-criteria-table"  cellspacing="0" width="100%">
    <thead>
        <tr>
            <th></th>
            <th>#</th>
            <th>Criteria Name</th>
            <th>Type</th>
        </tr>
    </thead>
</table>
@stop
@section('js')
<script>
    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#score-criteria-table').DataTable({
            bProcessing: false,
            responsive: true,
            processing: false,
            serverSide: true,
            dom: 'lfrtBip',
            buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [1, 2]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [1, 2]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [1, 2]
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
            ajax: "{{ route('recruitment.score-criteria.list') }}",
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
            "columnDefs": [
                { "width": "60%", "targets": 2 },
                // { "width": "10%", "targets": 2 },
                // { "width": "5%", "targets": 3 },
            ],
            columns: [{

                    data: 'id',
                    name: 'id',
                    visible:false
                },{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false
                },
                {
                    data: 'criteria_name',
                    name: 'criteria_name',
                },
                {
                    data: 'type_id',
                    name: 'type_id'
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }
    });
</script>
@stop
