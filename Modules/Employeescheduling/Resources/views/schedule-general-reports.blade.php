@extends('layouts.app') @section('content')
@section('css')
    <style>
        .greenbg{
            background: #343F4E;
            color: white !important;
        }
        .greenbg a{
            color: white !important;
        }
        .yellowbg{
            background:yellow;
            color: white !important;
        }
        .yellowbg a{
            color: #000 !important;
        }
        .redbg{
            background:red;
            color: white !important;
        }
        
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12"><div class="table_title position-static">
            <h4>Client Schedule</h4>
        </div></div>
        
    </div>
    <div class="row beforeprojectselect">
        <div class="col-md-4">
            <select id="project" placeholder="Select a Project">
                <option value="">Select a Customer</option>
            
                @foreach ($customers as $value)
                    <option value="{{$value["id"]}}">{{$value["project_number"]}}-{{$value["client_name"]}}</option>
                @endforeach
                
            </select>
        </div>
        <div class="col-md-1">Pay Period :</div>
        <div class="col-md-4" style="height:120px;overflow-y:auto">
            <select id="payperiod" multiple="multiple">
                <option value=""></option>
                @foreach ($payperiods as $payperiod)
                    <option value="{{$payperiod->id}}">{{$payperiod->pay_period_name}}({{$payperiod->short_name}})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary" id="viewreport">View</button>
        </div>
       
    
    </div>
    <div class="row reportrow">

    </div>
    




 

@stop

@section('scripts')
    <script type="text/javascript">
    $("#viewreport").on("click",function(e){
            e.preventDefault();
            var project = $("#project").val();
            var payperiod = $("#payperiod").val();
            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('scheduling.schedulegeneralreportresults')}}",
                data: {"project":project,"payperiod":payperiod},
                success: function (response) {
                    $(".reportrow").html(response).after(function(e){
                        $("#genreport").dataTable({
                            dom: 'Blfrtip',
                            buttons: [
                                {
                                    extend: 'excelHtml5',
                                    title: 'Scheduling Audit report'
                                }
                            ],
                            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                            "columns": [
                            { "width": "7%" },
                            { "width": "10%" },
                            { "width": "7%" },
                            { "width": "7%" },
                            { "width": "7%" },
                            { "width": "7%" },
                            null,
                            { "width": "7%" },
                            { "width": "7%" }
                            ]});
                    });
                }
            });
        })
        $(function(e){
            var currentpayperiod = JSON.parse("{{ json_encode($currentpayperiod) }}");

            $("#project").select2();
            $("#payperiod").val(currentpayperiod).select2();
            //$("payperiod").val(currentpayperiod)
            $("#viewreport").trigger("click");
        })

        
    </script>
@endsection