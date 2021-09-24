@section('scripts')
<script>
    $(function () {
        $('.select2').select2();
        var table = $('#candidates-table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('employee.whistleblowersummarylist') }}",
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: "Employee Whistleblower Summary",
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]
                    }
                },
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [0, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                status = aData['status_color'];
                console.log(aData);
                /* Append the grade to the default row class name */
                if (status == "Open") {
                    $(nRow).addClass('open');
                } else if(status == "In Progress"){
                    $(nRow).addClass('in_progress');
                }else if(status == "Closed"){
                    $(nRow).addClass('closed');
                }else{
                    $(nRow).addClass('white');
                }
            },
            columns: [
                {
                    data: 'id',
                     name: 'id',
                     "visible": false,
                     "searchable": false
                },
                {
                    data: 'date',
                    name: 'date',
                    defaultContent: "--",
                },
                // {
                //     data: 'employee_details',
                //     name: 'employee_details',
                //     defaultContent: "--",
                // },
                {
                    data: 'created_by',
                    name: 'created_by',
                    defaultContent: "--"
                },
                {
                    data: 'customer',
                    name: 'customer',
                    defaultContent: "--"
                },
                {
                    data: 'subject',
                    name: 'subject',
                    defaultContent: "--"
                },
                // {
                //     data: 'category',
                //     name: 'category',
                //     defaultContent: "--"

                // },
                {
                    data: 'policy',
                    name: 'policy',
                    defaultContent: "--"
                },
                {
                    data: 'priority',
                    name: 'priority',
                    defaultContent: "--"

                },
                // {
                //     data: 'note',
                //     name: 'note',
                //     defaultContent: "--"

                // },
                {
                    data: null,
                    name: 'note',
                    sortable: false,
                    render: function (o) {
                        var notesDiv = '';
                        var notes = o.note;
                        if(notes.length > 100)
                        {
                            notesDiv += '<div class="text-wrap width-200">' +  notes.substr(0, 100) + '...</div>';
                        }else{
                            notesDiv += '<div class="text-wrap width-200">' + notes + '</div>';
                        }
                       return notesDiv;
                    },

                },
                {
                    data: 'status',
                    name: 'status',
                    defaultContent: "--"

                },
                {
                    data: null,
                    name: 'reg_manager_notes',
                    defaultContent: "--",
                    render: function (o) {
                        var notesDiv = '';
                        var notes = o.reg_manager_notes;
                        if(notes){
                            if(notes.length > 100)
                            {
                                notesDiv += '<div class="text-wrap width-200">' +  notes.substr(0, 100) + '...</div>';
                            }else{
                                notesDiv += '<div class="text-wrap width-200">' + notes + '</div>';
                            }
                        }

                       return notesDiv;
                    },

                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += o.latitude && o.longitude
                        ? '<a id="location" onclick="showlocation(' + o.latitude + ',' + o.longitude + ');" href="javascript:void(0);"><img width="40px" src="{{url("images/map_pointer.png")}}" ></a>'
                        : '';
                        return actions;
                    },
                },
                  @canany(['create_all_whistleblower','create_allocated_whistleblower'])
                {
                    data: null,
                    name: 'action',
                    sortable: false,
                    render: function (o) {
                        var actions = '';

                        actions = '<a href="#" class="edit fa fa-edit" data-id=' + o.id + '></a>';

                        return actions;
                    },

                }
                  @endcan

            ]

        });


/*Add new - modal popup -end */

 $('.add-new').on('click', function () {
    var title = $(this).data('title');
    $("#myModal").modal();
    $('#myModal form').trigger('reset');
    $('#myModal').find('input[type=hidden]').val('');
    $('#myModal').find('select[name="employee_id"]').select2().val('');
    $('#myModal .modal-title').text(title);
    $('#myModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
});

    $('#myModal #status_id').hide();
    $('#myModal #reg_manager_notes').hide();

    @can('edit_whistleblower_entries')
    $('#myModal #status_id').show();
    $('#myModal #reg_manager_notes').show();
    @endcan

    $('#whistleblower_submit').on('click', function(event) {
        event.preventDefault();
        var reg_manager_notes =  $('textarea[name="reg_manager_notes"]').val();
        var status =  $('select[name="status"]').val();
        if ($("#status_id").is(":hidden") && $("#reg_manager_notes").is(":hidden")) {
            $('#employee-whistleblower-form').submit();
        }else if(status == null || status == '' || status == undefined){
            $('#status_error').text('Action field is required')
            return false;
        }else if(reg_manager_notes == null || reg_manager_notes == '' || reg_manager_notes == undefined){
            $('#reg_manager_notes_error').text('Regional manager notes field is required')
            return false;
        }else{
            $('#employee-whistleblower-form').submit();
        }
    });

$('#employee-whistleblower-form').submit(function (e) {
      e.preventDefault();
      var $form = $(this);
      url = "{{ route('employee.whistleblower.store') }}";
      var formData = new FormData($('#employee-whistleblower-form')[0]);
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: 'POST',
        data: formData,
        success: function (data) {
          $("#myModal").modal('hide');
          console.log(data);
          if (data.success) {
            swal({
              title: "Saved",
              text: "Employee Whistleblower has been saved",
              type: "success"
            },function(){
                $('.form-group').removeClass('has-error').find('.help-block').text('');
                $('#employee-whistleblower-form')[0].reset();
                table.ajax.reload();
              });
          } else {
            console.log(data);
            swal("Oops", "The record has not been saved", "warning");
          }
        },
        fail: function (response) {
          console.log(response);
          swal("Oops", "Something went wrong", "warning");
        },
        error: function (xhr, textStatus, thrownError) {
          associate_errors(xhr.responseJSON.errors, $form);
        },
        contentType: false,
        processData: false,
      });
    });


     $('#candidates-table').on('click', '.edit', function(e){

        var id = $(this).data('id');
        var base_url = "{{route('employee.whistleblower-single', ':id')}}";
        var url = base_url.replace(':id', id);
        console.log(id,url);
        $('#type-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:url,
            type: 'GET',
            success: function (data) {
                console.log(data);
               if(data){
                $('input[name="id"]').val(data.id);
                $('input[name="date"]').val(data.created_at_date);
                $('input[name="created_by"]').val(data.created_by);
                // $('select[name="employee_id"]').val(data.employee_id);
                // $('select[name="employee_id"]').select2();
                $('input[name="whistleblower_subject"]').val(data.whistleblower_subject);
                $('select[name="whistleblower_category_id"]').val(data.whistleblower_category_id);
                $('select[name="whistleblower_category_id"]').select2();
                $('select[name="policy_id"]').val(data.policy_id);
                $('select[name="policy_id"]').select2();
                $('select[name="whistleblower_priority_id"]').val(data.whistleblower_priority_id);
                $('textarea[name="reg_manager_notes"]').val(data.reg_manager_notes);
                $('select[name="status"]').val(data.status);
                $('select[name="customer_id"]').val(data.customer_id).select2({ width: '100%' });
                $('textarea[name="whistleblower_documentation"]').val(data.whistleblower_documentation);
                $("#myModal").modal();
                $('#myModal .modal-title').text("Edit Employee Whistleblower Form: ")
               }
            },
            fail: function (response) {
                swal("Oops", "Something went wrong", "warning");
            },
            contentType: false,
            processData: false,
        });
    });




    });

    $(document).keyup(function(e) {jQuery
         if (e.key === "Escape") {
          $("#myModal").modal('hide');
       }
     });

    function initialize(myCenter, radius) {

        var renderContainer = document.getElementById("modal-content");
        var mapProp = {center: myCenter, zoom: 8};
        {!!\App\Services\HelperService::googleAPILog('map','Modules\Hranalytics\Resources\views\whistleblower\partials\scripts')!!}
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

    function showlocation(lat,long){
        $('#modalContent').modal('show');
        $('#modal-content').show();
        $('#modal-img-content').hide();
        $('#modalLabel').text('Location');

        var radius = 0;
        $('#modalContent').on('shown.bs.modal', function (e) {
            initialize(new google.maps.LatLng(lat, long), radius);
        });
    }
</script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}"></script>
@stop
