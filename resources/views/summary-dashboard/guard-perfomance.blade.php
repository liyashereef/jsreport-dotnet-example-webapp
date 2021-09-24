@extends('layouts.app')

@section('content')

<style>
    .gpt-container {
        width: 100%;
        height: 100%;
    }

    .js-info-sec {
        display: none;
    }
</style>

<div class="row">
    <div class="col-md-8">
        <div class="table_title">
            <h4>Guard Perfomance Trends<span id="gp-date"></span></h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-4"><label>Chart Frequency</label></div>
            <div class="col-md-8">
                <select class="form-control js-chat-frequency">
                    @foreach($freqs as $key => $f)
                    <option value="{{$key}}" data-from="{{$f['from']->format('Y-m-d')}}" data-to="{{$f['to']->format('Y-m-d')}}">{{$f['label']}}</option>
                    @endforeach
                </select>
                <span class="help-block"></span>
            </div>
        </div>
    </div>

    <div class="m-4 gpt-container">
        <canvas id="gpt-canvas" height="400"></canvas>
    </div>
</div>

<div class="js-info-sec">

    <!-- Customer List table -->
    <div class="js-cust-summary"></div>

    <!-- Employee list table -->
    <div class="mt-4 mb-4 table_title">
        <h4>Rating Details</h4>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive js-child-table">
                <table id="grd-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Customer</th>
                            <th>Emp No</th>
                            <th>Phone No</th>
                            <th>Email</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@stop
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>

<script>
    const gp = {
        chart: null,
        table: null,
        res: {},
        getParams() {
            let args = globalUtils.uraQueryParamToJson(window.location.href);
            args.cIds = globalUtils.decodeFromCsv(args.cIds)
            args.frequency = $('.js-chat-frequency').val();
            return args;
        },
        loadChart() {
            let root = this;
            $.ajax({
                type: "GET",
                url: "{{route('guard-perfomance-info')}}",
                data: root.getParams(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    let data = root.buildChartData(response.data.graph);
                    root.res = response.data.list;
                    root.generateClientGraph(data, '#gpt-canvas');
                }
            });
        },
        buildChartData(inputs) {
            //gen chart data
            let res = [];
            let minVal = 5;

            for (const input of inputs) {
                let val = Number(input.value).toFixed(2);
                if (val <= minVal) {
                    minVal = (parseInt(val) - 0.5);
                }
                res.push({
                    rawDate: input.date,
                    x: new Date(input.date),
                    y: val
                })
            }

            if (minVal < 0 || inputs.length <= 0 || minVal === 5) {
                minVal = 0;
            }

            return {
                res: res,
                suggestedMin: minVal
            };
        },
        //generate client survey graph
        generateClientGraph(chartData, canvasId) {

            let root = this;
            if (root.chart) {
                root.chart.destroy();
            }

            root.chart = new Chart($(canvasId), {
                type: 'bar',
                data: {
                    datasets: [{
                        backgroundColor: "#333f5a",
                        data: chartData.res
                    }]
                },
                options: {
                    onClick: root.onGraphClick,
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false,
                    },
                    scales: {
                        xAxes: [{
                            offset: true,
                            type: 'time',
                            maxBarThickness: 100,
                            time: {
                                unit: 'month',
                            },
                            gridLines: {
                                lineWidth: 0
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                suggestedMin: chartData.suggestedMin,
                                autoSkip: false,
                            },
                        }],

                    }
                }
            });
        },
        onGraphClick(e, array) {
            let el = gp.chart.getElementAtEvent(e);
            let data = el[0]._chart.config.data.datasets[0].data[el[0]._index];
            let key = data.rawDate;
            gp.generateChildTable(data.x, key);
        },
        generateCustomerSummaryTable(data) {
            if (data.length <= 1) {
                $('.js-cust-summary').html('');
                return;
            }

            let csBody = '';
            data.forEach(function(item, i) {
                csBody += `
                    <tr>
                        <td>${i}</td>
                        <td>${item.customer_name}</td>
                        <td>${Number(item.average).toFixed(2)}</td>
                    </tr>

                `;
            });

            let cs = `
            <div class="mt-4 mb-4 table_title">
                <h4>Customer Rating Summary</h4>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive js-child-table">
                        <table id="cust-summary-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody>${csBody}</tbody>
                        </table>
                    </div>
                </div>
            </div>
            `;

            $('.js-cust-summary').html(cs);

            $('#cust-summary-table').DataTable({
                response: true,
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
            });

        },
        generateChildTable(data, key = null) {
            let root = this;
            let d = this.getParams();
            d.date = moment(data).format('YYYY-MM-DD');

            if (this.res.hasOwnProperty(key)) {
                d.split_up = this.res[key];
            }
            $('.js-info-sec').show();

            this.table = $('#grd-table').DataTable({
                destroy: true,
                responsive: true,
                dom: 'Blfrtip',
                serverSide: true,
                fixedHeader: true,
                buttons: [],
                "drawCallback": function(settings, json) {
                    root.generateCustomerSummaryTable(settings.json.custAgg);
                },
                ajax: {
                    url: "{{route('guard-perfomance-details')}}",
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    },
                    "type": "POST",
                    "data": function(rp) {
                        rp.date = d.date;
                        rp.split_up = d.split_up;
                    },
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                order: [
                    [0, 'desc']
                ],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'employee_no',
                        name: 'employee_no'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'employee_work_email',
                        name: 'employee_work_email'
                    },
                    {
                        data: 'rating',
                        name: 'rating'
                    },

                ],
            });

        },
        setCustomFilter() {
            let args = globalUtils.uraQueryParamToJson(window.location.href);
            if (args.from && args.to) {
                $('.js-chat-frequency').append(`
                <option value="custom" data-from="${args.from}" data-to="${args.to}" selected>Custom</option>
                `);
            }
        },
        showFilterRangeInfo() {
            let el = $('.js-chat-frequency').find('option:selected');
            let from = $(el).data('from');
            from = moment(from).format('MMMM Do YYYY')
            let to = $(el).data('to');
            to = moment(to).format('MMMM Do YYYY');

            $('#gp-date').html(` (${from} - ${to})`)
        },
        init() {
            let root = this;
            this.setCustomFilter();
            this.showFilterRangeInfo();

            $('.js-chat-frequency').on('change', function() {
                $('.js-info-sec').hide();
                root.showFilterRangeInfo();
                root.loadChart();
            });
            this.loadChart();
        }
    };

    //Document ready init
    $(function() {
        gp.init();
    });
</script>

@stop