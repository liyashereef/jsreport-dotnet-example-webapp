@extends('adminlte::page')
@section('title', 'Email Template')
@section('content_header')
<style>

#editors{
    margin: 1%;
}

.help-block{
    color:#f10606;
}

</style>

<a class="add-new" data-title="Add New Terms And Condition" href="{{route('customer-terms-and-conditions') }}">
    <span class="add-new-label">Lists</span>
</a>

<h1>Add Terms and Condition</h1>
@stop
@section('content')
<div id="message"></div>
{{ Form::open(array('url'=>'#','id'=>'terms-and-condition-form', 'method'=> 'POST')) }}
<input type="hidden" id="terms-and-condition-id" value="{{$id}}">
<div class="row">
    <div class="col-md-12" id="type_id">

        <div class="form-group row">
            <div class="col-md-2">Customer</div>
            <div class="col-md-8">
                @if(empty($termsAndCondition))
                <select name="customer_id"  id="customer_id" class="form-control" >
                    <option value='' selected >Select Customer</option>
                    @foreach($customers as $id=>$customer)
                    <option value='{{$customer->id}}'>{{$customer->projectNumber}} - {{$customer->name}}</option>
                    @endforeach
                </select>
                @else
                    @if($termsAndCondition->customer_id == 0)
                        Default terms and conditions
                    @else
                        {{$termsAndCondition->customer->project_number}} - {{$termsAndCondition->customer->client_name}} 
                    @endif
                    <input type="hidden" name="customer_id" value="{{$termsAndCondition->customer_id}}">
                @endif
                <span class="help-block" id="errorCustomerId"></span>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-2">Terms and Condition</div>
            <div class="col-md-12">
                <div id="editors">
                    <textarea name="terms_and_conditions" class="ckeditor" rows="20" id="editor">@if(!empty($termsAndCondition)){{$termsAndCondition->terms_and_conditions}}@endif</textarea>
                    <span class="help-block" id="errorTermsAndCondition"></span>
                </div>
            </div>
        </div>

        
        <div class="col-md-12" style='padding-top:10px; text-align: right !important;'>
            <button class="button btn btn-primary blue" style='margin-right:5px'>Save</button>
            <button class="button btn btn-primary blue allocate-cancel-btn ">Clear</button>
        </div>
        
    </div>
</div>



{{ Form::close() }}

@stop
@section('js')
<script>
    $('#customer_id').select2();
    CKEDITOR.replace('editor', {
        height: 500,

    });

    $(function() {

        $('.allocate-cancel-btn').on('click', function(e) {
            e.preventDefault();
            location.reload();
        });
       

        $('#terms-and-condition-form').submit(function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var $form = $(this);
            CKEDITOR.instances.editor.updateElement();
            var editor = (CKEDITOR.instances.editor.getData());
            var formData = new FormData($('#terms-and-condition-form')[0]);
            // formData.append('terms_and_conditions', editor);
            formData.append('id', $('#terms-and-condition-id').val());

            var termsAndConditionId = $('#terms-and-condition-id').val(); 
    
            var url = "{{route('customer-terms-and-conditions.store')}}";
            var url_method = "POST";
           
      
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: url_method,
                data: formData,
                success: function(data) {
                    if (data.success) {
                        swal({
                                title: data.modalTitle+" !",
                                text: data.message,
                                type: "success"
                            },
                            function() {
                                // $('#id').val(data.id);
                                location.reload();
                            }
                        );
                        
                    }
                },
                fail: function(response) {
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);

                    if(xhr.responseJSON.errors.customer_id){
                        $('#errorCustomerId').html(xhr.responseJSON.errors.customer_id[0]);
                    }
                    if(xhr.responseJSON.errors.terms_and_conditions){
                        $('#errorTermsAndCondition').html(xhr.responseJSON.errors.terms_and_conditions[0]);
                    }
                   
                },
                contentType: false,
                processData: false,
            });
        });

    });

 
</script>
<style type="text/css">
    table.helper td {
        padding-bottom: 10px;
    }
</style>
@stop