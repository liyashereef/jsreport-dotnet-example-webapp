@extends('layouts.app')
@section('content')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .delete{
            margin-left: 10px;
        }
    </style>
</head>

@section('content')
<div class="table_title">
    <h4>Team Management</h4>
</div>
<div class="col-lg-12">
    <div class="">
        <a href="{{\Illuminate\Support\Facades\URL::to('learningandtraining/teams/create')}}" style="width: 180px;"  class="btn add-new" id="new-team-record"> Create New Team</a>
    </div>
</div>
<div id="message"></div>



<table class="table table-bordered" id="team-table">
    <thead>
        <tr>
            <th>#</th>
             <th>Team Name</th>
             <th>Description</th>
             <th>Parent Team</th>
            <th>Mandatory Courses</th>
            <th>Recommended Courses</th>
            {{-- @can('edit_client_feedback') --}}
            <th>Actions</th>
            {{-- @endcan --}}
        </tr>
    </thead>
</table>

{{--@include('learningandtraining::admin.team.form')--}}

@stop
@section('scripts')
<script>
    $(function () {

/***** Team  Listing - Start */

        $('#name').val();
        $('#description').val();
        $('#team_id').val();
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#team-table').DataTable({
                bProcessing: false,
                responsive: true,
                dom: 'lfrtip',
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url":'{{ route('learningandtraining.team.list') }}',
                    "data": function ( d ) {
                        d.payperiod = $("#payperiod-filter").val();
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                order: [[0, 'desc']],
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [
                {
                    data: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false
                },
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
                {data: 'parent_team', name: 'parent_team'},
                {data: 'mandatory_courses', name: 'mandatory_courses'},
                {data: 'recommended_course', name: 'recommended_course'},

{{--                @can('edit_client_feedback')--}}
                {
                    data: null,
                    name: 'action',
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        var id =  o.id;
                        var url = '{{ route("learningandtraining.team.edit-form",'') }}';
                        actions = '<a href="'+url+"/"+ o.id +'" class="edit fa fa-edit" data-id=' + o.id + '></a>';
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        return actions;
                    },
                }
{{--                @endcan--}}
                ]
            });
        } catch(e){
            console.log(e.stack);
        }

/***** Team  Listing - End */

/***** Team  Delete - Start */
        $('#team-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('learningandtraining.team.destroy',':id') }}";
            var rec_training = <?php echo json_encode( config('globals.rec_training_id')); ?>;
            if(id == rec_training ){
                swal({
                    title: "Warning",
                    text: "This team is associated with recruitment. Cannot be deleted",
                    type: "warning",
                    confirmButtonText: "OK",
                    closeOnConfirm: true
                });
            }else{  
            var url = base_url.replace(':id', id);
            swal({
                    title: "Are you sure?",
                    // text: "You will not be able to undo this action. Proceed?",
                    text: "Warning: Once this team is deleted, it cannot be recovered. All courses assigned to the users in this team will be reset. Do you really want to continue ?",
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
                                swal("Deleted", "Team has been deleted successfully", "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else {
                                swal("Alert", "Cannot able to delete team", "warning");
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
        });
/***** Team Delete- End */

    });

/***** Team Creation Input fields clearing - Start */
    $('#new-team-record').on('click', function(e) {
        $('#team_id').val();
        $('#name').val();
        $('#description').val();
    });
/***** Team Creation Input fields clearing - End */

  



</script>
@stop
