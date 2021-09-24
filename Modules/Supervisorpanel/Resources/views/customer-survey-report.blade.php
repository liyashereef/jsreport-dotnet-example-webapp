@extends('layouts.app')
@section('content')

<div class="container-fluid">
<div class="table_title">
    <h4>Customer Survey Report</h4>
</div>
<div class="row" style="padding-bottom:10px">
    
    <div class="col-md-3">
    </div>
    <div class="col-md-1" style="text-align:center">
            
        </div>
        <div class="col-md-3">
            <div role="wrapper" class="gj-datepicker gj-datepicker-md gj-unselectable">
                <select id="payperiodlist">
                    <option value="0">Select a Pay Period</option>
                    @foreach ($pay_periods as $payperiod)
                         <option value="{{$payperiod->id}}">{{$payperiod->pay_period_name}}({{$payperiod->short_name}})</option>
                    @endforeach
                </select>    
            </div>
        </div>
    <div class="col-md-1">
        <button class="form-control button btn submit" id="filterbutton" name="filterbutton" type="button">Search</button>
    </div>
    

</div>
<div class="row" id="reportdiv">
    
</div>
    
</div>      

     
   

@endsection  

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function(e){
            $("#payperiodlist").select2();
        });

        $("#filterbutton").on("click",function(e){
            let payperiodid = $("#payperiodlist").val();
            $.ajax({
                type: "post",
                url: "{{route('customers.getsurveryreport')}}",
                data: {"payperiodid":payperiodid},
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
                success: function (response) {
                    $("#reportdiv").html(response);
                }
            }).done(function(e){
                var groupColumn = 0;
                var groupname = "";
                var table = $('#resulttable').DataTable({
                    
                    "pageLength": 10,
                    "bInfo" : false,
                    dom: 'Blfrtip',
                    lengthMenu: [
                        [ 10, 25, 50, -1 ],
                        [ '10', '25', '50', 'All' ]
                    ],
                    buttons: [
                        'excelHtml5'
                    ],
                    "columnDefs": [
                        { "orderable": false, "targets": [1, 2, 3, 4,5,6,7,8] },
                        { "width": "15%","orderable": true, "targets": [0] },
                        { "width": "5%", "targets": [1] },
                        { "width": "10%", "targets": [2] },
                        { "width": "10%", "targets": [3] },
                        { "width": "10%", "targets": [4] },
                        { "width": "10%", "targets": [5] }
                    ]
                   
                
                });

                table.on('page.dt', function() {
                    $('html, body').animate({
                        scrollTop: $(".dataTables_wrapper").offset().top
                    }, 'slow');
                    });

            });
        });
    </script>
    
@stop
@section('css')
    <style>
        .dataTables_wrapper{
            width: 100%;
        }
        #resulttable_paginate{
            float:left;
        }
        #resulttable_filter{
            margin-right: 3rem;
        }
        footer {
            position: fixed;
        }
    </style>
@endsection