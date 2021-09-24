@extends('adminlte::page')
@section('title', 'Email Template')
@section('content_header')
<h1>Email Template</h1>
@stop
@section('content')
<div id="message"></div>
{{ Form::open(array('url'=>'#','id'=>'template-form', 'method'=> 'POST')) }}
{{ Form::hidden('id', null,array('id' => 'id')) }}
<div class="row">
  <div class="col-md-12" id="type_id">
    <div class="form-group row">
      <div class="col-md-2">
        Choose Type:
      </div>
      <div class="col-md-3">
        <select name="type_id"  class="form-control">
          <option value=0 selected>Select</option>
          @foreach($type as $id=>$data)
          <option value={{$id}}>{{$data}}</option>
          @endforeach
        </select>
        <span class="help-block"></span>
      </div>     
      
      <div class="col-md-7">
       <!--  <a href="#" style="float: right;" data-toggle="popover" class="edit fa fa-question-circle fa-lg" title="Popover title" data-content="And here's some amazing content. It's very engaging. Right?">Helper</a> -->
       <!-- <button type="button" class="add-new info" data-container="body" style="float: right;"> -->
        <div class="add-new info" data-title="Add New Helper">
    <span class="add-new-label">Helper</span>
     </div>
        <!--  Helper
       </button> -->
     </div>
   </div>
 </div>
 <div class="col-md-12" id="email_subject">
  <div class="form-group row">
    <div class="col-md-2">
      Subject:
    </div>
    <div class="col-md-3">
      <textarea name="email_subject" id="subject" class="form-control"></textarea>
      <span class="help-block"></span>
    </div>
    
  </div>
  
</div>
</div>


<div id="editors">
 <div class="form-group row">
  <textarea name="editors" class="ckeditor" rows="20"  id="editor"></textarea>
  <span class="help-block"></span>
</div>
</div>
<div class="col-md-6 allocation-control" style='padding-top:10px'>
  <button class="btn blue allocate-submit-btn admin-btn" style='margin-right:5px'>Save</button>
  <button class="btn blue allocate-cancel-btn admin-btn">Cancel</button>
</div>
{{ Form::close() }}

@stop
@section('js')
<script>
  CKEDITOR.replace('editor', {
    height: 500,
    
  });
  
  $(function () {
    $('.allocate-cancel-btn').on('click',function(e) {
     e.preventDefault();
     location.reload();
   });
    $('.info').click(function(e)
    {
     var type_id=$("[name='type_id'] option:selected").val();
     var base_url = "{{route('email-template.helper',':id')}}";
     var url = base_url.replace(':id', type_id);
     $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url:url,
      type: 'GET',
      success: function (data) {
       if(data.success){
        if(data.result.length ===0){
         swal({title:"No helpers for the selected type",html: true });
       }           
       else
       {
         var swal_html = '<div class="panel"> <div class="panel-body"><table align="center" class="helper">';
         $.each(data.result, function( index, value ) {
           swal_html+= '<tr><td>'+index+'</td><td>&nbsp'+value+'</td></tr>';
         });
         swal_html+= '</table></div></div>';
         swal({title:"Replace the words with their tags", text: swal_html,html: true });
       }
     }
   },
   fail: function (response) {
    swal("Oops", "Something went wrong", "warning");
  },
  error: function (xhr, textStatus, thrownError) {
    associate_errors(xhr.responseJSON.errors, $form, true);
  },
  contentType: false,
  processData: false,
});
     
   });
    $('#template-form').submit(function(e) {
      e.preventDefault();
      var id = $(this).data('id');
      var $form = $(this); 
      CKEDITOR.instances.editor.updateElement();
      var editor=( CKEDITOR.instances.editor.getData()); 
      var formData = new FormData($('#template-form')[0]);
      formData.append('email_body', editor);
      var url = "{{route('email-template.store')}}";
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:url,
        type: 'POST',
        data: formData,
        success: function (data) {
         if(data.success){
          swal({title: "Saved", text: data.message, type: "success"},
           function(){ 
             $('#id').val(data.id);
           }
           );
        }
      },
      fail: function (response) {
        swal("Oops", "Something went wrong", "warning");
      },
      error: function (xhr, textStatus, thrownError) {
        associate_errors(xhr.responseJSON.errors, $form, true);
      },
      contentType: false,
      processData: false,
    });
    });
    
  });
  $('select[name="type_id"]').on('change', function() {
    var id = $(this).val();
    $('#template-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
    $('#template-form').removeClass('has-error');
    var base_url = "{{route('email-template.single',':id')}}";
    var url = base_url.replace(':id', id);
    $.ajax({
      url: url,
      type: 'GET',
      success: function(data) {
        $('#subject').val(data.email_subject);
        $('#id').val(data.id);
        if( Object.entries(data).length==0)
        {
         CKEDITOR.instances['editor'].setData('')
       }
       else{
        CKEDITOR.instances['editor'].setData(data.email_body)
      }
      
      
    }
  });
  });

  
</script>
<style type="text/css">
  table.helper td { 
    padding-bottom: 10px;
  }
</style>
@stop
