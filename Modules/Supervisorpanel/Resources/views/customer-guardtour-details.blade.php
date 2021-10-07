@extends('layouts.app') @section('content')
<div class="">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('customers.mapping')}}">Supervisor Panel</a></li>
        <li class="breadcrumb-item active">Customer Details</li>
    </ol>
</div>
<div class="customer-details-block">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 col-form-label  col-xs-3"><b>Project Number</b></label>
            <label class="col-md-6 col-form-label col-xs-3">{{$customer['details']['project_number'] ?? "--"}}
                <input type="hidden" name="customerid" id="customerid" value="{{$id}}" /></label>

            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 col-form-label  col-xs-3"><b>Client Contact</b></label>
                <label class="col-md-6 col-form-label col-xs-3">{{$customer['details']['contact_person_name'] ?? "--"}}
                </label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 col-form-label col-xs-3"><b>Supervisor</b></label>
                <label class="col-md-6 col-form-label  col-xs-3">{{ isset($customer['supervisor']['full_name']) ? $customer['supervisor']['full_name'] : "--"}} </label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 col-form-label col-xs-3"><b>Area Manager</b></label>
                <label class="col-md-6 col-form-label  col-xs-3">
                    {{isset($customer['areamanager']['full_name']) ? $customer['areamanager']['full_name'] : "--"}}</label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 col-form-label  col-xs-3"><b>Client</b></label>
                <label class="col-md-6 col-form-label col-xs-3">{{$customer['details']['client_name'] ?? "--"}}
                </label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 col-form-label  col-xs-3"><b>Client Phone</b></label>
                <label class="col-md-6 col-form-label col-xs-3">{{ ( $customer['details']['contact_person_phone_ext']!=null?($customer['details']['contact_person_phone'].' x'.$customer['details']['contact_person_phone_ext']):(null!=$customer['details']['contact_person_phone']?$customer['details']['contact_person_phone']:'--') )}}</label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 col-form-label col-xs-3"><b>Supervisor Phone</b></label>
                <label class="col-md-6 col-form-label  col-xs-3">{{ ( isset($customer['supervisor']['phone']) && isset($customer['supervisor']['phone_ext'])?($customer['supervisor']['phone'].' x'.$customer['supervisor']['phone_ext']):(isset($customer['supervisor']['phone'])?$customer['supervisor']['phone']:'--') )}}</label>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 col-form-label col-xs-3"><b>Area Manager Phone</b></label>
                <label class="col-md-6 col-form-label  col-xs-3">{{ (isset($customer['areamanager']['phone_ext'])?($customer['areamanager']['phone'].' x'.$customer['areamanager']['phone_ext']):(isset($customer['areamanager']['phone'])?$customer['areamanager']['phone']:'--') )}}</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 col-form-label  col-xs-3"><b>Address</b></label>
                <label class="col-md-6 col-form-label col-xs-3">{{$customer['details']['address'] ?? ""}}, {{$customer['details']['city'] ?? ""}}, {{$customer['details']['province'] ?? ""}}, {{$customer['details']['postal_code'] ?? ""}}.
                </label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 col-form-label  col-xs-3"><b>Client Email</b></label>
                <label class="col-md-6 col-form-label col-xs-3 email-break">{{$customer['details']['contact_person_email_id'] ?? "--"}}</label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 col-form-label col-xs-3"><b>Supervisor Email</b></label>
                <label class="col-md-6 col-form-label  col-xs-3 email-break">
                    {{$customer['supervisor']['email'] ?? "--"}}
                </label>
            </div>
        </div>
        {{--
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 label-adjust col-form-label col-xs-3"><b>Alternate Email</b></label>
                <label class="col-md-6 label-adjust col-form-label  col-xs-3 email-break">
                    {{$customer['supervisor']['alternate_email'] ?? "--"}}
                </label>
            </div>
        </div> --}}
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-6 col-form-label col-xs-3"><b>Area Manager Email</b></label>
                <label class="col-md-6 col-form-label  col-xs-3 email-break">
                    {{$customer['areamanager']['email'] ?? "--"}}
                </label>
            </div>
        </div>
    </div>
</div>


<div class="row">
<nav class="col-lg-9 col-md-9 col-sm-8">
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        @canany(['view_guard_tour','view_all_guard_tour'])
        <a class="nav-item nav-link active" id="nav-mandatory-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-mandatory" aria-selected="true">Guard Tour</a>
        @endcan
        @canany(['view_allocated_shift_journal','view_all_shift_journal','view_shift_journal'])
            <a class="nav-item nav-link" id="nav-recommended-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-recommended" aria-selected="false">@if($customer['details']['time_shift_enabled']==1)Time Shift @else Shift Journal @endif</a>
        @endcan
        @canany(['view_shift_journal_20_transaction', 'view_all_shift_journal_20_transaction'])
            @foreach($modules as $key=>$each_module)
                <a class="nav-item nav-link " id="{{$key}}" data-toggle="tab" href="#" role="tab" aria-controls="nav-{{$each_module}}" aria-selected="true"> {{$each_module}}</a>
            @endforeach
        @endcan
        {{-- <a class="nav-item nav-link" id="nav-status-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-status" aria-selected="false">Course Status</a> --}}
    </div>
</nav>

@if($customer['details']['shift_journal_enabled'] == 1 && $customer['details']['time_shift_enabled']==0)

<div class="col-lg-3 col-md-3 col-sm-3 text-align-right text-left-mob" id="shift_button" style="display: none">
            <a title="Add another" href="javascript:;" class="btn cancel ico-btn incident_add_button" data-toggle="modal" data-target="#incidentModal">
                <i class="fa fa-plus" aria-hidden="true"></i>Add Shift Journal</a>
</div>
@endif
</div>
{{ Form::open(array('url'=>'#','method'=> 'POST')) }}
{{csrf_field()}}
<div id="filter">
        <div class="col-md-4">Filter By:</div><br>

<div class="form-group row mx-0">

    <div class="col-md-1" >From Date</div>


    <div class="col-sm-2" id="from_date">
             <input type="text" id="fr_date" name="fr_date" class="form-control datepicker" max="2900-12-31" value="{{date('Y-m-d', strtotime("-2 days"))}}" />

            <small class="help-block"></small>
        </div>
        <div class="col-md-0"> To Date</div>
        <div class="col-sm-2" id="to_date">
                <input type="text" id="t_date" name="to_date" class="form-control datepicker" max="2900-12-31" value="{{date('Y-m-d')}}" />
                <small class="help-block"></small>
            </div>

            <div class="col-md-0"> Employee Name</div>
            <div class="col-sm-2" id="emp_name">

                 {{ Form::select('emp_name',[''=>'Please Select']+$emp_array, null,array('id'=>'emp_id','class' => 'form-control select2')) }}
                    <small class="help-block"></small>

            </div>
            <div class="col-sm-2">
                    <input class="button btn btn-primary blue" id="search" type="button" value="Filter" onclick="filter()">
            </div>

</div>

    </div>
    {{ Form::close() }}

<!-- Incident Add Modal - Start -->
<div class="modal fade" id="incidentModal" data-backdrop="static" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Shift Journal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>

            </div>
            {{ Form::open(array('url'=>'#','id'=>'shift-journal','class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
            <div class="modal-body">
                <input type="hidden" name="customer_id" value="{{$customer['details']['id']}}">
                <div class="form-group row" id="note">
                    <label for="note" class="col-sm-4 control-label">Notes</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" maxlength="10000" rows=6 name="note" placeholder="Notes" required></textarea>
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-edit','id'=>'mdl_save_change'))}}
                <button class="btn btn-edit" data-dismiss="modal" aria-hidden="true" onclick="$('#shift-journal').trigger('reset');">Cancel</button>
            </div>
            {{ Form::close() }}

        </div>
    </div>
</div>
<!-- Incident Add Modal - End -->
{{--
<div> --}}


    <table class="table table-bordered" id="shift-journal-table">
        <thead>

        </thead>
    </table>

</div>


    <div class="modal fade" id="modalContent" data-backdrop="static" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel"></h4>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                       <span aria-hidden="true">×</span>
                     </button>
                </div>
                 <div id="modal-content" style="height: 500px;" class="modal-body">
                 </div>
                 <div align="center"  style="display: none;"  id="modal-img-content" style="height: 550px;" class="modal-body">
                      <div style="text-align: center;" >
                        <img  style="left: 50%;max-width: 600px;"  height="400px" id="ImgContainer" src="">
                   </div>

                </div>

           </div>
        </div>
    </div>

@stop @section('scripts')
<script>

    function showimage(img_id){
       $('#modalContent').modal('show');
       $('#modal-img-content').show();
        $('#modal-content').hide();
       $('#modalLabel').text('');
        var view_url = '{{ route("filedownload", [":id",":module",":attachment"]) }}';
          view_url = view_url.replace(':id', img_id);
          view_url = view_url.replace(':module', 'shift-module');
           view_url = view_url.replace(':attachment', false);
         $('#ImgContainer').attr('src',view_url);

   }

       function showVideo(video_id){
        $('#img_div').hide();
          var view_url = '{{ route("filedownload", [":id",":module"]) }}';
          view_url = view_url.replace(':id', video_id);
          view_url = view_url.replace(':module', 'shift-module');
          window.open(view_url, '_blank');
      }

    $(document).ready(function() {
        $('#filter').hide();
        var time_shift_enabled = JSON.parse('{{ json_encode($customer['details']['time_shift_enabled']) }}');
        if (time_shift_enabled && $('#nav-recommended-tab').hasClass('active')) {
            url = "{{ route('timeshift.list',$id) }}";
            datatableLoad(url)
        }
        @canany(['view_allocated_shift_journal', 'view_all_shift_journal', 'view_shift_journal'])
        $('#shift_button').css('display', 'block');
        url = "{{ route('shiftJournal.list',$id) }}"
        @endcan
        @canany(['view_guard_tour', 'view_all_guard_tour'])
        $('#shift_button').css('display', 'none');

        url = "{{ route('guardTour.list',$id) }}"
        @endcan
        datatableLoad(url);
        $('.nav-tabs a').click(function(ev) {
            $('#course-table').dataTable().fnDestroy();

            id = $(this).attr('id')
            var customerid = $("#customerid").val();
            if (id == "nav-mandatory-tab") {
                $('#shift_button').css('display', 'none');
                $('#filter').hide();
                url = "{{ route('guardTour.list',$id) }}"
            } else if (time_shift_enabled && id == "nav-recommended-tab") {
                $('#shift_button').css('display', 'none');
                $('#filter').hide();
                url = "{{ route('timeshift.list',$id) }}";
            } else if (id == "nav-recommended-tab") {
                $('#shift_button').css('display', 'block');
                $('#filter').hide();
                url = "{{ route('shiftJournal.list',$id) }}";
            } else {

                $('#shift_button').css('display', 'none');
                $('#filter').show();
                var base_url = "{{ route('shift.module',[':module_id',':customer_id']) }}";
                var base_url1 = base_url.replace(':module_id', id);
                 var url = base_url1.replace(':customer_id', customerid);
                }
                $("#emp_id").val('')
                //$("#t_date").val('')
                //$("#fr_date").val('')
                datatableLoad(url);
            });

        });


        $('#shift-journal').submit(function(e) {
            e.preventDefault();
            var $form = $(this);
            var url = "{{ route('shiftJournal.save') }}";
            var formData = new FormData($('#shift-journal')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.success) {
                        swal({
                                title: "Saved",
                                text: "Shift journal has been Saved",
                                type: "success"
                            },
                            function() {
                                $('#incidentModal').modal('hide');
                                id = "nav-recommended-tab";
                                datatableLoad('{{ route('shiftJournal.list',$id) }}');
                                $('#nav-recommended-tab').addClass('active');
                                $('#shift_button').css('display', 'block');
                            }
                        );
                    } else {
                        console.log('else', data);
                    }
                },
                fail: function(response) {
                    console.log(response);
                },
            });
        });

        function filter() {

            var base_url = "{{ route('shift.module',[':module_id',':customer_id']) }}";
            var base_url1 = base_url.replace(':module_id', id);
            var url = base_url1.replace(':customer_id', {{$id}});
            datatableLoad(url)

        }
        function datatableLoad(url) {
        var name = $('#emp_id').val();
        var from_date = $('#fr_date').val();
        var to_date = $('#t_date').val();
         $.ajax({
            url: url,
            type: 'GET',
            data :{'name':name , 'from_date':from_date,'to_date':to_date},
            success: function(response) {},
            complete: function(complete_response) {
                var answers = [];
                var ans = [];
                var cols = [];
                var answer = [];
                answers = complete_response.responseJSON.data;

                if(answers[0].Date !== null) {
                 module_order = complete_response.responseJSON.module_order;
                } else {
                 module_order = 0;
                }

                var exampleRecord = answers[0];
                //get keys in object. This will only work if your statement remains true that all objects have identical keys
                var keys = Object.keys(exampleRecord);
                //for each key, add a column definition
                keys.forEach(function(k) {
                    cols.push({
                        title: k,
                        //optionally do some type detection here for render function
                    });

                });
              //  console.log(data);

                $.each(answers, function(key, value) {
                    inner_array = [];
                    $.each(value, function(inner_key, inner_value) {
                        inner_array.push(inner_value);

                    });
                    answer.push(inner_array);
                });

                if ($.fn.DataTable.isDataTable('#shift-journal-table')) {
                    $('#shift-journal-table').DataTable().destroy();
                    $('#shift-journal-table').empty();
                };
                //initialize DataTables
                var table = $('#shift-journal-table').DataTable({
                    destroy: true,
                     bAutoWidth: false,
                    columns: cols
                });
                //add data and draw
                table.clear().draw();

                if(answers.length > 0) {
                    table.rows.add(answer).draw();
                }
                $('.notesspan').closest('table').find('th').eq($('.notesspan').parent().index()).css('width','20%');

          $.fn.dataTable.ext.errMode = 'hide';

            $('#shift-journal-table').DataTable({
                    destroy: true,
                    order: module_order,
                    columnDefs: [
                    {
                    targets: [ 0 ],
                    visible: false,
                    searchable: false
                    }],
                    lengthMenu: [
                        [10, 25, 50, 100, 500, -1],
                        [10, 25, 50, 100, 500, "All"]
                        ],
                    dom: 'Blfrtip',
                    buttons: [
                        {
                        extend: 'pdfHtml5',
                        pageSize: 'A2',
                        },
                        {
                        extend: 'excelHtml5',
                        },
                        {
                        extend: 'print',
                        pageSize: 'A2',
                        },
                        ],
                  //  "rowCallback": function(row, data, displayNum, displayIndex, dataIndex) {
                   //     $(row).find('td').css('background-color', 'green').css('color', 'white');

                  //  }
                });

                if (inner_array.every(element => element === null)) {
                    table.clear().draw();

                }
            }
        });

    }

    $('#incidentModal').on('hidden.bs.modal', function() {
        $(this).find("textarea").val('').end();

    });

    function tConvert(time) {
        // Check correct time format and split into components
        time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];

        if (time.length > 1) { // If time format correct
            time = time.slice(1); // Remove full string match value
            time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
            time[0] = +time[0] % 12 || 12; // Adjust hours
        }
        time.splice(3, 1);
        return time.join(''); // return adjusted time or original string
    }

    function showlocation(lat,long){
       $('#modalContent').modal('show');
       $('#modal-content').show();
       $('#modal-img-content').hide();
       $('#modalLabel').text('Location');

       var radius = 150;
             $('#modalContent').on('shown.bs.modal', function (e) {
                 initialize(new google.maps.LatLng(lat, long), radius);
             });
    }


    function initialize(myCenter, radius) {

            var renderContainer = document.getElementById("modal-content");
            var mapProp = {center: myCenter, zoom: 8};
            var map = new google.maps.Map(renderContainer, mapProp,{
                gestureHandling  : 'greedy',
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
            marker.addListener('dragend', function (event)
            {
                $('#lat').val(event.latLng.lat());
                $('#long').val(event.latLng.lng());
            });

            //Add event listner on drag event of marker
            marker.addListener('drag', function (event) {
                circle.setOptions({center: {lat: event.latLng.lat(), lng: event.latLng.lng()}});
            });

            //Add listner to change radius value on field
            circle.addListener('radius_changed', function () {
                $('#radius').val(circle.getRadius());
            });

            //Add event listner on drag event of circle
            circle.addListener('drag', function (event) {
                marker.setOptions({position: {lat: event.latLng.lat(), lng: event.latLng.lng()}});
            });

            //changing the radius of circle on changing the numeric field value
            $("#radius").on("change paste keyup keydown", function () {
                //radius = $("#radius").val();
                circle.setRadius(Number($("#radius").val()));

            });
        }

$('table').on('mouseover', '#popover', function(e){
    $(e.target).popover('show');
});
$('table').on('mouseout', '#popover', function(e){
    $(e.target).popover('hide');
});

$(function () {
$('#emp_id').select2();
});

</script>
<style type="text/css">
    .nav-tabs .nav-link.active {
        background: #003A63 !important;
    }

    .nav-tabs {
        margin-bottom: 9px;
    }

    a:hover.nav-link.active {
        color: #003A63!important;
    }

    a:hover.nav-link {
        color: #003A63!important;
    }
    .fa-file-video {
       color: #003A63!important;
    }
    .fa-file-image{
        color: #003A63!important;
    }

    .fa-map-marker {
       color: #003A63!important;
    }

    .fa-sticky-note{
       color: #ffffff!important;
    }

    .fa-file-alt{
        color: #003A63!important;
    }
</style>
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> --}}
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
@include('supervisorpanel::scripts') @endsection
