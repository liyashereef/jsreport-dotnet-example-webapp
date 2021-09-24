@extends('layouts.app')
@section('content')
    <div class="table_title">

        <h4>Compliance Report</h4>

    </div>
    <div class="row">
        <div class="col-md-1">
            Customers
        </div>
        <div class="col-md-3">
            <select id="project" name="project[]" multiple placeholder="Select a Project">

                {{-- <option value=""></option> --}}

                @foreach ($Customers as $value)
                    <option value="{{$value["id"]}}">{{$value["project_number"]}}-{{$value["client_name"]}}</option>
                @endforeach

            </select>
        </div>
        <div class="col-md-1">
            Area Manager
        </div>
        <div class="col-md-3">
            <select id="areamanagerselect" multiple="multiple">
                   @foreach ($areaManager as $key=>$value)
                       <option value="{{$value[0]}}">{{$value[1]}}</option>
                   @endforeach


            </select>
        </div>
        <div class="col-md-1">
            Employees
        </div>
        <div class="col-md-3">
            <select id="employeesselect" multiple="multiple">

                @foreach ($Employees as $Employee)
                    <option value="{{$Employee->id}}">{{$Employee->getFullNameAttribute()}}</option>
                @endforeach

            </select>
        </div>





       </div>   <div class="row mt-2">




        <div class="col-md-1">
            <label >Start Date</label>
       </div>
           <div class="col-sm-3">
                    <input id="start_date" class="form-control datepicker" placeholder="Start Date" type="text" max="2900-12-31" value="{{date('Y-m-d', strtotime("-1 days"))}}">
           </div>
           <div class="col-md-1">
            <label >End Date</label>
       </div>
           <div class="col-sm-3">
                    <input id="end_date" class="form-control datepicker" placeholder="End Date" type="text" max="2900-12-31" value="{{date('Y-m-d')}}">
            </div>
            <div class="col-md-1">
                <input id="filterbutton" class="btn btn-primary form-control" type="button" value="Search">
        </div>
       </div>

   <div class="row" id="reportdiv" style="display:none">
        <div class="col-md-12" id="chartDiv">


        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
       $(document).ready(function () {
           $("#project").select2()
           $("#areamanagerselect").select2();
           $("#employeesselect").select2();
           setTimeout(() => {
               $("#filterbutton").trigger("click")
           }, 500);
       });


       $(document).on("click","#filterbutton",function(e){
           e.preventDefault();
           $.ajax({
               type: "post",
               url: '{{route("reports.getcompliancegraph")}}',
               data:{"customer_id":$("#project").val(),
               "startDate":$("#start_date").val(),
               "endDate":$("#end_date").val(),
               "employees":$("#employeesselect").val(),
               "area_manager":$("#areamanagerselect").val()},
               headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
               success: function (response) {
                   var data=jQuery.parseJSON(response)
                   chartFunction(data)
               }
           }).done(function(data){
               $("#reportdiv").show()
           });
       })

       var chartFunction=function(response){

        $('#chartDiv').highcharts({
            chart: {
                type: 'area'
            },
            title: {
                text: '',
                x: -20 //center
            },credits: {
    enabled: false
  },
            subtitle: {
                text: '',
                x: -20
            },
            xAxis: {
                categories: response["series"]
            },
            yAxis: {
                title: {
                    text: ''
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: ''
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0,
                 showInLegend: false
            },
            series: [{
                    name: 'Total',
                    color: 'black',
                    marker: {
                        fillColor: 'black'
                    },
                    fillColor: 'white',
                    data: response["data"]["total"]
                }, {
                    name: 'Yes',
                    color: '#003b63',
                    marker: {
                        lineColor: '#003b63',
                    },
                    fillColor: '#003b63',
                    data: response["data"]["yes"]
                }, {
                    name: 'No',
                    color: '#F2351F',
                    marker: {
                        lineColor: 'white',
                    },
                    fillColor: '#F2351F',
                    data: response["data"]["no"],
                }
            ]
        });
       }
    </script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>
@endsection
@section('css')
    <style>
        /* .fixed {
            position: fixed;
            top: 8rem;
        } */
        .dataTables_wrapper{
            width: 100%;
        }
        /* .dt-button.buttons-excel.buttons-html5 {
            position: fixed;
        }
        #resulttable_length.dataTables_length {
            position: fixed;
            margin-left: 4rem;
        }
        #resulttable_paginate{
            float:right;
            margin-right: 116rem;
        }
        #resulttable_info{
            float: left;
        }
        #resulttable_filter{
            right: 1rem;
            position: fixed;
        }
        #resulttable {
            margin-top: 40px;
            position: relative;
            z-index: 5;
        } */
        footer {
            position: fixed;
        }
        .colorClass1 {
            background-color: #F3F3F3;
        }
        .colorClass2 {
            background-color: #d9d9d9;
        }
        .swal2-styled.swal2-confirm {
            background-color: #003A63 !important;
        }
        .swal2-icon.swal2-warning {
            border-color: #F8BB86 !important;
            color: #F8BB86 !important;
        }
        .labelstyle {
            float: right;
            margin-right: -15px;
            margin-top: 6px;
        }

        body {
            overflow-x: hidden; /* Hide horizontal scrollbar */
        }
    </style>
@endsection
