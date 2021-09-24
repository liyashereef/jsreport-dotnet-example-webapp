
<div class="table_title">
    <h3>{{$payperiod_name}}</h3>
</div>
</section>
<section  class="content">
    <hr>
    {{-- Get Current date for first time / Created at date for edit --}}
    <div class="text-align-right report-date"><span>Report Date:</span>{{$reportDate}}</div>
    {{-- get the question category --}}
    <form id="customer-report" name="customer-report" action="{{ route('customer.report',[$formated_template['customer_id'],$formated_template['payperiod_id']]) }}">
        <input type="hidden" name="template_id" value="{{$formated_template['template_id']}}"/>
        <input type="hidden" name="customer_id" value="{{$formated_template['customer_id']}}"/>
        <input type="hidden" name="payperiod_id" value="{{$formated_template['payperiod_id']}}"/>
        <input type="hidden" name="template_customer_payperiod_id" value="{{$formated_template['template_customer_payperiod_id'] or ''}}"/>
        @foreach($formated_template['questions'] as $question_category => $question_arr)
        <div class="formpanel-header-sub">
            <h8 class="color-white">{{$question_category}}</h8>
        </div>
        {{-- foreach question --}}
        @foreach($question_arr as $key => $each_question)
        <div class="form-group row parent">
            <label class="col-sm-5 col-md-6 col-form-label parent-label qlabel">{{$each_question['question']}}</label>
            {{-- from the question type fetch associated question format - Radio/text/employee lookup --}}
            {!!$each_question['answer_html']!!}
        </div>
        {{-- children --}}
        @if(isset($each_question['children']))
        <div class="children" style="display:none">
            {{-- Condition for area manager --}}
            @if($can_submit || (@auth()->user()->can('edit-survey')))
            @if($each_question['multi_answer'])
            <div class="form-group row">
                <label class="col-sm-5 col-md-6 col-form-label"></label>
                <div class="col-sm-4 form-group row">
                    <div class="radio-inline col-sm-6 col-md-4"></div>
                    <div class="radio-inline col-sm-6 col-md-4"></div>
                </div>
                @if((@auth()->user()->can('edit-survey')) || ((!$report_submitted) && @auth()->user()->can('submit-survey')))
                <div class="col-sm-2">
                <div class="col-sm-6 col-xs-12 text-align-right text-left-mob">
                    <a title="Add another" href="javascript:;" class="btn cancel ico-btn add_button"><i class="fa fa-plus" aria-hidden="true"></i>Add another record</a>
                </div>
                <div class="col-sm-6 col-xs-12 text-align-right text-left-mob">
                    <a title="Remove" href="javascript:;" class="btn cancel ico-btn remove_button" style="display: none"><i class="fa fa-minus size-adjust-icon" aria-hidden="true"></i> Remove last record</a>
                </div>
                </div>
                @endif
            </div>
            @endif
            @endif
            {{-- Condition for area manager End--}}
            @foreach($each_question['children'] as $key => $each_children)
            @if($key == 0 || $each_children['answer_index'] != $each_question['children'][($key-1)]['answer_index'])
            <div class="child-questions @if($each_children['answer_index'] == 0) first @endif">
                @endif
                <div class="form-group row">
                    <label class="col-sm-5 col-md-6 col-form-label child-align">{{$each_children['question']}}</label>
                    {!!$each_children['answer_html']!!}
                </div>
                @if(!isset($each_question['children'][($key+1)]) || $each_children['answer_index'] != $each_question['children'][($key+1)]['answer_index'])
            </div>
            @endif
            @endforeach
        </div>
        @endif
        {{-- end - children --}}
        @if(($report_submitted) && ($can_view_areamanager_notes || $can_edit_areamanager_notes))
        <div class="form-group row parent">
            <label class="col-sm-5 col-md-6 col-form-label parent-label qlabel areamanager-notes-label">Area Manager Notes</label>
            {{-- fetched areamanager notes --}}
            {!!$each_question['area_manager_notes']!!}
        </div>
        @endif
        @endforeach {{-- end - foreach question --}}
        @endforeach
        {{-- end of questions --}}
        @if(($can_edit_areamanager_notes) || (@auth()->user()->can('edit-survey')) || ((!$report_submitted) && @auth()->user()->can('submit-survey')))
        <div class="text-center margin-bottom-5 report-button-left">
            <input type="submit" class="btn cancel" value="Next"/>
            <a href="{{ route('customers.mapping') }}" class="btn cancel">Cancel</a>
        </div>
        @endif
    </form>

    @can('employee-mapping-rating')
        @include('supervisorpanel::partials.employeerate')
    @endcan
</section>

<script>
    reportObj.showChildren($('.show-no:checked,.show-yes:checked').parents('div.parent').next('.children'));
    reportObj.showChildren($('.show-yes-text,.show-no-text').parents('div.parent').next('.children'));
    //Datepicker date format
    $(".datepicker").mask("9999-99-99");
</script>
