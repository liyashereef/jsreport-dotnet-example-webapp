@extends('adminlte::page')
@section('title', 'IDS Office Slot')
@section('content_header')
<h1>{{$office->name}} : Slot List</h1>
@stop

@section('css')
<style>
    .fa {
        margin-left: 11px;
    }
    .select2 .select2-container{
        width : 12% !important;
    }
    .add-new-modal {
        float: right;
        width: 200px;
        background-color: #f26222;
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 10px;
        text-align: center;
        border-radius: 5px;
        padding: 5px 0px;
        margin-left: 5px;
        cursor: pointer;
    }
</style>
@stop

@section('content')
<div id="message"></div>

<table class="table table-bordered" id="office-table">
    <thead>
        <tr>
            
            <th>#</th>
            <th>Display Name</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Created At</th>

        </tr>
    </thead>
</table>

@stop
@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}&libraries=places"></script>
<script>
    $(function () {
        $('#slot-ids').select2();//Added Select2 to slot-ids listing
    
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var office_slot_url = '{{ route("idsOffice.slot-data",":officeId") }}';
            var officeSlotURL = office_slot_url.replace(':officeId', {{$officeId}});
            
            var table = $('#office-table').DataTable({
              
                ajax: {
                    "url": officeSlotURL,
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: 'display_name',
                        name: 'display_name'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time',
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    
                  
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

    });



</script>

@stop
