@section('scripts')
<script>
     $(function () {
        $('.select2').select2();
          /* On changing projects document name will be listed - Start */

     $('#document_categories').on('change', function(e){

        var document_name_id = $(this).val();
        var typeid = $('#type_id').val();
       
            var url = '{{ route("document-name-details.single",":id") }}';
            var url = url.replace(':id', document_name_id);
       
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "json",
            data: {'id':document_name_id,'typeid':typeid},
            success: function (data) {
                if (data) {
                  $('#document_names').find('option').not(':first').remove();
                  $.each(data, function(key, value){
                      if(document_name_id == 1){
                        $('#document_names').append("<option value="+value.id+">"+value.security_clearance+"</option>");
                      }else if(document_name_id == 2){
                        $('#document_names').append("<option value="+value.id+">"+value.certificate_name+"</option>");
                      }else{
                    $('#document_names').append("<option value="+value.id+">"+value.name+"</option>");
                      }
                     
                  });
                } else {
                    console.log(data);
                }
            },
        })
       
        
    });


    /* On changing projects document name will be listed - End */

     /* Add document Store - Start*/

     $('#add-document-form').submit(function (e) {
            e.preventDefault();
            var typeid = $('#type_id').val();
            if(typeid == 1){
                id = $('#user_id').val();
            }else if(typeid == 2){
                id = $('#customer_id').val();
            }
            else if(typeid == 3){
                id = $('#other_category_name_id').val();
            }
            var $form = $(this);
             formData = new FormData($('#add-document-form')[0]);
             $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('documents.store',['module' => 'documents']) }}",
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        
                        if(data.result)
                        {
                            swal({
                                title: "Success",
                                text: "Document details has been successfully updated",
                                type: "success"
                            }, function() {
                                window.location = '{{ route("documents.view-document",["typeid" => "", "id" => ""])}}/'+ typeid +'/'+ id +'';
                            });
                        }else{
                            swal({
                                title: "Success",
                                text: "Documents details has been successfully added",
                                type: "success"
                            }, function() {
                                window.location = '{{ route("documents.view-document",["typeid" => "", "id" => ""])}}/'+ typeid +'/'+ id +'';
                            });
                            
                        }
                       
                        
                    } else {
                        //alert(data);
                        console.log(data);
                    }
                },
                fail: function (response) {
                    //alert('here');
                    console.log(response);
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });
    /* Add document Store - End*/





});

   
   
   
</script>
@endsection
