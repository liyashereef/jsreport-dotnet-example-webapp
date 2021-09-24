@extends('adminlte::page')
@section('title', 'Task Update Interval')
@section('content_header')
    <h1>Client Onboarding Settings</h1>
@stop

@section('content')

        {{ Form::open(array('url'=>'#','id'=>'onboarding-interval-form','class'=>'form-horizontal', 'method'=> 'POST')) }}


        <div id="dynamic-rows">
        </div>

        <div class="modal-footer row col-sm-12">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            <a href="" class="btn btn-primary blue">Cancel</a>
            {{ Form::close() }}
        </div>

        <template id="more-content">
            <div class="el_fields row" id="--name--_row_--position_num--" data-elid="--position_num--">
                <label for="interval" class="col-md-5 interval_label" id="label_--position_num--">
                    <span>
                    Number of days prior
                    to
                    due date for sending remainder email
                    </span>
                    <span class="mandatory">*</span>
                </label>
                <div class="col-md-4">
                    {{ Form::number('reminder[]', null,
                    array(
                    'class'=>'form-control reminder-days',
                    'min'=>'1',
                    'id'=>"intervals--position_num--",
                    'placeholder'=>'Number of Days','required'=>true)) }}
                    <small class="help-block"></small>
                </div>

                <div class="col-sm-3">
                    <a title="Add More" href="javascript:;" class="add_button" data-elid="--position_num--">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                    <a href="javascript:void(0);" class="remove_button" title="Remove" data-elid="--position_num--">
                        <i class="fa fa-minus" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </template>



        @stop
        @section('js')


            <script src="{{ asset('js/moreel.js') }}"></script>
            <script>
                $(function () {
                    let divParam = {
                        containerDiv: '#dynamic-rows',
                        addButton: '.add_button',
                        addMaxCount: 3,
                        removeButton: '.remove_button',
                        removeOne: true,
                        form: '#onboarding-interval-form',
                        afterAdd: function (el) {
                            let totalLength = $('#dynamic-rows>div').length;
                            let label = $('#dynamic-rows>div:last label>span:first').text();
                            let newLabel = label+' '+totalLength;
                            $('#dynamic-rows>div:last label>span:first').text(newLabel);
                        },
                    };
                    let moreRemainder = new MoreEl('remainder', divParam);
                    moreRemainder.initElDiv(true);
                    var dataVar = JSON.parse('{!! $onboardingList  !!}');
                    if(dataVar.length == 0){
                        moreRemainder.initElDiv();
                    } else {
                        moreRemainder.initElDiv(true);
                    }
                    for(let i=0; i<dataVar.length; i++) {
                        let rowEl = moreRemainder.addRow();
                        $(rowEl).find(".reminder-days").val(dataVar[i].value);
                    }
                });
            </script>

            <script>
                $(function () {

                    $('#onboarding-interval-form').submit(function (e) {
                        e.preventDefault();
                        var $form = $(this);
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        url = "{{ route('client-onboarding-settings.store') }}";
                        var formData = new FormData($('#onboarding-interval-form')[0]);
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'accept': 'application/json',
                            },
                            url: url,
                            type: 'POST',
                            data: formData,
                            success: function (data) {
                                if (data.success) {
                                    swal({title: "Saved", text: "The interval has been saved", type: "success"},
                                        function () {
                                            location.reload();
                                        }
                                    );
                                } else {
                                    alert(data);
                                }
                            },
                            fail: function (response) {
                                alert('here');
                            },
                            error: function (xhr, textStatus, thrownError) {
                                associate_errors(xhr.responseJSON.errors, $form, true);
                            },
                            contentType: false,
                            processData: false,
                        });
                    });
                });


            </script>
@stop
