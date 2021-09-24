@extends('layouts.landing')

@section('content')
<div id="dashboard_content" style="margin-bottom: 1% !important;margin-top: -0.50em !important;height: 95%;margin-left:3px !important;margin-right:3px !important;">
    <div class="" id="dashboard_tab_element" style="margin-left: -8px !important;"></div>
    <div class="row" id="dashboard_tab_details"></div>
</div>
@endsection


@section('scripts')

@include('partials.widgets.widgets')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>
<script type="text/javascript">
    let selected_customer = JSON.parse('{!!json_encode($selected_customer);!!}');

    //Un completed request pool
    $.xhrPool = [];

    function abortRequests() {
        $.xhrPool.forEach(function(jqXHR) {
            jqXHR.abort();
        });
        $.xhrPool = [];
    }

    //Filter cache
    let filterCache = new Map();

    const logger = {
        log(str) {
            console.log(str);
        },
        error(str) {
            console.error(str);
        },
        warn(str) {
            console.warn(str)
        }
    }

    //Route configruation
    const routes = {
        getTabs: "{{route('dashboard-tabs')}}",
        getTabsv2: "{{route('landing-page-tabs')}}",
        getDashboardDetails: "{{route('dashboard-tab-details')}}",
        syncDashboardCustomerFilter: "{{route('sync-dashboard-filter')}}",
        //Widgets API's
    }
    //Dashboard script
    const dashboard = {
        data: {
            tabs: [],
            booted: false,
            activeTab: null,
        },
        syncDashboardCustomerFilter(values) {
            let root = this;
            //async request.
            $.ajax({
                url: routes.syncDashboardCustomerFilter,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "customer_ids": values
                },
                beforeSend: function(jqXHR) { // before jQuery send the request we will push it to our array
                    $.xhrPool.push(jqXHR);
                },
                success: function(response) {
                    if (response.success == true || response.success == "true") {
                        $('#dashboard_tab_details').html('');
                        if (values.length == 0) {
                            values = selected_customer;
                        }
                        root.loadTabs(values);
                        root.reloadIframe(); // For Dashboard iframe reload
                    }
                },
                complete: function(jqXHR) { // when some of the requests completed it will splice from the array
                    let index = $.xhrPool.indexOf(jqXHR);
                    if (index > -1) {
                        $.xhrPool.splice(index, 1);
                    }
                }
            });
        },
        reloadIframe() {
            // iframe reload on Dashboard
            if (document.getElementById('iframe') != undefined && document.getElementById('iframe').src != null) {
                document.getElementById('iframe').src += '';
            }
        },
        convertCamelToKebab(string) {
            return string.replace(/([a-z0-9]|(?=[A-Z]))([A-Z])/g, '$1-$2').toLowerCase();
        },
        transformObjectForApi(object) {
            let output = {};
            for (const [key, value] of Object.entries(object)) {
                output[this.convertCamelToKebab(key)] = value;
            }
            return output;
        },
        loadTabs(customerId) {
            let root = this;
            $.ajax({
                type: "GET",
                url: routes.getTabsv2,
                data: {
                    'customer_id': customerId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function(jqXHR) { // before jQuery send the request we will push it to our array
                    $.xhrPool.push(jqXHR);
                },
                success: function(response) {
                    root.setupTabs(response.data);
                },
                complete: function(jqXHR) { // when some of the requests completed it will splice from the array
                    let index = $.xhrPool.indexOf(jqXHR);
                    if (index > -1) {
                        $.xhrPool.splice(index, 1);
                    }
                }
            });
        },
        setupTabs(tabs) {
            //TODO:rewrite this function
            let root = this;
            this.data.tabs = tabs;
            //Bind tabs
            $('#dashboard_tab_element').html(root.buildTabs());
            //Register events for tab
            $('body').find('.arrow').hide();

            let tabs_count = $('body').find('.dashboard-tabs > .tab-config').length;
            if (tabs_count > 0) {
                load_tab_structure(1, 6);
                load_first_tab_content();
            }

            function load_tab_structure(minIndex, maxIndex) {
                $('body').find('.arrow').hide();
                $('body').find('.tab-config').hide();
                $('body').find('.arrow').addClass('hidden');
                $('body').find('.arrow').removeClass('show');
                $('body').find('.tab-config').removeClass('tab-end');
                $('body').find('.tab-config').removeClass('tab-start')

                let index = 1;
                $('body').find('.tab-config').each(function() {
                    if (index <= maxIndex && index >= minIndex) {
                        $('body').find('.tab_' + index).show();
                    } else {
                        $('body').find('.tab_' + index).hide();
                    }

                    if (index > maxIndex) {
                        $('body').find('.next-arrow').show();
                        $('body').find('.next-arrow').removeClass('hidden');
                        $('body').find('.next-arrow').addClass('show');
                    }

                    if (index < minIndex) {
                        $('body').find('.prev-arrow').show();
                        $('body').find('.prev-arrow').removeClass('hidden');
                        $('body').find('.prev-arrow').addClass('show');
                    }

                    if (index == maxIndex) {
                        $('body').find('.tab_' + index).addClass('tab-end');
                    }

                    if (index == minIndex) {
                        $('body').find('.tab_' + index).addClass('tab-start');
                    }
                    index++;
                });
                $('body').find('.tab-config').css('width', '16%');
                // $('body').find('.tab-config').css('width', '100%');

                //trim and limit tabs character content
                $('body').find('.tab-config').each(function() {
                    let tab_selected = $(this);
                    let tab_group = tab_selected.attr('data-tab-group');
                    let tabs_count = $('body').find('.tab_group_' + tab_group).length;
                    let calculated_character_length = parseInt(80 / tabs_count);
                    let tab_text = tab_selected.text().trim();
                    let tab_text_length = tab_text.length;
                    if (tab_text_length > calculated_character_length) {
                        tab_selected.text(tab_text.substring(0, calculated_character_length) + '..');
                    }
                });
            }

            function load_first_tab_content() {

                let firstTabId = $('body').find('.tab-config.tab-start').attr('data-tab');
                if (firstTabId !== "") {
                    selected_tab = firstTabId;
                    dashboard.loadTabDetails(selected_tab);
                }
            }

            $('body').find('.tab-config').on('click', function() {
                let selected_tab = $(this).attr('data-tab');
                let tabs_count = $('body').find('.dashboard-tabs > .tab-config').length;
                let end_tab = $(this).hasClass('tab-end');
                let start_tab = $(this).hasClass('tab-start');
                let tab_index = $(this).attr('data-tab-index');
                if (end_tab && tabs_count != tab_index) {
                    let minIndex = Number(tab_index) - 0;
                    let maxIndex = Number(tab_index) + 5;
                    load_tab_structure(minIndex, maxIndex);
                    dashboard.loadTabDetails(selected_tab);
                } else if (start_tab && tab_index >= 6) {
                    let minIndex = Number(tab_index) - 5;
                    let maxIndex = Number(tab_index) + 0;
                    load_tab_structure(minIndex, maxIndex);
                    dashboard.loadTabDetails(selected_tab);
                } else {
                    dashboard.loadTabDetails(selected_tab);
                }
            });

            $('body').find('.next-arrow').on('click', function() {
                let tab_index = $('body').find('.tab-end').attr('data-tab-index');
                let minIndex = Number(tab_index) + 1;
                let maxIndex = Number(tab_index) + 6;
                load_tab_structure(minIndex, maxIndex);
                load_first_tab_content();
            });

            $('body').find('.prev-arrow').on('click', function() {
                let tab_index = $('body').find('.tab-start').attr('data-tab-index');
                if (tab_index < 7) {
                    logger.log('in');
                    let minIndex = 1;
                    let maxIndex = 6;
                    load_tab_structure(minIndex, maxIndex);
                } else {
                    let minIndex = Number(tab_index) - 6;
                    let maxIndex = Number(tab_index) - 1;
                    load_tab_structure(minIndex, maxIndex);
                }

                load_first_tab_content();
            });
        },
        buildTabs() {
            let els = '';
            let tabGroup = '';
            this.data.tabs.forEach(function(data, i) {
                i++;
                let defaultTab = i == 1 ? 'dashboard-default-selected-tab' : '';
                tabGroup = parseInt(i / 7);

                els += `<a data-tab="${data.id}"
                    data-tab-group="${tabGroup}"
                    data-tab-index="${i}"
                    class="tab_${i} nav-item nav-link expense tab-config ${defaultTab} tab_group_${tabGroup}"
                    title="${data.name}"
                    id="tab_${data.id}"
                    href="javascript:void(0)">${data.name}
                </a>`;
            });

            return `<div id="dashboard-tab-area" class="nav nav-tabs expense dashboard-tabs" role="tablist" style="flex-wrap: inherit;">
                        <a class="nav-item nav-link arrow prev-arrow" data-tab-index="0" href="#">
                            <span style="color:white;">&#10229;</span>
                        </a>
                        ${els}
                        <a class="nav-item nav-link arrow next-arrow" data-tab-index="${this.data.tabs.length+1}" href="#">
                            <span style="color:white;">&#10230;</span>
                        </a>
                    </div>`;
        },
        async clearTabDetails() {
            $('.dashboard-tables').hide();
            return ($('.dashboard-tables').length > 0) ? $('.dashboard-tables').is(":hidden") : true;
        },
        async loadTabDetails(tabId) {
            let root = this;
            abortRequests();
            $(".image-wrapper").remove(); //for schedule widget

            //Hide all tabs
            if (!await root.clearTabDetails()) {
                logger.log(`Failed to hide/clear tab[${tabId}] details`);
                return;
            }

            let element_uid = 'tabuid_' + tabId;
            $('.tab-config').removeClass('active');
            $('#tab_' + tabId).addClass('active');

            let tabElement = document.getElementById(element_uid);
            if (tabElement) {
                clear_status = await root.clearTabDetails();
                $('#' + element_uid).show();

                $('.w-sch-payperiod:visible').select2();
                $('.w-sch-payperiod:visible').val($('.w-sch-payperiod:visible').val()).trigger('change');
                $('.emp-schedule-no-data').show();
                $('.emp-schedule-with-data').hide();
            } else {
                let expandSideMenu = '';
                let searchBarHeight = $("#dashboard_filter_section").height();
                let parentDivWidth = $(window).width();
                let parentDivHeight = (($('#content').height() - $('#content').offset().top) - searchBarHeight);

                $.ajax({
                    type: "GET",
                    url: routes.getDashboardDetails,
                    data: {
                        'tab_id': tabId,
                        'parentDivWidth': parentDivWidth,
                        'parentDivHeight': parentDivHeight,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#dashboard_tab_details').append(response.html);
                        $('.dashboard-tables').hide();
                        $('#' + element_uid).show();
                        logger.log(`----- Showing Tab: [${element_uid}] -----`);
                        root.loadWidgetFieldsOfTab(tabId);
                    }
                });
            }

        },
        loadDefaultSelectedTabContent() {
            let selected_tab = $('.dashboard-default-selected-tab').attr('data-tab');
            this.LoadTabDetails(selected_tab);
        },
        loadSingleWidget(tag, filters = {}) {
            $("<div class='widget_loader'>P&nbsp;L&nbsp;E&nbsp;A&nbsp;S&nbsp;E&nbsp;&nbsp;&nbsp;&nbsp;W&nbsp;A&nbsp;I&nbsp;T&nbsp;.&nbsp;.&nbsp;.</div>").css({
                position: "absolute",
                width: "100%",
                height: "100%",
                top: 0,
                left: 0,
                background: "#ccc"
            }).appendTo($("." + tag + '>.dasboard-card-body').css("position", "relative"));
            let target = $(`.${tag}:visible`);
            if (target.length <= 0) {
                logger.log(`Loading single widget [${tag}] : element not found in dom`);
            }
            if (target.length > 1) {
                logger.error(`Load: ${target.length} item(s) found`);
            }
            //Do load widgets
            if (target.length == 1) {
                let attr = this.unpackWidgetInfo($(target).data('attr'));
                this.callWidgetApi(attr, filters);
            }
            $("." + tag + '>.dasboard-card-body').children('div.widget_loader').remove();
        },
        unpackWidgetInfo(base64dt) {
            return JSON.parse(atob(base64dt));
        },
        loadWidgetFieldsOfTab(tabId, useFilterCache = false, exclusion = []) {
            let root = this;
            let widgets = '';
            if (useFilterCache) {
                widgets = $(`#tabuid_${tabId}`).find('.js-widget.LandingWidget');
            } else {
                widgets = $(`#tabuid_${tabId}`).find('.js-widget');
            }

            if (widgets.length < 0) {
                logger.log(`Load: No widges detected for tab: ${tabId}`);
            }
            logger.log(`Load: Loading ${widgets.length} widgets for tab ${tabId}`);

            widgets.each(function(i, widget) {
                let filters = {};
                let widgetAttrs = root.unpackWidgetInfo($(widget).data('attr'));
                let widgetid = widgetAttrs.dataTargetId
                if (useFilterCache) {
                    filters = filterCache[widgetAttrs.dataTargetId];
                }
                let attr = {
                    ...widgetAttrs,
                    tabId
                }

                //Check the widget is in exclusion list
                let isExcluded = false;
                if (exclusion.length > 0) {
                    $.each(exclusion, function(i, value) {
                        if (widgetid.includes(value)) {
                            isExcluded = true;
                        }
                    })
                }

                //Call API if widgets in not excluded
                if (isExcluded == false) {
                    root.callWidgetApi(attr, filters);
                }

            });
        },
        callWidgetApi(widgetInfo, filters = {}) {
            let root = this;

            if (widgetInfo === null || widgetInfo === undefined) {
                logger.error(`Load: Field details invalid widget data provided`);
            }

            //fetch customer filter for session re-check in controller
            let customerSearch = {
                'customer-search': $('#dashboard-filter-customer').val()
            };

            //Cache filters into cache
            filterCache[widgetInfo.dataTargetId] = filters;
            //Send request to server
            $.ajax({
                type: "GET",
                url: widgetInfo.dataApiUrl,
                bFilter: true,
                async: true,
                data: root.transformObjectForApi({
                    ...widgetInfo,
                    ...filters,
                    ...customerSearch
                }),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function(jqXHR) { // before jQuery send the request we will push it to our array
                    $.xhrPool.push(jqXHR);
                },
                success: function(response) {
                    //For new widgets
                    if (response.type !== undefined && response.type === 'json') {
                        //add filters to the payload
                        response.filters = filters;
                        //add widget info
                        response.widgetInfo = widgetInfo;
                        //Generte widgets using factory
                        try {
                            WidgetFactory.build(response.widgetTag, response);
                        } catch (e) {
                            logger.error(e)
                        }
                        return;
                    }
                    //For old widgets
                    root.afterOldWidgetFetch(response, widgetInfo);
                },
                error: function(xhr, textStatus, errorThrown) {
                    //TODO:check
                    if (textStatus === "abort") {
                        $('#tabuid_' + widgetInfo.tabId).remove();
                    }
                },
                complete: function(jqXHR) { // when some of the requests completed it will splice from the array
                    let index = $.xhrPool.indexOf(jqXHR);
                    if (index > -1) {
                        $.xhrPool.splice(index, 1);
                    }

                    $('.filter-content').each(function() {
                        if (!$(this).is(":empty")) {
                            let headerSpanId = '#h_span_' + $(this).attr('id');
                            $(headerSpanId).css("width", "58%");
                            $(headerSpanId).css("overflow", "hidden");
                        }
                    });
                }
            });

        },
        afterOldWidgetFetch(response, data_object) {
            let root = this;
            let id = data_object.dataTargetId;
            let spanId = data_object.spanId;
            let tabId = data_object.tabId;
            let searchBoxItems = $('#dashboard-filter-customer').val();
            //Old widget code ...
            if (response.module_type !== undefined && response.module_type !== "" && response.module_type == "ShiftModule") {
                $('#' + id).html('<thead id="' + response.shift_module_id + '"></thead>');
                $('#' + id).prop('id', 'modu-' + response.shift_module_id);
                root.loadEachTable(response.shift_module_id, response.customer_id, data_object.tabId);
            } else {
                if ((response.selectBoxView !== undefined) && (response.selectBoxView !== "")) {
                    $('#' + spanId).html(response.selectBoxView);
                    $('#' + id).addClass('schedule-widget');
                    $('.schedule-widget').bind("mouseover", function() {
                        let new_width = ($(this).width() - 300);
                        $('.employee_schedule_tbl').css('max-width', new_width + 'px');
                    });
                }

                if ((response.heading !== undefined) && (response.heading !== "")) {
                    $('#heading-' + spanId).html(response.heading);
                }

                if ((response.href !== undefined) && (response.href !== "")) {
                    $('#heading-' + spanId).attr('href', response.href);
                }

                let sort_arr = [];
                if ((response.processed_array != undefined && (response.processed_array != "")) && (response.processed_array.sort_arr != undefined && response.processed_array.sort_arr !== "" && (response.processed_array.sort_arr.length > 0))) {
                    sort_arr = response.processed_array.sort_arr;
                }

                if ((response.html !== undefined) && (response.html !== "")) {
                    if (($('#' + id).length) > 0) {
                        if ($.fn.DataTable.isDataTable('#' + id)) {
                            $('#' + id).dataTable().fnClearTable();
                            $('#' + id).dataTable().fnDestroy();
                        }
                    }

                    $('#' + id).html(response.html);
                    if (response.is_not_tbl == undefined) {
                        $('#' + id).addClass('data_table_element_' + tabId);
                        table = $('#' + id).DataTable({
                            pageLength: 10,
                            pagingType: "full",
                            bProcessing: false,
                            //dom: 'lfrtBip',
                            buttons: [],
                            bFilter: true,
                            responsive: false,
                            dom: "l<'input-group' f <'input-group-append'>>rtip",
                            language: {
                                loadingRecords: "Loading...",
                                processing: "Processing...",
                                search: "_INPUT_",
                                searchPlaceholder: "Search...",
                                info: "Showing _START_ to _END_ of _TOTAL_",
                                infoEmpty: "Showing 0 to 0 of 0",
                            },
                            order: sort_arr,
                            columnDefs: [{
                                targets: 0,
                                className: 'left_padding_40'
                            }],
                        });
                    } else {
                        $('#' + id).removeClass('table');
                    }
                }
            }
        },
        loadEachTable(mod_id, custom_id, tab_id) {
            let base_url = "{{ route('shift.module',[':module_id',':customer_id']) }}";
            let base_url1 = base_url.replace(':module_id', mod_id);
            let url = base_url1.replace(':customer_id', custom_id);
            let name = $('#emp_id').val();
            let from_date = $('#fr_date').val();
            let to_date = $('#t_date').val();
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    'name': name,
                    'from_date': from_date,
                    'to_date': to_date,
                    'tab_id': tab_id
                },
                success: function(response) {},
                complete: function(complete_response) {
                    let answers = [];
                    let ans = [];
                    let cols = [];
                    let answer = [];
                    answers = complete_response.responseJSON.data;

                    if (answers[0].Date !== null) {
                        module_order = complete_response.responseJSON.module_order;
                    } else {
                        module_order = 0;
                    }

                    let exampleRecord = answers[0];
                    //get keys in object. This will only work if your statement remains true that all objects have identical keys
                    let keys = Object.keys(exampleRecord);
                    //for each key, add a column definition
                    keys.forEach(function(k) {
                        cols.push({
                            title: k,
                            //optionally do some type detection here for render function
                        });

                    });
                    //  logger.log(data);

                    $.each(answers, function(key, value) {
                        inner_array = [];
                        $.each(value, function(inner_key, inner_value) {
                            inner_array.push(inner_value);

                        });
                        answer.push(inner_array);
                    });

                    $.fn.dataTable.ext.errMode = 'hide';

                    if ($.fn.DataTable.isDataTable('#modu-' + mod_id)) {
                        $('#modu-' + mod_id).DataTable().destroy();
                        $('#modu-' + mod_id).empty();
                    };
                    //initialize DataTables
                    let table = $('#modu-' + mod_id).DataTable({
                        destroy: true,
                        bAutoWidth: false,
                        columns: cols,

                    });
                    //add data and draw
                    table.clear().draw();
                    if (answers.length > 0) {
                        table.rows.add(answer).draw();
                    }
                    $('.notesspan').closest('table').find('th').eq($('.notesspan').parent().index()).css('width', '20%');
                    $('#modu-' + mod_id).find('th').addClass('word-wrap');
                    $('#modu-' + mod_id).DataTable({
                        destroy: true,
                        order: module_order,
                        dom: "l<'input-group' f <'input-group-append'>>rtip",
                        language: {
                            search: "_INPUT_",
                            searchPlaceholder: "Search..."
                        },
                        "aoColumnDefs": [{
                            "sClass": "left_padding_40",
                            "aTargets": [0]
                        }]
                    });

                    if (inner_array.every(element => element === null)) {
                        table.clear().draw();
                    }
                }
            });
        },
        init() {
            let root = this;
            //Store customer
            if (selected_customer !== "") {
                localStorage.setItem('selected_customer', selected_customer);

                $('#dashboard-filter-customer').val(null).trigger('change');
                $.fn.dataTable.ext.errMode = 'throw';

                //load dynamic contents
                this.loadTabs(selected_customer);
            }

            //Sync the filter data in server.
            $('#dashboard-filter-customer').on('change', function() {
                abortRequests();
                root.syncDashboardCustomerFilter($(this).val());
            });
            let reloadExcludedWidget = JSON.parse('{!! json_encode($reloadExcludedWidget) !!}');

            //Auto refresh
            setInterval(function() {
                let selectedTab = $('.tab-config.active').attr('data-tab');
                if (selectedTab !== null && selectedTab !== undefined) {
                    logger.log(`Refresh: Auto refresh staretd for tab: [${selectedTab}]`);
                    root.loadWidgetFieldsOfTab(selectedTab, true, reloadExcludedWidget);
                }
            }, 100000);
        }
    };

    const WidgetFactory = {
        _camelize(str) {
            let arr = str.split('-');
            let capital = arr.map((item, index) => index ? item.charAt(0).toUpperCase() + item.slice(1).toLowerCase() : item);
            // ^-- change here.
            return capital.join("");
        },
        //Generating widgets by type and payload
        build(widgetTag, payload) {
            logger.log(`Gen: Building widget tag: [${widgetTag}] of data type ${typeof payload.data}`);

            let targetFn = this._camelize(widgetTag);

            logger.log(`Gen: Trying to run build function: [${targetFn}]`);

            if (widgets.hasOwnProperty(targetFn)) {
                widgets[targetFn](payload);
            } else {
                logger.error(`Gen: widgets does not contain function ${targetFn}`);
            }
        }
    };

    const dsCore = {
        clock: 0,
        interval_msec: 1000,
        date: null,
        datetimeformat(date_obj, onlytime) {
            if (onlytime) {
                let hr_split_arr = date_obj.split(":");
                datetime_str = hr_split_arr[0] + ':' + hr_split_arr[1];
                return datetime_str;
            }
            let date_str = date_obj.getDate();
            if (date_str < 10) date_str = '0' + date_str;
            let month_str = (date_obj.getMonth()) + 1;
            if (month_str < 10) month_str = '0' + month_str;
            let year_str = date_obj.getFullYear();
            let hour_str = date_obj.getHours();
            if (hour_str < 10) hour_str = '0' + hour_str;
            let minute_str = date_obj.getMinutes();
            if (minute_str < 10) minute_str = '0' + minute_str;
            let datetime_str = year_str + '-' + month_str + '-' + date_str + ' ' + hour_str + ':' + minute_str;
            return datetime_str;
        },
        tConvert(time) {
            if (!time) {
                return ''
            }
            // Check correct time format and split into components
            time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];
            if (time.length > 1) { // If time format correct
                time = time.slice(1); // Remove full string match value
                time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
                time[0] = +time[0] % 12 || 12; // Adjust hours
            }
            time.splice(3, 1);
            return time.join(''); // return adjusted time or original string
        },
        updateClock() {
            let root = this;
            // clear timer
            clearTimeout(this.clock);
            if (this.date == null) {
                this.date = new Date('{{ \Carbon::now() }}');
            } else {
                this.date = new Date(this.date.getTime() + 1 * 1000);
            }
            //logger.log(date);
            let hours = Number(this.date.getHours());
            let minutes = Number(this.date.getMinutes());
            let ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            let strTime = (('0' + hours).slice(-2)) + ' : ' + (('0' + minutes).slice(-2)) + ' ' + ampm;
            $("#myclock").html(strTime);
            // set timer
            this.clock = setTimeout(function() {
                root.updateClock();
            }, this.interval_msec);
        },
        init() {
            let root = this;
            let isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            if (!isSafari) {
                clock = setTimeout(function() {
                    root.updateClock();
                }, root.interval_msec);
            }
            $('#content .row').each(function() {
                let c = 12 / Number($(this).find('.card-table').length);
                $(this).find('.card-padding').addClass('col-lg-' + c).addClass('col-lg-' + c);
            });
        }
    }
    $(document).ready(function () {
        let customer_ids={!! json_encode($selectedCustomerArray) !!};
              
        dsCore.init();
        dashboard.init();
        $.each($("#dashboard-filter-customer"), function(){
            if(customer_ids!="" || customer_ids!=null){
                $(this).val(customer_ids).select2();
            }
        }).after(function(){
            if(customer_ids!="" || customer_ids!=null){
                $("#dashboard-filter-customer").trigger("change");
            }
        });  

    });
    //Run on document ready
    $(function() {
        
    });
</script>



@endsection
