@extends('pms.layouts.app')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .content-wrap {
            min-height: calc(100vh - 130px);
        }

        .page-wrap {
            border: 1px solid #e2e2e2;
            padding: 16px 24px;
            -webkit-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
            box-shadow: 0 1px 5px #0000001a;
            border-radius: 6px;
        }

        .page-title {
            padding-bottom: 1em;
            border-bottom: 1px solid #e2e2e2;
        }

        .dashboard-wrap .card-chartBox {
            font-weight: 700;
        }

        .dashboard-wrap .bg-success-light {
            background-color: #e3eee3;
        }

        .dashboard-wrap .bg-danger-light {
            background-color: #fee9e9;
        }

        .rounded-pill {
            border-radius: 50rem !important;
        }

        .dashboard-wrap .tabs {
            list-style-type: none;
            margin: 0;
            padding: 2px;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            background: #e2e2e2;
            border-radius: 6px;
        }

        .dashboard-wrap .tabs li {
            padding: 2px;
        }

        .tabs li a.active {
            display: inline-block;
            background: #fff;
            color: #000;
            -webkit-box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
            box-shadow: 1px 1px 3px #0003;
        }

        .dashboard-wrap .tabs li a {
            display: inline-block;
            padding: 5px 25px;
            text-transform: uppercase;
            font-size: 13px;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
        }

        .dashboard-wrap .colorDot {
            width: 16px;
            height: 16px;
            display: inline-block;
            border-radius: 4px;
        }

        .filterOption {
            position: absolute;
            z-index: 9;
            background: #fff;
            border-radius: 6px;
            border: 1px solid #e1e1e1;
            -webkit-box-shadow: 3px 3px 5px rgba(0, 0, 0, .12);
            box-shadow: 3px 3px 5px #0000001f;
            padding: 10px;
            
        }

        .btn {
            padding: 9px 23px;
        }

        .filter-btn {
            background-color: #c79f62;
            color: #fff;
            text-decoration: none;
            padding: 8px 23px;
            transition: 0.5s ease;
            border-radius: 15px;
        }

        .filter-btn:hover {
            background-color: #000;
        }

        /* Spinner CSS */
        .spinner {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: #c79f62;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
    </style>
    <section class="section">
        <div class="container-fluid dashboard-wrap">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">Analytics</h1>
                    </div>
                </div>
            </div>
            <div class="content-box p-3">
                <div class="page-title">
                    <div class="row gy-3 align-items-center justify-content-end">
                        <div class="col">
                            <div class="search-filter">
                                <div class="row gy-3 gx-2">
                                    <div class="col-auto position-relative">
                                        <div class="row justify-content-xl-end gy-3 gx-2">
                                            <div class="col-12 col-lg col-xl-auto">
                                                <div class="row gy-3 gx-2">
                                                    <div class="col">
                                                        <input type="date" class="form-control flatpickr"
                                                            name="from-date" placeholder="From - To" id="dateRangePicker" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-auto">
                                                <div class="btn-group gap-1">
                                                    <button class="btn btn-primary btn-icon" id="searchBtn">
                                                        <span class="bi bi-search"></span>
                                                    </button>
                                                    <button class="btn btn-icon btn-clear btn-warning" id="resetBtn">
                                                        <span class="bi bi-arrow-clockwise"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="search-filter">
                                <div class="row gx-2 align-items-center">
                                    <div class="col-auto">
                                        <select class="form-select" id="daysSelect">
                                            <option value="" disabled>Select days</option>
                                            <option value="7_next">Next 7 days</option>
                                            <option value="7">Last 7 days</option>
                                            <option value="30" selected>Last 30 days</option>
                                            <option value="60">Last 60 days</option>
                                            <option value="90">Last 90 days</option>
                                        </select>
                                    </div>
                                    <div class="col-auto position-relative">
                                        <a href="javascript:void(0)" class="btn-sm btn-outline-dark filter filter-btn"
                                            onclick="toggleFilter()">
                                            <i class="bi bi-sort-down me-2"></i> Filter
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="page-content position-relative">
                        <div class="row g-4 chartRow">
                            <div class="col-12 col-xl-4 position-relative">
                                <a href="{{ route('pms.analytics.net_revenue') }}" id="netRevenueLink"
                                    class="card card-chartBox bg-transparent h-100 text-decoration-none">
                                    <div class="card-body p-0">
                                        <div class="row align-items-center m-3">
                                            <div class="col-12">
                                                <h5>Net Revenue</h5>
                                            </div>
                                            <div class="col-auto">
                                                <h2 class="text-primary" id="net-revenue-value">₹0</h2>
                                            </div>
                                            <div class="col-auto">
                                                <span class="rounded-pill py-1 px-2" id="net-revenue-percentage"><i
                                                        class="bi"></i> 0%</span>
                                            </div>
                                        </div>
                                        <div class="chartBox position-relative">
                                            <canvas id="netRevenueChart" role="img" width="590"
                                                height="160"></canvas>
                                            <div class="spinner" id="netRevenueSpinner"></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12 col-xl-4 position-relative">
                                <a href="{{ route('pms.analytics.average_price_per_night') }}" id="avgPriceLink"
                                    class="card card-chartBox bg-transparent h-100 text-decoration-none">
                                    <div class="card-body p-0">
                                        <div class="row align-items-center m-3">
                                            <div class="col-12">
                                                <h5>Average Price Per Night</h5>
                                            </div>
                                            <div class="col-auto">
                                                <h2 class="text-secondary" id="average-price-per-night-value">₹0</h2>
                                            </div>
                                            <div class="col-auto">
                                                <span class="rounded-pill py-1 px-2"
                                                    id="average-price-per-night-percentage"><i class="bi"></i> 0%</span>
                                            </div>
                                        </div>
                                        <div class="chartBox position-relative">
                                            <canvas id="avgPriceChart" role="img" width="590"
                                                height="160"></canvas>
                                            <div class="spinner" id="avgPriceSpinner"></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12 col-xl-4">
                                <div id="chartSection">
                                    <a href="{{ route('pms.analytics.bookings_created') }}" id="bookingsCreatedLink"
                                        class="card card-chartBox bg-transparent h-100 text-decoration-none">
                                        <div class="card-body p-0">
                                            <div class="row align-items-center m-3">
                                                <div class="col-12">
                                                    <h5>Bookings Created</h5>
                                                </div>
                                                <div class="col-auto">
                                                    <h2 class="text-primary" id="bookings-created-value">0</h2>
                                                </div>
                                                <div class="col-auto">
                                                    <span class="rounded-pill py-1 px-2"
                                                        id="bookings-created-percentage"><i class="bi"></i> 0%</span>
                                                </div>
                                            </div>
                                            <div class="chartBox position-relative">
                                                <canvas id="bookingsChart" role="img" width="590"
                                                    height="160"></canvas>
                                                <div class="spinner" id="bookingsSpinner"></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="filterOption card card-chartBox bg-white text-decoration-none"
                                    id="filterOption" style="display: none;">
                                    <div class="card-body p-0">
                                        <div class="row align-items-center m-3">
                                            <div class="col">
                                                <h6 class="mb-0"><b>Locations</b></h6>
                                            </div>
                                            <div class="col-auto">
                                                <a href="javascript:void(0)" id="clearLocation"
                                                    class="text-primary text-decoration-none fw-bold fs-14">
                                                    <i class="bi bi-x-lg me-1"></i><small>Clear All</small>
                                                </a>
                                            </div>
                                        </div>
                                        <div id="location-list" class="ps-3 pb-3 d-flex flex-wrap">
                                        </div>
                                        <h6 class="mb-2 px-3"><b>Property Name</b></h6>
                                        <div class="formwrap px-3 pb-3">
                                            <div class="row g-0 align-items-center">
                                                <div class="col">
                                                    <div class="property-filtter-wrap">
                                                        <div class="input-group position-relative">
                                                            <input type="text" id="propertyInput" class="form-control"
                                                                placeholder="Search Property">
                                                            <ul id="propertySuggestionList"
                                                                class="list-group position-absolute w-100"
                                                                style="z-index: 1000; top: 100%; display: none;"></ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-xl-4 position-relative">
                                <a class="card card-chartBox bg-transparent h-100 text-decoration-none">
                                    <div class="card-body p-0">
                                        <div class="row align-items-center m-3">
                                            <div class="col-12">
                                                <h5>Nights Filled</h5>
                                            </div>
                                            <div class="col-auto">
                                                <h2 class="text-secondary" id="nights-filled-value">0</h2>
                                            </div>
                                            <div class="col-auto">
                                                <span class="rounded-pill py-1 px-2" id="nights-filled-percentage"><i
                                                        class="bi"></i> 0%</span>
                                            </div>
                                        </div>
                                        <div class="chartBox position-relative">
                                            <canvas id="nightsChart" role="img" width="590"
                                                height="160"></canvas>
                                            <div class="spinner" id="nightsSpinner"></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12 col-xl-4 position-relative">
                                <a class="card card-chartBox bg-transparent h-100 text-decoration-none">
                                    <div class="card-body p-0">
                                        <div class="row align-items-center m-3">
                                            <div class="col-12">
                                                <h5>Occupancy Rates</h5>
                                            </div>
                                            <div class="col-auto">
                                                <h2 class="text-primary" id="occupancy-rates-value">0%</h2>
                                            </div>
                                            <div class="col-auto">
                                                <span class="rounded-pill py-1 px-2" id="occupancy-rates-percentage"><i
                                                        class="bi"></i> 0%</span>
                                            </div>
                                        </div>
                                        <div class="chartBox position-relative">
                                            <canvas id="occupancyChart" role="img" width="590"
                                                height="160"></canvas>
                                            <div class="spinner" id="occupancySpinner"></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12 col-xl-4 d-flex">
                                <div class="card card-chartBox bg-transparent w-100">
                                    <div class="average-content">
                                        <div class="card-body p-0 d-flex flex-column justify-content-center">
                                            <div class="row align-items-center m-3">
                                                <div class="col-12">
                                                    <h5>Average Length of Stay</h5>
                                                </div>
                                                <div class="col-auto">
                                                    <h2 class="text-secondary" id="avg-stay-value">0 Nights</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-0 d-flex flex-column justify-content-center border-top">
                                            <div class="row align-items-center m-3">
                                                <div class="col-12">
                                                    <h5>Average Lead Time</h5>
                                                </div>
                                                <div class="col-auto">
                                                    <h2 class="text-primary" id="avg-lead-value">0 Days</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-8 d-flex">
                                <div class="card card-chartBox w-100 bg-transparent">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center mb-4">
                                            <div class="col">
                                                <h5>Weekly Revenue Analysis</h5>
                                            </div>
                                            <div class="col-auto">
                                                <ul class="tabs" data-type="line">
                                                    <li>
                                                        <a href="javascript:void(0);" class="active"
                                                            data-type="net">Net</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" data-type="gross">Gross</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="tabContent">
                                            <canvas id="weeklyRevenueChart" role="img" width="1158"
                                                height="579"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-4 d-flex">
                                <div class="card card-chartBox bg-transparent w-100">
                                    <div class="card-body p-4">
                                        <div class="row gy-3 gy-md-0 align-items-center">
                                            <div class="col-12">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <h5>Channel Distribution / Revenue</h5>
                                                    </div>
                                                    <div class="col-auto">
                                                        <ul class="tabs" data-type="doughnut">
                                                            <li>
                                                                <a href="javascript:void(0);" class="active"
                                                                    data-type="net">Net</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0);" data-type="gross">Gross</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                   <div class="totalChannelRevenue col-auto">
                                                          <h2></h2>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="chartBoxdoughnut mt-4">
                                            <canvas id="channelRevenueChart" role="img" width="442"
                                                height="200"></canvas>
                                        </div>
                                        <div class="channelList mt-4" id="channelList">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script>
        let charts = {
            netRevenue: null,
            avgPrice: null,
            bookings: null,
            nights: null,
            occupancy: null,
            weeklyRevenue: null,
            channelRevenue: null
        };
        const weeklyChartConfig = {
            line: {
                type: 'line',
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'nearest',
                        intersect: false,
                        axis: 'x'
                    },
                    plugins: {
                        tooltip: {
                            enabled: true,
                            display: false
                        },
                        legend: {
                            display: false
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    elements: {
                        point: {
                            radius: 0
                        }
                    }
                }
            },
        }

        const chartConfigs = {
            line: {
                type: 'line',
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false
                        }
                    },
                    layout: {
                        padding: 0
                    },
                    elements: {
                        point: {
                            radius: 0
                        }
                    }
                }
            },
            doughnut: {
                type: 'doughnut',
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            enabled: true
                        }
                    }
                }
            }
        };

        const datePicker = flatpickr('#dateRangePicker', {
            mode: 'range',
            dateFormat: 'd/m/Y',
            onChange: function(selectedDates) {
                if (selectedDates.length === 2) {
                    // fetchAllData();
                }
            }
        });

        function fetchAllData() {
            const daysSelect = document.getElementById('daysSelect');
            const dates = datePicker.selectedDates || [];

            let params = {};

            if (dates.length === 2) {
                params.from_date = standardFormat(dates[0]);
                params.to_date = standardFormat(dates[1]);
                params.days = '';
                if (daysSelect) {
                    daysSelect.value = '';
                }
            } else {
                params.days = daysSelect?.value || '30';
                params.from_date = null;
                params.to_date = null;
            }
            if (locationID) {
                params.location_id = locationID;
            }

            if (selectedRuPropertyId) {
                params.ru_property_id = selectedRuPropertyId;
                params.pType = selectedpType;
            }
            console.log(params.days,"params");


            const updateCardLink = (elementId, baseUrl) => {
                const link = document.getElementById(elementId);
                if (!link) return;

                let url = new URL(baseUrl, window.location.origin);

                if (params.from_date && params.to_date) {
                   // url.searchParams.set('from_date', params.from_date);
                   // url.searchParams.set('to_date', params.to_date);
                    url.searchParams.delete('days');
                } else {
                    url.searchParams.set('days', params.days || '30');
                   // url.searchParams.delete('from_date');
                  //  url.searchParams.delete('to_date');
                }

                link.href = url.toString();
            };

            // Update all 3 cards
            updateCardLink('netRevenueLink', '/pms/net-revenue');
            updateCardLink('avgPriceLink', '/pms/average-price-per-night');
            updateCardLink('bookingsCreatedLink', '/pms/bookings-created');






            // Show spinners for the specified charts
            ['netRevenueSpinner', 'avgPriceSpinner', 'bookingsSpinner', 'nightsSpinner', 'occupancySpinner'].forEach(id => {
                const spinner = document.getElementById(id);
                if (spinner) spinner.style.display = 'block';
            });

            axios.post('https://unreal.tempsite.in/pms/linechart', params)
                .then(response => {
                    const data = response.data.data || [];
                    updateLineCharts(data);
                    // Hide spinners after response
                    ['netRevenueSpinner', 'avgPriceSpinner', 'bookingsSpinner', 'nightsSpinner', 'occupancySpinner'].forEach(id => {
                        const spinner = document.getElementById(id);
                        if (spinner) spinner.style.display = 'none';
                    });
                })
                .catch(error => {
                    console.error('Error fetching line chart data:', error);
                    // Hide spinners on error
                    ['netRevenueSpinner', 'avgPriceSpinner', 'bookingsSpinner', 'nightsSpinner', 'occupancySpinner'].forEach(id => {
                        const spinner = document.getElementById(id);
                        if (spinner) spinner.style.display = 'none';
                    });
                });

            axios.post('https://unreal.tempsite.in/pms/weeklyreport', {
                    ...params,
                    type: 'net'
                })
                .then(response => updateWeeklyChart(response.data.data || [], 'net'))
                .catch(error => console.error('Error fetching weekly report:', error));

            axios.post('https://unreal.tempsite.in/pms/channelrevenue', {
                    ...params,
                    type: 'net'
                })
                .then(response => updateChannelChart(response.data.data || {}, 'net'))
                .catch(error => console.error('Error fetching channel revenue:', error));

            axios.post('https://unreal.tempsite.in/pms/dashboard', params)
                .then(response => {
                    const data = response.data.data || {};
                    updateAverageCard(data);
                })
                .catch(error => {
                    console.error('Error fetching average data:', error);
                    updateAverageCard({});
                });
        }

        function standardFormat(dateObj) {
            if (!dateObj) return null;
            const yyyy = dateObj.getFullYear();
            const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
            const dd = String(dateObj.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        function updateAverageCard(data) {
            const avgStayValue = document.getElementById('avg-stay-value');
            const avgLeadValue = document.getElementById('avg-lead-value');
            const loading = document.querySelector('.average-loading');
            const content = document.querySelector('.average-content');

            if (avgStayValue) {
                avgStayValue.textContent = `${data[0].value} ${data[0].name}`;
            }
            if (avgLeadValue) {
                avgLeadValue.textContent = `${data[1].value} ${data[1].name}`;
            }

            if (loading && content) {
                loading.style.display = 'none';
                content.style.display = 'block';
            }
        }

        // Update Line Charts
        function updateLineCharts(data) {
            const chartData = [{
                    id: 'netRevenue',
                    canvas: 'netRevenueChart',
                    valueId: 'net-revenue-value',
                    percentageId: 'net-revenue-percentage',
                    colors: {
                        border: 'rgb(75, 192, 192)',
                        bg: 'rgba(75, 192, 192, 0.3)'
                    }
                },
                {
                    id: 'avgPrice',
                    canvas: 'avgPriceChart',
                    valueId: 'average-price-per-night-value',
                    percentageId: 'average-price-per-night-percentage',
                    colors: {
                        border: 'rgb(153, 102, 255)',
                        bg: 'rgba(153, 102, 255, 0.3)'
                    }
                },
                {
                    id: 'bookings',
                    canvas: 'bookingsChart',
                    valueId: 'bookings-created-value',
                    percentageId: 'bookings-created-percentage',
                    colors: {
                        border: 'rgb(255, 159, 64)',
                        bg: 'rgba(255, 159, 64, 0.3)'
                    }
                },
                {
                    id: 'nights',
                    canvas: 'nightsChart',
                    valueId: 'nights-filled-value',
                    percentageId: 'nights-filled-percentage',
                    colors: {
                        border: 'rgb(255, 205, 86)',
                        bg: 'rgba(255, 205, 86, 0.3)'
                    }
                },
                {
                    id: 'occupancy',
                    canvas: 'occupancyChart',
                    valueId: 'occupancy-rates-value',
                    percentageId: 'occupancy-rates-percentage',
                    colors: {
                        border: 'rgb(255, 99, 132)',
                        bg: 'rgba(255, 99, 132, 0.3)'
                    }
                }
            ];

            data.forEach((item, index) => {
                const config = chartData[index];
                if (!config || !item) return; // Skip if config or item is undefined

                const labels = item.data?.map(d => d.label) || [];
                const values = item.data?.map(d => d.price) || [];
                const percentage = item.percentage || 0;
                const value = item.type === 'percentage' ? `${item.value || 0}%` :
                    item.title === 'Net Revenue' || item.title === 'Average Price Per Night' ?
                    `₹${(item.value || 0).toLocaleString()}` :
                    (item.value || 0).toLocaleString();

                const valueElement = document.getElementById(config.valueId);
                const percentageSpan = document.getElementById(config.percentageId);
                const canvas = document.getElementById(config.canvas);

                if (valueElement) {
                    valueElement.textContent = value;
                }

                if (percentageSpan) {
                    percentageSpan.textContent = `${Math.abs(percentage)}%`;
                    percentageSpan.className =
                        `rounded-pill py-1 px-2 ${percentage >= 0 ? 'text-success bg-success-light' : 'text-danger bg-danger-light'}`;
                    const icon = percentageSpan.querySelector('i');
                    if (icon) {
                        icon.className = `bi bi-arrow-${percentage >= 0 ? 'up' : 'down'}`;
                    } else {
                        const newIcon = document.createElement('i');
                        newIcon.className = `bi bi-arrow-${percentage >= 0 ? 'up' : 'down'}`;
                        percentageSpan.prepend(newIcon);
                    }
                }

                if (charts[config.id]) charts[config.id].destroy();
                if (canvas) {
                    charts[config.id] = new Chart(canvas.getContext('2d'), {
                        ...chartConfigs.line,
                        data: {
                            labels,
                            datasets: [{
                                label: item.title || '',
                                data: values,
                                borderColor: config.colors.border,
                                backgroundColor: config.colors.bg,
                                tension: 0.4,
                                fill: true
                            }]
                        }
                    });
                }
            });
        }

        // Update Weekly Revenue Chart
        function updateWeeklyChart(data, type) {
            const labels = data.map((d, index) => `Week ${index + 1}`) || [];
            const values = data.map(d => d.total_revenue) || [];
            const colors = type === 'net' ? {
                border: 'rgb(71, 100, 72)',
                bg: 'rgb(227, 238, 227)'
            } : {
                border: 'rgb(54, 162, 235)',
                bg: 'rgba(54, 162, 235, 0.3)'
            };

            if (charts.weeklyRevenue) charts.weeklyRevenue.destroy();
            const canvas = document.getElementById('weeklyRevenueChart');
            if (canvas) {
                charts.weeklyRevenue = new Chart(canvas.getContext('2d'), {
                    ...weeklyChartConfig.line,
                    data: {
                        labels,
                        datasets: [{
                            label: type === 'net' ? 'Net Revenue' : 'Gross Revenue',
                            data: values,
                            borderColor: colors.border,
                            backgroundColor: colors.bg,
                            tension: 0.4,
                            fill: true
                        }]
                    }
                });
            }
        }

        // Update Channel Revenue Chart
        function updateChannelChart(data, type) {
            console.log(data.total_channel_revenue,type);
            const channelData = data.channel_distribution_revenue || [];
            const labels = channelData.map(c => c.channel) || [];
            const values = channelData.map(c => c.total_amount) || [];
            const counts = channelData.map(c => c.total_count) || [];
            const colors = ['rgb(52, 55, 65)', 'rgb(217, 167, 80)', 'rgb(58, 96, 58)', 'rgb(151, 100, 37)',
                'rgb(35, 31, 32)', 'rgb(255, 99, 132)'
            ];

            const channelList = document.getElementById('channelList');
            if (channelList) {
                channelList.innerHTML = channelData.map((c, i) => `
                    <div class="row gx-3 align-items-center my-2">
                        <div class="col-auto pb-0">
                            <span class="colorDot" style="background-color: ${colors[i % colors.length]}"></span>
                        </div>
                        <div class="col">
                            <h5 class="mb-0" style="color: ${colors[i % colors.length]} !important">${c.channel || 'Unknown'}</h5>
                        </div>
                        <div class="col-auto">
                            ${c.total_count || 0} / <span style="color: ${colors[i % colors.length]}">${(c.total_amount || 0).toLocaleString()}</span>
                        </div>
                    </div>
                `).join('');
            }

const totalRevenueElement = document.querySelector('.totalChannelRevenue h2');
if (totalRevenueElement) {
    let revenueColor = type === 'net' ? 'green' : '#C79F62';
    totalRevenueElement.textContent = `₹${(data.total_channel_revenue || 0).toLocaleString()}`;
    totalRevenueElement.style.color = revenueColor;  // Fixed line
}

             
            if (charts.channelRevenue) charts.channelRevenue.destroy();
            const canvas = document.getElementById('channelRevenueChart');
            if (canvas) {
                charts.channelRevenue = new Chart(canvas.getContext('2d'), {
                    ...chartConfigs.doughnut,
                    data: {
                        labels,
                        datasets: [{
                            label: 'Revenue Distribution',
                            data: values,
                            backgroundColor: colors,
                            borderWidth: 1
                        }]
                    }
                });
            }
        }

        // Tab click handlers
        document.querySelectorAll('.tabs[data-type="line"] a').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tabs[data-type="line"] a').forEach(t => t.classList.remove(
                    'active'));
                tab.classList.add('active');
                const type = tab.dataset.type;
                const params = getParams();
                axios.post('https://unreal.tempsite.in/pms/weeklyreport', {
                        ...params,
                        type
                    })
                    .then(response => updateWeeklyChart(response.data.data || [], type))
                    .catch(error => console.error('Error fetching weekly report:', error));
            });
        });

        document.querySelectorAll('.tabs[data-type="doughnut"] a').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tabs[data-type="doughnut"] a').forEach(t => t.classList.remove(
                    'active'));
                tab.classList.add('active');
                const type = tab.dataset.type;
                const params = getParams();
                axios.post('https://unreal.tempsite.in/pms/channelrevenue', {
                        ...params,
                        type
                    })
                    .then(response => updateChannelChart(response.data.data || {}, type))
                    .catch(error => console.error('Error fetching channel revenue:', error));
            });
        });

        // Get parameters for API calls
        function getParams() {
            const days = document.getElementById('daysSelect')?.value || '30';
            const dates = datePicker.selectedDates || [];
            let params = {
                days
            };

            if (dates.length === 2) {
                params.from_date = dates[0].toISOString().split('T')[0];
                params.to_date = dates[1].toISOString().split('T')[0];
                params.days = '';
            }

            return params;
        }
        document.getElementById('searchBtn')?.addEventListener('click', fetchAllData);
        document.getElementById('resetBtn')?.addEventListener('click', () => {
            const daysSelect = document.getElementById('daysSelect');
            if (daysSelect) {
                daysSelect.value = '30';
            }
            datePicker.clear();
            fetchAllData();
        });

        document.getElementById('daysSelect')?.addEventListener('change', () => {
            datePicker.clear();
            fetchAllData();
        });
       
        let locationID = null;
        let properties = [];
        let propertyFilterList = [];
        let selectedRuPropertyId = null;
        let selectedpType = null;

        function toggleFilter() {
            const chartSection = document.getElementById('chartSection');
            const filterOption = document.getElementById('filterOption');

            if (filterOption.style.display === 'none') {
                chartSection.style.display = 'none';
                filterOption.style.display = 'block';
                fetchLocations(); 
                fetchProperties();
            } else {
                chartSection.style.display = 'block';
                filterOption.style.display = 'none';
            }
        }
        async function fetchLocations() {
            try {
                const response = await axios.get('https://unreal.tempsite.in/pms/dashboard/location');
                if (response.data.status) {
                    const locations = response.data.location;
                    const locationList = document.getElementById('location-list');
                    locationList.innerHTML = locations.map(loc => `
                    <span onclick="setLocation(${loc.location_id})"
                        class="badge rounded-pill px-3 py-2 me-2 mb-2"
                        style="
                            cursor:pointer;
                            background-color: ${locationID == loc.location_id ? '#000' : '#C79F62'};
                            color: ${locationID == loc.location_id ? '#fff' : '#fff'};
                        ">
                        ${loc.location_name}
                    </span>
                `).join('');
                }
            } catch (error) {
                console.error('Error fetching locations:', error);
            }
        }

        async function fetchProperties() {
            try {
                const response = await axios.get('https://unreal.tempsite.in/pms/dashboard/property');
                if (response.data.status) {
                    properties = response.data.property;
                    propertyFilterList = properties;
                }
            } catch (error) {
                console.error('Error fetching properties:', error);
            }
        }

        function setLocation(id) {
            locationID = id;
            fetchLocations(); 
            fetchProperties(); 
            fetchAllData();
        }
        // Listen for clear button click
        document.getElementById('clearLocation').addEventListener('click', function() {
            locationID = null; 
            document.getElementById('propertyInput').value = '';
             selectedRuPropertyId = null;
              selectedpType = null;
            fetchLocations(); 
            fetchAllData(); 
        });

        document.getElementById('propertyInput').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const suggestionList = document.getElementById('propertySuggestionList');
            if (query.length === 0) {
                suggestionList.style.display = 'none';
                return;
            }
            const filtered = propertyFilterList.filter(p => p.property_name.toLowerCase().includes(query));

            if (filtered.length > 0) {
                suggestionList.innerHTML = filtered.map(p => `
                    <li class="list-group-item list-group-item-action" 
                    style="cursor:pointer;" 
                        onclick="selectProperty(${p.ru_property_id}, '${p.pType}', '${p.property_name.replace(/'/g, "\\'")}')">
                        ${p.property_name}
                    </li>
                `).join('');
                suggestionList.style.display = 'block';
            } else {
                suggestionList.innerHTML = '<li class="list-group-item text-muted">No property found</li>';
                suggestionList.style.display = 'block';
            }
        });

        function selectProperty(id, pType, name) {
            selectedRuPropertyId = id;
            selectedpType = pType;
            document.getElementById('propertyInput').value = name;
            document.getElementById('propertySuggestionList').style.display = 'none';
            fetchAllData(); 
        }
        document.addEventListener('DOMContentLoaded', () => {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            }
            fetchAllData();
        });
    </script>
@endsection