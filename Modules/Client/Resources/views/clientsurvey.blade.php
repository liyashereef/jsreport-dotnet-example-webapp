@extends('layouts.app')
@section('css')
    <style>
        .chartview{
            display:none
        }
        span.select2-container {
    z-index:10050;
}

    </style>
@endsection
@section('content')
<div class="container_fluid">

 
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-sm-7  table_title" style="text-align: left">
            <h4 style="margin: 0">Client Survey</h4>
        </div>
        <div class="col-sm-5 table_title" style="text-align: right">
            
            @canany(['view_allocated_clientsurvey','view_all_clientsurvey'])
                            <button id="addnewsurvey" type="button" class="btn btn-primary " style="">Add New Survey</button>
            @endcanany


        </div>
    </div>
    <form id="filtertableform" method="post">
        <div class="row tableview" >
        <div class="col-md-2"></div>
        <div class="col-md-1">Start Date</div>
        <div class="col-md-2">
            <input type="text" name="tablestartdate" id="tablestartdate"
                class="datepicker" value="{{date('Y-m-d', strtotime(date("Y-m-d"). ' -365 days'))}}" />
        </div>
        <div class="col-md-1">End Date</div>
        <div class="col-md-2">
            <input type="text" name="tableenddate" id="tableenddate"
                class="datepicker" value="{{date('Y-m-d', strtotime(date("Y-m-d"). ' +1 days'))}}" />
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary filtertable">Search</button>
        </div>
        <div class="col-md-3" style="padding-right: 0px !important;">
            
            <i  id="chartview"  data-toggle="tooltip" title="Chart View" class=" fa fa-bar-chart ml-2" 
            style="border-radius:5px;float:left;color: white !important;background:#F2351F;padding:10px"  aria-hidden="true"></i>

        </div>
    </div>
    </form>


    <div class="row tableview" >
        <div class="col-sm-12">
            <table class="table table-bordered clientsurveytable"
          id="clientsurveytable">
            <thead>

                    <th>#</th>
                    <th>Client Name</th>
                    <th>Client Contact</th>
                    <th>Phone Number</th>
                    <th>Rating</th>
                    <th>Notes</th>
                    <th>Reviewed By</th>
                    <th>Date & Time</th>

            </thead>

            <tbody>

            </tbody>
        </table>
        </div>
    </div>
    <form id="chartsearch">
         <div class="row chartview" style="">

        <div class="col-sm-1 " style="display: inline-table;vertical-align:top;z-index:1">
            Customer
        </div>
        <div class="col-sm-6" style="display: inline-table;vertical-align:top;;z-index:1">
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


        <div class="col-sm-1 " style="display: inline-table;vertical-align:top;z-index:1">
            Area Managers
        </div>
        <div class="col-sm-4 " style="display: inline-table;vertical-align:top;;z-index:1">
            <select name="area_manager[]" id="area_manager" class="form-control" multiple>
                @canany(['view_allocated_clientsurvey','view_all_clientsurvey'])
                    @foreach ($aremanagersarray as $key=>$areamanager)
                        <option value="{{$key}}">{{$areamanager}}</option>
                    @endforeach
                @endcanany


            </select>
        </div>

    </div>
    <div class="row chartview" style="padding-top: 10px !important;">


        <div class="col-sm-1 " style="display: inline-table;vertical-align:top;">
                    Start Date
                </div>
                <div class="col-sm-2 " style="display: inline-table;vertical-align:top;;">
                <input type="text" name="startdate" id="startdate"
                class="datepicker" value="{{date('Y-m-d', strtotime(date("Y-m-d"). ' -365 days'))}}" />
                </div>
                <div class="col-sm-2" style="display: inline-table;vertical-align:top;">
            <select name="userratingcondition" id="userratingcondition" class="form-control">
                <option value="0">Where User rating</option>
                <option value="equal">Equal to</option>
                <option value="greater">Greater than or equal</option>
                <option value="less">Less than or equal</option>
            </select>
        </div>
        <div class="col-sm-2" style="display: inline-table;vertical-align:top;">
            <select name="userratingfilter" id="userratingfilter" class="form-control">
                <option value="0">Select User rating</option>
                @foreach ($employeeratinglookup as $rating)
                    <option value="{{$rating->score}}">{{$rating->score}} - {{$rating->rating}}</option>
                @endforeach


            </select>
        </div>
        <div class="col-sm-1 " style="display: inline-table;vertical-align:top;">
            Graph Type
        </div>
        <div class="col-sm-2  " style="display: inline-table;vertical-align:top;">
            <select name="graphtype" id="graphtype" class="form-control">
                <option value="individual">Individual</option>
                <option value="average">Average</option>

            </select>
        </div>
        <div class="col-sm-1 " style="display: inline-table;vertical-align:top;">
            <button type="button" class="btn btn-primary plotbutton form-control">Search</button>
        </div>
        <div class="col-sm-1">
            <i  id="tableview"  data-toggle="tooltip" title="Table View" class=" fa fa-list-ul orange" 
            style="float:right;border-radius:5px;color: white !important;background:#F2351F;padding:10px"  aria-hidden="true"></i>

        </div>
    </div>
</form>
    <div class="row chartview" >
        <div class="col-sm-12 chartcanvas" style="height: 600px">

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
                    <label for="name" class="col-sm-3 control-label">Client</label>
                    <div class="col-sm-8">
                        <select name="client_id" id="client_id" class="form-control">
                                <option value="0">Select Any</option>
                                @foreach ($permissionaddclients as $addclient)
                                    <option value="{{$addclient->id}}">{{$addclient->project_number.'-'.$addclient->client_name}}</option>
                                @endforeach
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="row" id="client_contact_userid_div" style="padding-bottom: 10px">
                    <label for="name" class="col-sm-3 control-label">Client Contact</label>
                    <div class="col-sm-8" id="contact_control_block">
                        <select name="client_contact_userid" id="client_contact_userid" class="form-control">
                            <option value="0">Select Any</option>
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="row" id="client_contact_userid_optional_div" style="padding-bottom: 10px;display: none">
                    <label for="name" class="col-sm-3 control-label">Client Contact(Other)</label>
                    <div class="col-sm-8" >
                        <select name="client_contact_userid_optional" id="client_contact_userid_optional" class="form-control">
                            <option value="0">Select Any</option>
                        </select>

                        <small class="help-block"></small>
                    </div>



                </div>

                <div class="row" id="userrating_div" style="padding-bottom: 10px">
                    <label for="name" class="col-sm-3 control-label">Rating</label>
                    <div class="col-sm-8">
                        <select name="userrating" id="userrating" class="form-control">
                            @foreach ($employeeratinglookup as $rating)
                                <option value="{{$rating->score}}">{{$rating->score}} - {{$rating->rating}}</option>
                            @endforeach
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="row" id="notes_div" style="padding-bottom: 10px">
                    <label for="name" class="col-sm-3 control-label">Notes</label>
                    <div class="col-sm-8">
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

        var datatablefunction = function(){
            try{

                var table = $('#clientsurveytable').DataTable({
                destroy: true,
                bprocessing: false,
                processing: true,
                serverSide: true,
                // responsive: true,
                "autoWidth": false,
                "searching":true,
                ajax:({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("clientsurvey.surveydata") }}',
                    type: 'POST',
                    data:{startdate:$('input[name="tablestartdate"]').val(),
                    enddate:$('input[name="tableenddate"]').val(),},
                }),
                order: [
                [1, "asc"]
                ],
                lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
                ],
                columnDefs: [
                    { width: "3%", targets: 0 },
                    { width: "20%", targets: 1 },
                    { width: "14%", targets: 2 },
                    { width: "9%", targets: 3 },
                    { width: "7%", targets: 4 },

                ],

                columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false
                },
                {
                    data: "client",
                    name:"client",
                    orderable: false,

                },
                {
                    data: "usercontact",
                    name:"usercontact",
                    orderable: false,
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
                    data: "user.employee.phone",
                    name:"user.employee.phone",

                },
                {
                    data: 'rating',
                    name: 'Rating'
                },
                {
                    data: 'notes',
                    name: 'Notes'
                },
                {
                    data: null,
                    orderable: false,
                    render: function (o) {
                    let name=o.created_user.first_name;
                    if(o.created_user.last_name!=null){
                        name+=" "+o.created_user.last_name
                    }
                    return name;
                    },
                },
                {
                    data: null,
                    orderable: false,
                    render: function (o) {

                        let formattedDate=moment(o.created_at).format('dddd, MMM DD YYYY, h:mm A');

                        return formattedDate;
                    },
                },

                ]
                });
                } catch(e){
                console.log(e.stack);
                }
        }
        $(document).ready(function (e) {

            $('#clientsurveytable').show();
            $("#area_manager").select2();
            $.fn.dataTable.ext.errMode = 'throw';
            datatablefunction()
            });
            $(document).on("click",".filtertable",function(e){
                e.preventDefault();
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

            $('#client_id').on('select2:select', function (e) {
                let clientid = ($(this).val())
                $.ajax({
                    type: "post",
                    url: '{{ route("clientsurvey.clientuserdata") }}',
                    data: {clientid:clientid},
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        let selectlist = '<option value="0">Select Any</option>'
                        let otherselectlist = '<option value="0">Select Any</option>'
                        let data = response;
                        let clientdata = data[0];
                        let otherdata = data[1];

                        $("#client_contact_userid").val(clientdata);

                        (Object.keys(otherdata)).forEach(element => {
                            otherselectlist+='<option value="'+element+'">'+otherdata[element]+"</option>"
                        });
                        $("#client_contact_userid").html(otherselectlist);
                    }
                });

            });

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
                                var table = $('#clientsurveytable').DataTable();
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
                $(".tableview").css("display","flex");
            })

            $(document).on("click","#chartview",function(e){
                e.preventDefault();
                $(".tableview").hide();
                $(".chartview").show();
                $(".chartview").css("display","flex");
            })

            $(document).ready(function () {
                $("#client").select2()
            });

            $(document).on("select2:select","#client",function(e){
                e.preventDefault();
                let clientid = $(this).val();
            });

            $(document).on("click",".plotbutton",function(e){
                e.preventDefault()
                var formData = new FormData($('#chartsearch')[0]);
                let clientid = $("#client").val();
                let clientname = $("#client").select2("data");

                $.ajax({
                    type: "post",
                    url: '{{ route("clientsurvey.plotdata") }}',
                    data: formData,
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData : false,
                    contentType: false,
                    success: function (response) {
                        var data = jQuery.parseJSON(response);
                        var alldata = jQuery.parseJSON(response);
                        let yaxis = [];
                        $(".chartcanvas").html("");
                        $(".chartcanvas").html(`<canvas  id="myChart" ></canvas>`);


                        var ctx = document.getElementById('myChart').getContext('2d');

                            //Multiple Line chart

                            let dataarray = [];
                            let i =0;
                            try {
                                Object.keys(data["customer"]).forEach(element => {




                                let dobject = {
                                label: data["customer"][element]["name"],
                                backgroundColor: 'transparent',
                                borderColor: data["customer"][element]["color"],
                                data: Object.values(data["yaxis"][element])
                                }
                                dataarray.push(dobject);
                                });
                            } catch (error) {

                            }

                            console.log(dataarray)
                            let chart = new Chart(ctx, {
                            // The type of chart we want to create
                            type: 'line',

                            // The data for our dataset
                            data: {
                                labels: data["xaxis"],
                                datasets: dataarray
                            },

                            // Configuration options go here
                            options: {
                                maintainAspectRatio: false,
                                spanGaps: true,
                                legend:{
                                    display:false
                                },
                                // events: ['click'],
                                layout: {
                                    padding: {
                                        left:25,
                                        right: 0,
                                        top: 30,
                                        bottom: 40
                                    }
                                },tooltips:{
                                    callbacks: {
                                        labelColor: function(tooltipItem, chart) {
                                            // console.log(dataarray)
                                            // debugger
                                        return {
                                            borderColor: dataarray[tooltipItem.datasetIndex]["borderColor"],
                                            backgroundColor: dataarray[tooltipItem.datasetIndex]["borderColor"]
                                        }
                                    },
                                    label: function(tooltipItem,data) {
                                        var dataarray = alldata["count"];
                                        var customerdataarray = alldata["customer"];
                                        let customervalues = (Object.values(customerdataarray)[tooltipItem.datasetIndex]["id"]);
                                        let noofrating = 0;
                                        if(customervalues){
                                            //console.log(tooltipItem["label"])
                                           // console.log(data["datasets"][tooltipItem.datasetIndex])
                                           noofrating=(dataarray[customervalues][tooltipItem["label"]])
                                        }


                                        //console.log(tooltipItem.datasetIndex);
                                        if(tooltipItem.value>0){
                                            if(noofrating>1){
                                                if((tooltipItem.value).search(".")){
                                                    return data["datasets"][tooltipItem.datasetIndex]["label"]+" Rating - "+parseFloat(tooltipItem.value).toFixed(2)+" (No of Rating : "+noofrating+")";
                                                }else{
                                                    return data["datasets"][tooltipItem.datasetIndex]["label"]+" Rating - "+parseFloat(tooltipItem.value).toFixed(2)+" (No of Rating : "+noofrating+")";
                                                }

                                            }else{
                                                return data["datasets"][tooltipItem.datasetIndex]["label"]+" Rating - "+parseFloat(tooltipItem.value).toFixed(2);
                                            }

                                        }else{
                                            return false;
                                        }

                                                //return clientname[0].text+" : Rating - "+tooltipItem.value;
                                        },
                                        title:function(){
                                            return false
                                        },

                                    },
                                }

                            }
                        });

                    }
                });

            })

            $(document).on("change","#userratingcondition",function(e){
                if($(this).val()!="0"){
                    if($("#graphtype").val()=="average"){
                        // swal("Warning","Graph type cannot be average","warning");
                        // $("#graphtype").val("individual")
                    }
                }
            })

            $(document).on("change","#graphtype",function(e){
                if($(this).val()=="average"){
                    if($("#userratingcondition").val()!="0"){
                        // swal("Warning","Graph type cannot be average","warning");
                        // $("#userratingcondition").val("0")
                    }
                }
            })
    </script>
@endsection
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
