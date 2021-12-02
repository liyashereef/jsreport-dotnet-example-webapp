@extends('adminlte::page')
@section('title', 'Spare Bonus Model Settings')
@section('css')
<style>
.mb2{
    padding-bottom: 8px !important;
}
</style>
@endsection
@section('content_header')
<h1>Spare Bonus Model Settings</h1>
@stop
@section('content')
<br>
<!-- form start -->
{{ Form::open(array('url'=>'#','id'=>'spare-bonus-setting-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
{{ csrf_field() }}

<div class="row mb2">
    <div class="col-md-3"></div>
    <div class="col-md-2"><label >Score</label></div>
    <div class="col-md-2"><label >Background Color</label></div>
    <div class="col-md-2"><label >Font Color</label></div>
</div>
<div class="row mb2">
    <div class="form-group row col-lg-12" id="reliability_safe_score">
        <label for="reliability_safe_score" class="col-sm-3" style="margin-left: 1em;">Reliability Safe Score<span class="mandatory">*</span></label>
        <div class="col-sm-2" style="margin-left: -0.3em;">
        {{ Form::number('reliability_safe_score', isset($spareBonusModelSettings->reliability_safe_score)?$spareBonusModelSettings->reliability_safe_score:"",
                                array(
                                'class'=>'form-control reliability_safe_score',
                                'min'=>'1',
                                'max'=>'2000',
                                'id'=>"reliability_safe_score",
                                'placeholder'=>'Reliability Safe Score','required'=>true)) }}
        <small class="help-block"></small>
        </div>
        <div class="col-md-2">
                    <input type="color" required
                    id="reliability_safe_score_color_code" name="reliability_safe_score_color_code" class="form-control " style="float: right"
                    onchange="clickColor(0, -1, -1, 5)" value="{{isset($spareBonusModelSettings->reliability_safe_score_color_code)?$spareBonusModelSettings->reliability_safe_score_color_code:""}}" style="width:85%;">
                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="reliability_safe_score_font_color_code" name="reliability_safe_score_font_color_code" class="form-control " style="float: right"
                    onchange="clickColor(0, -1, -1, 5)" value="{{isset($spareBonusModelSettings->reliability_safe_score_font_color_code)?$spareBonusModelSettings->reliability_safe_score_font_color_code:""}}" style="width:85%;">
                </div>
    </div>
</div>
<div class="row mb2" style="margin-top: -1.5em;">
    <div class="form-group row col-lg-12" id="reliability_grace_period_in_days">
        <label for="reliability_grace_period_in_days" class="col-sm-3" style="margin-left: 1em;">Reliability Grace Score<span class="mandatory">*</span></label>
        <div class="col-sm-2" style="margin-left: -0.3em;">
        {{ Form::number('reliability_grace_period_in_days', isset($spareBonusModelSettings->reliability_grace_period_in_days)?$spareBonusModelSettings->reliability_grace_period_in_days:"",
                                array(
                                'class'=>'form-control reliability_grace_period_in_days',
                                'min'=>'1',
                                'max'=>'2000',
                                'id'=>"reliability_grace_period_in_days",
                                'placeholder'=>'Reliability Grace Score','required'=>true)) }}
        <small class="help-block"></small>
        </div>
        <div class="col-md-2">
                    <input type="color" required
                    id="reliability_grace_period_color_code" name="reliability_grace_period_color_code" class="form-control " style="float: right"
                    onchange="clickColor(0, -1, -1, 5)" value="{{isset($spareBonusModelSettings->reliability_grace_period_color_code)?$spareBonusModelSettings->reliability_grace_period_color_code:""}}" style="width:85%;">
                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="reliability_grace_period_font_color_code" name="reliability_grace_period_font_color_code" class="form-control " style="float: right"
                    onchange="clickColor(0, -1, -1, 5)" value="{{isset($spareBonusModelSettings->reliability_grace_period_font_color_code)?$spareBonusModelSettings->reliability_grace_period_font_color_code:""}}" style="width:85%;">
                </div>
    </div>
</div>

<br>

<div class="row mb2" style="margin-top: -1.5em;">
    <div class="form-group row col-lg-12" id="reliability_rank_top_level">
        <label for="reliability_rank_top_level" class="col-sm-3" style="margin-left: 1em;">Rank Top Level<span class="mandatory">*</span></label>
        <div class="col-sm-2" style="margin-left: -0.3em;">
        {{ Form::number('reliability_rank_top_level', isset($spareBonusModelSettings->reliability_rank_top_level)?$spareBonusModelSettings->reliability_rank_top_level:"",
                                array(
                                'class'=>'form-control reliability_rank_top_level',
                                'min'=>'1',
                                'max'=>'2000',
                                'id'=>"reliability_rank_top_level",
                                'placeholder'=>'Rank Top Level','required'=>true)) }}
        <small class="help-block"></small>
        </div>
        <div class="col-md-2">
                    <input type="color" required
                    id="reliability_rank_top_level_color_code" name="reliability_rank_top_level_color_code" class="form-control " style="float: right"
                    onchange="clickColor(0, -1, -1, 5)" value="{{isset($spareBonusModelSettings->reliability_rank_top_level_color_code)?$spareBonusModelSettings->reliability_rank_top_level_color_code:""}}" style="width:85%;">
                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="reliability_rank_top_level_font_color_code" name="reliability_rank_top_level_font_color_code" class="form-control " style="float: right"
                    onchange="clickColor(0, -1, -1, 5)" value="{{isset($spareBonusModelSettings->reliability_rank_top_level_font_color_code)?$spareBonusModelSettings->reliability_rank_top_level_font_color_code:""}}" style="width:85%;">
                </div>
    </div>
</div>

<div class="row mb2" style="margin-top: -1.5em;">
    <div class="form-group row col-lg-12" id="reliability_rank_average_level">
        <label for="reliability_rank_average_level" class="col-sm-3" style="margin-left: 1em;">Rank Average Level<span class="mandatory">*</span></label>
        <div class="col-sm-2" style="margin-left: -0.3em;">
        {{ Form::number('reliability_rank_average_level', isset($spareBonusModelSettings->reliability_rank_average_level)?$spareBonusModelSettings->reliability_rank_average_level:"",
                                array(
                                'class'=>'form-control reliability_rank_average_level',
                                'min'=>'1',
                                'max'=>'2000',
                                'id'=>"reliability_rank_average_level",
                                'placeholder'=>'Rank Average Level','required'=>true)) }}
        <small class="help-block"></small>
        </div>
        <div class="col-md-2">
            <input type="color" required
             id="reliability_rank_average_level_color_code" name="reliability_rank_average_level_color_code" class="form-control " style="float: right"
            onchange="clickColor(0, -1, -1, 5)" value="{{isset($spareBonusModelSettings->reliability_rank_average_level_color_code)?$spareBonusModelSettings->reliability_rank_average_level_color_code:""}}" style="width:85%;">
        </div>
        <div class="col-md-2">
            <input type="color" required
            id="reliability_rank_average_level_font_color_code" name="reliability_rank_average_level_font_color_code" class="form-control " style="float: right"
            onchange="clickColor(0, -1, -1, 5)" value="{{isset($spareBonusModelSettings->reliability_rank_average_level_font_color_code)?$spareBonusModelSettings->reliability_rank_average_level_font_color_code:""}}" style="width:85%;">
        </div>
    </div>
</div>

<br><br>
<div class="row mb2">
    <div class="form-group col-lg-12" id="schedule_top_rank_message">
        <label for="schedule_top_rank_message" class="col-sm-3" style="margin-left: 1em;">Top Rank Message <span class="mandatory">*</span></label>
        <div class="col-sm-6" style="margin-left: -0.7em;">
            {{ Form::textarea('schedule_top_rank_message',isset($spareBonusModelSettings->schedule_top_rank_message)?$spareBonusModelSettings->schedule_top_rank_message:"",array('class' => 'form-control', 'Placeholder'=>'Top Rank Message', 'rows' => 3, 'cols' => 40)) }}
        <small class="help-block"></small>
        </div>
    </div>
</div>
<div class="row mb2" style="margin-top: -1.7em;">
    <div class="form-group col-lg-12" id="schedule_average_rank_message">
        <label for="schedule_average_rank_message" class="col-sm-3" style="margin-left: 1em;">Average Rank Message <span class="mandatory">*</span></label>
        <div class="col-sm-6" style="margin-left: -0.7em;">
            {{ Form::textarea('schedule_average_rank_message',isset($spareBonusModelSettings->schedule_average_rank_message)?$spareBonusModelSettings->schedule_average_rank_message:"",array('class' => 'form-control', 'Placeholder'=>'Average Rank Message', 'rows' => 3, 'cols' => 40)) }}
        <small class="help-block"></small>
        </div>
    </div>
</div>
<div class="row mb2" style="margin-top: -1.7em;">
    <div class="form-group row col-lg-12" id="schedule_below_average_rank_message">
        <label for="schedule_below_average_rank_message" class="col-sm-3" style="margin-left: 1em;">Below Average Rank Message <span class="mandatory">*</span></label>
        <div class="col-sm-6" style="margin-left: -0.7em;">
            {{ Form::textarea('schedule_below_average_rank_message',isset($spareBonusModelSettings->schedule_below_average_rank_message)?$spareBonusModelSettings->schedule_below_average_rank_message:"",array('class' => 'form-control', 'Placeholder'=>'Below Average Rank Message', 'rows' => 3, 'cols' => 40)) }}
        <small class="help-block"></small>
        </div>
    </div>
</div>

 <!-- /.box-body -->
<div class="box-footer">
{{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>''))}}
</div>
</form>

@endsection
@section('js')
<script>
 $('#spare-bonus-setting-form').submit(function (e) {
               e.preventDefault();
                 var $form = $(this);
                var formData = new FormData($('#spare-bonus-setting-form')[0]);
                $.ajax({
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                        url: "{{route('spare-bonus-model-settings.store')}}",
                        type: 'POST',
                        data:  formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            if (data.success) {
                                swal("Success", "Spare bonus model settings has been successfully updated", "success");
                                 $('.form-group').removeClass('has-error').find('.help-block').text('');
                            } else {
                                swal("Alert", "Something went wrong", "warning");
                            }
                        },
                        error: function (xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                            associate_errors(xhr.responseJSON.errors, $form);
                            swal("Oops", "Something went wrong", "warning");
                        },
                    });
            });

</script>
@endsection
