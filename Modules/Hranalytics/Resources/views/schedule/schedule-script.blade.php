<script>

    function openScheduleWindow(userId) {
        let html = '<div style="overflow-y:scroll;max-height:600px;" class="table-responsive-sm"><table class="table table-sm table-bordered schedule-overview"><thead>'
        html +='<tr><th class="text-center sticky-th" colspan="4" title="Previous"  onclick="loadPreviousData('+userId+')" style="background:#003A63;line-height:1px;cursor:pointer;"><i class="fa fa-angle-up" style="font-size:25px;"></i></th></tr>';
        html +='<tr><th class="sticky-th">Date</th><th class="sticky-th">Site No and Name</th><th class="sticky-th">Shift Timing</th><th class="sticky-th">Type</th></tr></thead><tbody class="schedule-overview-tbody">';
        html +='</tbody><tr><th class="text-center sticky-th" colspan="4" title="Next" onclick="loadNextData('+userId+')" style="background:#003A63;line-height:1px;cursor:pointer;"><i class="fa fa-angle-down" style="font-size:25px;"></i></th></tr>';
        html +='</table></div>';
        $('#scheduleOverViewModal .modal-body').html(html);
        $("#scheduleOverViewModal").modal();
        loadScheduleDateByDirection(userId);
    }

    function loadPreviousData(userId){
        let rowId = $('.schedule-overview tr.row-data:first').attr('data-id');
        loadScheduleDateByDirection(userId, 2, rowId);
    }

    function loadNextData(userId){
        let rowId = $('.schedule-overview tr.row-data:last').attr('data-id');
        loadScheduleDateByDirection(userId, 1, rowId);
    }

    function loadScheduleDateByDirection(userId, type = 0, rowId = null) {
        $.ajax({
            url: "{{ route('candidate.scheduleOverview') }}",
            type: 'GET',
            data:{
                'type':type,
                'key-date':rowId,
                'user-id':userId
            },
            success: function (resp) {
                let html = '';
                if (resp.records) {
                    let records = Object.entries(resp.records);
                    records.forEach(function(record, i) {
                        record.forEach(function(rowData, k) {
                            if($.isArray(rowData)) {
                                rowData.forEach(function(columnData, j) {
                                    if(j == 0) {
                                        html += `<tr class="row-data" data-id="${columnData.date_key}"><td>${columnData.date}</td><td>${columnData.site}</td><td>${columnData.timing}</td><td>${columnData.type}</td></tr>`;
                                    }else{
                                        html += `<tr class="row-data" data-id="${columnData.date_key}"><td></td><td>${columnData.site}</td><td>${columnData.timing}</td><td>${columnData.type}</td></tr>`;
                                    }
                                });
                            }
                        });
                    });
                }

                $('#scheduleOverViewModal .schedule-overview-tbody').html(html);
            }
        });
    }

    function prepareDatatable(id){
        $('#types-table').show();
        //$('#map_view_div').show();
       var table = $('#dynamic-table').DataTable({
           destroy:true,
               bProcessing: false,
               processing: true,
               serverSide: false,
               fixedHeader: false,
               deferLoading: 0,
               ajax: {
                   url: "{{ route('multifill.tablegenerate') }}", // Change this URL to where your json data comes from
                   type: "GET", // This is the default value, could also be POST, or anything you want.
                   data: function (d) {
                       d.id = JSON.stringify(id);
                   },
                   "error": function (xhr, textStatus, thrownError) {
                       if (xhr.status === 401) {
                           window.location = "{{ route('login') }}";
                       }
                   }
               },
               columnDefs: [ {
                               "searchable": false,
                               "orderable": false,
                               "targets": 0
                           } ],
               order: [[ 1, 'asc' ]],
               columns: [
                // { data: "day" ,visible:false  },
               { data:null,},
               { data: "start_date" },
               { data: "shift_from" },
               { data: "end_date" },
               { data: "shift_to" },
               { data: "site_rate" },
               { data: "hourdiff" },
               {
                       data: null,
                       sortable: false,
                       render: function (row) {
                           var shifts=transformData(row.name);
                                   return shifts;
                           }

                       },
               { data: "assigned" },
               {
                       data: null,
                       sortable: false,
                       render: function (row) {
                             var actions = '';
                            //  debugger
                            //   var shifts=transformData(row.name);
                           if(row.assigned!='Not Set')
                               {
                                 actions += '<a href="#"  class="fa fa-tasks  fa-disabled pointer" data-dateval="'+row.start_date+'" data-days="'+row.day+'" data-shifts="'+row.shift+'" data-level="'+row.security_clearance_level+'"data-shift-id="'+row.shift_id+'"></a>&nbsp';
                            }
                            else
                            {
                               actions += '<a title="Assign Employee"  class="fa fa-tasks schedule-data-selector pointer" data-dateval="'+row.start_date+'" data-days="'+row.day+'" data-shifts="'+row.shift+'" data-level="'+row.security_clearance_level+'"data-shift-id="'+row.shift_id+'"></a>&nbsp';
                            }

                            if(row.can_unassign)
                               {
                                actions += '&nbsp&nbsp<a title="Unassign" class="unassign fa fa-times pointer" data-id=' + row.shift_id + '></a>';
                            }
                            else
                            {
                                actions += '&nbsp&nbsp<a attr-parentid="'+row.parent_id+'" title="Unassign" class="unassignparentchange fa fa-times pointer" data-id=' + row.shift_id + '></a>';
                            }

                                 if(row.parent_id > 0) {
                                  actions += '&nbsp&nbsp<a  title="Delete Shift"  class="delete fa fa-trash pointer" data-id=' + row.shift_id + '></a>';
                                 }else if(row.no_of_shifts == 1){
                                    actions += '&nbsp&nbsp<a  title="Delete Shift"  class="delete fa fa-trash pointer" data-id=' + row.shift_id + '></a>';
                                 }
                                   return actions;
                           }

                       },


               ]
           });
           table.on( 'order.dt search.dt', function () {
               rowlength = table.rows().count();
               table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                   var cell_data = (i+1)+' of '+rowlength;
                   cell.innerHTML = cell_data;
           } );
       } ).draw();
}
 /*Delete assigned candidate from multishift-Start  */
  $('#dynamic-table').on('click', '.unassign', function (e) {
   var shift_id=$(this).data('id');
   var base_url= "{{route('multifill.destroy',':id' )}}";
    var url = base_url.replace(':id', shift_id);

         swal({
               title: "Are you sure?",
               text: "You want to unassign the employee?",
               type: "warning",
               showCancelButton: true,
               confirmButtonClass: "btn-danger",
               confirmButtonText: "Unassign",
               showLoaderOnConfirm: true,
               closeOnConfirm: false
           },function(unassign) {
               if (unassign){
                   $.ajax({
                   url: url,
                           type: 'GET',
                           success: function (data) {
                           if (data.success) {
                               swal("Unassigned", "Employee has been unassigned", "success");
                               var table = $('#dynamic-table').DataTable();
                               table.ajax.reload();
                           } else {
                               swal("Alert", "Employee not assigned to this customer", "warning");
                           }
                           },
                           error: function (xhr, textStatus, thrownError) {
                               //alert(xhr.status);
                               //alert(thrownError);
                            //    console.log(xhr.status);
                            //    console.log(thrownError);
                               swal("Oops", "Something went wrong", "warning");
                           },
                   });
               };
           });
       });
/*Delete assigned candidate from multishift-End  */

/*Delete assigned candidate from multishift-Start  */
$('#dynamic-table').on('click', '.unassignparentchange', function (e) {
    var shift_id=$(this).data('id');
    var parent_id=$(this).attr('attr-parentid');
  
    var base_url= "{{route('multifill.destroy',':id' )}}";
    var url = base_url.replace(':id', shift_id);

            swal({
                title: "Are you sure?",
                text: "You want to unassign the employee?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Unassign",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },function(unassign) {
                if (unassign){
                    $.ajax({
                        type: "post",
                        url: "{{route("multifill.changeparent")}}",
                        data: {id:shift_id,parentId:parent_id},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            let data=jQuery.parseJSON(response)
                            if (data.success) {
                                $.ajax({
                                url: url,
                                        type: 'GET',
                                        success: function (data) {
                                        if (data.success) {
                                            swal("Unassigned", "Employee has been unassigned", "success");
                                            var table = $('#dynamic-table').DataTable();
                                            table.ajax.reload();
                                        } else {
                                            swal("Alert", "Employee not assigned to this customer", "warning");
                                        }
                                        },
                                        error: function (xhr, textStatus, thrownError) {
                                            //alert(xhr.status);
                                            //alert(thrownError);
                                            //    console.log(xhr.status);
                                            //    console.log(thrownError);
                                            swal("Oops", "Something went wrong", "warning");
                                        },
                                });
                            } else {
                                swal("Alert", "Employee not assigned to this customer", "warning");
                            } 
                        }
                    });

                    // $.ajax({
                    // url: url,
                    //         type: 'GET',
                    //         success: function (data) {
                    //         if (data.success) {
                    //             swal("Unassigned", "Employee has been unassigned", "success");
                    //             var table = $('#dynamic-table').DataTable();
                    //             table.ajax.reload();
                    //         } else {
                    //             swal("Alert", "Employee not assigned to this customer", "warning");
                    //         }
                    //         },
                    //         error: function (xhr, textStatus, thrownError) {
                    //             //alert(xhr.status);
                    //             //alert(thrownError);
                    //             //    console.log(xhr.status);
                    //             //    console.log(thrownError);
                    //             swal("Oops", "Something went wrong", "warning");
                    //         },
                    // });
                };
            });
        });
/*Delete assigned candidate from multishift-End  */

 /*Delete shift-Start  */
  $('#dynamic-table').on('click', '.delete', function (e) {
   var shift_id=$(this).data('id');
   var base_url= "{{route('multifill.delete-shift',':id' )}}";
    var url = base_url.replace(':id', shift_id);

         swal({
               title: "Are you sure?",
               text: "You want to delete the shift?",
               type: "warning",
               showCancelButton: true,
               confirmButtonClass: "btn-danger",
               confirmButtonText: "Delete",
               showLoaderOnConfirm: true,
               closeOnConfirm: false
           },function(deleteShift) {
               if (deleteShift){
                   $.ajax({
                   url: url,
                           type: 'GET',
                           success: function (data) {
                           if (data.success) {
                               if(data.parent_id != "") {
                                $('.body_tr_' +data.parent_id + ' td:eq(9)').append('<a  title="Delete Shift"  class="delete fa fa-trash pointer" data-id=' + data.parent_id + '></a>');
                               }
                               $('.body_tr_' +shift_id).remove();
                               let total_no_of_shifts = parseInt($('#total_no_of_shifts').val());
                               if(total_no_of_shifts > 0) {
                                    $('#total_no_of_shifts').val((total_no_of_shifts - 1));
                               }else{
                                $('#total_no_of_shifts').val(0);
                               }
                               swal("Deleted", data.msg, "success");
                               var table = $('#dynamic-table').DataTable();
                               table.ajax.reload();
                           }else{
                            swal("Error", data.msg, "error");
                           }
                           },
                           error: function (xhr, textStatus, thrownError) {
                               //alert(xhr.status);
                               //alert(thrownError);
                            //    console.log(xhr.status);
                            //    console.log(thrownError);
                               swal("Oops", "Something went wrong", "warning");
                           },
                   });
               };
           });
       });
/*Delete shift-End  */



function transformData(str) {

 var frags = str.split('_');
if(frags.length==2)
{

  frags[0] = frags[0].charAt(0).toUpperCase() + frags[0].slice(1);
}
else
{
 for (i=0; i<frags.length; i++) {
   frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
 }
}
 return frags.join(' ');
}


   var cust_id = Number('{{ (int)$customer_id }}');
   var req_id = Number('{{ (int)$requirement_id }}');
   var customer_contract_type = Number('{{ $customer_contract_type }}');
   security_clearence_id = Number('{{ $security_clearence_id }}');
   var already_contacted_candidates = [];
   $(function () {
       $('#customer_id select[name="customer_id"]').select2(); //Added Select2 to project listing
       $('#shift_timing_id input:checkbox').prop('checked',false); //Remove checked property on load
       $(".gj-calendar").css("z-index", 2000); //Calendar z-index
         $("#secClearance").prop('checked', true);
       try {
           arr_schedule = jQuery.parseJSON('{!! json_encode($arr_schedule) !!}');
           var employee_id_array =[];
           employee_id_array = jQuery.parseJSON('{!! json_encode($employee_id_array) !!}');
           var array_candidate = [];
              var requirement = [];
              var day=0;
              var shift=0;
              var dateval=0;
              var checked = [];
              var value_exist=true;
           var security_clearance_check=($("select[name ='type']").val() == 4 && $("select[name ='require_security_clearance']").val()=="yes")?1:'';
           $(".schedule-map-view").hide();
          // $('#map_view_div').hide();
           $(document).on('click', '.schedule-data-selector', function () {
           security_clearance_check=($("select[name ='type']").val() == 4 && $("select[name ='require_security_clearance']").val()=="yes")?1:'';
               $("#secClearance").prop('checked', true);
               $('.schedule-data-selector').closest('tr').css('background-color','')
               $(this).closest('table').find('td').css("color","rgb(0, 58, 99)")
               $('.schedule-data-selector').removeClass('active');
               $(this).addClass('active');
               $(this).closest('tr').css('background-color','#548235')
               $(this).closest('tr').find('td').css("color","white")
               requirement_id = $('#requirement_id_hidden').val();
               $selected_days = $(this).data('days');
               $selected_shifts = $(this).data('shifts');
               $selected_level = $(this).data('level');
                dateval = $(this).data('dateval');
               security_clearence_id=$selected_level;
               day = $(this).data('days');
               shift = $(this).data('shifts');
               security_clearence_id=$selected_level;
               securityclearnce_enabled=$('#security_clearence_require').val();
               security_clearance_check=(securityclearnce_enabled=='yes')?1:0;
                checked = [];
                $("input[name='timecheck[]']:checked").each(function () {
                 checked.push(($(this).val()));
                });
               if($selected_level=='--'||$selected_level==null){ $selected_level='All'}
               $('.schedule-map-view').attr('data-days', day);
               $('.schedule-map-view').attr('data-shifts', shift);
               value_exist=checkValueExistsinObject($selected_days,$selected_shifts,employee_id_array);
               employee_ids=(value_exist)? [employee_id_array[shift][day]]:[];
               requirement=requirement_id;
               table.ajax.reload();
               $('html, body').animate({
                   scrollTop: $("#schedules-table").offset().top
               }, 1000);
           });

           /*get the selected customer and candidates id- Start*/
           $('.schedule-map-view').on('click', function () {
               $('#map_view_submit input[name="employee_id_array"]').val(employee_ids_map);
           });

           /*get the selected customer and candidates id- End*/
           $.fn.dataTable.ext.errMode = 'throw';
           var table = $('#schedules-table').DataTable({

               destroy:true,
               bProcessing: false,
               processing: true,
               serverSide: false,
               fixedHeader: false,
               ajax: {
                   url: "{{ route('schedules.list') }}", // Change this URL to where your json data comes from
                   type: "GET", // This is the default value, could also be POST, or anything you want.
                   data: function (d) {
                     //  d.array_candidate = JSON.stringify(array_candidate);
                        d.requirement_id =requirement;
                        d.day = day;
                        d.shift = shift;
                        d.level = security_clearence_id ? security_clearence_id : 0;
                        d.checkedvalue = checked;
                        d.security_clearance_check= security_clearance_check;
                        d.dateval=dateval?dateval:0;

                   },
                    dataSrc: function ( json ) {
                       var arr=[];
                       $.each(json.data, function(i, item) {
                       arr.push(item.user_id);
                       });
               employee_ids_map=arr;
               if ((employee_ids_map.length > 0)) {
                   $(".schedule-map-view").show();
                   //$('#map_view_div').show();
                } else {
                   //$('#map_view_div').hide();
                   $(".schedule-map-view").hide();
                }
               return json.data;

           },
                   error: function (xhr, textStatus, thrownError) {
                       if (xhr.status === 401) {
                           window.location = "{{ route('login') }}";
                       }
                   }
               },

               createdRow: function (row, data, dataIndex) {
                //    console.log(data)
                   $(row).find('td:eq(4),td:eq(8),td:eq(11),td:eq(12),td:eq(13)').css('text-align','center');
                   if( data.eventlog_status!=0){
                       switch(data.eventlog_status)
                       {
                           case '1':
                           $(row).css('background-color', 'green').addClass('font-color-red');
                           $(row).children().addClass('font-color-green');
                           $(row).find('.candidate-view').addClass('font-color-green')
                           break;
                           case '2':
                           $(row).css('background-color', 'yellow').addClass('font-color-red');
                           $(row).children().addClass('font-color-yellow');
                           $(row).find('.candidate-view').addClass('font-color-yellow');break;
                           case '4':
                           case '3':
                            $(row).css('background-color', 'red').addClass('font-color-red');
                            $(row).children().addClass('font-color-red');
                            $(row).find('.candidate-view').addClass('font-color-red');
                           break;
                       }

                  }

               },
               lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
               columns: [{
                       data: null,
                       name: 'employee.user.name',
                       render: function (row) {
                           let user_name = row.name;
                           actions = '';
                           var url ='#';
                           var title ='Not Available';
                           if(row.candidate_transition != null){
                             url =
                               '{{ route("candidate.view", [":candidate_id",":job_id"]) }}';
                           url = url.replace(':candidate_id', row.candidate_id);
                           url = url.replace(':job_id', ((null!=row.candidate_transition)?row.job_id:''));
                           title ='View';
                           }

                           let status_title='Offline';
                           if(row.live_status_color == 1) {
                                status_title = 'Online';
                           }else if(row.live_status_color == 2) {
                                status_title = 'Meeting';
                           }
                        actions += '<span title="'+status_title+'" class="span-dot live_status_'+row.live_status_color+'"></span>&nbsp;&nbsp;<a title="'+user_name+'" class="candidate-view" title="'+title+'" href="' + url + '">' +
                        ((user_name.length > 10)? user_name.substring(0, 10)+'..':user_name)+ '</a>';
                           return actions;
                       }
                   },
                   {
                       data: 'employee_address',
                       name: 'employee_address'
                   },
                   {
                       data: 'employee_city',
                       name: 'employee_city'
                   },
                   {
                       data: 'employee_postal_code',
                       name: 'employee_postal_code'
                   },
                   {
                       data: 'years_of_security',
                       name: 'years_of_security'
                   },
                   {
                       data: 'security_clearance_user',
                       name: 'security_clearance_user',
                   },
                   {
                       data: null,
                       name: 'being_canada_since',
                       render: function (data) {
                           return (data.being_canada_since!=null)? moment(data.being_canada_since).format('MMM D, Y'):"--";
                       }
                   },
                   {
                       data: null,
                       name: 'wage_expectations_from',
                       render: function (data) {
                         if(data.wage_expectations_from != null && data.wage_expectations_to != null){
                           return '$' + (parseFloat(data.wage_expectations_from).toFixed(2))+'-$' + (parseFloat(data.wage_expectations_to)).toFixed(2)
                         }else{
                           return '--';
                        }

                     }
                   },
                   /*{
                       data: 'wageexpectation.wage_expectations_to',
                       name: 'wageexpectation.wage_expectations_to',
                       render: function (wage_expectations_to) {
                           return '$' + parseFloat(wage_expectations_to).toFixed(2)
                       }
                   },*/
                   {
                       data: 'prev_attempt',
                       name: 'prev_attempt',
                   },
                   {
                       data: 'phone',
                       name: 'phone'
                   },
                   {
                       data: 'email',
                       name: 'email'
                   },
                   {
                       data: null,
                       sortable: false,
                       render: function (row) {
                           actions =
                               '<a title="Select a project and/or add requirement" href="#" class="fa fa-ban fa-inverse"></a>&nbsp;';
                           customer_id = $('#customer_id select[name="customer_id"] option:selected').val();
                           requirement_id = $('#requirement_id_hidden').val();
                           shift_id = $('.schedule-data-selector.active').data('shifts');
                            new_shift_id = $('.schedule-data-selector.active').data('shift-id');
                          if(shift_id==null){ shift_id=0;}

                           if (customer_id && requirement_id) {
                               var url ='{{ route("candidate.eventLog",[":requirement_id",":new_shift_id",":user_id"])}}';
                               url = url.replace(':requirement_id', requirement_id);
                               url = url.replace(':shift_id', new_shift_id);
                               url = url.replace(':user_id', row.user_id);
                            //    actions = '<a onclick="gatewayCheckEventLog(' + requirement_id + ',' + customer_id + ',' + new_shift_id + ',' + row.user_id + ')" title="Event Log"  href="javascript:;" class="fa fa-calendar" id="event-log"></a>&nbsp;';
                               actions = '<a onclick="assignToEmployee(' + requirement_id + ',' + customer_id + ',' + new_shift_id + ',' + row.user_id + ')" title="Event Log"  href="javascript:;" class="fa fa-calendar" id="event-log"></a>&nbsp;';
                           }
                           return actions;
                       }
                   },
                   {
                       data: 'avg_score',
                       name: 'avg_score',
                       render: function (avg_score) {
                           return Number(avg_score) + '%';
                       }
                   },
                   {
                       data: null,
                       render: function (row) {
                            requirement_id = $('#requirement_id_hidden').val();
                           if(row.unavailability_set==1)
                            return '<a onclick="openScheduleWindow('+row.user_id+')" title="Schedule View" href="javascript:;" class="fa fa-calendar-o" id="stc-schedule-view"></a>'
                            +'&nbsp;&nbsp;<a href="#" class="popoverButton fa fa-info-circle fa-3x" data-id=' + row.user_id + ' data-requirement-id='+requirement_id+'></a>';
                        else
                           return '<a onclick="openScheduleWindow('+row.user_id+')" title="Schedule View" href="javascript:;" class="fa fa-calendar-o" id="stc-schedule-view"></a>';

                       }
                   }
               ]
           });
       } catch (e) {
        //    console.log(e.stack);
       }

       $('#schedules-table').on('click', '.popoverButton', function(e){
       e.preventDefault();
       var id = $(this).data('id');
       var requirementid = $(this).data('requirement-id');
       var base_url = "{{route('checkavailability', [':id',':requirementid'])}}";
       var url = base_url.replace(':id', id);
       var url = url.replace(':requirementid', requirementid);
       $.ajax({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           url:url,
           type: 'GET',
           success: function (data) {
              if(data.success){
               $(' #schedules-table a[data-id="'+data.employeeid+'"]').popover({
                      "html": true,
                       trigger: 'focus',
                       placement: 'left',
                      "content": function () {
                      var result="";
                      jQuery.each(data.result , function(index, value){
                      result+= "<div>"+index+" " +value+"</div>";
                       });
                      return result;
                    }
                    });
               $('#schedules-table a[data-id="'+data.employeeid+'"]').popover('toggle');
              }
           },
           fail: function (response) {
               swal("Oops", "Something went wrong", "warning");
           },
           contentType: false,
           processData: false,
       });
   });
       /* populating the matrix with respect to the availabilty - part time or full time */

       $("input[name='timecheck[]']").change(function () {
           $(".schedule-map-view").hide();
           var checked = []
           flag = 0;
           var table = $('#schedules-table').DataTable();
           //clear datatable
           table.clear().draw();
           $("input[name='timecheck[]']:checked").each(function () {
               checked.push(($(this).val()));
           });
           requirement_id = $('#requirement_id_hidden').val();
           if (checked.length == 0) {
               $(this).prop('checked', true);
           } else {
               $.ajax({
                   type: "GET",
                   url: "{{ route('candidate.schedule') }}",
                   data: {
                       checkedvalue: checked,
                       requirement_id: requirement_id,
                       isajax: true,
                       flag: flag
                   },
                   success: function (data) {
                       var new_schedule = JSON.parse(data);
                       $('#candidate-schedule-calender').html($(new_schedule.htmlview).find(
                           '#candidate-schedule-calender').html());
                       arr_schedule = new_schedule.arr_schedule
                       employee_id_array = new_schedule.employee_id_array
                   },

               });
           }
       });




        /* populating the eventlog table with security clerance data if checkbox is checked-Start  */
       $("body").on("change", "#secClearance", function () {
           var value_exist=true;
         if ($(this).is(':checked')){

               $selected_level = $('.schedule-data-selector.active').data('level');
               security_clearance_check=1;
           }
           else
           {
               $selected_level = 0;
                security_clearance_check=1;security_clearance_check=0;
           }
            $selected_days = $('.schedule-data-selector.active').data('days');
            $selected_shifts = $('.schedule-data-selector.active').data('shifts');
            value_exist=checkValueExistsinObject($selected_days,$selected_shifts,employee_id_array);
            employee_ids=(value_exist)?[employee_id_array[$selected_shifts][$selected_days]]:[];
           table.ajax.reload();


       });
          /* populating the eventlog table with security clerance data if checkbox is checked-End  */


       /*STC Save - Start*/
       $('#stc-customer-form').submit(function (e) {
           e.preventDefault();
           var $form = $(this);
           $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
           url = "{{ route('stc.store') }}";
           var formData = new FormData($('#stc-customer-form')[0]);
           $.ajax({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               url: url,
               type: 'POST',
               data: formData,
               success: function (data) {
                   if (data.success) {
                       swal("Created", "New STC project created successfully", "success");
                       $("#myModal").modal('hide');
                       $('#stc-customer-form').trigger('reset');
                       $('.modal-backdrop').remove();
                       var url = '{{ route("candidate.stcprojectlist",":stc") }}';
                       url = url.replace(':stc', 1)
                       getProjectlistAjax(url);
                       $("#security_clearance_lookup_id").hide();
                   } else {
                       swal("Oops", "STC project creation was unsuccessful", "warning");
                   }
               },
               fail: function (response) {
                   swal("Oops", "Something went wrong", "warning");
               },
               error: function (xhr, textStatus, thrownError) {
                   associate_errors(xhr.responseJSON.errors, $form);
               },
               contentType: false,
               processData: false,
           });
       });
       /*STC Save - End*/

       /*List project number based on project type(Permanent or STC) Ajax - Start*/
       $(function () {
            // $('#new-stc-button').hide();
            $('#nmso_account_details').show();
            $('#site_note').show();
            $('#stc-security-cearance').show();
            $('#schedule-shift').hide();
            $("select[name='type'] option[value!={{config('globals.multiple_fill_id')}}]").hide();
            $("select[name='type'] option[value={{config('globals.multiple_fill_id')}}]").show();
            $("select[name='type']").val("{{config('globals.multiple_fill_id')}}");
            $('#project-no-new-stc').show();
            $('#customer_id select[name="customer_id"]').prop('selectedIndex', 0);
            $('#posting-detail-validation').text('');
            clearField();
            $("select[name='type']").val("{{config('globals.multiple_fill_id')}}").trigger('change');
            $('.assignment_type_class').attr('style', 'display: none;');
            $("select[name='fill_type'] option[value!={{config('globals.multiple_fill_id')}}]").show();
            $("select[name='fill_type'] option[value={{config('globals.multiple_fill_id')}}]").hide();
            $('.fill_type_class').attr('style', 'display: block;');
            $("select[name='fill_type']").prop('required',true);
            var url = '{{ route("candidate.stcprojectlist") }}';
            getProjectlistAjax(url);
       });

       /*Display the customer details on the readonly fields based on project number chosen - Start*/
       $('#project-no-new-stc').find('select[name="customer_id"]').on('select2:select', function (e) {
            if($('#short-term-contract').is(':checked')) {
                $("#assignment_type").val("{{config('globals.multiple_fill_id')}}").trigger('change');
            }

           id = this.value;
           var edit_url = '{{ route("stc.edit", ":id") }}';
           edit_url = edit_url.replace(':id', id);
           $('#sitenote'). attr("href",edit_url );
           $('#map_view_submit input[name="selected_project_no"]').val(id);
           if (id) {
               $('#project-no-details').show();
               url = "{{ route('schedule.getCustomer') }}";
               $.ajax({
                   headers: {
                       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   },
                   url: url,
                   type: 'GET',
                   data: {
                       id: id
                   },
                   success: function (data) {
                       if (data.success) {
                           $('#project-no-details input[name="customer_id"]').val(data.data.id);
                           $('#project-no-details input[name="client_name"]').val(data.data.client_name);
                           $('#project-no-details input[name="site_city"]').val(data.data.city);
                           $('#project-no-details input[name="site_address"]').val(data.data.address);
                           $('#project-no-details input[name="site_postal_code"]').val(data.data.postal_code);
                           $('#postingdetails input[name="customer-contract-type"]').val(data.data.stc);
                           if(data.data.stc_details != null){
                               $('#project-no-details input[name="customer_nmso_account"]').val(data.data.stc_details.nmso_account);
                                $('#project-no-details textarea[name="site_note"]').val(data.data.stc_details.job_description);
                               if(data.data.stc_details.nmso_account == 'yes'){
                                   $('.customer_security_clearance').show();
                               }else if(data.data.stc_details.nmso_account == 'no'){
                                   $('.customer_security_clearance').hide();
                               }else{
                                   $('#project-no-details input[name="customer_nmso_account"]').val('Value Not Updated');
                                   $('.customer_security_clearance').hide();
                               }
                               if(data.data.stc_details.security_clearance != null){
                                   $('#project-no-details input[name="customer_security_clearance_lookup_id"]').val(data.data.stc_details.security_clearance.security_clearance);
                               }
                           }
                           $('html, body').animate({
                               scrollTop: $("#requirements").offset().top
                           }, 1000);
                       } else {
                           console.log(data);
                       }
                   },
                   fail: function (response) {
                       console.log(response);
                   },
               }).done(function(e){

               });
           } else {
               $('#project-no-details').hide();
           }
       });

       /*Display the customer details on the readonly fields based on project number chosen - End*/
      $('#types-table').hide();
        $('#assignment_type').on('change', function () {
     if($(this).find(":selected").val()== {{config('globals.multiple_fill_id')}}) //To find whether multiplefill is choosed
       {
           $('#schedule-grid').hide();
           //$('#map_view_div').hide();
           $(".schedule-map-view").hide();
       }
       else
       {
           $('#schedule-grid').show();
           //$('#map_view_div').show();
           $(".schedule-map-view").show();
           $('#types-table').hide();

       }
   });

       /*Schedule Customer Requirements - Save - Start*/
       $('#schedule-customer-requirements-form').submit(function (e) {
           e.preventDefault();
           var $form = $(this);
           $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
           url = "{{ route('schedule.requirements') }}";
           $("#assignment_type").prop('disabled', false);
           var formData = new FormData($('#schedule-customer-requirements-form')[0]);
           $.ajax({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               url: url,
               type: 'POST',
               data: formData,
               success: function (data) {
                   if (data.success) {
                       table.ajax.reload();
                       $("#requirement_id_hidden").val(data.result.id);
                       $('#requirements-button').hide();
                       $('.content-holder').find('input[type="text"],input[type="number"],input[type="radio"],input[type="checkbox"],textarea,select').prop('disabled', true);
                       $('.gj-icon').hide();
                       $('html, body').animate({
                           scrollTop: $("#schedules-table").offset().top
                       }, 1000);
                       var url = "{{ route('candidate.schedule',[':customer_id',':requirement_id',':customer_contract_type',':security_clearence_id']) }}";
                       url = url.replace(':customer_id', data.result.customer_id);
                       url = url.replace(':requirement_id', data.result.id);
                       url = url.replace(':customer_contract_type', data.result.customer.stc);
                       url = url.replace(':security_clearence_id', data.result.security_clearance_level);
                       if($("#timeoff_requestid").val()>0){
                           var timeoffid = $("#timeoff_requestid").val();
                           var reqid = data.result.id;
                           $.ajax({
                                       type: "post",
                                       url: "{{route('schedule.requirements.updatetimeoff')}}",
                                        data: {"timeoff_id":timeoffid,"requirement_id":reqid},
                                       headers: {
                                           'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                       },
                                       success: function(response) {
                                           window.history.pushState('page2', 'Title', url);
                                           window.location.reload();
                                       }
                                   }).done(function(ev) {

                                   });

                       }else{
                           window.history.pushState('page2', 'Title', url);
                               window.location.reload();
                       }
                      // console.log('inside');

                       if($('#assignment_type').val()=={{config('globals.multiple_fill_id')}}){
                             if($('#security_clearence_require').val()!="no"){
                       $(".checkboxs").removeClass('hide-this-block');

                   }
                       prepareDatatable(data.result.id);
                       $('#secClearance').val(data.result.id)
                   }

                   } else {
                       swal("Oops", "Requirements was not saved", "warning");
                       if($("#assignment_type").val()==2)
                           {
                               $("#assignment_type").prop('disabled', true);
                           }
                   }
               },
               fail: function (response) {
                   swal("Oops", "Something went wrong", "error");
                   if($("#assignment_type").val()==2)
                   {
                       $("#assignment_type").prop('disabled', true);
                   }
               },
               error: function (xhr, textStatus, thrownError) {
                   associate_errors(xhr.responseJSON.errors, $form, true);
                   if($("#assignment_type").val()==2)
                   {
                       $("#assignment_type").prop('disabled', true);
                   }

                   console.log(xhr.responseJSON.errors.overtime_notes);
                   if(xhr.responseJSON.errors.overtime_notes != undefined){
                      $('#overtimeNotesModal').modal('show');
                      $('#overtime_notes textarea').val('');
                      $('#overtime_validation').hide();
                      $('#overtime_validation').text(xhr.responseJSON.errors.overtime_notes[0]);
                      /* For overtime notes shift timing labels display - Start */
                      if(xhr.responseJSON.errors.overtime_shift_timing_id != undefined){
                           var shift_timing = xhr.responseJSON.errors.overtime_shift_timing_id[0].replace(/,/g,', ');
                           console.log(shift_timing);
                           $('#overtimeNotesModal label').text('Overtime Notes for '+sentenceCase(shift_timing)+' shift timing');
                      }
                      /* For overtime notes shift timing labels display - End */
                   }
               },
               contentType: false,
               processData: false,
           });
       });
       /*Schedule Customer Requirements - Save - End*/
       var expirytime_control=` <input type="text" class="form-control expiry_time" id="expiry_time" name="expiry_time" 
                value="{{old('expiry_time')}}" 
                    placeholder="Expiry Time" >`;
       /* Populating customer and requirement details - Start */
       if (cust_id && req_id) {
           $("#requirement_id_hidden").val(req_id);
           $('#postingdetails input[name="customer-contract-type"]').val(customer_contract_type);
           $('#requirements-button').hide();
           $('#new-stc-button').show();
           $("#requirements").find('input,textarea,select').prop('disabled', true);
           $('.schedule-requirement-row').show();

           $('.gj-icon').hide();
           req_url = '{{ route("candidate.scheduleRequirementDetails", ":id") }}';
           requirement_url = req_url.replace(':id', req_id);
           $.ajax({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               url: requirement_url,
               type: 'GET',
               success: function (data) {
                   if (data.success) {
                       $.each(data.data.event_logs, function (key, value) {
                           already_contacted_candidates.push(value.user_id);
                       });

                       $('#project-no-details select[name="type"]').append($('<option>', {
                           value: data.data.type,
                           text: data.data.trashed_assignment_type.type,
                           selected: true
                       }));

                       /* Assignment type - Multiple fill edit portion - Start */
                       if(data.data.type == {{config('globals.multiple_fill_id')}}){
                           $('#project-no-details #total-shift-timing').show();
                           $('#project-no-details input[name="no_of_shifts"]').val(data.data.no_of_shifts);
                           if(data.data.overtime_notes != null){
                               $('#project-no-details #maximum-hours-notes').show();
                               $('#project-no-details textarea[name="overtime_hours_notes"]').val(data.data.overtime_notes);
                           }
                           var uniqueNames = [];
                            var uniqueNames_arr = [];
                           $.each(data.data.multifill, function(i, el){
                            //    if($.inArray(el.shift_timing_id, uniqueNames_arr) === -1)
                                   uniqueNames.push(el);
                                   uniqueNames_arr.push(el.shift_timing_id)
                           });

                           if(uniqueNames != null){
                               $('#shift-timing-table').show();
                               $.each(uniqueNames, function(key,value){
                                   var splitStr_from = value.shift_from.split(" ");
                                   var splitStr_to = value.shift_to.split(" ");
                                   var splitStr_start_date = value.shift_from;
                                   $("#shift_timing_id input[value='" + value.shift_timing_id + "']").prop('checked', true);
                                   $('#shift-timing-table tbody').append("<tr class='body_tr_"+value.id+"'><td>"+sentenceCase(value.shift_timing.shift_name)+"</td><td><input type='text' class='form-control' value='"+moment(splitStr_start_date).format("D/M/Y")+"' readonly></td><td><input type='text' class='form-control timepicker' placeholder='From (HH:MM AM/PM)' id='from' value='"+splitStr_from[1]+"' readonly></td><td><input type='text' class='form-control timepicker' placeholder='To (HH:MM AM/PM)' id='to' value='"+splitStr_to[1]+"' readonly></td></tr>");
                                   $('.shift-timing-table-multi-fill-positions').hide();
                                   $('#schedule-grid').hide();
                                  // $('#map_view_div').hide();
                                   $(".schedule-map-view").hide();
                               });
                           }


if(data.data.require_security_clearance!="no"){
                         $('.checkboxs').removeClass('hide-this-block')
                       }

                                   prepareDatatable(data.data.id);
                                    $("#types-table tbody tr").on('click',function(event) {
                                       alert('this')
       $("#dynamic-table tbody tr").removeClass('row_selected');
       $(this).addClass('row_selected');
   });

                                   $('#secClearance').val(data.data.id)
                       }
                       else
                           {
                               $('#types-table').hide();
                           }
                       /* Assignment type - Multiple fill edit portion - End */

                       $('#project-no-details input[name="site_rate"]').val(data.data.site_rate);
                       $('#project-no-details input[name="start_date"]').val(data.data.start_date);
                       $('#project-no-details input[name="end_date"]').val(data.data.end_date);
                       if(data.data.expiry_date!=null){
                            $('#project-no-details input[name="expiry_date"]').val((data.data.expiry_date).split(" ")[0]);
                            let timeArray=(data.data.expiry_date).split(" ")[1].split(":");
                            let meredian="AM"
                            let hour=timeArray[0];
                            // if(timeArray[0]>11){
                            //     meredian="PM"
                            //     hour=timeArray[0]-12;
                            // }
                            var value = hour+":"+timeArray[1];

                            // expirytime_control
                            $('#project-no-details input[name="expiry_time"]').remove()
                            setTimeout(() => {
                                $("#expiry_time").html(expirytime_control).after(function(){
                                    $('input[name="expiry_time"]').val(value).timepicki({show_meridian:false,min_hour_value:0,
		max_hour_value:23,start_time: [hour, timeArray[1]]})
                                })
                            }, 2000);
                       }
                       $('#project-no-details input[name="end_date"]').attr('value', data.data.end_date);
                       $('#project-no-details input[name="length_of_shift"]').val(data.data.length_of_shift);
                       $('#project-no-details textarea[name="notes"]').val(data.data.notes);
                       $('#project-no-details input[name="time_scheduled"]').val(data.data.time_scheduled);
                       $('#project-no-details select[name="fill_type"]').val(data.data.fill_type);
                       if(data.data.require_security_clearance != null){
                           $('#project-no-details select[name="require_security_clearance"]').val(data.data.require_security_clearance);
                           if(data.data.require_security_clearance == 'yes'){
                               $('.security_clearance_level').show();
                               $('#project-no-details select[name="security_clearance_level"]').val(data.data.security_clearance_level);
                           }else if(data.data.require_security_clearance == 'no'){
                               $('.security_clearance_level').hide();
                           }else{
                               $('#project-no-details select[name="security_clearance_level"]').prop('selectedIndex',0);
                               $('.security_clearance_level').hide();
                           }
                       }
                       else{
                           $('#project-no-details select[name="security_clearance_level"]').prop('selectedIndex',0);
                           $('.security_clearance_level').hide();
                       }
                   } else {
                       swal("Oops", "STC project creation was unsuccessful", "warning");
                   }
               },
           }).done(function(data){
                $('input[name=expiry_time]').prop("disabled",false).timepicki({show_meridian:false,min_hour_value:0,
		max_hour_value:23});
               let expiry_date=$(".expiry_date").val();
               $(".expiry_date").remove();

               setTimeout(() => {
                   let expiryControl=`<input name="expiry_date" class="form-control expiry_date" value="${expiry_date}" />`
                    $("#expiry_date").html(expiryControl)

                        $(".expiry_date").mask("9999-99-99").datepicker({
                            format: 'yyyy-mm-dd',
                            showRightIcon: false
                        })




               }, 1000);

           });
       }else{
        $('.security_clearance_level').hide();
       }
       /* Populating customer and requirement details - End */

   });

   $(document).on("click","#schedule-requirement-update",function(e){
       e.preventDefault();
       $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('schedule.update') }}", // Change this URL to where your json data comes from
            type: "post", // This is the default value, could also be POST, or anything you want.
            data: {reqId:$("#requirement_id_hidden").val(),expiry_date:$("input[name=expiry_date]").val(),expiry_time:$("input[name=expiry_time]").val()},
            success: function (response) {
                let data= jQuery.parseJSON(response);
                if(data.code==200){
                    swal({
                        title: "Success",
                        text: "Updated Successfully",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'OK',
                        cancelButtonText: "No, cancel it!"
                    },
                        function(isConfirm){

                        if (isConfirm){
                            location.reload()
                            } 
                        });
                }else{
                    swal("Warning","Not Updated","warning")

                }
            }
       });
   })
   function getProjectlistAjax(url) {
       $.ajax({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           url: url,
           type: 'GET',
           success: function (data) {
               if (data.success) {
                   $('#stc-customer-form').trigger('reset');
                   $('#customer_id select[name="customer_id"]').children('option:not(:first)').remove();
                   $.each(data.data.projectlist, function (index, value) {
                       $('#customer_id select[name="customer_id"]').append('<option value=' +value.id + '>' + value.project_no +'</option>');
                   });
                   if (cust_id && req_id) {
                       $('#project-no-new-stc').find('select[name=customer_id]').val(cust_id).prop('disabled', true);
                       $('#project-no-new-stc').find('select[name=customer_id]').trigger('select2:select');
                   }
               } else {
                //    console.log(data);
               }
           },
           fail: function (response) {
            //    console.log(response);
           },
       });
   }
   /*List project number based on project type(Permanent or STC) Ajax - End*/

   /* Show/Hide security clearance div based on NMSO account value - Start*/
   $('#nmso_account select').on('change', function () {
       if (this.value == 'yes') {
           $("#security_clearance_lookup_id").show();
       } else {
           $('#project-no-details input[name="customer_security_clearance_lookup_id"]').val('');
           $("#security_clearance_lookup_id").hide();
       }
   });
   /* Show/Hide security clearance div based on NMSO account value - End*/

   /* Show/Hide security clearance level div based on required clearance value - Start*/
   $('#require_security_clearance select').on('change', function () {
       if (this.value == 'yes') {
           $(".security_clearance_level").show();
       } else {
           $('#project-no-details select[name="security_clearance_level"]').prop('selectedIndex',0);
           $(".security_clearance_level").hide();
           $(".security_clearance_level").find('span').text('');
       }
   });
   /* Show/Hide security clearance level div based on required clearance value - End*/

   /*New STC Project - button click function - Start*/
   function addnew() {
       window.location = "{{ route('candidate.schedule') }}";
   }
   /*New STC Project - button click function - End*/

   function assignToEmployee(requirement_id, customer_id, multiple_shift_id, user_id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('openshift.shift-availability') }}",
                type: 'GET',
                data: {"multiple_shift_id":multiple_shift_id, 'user_id':user_id, 'customer_id':customer_id, 'requirement_id':requirement_id},
                success: function (data) {
                    if(data.success) {
                        gatewayCheckEventLog(data.requirement_id, data.customer_id,data.multiple_shift_id,user_id,[]);
                    }else if(data.alreadyAssigned){
                        swal('Warning',"Already assigned", 'warning');
                    }else if(data.msg){
                        swal("Error", data.msg,"error");
                    }
                }
            });
        }

   /**
   To check if this candidate is already contacted for the same requirment
   **/
   function gatewayCheckEventLog(requirement_id, customer_id,shift_id,user_id) {
       var url = '{{ route("candidate.eventLog",[":requirement_id",":shift_id",":user_id"])}}';
       url = url.replace(':requirement_id', requirement_id);
       url = url.replace(':shift_id', shift_id);
       url = url.replace(':user_id', user_id);
       if ($.inArray(user_id, already_contacted_candidates) != -1) {
           swal({
               title: "Already contacted",
               text: "Already contacted the candidate for the same requirement. Do you want to call this person again?",
               type: "info",
               showCancelButton: true,
               //confirmButtonClass: "btn-success",
               confirmButtonText: "Yes",
               showLoaderOnConfirm: true,
               closeOnConfirm: false
           }, function () {
               window.location = url;
           });
       } else {
           window.location = url;
       }
   }

   function clearField() {
       $('#project-no-details').find(':input[type=text], :input[type="number"], textarea, select').val('');
       $('#project-no-details').find('input:checkbox').prop('checked',false);
   }

   $('#resetbutton').on('click', function() {
       let customerId = $('#project-no-new-stc').find('select[name=customer_id]').val();
       $('#schedule-customer-requirements-form')[0].reset();
       $('#shift-timing-table tbody').html('');
       $('#shift-timing-table').css('display','none');
       $('#project-no-new-stc').find('select[name=customer_id]').val(customerId).trigger('select2:select');
       $('#assignment_type').val("{{config('globals.multiple_fill_id')}}");
   });
   /**
       Function to prepare multiple fill shift timings
    **/
   function prepareShiftTimingTable(shift_timing_id, shift_timing, shift_timing_index, shift_timing_from, shift_timing_to){
       var htmlData = "<tr id='"+shift_timing_id+"'>";
       htmlData += "<td>"+sentenceCase(shift_timing)+"<span class='mandatory'>*</span></td>";
       htmlData += "<td><div class='form-group' id='shift_from_"+shift_timing_index+"'>";
       htmlData += "<input type='text' class='form-control timepicker' name='shift_from_"+shift_timing_index+"' placeholder='From (HH:MM AM/PM)' id='from' value='"+shift_timing_from+"'>";
       htmlData += "<small class='help-block'></small>";
       htmlData += "</div></td><td>";
       htmlData += "<div class='form-group' id='shift_to_"+shift_timing_index+"'>";
       htmlData += "<input type='text' class='form-control timepicker' name='shift_to_"+shift_timing_index+"' placeholder='To (HH:MM AM/PM)' id='to' value='"+shift_timing_to+"'>";
       htmlData += "<small class='help-block'></small>";
       htmlData += "</div></td></tr>";
       return htmlData;
   }

   var getDates = function(startDate, endDate) {
        let current_date = new Date(startDate);
        currentDate = new Date(current_date.getUTCFullYear(), current_date.getUTCMonth(), current_date.getUTCDate(),  current_date.getUTCHours(), current_date.getUTCMinutes(), current_date.getUTCSeconds());
        let end_date = new Date(endDate);
        endDate = new Date(end_date.getUTCFullYear(), end_date.getUTCMonth(), end_date.getUTCDate(),  end_date.getUTCHours(), end_date.getUTCMinutes(), end_date.getUTCSeconds());
        var dates = [], addDays = function(days) {
            var date = new Date(this.valueOf());
            date.setDate(date.getDate() + days);
            return date;
        };
        while (currentDate <= endDate) {
            dates.push(currentDate);
            currentDate = addDays.call(currentDate, 1);
        }
        return dates;
    };



   function prepareShiftTimingTableforMultiFill(shift_timing_id, shift_timing, shift_timing_index, shift_timing_from, shift_timing_to){
       var start_date = $('.start_date').val();
       var end_date = $('.end_date').val();

       if(start_date != "" && end_date != "") {
            var dates = getDates(start_date, end_date);

            if(dates.length > 0) {
                    var htmlData = '';
                    dates.forEach(function(date) {
                        let dateStr = moment(date).format("DD_MM_Y");
                        htmlData += "<tr class='"+shift_timing_id+"'>";
                        htmlData += "<td>"+sentenceCase(shift_timing)+"<span class='mandatory'>*</span></td>";
                        htmlData += "<td><input type='text' class='form-control' name='shift_date_"+shift_timing_index+"_"+dateStr+"' value='"+moment(date).format("D/M/Y")+"' readonly></td>";
                        htmlData += "<td><div class='form-group' id='shift_from_"+shift_timing_index+"_"+dateStr+"'>";
                        htmlData += "<input type='text' class='form-control timepicker' required name='shift_from_"+shift_timing_index+"_"+dateStr+"' placeholder='From (HH:MM AM/PM)' id='from' value='"+shift_timing_from+"'>";
                        htmlData += "<small class='help-block'></small>";
                        htmlData += "</div></td><td>";
                        htmlData += "<div class='form-group' id='shift_to_"+shift_timing_index+"_"+dateStr+"'>";
                        htmlData += "<input type='text' class='form-control timepicker' required name='shift_to_"+shift_timing_index+"_"+dateStr+"' placeholder='To (HH:MM AM/PM)' id='to' value='"+shift_timing_to+"'>";
                        htmlData += "<small class='help-block'></small>";
                        htmlData += "</div></td>";
                        htmlData += "<td><input type='number' class='form-control no-of-positions' onkeyup='countTotalShift();' required min='1' name='no_of_positions_"+shift_timing_index+"_"+dateStr+"'></td></tr>";
                    });
                    return htmlData;
            }
       }
       return false;
   }

   /*  Shift timing checkbox onchange - Start*/
   $("input[name='shift_timing_id[]']").change(function() {
       var assignment_type = $('#assignment_type').val();
       var shift_timing = $(this).data("shift-timing");
       var shift_timing_index = $(this).data("shift-timing-index");
       var shift_timing_id = shift_timing.replace(/\s+/g, '-').toLowerCase();
       var shift_timing_from = $(this).data("shift-start-timing");
       var shift_timing_to = $(this).data("shift-end-timing");
       $('#maximum-hours-notes').find('textarea').val('');
       $('#maximum-hours-notes').hide();
       if($('input[name="shift_timing_id[]"]:checked').serialize() === ''){
           $('#shift-timing-table').hide();
       }else{
           $('#shift-timing-table').show();
       }

       var start_date = $('.start_date').val();
       var end_date = $('.end_date').val();

       if(start_date == "" || end_date == "") {
            swal("Warning", "Please enter Start Date and End Date", "warning");
            $('#shift-timing-table').css('display','none');
            return false;
       }

       if(this.checked) {
           if(assignment_type == "{{config('globals.multiple_fill_id')}}") {
                $('#shift-timing-table tbody').append(prepareShiftTimingTableforMultiFill(shift_timing_id, shift_timing, shift_timing_index, shift_timing_from, shift_timing_to));
                $('.' + shift_timing_id +' .timepicker').timepicki();
           }else{
                $('#shift-timing-table tbody').append(prepareShiftTimingTable(shift_timing_id, shift_timing, shift_timing_index, shift_timing_from, shift_timing_to));
                $('#' + shift_timing_id +' .timepicker').timepicki();
                $('tr#' + shift_timing_id +' input.timepicker').on("focus", function() {
                    $('#maximum-hours-notes').find('textarea').val('');
                    $('#maximum-hours-notes').hide();
                });
           }
       }else{
        if(assignment_type == "{{config('globals.multiple_fill_id')}}") {
            $('#shift-timing-table tbody').find('.'+shift_timing_id).remove();
        }else{
            $('#shift-timing-table tbody').find('#'+shift_timing_id).find('input').val('');
            $('#shift-timing-table tbody').find('#'+shift_timing_id).remove();
        }
       }
   });
   /*  Shift timing checkbox onchange - End*/

   $('#start_date, #end_date').on('change', function(){
    $('#shift-timing-table tbody').html('');
    $("input[name='shift_timing_id[]']").prop('checked', false);
   });

   /* List project number based on project type(Permanent or STC) Ajax - Start */
       $('#assignment_type').change(function () {
           if (this.value == {{config('globals.multiple_fill_id')}}) {
               $('#total-shift-timing').show();
           } else {
               $('#total-shift-timing').hide();
               $('#total-shift-timing input:checkbox').prop('checked',false);
               $('#total_no_of_shifts').val('');
               $('#shift-timing-table tbody tr').remove();
               $('#shift-timing-table').hide();
           }
       });
   /* List project number based on project type(Permanent or STC) Ajax - End */


   /*Hide the calendat and title on loading - Start*/
       //$('.responsive-calendar div').hide();
       //$('.table_title').hide();
   /*Hide the calendat and title on loading - End*/

   /* Overtime notes submit - Start */
   $('#overtime_notes_submit').click(function () {
       var notes = $('#overtime_notes textarea').val();
       if(notes === ''){
           $('#overtime_validation').show();
       }else{
           $('#overtime_validation').hide();
           $('#maximum-hours-notes').show();
           $('#maximum-hours-notes textarea').val(notes);
           $("#overtimeNotesModal").modal('hide');
       }
   });
   /* Overtime notes submit - End */
   /* Function for concatinating address and populate in billing address if checkbox is checked - Start */
       $("#check_same_address").click(function() {
           if ($("input[name=address]").val().length <= 0 || $("input[name=city]").val().length <= 0 || $("input[name=province]").val().length <= 0 || $("input[name=postal_code]").val().length <= 0) {
               swal("Warning", "Please enter address details", "warning");
               $(this).prop('checked', false);
           }
           if (this.checked) {
               var address = '';
               var city = '';
               var province = '';
               var postal_code = '';
               if ($("input[name=address]").val().length > 0)
                   var address = $("input[name=address]").val() + ', ';
               if ($("input[name=city]").val().length > 0)
                   var city = $("input[name=city]").val() + ', ';
               if ($("input[name=province]").val().length > 0)
                   var province = $("input[name=province]").val() + ', ';
               var postal_code = $("input[name=postal_code]").val();
               var full_addr = address + city + province + postal_code;
               $('input:text[name="billing_address"]').val(full_addr);
               $('input:text[name="billing_address"]').prop('readonly', true);
           } else {
               $('input:text[name="billing_address"]').val('');
               $('input:text[name="billing_address"]').prop('readonly', false);
           }
       });
       /* Function for concatinating address and populate in billing address if checkbox is checked - End */


   function checkValueExistsinObject($selected_days,$selected_shifts,employee_id_array)
   {
    //    console.log($selected_days,$selected_shifts,employee_id_array)
   var value_exists=false;
   for (var key in employee_id_array) {
   // skip loop if the property is from prototype
   if (!employee_id_array.hasOwnProperty(key)) continue;
   // alert(key);
    if(key==$selected_shifts)
    {
   var obj = employee_id_array[key];
   for (var prop in obj) {
       // skip loop if the property is from prototype
       if (!obj.hasOwnProperty(prop)) continue;
       if(prop== $selected_days)
       {
          value_exists=true;
          break;
       }
       else
       {
         value_exists=false;
       }
   }
}

}    return value_exists;
   }

   var posttrigger = function(e){

       $('.wrapper').loading({
                   stoppable: false,
                   message: 'Please wait...'
               });

        $('#resbutton').attr( 'disabled', true );
       setTimeout(() => {

           var projectid = $("#timeoffcustomer").val();

           $('#customer_id select[name="customer_id"]').val(projectid).select2();

           var data = $('#project-no-new-stc').find('select[name=customer_id]').select2('data');

           $("#customer_id_label").html(data[0].text);
           $('#project-no-new-stc').find('select[name=customer_id]').trigger('select2:select');
           $('#project-no-new-stc').find('select[name=customer_id]').next(".select2-container").hide();

           $('#requirements').find('input[name=site_rate]').val($("#timeoff_payrate").val()).prop("readonly","readonly");
           $('#project-no-details').find('input[id=start_date]').val($("#timeoffstartdate").val()).prop("readonly","readonly");
           $('#project-no-details').find('input[id=start_date]').hide();

           $('#project-no-details').find('input[name=time_scheduled]').val($("#timeoffstarttime").val()).prop("readonly","readonly");
           $('#project-no-details').find('input[id=end_date]').val($("#timeoffenddate").val()).prop("readonly","readonly");
           $('#project-no-details').find('input[id=end_date]').hide();

           var startdate =$('input[name="timeoff_formattedstartdate"]').val();
            var enddate =$('input[name="timeoff_formattedenddate"]').val();
            $("#start_datelabel").html(startdate);
            $("#end_datelabel").html(enddate);
           $('#project-no-details').find('input[id=end_date]').css("visibility","hidden");


           $('#requirements').find('select[id=assignment_type]').val(2);
           $('#requirements').find('select[id=assignment_type]').css('display',"none");
           $('#requirements').find('select[id=assignment_type]').select2();

           $('#assignment_type_label').html("Employee Timeoff");

           if($("#timeoffstartdate").val() == $("#timeoffenddate").val()){


                $('#requirements').find('select[id=assignment_type]').next(".select2-container").hide();
           }else{
                $('#requirements').find('select[id=assignment_type]').next(".select2-container").hide();
           }

           $(".gj-icon").hide();
           if($("#timeoffstarttime").val()!=""){
               $('#project-no-details').find('input[name=time_scheduled]').prop("readonly","readonly").unbind();
               var diff = ( new Date("1970-1-1 " + $("#timeoffendtime").val()) - new Date("1970-1-1 " + $("#timeoffstarttime").val()) )/1000 ;

               var minutes = Math.floor(diff/60);
               seconds = diff % 60;
               var hours = Math.ceil(minutes/60);
               minutes = minutes % 60;

               var timeofflength = hours;

               if(timeofflength < 0){
                var diff = ( new Date("1970-1-2 " + $("#timeoffendtime").val()) - new Date("1970-1-1 " + $("#timeoffstarttime").val()) )/1000 ;

               var minutes = Math.floor(diff/60);
               seconds = diff % 60;
               var hours = Math.ceil(minutes/60);
               minutes = minutes % 60;
               timeofflength = hours
               }
               if(minutes>0){
                timeofflength = timeofflength+"."+minutes
               }
               $('#project-no-details').find('input[name=length_of_shift]').val(timeofflength).prop("readonly","readonly");

           }

                if($("#employeename").val()!=""){
                                $(".empidblock").show();
                                $("#empname").html($("#employeename").val());
                            }else{
                                $(".empidblock").hide();
                            }
           $('.wrapper').loading('stop');
       }, 2500);



   };
   $(function(e){
       if($("#timeoffcustomer").val()>0){
        $('#resbutton').attr( 'disabled', true );
           if($("#timeoffcustomerstc").val()==1){
               $("#short-term-contract").trigger('click').after(function(e1){
                   $('body').loading({
                   stoppable: false,
                   message: 'Please wait...'
               });
                   posttrigger();
               });
           }
           else if($("#timeoffcustomerstc").val()==0){
               $("#permanent").trigger('click').after(function(e1){
                   $('body').loading({
                   stoppable: false,
                   message: 'Please wait...'
               });
                   posttrigger();
               });
           }
       }

       if($("#employeetimeoffreference").val()>0){
        $('#resbutton').attr( 'disabled', true );
               //$('#requirements').find('select[id=assignment_type]').prop("disabled",false);
               // $('#requirements').find('select[id=assignment_type]').val(2);
                //$('#requirements').find('select[id=assignment_type] option:selected').text('Employee Timeoff').val(2);
                $('#requirements').find('select[id=assignment_type]').hide();
                $('#assignment_type_label').html("Employee Timeoff");

                $('#project-no-details').find('input[id=start_date]').hide();
                $('#project-no-details').find('input[id=end_date]').hide();


                setTimeout(() => {
                    var startdate =$('input[name="timeoff_formattedstartdate"]').val();
                    var enddate =$('input[name="timeoff_formattedenddate"]').val();
                    $("#start_datelabel").html(startdate);
                    $("#end_datelabel").html(enddate);
                    if($("#employeename").val()!=""){
                        $(".empidblock").show();
                        $("#empname").html($("#employeename").val());
                    }else{
                        $(".empidblock").hide();
                    }
                }, 1000);

       }
   })

function formatDate(date) {
    var d = new Date(date);
    var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    var today  = new Date(date);
    return today.toLocaleDateString("en-US", options);
}

   function countTotalShift() {
       let no_of_shifts = 0;
       $('.no-of-positions').each(function(i, obj) {
           if($(this).val() != "") {
                no_of_shifts += parseInt($(this).val());
           }
        });
        $('#total_no_of_shifts').val(no_of_shifts);
   }

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

        $(document).ready(function () {
            $('input[name=expiry_time]').prop("disabled",false).timepicki({show_meridian:false,min_hour_value:0,
		max_hour_value:23});
            $('[data-toggle="tooltip"]').tooltip()

        });

</script>
<style type="text/css">
.checkboxs {
   padding-left: 20px;
}
.check-controlline .checkboxs {
   float: left;
   line-height: 16px;
}

/* Style for displaying timepicker inside table - Start */
#shift-timing-table td{
   position:relative;
}
.fa-disabled {
 opacity: 0.6;
 cursor: not-allowed;
}
tr.row_selected td{background-color:red !important;}
/*log event tab content END*/
.schedule-data-selector.active {
   background-color: #548235 !important;
   color: white;
}
.pointer
{
  cursor: pointer;
}
.empidblock{
    display: none;
}


/* Style for displaying timepicker inside table - End */

</style>
