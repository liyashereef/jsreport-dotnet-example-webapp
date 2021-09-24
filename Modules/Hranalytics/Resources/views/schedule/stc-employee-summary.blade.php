@extends('layouts.app')
@section('content')
<div class="table_title" id="map_heading">
     <h4>STC Employee Summary </h4>

     @can(['view_stc_employee_mapping'])
<div id="map_view_div" class="row margin-bottom-8 padding-15">
    <div class="col-xs-12 col-sm-12 col-md-12 text-right">
        <form action="{{ route('candidate.schedule.mapping') }}" target="_blank" method="POST" id="map_view_submit">
            {{csrf_field()}}  {{ Form::hidden('employee_id_array') }}
            <input type="submit" data-days="" data-shifts="" value="Map View" class="btn submit schedule-map-view" />
        </form>
    </div>
</div>
@endcan
</div>

<div class="col-sm-10" style="float: right;">
<div class="row">
    <div class="col-sm-4">
      <select id="employee_filter" name="employee_filter" class="form-control select2">
        <option value="0">--Select Employee--</option>
        @foreach($employeeList as $ky => $employee)
          <option value="{{$ky}}">{{$employee}}</option>
        @endforeach
      </select>
    </div>

    <div class="col-sm-3">
    <label class="control-label">
      <input type="checkbox" id="spare_pool" name="spare_pool" checked/>
      Spares Only</label>
    </div>
  </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered" id="schedules-table">
        <thead>
            <tr>
                <th width="6%" class="sorting">Employee ID
                </th>
                <th width="10%" class="sorting">Employee Name
                </th>
                <th width="8%" class="sorting">Cell Phone
                </th>
                <th width="10%" class="sorting">Email Address
                </th>
                <th width="8%">Role
                </th>
                <th width="15%" class="sorting">Address
                </th>
                <th width="15%" class="sorting">City
                </th>
                <th width="5%" class="sorting">Postal Code
                </th>
                <th width="5%" class="sorting">Rating</th>
                <th width="10%" class="sorting">Security Experience
                </th>
                <th width="10%" class="sorting">Years in Canada
                </th>
              {{-- <th width="5%" class="sorting">Wage
                </th>--}}
                <th width="2%" class="sorting">Previous Attempts
                </th>
                {{-- <th width="5%">Event Log
                </th> --}}
                <th width="5%" class="sorting">Score
                </th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="myModalLabel">Performance Log</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>

            <div class="modal-body">

            </div>

            <div style="display:none;padding: 50px 10px 2px 50px;" id="log-form" >
            {{ Form::open(array('url'=>'#','id'=>'performance-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            <input type="hidden" name="id" />
            <input type="hidden" name="user_id" id="user_id" value=""/>
            <input type="hidden" name="employee_id" id="employee_id" value=""/>
            <div class="form-group row" style="margin-left:0px;" id="customer_id">
                <label for="customer" class="col-sm-3">Select Customer</label>
                <div class="col-sm-8">
                  {!!Form::select('customer_id',[null=>'Please Select'] + $project_list,null, ['class' => 'form-control select2','id'=>'customer_id'])!!}
                  <small class="help-block"></small>
                </div>
              </div>
              <div class="form-group row" style="margin-left:0px;" id="subject">
                <label for="subject" class="col-sm-3">Subject</label>
                <div class="col-sm-8">
                  {{ Form::text('subject',null,array('class'=>'form-control')) }}
                  <small class="help-block"></small>
                </div>
              </div>
              <div class="form-group row" style="margin-left:0px;">
                <label for="employee_rating_lookup_id" class="col-sm-3 control-label" style="vertical-align: top;">Rating</label>
                <div class="col-sm-8">
                  {!!Form::select('employee_rating_lookup_id',[null=>'Please Select'] + $ratingLookups,null, ['class' => 'form-control','id'=>'employee_rating_lookup_id'])!!}
                  <small class="help-block"></small>
                </div>
              </div>
              <div class="form-group row" style="margin-left:0px;">
                <label for="policy_id" class="col-sm-3 control-label">Policy</label>
                <div class="col-sm-8">
                  {!!Form::select('policy_id',[null=>'Please Select'] ,null, ['class' => 'form-control','id' => 'policy_id'])!!}
                  <small class="help-block"></small>
                </div>
              </div>

              <div class="form-group"  style="display: none;" id="description-div">
                <label for="description" class="col-sm-5 control-label">Policy Description</label>
                <div class="col-sm-12">
                 <div style="height: 120px;width:94%;border:1px solid #ced4dafc;overflow-y:auto;">
                   <label id="description">
                   </label>
                 </div>
                  <small class="help-block"></small>
                </div>
              </div>
              <div class="form-group row" style="margin-left:0px;margin-bottom:2px;">
               <label for="subject" class="col-sm-3">Notify Employee</label>
                <div class="col-sm-8">
                {{ Form::checkbox('notify_employee',null,'checked', array('class'=>'form-control','id'=>'notify_employee','style'=>'width:22px;height:30px;')) }}
                  <small class="help-block"></small>
                </div>
              </div>
              <div class="form-group" id="supporting_facts">
                <label for="supporting_facts" class="col-sm-3 control-label">Supporting Facts</label>
                <div class="col-sm-12">
                  {{ Form::textarea('supporting_facts',null,array('class'=>'form-control','rows' => 3, 'cols' => 42,'style' => 'width:94%')) }}
                  <small class="help-block"></small>
                </div>
              </div>
            </div>


            <div style="display: none;text-align:center;" class="modal-footer">
              {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
              {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            </div>
            {{ Form::close() }}
          </div>
    </div>
</div>
@stop @section('scripts')

<script>
     var rules_arr = jQuery.parseJSON('{!! json_encode($template_rule_arr) !!}');
     var candidate_arr = [];
         $(function () {
          var urlPath = '{{ route("stc.employee-summary.list",[":employeeId",":spare"]) }}';
          urlPath = urlPath.replace(':employeeId', 0);
          urlPath = urlPath.replace(':spare', 1);

          $('.select2').select2();
         /*get the selected customer and candidates id- End*/
            $.fn.dataTable.ext.errMode = 'throw';
            try{
            var table = $('#schedules-table').DataTable({
                responsive: true,
                bProcessing: false,
                 dom: 'Blfrtip',
                  buttons: [{
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: ['0,1,2,3,4,5,6,7,8,9,10,11,12']
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ['0,1,2,3,4,5,6,7,8,9,10,11,12']
                    }
                },
            ],
              processing: true,
              serverSide: true,
              fixedHeader: false,
                ajax: {
                    url: urlPath, // Change this URL to where your json data comes from
                    type: "GET", // This is the default value, could also be POST, or anything you want.
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                 columnDefs: [
                 {className: "dt-center", targets: [8,9,10,11,12]}
                  ],
                createdRow: function (row, data, dataIndex) {
                   candidate_arr.push(data.id);
                   var avg_score = data.avg_score;
                    rules_arr.forEach(function(item) {
                   if((avg_score>=item.min_value)&&(avg_score<=item.max_value))
                         {
                    $(row).find('td:eq(12)').css('background-color', item.color.color_class_name);
                    $(row).find('td:eq(12)').addClass('font-color-'+ item.color.color_class_name);
                     }
                     });
                },
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [
                    {
                        data: 'employee_no',
                        name: 'employee_no',
                    },
                    {
                        data: null,
                        name: 'user_name',
                        render: function (row) {
                            actions = '';
                            var url ='#';
                            var title ='Not Available';
                             if(row.candidate_id != ""){
                                title ='View';
                                url = '{{ route("candidate.view", [":candidate_id",":job_id"]) }}';
                            url = url.replace(':candidate_id', row.candidate_id);
                            url = url.replace(':job_id', (row.job_id));
                            }
                            actions += '<a title="'+ title +'" href="' + url + '">' +
                                row.user_name + '</a>';
                            return actions;
                        }
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role',
                        orderable: false,
                    },
                    {
                        data: 'employee_address',
                        name: 'employee_address'
                    },
                    {
                        data: 'employee_city',
                        name: 'employee_city'
                    },
                    {
                        data: 'employee_postal_code',
                        name: 'employee_postal_code'
                    },
                    {
                        data: null,
                        name: 'employee_rating',
                        render: function (row) {
                          if(row.employee_rating != ""){
                            actions = '<a title="View" href="#" onclick="ratingDetails('+ row.user_id +');">' +
                             ( row.employee_rating ? parseFloat(row.employee_rating).toFixed(3) : '' ) + '</a>';
                          }else{
                            actions = '<a title="Add Rating" href="#" onclick="addNewRating('+ row.user_id +');">--</a>';
                          }
                          return actions;
                        }
                    },
                    {
                        data: 'years_of_security',
                        name: 'years_of_security'
                    },
                    {
                        data: 'being_canada_since',
                        name: 'being_canada_since'
                    },
                  /*  {
                        data: null,
                        name: 'wageexpectation.wage_expectations_from',
                        render: function (data) {
                            return '$' + (parseFloat(data.wageexpectation.wage_expectations_from).toFixed(2))+'-$' + (parseFloat(data.wageexpectation.wage_expectations_to)).toFixed(2)
                        }
                    },*/
                    {
                        data: 'prev_attempt',
                        name: 'prev_attempt',
                    },

                    {
                        data: 'avg_score',
                        name: 'avg_score',
                        orderable: true,
                        render: function (avg_score) {
                            return Number(avg_score) + '%';
                        }
                    }
                ]
            });
        } catch(e){
            console.log(e.stack);
        }

            $('#employee_filter, #spare_pool').change( function() {
              var employeeId = $('#employee_filter').val();
              var spare = $('#spare_pool').is(":checked") ? 1: 0;
              var urlPath = '{{ route("stc.employee-summary.list",[":employeeId",":spare"]) }}';
              urlPath = urlPath.replace(':employeeId', employeeId);
              urlPath = urlPath.replace(':spare', spare);
              table.ajax.url(urlPath).load();
            });

            $('.schedule-map-view').on('click', function () {
                $('#map_view_submit input[name="employee_id_array"]').val(candidate_arr);
            });


       $('#performance-form').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        url = "{{ route('employee.rating') }}";
        var formData = new FormData($('#performance-form')[0]);
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: url,
          type: 'POST',
          data: formData,
          success: function (data) {
            if (data.success) {
              swal({
                title: "Saved",
                text: "The record has been saved",
                type: "success"},
              function(){
                $("#myModal").modal('hide');
                table.ajax.reload( null, false );
              });
            } else {
              swal("Oops", "The record has not been saved", "warning");
            }
          },
          fail: function (response) {
            swal("Oops", "Something went wrong", "warning");
          },
          error: function (xhr, textStatus, thrownError) {
            associate_errors(xhr.responseJSON.errors, $form);
          },
          contentType: false,
          processData: false,
        });
      });

        //To reset the hidden value in the form
        $('#myModal').on('hidden.bs.modal', function() {
          $('#myModal .modal-body').show();
          $('#myModal #log-form').hide();
          $('#myModal .modal-footer').hide();
        });
});


function ratingDetails(user_id){
    useid = "{{ Auth::user()->id }}";
    $('#myModal #employee_id').val(user_id);
    $('#myModal #user_id').val(useid);
    url = "{{ route('employee.ratings-summary', 'id') }}";
    var url = url.replace('id', user_id)
    $.ajax({
        url: url,
        type: 'GET',
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
       success:function(response){
        $("#myModal").modal();
        $('#myModal .modal-body').html(response);
       },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,

      });
}

function addRating(){
    $('#myModal .modal-body').hide();
    $('#myModal #log-form').show();
    $('#myModal .modal-footer').show();
    $('#performance-form').trigger('reset');

}

function addNewRating(user_id){
    $("#myModal").modal();
    useid = "{{ Auth::user()->id }}";
    $('#performance-form').trigger('reset');
    $('#myModal #employee_id').val(user_id);
    $('#myModal #user_id').val(useid);
    $('#myModal .modal-body').hide();
    $('#myModal #log-form').show();
    $('#myModal .modal-footer').show();


}

        $('#employee_rating_lookup_id').on('change', function() {
          $('#description-div').hide();
          var id = $(this).val();
          var base_url = "{{route('employee.ratings-getPolicy',':id')}}";
          var url = base_url.replace(':id', id);
          $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                policies = data;
                $('#policy_id').empty().append($("<option></option>")
                    .attr("value",0)
                    .text('Please Select'));;
                $.each(data, function(index, policy) {
                   $('#policy_id')
                   .append($("<option></option>")
                    .attr("value",policy['id'])
                    .text(policy['name']));
               });

            }
        });
      })

      $('#policy_id').on('change', function() {
          var id = $(this).val();
           $.each(policies, function(index, policy) {
                if(policy['id']==id){
                  $('#description-div').show();
                  $('#description').text(policy['description']);
                }
            });

        });

        $('#notify_employee').change(function() {
       if(!$(this).is(':checked')){
        swal({
                title: "Are you sure you want to turn off notification?",
                text: "If turned off, employee will not be notified of this rating.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                cancelButtonText: "Cancel",
                confirmButtonText: "Yes, turn off",
                showLoaderOnConfirm: true,
                closeOnConfirm: true
            },
            function () {
              $('#notify_employee').prop('checked', false);
            });
            $('#notify_employee').prop('checked', true);
       }
     });



</script>
<style type="text/css">
  #map_heading {
  display: flex;
  justify-content: space-between;
}

/* Important part */
.modal-dialog{
    overflow-y: initial !important
}
.modal-body{
    height: 500px;
    overflow-y: auto;
}
</style>
@stop
