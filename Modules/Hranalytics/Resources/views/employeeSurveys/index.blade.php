@extends('layouts.app')

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
@section('css')
    <style>
        .chartview{
            display:none
        }
        span.select2-container {
            z-index:10050;
        }
        .select2-search__field
        {
            width: 200px !important;
        }
        .active{
            background: #003A63 !important;
            color: #fff;
        }
        i{
            cursor: pointer;
        }
        .select2-selection__choice {
    color: #fff;
}

    </style>
@endsection
@section('content')
<div class="container_fluid" >

    <div class="row ">
        <div class="table_title col-md-10">
            <h4>Employee Survey</h4>
        </div>

    </div>


</div>
   <form id="filtertableform" method="post">
<div class="row tableview" style="padding-bottom:0px !important;text-align: right;align-items: center;" >
        <div class="col-md-5 col-lg-2"></div>
        <div class="col-md-1 col-lg-1">
            Start Date
        </div>
        <div class="col-md-2 col-lg-2">
            <input type="text" name="tablestartdate" id="tablestartdate"  class="form-control datepicker" value="{{date('Y-m-d', strtotime(date("Y-m-d"). ' -365 days'))}}" />
        </div>
        <div class="col-md-1  col-lg-1" style="margin-left:-10px;">
                End Date
            </div>
            <div class="col-md-2 col-lg-2">
                <input type="text" name="tableenddate" id="tableenddate"  class="form-control datepicker" value="{{date('Y-m-d', strtotime(date("Y-m-d"). ' +1 days'))}}" />
            </div>
        <div class="col-md-1 col-lg-1">
            <button class="btn btn-primary filtertable" id="filterbutton" name="filterbutton" type="button" >Search</button>
        </div>
        <div class="col-md-3" style="padding-right: 0px !important;">

            <i  id="chartview"  data-toggle="tooltip" title="Chart View" class=" fa fa-bar-chart ml-2"
            style="border-radius:5px;float:left;color: white !important;background:#F2351F;padding:10px"  aria-hidden="true"></i>

        </div>
    </div>
</form>




    <div class="row tableview " >
        <div class="col-md-12 employeesurveytablemain">
            <table class="table table-bordered employeesurveytable"
          id="employeesurveytable">
            <thead>

                    <th>#</th>
                    <th>Survey Name</th>
                    <th>Expected Responses</th>
                    <th>Responses</th>
                    <th>Actions</th>
            </thead>

            <tbody>

            </tbody>
        </table>


        </div>
        <div class="col-md-12 employeesurveytabledetailed" style="display: none">

        <table class="table table-bordered employeesurveytabledetailed"
          id="employeesurveytabledetailed" >
            <thead>

                    <th>#</th>
                    <th>Survey Name</th>
                    <th>Client Name</th>
                    <th>Survey By</th>


                    <th>Position</th>
                    <th>Email Address</th>
                    <th>Phone Number</th>
                    <th>Date </th>
                    <th>Time </th>
                    <th>Actions</th>
            </thead>

            <tbody>

            </tbody>
        </table>
        </div>
    </div>
<form id="chartsearch" >
         <div class="row chartview" >

        <div class="col-md-1 " style="display: inline;vertical-align:top;z-index:1">
            Customer
        </div>
        <div class="col-md-4" style="display: inline;vertical-align:top;;z-index:1">
                @if (\Auth::user()->hasAnypermission('view_all_clientsurvey','super_admin'))
                    {{-- <option value="0">Select all customer</option> --}}

                @else
                    {{-- <option value="-1">View all allocated customer</option> --}}
                @endif
            <select name="client[]" id="client" class="form-control" multiple>


                @if (\Auth::user()->hasAnypermission('view_all_clientsurvey','super_admin'))
                    {{-- <option value="0">Select all customer</option> --}}
                @else
                    {{-- <option value="-1">View all allocated customer</option> --}}
                @endif
                @foreach ($clients as $client)
                    <option value="{{$client->id}}">{{$client->client_name}}</option>
                @endforeach
            </select>
        </div>

        {{-- <div class="col-md-1 " style="display: inline;vertical-align:top;z-index:1">
            Area Managers
        </div>
        <div class="col-md-4 " style="display: inline;vertical-align:top;;z-index:1">
            <select name="area_manager[]" id="area_manager" class="form-control" multiple>
                @canany(['view_allocated_clientsurvey','view_all_clientsurvey'])
                    @foreach ($aremanagersarray as $key=>$areamanager)
                        <option value="{{$key}}">{{$areamanager}}</option>
                    @endforeach
                @endcanany


            </select>
        </div> --}}


        <div class="col-md-1 " style="display: inline-block;vertical-align:top;z-index:1">
            Surveys
        </div>
        <div class="col-md-4" style="display: inline-block;vertical-align:top;;z-index:1">
            <select name="surveys[]" id="surveys"
            aria-placeholder="Select a Survey"
            class="form-control" multiple>
                @if($permission=="all")
                    @foreach ($employeeSurveys as $survey)
                        <option attr-customer="{{$survey->customer_id}}" value="{{$survey->id}}">{{$survey->survey_name}}</option>
                    @endforeach
                @else
                    @foreach ($viewallocatedarray as $key=>$survey)


                    <option

                         value="{{$survey["id"]}}">{{$survey["name"]}}</option>


                    @endforeach
                @endif


            </select>
        </div>
        <div class="col-md-1 " style="display: inline;vertical-align:top;">
            <button type="button" class="btn btn-primary plotbutton form-control">Search</button>
        </div>
        <div class="col-md-1" style="right:0 !important;text-align: right">

            <i  id="tableview"  data-toggle="tooltip" title="Table View" class=" fa fa-list-ul orange"
            style="float:left;border-radius:5px;color: white !important;background:#F2351F;padding:10px"  aria-hidden="true"></i>

        </div>

    </div>
</form>
    <div class="row chartview" >
        <div class="col-md-12 chartcanvas" id="chartcanvas" style="min-height: 600px">


        </div>

    </div>


</div>



    <div class="modal fade"
    id="myModal"
    data-backdrop="static"
    tabindex="-1"
    role="dialog"
    aria-labelledby="myModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Client Survey</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>

            </div>
            {{ Form::open(array('url'=>'#','id'=>'survey_form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="row" id="client_id_div" style="padding-bottom: 10px">
                    <label for="name" class="col-md-3 control-label">Client</label>
                    <div class="col-md-6">
                        <select name="client_id" id="client_id" class="form-control">
                                <option value="0">Select Any</option>
                                @foreach ($permissionaddclients as $addclient)
                                    <option value="{{$addclient->id}}">{{$addclient->client_name}}</option>
                                @endforeach
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="row" id="client_contact_userid_div" style="padding-bottom: 10px">
                    <label for="name" class="col-md-3 control-label">Client Contact</label>
                    <div class="col-md-6" id="contact_control_block">
                        <select name="client_contact_userid" id="client_contact_userid" class="form-control">
                            <option value="0">Select Any</option>
                        </select>


                        <small class="help-block"></small>
                    </div>




                </div>

                <div class="row" id="client_contact_userid_optional_div" style="padding-bottom: 10px;display: none">
                    <label for="name" class="col-md-3 control-label">Client Contact(Other)</label>
                    <div class="col-md-6" >
                        <select name="client_contact_userid_optional" id="client_contact_userid_optional" class="form-control">
                            <option value="0">Select Any</option>
                        </select>

                        <small class="help-block"></small>
                    </div>



                </div>

                <div class="row" id="userrating_div" style="padding-bottom: 10px">
                    <label for="name" class="col-md-3 control-label">Surveys</label>
                    <div class="col-md-6">
                        <select name="userrating" id="userrating" class="form-control">
                            @foreach ($employeeratinglookup as $rating)
                                <option value="{{$rating->score}}">{{$rating->score}} - {{$rating->rating}}</option>
                            @endforeach
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="row" id="notes_div" style="padding-bottom: 10px">
                    <label for="name" class="col-md-3 control-label">Notes</label>
                    <div class="col-md-6">
                        <textarea name="notes" id="notes" class="form-control" rows="5"></textarea>
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop

@section('scripts')
    <script>
var datatabledetailedfunction = function(survey_id){
    $(".employeesurveytablemain").hide();

    $(".employeesurveytabledetailed").show();

var month = new Array();
        month[0] = "January";
        month[1] = "February";
        month[2] = "March";
        month[3] = "April";
        month[4] = "May";
        month[5] = "June";
        month[6] = "July";
        month[7] = "August";
        month[8] = "September";
        month[9] = "October";
        month[10] = "November";
        month[11] = "December";
try{

    var table = $('#employeesurveytabledetailed').DataTable({
    destroy: true,
    bprocessing: false,
    // processing: true,
    serverSide: false,
    // responsive: true,
    "autoWidth": false,
    "searching":true,
    ajax:({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route("employee.surveyDataDetailed") }}',
        type: 'POST',
        data:{startdate:$('input[name="tablestartdate"]').val(),
        enddate:$('input[name="tableenddate"]').val(),survey_id:survey_id},
    }),
    lengthMenu: [
    [10, 25, 50, 100, 500, -1],
    [10, 25, 50, 100, 500, "All"]
    ],
    columnDefs: [
        { width: "3%", targets: 0 },
        { width: "20%", targets: 1 },
        { width: "14%", targets: 2 },


    ],

    columns: [{
        data: 'DT_RowIndex',
        name: '',
        sortable:false
    },
    {
        data: null,
        name:'survey.survey_name',
        render: function (o) {

        return o.survey.survey_name;
        },
    },
    {
        data: null,
        name:"client",
        render: function (o) {

            if(o.client){
                return o.client;

            }else{
                return "General";
            }

        },

    },
    {
        data: "usercontact",
        name:"usercontact",
        // searchable:true,
        // render: function (o,type,row) {
        //     var customers ="";
        // //    var customers = o.user.first_name+"-"+o.user.last_name;
        // //    if(o.user.employee.position_id!=null){
        // //        customers+="<br/> ("+o.user.employee.employee_position.position+")";
        // //    }
        // customers = row.user.first_name+" "+row.user.last_name;
        //    if(o.user.employee){
        //     if(o.user.employee.position_id!=null){
        //        customers+="<br/> ("+o.user.employee.employee_position.position+")";
        //     }
        //    }


        //     return customers;
        // },
    },

    {
        data: null,
        orderable: false,
        render: function (o) {
           // console.log((o.user.employee.employee_position)["position"]);
           if(o.user.employee.employee_position){
               return (o.user.employee.employee_position)["position"];
           }else{
               return "";
           }

        },
    },
    {
        data: "user.employee.employee_work_email",
        name:"user.employee.employee_work_email",

    },
    {
        data: "user.employee.phone",
        name:"user.employee.phone",

    },
    {
        data: null,
        orderable: false,
        render: function (o) {

        var dt = new Date((o.created_at));



            return dt.getDate()+" "+month[dt.getMonth()]+" "+dt.getUTCFullYear();
        },
    },
    {
        data: null,
        orderable: false,
        render: function (o) {
            var dt = new Date((o.created_at));
            return dt.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })
        },
    },
      {
        data: null,
        sortable: false,
        render: function (o) {
            var actions = '';
            var url = '{{ route("employeeSurvey.view",'') }}';
            actions += '<a target="_blank" href="'+url+"/"+ o.id +'" title="View" class="fa btn fa-eye"></a>'
            return actions;
        },
    }

    ]
    });
    } catch(e){
    console.log(e.stack);
    }
}
        var datatablefunction = function(){

            var month = new Array();
                    month[0] = "January";
                    month[1] = "February";
                    month[2] = "March";
                    month[3] = "April";
                    month[4] = "May";
                    month[5] = "June";
                    month[6] = "July";
                    month[7] = "August";
                    month[8] = "September";
                    month[9] = "October";
                    month[10] = "November";
                    month[11] = "December";
            try{

                var table = $('#employeesurveytable').DataTable({
                destroy: true,
                bprocessing: false,
                // processing: true,
                serverSide: false,
                // responsive: true,
                "autoWidth": false,
                "searching":true,
                ajax:({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("employee.surveydata") }}',
                    type: 'POST',
                    data:{startdate:$('input[name="tablestartdate"]').val(),
                    enddate:$('input[name="tableenddate"]').val(),},
                }),
                lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
                ],
                columnDefs: [
                    { width: "3%", targets: 0 },
                    { width: "78%", targets: 1 },
                    { width: "12%", targets: 2 },
                    { width: "7%", targets: 3 },


                ],

                columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false
                },
                {
                    data: "surveyName",
                    name:"surveyName",
                    // render: function (o) {
                    //     //console.log(o)
                    //     //debugger
                    //     return survey_name;
                    // },
                },
                {
                    data: "expectedResponse",
                    name:"expectedResponse"

                },
                {
                    data: "Responses",
                    name:"Responses",
                    // render: function (o) {

                    //     return Object.keys(o).length-1

                    // },

                },
                  {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        var url = '{{ route("employeeSurvey.view",'') }}';
                        actions += '<a attr-id="'+o.id+'"  title="View" class="fa btn fa-eye detailedview"></a>'
                        return actions;
                    },
                }

                ]
                });
                } catch(e){
                console.log(e.stack);
                }
        }
        $(document).ready(function (e) {

            $('#employeesurveytable').show();
            $("#surveys").select2({
                placeholder: "Select a Survey"
            });
            $.fn.dataTable.ext.errMode = 'throw';
            datatablefunction()
            });
            $(document).on("click",".filtertable",function(e){
                e.preventDefault();
                $('.employeesurveytablemain').show();
                $('.employeesurveytabledetailed').hide();

                if($('input[name="tablestartdate"]').val()=="" &&
                $('input[name="tableenddate"]').val()==""){
                    swal("Warning","Start date and End date cannot be empty");
                }else if($('input[name="tablestartdate"]').val()>$('input[name="tableenddate"]').val()){
                    swal("Warning","Start date cannot be greater than end date");
                }else{
                    datatablefunction()
                }

            })
            $(document).on("click","#addnewsurvey",function(e){
                $("#clientcontactorother").prop("checked",false)
                $("#client_contact_userid_optional").val("0")
                $("#client_id").val("0");
                $("#client_contact_userid_optional_div").hide();
                $("#survey_form")[0].reset();

                $("#myModal").modal();

                $('select[name="client_id"]').select2({
                    width: '100%',
                    dropdownParent: $("#myModal")
                })

            })



            $(document).on("change","#clientcontactorother",function(e){
                let checkedstate = ($(this).is(":checked"));
                if(checkedstate==true){
                    if($("#client_id").val()==0){
                        swal("Warning","Please select a client","warning");
                        $(this).prop("checked",false)
                    }else{

                        $("#client_contact_userid_optional_div").show()
                        // $('#myModal select[name="client_contact_userid"]').css("disabled",true)
                        document.getElementById("client_contact_userid").disabled = true;
                    }

                }else{
                    $("#client_contact_userid_optional").val("0")
                    $("#client_contact_userid").css("disabled",false)
                    document.getElementById("client_contact_userid").disabled = false;
                    $("#client_contact_userid_optional_div").hide()
                }
            })

            $(document).on("click","#mdl_save_change",function(e){
                e.preventDefault();

                if($("#clientcontactorother").is(":checked")===true &&
                 $('select[name="client_contact_userid_optional"]').val()=="0"){
                    swal("Warning","Other client contact cannot be empty","warning");
                 }else if($("#notes").val()==""){
                    swal("Warning","Notes cannot be empty","warning");
                 }
                 else if($("#client_contact_userid").val()<1){
                    swal("Warning","Client contact cannot be empty","warning");
                 }
                     else{
                    var formData = new FormData($('#survey_form')[0]);
                var client_contact_userid = $("#client_contact_userid").val();
                 formData.append("client_contact_userid",client_contact_userid)
                $.ajax({
                    type: "post",
                    url: '{{ route("clientsurvey.saveclientuserdata") }}',
                    data: formData,
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData : false,
                    contentType: false,
                    success: function (response) {
                        var data = jQuery.parseJSON(response);
                        if(data.code==200){
                            swal({
                            title: "Success",
                            text: data.message,
                            type: "success"
                            },
                            function(){
                                swal.close();
                                var table = $('#employeesurveytable').DataTable();
                                table.ajax.reload();
                                $("#myModal").modal("hide")
                            });
                        }
                    }
                }).fail(function(response){

                });
                 }

            })


            $(document).on("click","#tableview",function(e){
                e.preventDefault();
                $(".chartview").hide();
                $(".tableview").show();
                $("#chartview").show();
                $("#tableview").hide();
                $("#filtertableform").show();
                $(".tableview").css("display","flex");
            })

            $(document).on("click","#chartview",function(e){
                e.preventDefault();
                $(".tableview").hide();
                $(".chartview").show();
                $("#chartview").hide();
                $("#tableview").show();
                $("#filtertableform").hide();
                $(".chartview").css("display","flex");
            })

            $(document).ready(function () {
                $("#client").select2()
                $("#tableview").hide();

            });

            $(document).on("select2:select","#client",function(e){
                e.preventDefault();
                let clientid = $(this).val();
            });

            $(document).on("select2:select","#surveys",function(e){
                $("#chartcanvas").html("")
            });

            $(document).on("select2:unselect","#surveys",function(e){
                $("#chartcanvas").html("")
            });

            $(document).on("click",".plotbutton",function(e){
                e.preventDefault()
                var formData = new FormData($('#chartsearch')[0]);
                let clientid = $("#client").val();
                let clientname = $("#client").select2("data");
                let surveyid = $("#surveys").val();

                if(surveyid.length>0){
                    $.ajax({
                    type: "post",
                    url: '{{ route("employeesurvey.surveyblock") }}',
                    data: formData,
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData : false,
                    contentType: false,
                    success: function (response) {
                        $("#chartcanvas").html(response)

                    }}).done(function(response){
                       $( "#tab_1" ).trigger("click");
                    })
                }
            });




            $(document).on("change","#userratingcondition",function(e){
                if($(this).val()!="0"){
                    if($("#graphtype").val()=="average"){
                        // swal("Warning","Graph type cannot be average","warning");
                        // $("#graphtype").val("individual")
                    }
                }
            })



            $('#client').on('select2:select', function (e) {
                let customeralloc = {!! json_encode($templatecustomerarray) !!};
                binddropdown(customeralloc,$(this).val())
            });
            $('#client').on('select2:unselect', function (e) {
                let customeralloc = {!! json_encode($templatecustomerarray) !!};
                binddropdown(customeralloc,$(this).val())
            });

            var binddropdown=function(customeralloc,selectedval){
                $("#chartcanvas").html("");
                let selecthtml="";
                if(selectedval.length<1){

                   Object.values(customeralloc).forEach(function(element,key) {

                       let customerallocelement = element;
                       if(customerallocelement.length>0){
                            customerallocelement.forEach(allocelement => {
                                selecthtml+=`<option value="${allocelement["id"]}">${allocelement["name"]}</option>`
                           });
                       }
                       //debugger
                       // selecthtml+=`<option id="${customerallocelement["id"]}">${element["name"]}</option>`
                    });
                }else{
                    let checkarray = [];
                    selectedval.forEach(element => {
                        if(customeralloc[element]){
                            (customeralloc[element]).forEach(element => {
                                if(checkarray.indexOf(element["id"]) == -1)
                                {
                                    selecthtml+=`<option value="${element["id"]}">${element["name"]}</option>`
                                    checkarray.push(element["id"])
                                }

                            });
                        }
                    });
                }
                $("#surveys").html(selecthtml).select2()
            }

            $(document).on("click",".templatetab",function(e){
                let templateid = $(this).attr("attr-templateid");
                let clientid = $("#client").val();
                let self = this;
               // $(".tab-content").hide();
               $(".tab-pane").hide();
                $(".tab-pane").addClass("fade");
                $(".templatetab").removeClass("active");
                let colorarray = ["#fd7e14","#0E3A63","#343a40",
                "#dc3545","#007bff","#6610f2","#ffc107","#20c997"]
                $.ajax({
                    type: "post",
                    url: '{{ route("employeesurvey.plotgraph") }}',
                    data: {templateid:templateid,clientid:clientid},
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        let data = jQuery.parseJSON(response);
                        let graphdata = data[0];
                        let lookupdata = data[1];
                        let graphx = [];
                        Object.keys(lookupdata).forEach(element => {
                            graphx.push(lookupdata[element]);
                        });

                        Object.keys(graphdata).forEach(function(key) {
                            let blockdata = graphdata[key];
                            let canvas = "canvaschartblock_"+key;
                            // $("#chartblock_"+key).html(`<canvas id="${canvas}"
                            // style="width: 100%; height: auto;"></canvas>`)
                            if(blockdata["answer_type"]==1){


                                let valid = 0;
                                let pldata=[];
                                Object.keys(blockdata["data"]).forEach(key => {
                                    let obj={
                                        name: key,
                                        y: blockdata["data"][key].length,
                                        sliced: false,
                                        selected: true
                                    };
                                    valid=valid+blockdata["data"][key].length
                                    pldata.push(obj);
                                });

                                if(valid>0){
                                Highcharts.chart("chartblock_"+key, {
                                chart: {
                                    plotBackgroundColor: null,
                                    plotBorderWidth: null,
                                    plotShadow: false,
                                    type: 'pie',
                                    height:310
                                },
                                credits: {
                                    enabled: false
                                },
                                title: {
                                    text: ''
                                },
                                tooltip: {
                                    pointFormat: ' <b>Count : {point.y}</b>'
                                },

                                accessibility: {

                                    point: {
                                        valueSuffix: ''
                                    }
                                },
                                exporting: {
                                    buttons: {
                                    contextButton: {
                                        menuItems: ['downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG'],
                                    },
                                    },
                                },
                                legend: {
   layout: 'vertical',
   align: 'right',
   verticalAlign: 'middle',
   itemMarginTop: 10,
   itemMarginBottom: 10
 },

                                plotOptions: {
                                    pie: {
                                        allowPointSelect: true,
                                        cursor: 'pointer',
                                        colors: ["#003A63","#f26321"],
                                        dataLabels: {
                                            enabled: false
                                        },
                                        showInLegend: true

                                    }
                                },
                                series: [{
                                    name: '',
                                    colorByPoint: true,
                                    data:pldata
                                }]
                            });
                                }else{
                                    $("#chartblock_"+key).html("No Submission against template")
                                }

                            }else if(blockdata["answer_type"]==2){
                                let prearray = [];
                                let pldata=blockdata["data"];
                                if(blockdata["data"]){


                                let i=0;
                                Object.keys(lookupdata).forEach(key=>{
                                    let index = key;
                                    let text = (lookupdata[key]);
                                    let indexvalue = pldata[index]
                                    let objval={
                                        name:text,
                                        data:[indexvalue],
                                        color:colorarray[i]
                                    }
                                    //console.log(indexvalue)
                                    prearray.push(objval)
                                    //prearray.push(indexvalue);
                                    i++;
                                })

                                //debugger



                                Highcharts.chart("chartblock_"+key, {
                                chart: {
                                    type: 'column',
                                    height:310
                                },
                                title: {
                                    text: ''
                                },
                                credits: {
                                    enabled: false
                                },
                                subtitle: {
                                    text: ''
                                },
                                exporting: {
                                    buttons: {
                                    contextButton: {
                                        menuItems: ['downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG'],
                                    },
                                    },
                                },
                                legend: {
   layout: 'vertical',
   align: 'right',
   verticalAlign: 'middle',
   itemMarginTop: 10,
   itemMarginBottom: 10
 },
                                xAxis: {
                                    categories: [blockdata["question"]],
                                    // crosshair: true,
                                    // title:{
                                    //     text:"Categories"
                                    // }
                                },
                                yAxis: {
                                    min: 0,
                                    title: {
                                        text: 'Survey (count)'
                                    }
                                },
                                tooltip: {
                                    // headerFormat: '<span style="font-size:14px;font-weight:bold">{point.key}</span><table>',
                                    // pointFormat: '<tr><td style="color:{series.color};padding:0;font-size:12px">{series.name}: </td>' +
                                    //     '<td style="padding:0"><b>{point.y} </b></td></tr>',
                                    // footerFormat: '</table>',
                                    formatter: function () {
                                        return this.series.name+" : "+this.point.y
                                    }
                                },
                                plotOptions: {
                                    column: {
                                        pointPadding: 0.2,
                                        borderWidth: 0
                                    }
                                },
                                series: prearray
                            });

                        }else{
                            $("#chartblock_"+key).html("No Submission against template")
                        }
                            }


                        });
                        $("#template_"+templateid).removeClass("fade");
                        $("#template_"+templateid).show()
                        $(self).addClass("active")

                    }
                });
            })
            $(document).on('focusout', '.select2-search__field', function (e) {
                $("body").css("overflow-x","auto")
            });
            $(document).on('focus', '.select2-search__field', function (e) {
                $("body").css("overflow-x","hidden")
            });
            $(window).scroll(function() {
                $("#client").select2();
                $("#surveys").select2({
                    placeholder: "Select a Survey"
                });

});
$(document).on("click",".detailedview",function(e){
                    e.preventDefault();
                    let survey_id=$(this).attr("attr-id")
                    datatabledetailedfunction(survey_id);
                })
    </script>

@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
