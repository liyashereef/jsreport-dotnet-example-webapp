<script>
    function collectFilterData() {
        return {
                client_id:$("#clientname-filter").val(),
            }
    }
    $(function () {
        $('.select2').select2();
        var table = $('#stc-table').DataTable({
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url":'{{ route("stc.list") }}',
                "data": function ( d ) {
                    return $.extend({}, d, collectFilterData());
                        },
                    "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    //text: ' ',
                    pageSize: 'A2',
                    //className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'excelHtml5',
                    //text: ' ',
                    //className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'print',
                    //text: ' ',
                    pageSize: 'A2',
                    //className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'project_number', name: 'project_number'},
                {data: 'client_name', name: 'client_name'},
                {data: 'requester_name', name: 'requester_name'},
                {data: 'contact_person_name', name: 'contact_person_name'},
                {data: 'contact_person_email_id', name: 'contact_person_email_id'},
                {data: 'contact_person_phone', name: 'contact_person_phone'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var edit_url = '{{ route("stc.edit", ":id") }}';
                            edit_url = edit_url.replace(':id', o.id);
                        return '<a href="'+ edit_url +'" class="edit fa fa-edit" data-id=' + o.id + '></a> <a href="#" class="delete fa fa-trash" data-id=' + o.id + '></a>';
                    },
                }
            ]
        });

        $(".client-filter").change(function(){
            table.ajax.reload();
        });

     $("#requester_id").select2();
        /* Posting data to ShortTermContractsController - Start*/
        $('#stc-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#stc-form')[0]);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            console.log(formData);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('stc.store') }}",
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal({
                            title: 'Success',
                            text: data.result,
                            icon: "success",
                            type: 'success',
                            button: "OK",
                        }, function () {
                            @can('list-stc-customers')
                                 window.location = "{{ route('stc') }}";
                            @else
                                window.location = "{{ route('stc.create') }}";
                            @endcan
                        });
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
        /* Posting data to ShortTermContractsController - End*/

        $('#stc-table').on('click', '.delete', function (e) {
            id = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, remove",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "{{route('stc.destroy')}}",
                    type: 'GET',
                    data: "id=" + id,
                    success: function (data) {
                        if (data.success) {
                            swal("Deleted", "The record has been deleted", "success");
                            table.ajax.reload();
                        } else {
                            console.log(data);
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                        // alert(xhr.status);
                        // alert(thrownError);
                        console.log(xhr.status);
                        console.log(thrownError);
                    },
                    contentType: false,
                    processData: false,
                });
            });
        });

    });

    $('#nmso_account select').on('change', function (e) {
        refreshSideMenu();
        if($( "#nmso_account select option:selected" ).text() == 'Yes'){
            $('#security_clearance_lookup_id').show();
        }else{
            $('#security_clearance_lookup_id select option:first').prop('selected', true);
            $('#stc-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#security_clearance_lookup_id').hide();
        }
    });

     /* Prepopulating employee details on choosing select2 - Start */
             $('#requester_id').on('change', function () {
                if($(this).val()=='')
                {
                $('input:text[name="requester_position"]').val('');
                $('input:text[name="requester_empno"]').val('');
                }
                var url = '{{ route("user.formattedUserDetails", ["id" => ":user_id"]) }}';
                    url = url.replace(':user_id', $(this).val());
                    $.ajax({
                        url:url,
                        method: 'GET',
                        success: function (data) {
                           $('input:text[name="requester_position"]').val(data.position).prop('readonly','true');
                           $('input:text[name="requester_empno"]').val(data.employee_no).prop('readonly','true');
                        },
                        error: function (xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        },
                    });

            });
             /* Prepopulating employee details on choosing select2 - End */

 /* Function for concatinating address and populate in billing address if checkbox is checked - Start */
        $("#check_same_address").click(function() {
        if($("input[name=address]").val().length<=0||$("input[name=city]").val().length<=0||$("input[name=province]").val().length<=0||$("input[name=postal_code]").val().length<=0)
        {
              swal("Warning", "Please enter address details", "warning");
              $(this).prop('checked', false);
        }
        if (this.checked) {
            var address='';
            var city='';
            var province='';
            var postal_code='';
            var full_addr=getFullAddress();
         $('input:text[name="billing_address"]').val(full_addr);
         $('input:text[name="billing_address"]').prop('readonly', true);

        }
        else
        {
            $('input:text[name="billing_address"]').val('');
            $('input:text[name="billing_address"]').prop('readonly', false);
        }
    });
        /* Function for concatinating address and populate in billing address if checkbox is checked - End */
if ($("#requester_id").val() === "") {
    $('#stc-form input[name="requester_position"]').val('');
    $('#stc-form input[name="requester_empno"]').val('');
}
if (typeof $('#stc-form input[name="customer_stc_details_id"]').val() !== 'undefined') {
    var full_add=getFullAddress();
     if($('input:text[name="billing_address"]').val()===full_add)
     {
       $('input:text[name="billing_address"]').prop('readonly', true);
        $('input[name="same_address_check"]').prop( "checked", true );
     }
}
function getFullAddress()
{
       if($("input[name=address]").val().length>0)
         var address=  $("input[name=address]").val()+', ';
        if($("input[name=city]").val().length>0)
         var city=  $("input[name=city]").val()+', ';
        if($("input[name=province]").val().length>0)
         var province=  $("input[name=province]").val()+', ';
         var postal_code=  $("input[name=postal_code]").val();
         var full_addr=address+city+province+postal_code;
         return full_addr;

}
</script>
