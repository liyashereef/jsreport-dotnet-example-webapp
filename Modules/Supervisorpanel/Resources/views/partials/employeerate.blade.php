<div class="modal fade employee-rate" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Employee Rating</h4>
        <!--<button type="button" class="close emprate-cancel" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>-->
      </div>
      {{ Form::open(array('url'=>'#','id'=>'performance-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
      {{ Form::hidden('id', null) }}
      {{ Form::hidden('user_id',Auth::user()->id) }}
      <input type="hidden" name="customer_id" value="{{$formated_template['customer_id']}}"/>
      <div class="modal-body">
        <div class="form-group row" style="margin-left:0px;" id="employee_id">
          <label for="employee_id" class="col-sm-3 control-label">Employee</label>
          <div class="col-sm-8">
            {!! Form::select('employee_id', [null=>'Please Select'] +  $employeeList,null, ['class' => 'form-control', 'id' => 'employee_id']) !!}
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
                {{ Form::checkbox('notify_employee',null,'checked', array('class'=>'form-control','id'=>'notify_employee','style'=>'width:30px;height:30px;')) }}
                  <small class="help-block"></small>
                </div>
              </div> 
              <div class="form-group" id="supporting_facts">
                <label for="supporting_facts" class="col-sm-5 control-label">Supporting Facts</label>
                <div class="col-sm-12">
                  {{ Form::textarea('supporting_facts',null,array('class'=>'form-control','rows' => 3, 'cols' => 42,'style' => 'width:94%')) }}
                  <small class="help-block"></small>
                </div>
              </div>
      </div>
      <div class="modal-footer">
        {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
        <div class="button btn btn-primary blue cancel emprate-cancel">Finish</div>
        {{-- Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))--}}
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

    <script>
      /* Posting data to EmployeeAllocationController - Start*/
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
              var employee_id =data.success.employee_id;
              swal({
                title: "Saved",
                text: "Employee rating has been saved",
                type: "success"},function(){
                  $('.form-group').removeClass('has-error').find('.help-block').text('');
                  $('#performance-form')[0].reset();
                  $('#employee_id option[value='+employee_id+']').prop('disabled',true);
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
      /* Posting data to EmployeeAllocationController - End*/

      $('.emprate-cancel').click(function () {
        $('#myModal').modal('hide');
        $(".stacked-bar-graph-header-size.payperiod.active a.view-add").click();
        swal({
                                  title: "Saved",
                                  text: "Survey has been saved successfully",
                                  type: "success",
                                  confirmButtonText: "OK",
        }, function(){
                                   location.reload();
                                });
          $("html, body").scrollTop(0);
      });

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
