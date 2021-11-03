<script>

    function getCpidRow(key, create = false){
            let _value = (create == false) ? key : '';
            return `
                <tr>
                    <td>
                        <div class='form-group' id='cpid_allocation_${key}'>
                        <input type='hidden' name='row-no[]' class='row-no' value="${_value}">
                        <select class='form-control' name='cpid_${key}'>
                            <option value='' selected>Choose cpid</option>
                            @foreach($lookups['cpidLookup'] as $id=>$cpids)
                                <option value='{{$cpids->id}}'>{{$cpids->cpid}}
                                @php if(!empty($cpids->position)) { @endphp
                                    ( {{$cpids->position->position }} )
                                    @php } @endphp
                                @php if(!empty($cpids->cpidFunction)) { @endphp
                                -{{$cpids->cpidFunction->name }}
                                @php } @endphp
                                </option>
                            @endforeach
                        </select>
                            <small class='help-block'></small>
                        </div>
                    </td>
                </tr>`;
            }


    $(function() {
        $('#subjects').select2({  width: '100%' });
        $("#addMore").click(function(e) {
            e.preventDefault();
            var last_row = $('#fieldList li:last-child').val();
            if (last_row != undefined) {
                var next_row = last_row + 1;
            } else {
                var next_row = 0;
            }
            $customerQrcodeRow = "<li value=" + next_row + ">" +
                "<div class='row'>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='qrcode_" + next_row + "'>" +
                "<input type='text' name='qr-location-row[]' class='row-no' value='" + next_row + "'>" +
                "<label for='qrcode_" + next_row + "' class='control-label'>QR Code <span class='mandatory'>*</span></label>" +
                "<input class='form-control qrcode' placeholder='QR Code' name='qrcode_" + next_row + "' type='text'>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='location_" + next_row + "'>" +
                "<label for='location_" + next_row + "' class='control-label'>Checkpoint<span class='mandatory'>*</span></label>" +
                "<input class='form-control location' placeholder='Checkpoints' name='location_" + next_row + "' type='text'>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "</div>" +
                "<div class='row'>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='no_of_attempts_" + next_row + "'>" +
                "<label for='no_of_attempts_" + next_row + "' class='control-label'>Number of Attempts <span class='mandatory'>*</span></label>" +
                "<input class='form-control no_of_attempts' placeholder='Number of Attempts' name='no_of_attempts_" + next_row + "' type='text'>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='location_enable_disable_" + next_row + "'>" +
                "<label for='location_enable_disable_" + next_row + "' class='control-label'>Enable/Disable Location<span class='mandatory'>*</span></label>" +
                "<select class='form-control' name='location_enable_disable_" + next_row + "'>" +
                "<option value='' selected='selected'>Select</option>" +
                "<option value='1'>Enable</option>" +
                "<option value='0'>Disable</option>" +
                "</select>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "</div>" +
                "<div class='row'>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='picture_enable_disable_" + next_row + "'>" +
                "<label for='picture_enable_disable_" + next_row + "' class='control-label'>Enable/Disable Picture<span class='mandatory'>*</span></label>" +
                "<select class='form-control' name='picture_enable_disable_" + next_row + "' onchange='getval(this," + next_row + ");'>" +
                "<option value='' selected='selected'>Select</option>" +
                "<option value='1'>Enable</option>" +
                "<option value='0'>Disable</option>" +
                "</select>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='qrcode_active_" + next_row + "'>" +
                "<label for='qrcode_active_" + next_row + "' class='control-label'>Active<span class='mandatory'>*</span></label>" +
                "<select class='form-control' name='qrcode_active_" + next_row + "'>" +
                "<option value='' selected='selected'>Select</option>" +
                "<option value='1'>Activate</option>" +
                "<option value='0'>De-activate</option>" +
                "</select>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "</div>" +
                "<div class='form-group' id='picture_mandatory_" + next_row + "' style=display:none>" +
                "<label for='picture_mandatory_" + next_row + "' class='control-label'>Picture Mandatory<span class='mandatory'>*</span></label>" +
                "<select class='form-control' name='picture_mandatory_" + next_row + "' id='picture_mandatory_id_" + next_row + "'>" +
                "<option value='' selected='selected'>Select</option>" +
                "<option value='1'>Yes</option>" +
                "<option value='0'>No</option>" +
                "</select>" +
                "<small class='help-block'></small>" +
                "</div>" +


                "</li>";
            $("#fieldList").append($customerQrcodeRow);

            $('#remove-qrcode-location').show();
        });
        $("#myModal").on("click", "#remove-qrcode-location", function(e) {

            var last_row = $('#fieldList li:last-child').val();
            if (last_row > -1) {
                $("#fieldList li:last-child").remove();
                if (last_row == 0) {
                    $('#remove-qrcode-location').hide();
                }
            } else {
                $('#remove-qrcode-location').hide();
            }
        });
    });



    $(function() {
        $('#incident_reset_btn').on('click', function(e) {
            e.preventDefault();
            let customerId = $('#myModal input[name="id"]').val();
            swal({
                title: 'Are you sure?',
                text: "It will be permanently deleted",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it'
            }, function() {
                $.ajax({
                    url: "{{ route('customer.reset_incident_logo') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'customer_id': customerId
                    },
                    success: function(data) {
                        $('#incident_report_logo_el').val('');
                        if (data.success) {
                            $('#incident-logo-section').hide();
                            swal('Deleted', 'Your file has been deleted.', 'success');
                        } else {
                            // swal("Oops", "Something went wrong", "warning");
                        }
                    },
                    error: function(xhr, textStatus, thrownError) {
                        // swal("Oops", "Something went wrong", "warning");
                    },
                });

            });

        });


        function collectFilterData() {
            return {
                client_id:$("#clientname-filter").val(),
                   }
        }

        $.fn.dataTable.ext.errMode = 'throw';
        try {
            $('.client-filter').select2();
            var table = $('#customer-table').DataTable({
                bProcessing: false,
                responsive: true,
                dom: 'lfrtBip',
                buttons: [{
                        extend: 'pdfHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'print',
                        text: ' ',
                        className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        text: ' ',
                        className: 'btn btn-primary fa fa-envelope-o',
                        exportOptions: {
                            columns: 'th:not(:last-child)'
                        },
                        action: function(e, dt, node, conf) {
                            emailContent(table, 'Customers');
                        }
                    }
                ],
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: {
                    "url": "{{ route('customer.list') }}",
                    "data": function ( d ) {
                         return $.extend({}, d, collectFilterData());
                        },
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [
                    [1, "asc"]
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
                        data: 'project_number',
                        name: 'project_number'
                    },
                    {
                        data: 'client_name',
                        name: 'client_name'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'contact_person_name',
                        name: 'contact_person_name'
                    },
                    {
                        data: 'contact_person_email_id',
                        name: 'contact_person_email_id'
                    },
                    {
                        data: 'contact_person_phone',
                        name: 'contact_person_phone'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(o) {
                            var trim = function(address) {
                                var nospace_address = address.split(' ').join('+');
                                var customer_address = nospace_address.replace('#', '');
                                return customer_address;
                            };
                            if (o.radius == 0 || o.radius == '' || o.radius == null || o.geo_location_lat == '' || o.geo_location_lat == null || o.geo_location_long == '' || o.geo_location_long == null)
                                btnclass = 'no_geofence';
                            else
                                btnclass = 'geofence';
                            return '<a href="#" class="map_location btn ' + btnclass + ' fa fa-location-arrow" data-id=' + o.id + ' data-radius=' + o.radius + ' data-lat=' + o.geo_location_lat + ' data-long=' + o.geo_location_long + ' data-address=' + trim(o.address) + ' data-city=' + trim(o.city) + ' data-province=' + trim(o.province) + ' data-postal_code=' + trim(o.postal_code) + '>' + '' + '</a>';
                        },
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            var actions = '';
                            @can('edit_masters')
                            actions += '<a href="#" class="edit  {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                            @endcan
                            @can('lookup-remove-entries')
                            actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                            @endcan
                            return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

        $(".client-filter").change(function(){
            var table = $('#customer-table').DataTable();
            table.ajax.reload();
        })

        function hasValidIncidentLogo() {
            let fileEl = $('#incident_report_logo_el');
            let file = fileEl[0].files[0];
            if (!file) {
                return true; //allow empty logo
            }
            //check valid image
            if (!file.type.match('image.*')) {
                return false;
            }
            //todo:check file dimensions
            return true;
        }

         /*Filters for Permanent and STC customer - Start*/
         $('#filter').on('change', 'input[name=customer-contract-type]', function() {
            var customer_type = $('input[name="customer-contract-type"]:checked').val();
            var customer_status = $('input[name="customer-status"]:checked').val();
            var url = "{{ route('customer.list',[':customer_type',':customer_status']) }}";
            url = url.replace(':customer_type', customer_type);
            url = url.replace(':customer_status', customer_status);
            table.ajax.url(url).load();
        });

        $('#filterActive').on('change', 'input[name=customer-status]', function() {
            var customer_type = $('input[name="customer-contract-type"]:checked').val();
            var customer_status = $('input[name="customer-status"]:checked').val();
            var url = "{{ route('customer.list',[':customer_type',':customer_status']) }}";
            url = url.replace(':customer_type', customer_type);
            url = url.replace(':customer_status', customer_status);
            table.ajax.url(url).load();
        });


        /*Filters for Permanent and STC customer - End*/

        function submitCustomerForm(table,e,message){
            formSubmit($('#customer-form'), "{{ route('customer.store') }}", table, e, message).then(function(data){
             let pane = $('#customer-form .has-error:first').closest('.tab-pane');
             if(pane.length > 0){
                 let paneId = $(pane).attr('id');
                 let targetLink = $('a[href="#'+paneId+'"]');

                 if(targetLink.length >0){
                    targetLink.trigger('click');
                 }
             }
            });
        }
        /* Customer Store - Start*/
        $('#customer-form').submit(function(e) {
            e.preventDefault();
            if ($('#customer-form input[name="id"]').val()) {
                var message = 'Customer has been updated successfully';
            } else {
                var message = 'Customer has been created successfully';
            }
            if (!hasValidIncidentLogo()) {
                swal("Warning", "Invalid Incident logo", "warning");
                return false;
            }
            var title = $("#title").val();
            if(title=="")
            {
                submitCustomerForm(table, e, message);
            }
            else{

                    submitCustomerForm(table, e, message);

            }

        });
        /* Customer Store - End*/

        $(function() {
            $("input[id*='radiusfence']").keydown(function(event) {


                if (event.shiftKey == true) {
                    event.preventDefault();
                }

                if ((event.keyCode >= 48 && event.keyCode <= 57) ||
                    (event.keyCode >= 96 && event.keyCode <= 105) ||
                    event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
                    event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

                } else {
                    event.preventDefault();
                }

                if ($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
                    event.preventDefault();
                //if a decimal has been added, disable the "."-button

            });
        });
        $('#myModal input[name="mobile_security_patrol_site"]').on("click", function(event) {
            if (this.checked == true) {
                $("#geo_fence_satellite").show();
                //$("#geo_fence_satellite").prop("checked", true);
            } else {
                $("#geo_fence_satellite").hide();
                $("#geo_fence_satellite").prop("checked", false);
            }
        });

        $('.nav-tabs').on('click', function(e){
            if(e.target.getAttribute("href") === '#landingPage') {
                $('#landingPage').css('display','block');
            }else {
                $('#landingPage').css('display','none');
            }
        });

        /* Customer Edit - Start*/
        $("#customer-table").on("click", ".edit", function(e) {

            var id = $(this).data('id');
            var base_url = "{{route('customer.edit',':id')}}";
            var url = base_url.replace(':id', id);
            window.location = url;

        });
        /* Customer Edit - End*/



        $('#myModal input[name="mobile_security_patrol_site"]').on("click", function(event) {
            //alert("Here");
        })

        /* Get region data */
        $('select[name="region_lookup_id"]').on('change', function() {
            var id = $(this).val();
            $('.region-description').val("");
            if ($.isNumeric(id)) {
                var base_url = "{{route('region.single',':id')}}";
                var url = base_url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        $('.region-description').val(data.region_description);
                    }
                });
            }
        });
        /* Get region data */

        /* Customer Delete - Start*/
        $('#customer-table').on('click', '.delete', function(e) {
            var id = $(this).data('id');
            var base_url = "{{route('customer.destroy',':id')}}"
            var url = base_url.replace(':id', id);
            var message = 'Customer has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Customer Delete - End*/

        //To reset the hidden value in the form
        $('#myModal').on('hidden.bs.modal', function() {
            $('#customer-form').find('input[name="id"]').val('0');
            $('#interval_check').hide();
            $('#guard_tour_duration').hide();
            $('input:text[name="billing_address"]').prop('readonly', false);
        });



        /*Hide alert -start*/
        /*$("#import-success-alert").fadeTo(2000, 500).slideUp(500, function(){
        $("#import-success-alert").slideUp(500);
        });*/
        /*Hide alert -end*/

        /* Show/Hide fields - Start */
        // $('#shift_journal').find('input').change(function() {
        //     if($(this).is(":checked")) {
        //         $('#time_shift_enabled').show();
        //     }else{
        //         $('#time_shift_enabled').hide();
        //         $('#time_shift_enabled').find('input').prop('checked',false);
        //     }
        // });
        $('#guard_tour').find('input').change(function() {
            if ($(this).is(":checked")) {
                $('#interval_check').show();
            } else {
                $('#interval_check').hide();
                $('#guard_tour_duration').hide();
                $('#interval_check').find('input').prop('checked', false);
                $('#duration').val('');
            }
        });
        $('#interval_check').find('input').change(function() {
            if ($(this).is(":checked")) {
                $('#guard_tour_duration').show();
            } else {
                $('#guard_tour_duration').hide();
                $('#guard_tour_duration').find('input').prop('checked', false);
                $('#duration').val('');
            }
        });
        $('#overstay_enabled').find('input').change(function() {
            if ($(this).is(":checked")) {
                $('#overstay_time').show();

            } else {
                $('#overstay_time').hide();
                $('#timepicker').val('');

            }
        });

        $('#employee_rating_response').find('input').change(function() {
            if ($(this).is(":checked")) {
                $('#employee_rating_response_time').show();

            } else {
                $('#employee_rating_response_time').hide();
                $('#timepicker').val('');

            }
        });
        /* Show/Hide fields - End */

        /*Clear MapContainer previous data - Start*/
        $("#modal_cancel").click(function() {
            $("#MapContainer").html("");
            $("#lat").val('');
            $("#long").val('');
            $("#radius").val('');
        });

        $("#modal-close").click(function() {
            $("#MapContainer").html("");
            $("#lat").val('');
            $("#long").val('');
            $("#radius").val('');
        });
        /*Clear MapContainer previous data - End*/


        /* Map Location click&Submit - Start */

        $('#latlong_submit').on('click', function(e) {
            id = $("#rowid").val();
            lat = $("#lat").val();
            long = $("#long").val();
            radius = $("#radius").val();
            if (radius != 0 && radius < 150) {
                swal("", "The radius should be either 0 or greater than or equal to 150mtrs", "warning");
            } else {
                $.ajax({
                    url: "{{route('customer.updateLatLong')}}",
                    type: 'POST',
                    data: {
                        'id': id,
                        'lat': lat,
                        'long': long,
                        'radius': radius
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success) {
                            swal("Success", "The geo location details updated successfully", "success");
                            //$('#message').html(data.payload);
                            $('#mapModal').modal('hide');
                            $("#MapContainer").html("");
                            table.ajax.reload();
                        } else {
                            //alert(data);

                            swal("Oops", "The geo location details updation was unsuccessful", "warning");
                        }
                    },
                    error: function(xhr, textStatus, thrownError) {
                        //alert(xhr.status);
                        //alert(thrownError);
                        console.log(xhr.status);
                        console.log(thrownError);
                        swal("Oops", "Something went wrong", "warning");
                    },
                });
            }
        });
        var markers = [];
        /* Function for add/edit fence - Start */
        function initializefence(myCenter, radius, jqdata) {
            var renderContainer = document.getElementById("mapfencelocation");
            var mapProp = {
                center: myCenter,
                gestureHandling: 'greedy',
                zoom: 7
            };

            var input = document.getElementById('addressfence');
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
            $("#radius").on("change paste keyup keydown", function() {
                //radius = $("#radius").val();
                circle.setRadius(Number($("#radiusfence").val()));

            });

            //Add listner to change latlong value on dragging the marker

            var bounds = new google.maps.LatLngBounds();


            var collection = [];
            var i = 0;
            $.each(jqdata, function(key, value) {
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
                        text: title,
                    },
                });
                dynmarker.id = id;
                dynmarker.setMap(map);
                if (i == 1) {
                    map.setCenter(new google.maps.LatLng(lat, long));
                    //dynmarker.setCenter(new google.maps.LatLng(lat, long));
                }
                bounds.extend(dynmarker.position);
                dynmarker.addListener('dragend', function(event) {
                    $('#title').val(title);
                    $('#addressfence').val(address);
                    $('#latfence').val(event.latLng.lat());
                    $('#longfence').val(event.latLng.lng());
                    $('#contractual_visit').val(contractualVisits);
                    if($("#whichfence").val()>0 && $('#latfence').val()!="")
                    {

                    }
                    else
                    {
                    $('#radiusfence').val(rad);
                    }
                    $('#visitsfence').val(visitcount);
                    $("#booleanedit").val("1");
                    $("#whichfence").val(id);
                    $("#addnewfence").val(id);
                });

                dynmarker.addListener('click', function(event) {
                    $('#title').val(title);
                    $('#addressfence').val(address);
                    // $('#radiusfence').val(radiusfence);
                    $('#latfence').val(event.latLng.lat());
                    $('#longfence').val(event.latLng.lng());
                    // if($("#whichfence").val()>0 && $('#latfence').val()!="")
                    // {

                    // }
                    // else
                    // {
                    // $('#radiusfence').val(rad);
                    // }
                    $('#radiusfence').val(rad);
                    $('#visitsfence').val(visitcount);
                    $("#booleanedit").val("1");
                    $("#whichfence").val(id);
                    $("#addnewfence").val(id);
                    $('#contractual_visit').val(contractualVisits);
                });

                //Add event listner on drag event of marker
                dynmarker.addListener('drag', function(event) {
                    dyncircle.setOptions({
                        center: {
                            lat: event.latLng.lat(),
                            lng: event.latLng.lng()
                        }
                    });
                });
                markers.push(dynmarker);

                //Add listner to change radius value on field
                dyncircle.addListener('radius_changed', function(event) {
                    $('#radiusfence').val(dyncircle.getRadius());
                    $('#title').val(title);
                    $('#addressfence').val(address);
                    if($("#whichfence").val()>0 && $('#latfence').val()!="")
                    {

                    }
                    else
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
                });
                //Add event listner on drag event of circle
                dyncircle.addListener('drag', function(event) {

                    dynmarker.setOptions({
                        position: {
                            lat: event.latLng.lat(),
                            lng: event.latLng.lng()
                        }
                    });
                    $('#title').val(title);
                    $('#addressfence').val(address);
                    if($("#whichfence").val()>0 && $('#latfence').val()!="")
                    {

                    }
                    else
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
                $("#radius").on("change paste keyup keydown", function() {
                    //radius = $("#radius").val();
                    dyncircle.setRadius(Number($("#radiusfence").val()));

                });

            });
        // map.fitBounds(bounds);




        }

        /* Function for add/edit fence - Start */
        function initialize(myCenter, radius) {
            var renderContainer = document.getElementById("MapContainer");
            var mapProp = {
                center: myCenter,
                zoom: 8
            };
            var map = new google.maps.Map(renderContainer, mapProp, {
                gestureHandling: 'greedy',
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
            marker.addListener('dragend', function(event) {
                $('#lat').val(event.latLng.lat());
                $('#long').val(event.latLng.lng());
            });

            //Add event listner on drag event of marker
            marker.addListener('drag', function(event) {
                circle.setOptions({
                    center: {
                        lat: event.latLng.lat(),
                        lng: event.latLng.lng()
                    }
                });
            });

            //Add listner to change radius value on field
            circle.addListener('radius_changed', function() {
                $('#radius').val(circle.getRadius());
            });

            //Add event listner on drag event of circle
            circle.addListener('drag', function(event) {
                marker.setOptions({
                    position: {
                        lat: event.latLng.lat(),
                        lng: event.latLng.lng()
                    }
                });
            });

            //changing the radius of circle on changing the numeric field value
            $("#radius").on("change paste keyup keydown", function() {
                //radius = $("#radius").val();
                circle.setRadius(Number($("#radius").val()));

            });

        }
        /* Function for add/edit fence - End */

        $('#add-fence').on('click', function(e) {
            $('#add-fence').hide();
            $('.radius').show();
        });

        /* Function for concatinating address and populate in billing address if checkbox is checked - Start */
        $("#check_same_address").click(function() {
            if ($("input[name=address]").val().length <= 0 || $("input[name=city]").val().length <= 0 || $("input[name=province]").val().length <= 0 || $("input[name=postal_code]").val().length <= 0) {
                swal("Warning", "Please enter address details", "warning");
                $(this).prop('checked', false);
            }
            if (this.checked) {
                var address = '';
                var city = '';
                var province = '';
                var postal_code = '';
                if ($("input[name=address]").val().length > 0)
                    var address = $("input[name=address]").val() + ', ';
                if ($("input[name=city]").val().length > 0)
                    var city = $("input[name=city]").val() + ', ';
                if ($("input[name=province]").val().length > 0)
                    var province = $("input[name=province]").val() + ', ';
                var postal_code = $("input[name=postal_code]").val();
                var full_addr = address + city + province + postal_code;
                $('input:text[name="billing_address"]').val(full_addr);
                $('input:text[name="billing_address"]').prop('readonly', true);
            } else {
                $('input:text[name="billing_address"]').val('');
                $('input:text[name="billing_address"]').prop('readonly', false);
            }
        });
        /* Function for concatinating address and populate in billing address if checkbox is checked - End */

        $('#customer-table').on('click', '.map_location', function(e) {
            $('#mapModal').off('shown.bs.modal');
            id = $(this).data('id');
            postal_code = $(this).data('postal_code');
            url = 'https://maps.google.com/maps/api/geocode/json?address=' + postal_code + '&sensor=false&key={{config('
            globals.google_api_key ')}}';
            if (($(this).data('lat') == '' || $(this).data('lat') == null) || ($(this).data('long') == '' || $(this).data('long') == null)) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    crossDomain: true,
                    dataType: 'json',
                    success: function(data) {
                        if (data.results.length > 0) {
                            lat = data.results[0].geometry.location.lat;
                            long = data.results[0].geometry.location.lng;
                            $('#lat').val(lat);
                            $('#long').val(long);
                        } else {
                            lat = {{config('globals.map_default_center_lat')}};
                            long = {{config('globals.map_default_center_lng')}};
                            $('#lat').val(lat);
                            $('#long').val(long);
                        }
                        setFence($(this));
                    },
                    error: function(xhr, textStatus, thrownError) {
                        //alert(xhr.status);
                        //alert(thrownError);
                        console.log(xhr.status);
                        console.log(thrownError);
                        swal("Oops", "Something went wrong", "warning");
                    },
                });
            } else {
                lat = $(this).data('lat');
                long = $(this).data('long');
                $('#lat').val(lat);
                $('#long').val(long);
                setFence($(this));
            }

            function setFence(curr_obj) {
                radius = Number((curr_obj.data('radius') != '' && curr_obj.data('radius') != null) ? curr_obj.data('radius') : 0);
                $('#rowid').val(id);
                $('#radius').val(radius);
                $('.radius').show();
                if (radius == 0) {
                    $('.radius').hide();
                    $('#add-fence').show();
                }
                $('#mapModal').modal();
                $('#mapModal').on('shown.bs.modal', function(e) {
                    initialize(new google.maps.LatLng(lat, long), radius);
                });
            }
        });

        /* Prepopulating employee details on choosing select2 - Start */
        $('#requester_id').on('change', function() {
            if ($(this).val() == '') {
                $('input:text[name="requester_position"]').val('');
                $('input:text[name="requester_empno"]').val('');
            }
            var url = '{{ route("user.formattedUserDetails", ["id" => ":user_id"]) }}';
            url = url.replace(':user_id', $(this).val());
            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    $('input:text[name="requester_position"]').val(data.position).prop('readonly', 'true');
                    $('input:text[name="requester_empno"]').val(data.employee_no).prop('readonly', 'true');
                },
                error: function(xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
            });

        });
        /* Prepopulating employee details on choosing select2 - End */


        /* Map Location click&Submit - End */

        /* CPID Allocation - Add - Start */

        $('#remove-cpid-allocation').hide();
        $("#myModal").on("click", "#add-cpid-allocation", function(e) {
            $last_row_no = $(".customer-cpid-allocation-table").find('tr:last .row-no').val();
            if ($last_row_no != undefined) {
                $next_row_no = ($last_row_no * 1) + 1;
            } else {
                $next_row_no = 0;
            }

            var customer_cpid_allocation_new_row = getCpidRow($next_row_no,true);
             $(".customer-cpid-allocation-table tbody").append(customer_cpid_allocation_new_row);
            $(".customer-cpid-allocation-table").find('tr:last').find('.row-no').val($next_row_no);

            $("#valid_until_" + $next_row_no + ">input").datepicker({
                format: "yyyy-mm-dd",
                maxDate: "+900y"
            });

            $(".datepicker").mask("9999-99-99");

            if ($last_row_no > 0 || $last_row_no == undefined) {
                $('#remove-cpid-allocation').show();
            }
        });
        /* CPID Allocation - Add - End */
        /* CPID Allocation - Remove - Start */
        $("#myModal").on("click", "#remove-cpid-allocation", function(e) {
            $last_row_no = $(".customer-cpid-allocation-table").find('tr:last .row-no').val();
            if ($last_row_no > -1) {
                $(".customer-cpid-allocation-table").find('tr:last').remove();
                if ($last_row_no == 0) {
                    $('#remove-cpid-allocation').hide();
                }
            } else {
                $('#remove-cpid-allocation').hide();
            }
        });
        /*CPID Allocation - Remove - End */

        var rendermap = function(customer_id) {

            $.ajax({
                type: "get",
                url: "{{route('customer.fencelistarray')}}",
                data: {
                    "customer_id": customer_id
                },
                success: function(response) {

                }
            }).done(function(data) {

                var latitiude = 43.93667009577818;
                var longitude = -79.65423151957401;
                var radius = 1000;
                $("#latfence").val(latitiude);
                $("#longfence").val(longitude);
                $("#radiusfence").val(radius);
                var jqdata = jQuery.parseJSON(data);

                initializefence(new google.maps.LatLng(latitiude, longitude), radius, jqdata);
            });
        }




        $("#fencebuttonreset").on("click", function(event) {
            $("#title").val("");
            $("#addressfence").val("");
            $("#booleanedit").val("0");
            $("#whichfence").val("0");
            $("#visitsfence").val("0");
            $("#radiusfence").val("");

            $("#addnewfence").val("-1");
            $("#contractual_visit").val("0");
        });

        $("#cancel_customer_modal").on("click", function(event) {
            $("#title").val("");
            $("#addressfence").val("");
            $("#booleanedit").val("0");
            $("#whichfence").val("0");
            $("#visitsfence").val("0");
            $("#radiusfence").val("");

            $("#addnewfence").val("-1");
            $("#contractual_visit").val("0");
        });

        /*Add new fence*/





        var placeSearch, autocomplete;

        var componentForm = {
            street_number: 'short_name',
            route: 'long_name',
            locality: 'long_name',
            administrative_area_level_1: 'short_name',
            country: 'long_name',
            postal_code: 'short_name'
        };

        var blurorfocusout = function(event) {
            var latfence = $("#latfence").val();
            if (latfence == "") {
                swal("Please enter a valid address");
            }
        }
        $("#addressfence").on('blur', function(event) {
            blurorfocusout(event);
        });

        $("#addressfence").on('focusout', function(event) {
            blurorfocusout(event);
        });

        var renderFences = function(customer_id) {
            $.ajax({
                type: "get",
                url: "{{route('customer.fencelist')}}",
                data: {
                    "customerid": customer_id
                },
                success: function(response) {
                    $("#fencerowsedit").html(response);
                }
            }).done(function(response) {
                var latitiude = 43.93667009577818;
                var longitude = -79.65423151957401;
                var radius = 1000;

                $.ajax({
                    type: "get",
                    url: "{{route('customer.fencelistarray')}}",
                    data: {
                        "customer_id": customer_id
                    },
                    success: function(response) {
                        var jqdata = jQuery.parseJSON(response);
                        initializefence(new google.maps.LatLng(latitiude, longitude), radius, jqdata);
                    }
                });
                $('.disablefences').on("click", function(event) {
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
                        function(isConfirm) {
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
                                    success: function(response) {
                                        swal("Updated", "Successfully updated", "success");
                                    }
                                }).done(function(ev) {
                                    renderFences(customer_id);
                                });


                            } else {
                                e.preventDefault();
                            }
                        });

                });

                $(".savedfences").on("click", function($event) {
                    var fenceid = $(this).attr("attr-fencerowid");
                    let customerId = $('#myModal input[name="id"]').val();
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
                        function(isConfirm) {

                            if (isConfirm) {
                                $.ajax({
                                    type: "get",
                                    url: "{{route('customer.removefencelist')}}",
                                    data: {
                                        "fenceid": fenceid
                                    },
                                    success: function(response) {
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

                $(".editfences").on("click", function($event) {

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
                });
                $("#title").val("");
                $("#booleanedit").val("0");
                $("#whichfence").val("0");


            })
        }
        var removeMarkers = function(fenceid) {

            for (var i = 0; i < markers.length; i++) {
                console.log(markers.length);
                if (markers[i].id == fenceid) {
                    //Remove the marker from Map
                    markers[i].setMap(null);

                    //Remove the marker from array.
                    markers.splice(i, 1);
                    // map.removeMarker(map.markers[i]);
                    return;
                }
            }
            //addMapmarker(map);
        }
        var addMapmarker = function(map) {
            var id = $("input[name=id]").val();
            $.ajax({
                type: "get",
                url: "{{route('customer.fencelistarray')}}",
                data: {
                    "customer_id": id
                },
                success: function(response) {
                    var jqdata = jQuery.parseJSON(response);

                    jqdata.forEach(element => {
                        var id = element[0];
                        var title = element[1];
                        var lat = element[2];
                        var long = element[3];

                        var radius = element[4];
                        console.log(map);
                        var marker = new google.maps.Marker({
                            position: new google.maps.LatLng(lat, long),
                            map: map,
                            draggable: true,
                            raiseOnDrag: true,
                        });

                    });
                }
            })
        }

        /* Display single row on adding cpid - Start */
        $('.add-new').click(function() {
            $(".nav-tabs li:not(:first-child)").removeClass('active')
            $(".tab-content div.tab-pane:not(:first-child)").removeClass('active')
            $('.landingPageTab').css('display','none');
            $('#landingPage').css('display','none');
            $('#user_tab').addClass('active show');
            $('#userTab').addClass('active');
            $('[href="#cpidTab"]').closest('li').show();
            $('#remove-cpid-allocation').show();
            $('#customer-cpid-allocation tbody').find('tr').remove();
            $('#customer-qrcode-location').find('li').remove();
             $("#subjects").val(null).trigger('change')
            var latitiude = 43.93667009577818;
            var longitude = -79.65423151957401;
            var radius = 1000;
            $('fencerowsedit').css('display', 'none');
            $("#latfence").val(latitiude);
            $("#longfence").val(longitude);
            $("#radiusfence").val(radius);
            jqdata = [];
            initializefence(new google.maps.LatLng(latitiude, longitude), radius, jqdata);

            $('#fencenew').css('display', 'block');
            //$('.fenceinputs').hide();

            $customer_cpid_first_row = getCpidRow(0)
            $('#customer-cpid-allocation tbody').append($customer_cpid_first_row);

            $customerQrcodeFirstRow = "<li value='0'>" +
                "<div class='row'>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='qrcode_0'>" +
                "<input type='text' name='qr-location-row[]' class='row-no' value='0'>" +
                "<label for='qrcode_0' class='control-label'>QR Code<span class='mandatory'>*</span></label>" +
                "<input class='form-control qrcode' placeholder='QR Code' name='qrcode_0' type='text'>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='location_0'>" +
                "<label for='location_0' class='control-label'>Checkpoint<span class='mandatory'>*</span></label>" +
                "<input class='form-control location' placeholder='Checkpoint' name='location_0' type='text'>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "</div>" +
                "<div class='row'>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='no_of_attempts_0'>" +
                "<label for='no_of_attempts_0' class='control-label'>Number of Attempts <span class='mandatory'>*</span></label>" +
                "<input class='form-control no_of_attempts' placeholder='Number of Attempts' name='no_of_attempts_0' type='text'>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='location_enable_disable_0'>" +
                "<label for='location_enable_disable_0' class='control-label'>Enable/Disable Location<span class='mandatory'>*</span></label>" +
                "<select class='form-control' name='location_enable_disable_0'>" +
                "<option value='' selected='selected'>Select</option>" +
                "<option value='1'>Enable</option>" +
                "<option value='0'>Disable</option>" +
                "</select>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "</div>" +
                "<div class='row'>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='picture_enable_disable_0'>" +
                "<label for='picture_enable_disable_0' class='control-label'>Enable/Disable Picture<span class='mandatory'>*</span></label>" +
                "<select class='form-control' name='picture_enable_disable_0' onchange='getval(this,0);'>" +
                "<option value='' selected='selected'>Select</option>" +
                "<option value='1'>Enable</option>" +
                "<option value='0'>Disable</option>" +
                "</select>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='qrcode_active_0'>" +
                "<label for='qrcode_active_0' class='control-label'>Active<span class='mandatory'>*</span></label>" +
                "<select class='form-control' name='qrcode_active_0'>" +
                "<option value='' selected='selected'>Select</option>" +
                "<option value='1'>Activate</option>" +
                "<option value='0'>De-activate</option>" +
                "</select>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "</div>" +

                "<div class='form-group' id='picture_mandatory_0' style=display:none>" +
                "<label for='picture_mandatory_0' class='col-lg-4 control-label'>Picture Mandatory<span class='mandatory'>*</span></label>" +
                "<select class='form-control' name='picture_mandatory_0' id='picture_mandatory_id_0'>" +
                "<option value='' selected='selected'>Select</option>" +
                "<option value='1'>Yes</option>" +
                "<option value='0'>No</option>" +
                "</select>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "</li>";
            $("#fieldList").append($customerQrcodeFirstRow);

        });
        /* Display single row on adding cpid - End */

    });

    $("#basement_mode").on("click", function(event) {
        var isChecked = $('#basement_mode').is(':checked');
        if ($("#basement_mode").is(':checked')) {
            $(".basement_mode").show();
        } else {
            $(".basement_mode").hide();
            $('#myModal input[name="basement_interval"]').val("");
            $('#myModal input[name="basement_noofrounds"]').val("");
        }

    });
    $('document').ready(function() {
        $(".binterval").mask("99:99");
    });

    $(document).keyup(function(e) {
        jQuery
        if (e.key === "Escape") {
            $("#myModal").modal('hide');
        }
    });

    $(document).ready(function() {
        $("#myModal").on("click", function() {
            var toggler = document.getElementsByClassName("caret");
            var i;

            for (i = 0; i < toggler.length; i++) {
            toggler[i].addEventListener("click", function() {
                this.parentElement.querySelector(".nested").classList.toggle("active");
                this.classList.toggle("caret-down");
            });
            }
        });

        $("#myModal #landingPage #tabList #editTab").on("click", function(e) {
            console.log(e);
            console.log('clicked tab edit');
        });

        $("#myModal #landingPage #tabList #editActiveTab").on("click", function(e) {
            console.log(e);
            console.log('active tab');
        });

    });

    $('#master_customer').select2();

    function addnew() {
        $("#myModal").modal();
        $("#requester_id").select2();
        $("#requester_id").val('').trigger('change');
        setTimeout(() => {
            $('#myModal input[name="fence_interval"]').val("5");
            $('#myModal select[name="contractual_visit_unit"] option[value="2"]').prop('selected',true)
            $('#myModal input[name="geo_fence"]').prop("checked","checked");
            $('#myModal input[name="customer_type"]').val("1")

        }, 200);
    }

    function getval(sel, key) {

        if (sel.value == 1) {
            //alert('yes');
            $('#picture_mandatory_' + key).show();
            //document.getElementById('#picture_mandatory_'+key).required = true;
        } else {
            //alert('no');
            $('#picture_mandatory_' + key).hide();
            //document.getElementById('#picture_mandatory_'+key).required = false;
        }
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}&libraries=places"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script src="{{ asset('js/timepicki.js') }}"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel='stylesheet' type='text/css' href='{{ asset("css/timepicki.css") }}' />

<script>
function open_new_configuration() {
    $(".close").trigger('click');
    var customer_id = $('#myModal input[name="id"]').val();
    let url = "{{ route('landing_page.new_configuration_window',['customer_id' => ''])}}" + customer_id + '';
    window.open(url);
}

function edit_tab(tab_id) {
    $(".close").trigger('click');
    let url = "{{ route('landing_page.new_configuration_window',['tab_id' => ''])}}" + tab_id + '';
    window.open(url);
}

function edit_activeTab(prevTabId) {
   name = 'li'+'#tabName' + prevTabId + ' a.editActiveTab';
   status = document.querySelector(name).getAttribute("value");
   var customer_id = $('#myModal input[name="id"]').val()
   $.ajax({
            type: "POST",
            url: "{{route('landing_page.saveTabActiveStatus')}}",
            data: {
                'customerid': customer_id,
                'tabid': prevTabId,
                'status': (status==1)?0:1,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.status === "success") {
                    swal({
                        title: "Success",
                        text: response.msg,
                        type: response.status,
                        confirmButtonText: "OK"
                    });

                    if (status == 1) {
                    $(name).removeClass("fa-toggle-on fa-2x").addClass("fa-toggle-off fa-2x");
                    document.querySelector(name).setAttribute("value", "0");
                    } else {
                    $(name).removeClass("fa-toggle-off fa-2x").addClass("fa-toggle-on fa-2x");
                    document.querySelector(name).setAttribute("value", "1");
                }
                }else {
                    swal(response.status_msg, response.msg, response.status);
                }
            }
        });

}
</script>
