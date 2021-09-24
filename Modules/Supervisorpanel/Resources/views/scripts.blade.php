<script>

    reportObj = {
        showChildren: function(childDiv){
            childDiv.show();
            childDiv.find('input,select,textarea').attr('required','required');
            $('#sidebar').css('height', $('#content-div').height());
        },
        hideChildren: function(childDiv){
            childDiv.hide();
            childDiv.find('input,select,textarea').removeAttr('required','required');
            childDiv.find('input,textarea,select').val('');
            childDiv.find('.first').nextAll().remove();//Remove the dynamically added row
            childDiv.find('a.remove_button').hide();
            $('#sidebar').css('height', $('#content-div').height());
        }
    }


    $(document).on('click','.show-yes, .show-no', function(){
        reportObj.showChildren($(this).parents('div.parent').next('.children'));
    });

    $(document).on('click','.show-no-false, .show-yes-false',function(){
        reportObj.hideChildren($(this).parents('div.parent').next('.children'));
    });

    $(document).on("mouseover",".select2-employee-list",function(e){
        e.preventDefault();
        $(this).select2()
    })

    $(document).on('click','.add_button',function(){
        var childDiv = $(this).parents().closest('.children');
        //Remove the data-datepicker attribute from datepicker input field before adding another record
        $(childDiv).find('.child-questions.first').find('input.datepicker').removeAttr('data-datepicker');
        var childrenDiv = $(this).parents().closest('.children');
        var childQuestionDiv = $(childrenDiv).find('.child-questions.first')[0];
        let divContent=(childQuestionDiv.outerHTML).replace("select2-hidden-accessible","")
        divContent=divContent.replace("select2-container--default","select2containerdefault")
        let rId = (Math.random() + 1).toString(36).substring(7);

        divContent=divContent.replace("attr-answer",'id="'+rId+'" attr-answer')
      
        $(this).parents().closest('.children').append(divContent);
        $(".select2containerdefault").remove()
        
        // $("#"+rId).select2()
        
        setTimeout(() => {
            $(".emplist").select2()
        }, 500);
        $( ".emplist" ).each(function( index ) {
               if($(this).attr("id")){
                   if($(this).hasClass("select2-hidden-accessible")){

                   }else{
                    // $("#"+$(this).attr("id")).select2()
                   }
                  console.log($(this).attr("id"))
                //   
               }
               
            });

        $(childrenDiv).find('a.remove_button').show();
        //Add datepicker function to input field on adding dynamically
        var addDatePicker = $(this).parents('div.children').find('.child-questions:last').find('input.datepicker');
        addDatePicker.datepicker({
            format: "yyyy-mm-dd",
            showOtherMonths: true
        });
        //Datepicker date format
        // $(".emplist").select2();
        $(".datepicker").mask("9999-99-99");
        resetReportFieldValues(this);
        

    });

    $(document).on('click','.remove_button',function(){
        resetReportFieldValues(this);
        var childrenDiv = $(this).parents().closest('.children');
        $(childrenDiv).find('.child-questions:last').remove();
        if($(this).closest('div.children').find('.child-questions').length <= 1){
            $(childrenDiv).find('a.remove_button').hide();
        }
    });


    function resetReportFieldValues(currObj){
        $(currObj).parents('div.children').find('.child-questions:last').find('textarea').html('');
        $(currObj).parents('div.children').find('.child-questions:last').find('input').val('');
        $(currObj).parents('div.children').find('.child-questions:last').find('option').prop('selected', false);
    }


    $(function () {
        $(document).on('submit','#customer-report', function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('report.store') }}";
            var formData = new FormData($('#customer-report')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success === 'true') {
                            if($('.content').find('div').hasClass('employee-rate'))//Check in customer-report blade file
                            {
                                $('.form-group').removeClass('has-error').find('.help-block').text('');
                                $('.last-update').text(data.last_update);
                                openModal();
                            }else{
                                swal({
                                  title: "Saved",
                                  text: "Report has been saved successfully",
                                  type: "success",
                                  confirmButtonText: "OK",
                                },function(){
                                    $('.form-group').removeClass('has-error').find('.help-block').text('');
                                    $(".stacked-bar-graph-header-size.payperiod.active a.view-add").click();
                                    $('.last-update').text(data.last_update);
                                    //window.location.href = "{{ route('templates') }}";
                                });
                            }
                    } else {
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        console.log(data);
                    }
                },
                fail: function (response) {
                    //alert('here');
                    console.log(data);
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
