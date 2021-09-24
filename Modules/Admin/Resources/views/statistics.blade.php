@extends('adminlte::page')
@section('title', config('app.name', 'Laravel').'- Policy Statistics')
@section('content')
<div class="table_title">
	<h4> </h4><br>
</div>
<div class=col-md-12 col-xs-12 col-sm-12 col-lg-6 >
	<div id="container" style="min-width: 310px; max-width: 800px; height: 400px; margin: 0 auto"></div>
</div>
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

@stop
@section('js')
<script type="text/javascript">
	$(function () {
		var complaince = JSON.parse('{{ json_encode($dataset1) }}');
		var non_complaince = JSON.parse('{{ json_encode($dataset2) }}');
		var labels = JSON.parse('{!!str_replace('&quot;','',json_encode($labels))!!}');
		Highcharts.chart('container', {
			chart: {
				type: 'bar'
			},
			credits: {
      enabled: false
  },
			title: {
				text: 'Compliance by Policy'
			},
			xAxis: {
				categories:labels
			},
			plotOptions: {
				series: {
					stacking: 'normal',
					cursor: 'pointer',
					events: {

						click: function (event) {

							var category=event.point.category
							var status=this.name
							getDatatable(category,status);
						}
					}
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Compliance Status'

				}
			},
			legend: {
				reversed: true
			},

			series: [{
				name: 'Compliant',
				data: complaince,
			}, {
				name: 'Non-Compliant',
				data: non_complaince,

			}]
		});
	});
	function getDatatable(label,status)
	{
		$('#policy_table').show();
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
					url: '{{ route("policy-status.list") }}',
					type: 'POST',
					data:{'category':label,'policy' : status},

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


</script>
@stop
