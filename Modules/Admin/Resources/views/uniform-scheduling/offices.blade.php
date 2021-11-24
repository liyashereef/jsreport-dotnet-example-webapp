@extends('adminlte::page')
@section('title', 'Uniform Scheduling Office')
@section('content_header')
<h1>Uniform Scheduling Office</h1>
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
{{-- <div class="add-new" id="add-new-office" data-title="Add New Offices">Add
    <span class="add-new-label">New</span>
</div> --}}
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
            {{ Form::open(array('url'=>'#','id'=>'office-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group row">
                    <label for="name" class="col-sm-3 control-label">Name <span class="mandatory"> *</span></label>
                    <div  id="name" class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control','placeholder' => 'Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="address" class="col-sm-3 control-label">Address<span class="mandatory"> *</span></label>
                    <div id="adress"  class="col-sm-9">
                    {{ Form::text('adress',null,array('class'=>'form-control','autocomplete'=>'false','placeholder' => 'Address', 'id' => 'office_address')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div  class="form-group row">
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

                <div  class="form-group row">
                    <label for="phone_number" class="col-sm-3 control-label">Phone Number<span class="mandatory"> *</span></label>
                    <div id="phone_number" class="col-sm-9">
                        {{ Form::text('phone_number',null,array('class'=>'form-control phone','placeholder' => 'Phone Number [ format (XXX)XXX-XXXX ]')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div  class="form-group row">
                    <label for="phone_number_ext" class="col-sm-3 control-label">Phone Ext</label>
                    <div id="phone_number_ext" class="col-sm-9">
                        {{ Form::text('phone_number_ext',null,array('class'=>'form-control','placeholder' => 'Phone Ext')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="office_start_time" class="col-sm-3 control-label">Office Start Time<span class="mandatory"> *</span></label>
                    <div id="office_start_time"  class="col-sm-9">
                        {{ Form::text('office_start_time',null,array('class'=>'form-control timepicker','placeholder' => 'Office hours start time')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="office_end_time" class="col-sm-3 control-label">Office End Time<span class="mandatory"> *</span></label>
                    <div id="office_end_time"  class="col-sm-9">
                        {{ Form::text('office_end_time',null,array('class'=>'form-control timepicker','placeholder' => 'Office hours end time')) }}
                        <small class="help-block"></small>
                    </div>
                </div>


                <div class="form-group row">
                    <label for="special_instructions" class="col-sm-3 control-label">Special Instructions</label>
                    <div  id="special_instructions" class="col-sm-9">
                        {{ Form::textarea('special_instructions',null,['class' => 'form-control','id'=>'note','rows'=>'3']) }}
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
                {{ Form::hidden('uniform_scheduling_office_id',null,array('id'=>'uniform_scheduling_office_id')) }}
                <div class="row">
                    <div class="form-group col-sm-2" style="margin-left: 17px;" id="start_time">
                        <label for="start_time">Start Time</label>
                        <input type="text" id="start_time_val"  name="start_time" class="form-control timepicker" placeholder="Start Time">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group col-sm-2" id="end_time">
                        <label for="end_time">End Time</label>
                        <input type="text" id="end_time_val"  name="end_time" class="form-control timepicker" placeholder="End Time">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group col-sm-2" id="start_date">
                        <label for="start_date">Start Date</label>
                        {{ Form::date('start_date',null,array('class'=>'form-control','placeholder' => 'Start Date','id'=>'start_date_val')) }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group col-sm-2" id="expiry_date">
                        <label for="expiry_date">End Date</label>
                        {{-- <input type="text" id="end_date_val"  name="end_date"  class="form-control" placeholder="End Date"> --}}
                        {{ Form::date('expiry_date',null,array('class'=>'form-control','placeholder' => 'End Date','id'=>'expiry_date_val')) }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group col-sm-2" id="intervals">
                        <label for="intervals">Interval (In Minutes)</label>
                        <input type="text" id="intervals_val"  name="intervals" class="form-control" placeholder="Intervals">
                        <small class="help-block intervals_error_msg" id=""></small>
                    </div>
                    <div class="form-group col-sm-2">
                        <button type="button" class="btn btn-primary blue pull-right resetForm" style="margin-top: 24px;"> Reset </button>
                        <button type="submit" class="btn btn-primary blue pull-right" id="timeFormSubmit" style="margin-top: 24px; margin-right: 2px;"> Submit </button>
                    </div>
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
            {{ Form::hidden('timing_id',null,array('id'=>'timing_id')) }}
                <div class="modal-body">
                    <div class="form-group">
                        <label for="start_date" class="col-sm-3 control-label">Start Date</label>
                        <div  id="start_date" class="col-sm-9">

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

<div class="modal fade" id="officeBlockModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 60% !important;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="createForm">   </h4>
            </div>

            {{ Form::open(array('url'=>'#','id'=>'office-block-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('uniform_scheduling_office_id',null,array('id'=>'uniform_scheduling_office_id')) }}
                <div class="row">
                    <div class="form-group col-sm-2" id="day_id" style="margin-left: 17px;">
                        <label for="day_id">Day</label>
                        {{ Form::select('day_id',['Please Select']+$days,null,array('class'=>'form-control','id'=>'day_id_val')) }}
                        <small class="help-block"></small>
                    </div>

                    <div class="form-group col-sm-2" id="start_date" style="margin-left: 17px;">
                        <label for="start_date">Start Date</label>

                        {{ Form::date('start_date',null,array('class'=>'form-control','placeholder' => 'Start Date','id'=>'start_date_val')) }}
                        <small class="help-block"></small>
                    </div>

                    <div class="form-group col-sm-2" id="end_date">
                        <label for="end_date">End Date</label>
                        {{ Form::date('end_date',null,array('class'=>'form-control','placeholder' => 'End Date','id'=>'block_expiry_date_val')) }}
                        <small class="help-block"></small>
                    </div>

                    <div class="form-group col-sm-2"  id="start_time">
                        <label for="start_time">Start Time</label>
                        <input type="text" id="start_time_val"  name="start_time" class="form-control timepicker" placeholder="Start Time">
                        <small class="help-block"></small>
                    </div>

                    <div class="form-group col-sm-2" id="end_time">
                        <label for="end_time">End Time</label>
                        <input type="text" id="block_end_time_val"  name="end_time" class="form-control timepicker" placeholder="End Time">
                        <small class="help-block"></small>
                    </div>

                    <div class="form-group col-sm-2">
                        <button type="button" class="btn btn-primary blue pull-right resetForm" style="margin-top: 24px;"> Reset </button>
                        <button type="submit" class="btn btn-primary blue pull-right" id="blockFormSubmit" style="margin-top: 24px; margin-right: 2px;"> Submit </button>
                    </div>
                </div>
            {{ Form::close() }}


            <div class="modal-header">
                <h4 class="modal-title" id="blockList"></h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="officeBlockTable">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Start Date</th>
                            <th>Start Time</th>
                            <th>End Date</th>
                            <th>End Time</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="officeBlocksTbody">

                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary blue pull-right resetForm" data-dismiss="modal"> Cancel </button>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="officeBlockEditModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 25%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title"> Update End Date on Block Entry </h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'office-block-edit-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('block_id',null,array('id'=>'block_id')) }}
                <div class="modal-body">
                    <div class="form-group">
                        <label for="start_date" class="col-sm-3 control-label">Start Date</label>
                        <div  id="start_date" class="col-sm-9">
                            <label id="start_date_val" class="control-label view-form-element"> </label>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="end_date" class="col-sm-3 control-label">End Date</label>
                        <div  id="end_date" class="col-sm-9">
                            {{ Form::date('end_date',null,array('class'=>'form-control','id'=>'update_end_date','placeholder' => 'End date','required'=>'required')) }}
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
        $('#office-form').submit(function (e) {
            e.preventDefault();
            if($('#office-form input[name="id"]').val()){
                var message = 'Office has been updated successfully';
            }else{
                var message = 'Office has been created successfully';
            }
            officeFormSubmit($('#office-form'), "{{ route('uniform-scheduling.offices.store') }}", table, e, message);
        });
        /* Office Store - End*/

        /* Form submit - Start */
        function officeFormSubmit($form, url, table, e, message) {
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

        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#office-table').DataTable({

                ajax: {
                    "url": "{{ route('uniform-scheduling.office.lists') }}",
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
                        data: 'office_start_time',
                        name: 'office_start_time'
                    },
                    {
                        data: 'office_end_time',
                        name: 'office_end_time'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function (o) {
                           var actions = "";
                            let timingsDetails = [];
                            timingsDetails = {
                                officeId: o.id,
                                name: o.name,
                                office_timings: o.uniform_scheduling_office_timings,
                                office_timings_blocked: o.uniform_scheduling_office_slot_blocks,
                            };
                            actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                            let timingsEncoded = btoa(JSON.stringify(timingsDetails))
                            actions += `<a href="#" class="fa fa-calendar office-card" data-event='${timingsEncoded}'></a>`
                            actions += `<a href="#" class="fa fa-ban blocked-card" data-event='${timingsEncoded}'></a>`
                            return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }


        $('#add-new-office').click(function(){
            $('#office-form')[0].reset();
            // $("#myModal").find('.edit-display').show();
            $("#myModal").modal();
            // $('#myModal .modal-title').text("Create Office")
            // $('#interval').attr('readonly', false);
            $('#myModal textarea[name="special_instructions"]').html('');
        });
        /* Office Edit - Start*/
        $("#office-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("uniform-scheduling.office.single",":id") }}';
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
                        $('#myModal input[name="office_start_time"]').val(data.office_start_time)
                        $('#myModal input[name="office_end_time"]').val(data.office_end_time)
                        // $('#myModal input[name="intervals"]').val(data.intervals)
                        $('#myModal textarea[name="special_instructions"]').html(data.special_instructions);

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

        /**Start** On office timings
        * set office timings on modal
        */
        $("body").on("click", ".office-card", function(){

            let officeData = JSON.parse(atob($(this).data('event')));
            $('#officeTimingModal').modal();
            $('#officeTimingModal #createForm').text(officeData.name+' : Create new slot timings.');
            $('#officeTimingModal #timeList').text(officeData.name+' : slot timings.');
            $('#office-timing-form')[0].reset();
            $('#uniform_scheduling_office_id').val(officeData.officeId);

            let tbody = '';
            $.each(officeData.office_timings, function(index, value) {
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
                tbody += `<td> ${moment(value.created_at).format('MMMM D, YYYY')}</td>`;
                tbody += `<td>
                            <a href="#" class="delete fa fa-trash-o" data-id='${value.id}' id='deleteOfficeTiming'></a>`;
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
            var message = 'Office timing has been created successfully';
            var $form = $('#office-timing-form');
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            var url = '{{ route("uniform-scheduling.offices.timings-store") }}';
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
                        tbody += `<td> ${moment(value.created_at).format('MMMM D, YYYY')}</td>`;
                        tbody += `<td>
                                    <a href="#" class="delete fa fa-trash-o" data-id='${value.id}' id='deleteOfficeTiming'></a>`;
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
            $('#timing_id').val(id);
            $('#officeTimingEditModal #start_date_val').text(startDate);
        });

        /* Office Time Store - Start*/
        $('#office-timing-edit-form').submit(function (e) {
            e.preventDefault();
            var message = 'Office timing has been updated successfully';
            var $form = $('#office-timing-edit-form');
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            var url = '{{ route("uniform-scheduling.offices.timings-update") }}';
            let expiry_date = $('#update_expiry_date').val();
            let timing_id = $('#timing_id').val();
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
                            $('#'+'endDate_'+timing_id).text(moment(expiry_date).format('MMMM D, YYYY'));
                            $('#officeTimingEditModal').modal('toggle');
                            $('#editIdsOfficeTiming_'+timing_id).hide();
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
        $('#officeTimingsTable').on('click', '#deleteOfficeTiming', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('uniform-scheduling.offices.timings.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Office time has been deleted successfully';
            let type = 0;
            deleteEntries(id,url, table, message,type);
        });



/**END** Office timings   Management  */

/**START* Time Block Management section  */
        $("body").on("click", ".blocked-card", function(){

            let officeData = JSON.parse(atob($(this).data('event')));
            $('#officeBlockModal').modal();
            $('#officeBlockModal #createForm').text(officeData.name+' : Create new slot block.');
            $('#officeBlockModal #blockList').text(officeData.name+' : slot block entries.');
            $('#office-block-form')[0].reset();
            $('#office-block-form #uniform_scheduling_office_id').val(officeData.officeId);
            let tbody = '';
            $.each(officeData.office_timings_blocked, function(index, value) {
                tbody += `<tr id="${value.id}">`;
                // tbody += `<td> ${index+1}</td>`;
                let dayName = '--';
                if(value.day_id){
                    dayName = value.day.name;
                }
                tbody += `<td> ${dayName}</td>`;
                tbody += `<td> ${moment(value.start_date).format('MMMM D, YYYY')}</td>`;
                tbody += `<td> ${value.start_time}</td>`;
                let end_date = '';
                if(value.end_date){
                    end_date = moment(value.end_date).format('MMMM D, YYYY');
                }
                tbody += `<td> ${end_date}</td>`;
                tbody += `<td> ${value.end_time}</td>`;
                tbody += `<td> ${moment(value.created_at).format('MMMM D, YYYY')}</td>`;
                tbody += `<td>
                <a href="#" class="delete fa fa-trash-o" data-id='${value.id}' id='deleteOfficeBlock'></a>`;
                if(end_date == ''){
                    tbody += ` <a href="#" class="edit fa fa-pencil editOfficeBlock" data-id='${value.id}'
                    data-startDate='${value.start_date}' id='editOfficeBlock_${value.id}'></a>`;
                }
                tbody += `</td>`;
                tbody += `</tr>`;

            });

            $('#officeBlocksTbody').html(tbody);

        });

        /* Office Time Store - Start*/
        $('#office-block-form').submit(function (e) {
            e.preventDefault();
            $('#blockFormSubmit').prop('disabled', true);
            var message = 'Office block has been created successfully. Please reschedule blocked slots bookings.';
            var $form = $('#office-block-form');
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            var url = '{{ route("uniform-scheduling.offices.block.store") }}';
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
                    $('#blockFormSubmit').prop('disabled', false);
                    if (data.success) {
                        $('#office-block-form')[0].reset();
                        let tbody = '';
                        let value = data.result;
                        tbody += `<tr id="${value.id}">`;
                        // tbody += `<td> ${index+1}</td>`;
                        tbody += `<td> ${data.dayName}</td>`;
                        tbody += `<td> ${moment(value.start_date).format('MMMM D, YYYY')}</td>`;
                        tbody += `<td> ${value.start_time}</td>`;
                        let end_date = '';
                        if(value.end_date){
                            end_date = moment(value.end_date).format('MMMM D, YYYY');
                        }
                        tbody += `<td id="endDate_${value.id}"> ${end_date}</td>`;
                        tbody += `<td> ${value.end_time}</td>`;
                        tbody += `<td> ${moment(value.created_at).format('MMMM D, YYYY')}</td>`;
                        tbody += `<td>
                                    <a href="#" class="delete fa fa-trash-o" data-id='${value.id}' id='deleteOfficeBlock'></a>`;
                        if(end_date == ''){
                            tbody += ` <a href="#" class="edit fa fa-pencil editOfficeBlock" data-id='${value.id}'
                            data-startDate='${value.start_date}' id='editOfficeBlock_${value.id}'></a>`;
                        }
                        tbody += `</td>`;
                        tbody += `</tr>`;
                        $('#officeBlocksTbody').append(tbody);
                        swal("Saved", message, "success");
                        table.ajax.reload();
                    } else {

                        if(data.message != ''){
                            swal("Warning", data.message, "warning");
                            // $('.intervals_error_msg').html(data.message);
                        }
                    }
                },
                fail: function (response) {
                    // console.log('Unknown error');
                    $('#blockFormSubmit').prop('disabled', false);
                },
                error: function (xhr, textStatus, thrownError) {
                    $('#blockFormSubmit').prop('disabled', false);
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


        /* Office Time Delete  - Start */
        $('#officeBlockTable').on('click', '#deleteOfficeBlock', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('uniform-scheduling.offices.block.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Office time block has been deleted successfully';
            let type = 0;
            deleteEntries(id,url, table, message,type);
        });

        /* Set Expiry Of Office Time  - Start */
        $('#officeBlockTable').on('click', '.editOfficeBlock', function (e) {
            var id = $(this).data('id');
            var startDate = $(this).attr("data-startDate")
            $('#officeBlockEditModal').modal();
            var $form = $('#office-block-edit-form');
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#office-block-edit-form')[0].reset();
            $('#block_id').val(id);
            $('#officeBlockEditModal #start_date_val').text(startDate);
        });

        /* Office block edit - Start*/
        $('#office-block-edit-form').submit(function (e) {
            e.preventDefault();
            var message = 'Office block entry has been updated successfully';
            var $form = $('#office-block-edit-form');
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            var url = '{{ route("uniform-scheduling.offices.block-update") }}';
            let expiry_date = $('#update_end_date').val();
            let block_id = $('#block_id').val();
            var e = e;
            // var table = table;
            var formData = new FormData($form[0]);
            let startDate = $('#officeBlockEditModal #start_date_val').text();
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
                            $('#'+'endDate_'+block_id).text(moment(expiry_date).format('MMMM D, YYYY'));
                            $('#officeBlockEditModal').modal('toggle');
                            $('#editOfficeBlock_'+block_id).hide();
                            swal("Saved", message, "success");
                            $('#office-block-edit-form')[0].reset();
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

/**END* Time Block Management section  blocked-card*/

        $('body').on('click', '.resetForm', function (e) {
            var $form = $('#office-timing-form');
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#office-timing-form')[0].reset();
            $('#office-timing-edit-form')[0].reset();
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
