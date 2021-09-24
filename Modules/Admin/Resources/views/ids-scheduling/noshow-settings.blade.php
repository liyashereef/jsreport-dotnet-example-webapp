@extends('adminlte::page')
@section('title', 'IDS No Show Settings')
@section('content_header')
{{-- <h1>IDS No Show Settings</h1> --}}
@stop

@section('css')
<style>
    .fa {
        margin-left: 11px;
    }
    .select2 .select2-container{
        width : 12% !important;
    }
    .modal-dialog{
        width: 100% !important;
    }
    .modal-header {
        border: 1px solid #e5e4e4 !important;
    }
    .form-horizontal .control-label {
        text-align: left;
    }

</style>
@stop

@section('content')
<div id="message"></div>


<div id="myModal" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">IDS No Show Settings</h4>
            </div>
            {{-- {{ Form::open(array('url'=>'#','id'=>'noshow-setting-form','class'=>'form-horizontal', 'method'=> 'POST')) }}  --}}
            @if(!empty($noshow))
            {{ Form::model($noshow, ['url'=>'#','id'=>'noshow-setting-form','class'=>'form-horizontal', 'method'=> 'POST']) }}
            @else
            {{ Form::open(['url'=>'#','id'=>'noshow-setting-form','class'=>'form-horizontal', 'method'=> 'POST']) }}
            @endif
            <div class="modal-body">
                {{ Form::hidden('id',old('id'),array('class'=>'form-control')) }}
                <div class="form-group" id="notice_hours">
                    <label for="notice_hours" class="col-sm-2 control-label">Notice Hours</label>
                    <div class="col-sm-3">
                        {{ Form::text('notice_hours',old('notice_hours'),array('class'=>'form-control','placeholder' => 'Notice Hours')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div id="cancellation_penalty" class="form-group">
                    <label for="cancellation_penalty" class="col-sm-2 control-label">Cancellation Penalty</label>
                    <div class="col-sm-3">
                        {{ Form::text('cancellation_penalty',old('cancellation_penalty'),array('class'=>'form-control','placeholder' => 'Cancellation Penalty')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="text-align: right !important;">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop
@section('js')
<script>
    $(function () {

        /* Service Store - Start*/
        $('#noshow-setting-form').submit(function (e) {
            e.preventDefault();
            if($('#noshow-setting-form input[name="id"]').val()){
                var message = 'Setting has been updated successfully';
            }else{
                var message = 'Setting has been created successfully';
            }
            formSubmit($('#noshow-setting-form'), "{{ route('ids-noshow-settings.store') }}", null, e, message);
        });
        /* Service Store - End*/

    });


</script>
@stop
