@extends('adminlte::page')
@section('title', 'Satellite Tracking Settings (Color)')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
          templateSettingsRowHtml =  '{!!preg_replace( "/\r|\n/", "",(View::make('timetracker::admin.partials.templatesettingrow')->with('availableColors', $availableColors)->render()))!!}';
    </script>
</head>
<h1>Satellite Tracking Settings (Color)</h1>
@stop

@section('content')
    {{ Form::open(array('url'=>'#','id'=>'template-settings-form',
        'class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{csrf_field()}}
        
        <div class="table-responsive">
            <table class="table table-bordered table-styled dataTable" >
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Color Name</th>
                        <th>Min Value</th>
                        <th>Max Value</th>
                    </tr>
                </thead>
                <tbody class="template-setting-tbody" id="template-setting-tbody">
                    @foreach($satelliteTrackingSettings as $key => $each_rule)
                        @include('timetracker::admin.partials.templatesettingrow')
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12 text-align-right add-more-rule">
                <a  title="Add new rule" href="javascript:;" 
                    class="add_button margin-left-table-btn" 
                    id="add-new-rule-plus" 
                    onclick="rulesObj.addRule(this)">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </a>
                <a  title="Remove rule" href="javascript:;"
                    class="remove_button margin-left-table-btn" 
                    id="remove-new-rule-minus">
                    <i class="fa fa-minus" aria-hidden="true"></i>
                </a>
        </div>

        <div class="modal-footer">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            <button class="btn btn-primary blue" type="reset">Cancel</button>
        </div>
 </div>
    {{ Form::close() }}
    @stop
    @section('js')
    <script>

    $(function () {
        rows = $('#template-setting-tbody tr').length;
        if(rows < 1){
            $('#remove-new-rule-minus').hide();
        }

        rulesObj = {

            htmlRow: templateSettingsRowHtml,
            initRow: function(){
                $("#template-setting-tbody").append(rulesObj.htmlRow);
                $("#template-setting-tbody tr")
                .find(".min-item:last, .max-item:last")
                .attr('pattern','^\\d{1,4}(\\.\\d{4})?$');
            },
            getSettingsRow:function(){
                return htmlRow;
            },
            addRule: function(currObj){
                ruleElementCount =  this.getLastRowNo();
                ruleElementCount++;
                var htmlStr = rulesObj.htmlRow;
                $('#template-setting-tbody').append(htmlStr);
                var newHtmlObj = $('#template-setting-tbody tr:last');
                this.prepareNextRow(newHtmlObj);
            },
            getLastRowNo:function(){
                rule_element_count_value = $("#template-setting-tbody tr:last").find(".row-no").val();
                return rule_element_count_value;
            },
            prepareNextRow: function(htmlObj){
                var positionHtml = (ruleElementCount*1+1)
                +'<input type="hidden" class="row-no" name="position[]" value="'+ruleElementCount
                +'"/>';
                $(htmlObj).find("[class='form-group color']").attr('id','rule_color_'+(ruleElementCount));
                $(htmlObj).find("[class='form-group min']").attr('id','min_value_'+(ruleElementCount));
                $(htmlObj).find("[class='form-group min']").find('input').attr('pattern','^\\d{1,4}(\\.\\d{4})?$');
                $(htmlObj).find("[class='form-group max']").attr('id','max_value_'+(ruleElementCount));
                $(htmlObj).find("[class='form-group max']").find('input').attr('pattern','^\\d{1,4}(\\.\\d{4})?$');
                $(htmlObj).find("[class='sl-no']").html(positionHtml);
            }
        }
        {!! (count($satelliteTrackingSettings)>0) ? 'rulesObj.addRule($("#add-new-rule-plus"))' : 'rulesObj.initRow()' !!}

        /* Posting data to TemplateSettingController - Start*/
        $('#template-settings-form').submit(function (e) {
                e.preventDefault();
                var $form = $(this);
                url = "{{ route('satellite-tracking-settings.save') }}";
                var formData = new FormData($('#template-settings-form')[0]);
                $.ajax({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        if (data.success && data.success=="true") {
                            swal("Saved", "Template settings has been updated successfully", "success");
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                        } else {
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            console.log(data);
                        }
                    },
                    fail: function (response) {
                        console.log(response);
                    },
                    error: function (xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form, true);
                    },
                    contentType: false,
                    processData: false,
                });
            });


        /* Removing Question Color Rule - Start*/
        $('#remove-new-rule-minus').on('click',function(){
            first_id = $('#template-setting-tbody tr:first').find('td div').attr('id');
            last_id = $('#template-setting-tbody tr:last').find('td div').attr('id');
            rows = $('#template-setting-tbody tr').length;
            if(last_id != first_id){
                $('#template-setting-tbody tr:last').remove();
            }
            if(rows == 2){
                $('#remove-new-rule-minus').hide();
            }
        });

        /* Show Remove Icon on Adding new rules- Start*/
        $('#add-new-rule-plus').on('click',function(){
            $('#remove-new-rule-minus').show();
        });
    });
    </script>
    @stop
