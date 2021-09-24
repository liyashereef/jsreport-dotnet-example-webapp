@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .filter{
         margin-left: -120px;

        }
        html, body {
        max-width: 100%;
        overflow-x: hidden;
        }
        #add-new-button{
            margin-right:-3.2em ;
        }
        .modal-div{
            padding-bottom: 15px;
            margin-bottom:-1em ;
        }
        .text-wrap{
            width:500;
            word-wrap: break-word;
        }
     </style>
</head>

@section('content')
    <div class="table_title">
        <h4>Video Post</h4>
    </div>

    <div class="row col-sm-12 modal-div">
      <div class="col-sm-6" >
      <div class="row">
            <div class="col-md-4"><label class="filter-text">Choose customer</label></div>
            <div class="col-md-6 filter">

                    {!!Form::select('customer_filter_id',[null=>'Please Select'] + $project_list,null, ['class' => 'form-control customer_filter_id','id'=>'customer_filter_id'])!!}

                <span class="help-block"></span>
            </div>
        </div>
      </div>

      <div class="col-sm-4" >

      </div>
      <div class="col-sm-2" >
        @can('add_video_post')
        <div class="add-new" id="add-new-button" data-title="Add New Video post">Add<span class="add-new-label"> New</span>
        </div>
        @endcan
      </div>
    </div>


    <div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Payperiod</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <br>
<table class="table table-bordered" id="table-id">
     <thead>
         <tr>
             <th>Project Number</th>
             <th>Project Name</th>
             <th>Video Post Name</th>
             <th>Video Post Description</th>
             <th>Uploaded Date </th>
             <th>Uploaded By </th>
             <th>Post</th>
             <th>Actions</th>
         </tr>
     </thead>
 </table>

 @stop

 @section('scripts')
   <script>

    $(function () {

        $(".customer_filter_id").select2();

        function collectFilterData() {
            return {
                client_id:$("#customer_filter_id").val(),

            }
        }

        $(".customer_filter_id").change(function(){
            table.ajax.reload();
        });

        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#table-id').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url":'{{ route('videopost.summary.list') }}',
                "data": function ( d ) {
                    return $.extend({}, d, collectFilterData());

                        },
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                }
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
            order: [[ 4, "desc" ]],
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],

            columns: [

                {data: 'project_number', name: 'project_number'},
                {data: 'client_name', name: 'client_name'},
                {data: 'file_name', name: 'file_name'},
                {
                    data: null,
                    name: 'description',
                    render: function (o) {
                        var descriptionDiv = '';
                        var description = o.description;
                        if(description.length > 100)
                        {
                            descriptionDiv += '<div class="text-wrap width-200">' +  description.substr(0, 100) + '...</div>';
                        }else{
                            descriptionDiv += '<div class="text-wrap width-200">' + description + '</div>';
                        }
                       return descriptionDiv;
                    },

                },


                {data: 'uploaded_date', name: 'uploaded_date'},
                {data: 'uploaded_by', name: 'uploaded_by'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        iconClass = 'fa-file-o';
                        if(o.file_type === 'video') {
                            iconClass = 'fa-file-video-o';
                        }
                        var actions = '';
                        actions += '&nbsp;&nbsp;&nbsp;' +
                            '<a href="#" class="download" data-id="' + o.file_name + '"' +
                            'data-name="' + o.video_path + '"' +
                            'data-file-type="' + o.file_type + '">' +
                            '<i class="fa fa-6 '+iconClass+'"></i>' +
                            '</a>'
                        return actions;
                    },
                },
                {
                    data: null,
                    orderable:false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_video_post')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '>&nbsp;&nbsp;&nbsp;</a>';
                        @endcan
                        @can('delete_video_post')
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '>&nbsp;</a>';
                        @endcan
                        return actions;
                    },
                }
                ]
        });
        } catch(e){
            console.log(e.stack);
        }
            /* videopost  download - Start*/
            $("#table-id").on("click", ".download", function (e) {
            $('#myModal').find('.modal-body').html('');
            $('#myModal .modal-title').text('');
            filepath = $(this).data('name');
            fileName = $(this).data('id');
            fileType = $(this).data('file-type');
            $.ajax({
                url: "{{route('videopost.filedownload')}}",
                type: 'GET',
                data: "filepath=" + filepath,
                success: function (data) {
                    if (data) {
                        var html = "";
                        if (fileType === 'video') {
                            html += '<video style="border:none;" width="890" height="500" controls>';
                            html += '<source src="' + data + '" type="video/mp4">';
                            html += 'Your browser does not support the video tag.';
                            html += '</video>';
                            $("#myModal .modal-body").html(html);
                            $("#myModal").modal();
                            $('#myModal .modal-title').text("Video Post : " +fileName);
                        } else {
                            window.open(data,'_blank');
                        }
                    } else {
                        console.log(data);
                        swal("Oops", "Download was unsuccessful", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });

        /* Video Post download - End*/

                /* Video Post download - End*/
        $('#add-new-button').on('click',  function(e){
        window.location='{{ route('videopost') }}';
        });

        $("#table-id").on("click", ".edit", function(e) {

            var id = $(this).data('id');
            var base_url = "{{route('video-post.edit',':id')}}";
            var url = base_url.replace(':id', id);
            window.location = url;

        });

        /* Video Post Delete  - Start */
        $('#table-id').on('click', '.delete', function (e) {
            id = $(this).data('id');
            console.log(e);
            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, remove",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "{{route('videopost.destroy')}}",
                    type: 'GET',
                    data: "id=" + id,
                    success: function (data) {
                        if (data.success) {
                            swal("Deleted", "Video Post has been deleted successfully", "success");
                            table.ajax.reload();
                        } else {
                            alert(data);
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    },
                    contentType: false,
                    processData: false,
                });
            });
        });
        /* Video Post Delete  - End */
        });





    </script>
    <style>

.client-filter {
        padding-right: 0px;
        padding-bottom: 5px;
        z-index: 6000;
    }
    #clientname-filter {
        width: 365px;
    }

.modal-dialog {
 height:1000px !important;
 width: 960px !important;

 }

.modal-content {
  height: 65%;
  width: 117%;
}

.modal-header {

 padding:14px 14px;

 }

    .select2-dropdown {
        z-index: 0 !important;
    }
    .filter-div{
     padding-bottom: 15px;
    }
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
    </style>
                @stop
