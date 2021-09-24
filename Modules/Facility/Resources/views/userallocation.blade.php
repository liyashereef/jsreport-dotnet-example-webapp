@extends('layouts.app')
@section('css')
<style>
    label{
        /* display: none */
        /* color: red; */

    }

    .ui-widget-overlay.custom-overlay
    {
        background-color: black;
        background-image: none;
        opacity: 0.9;
        z-index: 1040;
    }
</style>
<link rel='stylesheet' type='text/css' href='{{ asset('css/timepicki.css') }}' />
@endsection
@section('content')
<div class="container-fluid" style="padding: 0px !important">
    <div class="row mainhead" style="margin-bottom: 20px">
        <div class="col-md-10 table_title "><h4>Facility User Allocation</h4></div>
        <div class="col-md-2" style="text-align: right">

        <input type="hidden" name="user_id" id="user_id" value="{{\Auth::user()->id}}" />
        </div>
    </div>
    <div class="row"  style="margin-bottom:10px">
        <div class="col-sm-1 " style="padding-top:7px;padding-right:0px !important;">
            Customer <span class="mandatory">*</span></div>
        <div class="col-sm-2">
            <select class="form-control" id="customer" placeholder="customer" name="customer">
                <option value="">Select Any</option>
                @foreach ($customerarray as $key=>$value)
                    <option value="{{$key}}">{{$value}}</option>
                @endforeach
            </select>
            <label for="customer"></label>
        </div>


        <div class="col-sm-1 " style="padding-top:7px;padding-right:0px !important;padding-left:47px;text-align:left">
            Facility <span class="mandatory">*</span></div>
        <div class="col-sm-2">
            {!! Form::hidden("chosenfacility", "", ["id"=>"chosenfacility"]) !!}
            <select class="form-control " placeholder="Facility" name="facility" id="facility">
                <option value="">Select Any</option>
            </select>

        </div>
        <div class="col-sm-1 serviceb" style="padding-top:7px;padding-right:0px !important;text-align:left;padding-left:47px;display:none">
            Service </div>
        <div class="col-sm-2 serviceb" style="display:none">
            {!! Form::hidden("chosenservice", "", ["id"=>"chosenservice"]) !!}
            <select class="form-control " placeholder="Service" name="service" id="service">
                <option value="">Select Any</option>

            </select>

        </div>
    </div>
    <div class="row"  >
        <div class="col-md-12" id="userallocation" >
            <table id="usertable" class="table table-bordered">
                <thead>
                    <th></th>
                    <th>Name</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </thead>
                <tbody>

                </tbody>
            </table>
            <input type="hidden" name="facilityorservice" id="facilityorservice" value="0" />
            <input type="hidden" name="singleservice" id="singleservice" value="" />
        </div>
    </div>
    <div class="row"  >
        <div class="col-md-12"  >
            <button class="btn btn-primary" id="allocateuser">Allocate</button>
        </div>
    </div>

</div>

<div class="modal fade" id="adduserblock" tabindex="-1" role="dialog" aria-labelledby="adduserblockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">


      </div>
    </div>
  </div>
    <div class="modal fade" id="addprereqblock" tabindex="-1" role="dialog"
    aria-labelledby="addprereqblockModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{route('cbs.savefacilityuserprerequisite')}}" id="savefacilityuserprerequisite"  name="savefacilityuserprerequisite" method="post">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="addprereqblockModalLabel">User Prerequisites</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body"  id="userprereq">

                </div>
                <div class="modal-footer">
                    {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                    {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                </div>
            </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $("#usertable").dataTable();
            $('select[name="customer"]').select2();
            $(".phone").mask("(999)999-9999");

        });
        $(document).on("click",".close",function(e){
            $("label").html("")
            $('#adduserblock').modal('hide')
        })


        $(document).on("click","#allocateuser",function(e){

            e.preventDefault();
            var facilityorservice = $("#facilityorservice").val();
            // $('body').loading({
            //         stoppable: false,
            //         message: 'Please wait...'
            // });
            var facservice =[];
            $('.alloccbox').each(function() {

                var facilityorservice = $("#facilityorservice").val();
                var currentElement = $(this);
                var value = currentElement.val();
                allocated = currentElement.attr("attr-allocated");
                if(facilityorservice>0){
                    if(facilityorservice==1 && $("#facility").val()>0){
                        if(allocated=="false" && currentElement.is(":checked")){
                            facservice.push($(this).attr("id"))
                        }
                    }else{
                        if(facilityorservice==2 && $("#service").val()>0){
                            if(allocated=="false" && currentElement.is(":checked")){
                                facservice.push($(this).attr("id"))
                            }
                    }
                    }


                }else{
                    swal("Warning","Please choose Facility/Service","warning")
                }

            });
            if(facservice.length>0){

                if($("#facilityorservice").val()=="1")
                {
                    var type = "addfacility";
                    var id = $("#facility").val();
                }else{
                    var type = "addservice";
                    var id = $("#service").val();
                }


                var user_id = facservice;
                var facility_id = $("#facility").val();

                $.ajax({
                    type: "post",
                    url: "{{route('cbs.saveorremovemassallocation')}}",
                    data: {type:type,id:id,user_id:user_id,facility_id:facility_id},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        var data = jQuery.parseJSON(response);
                        if(data.code=="200"){
                            swal({
                            title: "Success",
                            text: "Users allocated successfully ",
                            type: "success"
                            }, function() {

                                $("#customer").trigger("change");


                            });

                        }else{

                        }
                    }
                });

            }else{
                swal("Warning","Cannot allocate again","warning")
            }
        })



        $(document).on("click","#mdl_save_change",function(e){
            e.preventDefault();
            var formdata = $('#addprereqblock form').serialize();
            var flag = 1;
            var formvalues = [];
            i=0;
            $(".frmdata").each(function() {
                if($(this).val()=="" && flag==1){
                    flag=0;
                }
                formvalues[i]={
                "facilityid":$(this).attr("attr-facilityid"),
                "userid":$(this).attr("attr-userid"),
                "allocationid":$(this).attr("attr-allocationid"),
                "requisite":$(this).attr("attr-requisite"),
                "choice":$(this).val()};
                i++;
            })
            if(flag==1){
                    $.ajax({
                    type: "post",
                    url: "{{route('cbs.savefacilityuserprerequisite')}}",
                    data: {formdata:formvalues},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        var data  =jQuery.parseJSON(response);
                        if(data.code==200){
                            swal({
                                title: "Success",
                                text: "Prerequisite added successfully",
                                type: "success",
                            },
                            function () {
                                $("#addprereqblock").modal("hide").after(function(e){
                                    $("#customer").trigger("change")
                                })
                            });
                        }else{
                            swal("Warning",data.message,"warning");
                        }
                    }
                });
            } else{
                swal("Warning","All fields are mandatory","warning")
            }


        })

        $(document).on("change","#facility",function(e){
            e.preventDefault();
            var facval = $(this).val();
            var single = $('option:selected', this).attr('attr-single');
            if(facval>0 && single==0){
                $(".serviceb").show()
            }else{
                $(".serviceb").hide()
            }
            $("#customer").trigger("change").after(function(e){
            });

        })
        $(document).on("change","#service",function(e){
            e.preventDefault();
            var facval = $(this).val();

            $("#customer").trigger("change").after(function(e){

            });

        });
        $(document).on("change","#customer",function(e){
            e.preventDefault();
            $.ajax({
                type: "post",
                url: "{{route('cbs.customersfacility')}}",
                data: {customerid:$(this).val(),
                    facility:$("#facility").val(),
                    service:$("#service").val()},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var facility = response.facility;
                    var chosenfacility = response.chosenfacility;
                    var chosenservice = response.chosenservice;
                    facilistlist = '<option value="">Select Any</option>';
                    $.each(facility, function( index, value ) {

                            id = value["id"];
                            facility = value["facility"];
                            var singleservicefacility = value["single_service_facility"];
                            facilistlist+='<option attr-single="'+singleservicefacility+'" value="'+id+'">'+facility+'</option>';

                    });

                    var facilityservice = response.facilityservice;
                    faciservlist = '<option value="">Select Any</option>';
                    $.each(facilityservice, function( index, value ) {

                            id = value["id"];
                            service = value["service"];
                            faciservlist+='<option value="'+id+'">'+service+'</option>';

                    });
                    $("#service").html(faciservlist);
                    $("#userallocation").html(response.body).after(function(e){
                        $("#usertable").dataTable({
                            "order": [[ 1, "asc" ]],
                            columnDefs: [
                                { width: "5%", targets: 0 },
                                { width: "15%", targets: 0 },
                                { width: "10%", targets: 0 },
                                { width: "20%", targets: 0 },
                                { width: "7%", targets: 0 },
                                { width: "23%", targets: 0 },
                                { width: "20%", targets: 0 }
                            ],

                        });

                    });
                    $("#facility").html(facilistlist).after(function(e){
                        if(chosenfacility>0){
                            $("#facility").val(chosenfacility);
                        }
                        if(chosenservice>0){
                            $("#service").val(chosenservice);
                        }
                    });
                    if($('#facility').val()=="" || $('#facility').val()==null){
                        $(".serviceb").hide()
                        $("#service").html("")
                    }
                }
            });
        })




        $(document).on("click",".adduseralloc",function(e){
            var type = $(this).attr("attr-add");
            var id = $(this).attr("attr-id");
            var user_id = $(this).attr("attr-user");
            var facility_id = $("#facility").val();
            var message = "";
            if(type=="addservice"){
                message="You want to add this user?";
            }else if(type=="removeservice"){
                message="You want to remove this user?";
            }else if(type=="addfacility"){
                message="You want to add this user?";
            }else if(type=="removefacility"){
                message="You want to remove this user?";
            }
            // $(this).prop("disabled","disabled");
            swal({
                title: "Are you sure?",
                text: message,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function (isConfirm) {
                if (isConfirm) {
            $.ajax({
                type: "post",
                url: "{{route('cbs.saveorremoveallocation')}}",
                data: {type:type,id:id,user_id:user_id,facility_id:facility_id},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var data = jQuery.parseJSON(response);
                    if(data.code=="200"){
                        $("#customer").trigger("change");
                    }else{

                    }
                    swal.close()
                }
            });
            }
            else{
                swal.close()
            }});
        });

        $(document).on("click",".prereqform",function(e){
            e.preventDefault()
            var facilityid = $("#facility").val();
            var allocationid = $(this).attr("attr-allocid");
            var user_id = $(this).attr("attr-userid");
            if(allocationid>0 && facilityid>0){
                $.ajax({
                type: "post",
                url: "{{route('cbs.getUserprerequisites')}}",
                data: {facilityid:facilityid,allocationid:allocationid,user_id:user_id},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var data = jQuery.parseJSON(response);
                    if(data.code=="406"){
                        swal("Warning",data.message,"warning")
                    }else{
                        $("#userprereq").html(data.text).after(function(e){
                        $("#addprereqblock").modal()
                    });
                    }

                }
            });
            }else{
                swal("Warning","Facility and allocation are mandatory","warning")
            }

        })

        $(document).on("change",".weekendallocation",function(e){
            e.preventDefault();
            var checkedstate = $(this).is(":checked");
            var process = "off";
            var type = "";
            var daytype = $(this).attr("attr-daytype")
            var user_id = $(this).attr("attr-user");
            var id = $(this).val();
            if ($(this).is(":checked")){
                if(daytype=="weekend"){
                    type = "addweekend";
                }else{
                    type = "addweekday";
                }

            }else{
                if(daytype=="weekend"){
                    type = "removeweekend";
                }else{
                    type = "removeweekday";
                }

            }
            $.ajax({
                type: "post",
                url: "{{route('cbs.saveorremovedayallocation')}}",
                data: {type:type,id:id,user_id:user_id},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var data = jQuery.parseJSON(response);
                    if(data.code=="200"){
                        $("#customer").trigger("change");
                    }else{

                    }
                }
            });
        })
        $("#usertable").dataTable({
            "order": [[ 1, "asc" ]],
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columnDefs: [
                                { width: "5%", targets: 0 },
                                { width: "15%", targets: 0 },
                                { width: "10%", targets: 0 },
                                { width: "20%", targets: 0 },
                                { width: "7%", targets: 0 },
                                { width: "23%", targets: 0 },
                                { width: "20%", targets: 0 }
                            ],
        });

        $(document).on("change","#selectall",function(e){
            e.preventDefault();
            if($(this).is(":checked")){
                //$(".alloccbox").prop("checked",true);
                $(".alloccbox").each(function(index,value){
                    $(this).prop("checked",true).trigger("change");
                })
            }else
            {
                $(".alloccbox").prop("checked",false);
            }
        })
    </script>
@endsection
