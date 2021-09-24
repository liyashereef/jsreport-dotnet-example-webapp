<!-- KPI Widget -->
<style>
    .kpt-head {
        background-color: #333b53;
        color: white;
        text-align: center;
        vertical-align: middle;
    }

    .kpt-header {
        background-color: #ff6500;
        color: white;
    }

    .kpt-kpi-lite {
        background-color: #ffece9;
    }

    .kpt-kpi-dark {
        background-color: #ffd9cc;
    }

    .kpt-data-fld {
        text-align: center;
        vertical-align: middle;
        color: white;
    }

    .kpt-val-fld {
        text-align: center;
        vertical-align: middle;
        /* color:white; */
        font-weight: bold;
    }

    .table-kpi-widget td,
    .table-kpi-widget th {
        border: 2px solid white;
    }

    .widget-kpi {
        display: block;
        margin-top: 5px;
    }

    .kpt-fbtn {
        display: inline-block;
        vertical-align: middle;
        margin-right: 15px;
        margin-left: 15px;
        margin-top: 5px;
        text-align: left;
    }

    .kpt-back-arrow {
        width: 30px;
    }

    .kpt-alert {
        width: 40%;
        margin: auto;
        position: relative;
        top: 30%;
        transform: translateY(-50%);
    }

    .loader-container {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 1800;
        background-color: rgba(255, 255, 255, 0.6);
    }

    /* Loader */
    .loader,
    .loader:before,
    .loader:after {
        background: #ff6500;
        -webkit-animation: load1 1s infinite ease-in-out;
        animation: load1 1s infinite ease-in-out;
        width: 1em;
        height: 4em;
    }

    .loader {
        color: #333b53;
        text-indent: -9999em;
        margin: 88px auto;
        position: relative;
        top: 40%;
        font-size: 11px;
        -webkit-transform: translateZ(0);
        -ms-transform: translateZ(0);
        transform: translateZ(0);
        -webkit-animation-delay: -0.16s;
        animation-delay: -0.16s;
    }

    .loader:before,
    .loader:after {
        position: absolute;
        top: 0;
        content: '';
    }

    .loader:before {
        left: -1.5em;
        -webkit-animation-delay: -0.32s;
        animation-delay: -0.32s;
    }

    .loader:after {
        left: 1.5em;
    }

    @-webkit-keyframes load1 {

        0%,
        80%,
        100% {
            box-shadow: 0 0;
            height: 4em;
        }

        40% {
            box-shadow: 0 -2em;
            height: 5em;
        }
    }

    @keyframes load1 {

        0%,
        80%,
        100% {
            box-shadow: 0 0;
            height: 4em;
        }

        40% {
            box-shadow: 0 -2em;
            height: 5em;
        }
    }
</style>
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    let _kpNavigation = [];

    widgets.define('widgetKeyPerformanceIndicators', function(payload) {
        let wc; //widget container

        const wkpi = {
            filters: {
                from: '',
                to: '',
                activeGroup: '',
            },
            getDefautFromDate() {
                return moment().subtract(31, 'd').format('YYYY-MM-DD');
            },
            getDefaultToDate() {
                return moment().subtract(1, 'd').format('YYYY-MM-DD');
            },
            applyHeaderFilter() {
                let output = `<div class="row">
                <div class="col-md-4">
                    <input disabled type="text" placeholder="Start Date" value="${this.hasFilters('from')? this.filters.from : this.getDefautFromDate()}" class="kpi-dt kpi-date-from">
                </div>
                <div class="col-md-4">
                    <input disabled type="text" value="${this.hasFilters('to')? this.filters.to : this.getDefaultToDate()}" placeholder="End Date" class="kpi-dt kpi-date-to">
                </div>
                <div class="col-md-4">
                    <a href="javascript:void(0)" class="kpt-fbtn kpt-filter-search">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </a>
                    <a href="javascript:void(0)" class="kpt-fbtn kpt-filter-reset">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </a>
                </div>
            </div>`;

                //Replace filter content with gen html
                $('body').find(`.${payload.widgetInfo.dataTargetId} .filter-content`).html(output);
            },
            hasFilters(key) {
                if (this.filters.hasOwnProperty(key)) {
                    if (this.filters[key]) {
                        return true;
                    }
                }
                return false;
            },
            genTableHeader() {
                let _backButton = '';
                if (_kpNavigation.length > 0) {
                    _backButton = '<label class="js-tree-back"><i class="fas fa-arrow-left"></i></labal>';
                }
                let els = `<th class="kpt-head kpt-back-arrow">${_backButton}</th><th class="kpt-head"></th>`;

                payload.data.groups.forEach(function(g, index) {
                    let _icon = g.isLeafNode == 0 ? '<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>' : '';
                    let _hStyle = g.isLeafNode == 0 ? '' : 'style="cursor:default;"';

                    els += `<th class="kpt-head js-kpt-head-link" data-id="${g.id}" data-is-leaf="${g.isLeafNode}"><a ${_hStyle} href="javascript:void(0)">${_icon} ${g.name}</a></th>`;
                });
                els += '<th class="kpt-head">Average</th>';
                return `<thead><tr>${els}</tr></thead>`;
            },
            genTableBody() {
                let data = payload.data.infos;
                let groups = payload.data.groups;
                let els = '';

                //Loop body content
                data.forEach(function(hdr) {
                    els += `<tr><td class="kpt-header" colspan="${groups.length+3}">${hdr.name}</td></tr>`;

                    //Loop kpis
                    hdr.kpis.forEach(function(kpi, index) {
                        let kpiClass = index % 2 == 0 ? 'kpt-kpi-lite' : 'kpt-kpi-dark';

                        let kpels = `<td class="${kpiClass}"></td><td class="${kpiClass}">${kpi.name}</td>`;
                        let vsum = 0;
                        let dataClass = 'kpt-val-fld';

                        for (let kv of kpi.values) {
                            let _style = `style="background-color:${kv.color};color:${kv.font_color};"`
                            vsum += Number(kv.value);
                            kpels += `<td ${_style} class="${dataClass}">${kv.value}</td>`;
                        }
                        let _avgStyle = `style="background-color:${kpi.color};color:${kpi.font_color};"`
                        kpels += `<td ${_avgStyle} class="${dataClass}">${kpi.average}<a/td>`;
                        els += `<tr>${kpels}</tr>`;
                    });
                });
                return `<body>${els}</body>`;
            },
            applyLoader() {
                let c = $('body').find(`.${payload.widgetInfo.dataTargetId} .kwt-container`)
                let loader = `<div class="loader-container">
                    <div class="loader">Loading...</div>
                </div>`;
                c.html(loader);
            },
            generateContent() {
                let content = '';
                let _btn = _kpNavigation.length > 0 ? ' <button type="button" class="btn d-inline js-tree-back">Go Back</button>' : '';

                if (payload.data.infos.length <= 0) {
                    content = `
                    <div class="kwt-container"></div>
                    <div class="kpt-alert">
                        <div class="alert d-inline" role="alert">
                            <h4 class="alert-heading d-inline">No records found!</h4>
                            ${_btn}
                        </div>
                   </div>
                    `;
                } else {
                    content = `
                    <div class="kwt-container"></div>
                    <div class="widget-kpi">
                        <table class="table table-kpi-widget">
                            ${this.genTableHeader()}
                            ${this.genTableBody()}
                        </table>
                    </div>
                    `;
                }

                //..process
                return content;
            },
            bindContent(el) {
                wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
                wc.find('.dasboard-card-body').html(el);
            },
            resetFilters() {
                this.filters.to = '';
                this.filters.from = '';
                this.filters.activeGroup = '';
                $('.kpi-date-from').val('');
                $('.kpi-date-to').val('');
            },
            afterBind() {
                let root = this;

                $('.kpi-date-from').datepicker({
                    format: 'yyyy-mm-dd',
                    maxDate: new Date(),
                    change: function(e) {
                        root.filters.from = $('.kpi-date-from').val();
                    },
                });
                $('.kpi-date-to').datepicker({
                    format: 'yyyy-mm-dd',
                    maxDate: new Date(),
                    change: function(e) {
                        root.filters.to = $('.kpi-date-to').val();
                    },
                });
                //On kpi search
                $('.kpt-filter-search').on('click', function() {
                    root.refreshWithFilter(payload.widgetTag, root.filters);
                });
                //On kpi filter reset
                $('.kpt-filter-reset').on('click', function() {
                    root.resetFilters();
                    _kpNavigation = [];
                    root.refreshWithFilter(payload.widgetTag, root.filters);
                });
                //On group click
                $('.js-kpt-head-link').on('click', function(e) {
                    e.stopImmediatePropagation();

                    //Skip leaf noad click
                    if ($(this).data('is-leaf') == 1) {
                        return;
                    }
                    _kpNavigation.push(root.filters.activeGroup);
                    root.filters.activeGroup = $(this).data('id');
                    root.refreshWithFilter(payload.widgetTag, root.filters);
                });
                //On group click
                $('.js-tree-back').on('click', function() {
                    let _gid = _kpNavigation.pop();
                    root.filters.activeGroup = _gid;
                    root.refreshWithFilter(payload.widgetTag, root.filters);
                });

                //Trigger date filter for initial time
                $('.kpi-dt').trigger('change');

                // //On change customer filter
                // $('#dashboard-filter-customer').on('change', function() {
                //     root.refreshWithFilter(payload.widgetTag, root.filters);
                // });
            },
            refreshWithFilter() {
                this.applyLoader();
                widgets.refresh(payload.widgetTag, this.filters);
            },
            loadFilters() {
                if (payload.hasOwnProperty('filters')) {
                    this.filters = payload.filters;
                }
            },
            init() {
                //Load Filters
                this.loadFilters();
                //Apply header filter
                this.applyHeaderFilter();
                //Bind contents
                this.bindContent(this.generateContent());
                //Execute after content is added to dom
                this.afterBind();
            }
        }

        //Initialize the widget
        wkpi.init();

    });
</script>
