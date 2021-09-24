<script>

    $(function () {
        $('.select2').select2();
        var table = $('#stc-table').DataTable({
        "order": [[ 0, "asc" ]]
    });
    })

    $(document).on("click",".finalize",function(e){
        e.preventDefault();
        $("#finalize").val(1);
        $("#bonusetting").submit();
    })
    $(document).on("click",".recalculate",function(e){
        e.preventDefault();
        $("#recalculate").val(1);
        $("#bonusetting").submit();
    })
    $('#bonusetting').submit(function(e) {
        e.preventDefault();
        var $form = $(this);
        var formData = $('#bonusetting').serialize();
        // formData.append('terms_and_conditions', editor);
        var url = "{{route('stc.savebonussettings')}}";
        var url_method = "POST";
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: url_method,
            data: formData,
            success: function (response) {
                let data=jQuery.parseJSON(response)
                if (data.success) {
                        swal({
                                title: "Success",
                                text: "Saved Successfully",
                                type: "success"
                            },
                            function() {
                                // $('#id').val(data.id);
                                location.reload();
                            }
                        );
                        
                    }else{
                        swal({
                                title: "Warning",
                                text: data.message+" !",
                                type: "warning"
                            },
                            function() {
                                // $('#id').val(data.id);
                                //location.reload();
                            }
                        );
                    }
            },error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                    swal({
                                title: "Warning",
                                text: data.message+" !",
                                type: "warning"
                            },
                            function() {
                                // $('#id').val(data.id);
                                //location.reload();
                            }
                        );
                    if(xhr.responseJSON.errors.customer_id){
                        $('#errorCustomerId').html(xhr.responseJSON.errors.customer_id[0]);
                    }
                    if(xhr.responseJSON.errors.terms_and_conditions){
                        $('#errorTermsAndCondition').html(xhr.responseJSON.errors.terms_and_conditions[0]);
                    }
                   
                }
        });
    })



    
</script>
