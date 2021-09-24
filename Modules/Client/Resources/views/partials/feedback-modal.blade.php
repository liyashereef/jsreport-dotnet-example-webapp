<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Performance Form</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
        {{ Form::open(array('url'=>'#','id'=>'client-employee-rating-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{ Form::hidden('id', null) }}
        {{ Form::hidden('employee_id', null, array('id'=>'employee_id')) }}
        {{ Form::hidden('customer_id', null, array('id'=>'customer_id')) }}
        {{ Form::hidden('feedback_id', null, array('id'=>'feedback_id')) }}
        {{ Form::hidden('feedback', null, array('id'=>'feedback')) }}
        {{ Form::hidden('review_permission', null) }}
        {{ Form::hidden('emp', null, array('id'=>'emp')) }}

        <div class="modal-body">
            <div class="form-group" id="employee_rating_lookup_id">
            <label for="employee_rating_lookup_id" class="col-sm-3 control-label">Rating</label>
            <div class="col-sm-11">
                {!!Form::select('employee_rating_lookup_id',[null=>'Please Select'] + $rating_lookups,null, ['class' => 'form-control'])!!}
                <small class="help-block" style="font-size: 85%;"></small>
            </div>
            </div>
            <div class="form-group" id="customer_feedback">
            <label for="customer_feedback" class="col-sm-3 control-label">Customer Feedback</label>
            <div class="col-sm-11">
                {{ Form::textarea('customer_feedback',null,array('class'=>'form-control')) }}
                <small class="help-block" style="font-size: 85%;"></small>
            </div>
            </div>

           <div class="form-group" id="status_lookup_id" >
            <label for="status" class="col-sm-3 control-label">Status <span class="mandatory">*</span> </label>
            <div class="col-sm-11">
            <select name="status_lookup_id"  style="width: 100%;" class="form-control">
                <option value=0 selected>Please select</option>
                @if($statusList)
                @foreach ($statusList as $type)
                <option  value="{{ $type->id }}">{{$type->name}} </option>
                @endforeach
                @endif
            </select>
            <span class="help-block" style="font-size: 85%;"></span>
             </div>
            </div>

            <div class="form-group" id="reg_manager_notes" >
            <label for="reg_manager_notes" class="col-sm-6 control-label">Regional Manager Notes <span class="mandatory">*</span></label>
            <div class="col-sm-11">
              {{ Form::textarea('reg_manager_notes',null,array('class'=>'form-control','placeholder'=>"Regional Manager Notes",'maxlength'=>1000)) }}
            <span class="help-block" style="font-size: 85%;"></span>
            </div>
            </div>

        </div>
        <div class="modal-footer">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'submit_client_feedback'))}}
            {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
        </div>
        {{ Form::close() }}
        </div>
    </div>
</div>

<script>

  // $('#myModalLabel').html(feedback+''+emp);
    $('#myModal #status_lookup_id').hide();
    $('#myModal #reg_manager_notes').hide();

    @can('review_client_feedback')
    $('#myModal #status_lookup_id').show();
    $('#myModal #reg_manager_notes').show();
    @endcan

    /* Posting data to ClientEmployeeFeedbackController - Start*/
    $('#client-employee-rating-form').submit(function (e) {
      e.preventDefault();
      var $form = $(this);
      var isHidden = 0;
      var id =$('#client-employee-rating-form input[name="id"]').val();
      if($("#status_lookup_id").is(":hidden") && $("#reg_manager_notes").is(":hidden")) {
        var isHidden = 1;
        $('input[name="review_permission"]').val(isHidden);
      }else{
        var isHidden = 0;
        $('input[name="review_permission"]').val(isHidden);
      }
      $('select[name="employee_rating_lookup_id"]').prop('disabled', false);
      url = "{{ route('client.employee-rating.store') }}";
      var formData = new FormData($('#client-employee-rating-form')[0]);
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
              text: "Client feedback has been saved successfully",
              type: "success"
            },function(){
                $('.form-group').removeClass('has-error').find('.help-block').text('');
                $('#client-employee-rating-form')[0].reset();
                table.ajax.reload();
              });
          } else {
            console.log(data);
            swal("Oops", "The record has not been saved", "warning");
          }
        },
        fail: function (response) {
          console.log(response);
          if(id){
            $('select[name="employee_rating_lookup_id"]').prop('disabled', true);
          }else{
            $('select[name="employee_rating_lookup_id"]').prop('disabled', false);
          }
          swal("Oops", "Something went wrong", "warning");
        },
        error: function (xhr, textStatus, thrownError) {
          if(id){
            $('select[name="employee_rating_lookup_id"]').prop('disabled', true);
          }else{
            $('select[name="employee_rating_lookup_id"]').prop('disabled', false);
          }
          associate_errors(xhr.responseJSON.errors, $form);
        },
        contentType: false,
        processData: false,
      });
    });
    /* Posting data to ClientEmployeeFeedbackController - End*/

    /*$('.emprate-cancel').click(function () {
      $('#myModal').modal('hide');
      $(".stacked-bar-graph-header-size.payperiod.active a.view-add").click();
      swal({
                                title: "Saved!",
                                text: "Survey has been saved successfully",
                                type: "success",
                                confirmButtonText: "OK",
      });
        $("html, body").scrollTop(0);
    });*/

</script>
