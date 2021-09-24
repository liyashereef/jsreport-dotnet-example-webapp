<style>
.timesheet-reconciliation-tbl th {
    background: #393f4f;
    color: white;
}

.content-cards .has-search .form-control-feedback {
    top: -2px;
    position: absolute;
    left: -10px;
    z-index: 1;
}
.content-cards li{
  list-style-type:none;
  padding: 0.45rem;
}
::-webkit-scrollbar {
  width: 5px;
  height: 5px;
  }

.section-common-height{
  height:600px;
  overflow:auto;
}
.js-area.js-chart-area{
  height:100%;
}
body .highcharts-container {
height: 100%!important;
overflow: auto!important;
}
.graph-section-incident{
  width:50%;
}

::-webkit-scrollbar-track {
-webkit-box-shadow: inset 0 0 4px rgba(0,0,0,0.5);
border-radius: 8px;
}

::-webkit-scrollbar-thumb {
border-radius: 8px;
-webkit-box-shadow: inset 0 0 4px rgba(0,0,0,0.4);
}

    .table-section{
    width:33.33%;
    }
    .graph-section{
    min-width:50%;
    }
    .graph-section-incident{
    min-width:50%;
    height: auto;
    min-height: 405px;
    }
    .border-graph{
    border:1px solid #ddd;
    }
    .border-graph label{
    width: 100%;
    background: #E65100;
    color: #ffffff;
    }
    .graph-section-metrics{
    min-width: 100%;
    }

    .pad-top-10 {
        padding-top: 20px;
    }

    .widget-timesheet-reconciliation span.filter-content {
        width: 25% !important;
        float: right;
    }

    .widget-timesheet-reconciliation span.pl-2 {
        width: 75% !important;
    }
</style>

<!-- <link href="{{ asset('faclitymanagementdashboard/dashboard-styles.css') }}" rel="stylesheet"> -->
<script src="{{ asset('js/highcharts/highcharts.js') }}"></script>

<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetTimesheetReconciliation',function(payload) {
        let wc; //widget container
        let j=0;
        let filters = payload.filters;
        const payperiodFilterKey = 'payperiod_id';

        function applyHeaderFilter() {
            //select box
            let options = `<option>No employee found</option>`;
            if ((payload.data.payperiods != null) && (payload.data.payperiods != undefined)) {
                let payperiods = payload.data.payperiods;
                //Generate options
                options = ``;
                $.each(payperiods, function(index, payperiod) {
                    payperiodId = payperiod.id;
                    payperiodName = (payperiod.short_name != null)? payperiod.pay_period_name+' ('+payperiod.short_name+')':payperiod.pay_period_name;
                    let selected = isFilterSelected(payperiodFilterKey, payperiodId) ? 'selected' : '';
                    options += `<option ${selected} value="${payperiodId}">${payperiodName}</option>`;
                });
            }
            let output = `<select class="w-payperiod-id form-control">${options}</select>`;

            //Replace filter content with gen html
            $('body').find(`.${payload.widgetInfo.dataTargetId} .filter-content`).html(output);
        }

        function isFilterSelected(key, value) {
            let fv = filters[payperiodFilterKey];
            return fv == value;
        }

        function formatValue(itemVal = 0) {
            let val = String(itemVal);
            let newVal = val.replace(',','');
            return parseFloat(newVal).toFixed(2);
        }

        function generateContent() {
            let reconciliationData = payload.data.result.reconciliation_data;
            let tableContent = ``, content = ``;
            if(reconciliationData != null && reconciliationData != undefined) {
                tableContent = `<tr><td class="text-center" colspan="16">No Data Found</td></tr>`;
                $.each(reconciliationData, function(key, reconciliationItem) {
                    if(j == 0) {
                        tableContent = ``;
                    }
                    tableContent +=`<tr>`;
                    tableContent +=`<td>${reconciliationItem.position}</td>`;
                    tableContent +=`<td class="text-center">${reconciliationItem.cpid}</td>`;
                    tableContent +=`<td class="text-center">${formatValue(reconciliationItem.net_reguler_hours)}</td>`;
                    tableContent +=`<td class="text-center">${formatValue(reconciliationItem.net_ot_hours)}</td>`;
                    tableContent +=`<td class="text-center">${formatValue(reconciliationItem.net_stat_hours)}</td>`;
                    tableContent +=`<td class="text-center">$${formatValue(reconciliationItem.reguler_pay_per_hours)}</td>`;
                    tableContent +=`<td class="text-center">$${formatValue(reconciliationItem.ot_pay_per_hours)}</td>`;
                    tableContent +=`<td class="text-center">$${formatValue(reconciliationItem.stat_pay_per_hours)}</td>`;
                    tableContent +=`<td class="text-center">$${formatValue(reconciliationItem.reguler_pay)}</td>`;
                    tableContent +=`<td class="text-center">$${formatValue(reconciliationItem.ot_pay)}</td>`;
                    tableContent +=`<td class="text-center">$${formatValue(reconciliationItem.stat_pay)}</td>`;
                    tableContent +=`<td class="text-center">$${formatValue(reconciliationItem.reguler_bill)}</td>`;
                    tableContent +=`<td class="text-center">$${formatValue(reconciliationItem.ot_bill)}</td>`;
                    tableContent +=`<td class="text-center">$${formatValue(reconciliationItem.stat_bill)}</td>`;
                    tableContent +=`<td class="text-center">${formatValue(reconciliationItem.billable_ot)}</td>`;
                    tableContent +=`<td class="text-center">${formatValue(reconciliationItem.absorved_ot)}</td>`;
                    tableContent +=`</tr>`;
                    j++;
                });
            }
            content += `<table class="table timesheet-reconciliation-tbl table-bordered tbl-line-height-1"><thead><tr><th>Position</th> <th class="text-center">CPID</th> <th  class="text-center">Net Reg Hrs</th> <th class="text-center">Net OT Hrs</th> <th class="text-center">Net Stat Hrs</th> <th class="text-center">Reg Pay/Hr</th> <th class="text-center">OT Pay/Hr</th> <th class="text-center">Stat Pay/Hr</th> <th class="text-center">Total <br> Reg Pay</th> <th class="text-center">Total <br> OT Pay</th> <th class="text-center">Total <br> Stat Pay</th> <th class="text-center">Total <br> Reg Bill</th> <th class="text-center">Total <br> OT Bill</th> <th class="text-center">Total <br> Stat Bill</th> <th class="text-center">Billable OT</th> <th class="text-center">Absorbed OT</th></tr></thead><tbody>${tableContent}</body></table>`;

            //graph
            if(j>0) {
                content += `<br /><div class="d-flex pad-top-10">`;
                content += `<section class="col-xl- px-1 table-section section-common-height bg-white" > <div class="px-3 py-2 rounded h-100"> <label for="" class="label-head">Time</label> <div id="reconciliation_time_chart" class="js-area js-chart-area"></div> <div> </section>`;
                content += `<section class="col-xl- px-1 table-section section-common-height bg-white" > <div class="px-3 py-2 rounded h-100"> <label for="" class="label-head">Pay</label> <div id="reconciliation_pay_chart" class="js-area js-chart-area"></div> <div> </section>`;
                content += `<section class="col-xl- px-1 table-section section-common-height bg-white" > <div class="px-3 py-2 rounded h-100"> <label for="" class="label-head">Billable</label> <div id="reconciliation_billable_chart" class="js-area js-chart-area"></div> <div> </section>`;
                content += `</div>`;
            }
            return content;
        }

        function generateReconciliationByTimeGraph(payload) {
            let items = payload.data.result;

            Highcharts.chart('reconciliation_time_chart', {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: 'Net Time By Types & Position'
                },

                xAxis: {
                    categories: items.reconciliation_time_chart.label,
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
                },
                tooltip: {
                    valueSuffix: ' Hours'
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return '<span style="stroke-width: 0px!important;color:black !important;font-weight: normal !important;font-size:12px !important;">' + this.y +' Hours</span>';
                            },
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                series: items.reconciliation_time_chart.series
            });
        }

        function generateReconciliationByPayGraph(payload) {
            let items = payload.data.result;
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
                    categories: items.reconciliation_pay_chart.label,
                    title: {
                        text: null
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Dollar ($)',
                        align: 'high'
                    }
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>${point.y:.2f}</b>',
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return '<span style="stroke-width: 0px!important;color:black !important;font-weight: normal !important;font-size:12px !important;">$' + Highcharts.numberFormat(this.y,2) + '</span>';
                            },
                        },
                    }
                },
                credits: {
                    enabled: false
                },
                series: items.reconciliation_pay_chart.series

            });
        }

        function generateReconciliationByBillGraph(payload) {
            let items = payload.data.result;
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
                    categories: items.reconciliation_billable_chart.label,
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
                },
                tooltip: {
                    valueSuffix: ' Hours'
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return '<span style="stroke-width: 0px!important;color:black !important;font-weight: normal !important;font-size:12px !important;">' + this.y +' Hours </span>';
                            },
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                series: items.reconciliation_billable_chart.series
            });
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);
        }

        function refreshWithFilter() {
            widgets.refresh(payload.widgetTag, filters);
        }

        function afterBind() {
            wc.find(`.w-payperiod-id`).on('change', function() {
                filters[payperiodFilterKey] = $(this).val();
                refreshWithFilter();
            });
        }

        //Bind contents
        applyHeaderFilter();
        bindContent(generateContent());
        afterBind();

        if(j>0) {
            generateReconciliationByTimeGraph(payload);
            generateReconciliationByPayGraph(payload);
            generateReconciliationByBillGraph(payload);
        }
    });

</script>
