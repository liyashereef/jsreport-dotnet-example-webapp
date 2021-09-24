@extends('adminlte::page')
@section('title', 'Expense tracker')

@section('content_header')

<h1>Tax Master</h1>
@stop
@section('content')

<div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">Tax Master Detail</a></li>
                <li><a href="#tab_2" data-toggle="tab">Tax History</a></li>
            </ul>
            {{ Form::hidden('id', isset($id) ? old('id',$id) : null,array('id'=>'id')) }}
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <table class="table table-bordered" id="tax-master-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tax Name</th>
                                <th>Short Name</th>
                                <th>Tax Percentage</th>
                                <th>Effective Date</th>
                            </tr>
                        </thead>
                    </table>

                    <br>

                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_2">
                    <table class="table table-bordered" id="tax-master-archieved">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tax %</th>
                                <th>Effective Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
            <!-- /.tab-content -->
        </div>
        <!-- nav-tabs-custom -->
    </div>
    <!-- /.col -->

</div>
@endsection
<style>
    .table.dataTable {
        width: 100% !important;
    }
</style>
@section('js')
<script>
    $(function () {
            $.fn.dataTable.ext.errMode = 'throw';         
            var id = $('#id').val();  
            var url = '{{ route('tax-master.show',[":id"]) }}';
            url = url.replace(':id', id);
        try{
        var table = $('#tax-master-table').DataTable({
            dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
               
                ],
            processing: false,
            serverSide: true,
            responsive: true,
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'short_name',
                    name: 'short_name'
                },
                {
                    data: 'tax_master_log.tax_percentage',
                    name: 'tax_master_log.tax_percentage',
                    defaultContent: "--"
                },
                {
                    data: 'tax_master_log.effective_from_date', 
                    name: 'tax_master_log.effective_from_date',
                    defaultContent: "--"
                },
               
            ]
        });
         } catch(e){
            console.log(e.stack);
        }
       
    });
</script>
<script>
    $(function () {
            $.fn.dataTable.ext.errMode = 'throw';         
            var id = $('#id').val();  
            var url = '{{ route('tax-master.archive',[":id"]) }}';
            url = url.replace(':id', id);
        try{
        var table = $('#tax-master-archieved').DataTable({
            dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                
                ],
            processing: false,
            serverSide: true,
            responsive: true,
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
                    data: 'tax_percentage',
                    name: 'tax_percentage',
                    defaultContent: "--"
                },
                {
                    data: 'effective_from_date', 
                    name: 'effective_from_date',
                    defaultContent: "--"
                },
                {
                    data: 'effective_end_date', 
                    name: 'effective_end_date',
                    defaultContent: "--"
                },
               
            ]
        });
         } catch(e){
            console.log(e.stack);
        }
       
    });
</script>
@stop