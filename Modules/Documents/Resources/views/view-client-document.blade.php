@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

@section('content')
<div class="d-flex justify-content-between my-10 align-items-center" style="margin-top: 12px !important;">
@if($typeid == CLIENT)
<div class="table_title">

    <h4 class="m-0">Client Documents Summary</h4>
    <div class="sub_table_title" style="margin-top: -8px !important;">{{isset($employee_list) ? ($employee_list['client_details']) : null}}</div>
</div>

@elseif($typeid == EMPLOYEE)
<div class="table_title">
    <h4 class="m-0">Employee Documents Summary </h4>
    <div class="sub_table_title" style="margin-top: 8px !important;"> {{isset($employee_list) ? ($employee_list['employee_details']) : null}}</div>
</div>

@else
<div class="table_title">
    <h4 class="m-0">Documents Summary </h4>
    <div class="sub_table_title" id="doc_sub_table"></div>

</div>
@endif
<div class="d-flex justify-content-center align-items-center">
    @canany(['add_employee_document','add_allocated_employee_document'])
    @if($typeid == EMPLOYEE)
    <div class="add-new client-add" id="add-new-button" data-title="Add New Customer">Add<span class="add-new-label"> New</span>
    </div>
    @endif
    @endcan
    @canany(['add_client_document','add_allocated_client_document'])
    @if($typeid == CLIENT)
    <div class="add-new client-add" id="add-new-button" data-title="Add New Customer">Add<span class="add-new-label"> New</span>
    </div>
    @endif
    @endcan
    @can('add_other_document')

    @if($typeid == OTHER)
    <div class="add-new client-add" id="add-new-button" data-title="Add New Customer">Add<span class="add-new-label"> New</span>
    </div>
    @endif
    @endcan
    @if($typeid == CLIENT)
    <div class="add-new client-add" id="back-button" data-title="Back">Back to<span class="add-new-label"> Clients</span></div>
    @elseif($typeid == EMPLOYEE)
    <div class="add-new client-add" id="back-button" data-title="Back">Back to<span class="add-new-label"> Employees</span></div>
    @else
    <div class="add-new client-add" id="back-button" data-title="Back">Back</div>
    @endif

</div>

</div><br>
@if($typeid == OTHER)
    <?php $other_cat_id = ($other_list['otherlist']->other_category_lookup_id) ?>
@endif
{{ Form::hidden('document_type_id', isset($typeid) ? old('type_id',$typeid) : null,array('id'=>'type_id')) }}
{{ Form::hidden('id', isset($id) ? old('id',$id) : null,array('id'=>'id')) }}
{{ Form::hidden('other_cat_id', isset($other_cat_id) ? old('other_cat_id',$other_cat_id) : null,array('id'=>'other_cat_id')) }}
<br>
<div class="pull-right" id="">
<div class="check-controlline checkboxes" >
    <input type="hidden" name="requirements" value="" id="requirement_id_hidden">
    <div class="checkboxs">
        <input type="checkbox" id="currentCheck" class="chk" name="currentrarchivedcheck" value="0"  checked="checked">
        <label for="currentCheck">
            Current
        </label>
    </div>
    <div class="checkboxs">
        <input type="checkbox" id="archivedtimeCheck" class="chk" name="currentrarchivedcheck" value="1"  checked="checked">
        <label for="archivedCheck">
            Archived
        </label>
    </div>
</div>
</div>

<table class="table table-bordered" id="table-id">
     <thead>
         <tr>
            @can('archive_documents')
            <th class="dt-body-center text-center">
                <input id="select_all" value="1" type="checkbox" />
            </th>
            @endcan
             <th>Document Type</th>
             <th>Document Name</th>
             <th>Document Description</th>
             <th>Uploaded Date </th>
             <th>Uploaded By </th>
             <th>Status </th>
             <th>Document </th>
             @can('delete_document')
             <th>Action</th>
             @endcan

         </tr>
     </thead>
 </table>
 @can('archive_documents')
   {{ Form::button('Archive Documents', array('class'=>'button btn archive submit','value'=>'archive','style'=>'display:none;'))}}
 @endcan

 @stop

 @section('scripts')
   <script>
    $(function () {

    var typeid = $('#type_id').val();
    var id = $('#id').val();
    var url = '{{ route('view-list.document',[":typeid",":id",":checked"]) }}';
    url = url.replace(':typeid', typeid);
    url = url.replace(':id', id);

        $.fn.dataTable.ext.errMode = 'throw';
        try{
            var table = $('#table-id').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: url,
            dom: 'Blfrtip',

                buttons: [{
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: [1, 2,3,4,5],
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [1, 2,3,4,5],
                    }
                },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: [1, 2,3,4,5],
                        stripHtml: false,
                    }
                }
            ],
            @can('archive_documents')
            columnDefs: [
                {
                    "targets": [ 0 ],
                   // "visible": false,
                    "searchable": false,
                    "orderable":false,
                    className: 'dt-body-center',
                    render: function (data, type, full, meta) {

                        return '<input type="checkbox" id="document_id" name="document_id" class="archive-button-trigger dt-body-center" value="' +
                            $('<div/>').text(data).html() + '">';
                    }
                },
            ],
            @endcan
            select: {
            style:    'os',
                    selector: 'td:first-child'
            },

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            createdRow: function (row, data, dataIndex) {


                    if(data.is_archived == "Archived")
                    {
                        $(row).addClass('archived');
                    }
                },
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            //lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columns: [
                @can('archive_documents')
                {data: 'id', name: 'id',},
                @endcan
                {data: 'document_category', name: 'document_category'},
                {data: 'document_name', name: 'document_name'},
                {data: 'document_description', name: 'document_description'},
                {data: 'uploaded_date', name: 'uploaded_date'},
                {data: 'uploaded_by', name: 'uploaded_by'},
                {data: 'is_archived', name: 'is_archived'},
                {
                        data: null, name: 'attachments',
                        sortable: false,
                        render: function (o) {
                         if((o.answer_type == 'RecDocument') || (o.answer_type == 'RecAttachment' )){
                          var link = '';
                          link += '<a title="Download"  target="_blank"  href="' + o.recruitment_document + '"><i class="fa fa-archive"></i></a><br>';
                          return link;
                         }
                        else if(o.attachments != null){
                         var link = '';
                         var view_url = '{{ route("filedownload", [":id",":module"]) }}';
                         view_url = view_url.replace(':id', o.attachments.id);
                         view_url = view_url.replace(':module', 'documents');
                         link += '<a title="Download"  target="_blank"  href="' + view_url + '"><i class="fa fa-archive"></i></a><br>';

                         return link;

                         } else{
                            return '';
                         }
                           return '';
                         },
                     },
                @can('delete_document')
                     {
                    data: null,
                    name: 'action',
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        return actions;
                    },
                }
                @endcan

            ]

        });

        /* Page redirection to add document page - Start */
            $('#add-new-button').on('click',  function(e){
            window.location='{{route("add-client.document", ["typeid" => "" , "id" => ""])}}/'+ typeid +'/'+ id  +'';
            });
        /* Page redirection to add document page - End */

        /* Page redirection to document detail page - Start */

        $('#back-button').on('click',  function(e){

           var type = $('#type_id').val();
           var otherCatID =  $('#other_cat_id').val();
           if(type == '{{ EMPLOYEE }}'){
            window.location='{{route("documents.employee-document")}}';
           }else if(type == '{{ CLIENT }}'){
            window.location='{{route("documents.client-document")}}';
           }else{
            window.location='{{route("documents.other-vendor", ["type_id" => ""])}}/'+ otherCatID +'';
           }


            });
        /* Page redirection to add document page - End */
        } catch(e){
            console.log(e.stack);
        }


    /* populating the current and archived with respect to the availabilty - Archived or current start */
@can('archive_documents')
      /* Archive documents manually */
      $('.dataTable').on('click', '#select_all', function () {
            var rows = table.rows({'search': 'applied'}).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked).trigger('change');
        });
        $('.dataTable').on('change', '.archive-button-trigger', function () {
            if($('input.archive-button-trigger:checkbox:checked').length > 0)
            {
                $('.archive').show();
            }else{

              $('.archive').hide();
            }
        });
        $('.archive').on('click', function () {
            swal({
                    title: "Are you sure?",
                    text: "Do you want to archive this document?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, archive",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: true
                },
                function () {
                    document_ids = [];
                    $(".dataTable input[name=document_id]:checked").each(function () {
                        document_ids.push($(this).val());
                    });
                    document_ids = (JSON.stringify(document_ids));
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('documents.archive') }}",
                        type: 'POST',
                        data: {
                            'document_ids': document_ids
                        },
                        success: function (data) {
                            if (data.success) {
                                table.ajax.reload();
                                $('.archive').hide();
                            }
                        }
                    });
                });
            });
            /* Archive documents manually */

@endcan
    });
    $("input[name='currentrarchivedcheck']").change(function () {

       var length=  $('.chk:checked').length;
        if(length == 2) {
            var checked = ":checked";
        }else{
            if($('#currentCheck').is(":checked")){
                $('#currentCheck').prop('checked', true);
                var checked = $('#currentCheck').val();

            }else{
                $('#archivedtimeCheck').prop('checked', true);
                var checked = $('#archivedtimeCheck').val();
            }
            if($('#archivedtimeCheck').is(":checked")){
                $('#archivedtimeCheck').prop('checked', true);
                var checked = $('#archivedtimeCheck').val();
            }else{

                $('#currentCheck').prop('checked', true);
                var checked = $('#currentCheck').val();
            }
        }
            var table = $('#table-id').DataTable();
            var typeid = $('#type_id').val();
            var id = $('#id').val();

            var url = '{{ route('view-list.document',[":typeid",":id",":checked"]) }}';
            url = url.replace(':typeid', typeid);
            url = url.replace(':id', id);
            url = url.replace(':checked', checked);
            table.ajax.url( url ).load();
        });

          /* populating the current and archived with respect to the availabilty - Archived or current end */

              /***** Visitor  Delete - Start */
    $('#table-id').on('click', '.delete', function (e) {
           var table = $('#table-id').DataTable();
            var id = $(this).data('id');
            var base_url = "{{ route('documents.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action Proceed?",
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
                                swal("Deleted", "Record has been deleted successfully", "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else {
                                swal("Alert", "Cannot able to delete ", "warning");
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
        });


    </script>
    <style type="text/css">
         /* #add-new-button{ margin-top: 0px;margin-right:5px;} */
        .checkboxs {
            padding-left: 20px;
        }
        .check-controlline .checkboxs {
            float: left;
            line-height: 16px;
        }
        .pull-right {

    margin-top: -40px;

        }
        .dataTables_filter{



        margin-top: -2px;

        }
        #doc_sub_table{
            margin-top: 40px;
        }

        .client-add {
            margin-bottom: 20px;
            margin-top: -10px;
                 }

        </style>

 @stop


