@extends('adminlte::page')
@section('title', config('app.name', 'Laravel').'-Training Policy')
@section('content_header')

<h1>Policy Analytics</h1>
@stop
@section('content')

<div class="row dashboard-row">
     <div class="col-sm-4 dashboard-box">
 <span class="chart">  {!! $policy_count_chart->render() !!}</span>
 </div>

    <div id="chart" style="height: 300px;"></div>
    <!-- Charting library -->
    <script src="https://unpkg.com/echarts/dist/echarts.min.js"></script>
    <!-- Chartisan -->
    <script src="https://unpkg.com/@chartisan/echarts/dist/chartisan_echarts.js"></script>
    <!-- Your application script -->
    <script>
        const chart = new Chartisan({
            el: '#chart',
            url: "@chart('sample_chart')",
        });
    </script>


  <div class="col-sm-4 dashboard-box">
 <span class="chart">  {!! $compliant_count_chart->render() !!}</span>
</div>
  <div class="col-sm-4 dashboard-box">
 <span class="chart">  {!! $average->render() !!}</span>
</div>
</div>

<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="10%">Reference Code</th>
            <th width="15%">Policy Name</th>
            <th width="15%">Category</th>
            <th width="30%">Description</th>
{{--            <th width="5%">Statistics</th>--}}
        </tr>
    </thead>
</table>
@stop
 @section('js')
<style>
    .dashboard-box {
        border-right: 1px solid;
        padding-bottom: 1%;
        text-align: center;
    }

    .dashboard-row {
        background-color: #f26222;
        margin-bottom: 1%;
    }

    svg {
        display: block;
        width: 50%;
    }


</style>
<script>{!! Charts::assets() !!}</script>
<script>
    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                buttons: [{
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
                            emailContent(table, 'Policy');
                        }
                    }
                ],
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('policy.list') }}",
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
                        sortable: false
                    },
                    {
                        data: 'reference_code',
                        name: 'reference_code'
                    },
                    {
                        data: 'policy_name',
                        name: 'policy_name'
                    },
                    {
                        data: 'category.compliance_policy_category',
                        name: 'category.compliance_policy_category'
                    },
                    {
                        data: 'policy_description',
                        name: 'policy_description'
                    },

                    {{--{--}}
                    {{--    data: 'status',--}}
                    {{--    name: 'status',--}}
                    {{--    render: function (status) {--}}
                    {{--        return '<a  class="fa fa-bar-chart" href="{{ route('policy.statistics') }}"></a>';--}}
                    {{--    }--}}
                    {{--},--}}
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }
    });
</script>
@stop
