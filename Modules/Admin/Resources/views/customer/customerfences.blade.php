{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Customers')

@section('content_header')
<div class="row">
    <div class="col-md-4" style="font-size:18px;padding-top:7px">Customer Fences</div>
    <div class="col-md-3">
        <select id="customerselect" class="form-control">
            <option value="-1">Select a Customer</option>
            @foreach ($customers as $customer)
            <option value="{{$customer->id}}">{{$customer->project_number}} - {{$customer->client_name}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-2"><button type="button" id="fencebuttonaddnew" class="btn btn-primary">Add/Edit Fence</button></div>
    <div class="col-sm-2"><button type="button" id="savemap" class="btn btn-primary" style="display:none">Save Map</button></div>
</div>

@stop

@section('content')
<div id="myModal" class="modal fade" data-backdrop="static" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                 <h4 class="modal-title">Add/Edit Fence</h4>
            </div>

            <div class="modal-body">

                <div class="row plotarea" class="form-control" style="padding:5px">
                    <div class="col-md-3">
                        <label> Location </label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="title">
                    </div>
                </div>

                <div class="row plotarea" class="form-control" style="padding:5px">
                    <div class="col-md-3">
                        <label> Address</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" id="addressfence" class="form-control" />
                    </div>
                </div>

                <div class="row plotarea" class="form-control" style="padding:5px">
                    <div class="col-md-3">
                        <label> Contractual Visit</label>
                    </div>
                    <div class="col-md-3">
                        <input style="" type="number" min="1" id="contractual_visit" class="form-control  restrictdec" placeholder="Contractual Visits" />
                    </div>
                    <div class="col-md-3">
                        <label> Visits per Shift/Person</label>
                    </div>
                    <div class="col-md-3">
                        <input style="" type="number" min="1" id="visitsfence"  class="form-control restrictdec" placeholder="Visits per Shift" />
                    </div>
                </div>

                <div class="row plotarea" class="form-control" style="padding:5px">
                    <div class="col-md-3">
                        <label> Radius</label>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control  restrictdec" id="radiusfence">
                    </div>
                    <div class="col-md-3">
                        <label> Longitude</label>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" id="longfence">
                    </div>
                </div>

                <div class="row plotarea" class="form-control" style="padding:5px">
                    <div class="col-md-3">
                        <label> Latitude</label>
                    </div>
                    <div class="col-md-3">
                        <input style="" type="number"  id="latfence" class="form-control" placeholder="Latitude" />
                    </div>
                </div>

                <div class="row plotarea" class="form-control" style="padding:5px">
                    <div class="col-md-5"></div>
                    <div class="col-md-1">
                        <button type="button" id="fencebutton" class="btn btn-primary">Save</button>
                    </div>
                    <div class="col-md-1">
                        <button type="button" id="fencebuttoncancel" class="btn btn-primary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <input type="hidden" name="fencecount" id="fencecount" value="0" />
            <input type="hidden" name="booleanedit" id="booleanedit" value="0" />
            <input type="hidden" name="whichfence" id="whichfence" value="0" />
            <input type="hidden" name="addnewfence" id="addnewfence" value="-1" />
            <input type="hidden" name="customer_id" id="customer_id" value="" />
            <input type="hidden" name="renderedfence" id="renderedfence" value="0" />
            <input type="hidden" name="datatablepage" id="datatablepage" value="0" />
        </div>
    </div>
</div>

<div role="tabpanel">
    <ul class="nav nav-tabs" role="tablist" id="userTabs">
        <li role="presentation" class="active"><a href="#fencedetailsTab" aria-controls="fencedetailsTab" role="tab" data-toggle="tab">Fences</a></li>
        <li role="presentation"><a id="edit_tab_map" href="#fencemapTab" aria-controls="fencemapTab" role="tab" data-toggle="tab">Maps</a></li>

    </ul>
    <div class="tab-content tab-alignment" style="margin-top:7px">
        <div id="message"></div>
        @if(Session::has('customer-updated'))
        <div id="import-success-alert" class="alert alert-info fade in alert-dismissible" role="alert" style="width:50%;">
            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
            {{ Session::get('customer-updated') }}
        </div>
        @endif

        <div role="tabpanel" class="tab-pane active" id="fencedetailsTab">
            <div class="row">
            </div>
            <div class="" >
                <div id="fencerowsedit" style=""></div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="fencemapTab">
            <div class="row " id="fencemaparea" style="width:100%;height:1000px;border-radius:4px;margin-top:20px;margin-left:10px">
                <div class="col-md-12">
                    Map area
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<style>
    ul li {
        list-style: none;
    }
    hr {
        border: none;
        height: 10px;
        /* Set the hr color */
        color: #333;
        /* old IE */
        background-color: #333;
        /* Modern Browsers */
    }
</style>
<script>
    function initializefence(myCenter, radius, jqdata) {
        var renderContainer = document.getElementById("fencemaparea");
        var mapProp = {
            center: myCenter,
            gestureHandling: 'greedy',
            zoom: 8,
        };

        var input = document.getElementById('maparea');
        //new google.maps.places.Autocomplete(input);

        var map = new google.maps.Map(renderContainer, mapProp, {
            gestureHandling: 'greedy',
        });

        //Marker in the Map
        if (jqdata == null) {
            var marker = new google.maps.Marker({
                position: myCenter,
                draggable: true,
                //animation: google.maps.Animation.DROP,
            });
            //marker.setMap(map);
        }
        //Circle in the Map
        var circle = new google.maps.Circle({
            center: myCenter,
            map: map,
            radius: 1000, // IN METERS.
            fillColor: '#FF6600',
            fillOpacity: 0.3,
            strokeColor: "#FFF",
            strokeWeight: 1,
            draggable: true,
            editable: true
        });
        //circle.setMap(map);
        //changing the radius of circle on changing the numeric field value
        $("#radius").on("change paste keyup keydown", function () {
            //radius = $("#radius").val();
            //circle.setRadius(Number($("#radiusfence").val()));
            circle.setRadius(Number(1000));
        });
        //Add listner to change latlong value on dragging the marker

        var bounds = new google.maps.LatLngBounds();
        var collection = [];
        var i = 0;
        var bounds = new google.maps.LatLngBounds();
        $.each(jqdata, function (key, value) {
            i++;
            var id = value[0];
            var title = value[1];
            var address = value[2];
            var lat = value[3];
            var long = value[4];
            var rad = parseInt(value[5]);
            var visitcount = parseInt(value[6]);
            var contractualVisits = parseInt(value[8]);
            //Circle in the Map
            var dycenter = new google.maps.LatLng(lat, long);

            var dyncircle = new google.maps.Circle({
                center: dycenter,
                map: map,
                radius: rad, // IN METERS.
                fillColor: '#FF6600',
                fillOpacity: 0.3,
                strokeColor: "#FFF",
                strokeWeight: 1,
                draggable: true,
                editable: true
            });
            if (i == 1) {
                dyncircle.setMap(map);
            }

            var dynmarker = new google.maps.Marker({
                position: new google.maps.LatLng(lat, long),
                map: map,
                draggable: true,
                raiseOnDrag: true,
                radius: rad,
                fillColor: '#FF6600',
                label: {
                    color: 'black',
                    fontWeight: 'normal',
                    fontSize: '10',
                    background: '#fff',
                    text: title,
                },
            });

            bounds.extend(dynmarker.getPosition());
            map.fitBounds(bounds);
            dynmarker.id = id;
            dynmarker.setMap(map);
            if (i == 1) {
                map.setCenter(new google.maps.LatLng(lat, long));
                //dynmarker.setCenter(new google.maps.LatLng(lat, long));
            }
            dyncircle.addListener('dragend', function (event) {
                $('#title').val(title);
                $('#addressfence').val(address);
                $('#latfence').val(event.latLng.lat());
                $('#longfence').val(event.latLng.lng());
                $('#contractual_visit').val(contractualVisits);
                $("#savemap").css("display", "block");
                if ($("#whichfence").val() > 0 && $('#latfence').val() != "")
                {
                } else
                {
                    $('#radiusfence').val(rad);
                }
                $('#visitsfence').val(visitcount);
                $("#booleanedit").val("1");
                $("#whichfence").val(id);
                $("#addnewfence").val(id);
            });

            // bounds.extend(dynmarker.position);
            dynmarker.addListener('dragend', function (event) {
                $('#title').val(title);
                $('#addressfence').val(address);
                $('#latfence').val(event.latLng.lat());
                $('#longfence').val(event.latLng.lng());
                $('#contractual_visit').val(contractualVisits);
                $("#savemap").css("display", "block");
                if ($("#whichfence").val() > 0 && $('#latfence').val() != "")
                {
                } else
                {
                    $('#radiusfence').val(rad);
                }
                $('#visitsfence').val(visitcount);
                $("#booleanedit").val("1");
                $("#whichfence").val(id);
                $("#addnewfence").val(id);
            });

            dynmarker.addListener('click', function (event) {
                $('#title').val(title);
                $('#addressfence').val(address);
                $('#latfence').val(event.latLng.lat());
                $('#longfence').val(event.latLng.lng());
                if ($("#whichfence").val() > 0 && $('#latfence').val() != "")
                {
                } else{
                    $('#radiusfence').val(rad);
                }
                $('#visitsfence').val(visitcount);
                $("#booleanedit").val("1");
                $("#whichfence").val(id);
                $("#addnewfence").val(id);
                $('#contractual_visit').val(contractualVisits);
            });

            //Add event listner on drag event of marker
            dynmarker.addListener('drag', function (event) {
                dyncircle.setOptions({
                    center: {
                        lat: event.latLng.lat(),
                        lng: event.latLng.lng()
                    }
                });
            });
            //markers.push(dynmarker);

            //Add listner to change radius value on field
            dyncircle.addListener('radius_changed', function (event) {
                $("#savemap").css("display", "block");
                $('#radiusfence').val(dyncircle.getRadius());
                $('#title').val(title);
                $('#addressfence').val(address);
                if ($("#whichfence").val() > 0 && $('#latfence').val() != "")
                {
                } else
                {
                    $('#latfence').val(lat);
                    $('#longfence').val(long);
                }
                //
                $('#radiusfence').val(dyncircle.getRadius());

                $('#visitsfence').val(visitcount);
                $("#booleanedit").val("1");
                $("#whichfence").val(id);
                $("#addnewfence").val(id);
                $('#contractual_visit').val(contractualVisits);
            });
            //Add event listner on drag event of circle
            dyncircle.addListener('drag', function (event) {

                dynmarker.setOptions({
                    position: {
                        lat: event.latLng.lat(),
                        lng: event.latLng.lng()
                    },
                });
                $('#title').val(title);
                $('#addressfence').val(address);
                if ($("#whichfence").val() > 0 && $('#latfence').val() != "")
                {
                } else
                {
                    $('#latfence').val(lat);
                    $('#longfence').val(long);
                }
                $('#radiusfence').val(dyncircle.getRadius());
                $('#visitsfence').val(visitcount);
                $("#booleanedit").val("1");
                $("#whichfence").val(id);
                $("#addnewfence").val(id);
            });
            //Add event listner on drag event of circle
            //changing the radius of circle on changing the numeric field value
            $("#radius").on("change paste keyup keydown", function () {
                //radius = $("#radius").val();
                dyncircle.setRadius(Number($("#radiusfence").val()));

            });
        });
        // map.fitBounds(bounds);
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}&libraries=places"></script>
<script>
    var bindevents = function (event, customer_id) {
        var customer_id = $("#customer_id").val();
        $(".savedfences").on("click", function (e) {
            var fenceid = $(this).attr("attr-fencerowid");
            let customerId = $('#customer_id').val();
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this fence",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, I am sure',
                cancelButtonText: "No, cancel it",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: "get",
                        url: "{{route('customer.removefencelist')}}",
                        data: {
                            "fenceid": fenceid
                        },
                        success: function (response) {
                            // $('a[data-id="'+id+'"]').get(0).click();  
                            if (response == "Deleted") {
                                $("#fencerow-" + fenceid).remove();
                                renderFences(customerId);
                                swal("Removed", "Successfully updated", "success");
                            }
                        }
                    });
                } else {
                    e.preventDefault();
                }
            });
        });

        $('.disablefences').on("click", function (e) {
            var fenceid = $(this).attr("attr-fencerowid");
            var process = $(this).attr("attr-process");
            swal({
                title: "Are you sure?",
                text: "You will be disabling an active fence",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, I am sure',
                cancelButtonText: "No, cancel it",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: "post",
                        url: "{{route('customer.disablefence')}}",
                        data: {
                            "fenceid": fenceid,
                            "process": process
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            swal("Updated", "Successfully updated", "success");
                        }
                    }).done(function (ev) {
                        renderFences(customer_id);
                    });
                } else {
                    e.preventDefault();
                }
            });
        });
        $(".editfences").on("click", function ($event) {
            $("#savemap").css("display", "none");
            var fenceid = $(this).attr("attr-fencerowid");
            var fencename = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-fencename");
            var fenceaddress = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-fencedesc");
            var fencevisitcount = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-visitcount");
            var latitude = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-latitiude");
            var longitude = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-longitude");
            var radius = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-radiusfence");
            var contractualVisit = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-contractractual");

            $('#title').val(fencename);
            $("#addressfence").val(fenceaddress);
            $("#visitsfence").val(fencevisitcount);
            $('#latfence').val(latitude);
            $('#longfence').val(longitude);
            $('#radiusfence').val(radius);
            $('#contractual_visit').val(contractualVisit);

            $("#booleanedit").val("1");
            $("#whichfence").val(fenceid);
            $("#myModal").modal("show");
        });
    }
    $(document).ready(function () {
        var latitiude = 43.93667009577818;
        var longitude = -79.65423151957401;
        var radius = 1000;

        var jqdata = [];
        var autocomplete = new google.maps.places.Autocomplete(
                document.getElementById('addressfence'), {types: ['geocode'],
            componentRestrictions: {country: ["ca", "in"]}}
        );
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            let place = autocomplete.getPlace();
            let lat = place.geometry.location.lat();
            let lng = place.geometry.location.lng();
            $("#latfence").val(lat);
            $("#longfence").val(lng);
        });
        initializefence(new google.maps.LatLng(latitiude, longitude), radius, jqdata);
    });
    $("#fencebuttoncancel").on("click", function (e) {
        $("#myModal").modal("hide");
    })
    var renderFences = function (customer_id) {
        $("#savemap").css("display", "none");
        $.ajax({
            type: "get",
            url: "{{route('customer.fencelist')}}",
            data: {
                "customerid": customer_id
            },
            success: function (response) {
                $("#fencerowsedit").html(response);
            }
        }).done(function (response) {
            var latitiude = 43.93667009577818;
            var longitude = -79.65423151957401;
            var radius = 1000;
            var info = $('#customerfence-table').DataTable({
   "lengthMenu":[[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]]
         }).page.info();

            var pageno = $("#datatablepage").val();

            var table = $("#customerfence-table").dataTable().on('draw.dt', function (event) {
                var info = $('#customerfence-table').DataTable().page.info();

                $("#datatablepage").val(info.page);
                bindevents(event, customer_id);
            });
            if (pageno > 0) {
                table.fnPageChange(parseInt(pageno), true);
            }
            $.ajax({
                type: "get",
                url: "{{route('customer.fencelistarray')}}",
                data: {
                    "customer_id": customer_id
                },
                success: function (response) {
                    var jqdata = jQuery.parseJSON(response);
                    initializefence(new google.maps.LatLng(latitiude, longitude), radius, jqdata);
                }
            }).done(function (event) {
                bindevents(event);
            });

            $("#title").val("");
            $("#booleanedit").val("0");
            $("#whichfence").val("0");
        });

        $("#fencebutton").on("click", function (event) {
            event.preventDefault();
            $("#renderedfence").val("0");
            var fencename = $("#title").val();
            var address = $("#addressfence").val();
            var latitiude = $("#latfence").val();
            var longitude = $("#longfence").val();
            var radiusfence = $("#radiusfence").val();
            var visitsfence = $("#visitsfence").val();
            var booleanedit = $("#booleanedit").val();
            var whichfence = $("#whichfence").val();
            var customer_id = $("#customer_id").val();
            var contractual_visit = $("#contractual_visit").val();
            var contractual_visit_unit = $("#contractual_visit_unit").val();
            if (customer_id == "") {
                swal("Oops", "Customer cannot be empty", "warning");
            } else if (fencename == "") {
                swal("Oops", "Fence name cannot be empty", "warning");
            } else if (address == "") {
                swal("Oops", "Address cannot be empty", "warning");
            } else if (latitiude == "") {
                swal("Oops", "Latitude cannot be empty", "warning");
            } else if (longitude == "") {
                swal("Oops", "Longitude cannot be empty", "warning");
            } else if (radiusfence == "") {
                swal("Oops", "Radius cannot be empty", "warning");
            } else if (radiusfence < 0) {
                swal("Oops", "Radius cannot be less than zero", "warning");
            } else if (visitsfence == "") {
                swal("Oops", "Visits per shift cannot be empty", "warning");
            } else if (visitsfence < 0) {
                swal("Oops", "Visits per shift cannot be less than zero", "warning");
            } else if (contractual_visit == "") {
                swal("Oops", "Contractual visit cannot be empty", "warning");
            } else if (contractual_visit < 0) {
                swal("Oops", "Contractual visit cannot be less than zero", "warning");
            } else {
                if ($("#fencecount").val() == "") {
                    var nooffences = 1;
                    $("#fencecount").val("1");
                } else {
                    var nooffences = parseInt($("#fencecount").val()) + 1;
                }
                var inphiddentitle = '<input type="hidden" name="fhidtit-' + nooffences + '"  id="fhidtit-' + nooffences + '" value="' + fencename + '">';
                var inphiddenlat = '<input type="hidden" name="fhidlat-' + nooffences + '"  id="fhidlat-' + nooffences + '" value="' + latitiude + '">';
                var inphiddenlon = '<input type="hidden" name="fhidlon-' + nooffences + '"  id="fhidlon-' + nooffences + '" value="' + longitude + '">';
                var inphiddenradius = '<input type="hidden" name="fhidrad-' + nooffences + '"  id="fhidrad-' + nooffences + '" value="' + radiusfence + '">';
                var inphiddencontractualvisit = '<input type="hidden" name="fhidcontractual-' + nooffences + '"  id="fhidcontractual-' + nooffences + '" value="' + contractual_visit + '">';
                var fencehtml = '<div class="row fencerow" id="fencerow-' + nooffences + '" style="height:20px" ' +
                        'attr-fencename=' + fencename + ' attr-latitiude=' + latitiude + ' attr-longitude=' + longitude + ' ' +
                        'attr-radiusfence=' + radiusfence +
                        ' attr-contractractual=' + contractual_visit + '><div class="col-md-2"> ' + inphiddentitle + fencename + '</div>' +
                        '<div class="col-md-2"> ' + inphiddenlat + latitiude + '</div><div class="col-md-2">' +
                        ' ' + inphiddenlon + longitude + '</div><div class="col-md-2" style=""> ' + inphiddenradius + radiusfence + '</div><div class="col-md-2" style=""> ' + inphiddencontractualvisit + contractual_visit + '</div>' +
                        '<div style="cursor:pointer" class="col-md-2"  class="removeaddedfence"><a ' +
                        'attr-fencerowid=' + nooffences + ' id="removefence-' + nooffences + '">Remove</a></div></div>';

                var booleanedit = $("#booleanedit").val();

                var whichfence = $("#whichfence").val();
                if (customer_id > 0) {
                    swal({
                        title: "Are you sure?",
                        text: "Please confirm",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'Yes, I am sure',
                        cancelButtonText: "No, cancel it",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            if (booleanedit == 0) {
                                var title = $("#title").val();
                                var address = $("#addressfence").val();
                                var latitiude = $("#latfence").val();
                                var longitude = $("#longfence").val();
                                var radius = $("#radiusfence").val();
                                var visitsfence = $("#visitsfence").val();
                                var contractual_visit = $("#contractual_visit").val();
                                $.ajax({
                                    type: "post",
                                    url: "{{ route('customer.editFence') }}",
                                    type: "POST",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        'whichfence': 0,
                                        "customer_id": customer_id,
                                        "address": address,
                                        'title': title,
                                        'latitiude': latitiude,
                                        'longitude': longitude,
                                        'radius': radius,
                                        'contractual_visit': contractual_visit,
                                        'contractual_visit_unit': 0,
                                        'visitsfence': visitsfence,
                                        'fencestatus': 1,
                                    },
                                    success: function (response) {
                                        //var newfenceid = response;
                                    }
                                }).done(function (event) {
                                    var latitiude = 43.93667009577818;
                                    var longitude = -79.65423151957401;
                                    var radius = 1000;
                                    $.ajax({
                                        type: "get",
                                        url: "{{route('customer.fencelistarray')}}",
                                        data: {
                                            "customer_id": customer_id
                                        },
                                        success: function (response) {
                                        }
                                    }).done(function (data) {
                                        var latitiude = 43.93667009577818;
                                        var longitude = -79.65423151957401;
                                        var radius = 1000;
                                        $("#latfence").val("");
                                        $("#longfence").val("");
                                        $("#radiusfence").val(radius);
                                        var jqdata = jQuery.parseJSON(data);

                                        initializefence(new google.maps.LatLng(latitiude, longitude), radius, jqdata);

                                        $("#booleanedit").val("0");
                                        $("#whichfence").val("0");
                                        $("#addnewfence").val("0");
                                        $("#title").val("");
                                        $("#contractual_visit").val("");
                                        $("#addressfence").val("");
                                        $("#visitsfence").val("");
                                        $("#myModal").modal('hide');
                                    });
                                    $("#latfence").val("");
                                    $("#longfence").val("");
                                    $("#radiusfence").val(radius);
                                    renderFences(customer_id);
                                });
                            } else {
                                var title = $("#title").val();
                                var address = $("#addressfence").val();
                                var latitiude = $("#latfence").val();
                                var longitude = $("#longfence").val();
                                var radius = $("#radiusfence").val();
                                var visitsfence = $("#visitsfence").val();
                                var contractual_visit = $("#contractual_visit").val();
                                $.ajax({
                                    type: "post",
                                    url: "{{ route('customer.editFence') }}",
                                    type: "POST",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        'whichfence': whichfence,
                                        "customer_id": customer_id,
                                        "address": address,
                                        'title': title,
                                        'contractual_visit': contractual_visit,
                                        'contractual_visit_unit': contractual_visit_unit,
                                        'latitiude': latitiude,
                                        'longitude': longitude,
                                        'radius': radius,
                                        'visitsfence': visitsfence,
                                        'fencestatus': 1,
                                    },
                                    success: function (response) {
                                        //var newfenceid = response;
                                    }
                                }).done(function (event) {
                                    $.ajax({
                                        type: "get",
                                        url: "{{route('customer.fencelistarray')}}",
                                        data: {
                                            "customer_id": customer_id
                                        },
                                        success: function (response) {
                                        }
                                    }).done(function (data) {
                                        var latitiude = 43.93667009577818;
                                        var longitude = -79.65423151957401;
                                        var radius = 1000;
                                        $("#latfence").val(latitiude);
                                        $("#longfence").val(longitude);
                                        $("#radiusfence").val(radius);
                                        var jqdata = jQuery.parseJSON(data);
                                        initializefence(new google.maps.LatLng(latitiude, longitude), radius, jqdata);
                                        $("#title").val("");
                                        $("#addressfence").val("");
                                        $("#visitsfence").val("");
                                        $("#booleanedit").val("0");
                                        $("#whichfence").val("0");
                                        $("#addnewfence").val("0");
                                        $("#contractual_visit").val("");
                                        $("#myModal").modal('hide');
                                    });
                                    renderFences(customer_id);
                                    $("#booleanedit").val("0")
                                });
                            }

                        } else {
                            e.preventDefault();
                        }
                    });
                } else {
                    var addnewfence = parseInt($("#addnewfence").val());
                    if (addnewfence > -1) {
                    } else {
                        $(fencehtml).insertBefore("#fencerowbottom").after(function (event) {
                            $('#removefence-' + nooffences).on('click', function (event) {
                                //alert("Working inside");
                                //var rowid = $(this).attr("attr-fencerowid");
                                $("#fencerow-" + nooffences).remove();
                            });
                        });
                    }

                    $("#addnewfence").val("-1");

                    var jqdata = [];
                    $('.fencerow').each(function (index, value) {
                        // console.log('div' + index + ':' + $(this).attr('id'));
                        var stattitle = $(this).attr("attr-fencename");
                        var statlat = $(this).attr("attr-latitiude");
                        var statlong = $(this).attr("attr-longitude");
                        var statrad = $(this).attr("attr-radiusfence");
                        jqdata[index] = [index, stattitle, statlat, statlong, statrad];
                    });

                    var latitiude = 43.93667009577818;
                    var longitude = -79.65423151957401;
                    var radius = 1000;
                    $("#latfence").val(latitiude);
                    $("#longfence").val(longitude);
                    $("#radiusfence").val(radius);

                    $("#booleanedit").val("0");
                    $("#whichfence").val("0");
                    $("#addnewfence").val("0");
                    $("#title").val("");
                    $("#addressfence").val("");
                    $("#contractual_visit").val("");
                    $("#visitsfence").val("");
                    initializefence(new google.maps.LatLng(latitiude, longitude), radius, jqdata);
                    $("#fencecount").val(nooffences);
                }
            }
            event.stopImmediatePropagation();
            event.stopPropagation();
        });
    }
    $("#fencebuttonaddnew").on("click", function (ev) {
        ev.preventDefault();
        $("#title").val("");
        $("#addressfence").val("");
        $("#contractual_visit").val("");
        $("#latfence").val("");
        $("#longfence").val("");
        $("#radiusfence").val(1000);
        $("#visitsfence").val("");
        $("#booleanedit").val("0");
        $("#addnewfence").val("-1");
        $("#whichfence").val("0");
        $("#savemap").css("display", "none");
        if ($("#customerselect").val() == "-1") {
            swal("Oops", "Please select a customer", "warning");
        } else {

            $("#myModal").modal("show");
        }
    });

    $("#savemap").on("click", function (eve) {
        $("#fencebutton").trigger("click");
    })

    $('#myModal').on('pagehide', function (event) {
    });

</script>
<style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>
<script>
    $(function () {
        $("#customerselect").select2();

        $("#customerselect").on("select2:select", function (e) {
            $("#renderedfence").val("0");
            var customer_id = $(this).val();
            $("#customer_id").val(customer_id);
            renderFences(customer_id);
        });
    });

    $(".close").on('click', function (event) {
        $("#title").val("");
        $("#addressfence").val("");
        $("#contractual_visit").val("");
        $("#latfence").val("");
        $("#longfence").val("");
        $("#radiusfence").val(1000);
        $("#visitsfence").val("");
        $("#booleanedit").val("0");
        $("#addnewfence").val("-1");
        $("#whichfence").val("0");
    });

    $('a[href="#fencemapTab"]').on('shown.bs.tab', function (e)
    {
        var latitiude = 43.93667009577818;
        var longitude = -79.65423151957401;
        var radius = 1000;

        //renderFences($("#customerselect").val());
        if ($("#renderedfence").val() == 0) {
            renderFences($("#customerselect").val());
            $("#renderedfence").val("1");
        }
    });

    $(".restrictdec").on("keydown", function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);

        if (keycode == 110 || keycode == 190) {
            return false;
        }
    })

    $(document).keyup(function (e) {
        jQuery
        if (e.key === "Escape") {
            $(".close").trigger("click");
        }
    });
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script src="{{ asset('js/timepicki.js') }}"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel='stylesheet' type='text/css' href="{{ asset('css/timepicki.css') }}" />
@stop