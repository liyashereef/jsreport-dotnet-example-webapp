@extends('adminlte::page')
@section('title', 'IDS Office')
@section('content_header')
<h1>IDS Office</h1>
@stop

@section('css')
<style>
    .fa {
        margin-left: 11px;
    }
    .select2 .select2-container{
        width : 12% !important;
    }
    /* .modal-dialog{
        width: 60% !important;
    } */
    .form-horizontal .form-group {
        margin-left: 0px;
    }

    #office-timing-form{
        border-bottom: 1px solid #f4f4f4;
        margin-top: 14px;
    }
    .intervals_error_msg{
        color: #dd4b39;
    }
</style>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" id="add-new-office" data-title="Add New Offices">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="office-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Address</th>
            <th>Phone Number</th>
            <th>Phone Ext</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Color Code</th>
            <th>Photo Service</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Create Office</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'ids-office-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group">
                    <label for="name" class="col-sm-3 control-label">Name <span class="mandatory"> *</span></label>
                    <div  id="name" class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control','placeholder' => 'Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address" class="col-sm-3 control-label">Address<span class="mandatory"> *</span></label>
                    <div id="adress"  class="col-sm-9">
                    {{ Form::text('adress',null,array('class'=>'form-control','autocomplete'=>'false','placeholder' => 'Address', 'id' => 'office_address')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div  class="form-group">
                    <label for="" class="col-sm-3 control-label">Lat & Lng<span class="mandatory"> *</span></label>
                    <div id="latitude" class="col-sm-4">
                    {{ Form::text('latitude',null,array('class'=>'form-control col-sm-5','placeholder' => 'Latitude', 'id' => 'lat')) }}
                        <small class="help-block"></small>
                    </div>
                    <div id="longitude" class="col-sm-5">
                    {{ Form::text('longitude',null,array('class'=>'form-control col-sm-5','placeholder' => 'Longitude', 'id' => 'lng')) }}
                    <small class="help-block"></small>
                    </div>
                </div>

                <div  class="form-group">
                    <label for="phone_number" class="col-sm-3 control-label">Phone Number<span class="mandatory"> *</span></label>
                    <div id="phone_number" class="col-sm-9">
                        {{ Form::text('phone_number',null,array('class'=>'form-control phone','placeholder' => 'Phone Number [ format (XXX)XXX-XXXX ]')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div  class="form-group">
                    <label for="phone_number_ext" class="col-sm-3 control-label">Phone Ext</label>
                    <div id="phone_number_ext" class="col-sm-9">
                        {{ Form::text('phone_number_ext',null,array('class'=>'form-control','placeholder' => 'Phone Ext')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="office_hours_start_time" class="col-sm-3 control-label">Office Start Time<span class="mandatory"> *</span></label>
                    <div id="office_hours_start_time"  class="col-sm-9">
                        {{ Form::text('office_hours_start_time',null,array('class'=>'form-control timepicker','placeholder' => 'Office hours start time')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="office_hours_end_time" class="col-sm-3 control-label">Office End Time<span class="mandatory"> *</span></label>
                    <div id="office_hours_end_time"  class="col-sm-9">
                        {{ Form::text('office_hours_end_time',null,array('class'=>'form-control timepicker','placeholder' => 'Office hours end time')) }}
                        <small class="help-block"></small>
                    </div>
                </div>


                <div class="form-group">
                    <label for="special_instructions" class="col-sm-3 control-label">Special Instructions</label>
                    <div  id="special_instructions" class="col-sm-9">
                        {{ Form::textarea('special_instructions',null,['class' => 'form-control','id'=>'note','rows'=>'3']) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <!-- Active Toggle button - Start -->
                <div class="form-group" id="is_photo_service">
                    <label for="is_photo_service" class="col-sm-3 control-label">Enable Photo Service </label>
                    <div class="col-sm-9">
                        <label class="switch" style="">
                            <input name="is_photo_service" type="checkbox" value="1">
                            <span class="slider round"></span>
                        </label>
                        <small class="help-block"></small>
                    </div>
                </div>
            <!-- Active Toggle button - End -->


                <div class="edit-display">
                    <h4 >Slot Timings</h4>
                    <hr>
                </div>

                <div class="form-group edit-display">
                    <label for="start_time" class="col-sm-3 control-label">Start Time<span class="mandatory"> *</span></label>
                    <div id="start_time"  class="col-sm-9">
                        {{ Form::text('start_time',null,array('class'=>'form-control timepicker','placeholder' => 'Slot start time')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group edit-display">
                    <label for="end_time" class="col-sm-3 control-label">End Time<span class="mandatory"> *</span></label>
                    <div id="end_time"  class="col-sm-9">
                        {{ Form::text('end_time',null,array('class'=>'form-control timepicker','placeholder' => 'Slot end time')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div  class="form-group edit-display">
                    <label for="intervals" class="col-sm-3 control-label">Interval (In Minutes)<span class="mandatory"> *</span></label>
                    <div id="intervals" class="col-sm-9">
                        {{ Form::text('intervals',null,array('class'=>'form-control','placeholder' => 'Interval in minutes', 'id' => 'interval')) }}
                        <small class="help-block intervals_error_msg" id=""></small>
                    </div>
                </div>
                <div  class="form-group edit-display">
                    <label for="start_date" class="col-sm-3 control-label">Start Date<span class="mandatory"> *</span></label>
                    <div id="start_date" class="col-sm-9">
                        {{ Form::date('start_date',null,array('class'=>'form-control','placeholder' => 'Start Date','id'=>'startDate_val')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="office_ids">
                    <label for="colorcode" class="col-sm-3 control-label">Color Code</label>
                    <div class="col-sm-9">
                        {{ Form::color('icon_color_code',null,array('class' => 'form-control form-control-color', 'id'=>'icon_color_code', 'Placeholder'=>'Icon Colour', 'required'=>TRUE)) }}
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


<div class="modal fade" id="officeTimingModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 60% !important;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="createForm">   </h4>
            </div>

            {{ Form::open(array('url'=>'#','id'=>'office-timing-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('ids_office_id',null,array('id'=>'ids_office_id')) }}
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-3" style="margin-left: 17px;" id="start_time">
                            <label for="start_time">Start Time</label>
                            <input type="text" id="start_time_val"  name="start_time" class="form-control timepicker" placeholder="Start Time">
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group col-sm-3" id="end_time">
                            <label for="end_time">End Time</label>
                            <input type="text" id="end_time_val"  name="end_time" class="form-control timepicker" placeholder="End Time">
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group col-sm-3" id="start_date">
                            <label for="start_date">Start Date</label>
                            {{-- <input type="text" id="start_date_val"  name="start_date" class="form-control" placeholder="Start Date"> --}}
                            {{ Form::date('start_date',null,array('class'=>'form-control','placeholder' => 'Start Date','id'=>'start_date_val')) }}
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group col-sm-3" id="expiry_date">
                            <label for="expiry_date">End Date</label>
                            {{-- <input type="text" id="end_date_val"  name="end_date"  class="form-control" placeholder="End Date"> --}}
                            {{ Form::date('expiry_date',null,array('class'=>'form-control','placeholder' => 'End Date','id'=>'expiry_date_val')) }}
                            <small class="help-block"></small>
                        </div>

                    </div>
                    <div class="form-group col-sm-12">

                        <div class="form-group col-sm-2" id="intervals" style="margin-left: 17px;" >
                            <label for="intervals">Interval (In Minutes)</label>
                            <input type="text" id="intervals_val"  name="intervals" class="form-control" placeholder="Intervals">
                            <small class="help-block intervals_error_msg" id=""></small>
                        </div>

                        <div class="form-group col-sm-2" id="lunch_start_time" >
                            <label for="lunch_start_time">Lunch Start Time</label>
                            <input type="text" id="lunch_start_time_val"  name="lunch_start_time" class="form-control timepicker" placeholder="Lunch Start Time">
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group col-sm-2" id="lunch_end_time">
                            <label for="lunch_end_time">Lunch End Time</label>
                            <input type="text" id="lunch_end_time_val"  name="lunch_end_time" class="form-control timepicker" placeholder="Lunch End Time">
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group col-sm-3" > </div>
                        <div class="form-group col-sm-3" >
                            <button type="button" class="btn btn-primary blue pull-right resetForm" style="margin-top: 24px;"> Reset </button>
                            <button type="submit" class="btn btn-primary blue pull-right" id="timeFormSubmit" style="margin-top: 24px; margin-right: 2px;"> Submit </button>
                        </div>
                    </div>
                    <!-- <div class="form-group col-sm-2" style="margin-top: -4%;">
                        <button type="button" class="btn btn-primary blue pull-right resetForm" style="margin-top: 24px;"> Reset </button>
                        <button type="submit" class="btn btn-primary blue pull-right" id="timeFormSubmit" style="margin-top: 24px; margin-right: 2px;"> Submit </button>
                    </div> -->
                </div>

            {{ Form::close() }}


            <div class="modal-header">
                <h4 class="modal-title" id="timeList"></h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="officeTimingsTable">
                    <thead>
                        <tr>
                            {{-- <th>#</th> --}}
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Interval</th>
                            <th>Lunch Start Time</th>
                            <th>Lunch End Time</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="officeTimingsTbody">

                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary blue pull-right resetForm" data-dismiss="modal"> Cancel </button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="officeTimingEditModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 25%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title"> Update End Date </h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'office-timing-edit-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('ids_timing_id',null,array('id'=>'ids_timing_id')) }}
                <div class="modal-body">
                    <div class="form-group">
                        <label for="start_date" class="col-sm-3 control-label">Start Date</label>
                        <div  id="start_date" class="col-sm-9">
                            {{-- <span class="form-control" id='start_date_val'> </span> --}}
                            <label id="start_date_val" class="control-label view-form-element"> </label>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="expiry_date" class="col-sm-3 control-label">End Date</label>
                        <div  id="expiry_date" class="col-sm-9">
                            {{ Form::date('expiry_date',null,array('class'=>'form-control','id'=>'update_expiry_date','placeholder' => 'End date','required'=>'required')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary blue pull-right resetForm" style="margin-top: 24px;"> Reset </button>
                    <button type="submit" class="btn btn-primary blue pull-right" style="margin-top: 24px; margin-right: 2px;"> Submit </button>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>


@stop
@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}&libraries=places"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script>
    $(function () {
        $('#office-ids').select2();//Added Select2 to office-ids listing

        $('.timepicker').timepicki({
            show_meridian:false,
            min_hour_value:0,
            max_hour_value:23,
            step_size_minutes:15,
            overflow_minutes:true,
            increase_direction:'up',
        });
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#office-table').DataTable({

                ajax: {
                    "url": "{{ route('idsOffice.getAll') }}",
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dom: 'lfrtBip',
                buttons: [
                        {
                        extend: 'pdfHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            // columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            // columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        extend: 'print',
                        text: ' ',
                        className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            // columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        text: ' ',
                        className: 'btn btn-primary fa fa-envelope-o',
                        action: function (e, dt, node, conf) {
                            emailContent(table, 'Assignement Types');
                        }
                    }
                ],

                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'adress',
                        name: 'adress',
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number'
                    },
                    {
                        data: 'phone_number_ext',
                        name: 'phone_number_ext'
                    },
                    {
                        data: 'office_hours_start_time',
                        name: 'office_hours_start_time'
                    },
                    {
                        data: 'office_hours_end_time',
                        name: 'office_hours_end_time'
                    },
                    {
                        data: 'icon_color_code',
                        name: 'icon_color_code'
                    },
                    {
                        name: 'is_photo_service',
                        data: null,
                        orderable: false,
                        render: function (o) {
                            var actions = "";
                            if(o.is_photo_service == 1){
                                actions = 'Yes';
                            }else{
                                actions = 'No';
                            }
                            return actions;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function (o) {
                           var actions = "";

                           var office_slot_url = '{{ route("idsOffice.slot-page",":id") }}';
                           var office_slot_url = office_slot_url.replace(':id', o.id);

                           var office_slot_block_url = '{{ route("idsOffice.slot-block-page",":id") }}';
                           var office_slot_block_url = office_slot_block_url.replace(':id', o.id);

                                @can('edit_masters')
                                    actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                                @endcan
                                let timingsDetails = [];
                                timingsDetails = {
                                    officeId: o.id,
                                    name: o.name,
                                    ids_office_timings: o.ids_office_timings,
                                };
                                let timingsEncoded = btoa(JSON.stringify(timingsDetails))
                                actions += `<a href="#" class="fa fa-calendar office-card" data-event='${timingsEncoded}'></a>`
                                actions += '<a href="'+office_slot_url+'" class="fa fa-eye" data-id=' + o.id + '></a>'
                                actions += '<a href="'+office_slot_block_url+'" class="fa fa-ban" data-id=' + o.id + '></a>'
                                @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' +o.id + '></a>';
                                @endcan
                            return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

    /* Auto complete search - Start*/
        var input = document.getElementById('office_address');

        var autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('office_address'), {types: ['geocode'],
            componentRestrictions: {country: ["ca", "in"]}}
        );
        autocomplete.addListener('place_changed', function() {
          var place = autocomplete.getPlace();
          $('#lat').val(place.geometry.location.lat());
          $('#lng').val(place.geometry.location.lng());

        });
        $( "#office_address" ).keyup(function() {
           var office_address = $( "#office_address" ).val();
           if(office_address.length == 0){
            $('#lat').val('');
            $('#lng').val('');
           }
        });
    /* Auto complete search - End*/

        /* Office Store - Start*/
        $('#ids-office-form').submit(function (e) {
            e.preventDefault();
            if($('#ids-office-form input[name="id"]').val()){
                var message = 'Office has been updated successfully';
            }else{
                var message = 'Office has been created successfully';
            }
            IDSOfficeFormSubmit($('#ids-office-form'), "{{ route('idsOffice.store') }}", table, e, message);
        });
        /* Office Store - End*/

        /* Form submit - Start */
        function IDSOfficeFormSubmit($form, url, table, e, message) {
            var $form = $form;
            var url = url;
            var e = e;
            var table = table;
            var formData = new FormData($form[0]);
            return new Promise(function (resolve, reject) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        if (data.success) {
                            swal("Saved", message, "success");
                            $("#myModal").modal('hide');
                            if (table != null) {
                                table.ajax.reload();
                            }
                        } else if (data.success == false) {
                            if(data.message != ''){
                                $('.intervals_error_msg').html(data.message);
                            }
                        } else {
                            console.log(data);
                        }
                        resolve(data);
                    },
                    fail: function (response) {
                        resolve();
                    },
                    error: function (xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form);
                        resolve();
                    }, always: function () {
                        resolve();
                    },
                    contentType: false,
                    processData: false,
                });
            });
        }
        /* Form submit - End */

        $('#add-new-office').click(function(){
            $('#ids-office-form')[0].reset();
            $("#myModal").find('.edit-display').show();
            $("#myModal").modal();
            // $('#myModal .modal-title').text("Create Office")
            $('#interval').attr('readonly', false);
            $('#myModal textarea[name="special_instructions"]').html('');
        });

        /* Office Edit - Start*/
        $("#office-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("idsOffice.single",":id") }}';
            var url = url.replace(':id', id);
            $('#ids-office-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#interval').attr('readonly', true);
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        console.log(data);
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="name"]').val(data.name)
                        $('#myModal input[name="adress"]').val(data.adress)
                        $('#myModal input[name="latitude"]').val(data.latitude)
                        $('#myModal input[name="longitude"]').val(data.longitude)
                        $('#myModal input[name="phone_number"]').val(data.phone_number)
                        $('#myModal input[name="phone_number_ext"]').val(data.phone_number_ext)
                        $('#myModal input[name="office_hours_start_time"]').val(data.office_hours_start_time)
                        $('#myModal input[name="office_hours_end_time"]').val(data.office_hours_end_time)
                        // $('#myModal input[name="intervals"]').val(data.intervals)
                        $('#myModal textarea[name="special_instructions"]').html(data.special_instructions);
                        $('#myModal input[name="icon_color_code"]').val(data.icon_color_code);
                        $('#myModal input[name="is_photo_service"]').prop('checked', data.is_photo_service);
                        $("#myModal").find('.edit-display').hide();
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Office: " + data.name)
                    } else {
                        console.log(data);
                        swal("Oops", "Edit was unsuccessful", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                    if (xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                },
                contentType: false,
                processData: false,
            });
        });
        /* Office Edit - End*/

        /* Office Delete  - Start */
        $('#office-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('idsOffice.destroy',':id') }}";
            var url = base_url.replace(':id', id);

            var message = 'Office has been deleted successfully';
            let type = 1;
            // var table = table;
            deleteEntries(id,url, table, message,type);
        });
        /* Office Delete  - End */


        /**Start** On office timings
        * set office timings on modal
        */
        $("body").on("click", ".office-card", function(){

            let officeData = JSON.parse(atob($(this).data('event')));
            $('#officeTimingModal').modal();
            $('#officeTimingModal #createForm').text(officeData.name+' : Create new slot timings');
            $('#officeTimingModal #timeList').text(officeData.name+' : Slot timings');
            $('#office-timing-form')[0].reset();
            $('#ids_office_id').val(officeData.officeId);

            let tbody = '';
            $.each(officeData.ids_office_timings, function(index, value) {
                tbody += `<tr id="${value.id}">`;
                // tbody += `<td> ${index+1}</td>`;
                tbody += `<td> ${moment(value.start_date).format('MMMM D, YYYY')}</td>`;
                let end_date = '';
                if(value.expiry_date){
                    end_date = moment(value.expiry_date).format('MMMM D, YYYY');
                }
                tbody += `<td id="endDate_${value.id}"> ${end_date}</td>`;
                tbody += `<td> ${value.start_time}</td>`;
                tbody += `<td> ${value.end_time}</td>`;
                tbody += `<td> ${value.intervals}</td>`;
                let lunch_start_time = '';
                if(value.lunch_start_time){
                    lunch_start_time = value.lunch_start_time;
                }
                tbody += `<td> ${lunch_start_time}</td>`;
                let lunch_end_time = '';
                if(value.lunch_end_time){
                    lunch_end_time = value.lunch_end_time;
                }
                tbody += `<td> ${lunch_end_time}</td>`;
                tbody += `<td> ${moment(value.created_at).format('MMMM D, YYYY')}</td>`;
                tbody += `<td>
                            <a href="#" class="delete fa fa-trash-o" data-id='${value.id}' id='deleteIdsOfficeTiming'></a>`;
                if(!value.expiry_date){
                    tbody += ` <a href="#" class="edit fa fa-pencil editIdsOfficeTiming" data-id='${value.id}'
                    data-startDate='${value.start_date}' id='editIdsOfficeTiming_${value.id}'></a>`;
                }
                tbody += `</td>`;
                tbody += `</tr>`;

            });

            $('#officeTimingsTbody').html(tbody);
        });

        /* Office Time Store - Start*/
        $('#office-timing-form').submit(function (e) {
            e.preventDefault();
            $('#timeFormSubmit').prop('disabled', true);
            var message = 'Ids office timing has been created successfully';
            var $form = $('#office-timing-form');
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            var url = '{{ route("idsOffice.timing.store") }}';
            var e = e;
            // var table = table;
            var formData = new FormData($form[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    $('#timeFormSubmit').prop('disabled', false);
                    if (data.success) {
                        $('#office-timing-form')[0].reset();
                        let tbody = '';
                        let value = data.result;
                        tbody += `<tr id="${value.id}">`;
                        // tbody += `<td> ${index+1}</td>`;
                        tbody += `<td> ${moment(value.start_date).format('MMMM D, YYYY')}</td>`;
                        let end_date = '';
                        if(value.expiry_date){
                            end_date = moment(value.expiry_date).format('MMMM D, YYYY');
                        }
                        tbody += `<td id="endDate_${value.id}"> ${end_date}</td>`;
                        tbody += `<td> ${value.start_time}</td>`;
                        tbody += `<td> ${value.end_time}</td>`;
                        tbody += `<td> ${value.intervals}</td>`;
                        let lunch_start_time = '';
                        if(value.lunch_start_time){
                            lunch_start_time = value.lunch_start_time;
                        }
                        tbody += `<td> ${lunch_start_time}</td>`;
                        let lunch_end_time = '';
                        if(value.lunch_end_time){
                            lunch_end_time = value.lunch_end_time;
                        }
                        tbody += `<td> ${lunch_end_time}</td>`;
                        tbody += `<td> ${moment(value.created_at).format('MMMM D, YYYY')}</td>`;
                        tbody += `<td>
                                    <a href="#" class="delete fa fa-trash-o" data-id='${value.id}' id='deleteIdsOfficeTiming'></a>`;
                        if(!value.expiry_date){
                            tbody += ` <a href="#" class="edit fa fa-pencil editIdsOfficeTiming" data-id='${value.id}'
                             data-startDate='${value.start_date}' id='editIdsOfficeTiming_${value.id}'></a>`;
                        }
                        tbody += `</td>`;
                        tbody += `</tr>`;
                        $('#officeTimingsTbody').append(tbody);
                        swal("Saved", message, "success");
                        table.ajax.reload();
                    } else {

                        // console.log(data);
                        if(data.message != ''){
                            $('.intervals_error_msg').html(data.message);
                        }
                    }

                },
                fail: function (response) {
                    // console.log('Unknown error');
                    $('#timeFormSubmit').prop('disabled', false);
                },
                error: function (xhr, textStatus, thrownError) {
                    $('#timeFormSubmit').prop('disabled', false);
                    // console.log(xhr.responseJSON.errors);
                    associate_errors(xhr.responseJSON.errors, $form);
                    if (xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                },
                contentType: false,
                processData: false,
            });

        });
        /* Office Time Store - End*/

        /* Set Expiry Of Office Time  - Start */
        $('#officeTimingsTable').on('click', '.editIdsOfficeTiming', function (e) {
            var id = $(this).data('id');
            var startDate = $(this).attr("data-startDate")
            $('#officeTimingEditModal').modal();
            var $form = $('#office-timing-edit-form');
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#office-timing-edit-form')[0].reset();
            $('#ids_timing_id').val(id);
            $('#officeTimingEditModal #start_date_val').text(startDate);
        });

        /* Office Time Store - Start*/
        $('#office-timing-edit-form').submit(function (e) {
            e.preventDefault();
            var message = 'Ids office timing has been updated successfully';
            var $form = $('#office-timing-edit-form');
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            var url = '{{ route("idsOffice.timing.update") }}';
            let expiry_date = $('#update_expiry_date').val();
            let ids_timing_id = $('#ids_timing_id').val();
            var e = e;
            // var table = table;
            var formData = new FormData($form[0]);
            let startDate = $('#officeTimingEditModal #start_date_val').text();
            if(new Date(startDate) <= new Date(expiry_date)){

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        if (data.success) {
                            $('#'+'endDate_'+ids_timing_id).text(moment(expiry_date).format('MMMM D, YYYY'));
                            $('#officeTimingEditModal').modal('toggle');
                            $('#editIdsOfficeTiming_'+ids_timing_id).hide();
                            swal("Saved", message, "success");
                            $('#office-timing-edit-form')[0].reset();
                            table.ajax.reload();
                        } else {
                            swal("Warning", data.message, "warning");
                            console.log(data);
                        }
                    },
                    fail: function (response) {
                        console.log('Unknown error');
                    },
                    error: function (xhr, textStatus, thrownError) {
                        console.log(xhr.responseJSON.errors);
                        associate_errors(xhr.responseJSON.errors, $form);
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                    contentType: false,
                    processData: false,
                });

            }else{
                swal("Warning",'Expiry date must be grater than start date', "warning");
            }



        });
        /* Update Office Time Store - End*/

        /* Office Time Delete  - Start */
        $('#officeTimingsTable').on('click', '#deleteIdsOfficeTiming', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('idsOffice.timing.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Office time has been deleted successfully';
            let type = 0;
            deleteEntries(id,url, table, message,type);
        });

        /* Delete Record - Start */
        function deleteEntries(id,url, table, message,type) {
            var url = url;
            // var table = table;
            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action! Proceed?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, remove",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data.success) {
                            swal("Deleted", message, "success");
                            if (table != null) {
                                table.ajax.reload();
                            }
                            if(type != 1){
                                $('#'+id).hide();
                            }
                        } else if (data.success == false) {
                            if (Object.prototype.hasOwnProperty.call(data, 'message') && data.message) {
                                swal("Warning", data.message, "warning");
                            } else {
                                swal("Warning", 'Data exists', "warning");
                            }
                        } else if (data.warning == true) {
                            swal("Warning",data.message, "warning");
                        } else {
                            console.log(data);
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                        console.log(xhr.status);
                        console.log(thrownError);
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                    contentType: false,
                    processData: false,
                });
            });
        }
        /* Delete Record - End */

        $('body').on('click', '.resetForm', function (e) {
            var $form = $('#office-timing-form');
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#office-timing-form')[0].reset();
            $('#office-timing-edit-form')[0].reset();
        });


    });
</script>
<style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>
<script src="{{ asset('js/timepicki.js') }}"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel='stylesheet' type='text/css' href='{{ asset('css/timepicki.css') }}' />
@stop
