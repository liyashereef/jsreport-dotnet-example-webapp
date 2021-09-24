@extends('adminlte::page')
@section('title', 'IDS Office Slot Blocked List')
@section('content_header')
<h1>{{$office->name}} : Slot Blocked List</h1>
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
        width: 175px;
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
    .filter-section label{
        margin-top: 1%;
    }
    .table {
        margin-top: 20px;
    }
    .checkbox-tr{
        width: 20px !important;
    }
    .help-block{
        color: #a94442 !important;
        font-weight: bold;
    }

</style>
@stop

@section('content')
<div id="message"></div>

    <div class="row">
        <div class="filter-section col-sm-8" id="">
            <div class="row">
                <div class="col-sm-5">
                    <label for="start_date" class="col-sm-5 control-label">Start Date</label>
                    <div class="col-sm-7">
                        {{ Form::date('start_date',null,array('class'=>'form-control','placeholder' => 'Start Date','id'=>'start_date')) }}
                        <small class="help-block" id="start_date_error"></small>
                    </div>
                </div>
                <div class="col-sm-5">
                    <label for="end_date" class="col-sm-5 control-label">End Date</label>
                    <div class="col-sm-7">
                        {{ Form::date('end_date',null,array('class'=>'form-control','placeholder' => 'End Date','id'=>'end_date')) }}
                        <small class="help-block"  id="end_date_error"></small>
                    </div>
                </div>
                <div class="col-sm-2">
                    <input class="button btn btn-primary blue" id="search" type="button" value="Search">
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="add-new-modal" data-title="Add New Offices">Block
                <span class="add-new-label">Slot</span>
            </div>
        </div>
    </div>

    <table class="table table-bordered" id="office-table">

        <thead>
            <tr>
                <th class="dt-body-center text-center select_header checkbox-tr">
                    <input name="select_all" value="1" id="select-all" type="checkbox"/>
                </th>
                <th>Slot Block Date</th>
                <th>Slot</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tableBody"></tbody>

    </table>

    <div class="col-sm-2">
        <input class="button btn btn-danger" id="deleteSelected" type="button" value="Delete All">
    </div>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">{{$office->name}} : Slot Blocking</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'ids-office-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null) }}
            <div class="modal-body">
                {!! Form::hidden('ids_office_id',$officeId )!!}
                <div class="form-group" id="slot_block_date">
                    <label for="slot_block_date" class="col-sm-3 control-label">Date</label>
                    <div class="col-sm-9">
                        {{ Form::date('slot_block_date',null,array('class'=>'form-control','placeholder' => 'Name','id'=>'block_date')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="slot_ids">
                    <label for="slot_ids" class="col-sm-3 control-label">Select Slots </label>
                    <div class="col-sm-9">
                        {{ Form::select('slot_ids[]',[],old('time_slot'),array('class'=> 'form-control', 'id'=>'time_slot','multiple'=>"multiple",'style'=>'width: 591px;')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

@stop
@section('js')

<script>

    const blockedSlot = {
        ref: {
            idsofficeId : {{$officeId}},
            startDate : null,
            endDate : null,
            blockedData : [],
            dataTable : null,
            selectedBlockIds : [],
        },
        init() {
            //Event listeners
            this.registerEventListeners();
            this.fetchReportDataEvent();
        },
        registerEventListeners() {
            let root = this;

           /**START** Trigger filter */
            $('body').on('click', '#search', function (e) {
                $("#select-all").prop('checked', false);
                var trigerFunction = true;
                $('.form-group').removeClass('has-error').find('.help-block').text('');

                if($('#start_date').val() == ''){
                    $('#start_date_error').html('Start date is required');
                    trigerFunction = false;
                }
                if($('#end_date').val() == ''){
                    $('#end_date_error').html('End date is required');
                    trigerFunction = false;
                }

                if(trigerFunction == true){
                    root.ref.startDate = $('#start_date').val();
                    root.ref.endDate  = $('#end_date').val();
                    root.fetchReportDataEvent();
                }

            });
             /**END** On click select all check box, select all child rows */

            /**START** On click select all check box, select all child rows */
            $('#office-table').on('click', '#select-all', function(){
                var rows = root.ref.dataTable.rows({ 'search': 'applied' }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });
             /**END** On click select all check box, select all child rows */


            /**START** On click select all check box, select all child rows */
            $('body').on('click', '#deleteSelected', function(){
                var rows = root.ref.dataTable.rows({ 'search': 'applied' }).nodes();
                root.ref.selectedBlockIds = $.map($('input[name="slot_block_check"]:checked',rows), function(c){return c.value; })
                // console.log(root.ref.selectedBlockIds)
                root.removeSelectedEntries();
            });
             /**END** On click select all check box, select all child rows */

            /*Add new - modal popup -start */
            $('.add-new-modal').on('click', function () {
                var title = $(this).data('title');
                $("#myModal").modal();
                $('#ids-office-form #time_slot').empty()
                // $("#myModal #time_slot").val('').trigger('change');
                $('input[name="slot_block_date"]').val('');
                $('#ids-office-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            });
            /*Add new - modal popup -end */

            /* Date on change event - Start */
            $('#block_date').on('change', function() {
                var ids_office_id = {{$officeId}};
                var slot_booked_date = $(this).val();
                // console.log('id-date', ids_office_id, slot_booked_date);
                $('#ids-office-form #time_slot').empty();
                var id = $(this).val();
                var url = "{{route('idsscheduling-admin.office.free-slot')}}";
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {'ids_office_id': ids_office_id, 'slot_booked_date':slot_booked_date},
                    success: function(data) {
                        $('#ids-office-form #time_slot').append($("<option></option>").attr("value",'').text('All Slots'));
                        $.each(data, function(index, slot) {
                            $('#time_slot').append($("<option></option>")
                            .attr("value",slot.id)
                            .text(slot.display_name));
                        });
                    }
                });
            });
            /* Date on change event - End */

             /* Office Store - Start*/
             $('#ids-office-form').submit(function (e) {
                e.preventDefault();
                root.storeBlockEntry();
            });
            /* Office Store - End*/

            /* Office Delete  - Start */
            $('#office-table').on('click', '.delete', function (e) {
                var id = $(this).data('id');
                root.ref.selectedBlockIds = [];
                root.ref.selectedBlockIds.push(id);
                root.removeSelectedEntries();
            });
            /* Office Delete  - End */

        },
        fetchReportDataEvent(){
            let root = this;
            let url = '{{ route("idsOffice.slot-block-data-search") }}';
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "ids_office_id":root.ref.idsofficeId,
                    'start_date':root.ref.startDate,
                    'end_date':root.ref.endDate,
                },
                type: 'GET',
                success: function(data) {
                  root.ref.blockedData = data;
                  root.setReportData();
                },
                error: function(xhr, textStatus, thrownError) {
                    if(xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                },
                contentType: false
            });
       },
       setReportData(){
            let root = this;
            if ($.fn.DataTable.isDataTable( '#office-table' )) {
                root.ref.dataTable.clear();
                root.ref.dataTable.destroy();
            }
            let tableBody = '';

           //IDS trends set table body data.
            $.each(root.ref.blockedData, function(index, value) {
                var slot = "";
                if(value.ids_office_slots){
                    slot = value.ids_office_slots.display_name;
                }
                tableBody += `<tr>
                            <td class="dt-body-center text-center">
                                <input type="checkbox"  name="slot_block_check" value="${value.id}">
                            </td>
                            <td class="slingle-line" data-order="${value.slot_block_date}">${value.slot_block_date}</td>
                            <td class="slingle-line" data-order="${slot}">${slot}</td>
                            <td class="slingle-line" data-order="${value.created_at}">${value.created_at}</td>
                            <td><a href="#" class="delete fa fa-trash-o" data-id="${value.id}"></a></td>
                            `;
                tableBody += `</tr>`;

            });

            //IDS trends set table body.
            $('#tableBody').html(tableBody).after(function(e){
                root.initDataTable();
            });

       },
       initDataTable(){
            let root = this;
            var screenheight = screen.height;
            try{
                root.ref.dataTable = $('#office-table').DataTable({
                    lengthMenu: [
                        [10, 25, 50, 100, 500, -1],
                        [10, 25, 50, 100, 500, "All"]
                    ],
                    destroy: true,
                });
            } catch(e){
                console.log(e.stack);
            }
        },

        removeSelectedEntries(){
            let root = this;
            let url = '{{ route("idsOffice.slot-block-data-remove") }}';

            if(root.ref.selectedBlockIds.length >=1){
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
                        url: url,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: {"ids_office_ids":root.ref.selectedBlockIds},

                        success: function(data) {
                            swal("Deleted", "Data has been deleted successfully", "success");
                            root.ref.selectedBlockIds = [];
                            root.fetchReportDataEvent();
                        },
                        error: function(xhr, textStatus, thrownError) {
                            if(xhr.status === 401) {
                                window.location = "{{ route('login') }}";
                            }
                        },

                    });

                });
            }
        },
        storeBlockEntry(){
            let root = this;
            var $form = $('#ids-office-form');
            var formData = new FormData($form[0]);
            var url = "{{ route('idsOffice.slot.blocking') }}";

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal("Saved", 'Slot has been blocked successfully', "success");
                        $("#myModal").modal('hide');
                        $('#ids-office-form')[0].reset()
                        root.fetchReportDataEvent();
                    } else if (data.success == false) {
                        if (Object.prototype.hasOwnProperty.call(data, 'message') && data.message) {
                            swal("Warning", data.message, "warning");
                        } else {
                            console.log(data);
                        }
                    } else {
                        console.log(data);
                    }
                },
                fail: function (response) {

                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                }, always: function () {

                },
                contentType: false,
                processData: false,
            });
        }

    }

  // Code to run when the document is ready.
  $(function() {
    blockedSlot.init();
    $('#time_slot').select2();//Added Select2 to slot-ids listing
  });





</script>

@stop
