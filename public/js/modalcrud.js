/*Add new - modal popup -start */
$('.add-new').on('click', function () {
    var title = $(this).data('title');
    $("#myModal").modal();
    $('#other_category_name_id').hide();
    $('#is_valid').hide();
    $('#myModal form').trigger('reset');
    $('#myModal').find('input[type=hidden]').val('');
    $("#document_category_details option:not(:first)").remove().trigger('change');
    $('#myModal .modal-title').text(title);
    $('#myModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
});
/*Add new - modal popup -end */

/* Form submit - Start */
function formSubmit($form, url, table, e, message) {
    var $form = $form;
    var url = url;
    var e = e;
    var table = table;
    var formData = new FormData($form[0]);
    return new Promise(function (resolve, reject) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success) {
                    swal("Saved", message, "success");
                    $("#myModal").modal('hide');
                    if (table != null) {
                        table.ajax.reload();
                    }
                } else if (data.success == false) {
                    if (Object.prototype.hasOwnProperty.call(data, 'message') && data.message) {
                        swal("Warning", data.message, "warning");
                    } else if (Object.prototype.hasOwnProperty.call(data, 'error') && data.error)  {
                        swal("Warning", "Something went wrong", "warning");
                    }else {
                        console.log(data);
                    }
                } else {
                    console.log(data);
                }
                resolve(data);
            },
            fail: function (response) {
                resolve();
            },
            error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
                resolve();
            }, always: function () {
                resolve();
            },
            contentType: false,
            processData: false,
        });
    });
}
/* Form submit - End */

/* Delete Record - Start */
function deleteRecord(url, table, message) {
    var url = url;
    var table = table;
    swal({
        title: "Are you sure?",
        text: "You will not be able to undo this action! Proceed?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes, remove",
        showLoaderOnConfirm: true,
        closeOnConfirm: false
    },
    function () {
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                if (data.success) {
                    swal("Deleted", message, "success");
                    if (table != null) {
                        table.ajax.reload();
                    }
                } else if (data.success == false) {
                    if (Object.prototype.hasOwnProperty.call(data, 'message') && data.message) {
                        swal("Warning", data.message, "warning");
                    } else {
                        swal("Warning", 'Data exists', "warning");
                    }
                } else if (data.warning) {
                    swal("Warning", 'Competency exists for the category', "warning");
                } else {
                    console.log(data);
                }
            },
            error: function (xhr, textStatus, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
            },
            contentType: false,
            processData: false,
        });
    });
}
/* Delete Record - End */
