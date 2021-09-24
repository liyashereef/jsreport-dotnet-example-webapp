@extends('layouts.app')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<div class="table_title">
    <h4>RFP Summary</h4>
</div>

<table class="table table-bordered" id="table-id">
    <thead>
        <tr>
            <th class="sorting">#</th>
            <th class="sorting">Last Update</th>
            <th class="sorting">Prepared By</th>
            <th class="sorting">Proposal</th>
            <th class="sorting">Proposal Deadline</th>
            <th class="sorting">Location</th>
            <th class="sorting">Status</th>
            <th></th>
            <th class="sorting" style="text-align: left">Last Tracking Stage</th>
            <th class="sorting">Outcome</th>
            <th class="sorting">Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('url'=>'#','id'=>'rfp-action-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{csrf_field()}}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group {{ $errors->has('rpf_status') ? 'has-error' : '' }}" id="rpf_status">
                    <label for="rpf_status" class="col-sm-12 control-label">Choose Status</label>
                    <div class="col-sm-12">
                        {{ Form::select('rpf_status', [null=>'Please Select','Pending'=>'Pending','Approved'=>'Approved','Rejected'=>'Rejected'], null,array('class' =>'form-control','required'=>true,'id'=>'rpf_status_id')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('assign_resource_id') ? 'has-error' : '' }}"
                    id="assign_resource_id">
                    <label for="assign_resource_id" class="col-sm-12 control-label">Assign Resource</label>
                    <div class="col-sm-12">
                        {{ Form::select('assign_resource_id', [null=>'Please Select']+$rfpLookups, null,array('class' => 'form-control','required'=>true,'id'=>'assign_resource_input_id'))}}
                        <small class="help-block"></small>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn submit','id'=>'mdl_save_change'))}}
                {{ Form::button('Cancel', array('class'=>'btn cancel','data-dismiss'=>"modal", 'aria-hidden'=>true))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<div class="modal fade" id="myModals" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('url'=>'#','id'=>'rfp-status-action-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{csrf_field()}}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group {{ $errors->has('statuschoosen') ? 'has-error' : '' }}" id="statuschoosen">
                    <label for="statuschoosen" class="col-sm-12 control-label">Choose Status</label>
                    <div class="col-sm-12">
                        {{ Form::select('status', [null=>'Please Select','Win'=>'Win','Lose'=>'Lose'], null,array('class' =>'form-control','required'=>true,'id'=>'statuschoosen')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('rfp_debrief_attended') ? 'has-error' : '' }}"
                    id="rfp_debrief_attended" style="display:none;">
                    <label for="rfp_debrief_attended" class="col-sm-12 control-label rfp-label">Was RFP debrief
                        offered by
                        the
                        client?</label>
                    <div class="col-sm-12">
                        {{ Form::select('rfp_debrief_attended', [null=>'Please Select','Yes'=>'Yes','No'=>'No'], null,array('class' =>'form-control','id'=>'rfp_debrief_attended_id')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('did_we_take_it') ? 'has-error' : '' }}" id="did_we_take_it" style="display:none;">
                    <label for="did_we_take_it" class="col-sm-12 control-label rfp-label">Did we take it?</label>
                    <div class="col-sm-12">
                        {{ Form::select('did_we_take_it', [null=>'Please Select','Yes'=>'Yes','No'=>'No'], null,array('class' =>'form-control','id'=>'did_we_take_it_id')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('did_we_take_it_no') ? 'has-error' : '' }}" id="did_we_take_it_no"
                    style="display:none;">
                    <label for="did_we_take_it_no" class="col-sm-12 control-label">If we attended the debrief - why
                        did
                        we lose</label>
                    <div class="col-sm-12">
                        {{ Form::textarea('did_we_take_it_no',null,array('placeholder'=>'If we attended the debrief - why did we lose','id'=>'did_we_take_it_no_id','class'=> 'form-control','rows' => 2, 'cols' => 40)) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('rfp_debrief_attended_no') ? 'has-error' : '' }}"
                    id="rfp_debrief_attended_no" style="display:none;">
                    <label for="rfp_debrief_attended_no" class="col-sm-12 control-label">Why didn't we attend the
                        debrief </label>
                    <div class="col-sm-12">
                        {{ Form::textarea('rfp_debrief_attended_no',null,array('placeholder'=>'Reason why didnt we attend the debrief','class'=> 'form-control','rows' => 2, 'cols' => 40)) }}
                        <small class="help-block"></small>
                    </div>
                </div>


                <div class="form-group {{ $errors->has('offered_by_the_client_no') ? 'has-error' : '' }}"
                    id="offered_by_the_client_no" style="display:none;">
                    <label for="offered_by_the_client_no" class="col-sm-12 control-label"> Please explain the reason </label>
                    <div class="col-sm-12">
                        {{ Form::textarea('offered_by_the_client_no',null,array('placeholder'=>'Please explain the reason','class'=> 'form-control','rows' => 2, 'cols' => 40)) }}
                        <small class="help-block"></small>
                    </div>
                </div>

    </div>
    <div class="modal-footer">
        {{ Form::submit('Save', array('class'=>'button btn submit','id'=>'mdl_save_change'))}}
        {{ Form::button('Cancel', array('class'=>'btn cancel','data-dismiss'=>"modal", 'aria-hidden'=>true))}}
    </div>
    {{ Form::close() }}
</div>
</div>
</div>

@stop
@section('scripts')
<script>
    $(function () {

        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#table-id').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('rfp-summary.list') }}",
            dom: 'Blfrtip',

              buttons: [{
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: [ 0, 1, 2,3,4, 5,6,8,9 ],
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [ 0, 1, 2,3,4, 5,6,8,9 ],
                    }
                },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: [ 0, 1, 2,3,4, 5,6,8,9 ],
                        stripHtml: false,
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columnDefs: [
                {
                    'targets': [0,1,2,3,4,5,6,7],
                    'className': 'dt-center datatable-v-center'
                },
            ],columnDefs: [
                { width: 150, targets: 1 },
            { width: 150, targets: 9 },
            { width: 180, targets: 10 }
        ],
            columns: [
                {
					data: 'DT_RowIndex',
					name: '',
					sortable:false
				},
                {data: 'updated_at', name: 'updated_at'},
                {data: 'prepared_by', name: 'prepared_by',defaultContent: "--"},
                {data: 'rfp_site_name', name: 'rfp_site_name',defaultContent: "--"},
                {data: 'submission_deadline', name: 'submission_deadline',defaultContent: "--"},
                {data: 'location', name: 'location',defaultContent: "--"},
                {data: 'status', name: 'status',defaultContent: "--"},
                {data: 'step_number', name: 'step_number',visible:false, className: "text-left"},
                {
                    data: null,
                    className: "text-left",
                    name: 'last_step',
                    render: function(row){
                        if(row.status=="Rejected" || row.status=="Pending"){
                            return "--";
                        }else{
                            return row.step_number+'-' +row.last_step;
                        }


                    },
                    defaultContent: '--'
                },
                {
                    data: 'rfp_win_lose',
                    name: 'rfp_win_lose',
                    render: function (data, type, row) {
                        console.log(data, type, row);
                        // debugger;
                        let actions = data + ' ';
                        var edit_url = '{{ route("rfp.create-client-onboarding", ":id") }}';
                        edit_url = edit_url.replace(':id', row.id);
                        if(row.client_onboarding_id) {
                            edit_url+='/'+row.client_onboarding_id;
                        }
                        var track_url = '{{ route("rfp.track-client-onboarding", ":id") }}';
                        track_url = track_url.replace(':id', row.id);
                        if (data == 'Win') {
                            if(row.client_onboarding_id){
                            @canany(['view_assigned_client_onboarding_steps','view_all_client_onboarding_steps','update_client_onboarding_step_status'])
                                actions += '<a title="Track Onboarding" href="'  + track_url + '" class="status fa fa-compress fa-lg" style="margin: auto 5px"></a>';
                            @endcan
                            }
                            @can('configure_client_onboarding_tracking')
                                actions += '<a title="Edit Onboarding" href="' + edit_url + '" class="status fa fa-edit fa-lg" style="margin: auto 5px"></a>';
                            @endcan
                        }
                        return actions;
                    }
                },
                {
                    data: null,
                    sortable: false,
                    render: function (row) {
                        let actions = '';
                        var edit_url = '{{ route("rfp.edit", ":id") }}';
                        edit_url = edit_url.replace(':id', row.id);
                        actions += '<a attr-id="' + row.id +'" title="Change Outcome Status" onclick="openModals(' + row.id +')" href="#" class="fa fa-tasks fa-lg "   style="margin: auto 5px"></a>';
                        @can('rfp_approval')
                            actions += '<a title="Process" status ="'+row.rpf_status+'" assigneresource ="'+row.assign_resource_id+'" onclick="openApprovalModal(' + row.id +',this)" href="#" class="fa fa-podcast fa-lg" style="margin: auto 5px"></a>';
                            if (row.status == 'Approved')
                            {
                                var tracking_process_url ='{{ route("rfp.track",array(":rfp_id")) }}';
                                tracking_process_url = tracking_process_url.replace(':rfp_id',row.id);
                                actions += '<a title="Track RFP" href="' +tracking_process_url +'" class="status fa fa-compress fa-lg" style="margin: auto 5px"></a>';
                            }
                        @endcan


                        @can('edit_rfp')
                            actions += '<a title="Edit" href="' + edit_url +'" class="fa fa-edit fa-lg" style="margin: auto 5px" data-id=' + row.id + '></a>';
                        @endcan
                        @can('delete_rfp')
                            actions += '<a title="Trash"  onclick="unSetRfpApplication(' + row.id +',this)" href="#" class="fa fa-trash fa-lg" style="margin: auto 5px"></a>';
                        @endcan
                        return actions;
                    }
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        $(".winlose").on("click",function(e){
            alert("here");
        })

         $("#table-id").on("click", ".status", function (e) {
            var tracking_process_url ='{{ route("rfp.track",array(":rfp_id")) }}';
         tracking_process_url = tracking_process_url.replace(':rfp_id',row.id);
            window.location.href = tracking_process_url;
            e.preventDefault();
         });
        $('#rfp-action-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('rfp-status.store') }}";
            var formData = new FormData($('#rfp-action-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal("Saved", "The record has been saved", "success");
                        $("#myModal").modal('hide');
                            table.ajax.reload();

                    } else {
                        alert(data);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });

        $('#statuschoosen').on('change',function()
        {
         if($('select[name=status]').val()==="Lose")
        {
         $("#rfp_debrief_attended").show()
         $('.rfp-label span').remove();
         $(".rfp-label").append('<span class="mandatory">*</span>');
         $("#offered_by_the_client_no").hide()
         document.getElementById("rfp_debrief_attended_id").required = true;

        }
        else{
            $("select[name='rfp_debrief_attended']").val("");
            $("select[name='did_we_take_it']").val("");
            $("textarea[name='did_we_take_it_no']").val("");
            $("#rfp_debrief_attended").hide()
            $("#offered_by_the_client_no").hide()
            $("#did_we_take_it").hide()
            $('#did_we_take_it_id').prop('required',false);
            $("#did_we_take_it_no").hide()
            $("#rfp_debrief_attended_no").hide()
            document.getElementById("rfp_debrief_attended_id").required = false;

            }
        });


    $('#rfp_debrief_attended_id').on('change',function(){
    if( $(this).val()==="Yes"){
    $("#did_we_take_it").show()
    $("#did_we_take_it_no").show()
    $("#rfp_debrief_attended_no").hide()
    $("textarea[name='rfp_debrief_attended_no']").val("");
    $("select[name=did_we_take_it]").val("").trigger("change");
    $("textarea[name=did_we_take_it_no]").val("");

    document.getElementById("did_we_take_it_id").required = true;

    }
    else{
        $("textarea[name=offered_by_the_client_no]").val("");
        $("#offered_by_the_client_no").hide();
        $('textarea[name="did_we_take_it_no_id"]').val("");
        $("textarea[name=rfp_debrief_attended_no]").val("");
        $("#did_we_take_it_no").hide()
        $("#did_we_take_it").hide()
        if( $(this).val()==="No"){
            $("#rfp_debrief_attended_no").hide()
            $("#offered_by_the_client_no").show();
        }else {
            $("#rfp_debrief_attended_no").show()
        }

        $("#did_we_take_it_no").hide()
        document.getElementById("did_we_take_it_id").required = false;
    }
    });

    $('#did_we_take_it_id').on('change',function(){
        $("#did_we_take_it_no_id").val("");
        if( $(this).val()==="Yes"){
            $('#did_we_take_it_no').show()
            $('#rfp_debrief_attended_no').hide()
            $("textarea[name=rfp_debrief_attended_no]").val("");
            $("#offered_by_the_client_no").show();

        }else{
            $('#did_we_take_it_no').hide()
            $('#rfp_debrief_attended_no').show()
            $("textarea[name=did_we_take_it_no_id]").val("");
            $("#offered_by_the_client_no").hide();
            $("textarea[name=offered_by_the_client_no]").val("");
        }
    });




    $('#rfp-status-action-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('rfp-status-win-lose.store') }}";
            var formData = new FormData($('#rfp-status-action-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal("Saved", "The record has been saved", "success");
                        $("#myModals").modal('hide');
                            table.ajax.reload();

                    } else {
                        alert(data);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });

    });


    $("#rfp_debrief_attended_id").on("change",function(e){
        if($(this).val()=="Yes"){
            $("textarea['name'='offered_by_the_client_no']").val("");
        }else if($(this).val()=="No"){
            $("select['name'='did_we_take_it']").val("");
            $("textarea['name'='did_we_take_it_no']").val("");
        }else{
            $("select['name'='did_we_take_it']").val("");
            $("textarea['name'='did_we_take_it_no']").val("");
        }
    })
    function openModals(id) {
        $.ajax({
            type: "get",
            url: "{{route('rfp-summary.winlose')}}",
            data: {"rfpid":id},
            success: function (response) {

                var data = jQuery.parseJSON(response);
                var did_we_take_it = data.did_we_take_it;
                var did_we_take_it_no = data.did_we_take_it_no;
                var offered_by_the_client_no = data.offered_by_the_client_no;
                var rfp_debrief_attended = data.rfp_debrief_attended;
                var rfp_debrief_attended_no = data.rfp_debrief_attended_no;
                var status = data.status;
                $("select[name='status']").val(status).trigger("change");

                if(rfp_debrief_attended=="Yes"){
                    $("textarea[name='rfp_debrief_attended_no']").val("");
                   $("select[name='rfp_debrief_attended']").val(rfp_debrief_attended).trigger("change").after(function(e){

                   });
                   if(did_we_take_it=="Yes"){
                    $("#did_we_take_it").show().after(function(e){
                        $("select[name=did_we_take_it]").val(did_we_take_it).trigger("change").after(function(e){
                            $("#did_we_take_it_no_id").val(did_we_take_it_no);
                        });
                        $("#did_we_take_it_no").show();
                    });
                    $("textarea[name='offered_by_the_client_no']").val(offered_by_the_client_no);
                    $("#rfp_debrief_attended_no").hide();
                   }else if(did_we_take_it=="No"){
                    $("#did_we_take_it").show().after(function(e){
                        $("select[name=did_we_take_it]").val(did_we_take_it).trigger("change").after(function(e){

                            $("textarea[name=did_we_take_it_no]").val(rfp_debrief_attended_no);
                            //$("#did_we_take_it_no_id").val(did_we_take_it_no);
                        });
                        $("textarea[name=rfp_debrief_attended_no]").val(rfp_debrief_attended_no);
                        $("#did_we_take_it_no").hide();
                        $("#rfp_debrief_attended_no").show();
                    });
                    }



                }else if(rfp_debrief_attended=="No"){
                    $("select[name='did_we_take_it']").val("");

                    $("select[name='rfp_debrief_attended']").val(rfp_debrief_attended).trigger("change").after(function(e){
                        $("#offered_by_the_client_no").show();
                        if(offered_by_the_client_no!=""){
                            $("textarea[name='offered_by_the_client_no']").val(offered_by_the_client_no);

                        }else{
                            $("textarea[name='offered_by_the_client_no']").val("");
                        }
                   });
                   $("#rfp_debrief_attended_no").css("display","none");

                }else{
                   $("select[name='rfp_debrief_attended']").val(null);
                }



            }
        });
    $('#myModals form')[0].reset();
    $('#myModals').find('input[name="id"]').val(id);
    $('#myModals').modal();
}
/**
 * Open a modal popup
 *
 * @param {*} id
 */
 function openApprovalModal(id,self) {

    var resid = $(self).attr("assigneresource");
    var status = $(self).attr("status");

    $('#myModal form')[0].reset();
    $('#myModal').find('input[name="id"]').val(id);
    if(resid>0){
        $('#myModal select[name=assign_resource_id]').val(resid)
    }

    if(status!=""){

        $('#myModal select[name=rpf_status]').val(status)
    }
    if(status=="Rejected"){
        $("#assign_resource_input_id").val("");
        $('#myModal select[name=assign_resource_id]').removeAttr("required")
        $("#assign_resource_id").hide();
    }else{
        $("#assign_resource_id").show();
        $('#myModal select[name=assign_resource_id]').prop("required",true)
    }
    $('#myModal').modal();
}

$("#rpf_status_id").change(function(e){
    if($(this).val()=="" || $(this).val()=="Rejected"){
        $("#assign_resource_input_id").val("")
        $("#assign_resource_id").hide();
        $('#myModal select[name=assign_resource_id]').removeAttr("required")
    }else{
        $("#assign_resource_id").show();
        $('#myModal select[name=assign_resource_id]').prop("required",true)
    }
})

 function unSetRfpApplication(pid,self){
     var url ="{{route('rfp.trash')}}"
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
                    type: "get",
                    url: url,
                    data: {id:pid},

                    success: function (response) {
                        if (response.success) {

                            swal("Deleted", "Record has been deleted successfully", "success");

                            table.ajax.reload();
                        }
                     else {
                              swal("Warning", "Delete failed. Try again", "warning");
                        }
                    }
                });
            });
}


</script>

@stop
