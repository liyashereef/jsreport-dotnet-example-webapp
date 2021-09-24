@extends('layouts.app') @section('content')
@section('css')
    <style>
        body{
            overflow-x:hidden;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #f26321;
            color: white;
        }
        .greenbg{
            background: #343F4E;
            color: #343F4E !important;
        }
        .redbg{
            background:red;
            color: red !important;
        }
        .yellowbg{
            background:yellow;
            color: red !important;
        }
        .theadclass{
            color:#fff !important;
        }
        .tbodyclass{
            width:150px !important;
            color:#000 !important;
            border:solid 1px #000 !important;
        }
        .dataTables_wrapper .dataTables_filter {
            float: right;
            text-align: right;
            margin-right: 15px;
        }

        .rotatespan{
            text-align: right;
            writing-mode:tb-rl;
            transform: rotate(-180deg);

        }
        .reportrow{
            width:100%;
            height: 200px !important;
        }

        .cellheight{
            height: 38px;
        }
        th, td {

            white-space: nowrap
             }

             .namespan{
                /* white-space: initial !important;*/
                cursor: pointer;
                max-width: 200px;
                overflow: hidden;

             }



             .customerclass{
    border:solid 1px #000 !important;
}
.table-bordered td, .table-bordered th{
    border: solid 1px #000 !important;
}

        div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }


    .mainheademployeecell{
    }

    .loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}


/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

    </style>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.3.0/css/fixedColumns.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" />


@endsection
@section('content')
<div id="areamanagertooltip"
style="position: fixed;display: none;z-index:1000;width:300px;min-height:100px;word-wrap: break-word
;overflow-x:hidden;overflow-y:auto;background:#003A63;color:#fff;border-radius:5px;padding:10px"></div>
<div class="container-fluid" style="padding-left: 0px !important">
    <div class="row  h-10">
        <div class="col-md-12"><div class="table_title position-static">
            <h4>Client Schedule </h4>
        </div></div>

    </div>

    <div class="row beforeprojectselect filterbox  h-10" style="display: none">
        <div class="col-md-1">Year</div>
        <div class="col-md-4">
            <select id="year" multiple="multiple">

                @for ($i = $yearbegin;$i < $yearend;$i++)
                <option value="{{$i}}">{{$i}}</option>
                @endfor

            </select>
            <input type="hidden" name="togglestate" id="togglestate" value="1" />
        </div>

        <div class="col-md-1">Pay Period </div>
        <div class="col-md-4" style="height:50px;overflow-y:auto">

            <select id="payperiod" multiple="multiple">

            </select>
        </div>
        <div class="col-md-1">

        </div>


    </div>
    <div id="ablock" class="row beforeprojectselect filterbox  h-10" style="display: none">
        <div class="col-md-1">Area Manager</div>
        <div class="col-md-4">
            <select id="areamanagerselect" multiple="multiple">



            </select>
        </div>
        <div class="col-md-1">Supervisor</div>
        <div class="col-md-4">
            <select id="supervisorselect" multiple="multiple">


            </select>
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary" id="viewreport">View</button>
        </div>


    </div>
    <div class="row h-10">
        <div class="col-md-12" style="height: 50px !important"></div>
    </div>
    <div class="row" >
        <div class="col-md-5"></div>
        <div class="col-md-1 ">
            <div id="preloader" class="loader" style="display: none"></div>
        </div>
    </div>
    <div class="row reportrow" >

    </div>


</div>




@stop

@section('scripts')
    <script type="text/javascript">

        var payperiodsinayear = function(selectedyear){
            $.ajax({
                type: "get",
                url: "{{route('scheduling.payperiodsyearwise')}}",
                data: {"selectedyear":selectedyear},
                success: function (response) {
                    payperiodsarray = JSON.parse(response);
                    payperiods = payperiodsarray[0];
                    supervisors = payperiodsarray[1];
                    areamanagers = payperiodsarray[2];
                    var options = "";
                    payperiods.forEach(element => {
                        pay_period_id = element["id"];
                        pay_period_name = element["pay_period_name"];
                        pay_period_short_name = element["short_name"];
                        options+="<option value='"+pay_period_id+"'>"+pay_period_name+" ("+pay_period_short_name+")</option>";
                    });
                    $("#payperiod").html(options).after(function(e){
                        $("#payperiod").select2();
                        //$("#viewreport").trigger("click");
                    });
                    var supervisoroptions = "";
                    supervisors.forEach(element => {
                        supervisorid = element[0];
                        supervisorname = element[1];
                        supervisoroptions+="<option value='"+supervisorid+"'>"+supervisorname+"</option>";
                    });
                    $("#supervisorselect").html(supervisoroptions).after(function(e){
                        $("#supervisorselect").select2();

                    });

                    var areamanageroptions = "";
                    areamanagers.forEach(element => {
                        areamanagerid = element[0];
                        areamanagername = element[1];
                        areamanageroptions+="<option value='"+areamanagerid+"'>"+areamanagername+"</option>";
                    });
                    $("#areamanagerselect").html(areamanageroptions).after(function(e){
                        $("#areamanagerselect").select2();
                        $("#viewreport").trigger("click");
                    });

                }
            });

        }
        $(function(e){

            //var currentyear =JSON.parse("{{ json_encode($currentyear) }}");
            //alert(currentyear);
            var d = new Date();
            var currentyear = d.getFullYear();
            $("#year").val(currentyear).select2().after(function(e){
                $(".filterbox").show();
            });
            //$("#year").val(currentyear)
            payperiodsinayear($("#year").val());

        })

$("#viewreport").on("click",function(e){
    e.preventDefault();
    var dis = ($('#footer').offset().top - $('#ablock').offset().top)-340;

    if($("#year").val()!=""){
        $("#contenttable").hide();
        $("#preloader").show();
        var areamanager = $("#areamanagerselect").val();
        var supervisor = $("#supervisorselect").val();
        $.ajax({
        type: "post",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{route("scheduling.schedulepayperiodreportresults")}}',
        data: {"year":$("#year").val(),"payperiod":$("#payperiod").val(),"areamanager":areamanager,"supervisor":supervisor},
        success: function (response) {
            $(".reportrow").html(response).after(function(e){
                $.ajax({
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{route("scheduling.schedulepayperiodreportstatus")}}',
                    data: {"year":$("#year").val(),"payperiod":$("#payperiod").val()},
                    success: function (response) {
                        var returnarray = JSON.parse(response);


                        $.each(returnarray, function (index, value) {
                            try {
                                $("#"+index).removeClass('greenbg');
                                $("#"+index).removeClass('redbg');
                                $("#"+index).removeClass('yellowbg');
                            } catch (error) {
                                console.log("Error"+error);
                            }
                            if(value=="0"){
                                $("."+index).html("P");
                                $("#"+index).css('cssText', 'background:yellow;color: yellow !important;border:solid 1px #000 !important');
                            }else if(value=="1"){
                                $("."+index).html("A");
                                $("#"+index).css('cssText', 'background:#343F4E;color: #343F4E !important;border:solid 1px #000 !important');
                            }
                            else if(value=="2"){
                               // $("."+index).css("color","red");
                                $("."+index).html("R");
                                $("#"+index).css('cssText', 'background:red;color: red !important;border:solid 1px #000 !important');
                            }
                            $("."+index).css("text-align","center");

                        });
                        var screenheight = screen.height;
                        var screenwidth = screen.width;
                        if(screenheight>=1080){

                        }else{
                            screenheight = 700;
                        }
                        if(screenwidth>=1920){
                            //$("#contenttable").css("width",(screenwidth*.90)+"px");
                        }else{
                            //$("#contenttable").css("width",(screenwidth*.90)+"px");
                        }
                        //$("#namediv").css("max-height",(screenheight*.4)+"px");
                        var motherdivheight = $(".reportrow").height();
                        $("#genreport").DataTable({
                            autoWidth: false,
                            dom: 'Bfrtip',
                            buttons: [
                                {
                                    extend: 'excelHtml5',
                                    title: 'Scheduling Payperiodwise'
                                }
                            ],
                            "bLengthChange" : false, //thought this line could hide the LengthMenu
                            "bInfo":false,
                            scrollY: dis+"px",
                            scrollX: true,
                            scrollCollapse: false,
                            paging: false ,
                            ordering:false,
                            columnDefs: [
                                { targets: [0,1,2], width: '10%' }
                            ],
                            fixedColumns: {
                            leftColumns: 4
                            // heightMatch: 'auto' // did not help with alignment issue
                            },
                            "initComplete": function(settings, json) {
                                $("#contenttable").css("display","block");
                                $(".customerclass").remove('white-space');
                                $(".namespan").on("mouseover",function(e){
                                    var innerhtml = ($(this).html());
                                    var title = $(this).attr("title");
                                    var mousex = e.pageX;
                                    var mousey = e.pageY;
                                    if(innerhtml!=""){
                                        var pagetitle = "";
                                        if(title=="areamanager"){
                                            pagetitle="Area Manager";
                                        }else if(title=="supervisor"){
                                            pagetitle="Supervisor"
                                        }
                                        $("#areamanagertooltip").html("<p><b>"+pagetitle+"</b></p>"+innerhtml.replace(/,/g,"<br/>"));
                                        $("#areamanagertooltip").css({top: mousey-100, left:mousex+100 });
                                        $("#areamanagertooltip").show()
                                    }

                                })

                                $(".namespan").on("mouseout",function(e){

                                    $("#areamanagertooltip").hide();
                                })
                                }

                            });

                            $("#contenttable").show();
                            $("#preloader").hide();



                    }
                });
            });
        }
    });
    }else{
        swal("Alert", "Year cannot be empty", "warning");
    }

})

    $("#sidebarCollapse").on('click',function(){
        var togglestate = $("#togglestate").val();
        if(togglestate=="1"){
            $(".reportrow").css("width","90%");
            $("#togglestate").val("0");
        }else{
            $(".reportrow").css("width","100%");
            $("#togglestate").val("1");
        }
    })


    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>

@endsection
