
    <table class='timesheet-reconciliation w-100'>
        <thead>
           <tr>
               <th scope='col'>Position</th>
               <th scope='col'>CPID</th>
               <th scope='col'>Net Reg Hrs</th>
               <th scope='col'>Net OT Hrs</th>
               <th scope='col'>Net Stat Hrs</th>
               <th scope='col'>Reg Pay/Hr</th>
               <th scope='col'>OT Pay/Hr</th>
               <th scope='col'>Stat Pay/Hr</th>
               <th scope='col'>Total <br> Reg Pay</th>
               <th scope='col'>Total <br> OT Pay</th>
               <th scope='col'>Total <br> Stat Pay</th>
               <th scope='col'>Total <br> Reg Bill</th>
               <th scope='col'>Total <br> OT Bill</th>
               <th scope='col'>Total <br> Stat Bill</th>
               <th scope='col'>Billable OT</th>
               <th scope='col'>Absorbed OT</th>
           </tr>
       </thead>
       <tbody>
       @if(sizeof($cpid_timesheet_data['reconciliation_data'])>=1)
           @foreach($cpid_timesheet_data['reconciliation_data'] as $cpid)
               <tr>
               <td style='text-align: left;' scope='row'>{{$cpid['position']}}</td>
               <td style='text-align: left;' scope='row'>{{$cpid['cpid']}}</td>
               <td scope='row'>{{$cpid['net_reguler_hours']}}</td>
               <td scope='row'>{{$cpid['net_ot_hours']}}</td>
               <td scope='row'>{{$cpid['net_stat_hours']}}</td>
               <td scope='row'>${{$cpid['reguler_pay_per_hours']}}</td>
               <td scope='row'>${{$cpid['ot_pay_per_hours']}}</td>
               <td scope='row'>${{$cpid['stat_pay_per_hours']}}</td>
               <td scope='row'>${{$cpid['reguler_pay']}}</td>
               <td scope='row'>${{$cpid['ot_pay']}}</td>
               <td scope='row'>${{$cpid['stat_pay']}}</td>
               <td scope='row'>${{$cpid['reguler_bill']}}</td>
               <td scope='row'>${{$cpid['ot_bill'] }}</td>
               <td scope='row'>${{$cpid['stat_bill'] }}</td>
               <td scope='row'>{{$cpid['billable_ot'] }}</td>
               <td scope='row'>{{$cpid['absorved_ot'] }}</td>
               </tr>
           @endforeach
       @else
           <tr>
               <td colspan='16' style='text-align:center;'  class='position' scope='row'>
               No Data Found
           </td>
           </tr>
       @endif

      </tbody>
   </table>
<br>

@if(sizeof($cpid_timesheet_data['reconciliation_data'])>=1)
   <div class="d-flex">
       <section class="col-xl- px-1 pb-3 table-section section-common-height" >
           <div class="shadow px-3 py-2 bg-white rounded h-100">
               <label for="" class="label-head">Time</label>
               <div id="reconciliation_time_chart" class="js-area js-chart-area"></div>
           <div>
       </section>


       <section class="col-xl- px-1 pb-3 table-section section-common-height" >
           <div class="shadow px-3 py-2 bg-white rounded h-100">
               <label for="" class="label-head">Pay</label>
               <div id="reconciliation_pay_chart" class="js-area js-chart-area"></div>
           <div>
       </section>

       <section class="col-xl- px-1 pb-3 table-section section-common-height" >
           <div class="shadow px-3 py-2 bg-white rounded h-100">
               <label for="" class="label-head">Billable</label>
               <div id="reconciliation_billable_chart" class="js-area js-chart-area"></div>
           <div>
       </section>
   </div>

<script>

   Highcharts.chart('reconciliation_time_chart', {
       chart: {
           type: 'bar'
       },
       title: {
           text: 'Net Time By Types & Position'
       },

       xAxis: {
           categories: {!!json_encode($cpid_timesheet_data['reconciliation_time_chart']['label'],JSON_UNESCAPED_SLASHES)!!},
           title: {
               text: null
           }
       },
       yAxis: {
           min: 0,
           title: {
               text: 'Hours',
               align: 'high'
           },
           // labels: {
           //     overflow: 'justify'
           // }
       },
       tooltip: {
           valueSuffix: ' Hours'
       },
       plotOptions: {
           bar: {
               dataLabels: {
                   enabled: true
               }
           }
       },
       credits: {
           enabled: false
       },
       series: {!!json_encode($cpid_timesheet_data['reconciliation_time_chart']['series'],JSON_UNESCAPED_SLASHES)!!}

   });


   Highcharts.chart('reconciliation_pay_chart', {
       chart: {
           type: 'bar'
       },
       title: {
           text: 'Total Bill By Types & Position'
       },
       subtitle: {
           text: ''
       },
       xAxis: {
           categories: {!!json_encode($cpid_timesheet_data['reconciliation_pay_chart']['label'],JSON_UNESCAPED_SLASHES)!!},
           title: {
               text: null
           }
       },
       yAxis: {
           min: 0,
           title: {
               text: 'Dollar',
               align: 'high'
           },
           // labels: {
           //     overflow: 'justify'
           // }
       },
       tooltip: {
           valueSuffix: ' Dollar'
       },
       plotOptions: {
           bar: {
               dataLabels: {
                   enabled: true
               }
           }
       },
       credits: {
           enabled: false
       },
       series: {!!json_encode($cpid_timesheet_data['reconciliation_pay_chart']['series'],JSON_UNESCAPED_SLASHES)!!}

   });


   Highcharts.chart('reconciliation_billable_chart', {
       chart: {
           type: 'bar'
       },
       title: {
           text: 'Billable Details By Position'
       },
       subtitle: {
           text: ''
       },
       xAxis: {
           categories: {!!json_encode($cpid_timesheet_data['reconciliation_billable_chart']['label'],JSON_UNESCAPED_SLASHES)!!},
           title: {
               text: null
           }
       },
       yAxis: {
           min: 0,
           title: {
               text: 'Hours ',
               align: 'high'
           },
           // labels: {
           //     overflow: 'justify'
           // }
       },
       tooltip: {
           valueSuffix: ' Hours '
       },
       plotOptions: {
           bar: {
               dataLabels: {
                   enabled: true
               }
           }
       },
       credits: {
           enabled: false
       },
       series: {!!json_encode($cpid_timesheet_data['reconciliation_billable_chart']['series'],JSON_UNESCAPED_SLASHES)!!}
   });
</script>

@endif
