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
.redClass{
    background: red;
}

    </style>
@endsection
@section('content')
<div class="container_fluid" >

    <div class="row ">
        <div class="table_title col-md-10">
            <h4>Employee Feedback</h4>
        </div>

    </div>


</div>
   <form id="filtertableform" method="post">
<div class="row tableview" style="padding-bottom:0px !important;text-align: right;align-items: center;display: none" >
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


        </div>
    </div>
</form>




    <div class="row tableview " >
        <div class="col-md-12 employeefeedbacktablemain">
            <table class="table table-bordered employeefeedbacktable"
          id="employeefeedbacktable">
            <thead>

                <th># </th>
                <th>Date </th>
                <th>Created By</th>
                    <th>Customer</th>
                    <th>Address To</th>
                    <th>Subject</th>
                    <th>Note</th>
                    <th>Rating</th>
                    <th>Status</th>
                    {{-- <th>Updated By</th> --}}
                    {{-- <th>Notes</th>
                    <th>Updated On</th> --}}
                    <th>Location</th>
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


{{-- location Modal --}}
<div class="modal fade" id="modalContent" data-backdrop="static" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div id="modal-content" style="height: 500px;" class="modal-body">
            </div>
            <div align="center"  style="display: none;"  id="modal-img-content" style="height: 550px;" class="modal-body">
                <div style="text-align: center;" >
                    <img  style="left: 50%;max-width: 600px;"  height="400px" id="ImgContainer" src="">
                </div>

            </div>

        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}"></script>

    <script>
var datatabledetailedfunction = function(survey_id){
    $(".employeefeedbacktablemain").hide();

    $(".employeefeedbacktabledetailed").show();

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

    var table = $('#employeefeedbacktabledetailed').DataTable({
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
        url: '{{ route("employee.feedbackdata") }}',
        type: 'POST',
        data:{startdate:$('input[name="tablestartdate"]').val()},
    }),
    lengthMenu: [
    [10, 25, 50, 100, 500, -1],
    [10, 25, 50, 100, 500, "All"]
    ],
    columnDefs: [
        { width: "1%", targets: 0 },
        { width: "3%", targets: 1 },
        { width: "7%", targets: 2 },
        { width: "7%", targets: 3 },
        { width: "7%", targets: 8, className: 'dt-body-right' },
        { width: "7%", targets: 9, className: 'dt-body-right' },


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
            var url="";
            actions += '<a   id="location" onclick="showlocation(' + o.latitude + ',' + o.longitude + ');" title="View" class="fa btn fa-eye"></a>'
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

                var table = $('#employeefeedbacktable').DataTable({
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
                    url: '{{ route("employee.feedbackdata") }}',
                    type: 'POST',
                    data:{startdate:$('input[name="tablestartdate"]').val(),
                    enddate:$('input[name="tableenddate"]').val(),},
                }),
                'rowCallback': function(row, data, index){
                        let bckcolor="green";
                         let fontcolor="white";
                         let className=""; 
                         console.log(data.userstatus.status)
                    // debugger
                    if(data.userstatus.status==3){
                          bckcolor="#ff9999";
                          fontcolor="white";  
                          className="closed";                 
                    }else if(data.userstatus.status==2){
                            bckcolor="#ffe690";
                          fontcolor="black"; 
                          className="in_progress";                 
                  
                    }
                    else if(data.userstatus.status==1){
                        bckcolor="rgba(36, 169, 66, 0.62)";
                          fontcolor="white";
                          className="open";                 
                  
                    }
                    fontcolor="#003A63";
                    $(row).addClass(className);
                    // $(row).find('td:eq(1)').css("cssText",'background:'+bckcolor+';color:'+fontcolor);
                    // $(row).find('td:eq(2)').css("cssText",'background:'+bckcolor+';color:'+fontcolor);
                    // $(row).find('td:eq(3)').css("cssText",'background:'+bckcolor+';color:'+fontcolor);
                    // $(row).find('td:eq(4)').css("cssText",'background:'+bckcolor+';color:'+fontcolor);
                    // $(row).find('td:eq(5)').css("cssText",'background:'+bckcolor+';color:'+fontcolor);
                    // $(row).find('td:eq(6)').css("cssText",'background:'+bckcolor+';color:'+fontcolor);
                    // $(row).find('td:eq(7)').css("cssText",'background:'+bckcolor+';color:'+fontcolor);
                    // $(row).find('td:eq(8)').css("cssText",'background:'+bckcolor+';color:'+fontcolor);
                },
                lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
                ],
                columnDefs: [
                    { width: "2%", targets: 0 },
                    { width: "10%", targets: 1 },
                    { width: "7%", targets: 2 },
                    { width: "17%", targets: 3 },


                ],

                columns: [
                {
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false
                },
                {
                    data: null,
                    name:"created_at",
                    render: function (o) {
                        //console.log(o)
                        //debugger
                        return moment(o.created_at).format('MMMM D, YYYY');
                        // return new Date(o.created_at).toLocaleDateString();
                    },
                },
                {
                    data: null,
                    name:"created_by",
                    render:function(o){
                        return o.create_user.first_name +  " " + o.create_user.last_name;
                    }

                },
                {
                    data: null,
                    name:"customer_id",
                    render:function(o){
                        return o.customer.project_number +  " - " + o.customer.client_name;
                    }

                },
                  {
                    data: null,
                    name:"department_id",
                    render:function(o){
                        return o.department.name
                    }
                },
                  {
                    data: "subject",
                    name:"subject",
                },
                  {
                    data: "message",
                    name:"message",
                },
                  {
                    data: null,
                    name:"rating_id",
                    render:function(o){
                        let status="";

                        if(o.employee_rating!=null){
                            return o.employee_rating.rating
                        }else{
                            return null
                        }
                    }
                },
                  {
                    data: null,
                    name:"userstatus",
                    render:function(o){
                        let status="";
                        
                            status=o.userstatus.name
                        return status
                    }
                },
                // {
                //     data: null,
                //     name:"updated_by",
                //     render:function(o){
                //         let updated_by="";
                //         if(o.approvalfeedback){
                //             for (var k in o.approvalfeedback) {
                //                 // console.log(o.approvalfeedback[k])
                //                 // debugger
                //                 let feedBack=(o.approvalfeedback[k])
                //                 let name=feedBack.create_user.first_name;
                //                 if(feedBack.create_user.last_name!=null){
                //                     name+=" "+feedBack.create_user.last_name;
                //                 }
                //                 if(updated_by==""){
                //                     updated_by+="<b>"+name+"</b><br/>"

                //                 }else{
                //                      updated_by+=name+"<br/>"

                //                 }
                //             }
                //         }else{
                //             updated_by=o.userstatus.name
                //         }
                //         return updated_by
                //     }

                // },
                //   
                // ,
                // {
                //     data: null,
                //     name:"updated_at",
                //     render: function (o) {
                //         //console.log(o)
                //         //debugger
                //         return moment(o.updated_at).format('MMMM D, YYYY');
                //         // return new Date(o.created_at).toLocaleDateString();
                //     }},
                  {
                    data: null,
                    name:"location",
                    render:function(o){
                        if(o.latitude!=null){
                            var imgUrl = '{{ URL::asset('images/map_pointer.png') }}';
                        return '<a style="cursor:pointer"   id="location" onclick="showlocation(' + o.latitude + ',' + o.longitude + ');" ><img  width="40px" src="'+imgUrl+'"></a>';
                        }
                        return "";
                    }
                },
                  {
                    data: null,
                    name:"actions",
                    render:function(o){
                        var actions = '';
                        @canany(["super_admin","update_employee_feedback_status"])
                        actions = '<a  href="viewDetailed/' + o.id + '" class="edit fa fa-edit" data-id=' + o.id + '></a>';
                        @endcanany

                        return actions;
                    }
                }

                ]
                });
                } catch(e){
                console.log(e.stack);
                }
        }
        $(document).ready(function (e) {

            $('#employeefeedbacktable').show();
            $("#surveys").select2({
                placeholder: "Select a Survey"
            });
            $.fn.dataTable.ext.errMode = 'throw';
            datatablefunction()
            });
            $(document).on("click",".filtertable",function(e){
                e.preventDefault();
                $('.employeefeedbacktablemain').show();
                $('.employeefeedbacktabledetailed').hide();

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
                                var table = $('#employeefeedbacktable').DataTable();
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
                    url: '',
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
                let customeralloc = null;
                binddropdown(customeralloc,$(this).val())
            });
            $('#client').on('select2:unselect', function (e) {
                let customeralloc = null;
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
                    url: '',
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

                function showlocation(lat,long){
        $('#modalContent').modal('show');
        $('#modal-content').show();
        $('#modal-img-content').hide();
        $('#modalLabel').text('Location');

        var radius = 0;
        $('#modalContent').on('shown.bs.modal', function (e) {
            initialize(new google.maps.LatLng(lat, long), radius);
        });
    }


    function initialize(myCenter, radius) {

var renderContainer = document.getElementById("modal-content");
var mapProp = {center: myCenter, zoom: 8};
{!!\App\Services\HelperService::googleAPILog('map','Modules\Hranalytics\Resources\views\whistleblower\partials\scripts')!!}
var map = new google.maps.Map(renderContainer, mapProp,{
    gestureHandling  : 'greedy',
});

//Marker in the Map
var marker = new google.maps.Marker({
    position: myCenter,
    draggable: true,
    //animation: google.maps.Animation.DROP,
});
marker.setMap(map);

//Circle in the Map
var circle = new google.maps.Circle({
    center: myCenter,
    map: map,
    radius: radius, // IN METERS.
    fillColor: '#FF6600',
    fillOpacity: 0.3,
    strokeColor: "#FFF",
    strokeWeight: 1,
    //draggable: true,
    editable: true
});
circle.setMap(map);

//Add listner to change latlong value on dragging the marker
marker.addListener('dragend', function (event)
{
    $('#lat').val(event.latLng.lat());
    $('#long').val(event.latLng.lng());
});

//Add event listner on drag event of marker
marker.addListener('drag', function (event) {
    circle.setOptions({center: {lat: event.latLng.lat(), lng: event.latLng.lng()}});
});

//Add listner to change radius value on field
circle.addListener('radius_changed', function () {
    $('#radius').val(circle.getRadius());
});

//Add event listner on drag event of circle
circle.addListener('drag', function (event) {
    marker.setOptions({position: {lat: event.latLng.lat(), lng: event.latLng.lng()}});
});

//changing the radius of circle on changing the numeric field value
$("#radius").on("change paste keyup keydown", function () {
    //radius = $("#radius").val();
    circle.setRadius(Number($("#radius").val()));

});
}
    </script>

@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
