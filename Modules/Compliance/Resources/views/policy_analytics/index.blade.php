@extends('layouts.app')
@section('content')

<div class="table_title">
  <h4>Compliance Module</h4>

  <div class="dropdown" >
                <span class="dropdown-toggle"  id="global_chart_filter_dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                </span>
                <div class="dropdown-menu" aria-labelledby="global_chart_filter_dropdownMenu">
                        <span class="widget-dropdown-item" chart_type="column" > 
                            <i class="fa fa-bar-chart" aria-hidden="true"></i>
                        </span>
                        <span class="widget-dropdown-item" chart_type="pie" >
                            <i class="fa fa-pie-chart" aria-hidden="true"></i>
                        </span>
                        <span class="widget-dropdown-item" chart_type="donut" >
                            <i class="fa fa-stop-circle" aria-hidden="true"></i>
                        </span>
                </div>
        </div>
</div>

<div class="container">
<div class="row">


    <div class="col-md-4 col-xs-4 col-sm-4 col-lg-4 card">
        {{-- Dropdown menu for compliance chart - Begin  --}}
        <div class="dropdown ">
            <span class="dropdown-toggle"  id="compliance_container_dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bars" aria-hidden="true"></i>
            </span>
            <div class="dropdown-menu" aria-labelledby="compliance_container_dropdownMenu">
                <span class="dropdown-item" chart_container="compliance_container" chart_type="column" > <i class="fa fa-bar-chart" aria-hidden="true"></i></span>
                <span class="dropdown-item" chart_container="compliance_container" chart_type="pie" ><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
                <span class="dropdown-item" chart_container="compliance_container" chart_type="donut" ><i class="fa fa-stop-circle" aria-hidden="true"></i></span>
            </div>
        </div>
        {{-- Dropdown menu for compliance chart - End  --}}
        <div id="compliance_container"></div>
    </div>
    <div class="col-md-4 col-xs-4 col-sm-4 col-lg-4 card" >
        {{-- Dropdown menu for non compliance chart - Begin  --}}
        <div class="dropdown ">
                <span class="dropdown-toggle"  id="noncompliance_container_dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                </span>
                <div class="dropdown-menu" aria-labelledby="noncompliance_container_dropdownMenu">
                        <span class="dropdown-item" chart_container="non_compliance_container" chart_type="column" > <i class="fa fa-bar-chart" aria-hidden="true"></i></span>
                        <span class="dropdown-item" chart_container="non_compliance_container" chart_type="pie" ><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
                        <span class="dropdown-item" chart_container="non_compliance_container" chart_type="donut" ><i class="fa fa-stop-circle" aria-hidden="true"></i></span>
                </div>
        </div>
        {{-- Dropdown menu for non compliance chart - End  --}}
        <div id="non_compliance_container" ></div>
    </div>
    <div class="col-md-4 col-xs-4 col-sm-4 col-lg-4 card" >
        {{-- Dropdown menu for pending user chart - Begin  --}}
        <div class="dropdown ">
            <span class="dropdown-toggle"  id="pending_user_container_dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bars" aria-hidden="true"></i>
            </span>
            <div class="dropdown-menu" aria-labelledby="noncompliance_container_dropdownMenu">
                    <span class="dropdown-item" chart_container="pending_user_container" chart_type="column" > <i class="fa fa-bar-chart" aria-hidden="true"></i></span>
                    <span class="dropdown-item" chart_container="pending_user_container" chart_type="pie" ><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
                    <span class="dropdown-item" chart_container="pending_user_container" chart_type="donut" ><i class="fa fa-stop-circle" aria-hidden="true"></i></span>
            </div>
        </div>
        {{-- Dropdown menu for pending user chart - Begin  --}}
    
        <div id="pending_user_container" ></div>    
    </div>
</div>



<div class="row margin-top-20">
    <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12 ">
    <table class="table table-bordered" id="policy_table" style="display: none">
        <thead>
        <tr>
            <th>#</th>
            <th>Employee Id</th>
            <th>Employee Name</th>
            <th>Phone No</th>
            <th>Email Address</th>
            <th>Date completed</th>
        </tr>
        </thead>
    </table>
    <table class="table table-bordered" id="pending_user_table" style="display: none">
            <thead>
            <tr>
                <th>#</th>
                <th>Employee Id</th>
                <th>Employee Name</th>
                <th>Phone No</th>
                <th>Email Address</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
</div>

@stop

@section('scripts')
<script type="text/javascript">     
{!! Charts::assets() !!}
</script>
 <script>

 $(document).ready(function($){
	// $(function () {

		var complaince = JSON.parse('{!! str_replace('&quot;','',str_replace('&#039;','\\\'',json_encode($compliant_data['data']))) !!}');
		var non_complaince = JSON.parse('{!! str_replace('&quot;','',str_replace('&#039;','\\\'',json_encode($non_compliant_data['data']) ))!!}');
        var pending_users = JSON.parse('{!! str_replace('&quot;','',str_replace('&#039;','\\\'',json_encode($pending_user_data['data']) ))!!}');

        var policy_id = '{{ $policy_id}}';
        var non_complaince_labels = JSON.parse('{!! str_replace('&quot;','',str_replace('&#039;','\\\'',json_encode($non_compliant_data['labels']) ))!!}');
		var complaince_labels = JSON.parse('{!! str_replace('&quot;','',str_replace('&#039;','\\\'',json_encode($compliant_data['labels']) ))!!}');
        var pending_users_labels = JSON.parse('{!! str_replace('&quot;','',str_replace('&#039;','\\\'',json_encode($pending_user_data['labels']) ))!!}');
        console.log(non_complaince);

        /***** Non compliance chart - Begin *************/
        $('#non_compliance_container').highcharts({
            chart: {
                type: 'pie',
                
            },
            credits:{
                enabled:false
            },

            title: {
                text: 'Non-compliance by Policy'
            },
            xAxis: {
                    categories: non_complaince_labels,
                },

            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true
                    },
                    showInLegend: true
                },
                
            },
            series: [{
                name: 'Policy',
                dataLabels: [{
                    align: 'left',
                    format: '({point.name})'
                }], 
                colorByPoint: true,
                data: non_complaince,
                cursor: 'pointer',
                events: {
                            click: function (event) {
                                var reason_id=event.point.reason_id
                                
                                getComplianceDatatable(policy_id,reason_id);
                            }
                        }
            }],
            lang: {
                noData: "Nothing to show"
            },
            noData: {
                style: {
                    fontWeight: 'bold',
                    fontSize: '15px',
                    color: '#303030'
                }
            }

           

            
        });
        
         /***** Non compliance chart - End *************/

         /***** Compliance chart - Begin *************/

        $('#compliance_container').highcharts({
            chart: {
                type: 'pie'
            },
            credits:{
                enabled:false
            },
            title: {
                text: 'Compliance by Policy'
            },
            xAxis: {
                    categories: complaince_labels,
                },

            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true
                    },
                    showInLegend: true,
                    
                }
            },
        
            series: [{
                name: 'Policy',
                dataLabels: [{
                    align: 'left',
                    format: '({point.name})'
                }],
                colorByPoint: true,
                data: complaince,
                cursor: 'pointer',
                events: {
                            click: function (event) {
                                var reason_id=event.point.reason_id
                                
                                getComplianceDatatable(policy_id,reason_id);
                            }
                        }
            }],

           lang: {
                noData: "Nothing to show"
            },
            noData: {
                style: {
                    fontWeight: 'bold',
                    fontSize: '15px',
                    color: '#303030'
                }
            }
        });
         /***** Compliance chart - End *************/

         /***** Pending user chart - Begin *************/

        $('#pending_user_container').highcharts({
            chart: {
                type: 'pie'
            },
            credits:{
                enabled:false
            },
            title: {
                text: 'Pending Users'
            },
            xAxis: {
                    categories: pending_users_labels,
                },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true
                    },
                    showInLegend: true,
                    
                }
            },
        
            series: [{
                name: 'Policy',
                dataLabels: [{
                    align: 'left',
                    format: '({point.name})'
                }],
                colorByPoint: true,
                data: pending_users,
                cursor: 'pointer',
                events: {
                            click: function (event) {
                                var completed=event.point.completed
                                
                                getPendingUserDatatable(policy_id,completed);
                            }
                        }
            }],
            lang: {
                noData: "Nothing to show"
            },
            noData: {
                style: {
                    fontWeight: 'bold',
                    fontSize: '15px',
                    color: '#303030'
                }
            }
        });
         /***** Pending user chart - End *************/


        
        var compliance_chart = $('#compliance_container').highcharts(),
        non_compliance_chart = $('#non_compliance_container').highcharts(),
        pending_user_chart = $('#pending_user_container').highcharts(),
        name = false,
        enableDataLabels = true,
        enableMarkers = true,
        color = false;

        /******* Switching between chart types - Begin ********************/
            $('.dropdown-item').click(function () {
                var chart = $(this).attr('chart_container');
                var clicked_chart = $('#'+chart).highcharts();
                var type = $(this).attr('chart_type');
                //console.log(type,clicked_chart);

                //Attributes needed for donut chart
                if(type!='donut')
                {
                    clicked_chart.series[0].update({
                        type: type,
                        size: '',
                        innerSize: '',
                    });

                }else{
                    clicked_chart.series[0].update({
                    type: 'pie',
                    size: '100%',
                    innerSize: '60%',
                    });
                   
                }
            });


             $('.widget-dropdown-item').click(function () { 
                // var chart = $(this).attr('chart_container');
                var non_compliance_container = $('#non_compliance_container').highcharts();
                var compliance_container = $('#compliance_container').highcharts();
                var pending_user_container = $('#pending_user_container').highcharts();

                var type = $(this).attr('chart_type');
                //console.log(type,clicked_chart);

                //Attributes needed for donut chart
                if(type!='donut')
                {
                    non_compliance_container.series[0].update({
                        type: type,
                        size: '',
                        innerSize: '',
                    });

                    compliance_container.series[0].update({
                        type: type,
                        size: '',
                        innerSize: '',
                    });

                    pending_user_container.series[0].update({
                        type: type,
                        size: '',
                        innerSize: '',
                    });

                }else{
                    non_compliance_container.series[0].update({
                    type: 'pie',
                    size: '100%',
                    innerSize: '60%',
                    });

                    compliance_container.series[0].update({
                    type: 'pie',
                    size: '100%',
                    innerSize: '60%',
                    });

                    pending_user_container.series[0].update({
                    type: 'pie',
                    size: '100%',
                    innerSize: '60%',
                    });
                   
                }
            });


        /******* Switching between chart types - End ********************/

	});


    /****** Function for listing data of corresponding options in chart - compliance and non-compliance ****************/

	function getComplianceDatatable(policy_id,reason_id)
	{
		$('#policy_table').show();
		$('#pending_user_table_wrapper').hide();
		$.fn.dataTable.ext.errMode = 'throw';
		try{

			var table = $('#policy_table').DataTable({
				destroy: true,
				bprocessing: false,
				processing: false,
				serverSide: true,
				responsive: true,
				ajax:({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '{{ route("employee.compliance") }}',
					type: 'POST',
					 data:{'policy_id':policy_id,'reason_id' : reason_id},

				}),
				order: [
				[1, "asc"]
				],
				lengthMenu: [
				[10, 25, 50, 100, 500, -1],
				[10, 25, 50, 100, 500, "All"]
				],
				columns: [{
					data: 'DT_RowIndex',
					name: '',
					sortable:false
				},
				{
					data: 'employee_id',
					name: 'employee_id'
				},
				{
					data: 'employee_name',
					name: 'employee_name'
				},
				{
					data: 'phone_number',
					name: 'phone_number'
				},
				{
					data: 'email_address',
					name: 'email_address'
				},
				{
					data: 'date_completed',
					name: 'date_completed'
				},

				]
			});
		} catch(e){
			console.log(e.stack);
		}

	}


    /****** Function for listing data of pending user ***/
	function getPendingUserDatatable(policy_id,completed)
	{
		$('#policy_table_wrapper').hide();
		$('#pending_user_table').show();
		$.fn.dataTable.ext.errMode = 'throw';
		try{

			var table = $('#pending_user_table').DataTable({
				destroy: true,
				bprocessing: false,
				processing: false,
				serverSide: true,
				responsive: true,
				ajax:({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '{{ route("pending.compliance") }}',
					type: 'POST',
					 data:{'policy_id':policy_id,'completed' : completed},

				}),
				order: [
				[1, "asc"]
				],
				lengthMenu: [
				[10, 25, 50, 100, 500, -1],
				[10, 25, 50, 100, 500, "All"]
				],
				columns: [{
					data: 'DT_RowIndex',
					name: '',
					sortable:false
				},
				{
					data: 'employee_id',
					name: 'employee_id'
				},
				{
					data: 'employee_name',
					name: 'employee_name'
				},
				{
					data: 'phone_number',
					name: 'phone_number'
				},
				{
					data: 'email_address',
					name: 'email_address'
				},
				

				]
			});
		} catch(e){
			console.log(e.stack);
		}

	}



</script>
<style>
    .dropdown-menu {
    position: absolute !important;
    will-change: transform !important;
    transform: translate3d(5px, 213px, 0px);
    width: 150px !important;
    border-radius: 0px;
}
</style>
@stop
