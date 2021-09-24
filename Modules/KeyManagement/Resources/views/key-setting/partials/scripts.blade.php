@section('scripts')
<script>
    function collectFilterData() {
            return {
                client_id:$("#clientname-filter").val(),

            }
        }
    $(function () {
        $('.select2').select2();
        var table = $('#candidates-table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url":"{{ route('keysetting.customer.list') }}",
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
                    pageSize: 'A2',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                    }
                },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                        stripHtml: false,
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [1, "asc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'id', name: 'id',"visible": false,"searchable": false},
                {
                    data: 'project_number',
                    name: 'project_number',
                    defaultContent: "--",
                },
                {
                    data: 'client_name',
                    name: 'client_name',
                    defaultContent: "--"
                },
                {
                    data: 'contact_person_name',
                    name: 'contact_person_name',
                    defaultContent: "--"

                },
                {
                    data: 'contact_person_email_id',
                    name: 'contact_person_email_id',
                    defaultContent: "--"

                },
                {
                    data: 'contact_person_phone',
                    name: 'contact_person_phone',
                    defaultContent: "--"

                },
                  @canany(['view_allocated_customers_keys','view_all_customers_keys'])
                {
                    data: null,
                    name: 'action',
                    sortable: false,
                    render: function (o) {
                        actions = '';
                            var url ='{{ route("keysetting.keylist",array(":customer_id")) }}';
                            url = url.replace(':customer_id',o.id);
                            actions += '<a title="Key details" href="' + url + '" class="fa fa-key"></a>';
                            return actions;
                    },

                }
                  @endcan


            ]

        });
        $(".client-filter").change(function(){
            table.ajax.reload();
        });

/*Add new - modal popup -end */

 $('.add-new').on('click', function () {
    var title = $(this).data('title');
    $("#myModal").modal();
    $('#myModal form').trigger('reset');
    $('#myModal').find('input[type=hidden]').val('');
    $('#myModal').find('select[name="employee_id"]').select2().val('');
    $('#myModal .modal-title').text(title);
    $('#myModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
});

$('#customerkey-form').submit(function (e) {
      e.preventDefault();
      var $form = $(this);
      url = "{{ route('keysetting.store') }}";
      var formData = new FormData($('#customerkey-form')[0]);
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: 'POST',
        data: formData,
        success: function (data) {
          $("#myModal").modal('hide');
          console.log(data);
          if (data.success) {
            swal({
              title: "Saved",
              text: "Employee Whistleblower has been saved",
              type: "success"
            },function(){
                $('.form-group').removeClass('has-error').find('.help-block').text('');
                $('#customerkey-form')[0].reset();
                table.ajax.reload();
              });
          } else {
            console.log(data);
            swal("Oops", "The record has not been saved", "warning");
          }
        },
        fail: function (response) {
          console.log(response);
          swal("Oops", "Something went wrong", "warning");
        },
        error: function (xhr, textStatus, thrownError) {
          associate_errors(xhr.responseJSON.errors, $form);
        },
        contentType: false,
        processData: false,
      });
    });


     $('#candidates-table').on('click', '.edit', function(e){

        var id = $(this).data('id');
        var base_url = "{{route('keysetting.single', ':id')}}";
        var url = base_url.replace(':id', id);
        console.log(id,url);
        $('#type-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:url,
            type: 'GET',
            success: function (data) {
                console.log(data);
               if(data){
                $('#myModal input[name="id"]').val(data.id)
                $('#myModal select[name="customer_id"] option[value="'+data.customer_id+'"]').prop('selected',true);
                $('#myModal input[name="key_id"]').val(data.key_id)
                $('#myModal input[name="room_name"]').val(data.room_name);
                if (data.attachment != null) {
                            //$('#myModal file[name="policy_file"]').val(data.policy_file)
                    $('#myModal #key_image_name').text(data.attachment.original_name)
                    $('#myModal #key_image_name').css('font-weight',500)
                }
                $(".select2").select2()
                $("#myModal").modal();
                $('#myModal .modal-title').text("Edit Customer Shift: " + data.customer. client_name + ' ( ' +data.shiftname + ' ) ');
               }
            },
            fail: function (response) {
                swal("Oops", "Something went wrong", "warning");
            },
            contentType: false,
            processData: false,
        });
    });



    });

    $(document).keyup(function(e) {jQuery
         if (e.key === "Escape") {
          $("#myModal").modal('hide');
       }
     });
</script>
@stop
