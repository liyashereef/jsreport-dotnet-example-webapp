@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('css')
    <style>
        .section-header {
            background-color: #f36424;
        }

        .section-data {
            color: white;
            padding: 10px 18px;
            font-weight: bold;
            text-align: center;
        }

        .section-track {
            padding: 15px;
        }

        .add-remove-btn {
            margin-top: 10px;
        }

        td.action-data {
            width: 40px;
            padding: 18px!important;
        }

        .table thead th,
        table thead td {
            color: #ffffff;
            border-bottom: 1px solid #003A63;
        }

        .table thead th,
        table tfoot th {
            font-weight: 600;
            font-size: 15px;
        }

        .table-bordered td,
        .table-bordered td a,
        .table-bordered th {
            font-size: 14px;
            color: #003A63;
        }

        .table-bordered th {
            background: #003A63;
        }

        table.no-footer {
            border-bottom: none;
        }

        .fa {
            color: #f48452 !important;
        }

        .table-bordered td a:hover {
            color: #003A63;
        }

        .dataTables_length select {
            width: 100px;
            padding: 5px;
            color: #f48452;
            border: 1px solid #DDE9ED;
        }

        .table tbody tr {
            background-color: #dde9ed;
        }

        .dataTables_wrapper,
        .dataTables_paginate,
        .dataTables_info,
        .jobs-table_filter,
        .dataTables_length label {
            color: #003A63;
            /*font-weight: bold;*/
            font-size: 14px;
        }

        .select2-container {
            width: auto !important;
        }
        td span.select2-container {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    <div class="table_title">
        <h4>Client Onboarding Stages</h4>
    </div>

    {{ Form::open(array('url'=>'#','id'=>'rfp-status-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
    {{ Form::hidden('id', isset($onBoardingDetails->id)? $onBoardingDetails->id : null) }}
    <div id="form-container">
        {{Form::hidden('rfpDetailsId',$rfpDetails->id)}}
        <div id="dynamic-row">
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-xs-center text-sm-center text-md-center text-lg-center text-xl-center margin-top-1">
        {{ Form::submit('Save', array('class' => 'btn submit')) }}
        {{ Form::button('Cancel', array('class' => 'btn cancel','onclick'=>'window.history.back();')) }}
    </div>
    {{ Form::close() }}

    @include('contracts::rfp.partials.create-client-onboarding')
    @include('contracts::rfp.partials.create-client-onboarding-steps')
@stop
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="{{ asset('js/moreel.js') }}"></script>
    <script>
        $(function () {
            let onboardingId = "{{$onBoardingDetails->id ?? null}}";
            let moreStepArr = [];
            let sectionDataEncoded = atob('{{$sections}}');
            let sectionData = JSON.parse(sectionDataEncoded);
            let sectionIndex = 0;
            let divSectionParam = {
                containerDiv: '#dynamic-row',
                addButton: '.add_button',
                form: '#onboarding-template-form',
                afterAdd: function (el) {
                    let dataId = $(el).data('elid');
                    let divStepParam = {
                        containerDiv: '#dynamic_step_row_' + dataId,
                        moreHtmlDiv: '#more-step-content',
                        addButton: '.step_add_button',
                        removeButton: '.step_remove_button',
                        afterAdd: function(stepEl) {
                            $(stepEl).find(".datepicker").datepicker({format: "yyyy-mm-dd"});
                            $(stepEl).find(".assignee").select2();
                        }
                    };
                    let param = {
                        section_id: dataId
                    };
                    let moreStepObj = new MoreEl('steps' + dataId, divStepParam, param);
                    if(sectionData[sectionIndex] !== undefined)
                    {
                        for(let stepI = 0; stepI < sectionData[sectionIndex].step.length; stepI++) {
                            let newStepRow = moreStepObj.addRow();
                            let newRowElId = sectionIndex;
                            let currStep = sectionData[sectionIndex].step[stepI];
                            moreStepArr.push(moreStepObj);
                            if(onboardingId != "") {
                                // if onboardingId is null, this is initial adding
                                // ie. step id comes from template, which is not required
                                $(newStepRow)
                                    .find("[name='client-step-id["+newRowElId+"][]']")
                                    .val(currStep.id);
                            }
                            $(newStepRow)
                                .find("[name='client-step-sort-order["+(newRowElId)+"][]']")
                                .val(currStep.sort_order);
                            $(newStepRow)
                                .find("[name='client-step["+newRowElId+"][]']")
                                .val(currStep.step);
                            $(newStepRow)
                                .find("[name='client-step-target-date["+newRowElId+"][]']")
                                .val(currStep.target_date);
                            $(newStepRow)
                                .find("[name='client-step-assignee["+newRowElId+"][]']")
                                .val(currStep.assigned_to).trigger('change');
                        }
                    } else {
                        moreStepObj.addRow();
                        moreStepArr.push(moreStepObj);
                    }
                }
            };
            let moreSections = new MoreEl('section', divSectionParam);

            for(let i=0; i<sectionData.length; i++,sectionIndex++) {
                let newRow = moreSections.addRow();
                let newRowElId = $(newRow).data('elid');
                $(newRow).find("[name='sort[]']").val(sectionData[i].sort_order);
                $(newRow).find("[name='section[]']").val(sectionData[i].section);
                if(onboardingId != "") {
                    // if onboardingId is null, this is initial adding
                    // ie. section id comes from template, which is not required
                    $(newRow).find("[name='client-section-id[]']").val(sectionData[i].id);
                }
            }
            $('#rfp-status-form').submit(function (e) {
                e.preventDefault();
                var $form = $(this);
                $form.find('td').removeClass('has-error').find('.help-block').text('');
                var formData = new FormData($('#rfp-status-form')[0]);
                //console.log('yes');
                var url =
                    "{{ route('rfp.store-client-onboarding',[$rfpDetails->id]) }}";
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        console.log(data);
                        if (data.success) {
                            let message = 'Tracking step has been successfully created';
                            if(onboardingId != "") {
                                message = 'Tracking step has been successfully updated';
                            }
                            swal({
                                title: 'Success',
                                text: message,
                                icon: "success",
                                button: "Ok",

                            }, function () {
                                window.location = "{{ route('rfp.summary') }}";
                            });
                        } else {
                            console.error(data.message);
                        }
                    },
                    fail: function (response) {
                        console.error(response);
                    },
                    error: function (xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form);
                    },
                    contentType: false,
                    processData: false,
                });
            });


        });
    </script>
@endsection
