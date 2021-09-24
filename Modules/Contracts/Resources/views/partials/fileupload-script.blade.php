<script>
    $(document).on('click', '.file_attachment_remove_btn', function () {
        var maxFileCount = window.attachment.fileCount;
        $(this).closest('.attachment_file').remove();
        if($('[name="attachment_list[]"]').length < maxFileCount) {
            $(".attachement-control").show();
        }
    });
     $("#short_descriptions").keyup(function () {
        var VAL = this.value;
        var email = new RegExp('^[(a-zA-Z0-9_()\\-)]+$');
        if (!email.test(VAL) && $("#short_descriptions").val()!=='') {
           $(".short_description_file_div").addClass("has-error");
            $(".short_description_file_div .help-block").text("Special characters not allowed");
          
        }
        else
        {
          $(".short_description_file_div").removeClass("has-error");
          $(".short_description_file_div .help-block").text("");
        }

    });
 var maxFileCount = window.attachment.fileCount;
   $(".file_attachment_upload_btn").click(function () {
        /*Validation*/
        var maxFileCount = window.attachment.fileCount;
        var allowedFileSize = window.attachment.filesize;
        var file_attachment = $('input#file_attachment')[0];
        var allowedExtensionArr = window.attachment.extensionArr;
        if($(".short_description_file_div").hasClass("has-error"))
        {
          return false;
        }
        if (showFileSize(file_attachment) > allowedFileSize) {
            $("#attachment_div .help-block").addClass("has-error").text("Maximum file size allowed is " + allowedFileSize + " MB");
            return false;
        }
        if (!allowedExtensionArr.includes(getFileExtension())) {
            $("#attachment_div .help-block")
                .addClass("has-error")
                .text("File type not allowed. Only "+ allowedExtensionArr.join() +" files allowed");
            return false;
        }

        if ($('#file_attachment').val() == "") {
            $('#attachment-validation').addClass('has-error').text('Please attach a file to upload');
            return false;
        }
        if ($('#short_descriptions').val() == "") {
            $('#short_descriptions').val(getFileName());
        }

        /* Get file size */
       function showFileSize(file_name) {
           var input, file;
           var file_size = 0;
           if (!window.FileReader) {
               console.log("The file API isn't supported on this browser yet.");
               return;
           }
           input = $(file_name);
           if(input[0].files.length > 0){
               file_size = (input[0].files[0].size)/(1000000);
           }
           return file_size;
       }

       /* Get file name */
       function getFileName() {
           var input, file;
           var file_size = 0;
           if (!window.FileReader) {
               console.log("The file API isn't supported on this browser yet.");
               return;
           }
           input = $('#file_attachment');
           var file_name = input[0].files[0].name;
           return file_name;
       }

       /* Get file extension */
        function getFileExtension() {
            let fileName = getFileName();
            let fileNameArr = fileName.split('.');
            let extension = fileNameArr[fileNameArr.length-1];
            return extension;
        }

        /*Validation*/
        var fileUploadData = new FormData();
        fileUploadData.append('file', $('#file_attachment')[0].files[0]);
        var custom_name=btoa($('#short_descriptions').val());
        let url = "{{ route('fileupload',['module' => $attachmentModule,':custom_name'])}}";
         url = url.replace(':custom_name',  custom_name);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'POST',
            data: fileUploadData,
            success: function (data) {
                if (data.success) {
                    onFileUploadSuccess(data.data);
                    $('#attachment-validation').text('');
                    $('#short_descriptions').val('');
                } else {
                    console.log(data);
                    swal("Oops", "Could not upload", "warning");
                }
            },
            fail: function (response) {
                swal("Oops", "Something went wrong", "warning");
            },
            error: function (xhr, textStatus, thrownError) {
//associate_errors(xhr.responseJSON.errors, $form);
            },
            contentType: false,
            processData: false,
        });

   });
        function onFileUploadSuccess(responseData) {
          console.log(responseData)
         var name= (responseData.original_name)?responseData.original_name:$('#short_descriptions').val()
            var file_display = '<input type="hidden" name="all_attachments[]" value="' + responseData.id + '">';
            file_display += '<div class="form-group row attachment_file" style="transform: translateY(30%);">';
            file_display += '<div class="col-sm-8 control-label scroll-clear">' +name + '</div>';
            file_display += '<input name="attachment_list[]" type="hidden" value=\'' + JSON.stringify({
                id: responseData.id,
                name: name
            }) + '\'>';
            file_display += '<div class="col-sm-4 file_remove_btn_div">';
            file_display += '<input class="button btn file_attachment_remove_btn btn-danger" ' +
                                    'type="button" ' +
                                    'value="Remove" ' +
                                    'style="float: right;">' +
                            '</div>';
            file_display += '<small class="help-block"></small>';
            file_display += '</div>';
            $(".attachment-list").append(file_display);
            $("#file_attachment").val('');

            if($('[name="attachment_list[]"]').length >= maxFileCount) {
                $(".attachement-control").hide();
            }

        }
 
</script>
