@extends('layouts.app')
@section('content')

<div class="table_title">
    <h4>Customer Survey Report</h4>
</div>
<div class="row">

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
        <div>
            <button class="form-control button btn submit" id="filterbutton" name="filterbutton" type="button">Search</button>
        </div>
    </div>


</div>
<div class="row" id="reportdiv" style="padding: 20px;">

</div>
@endsection

@section('scripts')
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script> --}}
<script src="sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>

    <script type="text/javascript">
        $(document).ready(function(e){
            $("#payperiodlist").select2();
        });

        $("#filterbutton").on("click",function(e){
            let payperiodid = $("#payperiodlist").val();
            let payperiodName = $("#payperiodlist option:selected").text();
            $.ajax({
                type: "post",
                url: "{{route('reports.getsurveryreport')}}",
                data: {"payperiodid":payperiodid},
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
                success: function (response) {
                    $("#reportdiv").html(response);
                }
            }).done(function(e){
                if (e) {
                    var groupColumn = 0;
                    var groupname = "";
                    var date = new Date();
                    var table = $('#resulttable').DataTable({

                        "pageLength": 10,
                        "bInfo" : true,
                        "scrollX": true,
                        "sScrollY": "90%",
                        "responsive": false,
                        dom: 'Blfrtip',
                        lengthMenu: [
                            [ 10, 25, 50, -1 ],
                            [ '10', '25', '50', 'All' ]
                        ],
                        buttons: [{
                            extend: 'excel',
                            title : 'Customer Survey Report - '+ payperiodName,
                        }],
                        "columnDefs": [
                            { "width": "15%","orderable": true, "targets": [0] },
                            { "orderable": false, "targets": "_all" }
                        ]
                    });

                    table.on('page.dt', function() {
                        $('html, body').animate({
                            scrollTop: $('html').offset().top
                        }, 'slow');
                        });

                } else {
                    swal({
                    icon: 'warning',
                    title: 'Oops',
                    text: 'Reports not found',
                    });
                }

            });
        });
    </script>
@stop
@section('css')
    <style>
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
            float: left;
            margin-left: 85rem;
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
        .swal2-styled.swal2-confirm {
            background-color: #003A63 !important;
        }
        .swal2-icon.swal2-warning {
            border-color: ##F8BB86 !important;
            color: ##F8BB86 !important;
        }
        body {
            overflow-x: hidden; /* Hide horizontal scrollbar */
        }
    </style>
@endsection
