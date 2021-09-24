@extends('layouts.app') @section('content')
<style>
    .confirmation_policy {
        text-align: center;
        margin-top: 30px;
    }
</style>
<iframe src="{{Config::get('globals.policyPath')}}{{$policy_details['policy_file']}}" width="100%"
    height="670"></iframe>
{{ Form::open(array('url'=>'#','id'=>'acceptance','method'=> 'POST')) }}
@if ($boolean==1)
<div class="form-group col-md-12 col-xs-12 col-sm-12 confirmation_policy"><b>I have read, understood and accept the
        above policy
    </b>
</div>
{{ Form::hidden('employee_id', Auth::user()->id) }}
{{ Form::hidden('policy_id', $policy_details['id']) }}
<div style="text-align:center;">
    <div id="common_form">

        <div style="color:#717980;" class="col-md-6 col-xs-6 col-sm-6">Signature:</div>
        <small id="sign-message" style="color: red;"></small>

        <div id="signature-pad" class="signature-pad">
            <div class="signature-pad--body">
                <canvas id="signature-content"></canvas>
            </div>
            <br>
            <div class="signature-pad--footer">
                <div class="signature-pad--actions">
                    <div style="text-align: center;">
                        <button type="button" class="button-clear" data-action="clear">Clear</button>
                    </div>
                </div>
            </div>
            <br>
        </div>

    </div>


    @if($policy_details['enable_agree_or_disagree'] == 1)
    <div class=" col-md-12 col-xs-12 col-sm-12">

        {{ Form::button('Agree', array('class'=>' button btn btn-primary blue agree_btn','id'=>'agree', 'onclick'=>'this.form.reset()'))}}

        {{ Form::button('Disagree', array('class'=>' button btn btn-primary blue agree_btn','id'=>'disagree','onclick'=>'this.form.reset()'))}}
    </div>

    <div id="agree_reason_div" class="center" style="display:none;">
        {{ Form::hidden('agree', 1,['class' => 'form-control'] )}}
        <div class="sub-label col-md-6 col-xs-6 col-sm-6" id="compliance_policy_agree_reason_id">
            <label for="agree_reason" class="col-sm-6 control-label"></label>
            {{ Form::select('compliance_policy_agree_reason_id',$agree_reasons,null , array('placeholder' => 'Please select the reason','class'=>'form-control','id'=>'compliance_policy_agree_reason_id'))}}
            <small class="help-block"></small>
        </div>
        @if($policy_details['enable_agree_textbox'])
        <div class="sub-label col-md-6 col-xs-6 col-sm-6" id="comment">
            <label for="comments" class="col-sm-2 control-label"></label>
            {{ Form::textarea('comment',null, array('placeholder'=>'Comments','class'=>'form-control','id'=>'comment'))}}
            <small class="help-block"></small>
        </div>
        @endif
        <div class="margin-top-20 col-md-12 col-xs-12 col-sm-12">
            {{ Form::submit('OK', array('class'=>'stacked-bar-graph-content-size button btn btn-primary blue','id'=>'mdl_save_change'))}}

        </div>

    </div>

    <div id="disagree_reason_div" class="center" style="display:none;">
        {{ Form::hidden('agree', 0 ,['class' => 'form-control'])}}
        <div class="sub-label col-md-6 col-xs-6 col-sm-6" id="compliance_policy_agree_reason_id">
            <label for="disagree_reason" class="col-sm-6 control-label"></label>
            {{ Form::select('compliance_policy_agree_reason_id',$disagree_reasons,null , array('placeholder' => 'Please select the reason','class'=>'form-control','id'=>'compliance_policy_agree_reason_id'))}}
            <small class="help-block"></small>
        </div>
        @if($policy_details['enable_agree_textbox'])
        <div class="sub-label col-md-6 col-xs-6 col-sm-6" id="comment">
            <label for="comments" class="col-sm-2 control-label"></label>
            {{ Form::textarea('comment',null, array('placeholder'=>'Comments','class'=>'form-control','id'=>'comment'))}}
            <small class="help-block"></small>
        </div>
        @endif
        <div class="margin-top-20 col-md-12 col-xs-12 col-sm-12">
            {{ Form::submit('OK', array('class'=>'stacked-bar-graph-content-size button btn btn-primary blue','id'=>'mdl_save_change'))}}

        </div>

    </div>


</div>



@else
{{ Form::hidden('agree', 1,['class' => 'form-control'] )}}

<div class=" margin-top-20 col-md-12 col-xs-12 col-sm-12">
    {{ Form::submit('Agree', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
</div>
@endif


</div>
@endif
{{ Form::close() }}
<br />
<br />
<br />
<br />
@stop

@section('scripts')


<script src="{{ asset('js/signature_pad.umd.js') }}"></script>
<script src="{{ asset('js/signature_app.js') }}"></script>
<link href="{{ asset('css/ie9.css') }}">
<link href="{{ asset('css/signature-pad.css') }}">
<script>
    $('#acceptance').submit(function (e) {   
        e.preventDefault();
        var policy_id = {!! $policy_details['id'] !!};
        //var enable = {!! $policy_details['enable_agree_or_disagree'] !!};
        //if(enable == 1){
            if(signaturePad.isEmpty()){
          $('#sign-message').text('Signature is required');
          return false;        
             }else{
                 $('#sign-message').text('');
            }
        //}
        
        //alert('enable');
        var $form = $(this);
        var formData = new FormData($('#acceptance')[0]);
        
        var url = '{{ route("policy.compliant") }}';
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success) {
                    //alert(data);
                   // if(enable == 1){
                        saveSnap(policy_id); 
                   // }
                     
                    swal({
                        title: 'Updated',
                        text: 'Your policy compliance has been updated',
                        icon: "success",
                        button: "OK",
                    }, function () {
                        window.location =
                            '{{ route("policy.dashboard") }}';
                    });
                } else {
                    alert(data.message);
                }
            },
            fail: function (response) {
                alert('here');
            },
            error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
            },
            contentType: false,
            processData: false,
        });
    });

    $('.agree_btn').click(function(){

        refreshSideMenu();
        //console.log($(this).text());
        $('.agree_btn').removeClass('btn-success');
        $(this).addClass('btn-success');
        if($(this).text() == 'Agree')
        {
            $('#agree_reason_div').show().find('.form-control').attr('disabled',false);
            $('#common_form').show();
            $('#disagree_reason_div').hide().find('.form-control').attr('disabled',true);

        }else{
            $('#agree_reason_div').hide().find('.form-control').attr('disabled',true);

            $('#disagree_reason_div').show().find('.form-control').attr('disabled',false);
            $('#common_form').show();
        }
    });


    
        var canvas = document.getElementById("signature-content");
            canvas.width = 700;
            canvas.height = 240;
                

    function saveSnap(policy_id){
        var canvas = document.getElementById("signature-content");  

        var image = canvas.toDataURL();

        var data =  { imageBase64:image, policy_id:policy_id };

         url = "{{ route('policy.uploadimage') }}";
     $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: 'POST',
        data:data,
        success: function (response) {
            console.log(response);
          if (response == '1') {
              //alert(data);
             return true;
          } else {
            console.log(response);
            swal("Oops", "The record has not been saved", "warning");
          }
        },
        fail: function (response) {
          console.log(response);
          swal("Oops", "Something went wrong", "warning");
        }  ,
        error: function (xhr, textStatus, thrownError) {
          associate_errors(xhr.responseJSON.errors);
        },

      });

	 }




</script>
<style>
    canvas {
        padding: 0;
        margin: auto;
        display: block;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }

    #signature-content {
        min-width: 100px;
        min-height: 100px;
        overflow: hidden;
        border: 1px solid #ced4d9;
    }

    .sub-labels {
        display: inline-block;
        width: 100%;
        text-align: -webkit-auto;
        margin-bottom: 3px;
    }
</style>

@stop