@extends('layouts.app')
@section('content')
<style>

</style>
<div id="supervisor_panel">
    <div class="table_title">
        <h4>Activity Mapping </h4>
    </div>

    <div class="row mainlink-component card-view-section mb-2  position-relative">

        <div class="col-md-12" style="padding-left: 20px;">
            <div class="row">
            <div class="col-md-1" style="margin-right: -70px;padding-top:5px;">Date</div>
            <div class="col-md-2" ><input style="margin-left: 8px;" id="startdate" width="100%" value="{{$startdate}}" class="form-control custom-datepicker" /></div>
            <div class="col-md-1" style="padding-top:5px;">Customer</div>
            <div class="col-md-3 "><select class="form-control project-filter select2" name="project-filter" id="project-name-filter">
                        <option value="">Select Project</option>
                        @foreach($project_list as $id=>$project_name)
                        <option value="{{$id}}">{{ $project_name}}
                        </option>
                        @endforeach
                    </select>
             </div>
           
            <div class="col-md-1" style="margin-right: -30px;padding-top:5px;">Module</div>
            <div class="col-md-2">                   
                 <select class="form-control group-filter select2"  name="employee-filter" id="module-name-filter">
                        <option value="">Select Module</option>
                        @foreach($module_list as $id=>$module_name)
                        <option value="{{$id}}">{{ $module_name}}
                        </option>
                        @endforeach
                    </select>
            </div>
            </div>
        </div>

       <div  style="padding-left: 20px;padding-top:5px;display: none;" class="col-md-12" id="dropdown-filter">

        </div>
    </div>
    <div class="embed-responsive embed-responsive-4by3">
        <div id="map" style="min-height:335px;" class="embed-responsive-item">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L o a d i n g . . . . . . </div>
    </div>
</div>
@stop
@section('scripts')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
<script type="text/javascript">
    $(function() {
        $('.select2').select2();
        sessionStorage.setItem("mapping_start_date", $('#startdate').val());
        $('#startdate').datepicker({
            format: 'yyyy-mm-dd',
            showRightIcon: false,
            change: function(e) {
                if (sessionStorage.getItem("mapping_start_date") != $('#startdate').val()) {
                       changeDateFilter();
                }
            },
        });

        initEmptyMap(new google.maps.LatLng('43.6532', '-79.3832'));
    });

    function initEmptyMap(myCenter) {
        var logo = '<img src="{{ asset("images/short_logo.png") }}">';
        var locations = [];
        var mapProp = {
            center: myCenter,
            zoom: 8,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById('map'), mapProp);
    }

    $(window).bind("load", function() {
        $('#sidebar').css('height', $(window).height() - 70);
        $('#content-div').css('height', $(window).height() - 70);
        $('#content-div').css('overflow', 'hidden');

        var marker, i, contentString;

    });


    $(".project-filter").change(function() {
        var project_id = $("#project-name-filter").val();
        if (project_id) {
            let url = '{{ route("project.modulelist",":id") }}';
            url = url.replace(':id', project_id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    $('#dropdown-filter').html('');

                    if (data) {
                        var options = '<option selected="selected" value="">Select Module</option>';
                        $.each(data, function(key, value) {
                            options += '<option value="' + key + '">' + value + '</option>'
                        });
                        $("#module-name-filter").html('');
                        $("#module-name-filter").html(options);
                    } else {
                        swal("Oops", "Could not retrive data.", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false
            });
        } else {
            $("#module-name-filter").html('');
            $("#module-name-filter").html(options);
        }
    });

    $('#module-name-filter').on('change', function(e) {
        var sdate = $('#startdate').val();
        var module_id = !($("#module-name-filter").val()) ? 0 : $("#module-name-filter").val();
        var project_id = !($("#project-name-filter").val()) ? 0 : $("#project-name-filter").val();
        var url = "{{ route('shiftmodule.mappinglist',[':id',':module_id',':date']) }}";
        url = url.replace(':date', sdate);
        url = url.replace(':id', project_id);
        url = url.replace(':module_id', module_id);
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                console.log(data);
                
                $('#dropdown-filter').html('');
                $('#dropdown-filter').show();
                if (data) {
                    var selects = '';
                    var option = '';
                    $.each(data.filters, function(key, value) {
                        option += '<option value="0">Select ' + value.dropdown.name + '</option>';
                        $.each(value.options, function(key, value) {
                            option += '<option value="' + value + '">' + value + '</option>';
                        });
                        selects += '<div  class="col-md-2"><select data-name="' + value.dropdown.name + '"  id="dropdown-filter-' + value.dropdown.id + '" class="form-control filter">' + option + '</select></div>';
                        option = '';
                    });
                    $('#dropdown-filter').append('<div class="row"> <div class="col-md-1" style="margin-right: -70px;padding-top:5px;">Filters</div>' + selects + '</div>');
                    initMap(data);
                } else {
                    swal("Oops", "Could not retrive data.", "warning");
                }
            },
            error: function(xhr, textStatus, thrownError) {
                swal("Oops", "Something went wrong", "warning");
            },
            contentType: false
        });

    });

    var locations = [];
    var hiddenlocations = [];

    function initMap(data) {
        var logo = '<img src="{{ asset("images/short_logo.png") }}">';

        var head = document.getElementsByTagName('head')[0];
        // Save the original method
        var insertBefore = head.insertBefore;
        // Replace it!
        head.insertBefore = function(newElement, referenceElement) {
            if (newElement.href && newElement.href.indexOf('//fonts.googleapis.com/css?family=Roboto') > -1) {
                console.info('Prevented Roboto from loading!');
                return;
            }
            insertBefore.call(head, newElement, referenceElement);
        };
        var icon = "{{ asset('images/markers/green-dot.png') }}";

        var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 5,
                center: {lat: {{config('globals.map_default_center_lat')}}, lng: {{config('globals.map_default_center_lng')}}},
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                gestureHandling: 'greedy'
                });

        var infowindow = new google.maps.InfoWindow();
        var marker, i, contentString, icon;
        $.each(data, function(i, item) {

            var dynamic_content = '';

            $.each(item.details, function(nkey, moredetails) {
                dynamic_content += '<span class="col-sm-7 col-7 float-left p0 map-label">' + nkey + '</span><span class="col-sm-5 col-5 float-left p0 map-disc">' + moredetails.replace("#", ", ") + '</span>' +
                    '<div class="clearfix"></div>';
            });

            marker = new google.maps.Marker({
                position: new google.maps.LatLng(item.lat, item.long),
                map: map,
                filt: item.details,
                icon: icon,
                content: '<div id="content" style="min-width:500px;">' +
                    '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo +
                    '&nbsp;<a style="color:#f26338;" href="url"></a></h4><div id="bodyContent">' +
                    '<label style="width:100%;">' +
                    '<span class="col-sm-7 col-7 float-left p0 map-label">Name</span><span class="col-sm-5 col-5 float-left p0 map-disc">' + item.first_name + ' ' + item.last_name + '</span>' +
                    '<div class="clearfix"></div>' +
                    '<span class="col-sm-7 col-7 float-left p0 map-label">Email</span><span class="col-sm-5 col-5 float-left p0 map-disc">' + item.email + '</span>' +
                    '<div class="clearfix"></div>' + dynamic_content +
                    '</label>' +
                    '</div>' +
                    '</div>'
            });

            locations.push(marker);
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infowindow.setContent(marker.content);
                    infowindow.open(map, marker);
                    //map.setCenter(marker.getPosition());
                }
            })(marker, i));

        });

        google.maps.event.addDomListener(window, 'resize', function() {
            infowindow.open(map);
        });

    }


    $(document).on('change', 'select.filter', function() {
        if (hiddenlocations.length > 0) {
            locations.concat(hiddenlocations);
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 5,
                center: {lat: {{config('globals.map_default_center_lat')}}, lng: {{config('globals.map_default_center_lng')}}},
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                gestureHandling: 'greedy'
                });
            $.each(locations, function(k, item) {
                locations[k].setMap(map);
            });
        }

        var filter_categories = {};
        ($('#dropdown-filter').find("select")).each(function() {
            if (this.value != 0) {
                filter_categories[$(this).attr('data-name')] = this.value;
            }
        })

        for (var nkey in filter_categories) {
             for (i = 0; i < locations.length; i++) {
                if (typeof locations[i].filt != 'undefined') {

                    if (locations[i].filt[nkey] != filter_categories[nkey]) {
                        hiddenlocations.push(locations[i]);
                        locations[i].setMap(null);
                    }
                }
              }
        }
      

    });

    function changeDateFilter(){
       sessionStorage.setItem("mapping_start_date", $('#startdate').val());
       var date = $('#startdate').val();
       var module_id = !($("#module-name-filter").val()) ? 0 : $("#module-name-filter").val();
       var project_id = !($("#project-name-filter").val()) ? 0 : $("#project-name-filter").val();
       if((module_id != 0) && (project_id != 0)){
        $("#module-name-filter").trigger('change');
       }
       
     }
</script>
<style type="text/css">

.custom-datepicker{
    margin-left: 0px !important;
    }
</style>    
@stop
