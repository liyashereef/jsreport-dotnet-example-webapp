@extends('layouts.app')
@section('content')
<style>
    .error {
        color: red;
    }

    /* Set the size of the div element that contains the map */
    #map {
        height: 905px;
        width: 100%;
        cursor: pointer;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col">
            @if(Session::has('flash_message'))
            <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
            @endif

            <form action=" {!! route('dispatchrequest.store') !!}" method="POST" name="createForm">
                @csrf
                <div class="modal-dialog" style="margin:20px; ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title table_title" id="myModalLabel">Dispatch Request Form</h4>
                        </div>

                        <div class="modal-body">
                            <div class="form-group" id="severity">
                                <label for="subject" class="col-sm-3 control-label">Subject</label>
                                <div class="col-sm-11">
                                    <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required>
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="severity">
                                <label for="dispatch_request_type_id" class="col-sm-3 control-label">Issue Type</label>
                                <div class="col-sm-11">
                                    <select class="form-control" required name="dispatch_request_type_id" id="dispatch_request_type_id">
                                        <option value="" selected>Select Issue Type</option>
                                        @foreach($requestTypes as $requestType)
                                        <option value="{{$requestType->id}}">{{$requestType->name}}</option>
                                        @endforeach
                                    </select>
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label">Existing Customer?</label>
                                    <input type="checkbox" name="is_existing_customer" value="1" checked style="margin-left: 19px;">
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group" id="project_name_section">
                                <label for="customer_id" class="col-sm-3 control-label">Project Name</label>
                                <div class="col-sm-11">
                                    <select class="form-control" required name="customer_id" id="customer_id">
                                        <option value="" selected>Select Project Name</option>
                                        @foreach($customers as $key => $customer)
                                        <option value="{{$key}}">{{$customer}}</option>
                                        @endforeach
                                    </select>
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group" id="name_section">
                                <label for="name" class="col-sm-3 control-label">Name</label>
                                <div class="col-sm-11">
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group" id="severity">
                                <label for="site_address" class="col-sm-3 control-label">Site Address</label>
                                <div class="col-sm-11">
                                    <input type="text" class="form-control" name="site_address" id="site_address" placeholder="Site Address" required>
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group" id="severity">
                                <label for="site_postalcode" class="col-sm-3 control-label">Postal Code</label>
                                <div class="col-sm-11">
                                    <input type="text" class="form-control" name="site_postalcode" id="site_postalcode" placeholder="Postal Code" required>
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group" id="severity">
                                <label for="rate" class="col-sm-3 control-label">Rate</label>
                                <div class="col-sm-11">
                                    <input type="text" class="form-control" name="rate" id="rate" placeholder="Rate" required maxlength="10">
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group" id="concern">
                                <label for="description" class="col-sm-3 control-label">Description</label>
                                <div class="col-sm-11">
                                    <textarea class="form-control" name="description" id="description" cols="50" rows="5" placeholder="Description"></textarea>
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group" id="severity">
                                <label for="notify_customer_ids" class="col-sm-5 control-label">Select customer to notify allocated employees</label>
                                <div class="col-sm-11">
                                    <select class="form-control" id="notify_customer_ids" name="notify_customer_ids[]" multiple>
                                        @foreach($customers as $key => $customer)
                                        <option value="{{$key}}">{{$customer}}</option>
                                        @endforeach
                                    </select>
                                    <small class="help-block"></small>
                                </div>
                            </div>


                            <div class="form-group" id="severity">
                                <label for="site_postalcode" class="col-sm-3 control-label">Location</label>
                                <div class="col-sm-11">

                                    <input type="text" class="form-control" readonly class="form-control" placeholder="Latitude" name="latitude" id="latitude"> <br />
                                    <input type="text" class="form-control" readonly class="form-control" placeholder="Longitude" name="longitude" id="longitude">
                                    <small class="help-block"></small>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <input class="btn btn-primary blue" type="button" value="Cancel" id="cancel_form">
                        </div>

                    </div>
                </div>

            </form>
        </div>
        <div class="col">
            <div id="map"></div>

        </div>
    </div>
</div>




@stop
@section('scripts')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}"></script>
<script>
    // Initialize and add the map
    function initMap(lat, lng) {

        var location = {
            lat: lat,
            lng: lng
        };

        if (lat == '' || lng == '') {
            location = {
                lat: parseFloat({{config('globals.map_default_center_lat')}}),
                lng: parseFloat({{config('globals.map_default_center_lng') }})
            };
        }


        // The map, centered at given location
        var map = new google.maps.Map(
            document.getElementById('map'), {
                zoom: 8,
                center: location,
                gestureHandling: 'greedy'
            });

        if (lat != '' || lng != '') {
            $("#latitude").val(lat);
            $("#longitude").val(lng);
            //The marker, positioned at given location
            var marker = new google.maps.Marker({
                position: location,
                map: map,
                draggable: true
            });

            google.maps.event.addListener(marker, 'dragend', function(marker) {
                var latLng = marker.latLng;
                $("#latitude").val(latLng.lat());
                $("#longitude").val(latLng.lng());
            });

        } else {
            $("#latitude").val('');
            $("#longitude").val('');
        }

    }

    function defaultMapLoad(lat = '', lng = '') {
        // Defalut map load  
        initMap(lat, lng);
    }

    $(document).ready(function() {


        defaultMapLoad();

        //Adding select2 on customer listing 
        $('#customer_id').select2();

        //initialize select-2 for notify_customer_ids
        $('#notify_customer_ids').select2({
            placeholder: "Enter Customer Name or Number.",
        });

        //If Existing Customer show project list, If not provide a text box to  functionality.
        $('#name_section').hide();
        $('input[type="checkbox"]').click(function() {
            if ($(this).prop("checked") == true) {
                $('#name_section').hide();
                $('#project_name_section').show();
            } else if ($(this).prop("checked") == false) {
                $('#name_section').show();
                $('#project_name_section').hide();

                $("#customer_id").val('');
                $("#site_address").val('');
                $("#site_postalcode").val('');
                defaultMapLoad();
            }
        });

        // Postal code chage load map again.
        $("#site_postalcode").keyup(function() {
            // Defalut map load  
            var lat = '';
            var long = '';

            var site_postalcode = $("#site_postalcode").val();

            if (site_postalcode.length >= 6) {

                var url_get_location = '{{ route("dispatch_request.location_by_postal_code",":postal_code") }}';
                url_get_location = url_get_location.replace(':postal_code', site_postalcode);

                $.ajax({
                    url: url_get_location,
                    method: 'GET',
                    success: function(data) {
                        if (data) {
                            if (data.lat != null && data.long != null) {
                                lat = parseFloat(data.lat);
                                long = parseFloat(data.long);
                            } else {
                                swal("Alert", "Data not found. Try again.", "warning");
                            }
                            initMap(lat, long);
                        } else {
                            swal("Alert", "Data not found. Try again.", "warning");
                            initMap(lat, long);
                        }
                    },
                    error: function(xhr, textStatus, thrownError) {
                        swal("Oops", "Something went wrong", "warning");
                    },
                });

            }

            // initMap(lat, long);
        });

        $("#customer_id").change(function() {

            var customer_id = $("#customer_id").val();
            if (customer_id == '') {
                $("#site_address").val('');
                $("#site_postalcode").val('');
                defaultMapLoad();
            } else {

                var view_url = '{{ route("dispatch_request.customer_details",":id") }}';
                view_url = view_url.replace(':id', customer_id);

                $.ajax({
                    url: view_url,
                    method: 'GET',
                    success: function(data) {
                        if (data) {
                            $("#site_address").val(data.address + ',' + data.city);
                            $("#site_postalcode").val(data.postal_code);

                            initMap(parseFloat(data.geo_location_lat), parseFloat(data.geo_location_long));
                        } else {
                            swal("Alert", "Data not found. Try again.", "warning");
                        }
                    },
                    error: function(xhr, textStatus, thrownError) {
                        swal("Oops", "Something went wrong", "warning");
                    },
                });
            }


        });

        $("#dispatch_request_type_id").change(function() {

            var dispatch_request_type_id = $("#dispatch_request_type_id").val();
            var view_url = '{{ route("dispatch_request.request_type",":id") }}';
            view_url = view_url.replace(':id', dispatch_request_type_id);
            $.ajax({
                url: view_url,
                method: 'GET',
                success: function(data) {
                    if (data) {
                        $("#rate").val(data.rate);

                    } else {
                        swal("Alert", "Data not found. Try again.", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {

                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
            });

        });


    });


    $(function() {

        $("form[name='createForm']").validate({

            rules: {
                customer_id: {
                    required: true
                },
                subject: "required",
                // longitude: "required",
                rate: {
                    required: true,
                    maxlength: 10,
                    numericOnly: true
                    // digits: true
                },
                site_postalcode: "required",
                site_address: "required",
                dispatch_request_type_id: {
                    required: true
                },
                notify_customer_ids: "required",
                latitude: "required",
                longitude: "required",

            },
            // Specify validation error messages
            messages: {
                subject: "Please enter subject",
                // longitude: "Please select a location in map",
                rate: {
                    required: "Please enter rate",
                    maxlength: "Only 10 characters allowed",
                    digits: "this field can only contain numbers"
                },
                // rate: "Please enter rate",
                site_postalcode: "Please enter postal code",
                site_address: "Please enter address",
                dispatch_request_type_id: "Please select issue type",
                customer_id: "Please select project name",
                notify_customer_ids: "Please select customer to notify allocated employees",
               

            },
            submitHandler: function(form) {
                form.submit();
            }
        });

    });

    $('#cancel_form').on('click', function(e) {
        // window.location.replace('/learningandtraining/teams');
        window.location.replace("{{ route('dispatchrequest.index') }}");
    });


    $.validator.addMethod('numericOnly', function(value) {
        return /^[0-9]+$/.test(value);
    }, 'Please only enter numeric values (0-9)');
</script>



@stop