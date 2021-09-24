@extends('layouts.app')
@section('css')
<style>
    label{
        /* display: none */
    }
    .error{
        color: red;
    }
    .close{

    }
    .popover {
     z-index: 2060 !important;
}
.gj-modal .col-md-4{
    color: #38393a !important;
}
.col-md-4{
    font-size: 16px !important;
    font-weight: normal;
    color: #38393a !important
}
.gj-dialog-md-body{
font-family: 'MicrosoftJhengHeiUI', sans-serif
}

</style>
<link rel='stylesheet' type='text/css' href='{{ asset('css/timepicki.css') }}' />
@endsection
@section('content')
<input type="hidden" name="maxbooking_perday" id="maxbooking_perday" value="{{$mainfacilitydata['maxbooking_perday']}}" />
<div class="container-fluid" style="margin-top:-5px;padding: 3px !important">

    <div class="row" >
        <div class="col-md-10 table_title"><h4>Facility Services</h4></div>
        <div class="col-md-2" style="text-align: right;padding-top:5px !important; ">


        </div>
    </div>
    <div class="row" >
        <div class="col-md-10 table_title"></div>
        <div class="col-md-2" style="text-align: right;padding-top:5px !important;padding-bottom:10px ">
            @canany(['manage_all_customer_facility_service'])
                <button id="addnewfacsub"  class="btn btn-primary" style="">Add New Facility Service</button>
                <button id="addnewfac"  class="btn btn-primary" style="display:none">Add New Facility Service</button>
            @endcanany

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="amenitytable" class="table table-bordered">
                <thead>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>

                    <th>Actions</th>
                </thead>
                <tbody>
                    @foreach ($facilityservice as $facility)
                        <tr><td>{{$facility->service}}</td>
                        <td>{{$facility->description}}</td>
                        <td>
                            @if ($facility->active==1)
                            Active
                            @else
                            Inactive

                            @endif
                        </td>
                        <td>
                            @canany(['manage_all_customer_facility_service'])
                                <a  class="editservicemaster fa fa-edit" style="cursor: pointer" data-id="{{$facility->id}}"></a>
                            @endcanany
                            @can('remove_customer_facility_service')
                                <a  class="removeservicemaster fa fa-trash" style="cursor: pointer" data-id="{{$facility->id}}"></a>
                            @endcan


                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="container-fluid" id="editfacility" >
</div>
<div class="container-fluid" id="addfacility" style="padding: 5px !important;font-size:14px">
    <form id="saveservice" action='{{route("cbs.savefacilitysignout")}}' method="post">
        @csrf
        <input type="hidden" name="facilityid"  id="facilityid" value="{{$facilityid}}" />

        <div class="row" style="margin-top: 20px;margin-bottom: 20px">
            <div class="col-md-4">
                Facility
            </div>
            <div class="col-md-8" style="text-align: left" id="facility">
                <input type="text" name="facility" class="form-control"  />
                <label class="error" for="facility"></label>
            </div>


        </div>

        <div class="row" style="margin-top: 20px;margin-bottom: 20px">
            <div class="col-md-4">
                    Description
            </div>
            <div class="col-md-8" style="text-align: left" id="description">
                <textarea name="description" class="form-control" rows="5"  >
                    </textarea><label class="error" for="description"></label>
            </div>


        </div>
        <div class="row" style="margin-top: 20px;margin-bottom: 20px">

            <div class="col-md-4">
                Booking interval&nbsp;&nbsp;
                <i data-container="body" class="fa fa-question-circle" data-content="Interval between slot in hours" style="cursor:pointer" aria-hidden="true"></i>
            </div>
            <div class="col-md-8" style="text-align: left" id="booking_interval">
                <input type="text" maxlength="5"  class="form-control number" name="booking_interval" value="" />
                <label class="error" for="booking_interval"></label>
            </div>
        </div>

        <div class="row" style="margin-top: 20px;margin-bottom: 20px">

            <div class="col-md-4">
                Maximum occupancy per slot (Count)&nbsp;&nbsp;<i data-container="body" class="fa fa-question-circle" data-content="How many people allowed during an interval" style="cursor:pointer" aria-hidden="true"></i>
            </div>
            <div class="col-md-8" style="text-align: left">
                <input type="number" class="form-control" value="" name="tolerance_perslot" />    <label class="error" for="tolerance_perslot"></label>
            </div>
        </div>
        <div class="row" style="margin-top: 20px;margin-bottom: 20px;display: none">
            <div class="col-md-4" style="">
                Weekend Booking
            </div>
        </div>
        <div class="row" style="margin-top: 20px;margin-bottom: 20px">
            <div class="col-md-8" style="text-align: left;display: none" id="weekend_booking">
                <select class="form-control "  name="weekend_booking" >
                    <option value="">Select Any</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>

                </select>
                <label class="error" for="weekend_booking"></label>
            </div>
        </div>
        <div class="row" style="margin-top: 20px;margin-bottom: 20px;display:none">
            <div class="col-md-4">
                Start Time
            </div>
            <div class="col-md-8" style="text-align: left" id="start_time">
                <input type="text" class="form-control timepicker" value="" name="start_time" />    <label class="error" for="start_time"></label>
            </div>
        </div>
        <div class="row" style="margin-top: 20px;margin-bottom: 20px;display:none">
            <div class="col-md-4">
                End Time
            </div>
            <div class="col-md-8" style="text-align: left" id="end_time">
                <input type="text" class="form-control timepicker" name="end_time" value="" />        <label class="error" for="end_time"></label>
            </div>
        </div>

        <div class="row" style="margin-top: 20px;margin-bottom: 20px">
            <div class="col-md-5">

            </div>

            <div class="col-md-4" style="">
                         <button class="btn btn-primary save" attr-mode="save">Save</button>
                         <button class="btn btn-primary">Cancel</button>
            </div>
        </div>
    </form>

    </div>

@stop
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $( ".fa-question-circle" ).popover();
            $("#amenitytable").DataTable({
                autoWidth: false,
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                "columnDefs":[
                    {"width":"20%", "targets": 0},
                    {"width":"60%", "targets": 1},
                    {"width":"10%", "targets": 2}]
            });

            try {

                $('select[name="weekend_booking"]').val("1");
            } catch (error) {

            }

            $("#timingtable").dataTable();

            $(".timepick").timepicker({
                timeFormat: 'h:i A',
                step: 15,
            });
        });

        $(document).on("click","#addnewfac",function(e){
            var id= $("#facilityid").val();
            location.href="{{route('cbs.addfacilityservice',['id'=>$facilityid])}}"
        })
        $("#addfacility").dialog({
                autoOpen: false,
                width:"700",
                title: '<h4 class="table_title">Add Facility Service</h4>',
                show: {
                    effect: "blind",
                    duration: 1000
                },
                hide: {
                    effect: "explode",
                    duration: 1000
                },
                draggable: false,
                close: function() {
                    alert('close');
                },
                modal: true,
                open: function() {
                    $('.ui-widget-overlay').addClass('custom-overlay');

                }
            });

            $("#editfacility").dialog({
                autoOpen: false,
                width:"700",
                title: '<h4 class="table_title">Add Facility Service</h4>',
                position: ['center',20] ,
                show: {
                    effect: "blind",
                    duration: 1000
                },
                hide: {
                    effect: "explode",
                    duration: 1000
                },
                draggable: false,
                close: function() {
                    alert('close');
                },
                modal: true,
                open: function() {
                    $('.ui-widget-overlay').addClass('custom-overlay');

                }
            });

        $(document).on("click","#addnewfacsub",function(e){
            var id= $("#facilityid").val();
            $("#addfacility").dialog("open")
        })

        $(document).on("click",".save",function(e){
        e.preventDefault();
        var attrmode = $(this).attr("attr-mode");
        var data = $('#saveservice').serialize();
        var maxbooking_perday = $("#maxbooking_perday").val();
        if(parseFloat($('input[name="booking_interval"]').val())>parseFloat(maxbooking_perday)){
            swal("Warning","Booking interval cannot exceed facilities maximum booking hours","warning")
        }else{
        $.ajax({
        type: "post",
        url: "{{route('cbs.savefacilityservice')}}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: data,
        success: function (response) {
            var data = jQuery.parseJSON(response);
            if(data.code==200){
            if(attrmode=="saveadd"){
                swal({
                title: "Success",
                text: data.message,
                type: "success"
                }, function() {
                    location.reload();
                });
            }else{
                swal({
                title: "Success",
                text: data.message,
                type: "success"
                }, function() {
                   location.reload();
                });
            }
            }else{
                swal("Warning",data.message,data.success)
            }
        }
    }).fail(function( data ) {
        $(".error").html("");
        var response = JSON.parse(data.responseText);
        var errorString = '<ul>';
        $.each( response.errors, function( key, value) {
            console.log(key);
            $('label[for="'+key+'"]').html(value);
            errorString += '<li>' + value + '</li>';
        });
        errorString += '</ul>';

    });
        }
    })

    $(document).on("click",".editservicemaster",function(e){
        e.preventDefault();
        var id = $(this).attr("data-id");
        var base_url = "{{ route('cbs.editfacilityservices',':id') }}";
        var url = base_url.replace(':id', id);
        $.ajax({
            type: "get",
            // url: "/cbs/editfacilityservices/"+id,
            url:url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $("#editfacility").html(response).after(function(e){
                    $("#editfacility").dialog("open");
                    $("#editfacility").css("cssText","width:700px !important;margin-top:5% !important");
                    $( ".fa-question-circle" ).popover();
                })
            }
        });
    })

    $(document).on("click",".removeservicemaster",function(e){
        e.preventDefault();
        var id = $(this).attr("data-id")
        var base_url = "{{ route('cbs.removefacilityservices',':id') }}";
        var url = base_url.replace(':id', id);
        e.preventDefault();
            var facilityid = $(this).attr("data-id");
            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action. Proceed?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes',
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){

                if (isConfirm){
                    $.ajax({
                    type: "post",
                    url: base_url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {id:facilityid},
                    success: function (response) {

                      var data = jQuery.parseJSON(response);
                      $(function(e){
                        if(data.code==200){
                            swal({
                            title: "Deleted",
                            text: data.message,
                            type: "success"
                            }, function() {
                                location.reload();
                            });

                        }else{
                            swal("Warning",data.message,"warning");
                        }
                      })
                    }
                });


                } else {
                    swal.close()
                    e.preventDefault();
                }
            });

    })


    $(".cancel").on("click",function(e){
        e.preventDefault();
        history.go(-1);
    })
    $(document).on("click",".saveupdate",function(e){
        e.preventDefault();
        $(".error").html("");
        var attrmode = $(this).attr("attr-mode");
        var maxbooking_perday = $("#maxbooking_perday").val();
        if(parseFloat($('input[name="booking_interval"]').val())>parseFloat(maxbooking_perday)){
            swal("Warning","Booking interval cannot exceed facilities maximum booking hours","warning")
        }
        else{
            var data = $('#saveupdateservice').serialize();
            $.ajax({
            type: "post",
            url: "{{route('cbs.updatefacilityservice')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if(data.code==200){
                if(attrmode=="saveadd"){
                    swal({
                    title: "Success",
                    text: data.message,
                    type: "success"
                    }, function() {
                        location.reload();
                    });
                }else{
                    swal({
                    title: "Success",
                    text: data.message,
                    type: "success"
                    }, function() {
                        location.reload();
                    });
                }
                }else{
                    swal("Warning",data.message,"warning")
                }
            }
        }).fail(function( data ) {
            $(".error").html("");
            var response = JSON.parse(data.responseText);
            var errorString = '<ul>';
            $.each( response.errors, function( key, value) {

                $('label[for="'+key+'"]').html(value).addClass("error");
                errorString += '<li>' + value + '</li>';
            });
            errorString += '</ul>';

        });
        }

    })

    $(document).on("click",".closebutton",function(e){
        $("#editfacility").dialog("close");
        $("#editfacility").html("");
    })

    $(document).on("mouseover",".fa-question-circle",function(e){
        var tooltipid =$(this).attr("aria-describedby");
        $(this).trigger("click");
        $(".popover").css("cssText","z-index:2080 !important")
    })

    $(document).on("mouseout",".fa-question-circle",function(e){

        $(this).trigger("click");

    })

    $(document).on("keypress",'#editfacility .notdecimal',function(evt){

        var $txtBox = $(this);
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if(charCode==46){
            return false;
        }

    })

    $(document).on("keypress","#editfacility .number",function(evt){
    var $txtBox = $(this);
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;
        else {
            var len = $txtBox.val().length;
            var index = $txtBox.val().indexOf('.');
            if (index > 0 && charCode == 46) {
              return false;
            }
            if (index > 0) {
                var charAfterdot = (len + 1) - index;
                if (charAfterdot > 3) {
                    return false;
                }
            }
        }
        return $txtBox; //for chaining
})



    </script>
@endsection
