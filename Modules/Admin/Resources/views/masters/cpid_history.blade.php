@extends('adminlte::page')
@section('title', 'CPID History')
@section('content_header')
<h1>CPID - {{ $result->cpid ?? '' }} </h1>
@stop
@section('content')

{{ Form::hidden('id', isset($id) ? old('id',$id) : null,array('id'=>'id')) }}
<table class="table table-bordered" id="cpid-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Effective From</th>
            <th>Pay Standard</th>
            <th>Pay Overtime</th>
            <th>Pay Stat</th>
            <th>Bill Standard</th>
            <th>Bill Overtime</th>
            <th>Bill Stat</th>
            <th>Created Date</th>
            <th>Last Modified Date</th>
            
        </tr>
    </thead>
</table>

@stop @section('js')
<script>
    $(function () {
        var id = $('#id').val();
        var url = "{{ route('cp-id.historyList',[':id']) }}";
            url = url.replace(':id', id);

            $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#cpid-table').DataTable({
            dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4,5,6,7,8,9]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2, 3,4,5,6,7,8,9]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4,5,6,7,8,9]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Cpid');
                    }
                }
                ],
            processing: false,
            serverSide: true,
           
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
                    sortable:false,
                },
                {
                    data: 'effective_from',
                    name: 'effective_from'
                },
                {
                    data: 'p_standard',
                    name: 'p_standard'
                },
                {
                    data: 'p_overtime',
                    name: 'p_overtime'
                },
                {
                    data: 'p_holiday',
                    name: 'p_holiday'
                },
                {
                    data: 'b_standard',
                    name: 'b_standard'
                },
                {
                    data: 'b_overtime',
                    name: 'b_overtime'
                },
                {
                    data: 'b_holiday',
                    name: 'b_holiday'
                },
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'}
                
            ]
        });
         } catch(e){
            console.log(e.stack);
        }

        

    });
</script>
@stop
