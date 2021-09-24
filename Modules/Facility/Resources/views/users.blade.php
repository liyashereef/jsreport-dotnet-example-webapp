@extends('layouts.app')
@section('css')
<style>
    .error{
        /* display: none */
        color: red;

    }
.fa-lg{
    font-size:1.0em !important
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
    <div class="row mainhead" style="margin-bottom: 5px">
        <div class="col-md-10 table_title " style="padding-top: 0px !important"><h4 >Users</h4></div>
        <div class="col-md-2" style="text-align: right;padding-top:5px !important">

            <input type="hidden" name="user_id" id="user_id" value="" />
        </div>
    </div>
    <div class="row mainhead" style="margin-bottom: 5px">
        <div class="col-md-10 table_title " style="padding-top: 0px !important"></div>
        <div class="col-md-2" style="text-align: right;">
            @canany(['manage_all_facility_users','manage_allocated_facility_users'])
            <button id="addnewfac"  class="btn btn-primary" style="">Add New User</button>
            @endcanany

        </div>
    </div>
    <div class="row" id="userlist" style="margin-top: 17px">
        <div class="col-md-12">
            <table id="usertable" class="table table-bordered">
                <thead>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Status</th>
                    <th>Actions</th>
                </thead>
                <tbody>

                @foreach ($users as $user)
                <tr>
                    <td>{{$user->first_name}} {{$user->last_name}}</td>
                    <td>{{$user->username}}</td>
                    <td>{{$user->customer->client_name}} ({{$user->customer->project_number}})</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->phoneno}}</td>
                    <td>
                        @if ($user->active==1)
                        Active
                        @else
                        Inactive

                        @endif
                    </td>
                    <td>
                        @canany(['manage_all_facility_users','manage_allocated_facility_users'])
                            <i customername="{{$user->customer->client_name}} ({{$user->customer->project_number}})" attr-id="{{$user->id}}" title="Edit user" style="cursor: pointer" class="edituser fa fa-edit fa-lg" style="margin: auto 5px"></i>
                        @endcanany
                        &nbsp;
                        @canany(['remove_facility_users'])
                            <i attr-id="{{$user->id}}" title="Remove user" style="cursor: pointer" class="removeuser fa fa-trash fa-lg" style="margin: auto 5px"></i>
                        @endcanany
                    </td>

                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="row"  >
        <div class="col-md-12" id="userallocation" style="display: none"></div>
    </div>

</div>

<div class="modal fade" id="adduserblock" class="modal hide fade" role="dialog" aria-labelledby="adduserblockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="adduserblockModalLabel">Add Users</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form action="{{route('cbs.addfacilityuser')}}" id="faciluser" method="post" autocomplete="off">
                @csrf
                <input autocomplete="false" name="hidden" type="text" style="display:none;">


                    <div class="container-fluid "   >

                        <div class="row " style="margin-bottom:10px" id="first_name">
                            <div class="col-sm-4 ">
                            First Name <span class="mandatory">*</span>
                            </div>
                            <div class="col-sm-4 ">
                                <input autocomplete="off" class="form-control " placeholder="First Name" name="first_name" type="text" value="" required />
                                <input name="user_id" id="user_id" type="hidden" value="" />

                            </div>
                            <div class="col-sm-4 ">
                                <label class="error" for="first_name"></label>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom:10px" id="last_name">
                            <div class="col-sm-4 ">
                                Last Name</div>
                            <div class="col-sm-4">
                                <input autocomplete="off" class="form-control " placeholder="Last Name" name="last_name" type="text" value="" required />


                            </div>
                            <div class="col-sm-4 "><label class="error" for="last_name"></label></div>
                        </div>
                        <div class="row" style="margin-bottom:10px" id="unit_no">
                            <div class="col-sm-4 ">
                                Unit No</div>
                            <div class="col-sm-4">
                                <input autocomplete="off" class="form-control " placeholder="Unit Number" name="unit_no" type="text" value="">


                            </div>
                            <div class="col-sm-4 "><label class="error" for="unit_no"></label></div>
                        </div>
                        <div class="row" id="displaycustomer" style="margin-bottom:10px">
                            <div class="col-sm-4 ">
                                Customer <span class="mandatory">*</span></div>
                            <div class="col-sm-4" >
                                <input autocomplete="off" class="form-control " readonly placeholder="" name="displaycustomer" type="text" value="">

                            </div>
                            <div class="col-sm-4 "><label class="error" for="customer"></label></div>
                        </div>
                        <div class="row" id="customer" style="margin-bottom:10px">
                            <div class="col-sm-4 ">
                                Customer <span class="mandatory">*</span></div>
                            <div class="col-sm-4">
                                <select class="form-control" placeholder="customer" name="customer" required>
                                    <option value="">Select Any</option>
                                    @foreach ($customerarray as $key=>$value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-sm-4 "><label class="error" for="customer"></label></div>
                        </div>
                        <div class="row" id="email" style="margin-bottom:10px">
                            <div class="col-sm-4 ">
                                Email <span class="mandatory">*</span></div>
                            <div class="col-sm-4">
                                <input autocomplete="off" class="form-control" placeholder="Email" name="email" type="email" value="" required />

                            </div>
                            <div class="col-sm-4 "><label class="error" for="email"></label></div>
                        </div>
                        <div class="row" style="margin-bottom:10px" id="alternate_email">
                            <div class="col-sm-4 ">
                               Alternate Email
                            </div>
                            <div class="col-sm-4">
                                <input autocomplete="off" class="form-control" placeholder="Alternate Email" name="alternate_email" type="email" value="" />

                            </div>
                            <div class="col-sm-4 "><label class="error" for="alternate_email"></label></div>
                        </div>
                        <div class="row" id="phoneno" style="margin-bottom:10px">
                            <div class="col-sm-4 ">
                                Phone Number <span class="mandatory">*</span></div>
                            <div class="col-sm-4">
                                <input autocomplete="off" class="form-control phone" placeholder="Phone Number" name="phoneno" type="text" value="" required />

                            </div>
                            <div class="col-sm-4 "><label class="error" for="phoneno"></label></div>
                        </div>


                        <div class="row" style="margin-bottom:10px" id="username">
                            <div class="col-sm-4 ">
                                Username <span class="mandatory">*</span>
                            </div>
                            <div class="col-sm-4">
                                <input autocomplete="off" class="form-control " placeholder="Username" name="username" type="text" value="" required />

                            </div>
                            <div class="col-sm-4 "><label class="error" for="username"></label></div>
                        </div>
                        <div class="row" style="margin-bottom:10px" id="password">
                            <div class="col-sm-4 ">
                                Password <span class="mandatory">*</span>
                            </div>
                            <div class="col-sm-4">
                                <input autocomplete="off" class="form-control" placeholder="********" name="password" type="password" value="" required />

                            </div>
                            <div class="col-sm-4 "><label class="error" for="password"></label></div>
                        </div>
                        <div class="row" style=";margin-bottom: 10px" id="active">

                            <div class="col-md-4">
                                Active
                            </div>
                            <div class="col-md-4" style="text-align: right">

                                <select class="form-control" name="active">
                                    <option value="">Select Any</option>
                                    <option value="yes" >Yes</option>
                                    <option value="no" >No</option>

                                </select>
                            </div>
                            <div class="col-sm-4 "><label class="error" for="active"></label></div>
                        </div>
                        <div class="row" style="margin-bottom:10px;padding-top:25px"  id="save">

                            <div class="col-sm-12" style="text-align: center">
                                <button type="button" name="save" id="editbutton" class="btn btn-primary" style="display:none">Update</button>
                                <button type="button" name="save" id="savebutton" class="btn btn-primary" style="display:none">Save</button>
                                <button type="button" name="cancel" id="cancelbutton" data-dismiss="modal" class="btn btn-primary" style="">Cancel</button>
                            </div>
                        </div>
                    </div>

             </form>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $("#usertable").dataTable({
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                "order": [[ 0, "asc" ]]
            });
            //$('select[name="customer"]').select2();
            $(".phone").mask("(999)999-9999");
            $("select[name=customer]").select2({
                dropdownParent: "#adduserblock"
            });
        });
        $(document).on("click",".close",function(e){
            $(".error").html("");
            $('#adduserblock').modal('hide')
        })



        $(document).on("click","#addnewfac",function(e){
            // $("#adduserblock").show();
            // $(".mainhead").hide();
            // $("#userlist").hide();
            // $(this).hide();
            //$("#username").show()
            $("#savebutton").show();
            $("#editbutton").hide();
            $("#user_id").val("")
            $(".error").html("");
            $("#displaycustomer").hide();
            $('input[name="username"]').prop("readonly",false)
            $("#password").show()
            $("#customer").show()
            $("#active").hide()
            $("#faciluser")[0].reset()
            $(".modal-title").html("Add Users")
            $("select[name=customer]").select2();
            $("#adduserblock").modal();

        });

        $(document).on("click",".edituser",function(e){
            e.preventDefault();
            $(".error").html("");
            $("#savebutton").hide();
            $("#editbutton").show();
            var user_id =$(this).attr("attr-id");
            var cusname =$(this).attr("customername");
            $.ajax({
                type: "post",
                url: "{{route('cbs.facilityuserdetails')}}",
                data: {id:$(this).attr("attr-id")},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var data = jQuery.parseJSON(response);
                    $(".modal-title").html("Edit Users")
                    //$("#username").hide()
                    $('input[name="username"]').prop("readonly","readonly")
                    $("#password").hide()
                    $("#customer").hide()
                    $("#active").show()
                    var dialog = $('#adduserblock');
                    dialog.modal();
                        $("#displaycustomer").show();
                        $('input[name="displaycustomer"]').val(cusname)
                        $('input[name="user_id"]').val(user_id)
                        $('input[name="first_name"]').val(data.first_name)
                        $('input[name="last_name"]').val(data.last_name)
                        $('input[name="unit_no"]').val(data.unit_no)
                        $('select[name="customer"]').val(data.customer_id).select2();
                        $('input[name="email"]').val(data.email)
                        $('input[name="alternate_email"]').val(data.alternate_email)
                        $('input[name="username"]').val(data.username)
                        $('input[name="phoneno"]').val(data.phoneno)
                        if(data.active==1){
                            $('select[name="active"]').val("yes");
                        }else
                        {
                            $('select[name="active"]').val("no");
                        }


                }
            });

        })

        $(document).on("click","#savebutton",function(e){
            $(".error").html("");
            var formdata = $("#faciluser").serialize();
            $.ajax({
                type: "post",
                url: "{{route('cbs.addfacilityuser')}}",
                data: formdata,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var data = jQuery.parseJSON(response);
                    if(data.code=="200"){
                        swal({
                        title: "Success",
                        text: data.message,
                        type: data.success
                        }, function() {

                                location.reload();


                        });
                    }
                }
            }).fail(function(response){
                  var data = jQuery.parseJSON(response.responseText);
                  console.log(data);
                $.each( data.errors, function( key, value) {
                        var errorString = '<ul>';
                        errorString += '<li>' + value + '</li>';
                        var labelfor = $("label[for='" + key + "']");
                        console.log(labelfor);
                        $(labelfor).html(errorString);
                        });
            });
        })

        $(document).on("click","#editbutton",function(e){
            $(".error").html("");
            var formdata = $("#faciluser").serialize();
            $.ajax({
                type: "post",
                url: "{{route('cbs.editfacilityuser')}}",
                data: formdata,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var data = jQuery.parseJSON(response);
                    if(data.code=="200"){
                        swal({
                        title: "Success",
                        text: data.message,
                        type: data.success
                        }, function() {

                                location.reload();


                        });
                    }
                }
            }).fail(function(response){
                  var data = jQuery.parseJSON(response.responseText);
                  console.log(data);
                $.each( data.errors, function( key, value) {
                        var errorString = '<ul>';
                        errorString += '<li>' + value + '</li>';
                        var labelfor = $("label[for='" + key + "']");
                        console.log(labelfor);
                        $(labelfor).html(errorString);
                        });
            });
        })



        $(document).on("click",".removeuser",function(e){
            var userid = $(this).attr("attr-id");
            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action. Proceed?",
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
                    url: "{{route('cbs.removefacilityuser')}}",
                    data: {userid:userid},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        var data = jQuery.parseJSON(response);
                        swal("Deleted",data.message,data.success);
                        swal({
                        title: data.success,
                        text: data.message,
                        type: data.success
                    }, function() {
                        if(data.code==200){
                            location.reload();
                        }else{
                            swal("Warning",data.message,"warning");
                        }

                    });
                    }
                }).fail(function(response){
                    alert("here");
                });
                } else {
                    e.preventDefault();
                }
            });

        })


        $(document).on("click",".allocate",function(e){
            var userid = $(this).attr("attr-id");
            $.ajax({
                type: "post",
                url: "{{route('cbs.allocatefacilityuser')}}",
                data: {"userid":userid},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $("#user_id").val(userid);
                    //
                    $("#userlist").hide();
                    $("#userallocation").show();
                    $("#addnewfac").hide();
                    $("#userallocation").html(response).after(function(e){
                        //

                        $("#allocationtable").DataTable({
                            "autoWidth": false,
                            "columns": [
                                { "width": "5%" },
                                { "width": "40%" },
                                { "width": "40%" },
                                { "width": "15%" },

                            ],
                            "ordering":false,
                        });

                    })
                }
            });
        })

        $(document).on("click",".donealloc",function(e){
            $("#userlist").show();
            $("#userallocation").hide().html("");
            $("#addnewfac").show();
        });

        $(document).on("click",".adduseralloc",function(e){
            var type = $(this).attr("attr-add");
            var id = $(this).attr("attr-id");
            var user_id = $("#user_id").val();
            $(this).prop("disabled","disabled");

            $.ajax({
                type: "post",
                url: "{{route('cbs.saveorremoveallocation')}}",
                data: {type:type,id:id,user_id:user_id},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var data = jQuery.parseJSON(response);
                    if(data.code=="200"){

                        if(type=="addservice"){
                            $('#add-'+id).hide();
                            $('#rem-'+id).show();
                            $('#rem-'+id).prop("disabled",false);
                        }else if(type=="removeservice"){
                            $('#add-'+id).show();
                            $('#add-'+id).prop("disabled",false);
                            $('#rem-'+id).hide();
                        }
                    }else{
                        $(this).prop("disabled",false);
                    }
                }
            });
        });

        $(document).on("click",".cancel",function(e){
            swal.close()
        })
    </script>
@endsection
