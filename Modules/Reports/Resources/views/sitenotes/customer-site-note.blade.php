@extends('layouts.app')
@section('content')
    <div class="table_title">

        <h4>Site Notes Report</h4>

    </div>
   <div class="row">
    <div class="col-md-2">
    </div>
       <div class="col-md-1">
            <label class="labelstyle">Start Date</label>
       </div>
       <div class="col-sm-2">
                <input id="start_date" class="form-control datepicker" placeholder="Start Date" type="text" max="2900-12-31" value="{{date('Y-m-d', strtotime("-30 days"))}}">
       </div>
       <div class="col-md-1">
    </div>
       <div class="col-md-1">
            <label class="labelstyle">End Date</label>
       </div>
       <div class="col-sm-2">
                <input id="end_date" class="form-control datepicker" placeholder="End Date" type="text" max="2900-12-31" value="{{date('Y-m-d')}}">
        </div>
        <div class="col-md-1">
                <input id="filterbutton" class="btn btn-primary" type="button" value="Submit">
        </div>
   </div>

   <div class="row" id="reportdiv" style="padding: 20px;">

    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $('#start_date').on('change', function (evt) {
            var selectedDate = $('#start_date').val();
            var endDate = $('#end_date').val();

            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;

            if (endDate != '' && endDate < selectedDate) {
                $('#start_date').val('');
                swal({
                    icon: 'error',
                    title: 'Oops',
                    text: 'End date is less than start date',
                });
            }
        });

        $('#end_date').on('change', function (evt) {
            var selectedDate = $('#end_date').val();
            var startDate = $('#start_date').val();

            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;

            if (startDate > selectedDate) {
                $('#end_date').val('');
                swal({
                    icon: 'error',
                    title: 'Oops',
                    text: 'Start date is greater than end date',
                });
            }
        });

       $(function() {
            $('#start_date').datepicker({
                "format": "yyyy-mm-dd",
                "setDate": new Date() - 30,
            });

            $('#end_date').datepicker({
                "format": "yyyy-mm-dd",
                "setDate": new Date(),
            });
       });

        $("#filterbutton").on("click",function(e){
            let startDate = $("#start_date").val();
            let endDate = $("#end_date").val();
            if (startDate == '') {
                swal({
                icon: 'error',
                title: 'Oops',
                text: 'Please fill start date',
                });
            } else if (endDate == '') {
                swal({
                icon: 'error',
                title: 'Oops',
                text: 'Please fill end date',
                });
            } else {
                $.ajax({
                    type: "post",
                    url: "{{route('customer.getsitenotes')}}",
                    data: {"start_date":startDate,
                            "end_date":endDate},
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
                        var prevClient = -1;
                        var color = 'colorClass1';

                        var table = $('#resulttable').DataTable({

                            "pageLength": -1,
                            "bInfo" : true,
                            "scrollX": true,
                            "sScrollY": "90%",
                            "responsive": false,
                            dom: 'Blfrtip',
                            columnDefs: [
                            { width: 200, targets: 0 },
                            {width: 100, targets: 1},
                            {width: 100, targets: 8},
                            {width: 100, targets: 9}
                            ],
                            lengthMenu: [
                                [ 10, 25, 50, -1 ],
                                [ '10', '25', '50', 'All' ]
                            ],
                            buttons: [{
                            extend: 'excel',
                            title : 'Site Notes '+startDate+'-'+endDate,
                            customize: function(xlsx) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                // Loop over the cells
                                var i = 0;
                                var oldprojectname = "";
                                var colorno = 11;
                                var oldcolorno = 11;
                                $('row c', sheet).each(function() {
                                //select the index of the row

                                var numero=$(this).parent().index() ;
                                    var attr = $('is t', this).text();
                                    var residuo = numero%2;

                                    if (numero==1){
                                        //$(this).attr('s','22');//22 - Bold, blue background
                                    }else if (numero>1){
                                        if(residuo ==0  ){//'is t',
                                        //$(this).attr('s','25');//25 - Normal text, fine black border
                                        }else{
                                        //$(this).attr('s','32');//32 - Bold, gray background, fine black border
                                        }
                                    }
                                    if(attr!='Site Notes '+startDate+'-'+endDate){
                                        if(i%10==0){

                                            var projname = $('is t', this).text();
                                            console.log(projname);
                                            if(projname == "Project"){
                                                colorno = 22;
                                                oldcolorno = 22;
                                            }
                                            else
                                            {
                                                if(oldprojectname==projname){
                                                    if(oldcolorno==19){
                                                    // colorno = 21;
                                                    // oldcolorno = 21;
                                                    }else{
                                                    // colorno = 11;
                                                    // oldcolorno = 11;
                                                    }

                                                }else{
                                                    if(oldcolorno==5){
                                                        colorno = 20;
                                                        oldcolorno = 20;
                                                    }else{
                                                        colorno = 5;
                                                        oldcolorno = 5;
                                                    }
                                                    oldprojectname = projname;
                                                }

                                            }
                                            i = 0;
                                        }
                                        $(this).attr('s',colorno);
                                        i++;
                                    }

                                });
                            },
                            }],
                            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                                    // if (prevClient !== aData[0]) {
                                    //     prevClient = aData[0];

                                    //     if (color === 'colorClass1') {
                                    //         color = 'colorClass2';
                                    //     } else {
                                    //         color = 'colorClass1';
                                    //     }
                                    // }

                                    // if (!($('td', nRow).hasClass('colorClass1') || $('td', nRow).hasClass('colorClass2'))) {
                                    //     if (prevClient === aData[0]) {
                                    //         $('td', nRow).addClass(color);
                                    //     }
                                    // }

                                    if (prevClient !== aData['Project']) {
                                        prevClient = aData['Project'];

                                        if (color === 'colorClass1') {
                                            color = 'colorClass2';
                                        } else {
                                            color = 'colorClass1';
                                        }
                                    }

                                    if (!($('td', nRow).hasClass('colorClass1') || $('td', nRow).hasClass('colorClass2'))) {
                                        if (prevClient === aData['Project']) {
                                            $('td', nRow).addClass(color);
                                        }
                                    }
                                },
                                "columns": [
                                    {data: 'Project', name: 'Project'},
                                    {data: 'Date', name: 'Date'},
                                    {data: 'Time', name: 'Time'},
                                    {data: 'Subject', name: 'Subject',
                                    render: function (data) {
                                    var task_str = data;
                                    var task_str_clip = task_str;
                                    if (task_str.length > 35) {
                                    task_str =
                                    '<span class="show-btn nowrap" ' +
                                    'onclick="$(this).hide();$(this).next().show();">' +
                                    task_str_clip.substr(0, 15) +
                                    '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a>' +
                                    '</span>' +
                                    '<span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).prev().show();">' +
                                    task_str + '&nbsp;&nbsp;' +
                                    '<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a>' +
                                    '</span><br/>\r\n';
                                    }
                                    return task_str;

                                    }
                                    },
                                    {data: 'Attendees', name: 'Attendees',
                                    render: function (data) {
                                    var task_str = data;
                                    var task_str_clip = task_str;
                                    if (task_str.length > 35) {
                                    task_str =
                                    '<span class="show-btn nowrap" ' +
                                    'onclick="$(this).hide();$(this).next().show();">' +
                                    task_str_clip.substr(0, 15) +
                                    '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a>' +
                                    '</span>' +
                                    '<span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).prev().show();">' +
                                    task_str + '&nbsp;&nbsp;' +
                                    '<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a>' +
                                    '</span><br/>\r\n';
                                    }
                                    return task_str;

                                }
                            },
                            {data: 'Location', name: 'Location',
                            render: function (data) {
                            var task_str = data;
                            var task_str_clip = task_str;
                            if (task_str.length > 35) {
                            task_str =
                            '<span class="show-btn nowrap" ' +
                            'onclick="$(this).hide();$(this).next().show();">' +
                            task_str_clip.substr(0, 15) +
                            '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a>' +
                            '</span>' +
                            '<span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).prev().show();">' +
                            task_str + '&nbsp;&nbsp;' +
                            '<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a>' +
                            '</span><br/>\r\n';
                            }
                            return task_str;

                            }
                            },
                            {data: 'Task', name: 'Task',
                            render: function (data) {
                            var task_str = data;
                            var task_str_clip = task_str;
                            if (task_str.length > 35) {
                            task_str =
                            '<span class="show-btn nowrap" ' +
                            'onclick="$(this).hide();$(this).next().show();">' +
                            task_str_clip.substr(0, 15) +
                            '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a>' +
                            '</span>' +
                            '<span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).prev().show();">' +
                            task_str + '&nbsp;&nbsp;' +
                            '<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a>' +
                            '</span><br/>\r\n';
                            }
                            return task_str;

                            }
                            },
                            {data: 'Assigned To', name: 'Assigned To'},
                            {data: 'Due Date', name: 'Due Date'},
                            {data: 'Status', name: 'Status'}
                            ]
                        }); //datatable ends here

                        table.on('page.dt', function() {
                            $('html, body').animate({
                                scrollTop: $("html").offset().top
                            }, 'slow');
                            });
                        table.page.len( 10 ).draw();
                    } else {
                        swal({
                            icon: 'warning',
                            title: 'Oops',
                            text: 'No reports found',
                            });
                    }


                }); //ajax done ends here
            }
        });
    </script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script> --}}
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
    </style>
@endsection
