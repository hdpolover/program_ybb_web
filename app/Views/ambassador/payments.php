<?= $this->include('partials/main') ?>

<head>
    <?php echo view('partials/title-meta', array('title' => 'Payment Analytics')); ?>
    <?= $this->include('partials/head-css') ?>
    <link href="/assets/libs/apexcharts/apexcharts.css" rel="stylesheet" type="text/css" />
    <link href="/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div id="layout-wrapper">
        <?= $this->include('partials/ambassador-menu') ?>
        
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Payment Analytics</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="/ambassadors/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Payment Analytics</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <div class="card card-animate h-100">
                                <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0 fs-12">Total Participants</p>
                                        </div>
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-primary-subtle rounded fs-4">
                                                <i class="ri-group-line text-primary"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <h4 class="fs-20 fw-semibold ff-secondary mb-2" id="total-participants">
                                            <span class="counter-value" data-target="0">0</span>
                                        </h4>
                                        <span class="badge bg-primary-subtle text-primary fs-11">All referred</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <div class="card card-animate h-100">
                                <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0 fs-12">Paid Participants</p>
                                        </div>
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-success-subtle rounded fs-4">
                                                <i class="ri-check-double-line text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <h4 class="fs-20 fw-semibold ff-secondary mb-2" id="paid-participants">
                                            <span class="counter-value" data-target="0">0</span>
                                        </h4>
                                        <span class="badge bg-success-subtle text-success fs-11">Completed</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <div class="card card-animate h-100">
                                <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0 fs-12">Cancelled</p>
                                        </div>
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-secondary-subtle rounded fs-4">
                                                <i class="ri-close-circle-line text-secondary"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <h4 class="fs-20 fw-semibold ff-secondary mb-2" id="cancelled-participants">
                                            <span class="counter-value" data-target="0">0</span>
                                        </h4>
                                        <span class="badge bg-secondary-subtle text-secondary fs-11">Cancelled</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <div class="card card-animate h-100">
                                <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0 fs-12">Rejected</p>
                                        </div>
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-danger-subtle rounded fs-4">
                                                <i class="ri-error-warning-line text-danger"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <h4 class="fs-20 fw-semibold ff-secondary mb-2" id="rejected-participants">
                                            <span class="counter-value" data-target="0">0</span>
                                        </h4>
                                        <span class="badge bg-danger-subtle text-danger fs-11">Rejected</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <div class="card card-animate h-100">
                                <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0 fs-12">Payment Rate</p>
                                        </div>
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-info-subtle rounded fs-4">
                                                <i class="ri-percent-line text-info"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <h4 class="fs-20 fw-semibold ff-secondary mb-2" id="payment-rate">
                                            <span class="counter-value" data-target="0">0</span>%
                                        </h4>
                                        <span class="badge bg-info-subtle text-info fs-11">Success rate</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <div class="card card-animate h-100">
                                <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0 fs-12">Avg Payment Time</p>
                                        </div>
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-warning-subtle rounded fs-4">
                                                <i class="ri-time-line text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <h4 class="fs-20 fw-semibold ff-secondary mb-2" id="avg-payment-time">
                                            <span class="counter-value" data-target="0">0</span> days
                                        </h4>
                                        <span class="badge bg-warning-subtle text-warning fs-11">Average</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Charts and Tables -->
                    <div class="row g-3 mb-4">
                        <div class="col-xl-8">
                            <div class="card h-100">
                                <div class="card-header border-0">
                                    <h4 class="card-title mb-0">Payment Timeline</h4>
                                </div>
                                <div class="card-body">
                                    <div id="payment-timeline-chart" class="apex-charts" dir="ltr"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="card h-100">
                                <div class="card-header border-0">
                                    <h4 class="card-title mb-0">Payment Status Distribution</h4>
                                </div>
                                <div class="card-body">
                                    <div id="payment-status-chart" class="apex-charts" dir="ltr"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conversion Funnel -->
                    <div class="row g-3 mb-4">
                        <div class="col-xl-6">
                            <div class="card h-100">
                                <div class="card-header border-0">
                                    <h4 class="card-title mb-0">Conversion Funnel</h4>
                                </div>
                                <div class="card-body">
                                    <div id="conversion-funnel-container">
                                        <!-- Funnel data will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="card h-100">
                                <div class="card-header border-0">
                                    <h4 class="card-title mb-0">Top Converting Countries</h4>
                                </div>
                                <div class="card-body">
                                    <div id="top-countries-chart" class="apex-charts" dir="ltr"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="row g-3 mb-4">
                        <div class="col-xl-3 col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="alert alert-soft-info mb-0">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-info text-white rounded-circle fs-16">
                                                    <i class="ri-line-chart-line"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fs-14 text-info mb-1">Conversion Rate</h6>
                                                <p class="text-muted mb-0 fw-semibold" id="conversion-rate">0%</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="alert alert-soft-success mb-0">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-success text-white rounded-circle fs-16">
                                                    <i class="ri-check-line"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fs-14 text-success mb-1">Payment Success Rate</h6>
                                                <p class="text-muted mb-0 fw-semibold" id="payment-success-rate">0%</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="alert alert-soft-warning mb-0">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-warning text-white rounded-circle fs-16">
                                                    <i class="ri-calendar-line"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fs-14 text-warning mb-1">Avg Days to Payment</h6>
                                                <p class="text-muted mb-0 fw-semibold" id="avg-days-payment">0 days</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="alert alert-soft-primary mb-0">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-primary text-white rounded-circle fs-16">
                                                    <i class="ri-trending-up-line"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fs-14 text-primary mb-1">Monthly Growth</h6>
                                                <p class="text-muted mb-0 fw-semibold" id="monthly-growth">0%</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Payments Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-0">
                                    <h4 class="card-title mb-0">Recent Payment Activities</h4>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="table-responsive">
                                        <table id="recent-payments-datatable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Participant & Country</th>
                                                    <th>Program</th>
                                                    <th>Payment Method</th>
                                                    <th>Payment Date</th>
                                                    <th>Status</th>
                                                    <th>Days Ago</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                        Loading payment data...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
            </div>

            <?= $this->include('partials/footer') ?>
        </div>
    </div>

    <?= $this->include('partials/vendor-scripts') ?>

    <!-- DataTables JS -->
    <script src="/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

    <!-- ApexCharts JS -->
    <script src="/assets/libs/apexcharts/apexcharts.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing payment analytics...');
            
            // Simple check and load
            if (typeof $ !== 'undefined') {
                console.log('jQuery loaded successfully');
                if (typeof $.fn.DataTable !== 'undefined') {
                    console.log('DataTables loaded successfully');
                } else {
                    console.warn('DataTables not loaded - table features may be limited');
                }
            } else {
                console.error('jQuery not loaded');
            }
            
            loadPaymentData();
        });

        async function loadPaymentData() {
            try {
                console.log('Loading payment data...');
                
                const response = await fetch('/ambassadors/dashboard/payments');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('API Response:', result);
                
                if (result.status === 'success' && result.data) {
                    updatePaymentSummary(result.data);
                    initializePaymentCharts(result.data);
                    populateRecentPayments(result.data.recent_activity || []);
                } else {
                    console.error('API Error:', result);
                    showErrorState(result.message || 'Failed to load payment analytics data.');
                }
                
            } catch (error) {
                console.error('Error loading payment data:', error);
                showErrorState('Failed to load payment analytics data. Please check the console for details.');
            }
        }

        function showErrorState(message) {
            // Hide all content and show error message
            const container = document.querySelector('.container-fluid');
            const errorHTML = `
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Payment Analytics</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="/ambassadors/dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Payment Analytics</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <div class="avatar-lg mx-auto mb-4">
                                    <div class="avatar-title bg-danger-subtle text-danger rounded-circle fs-2">
                                        <i class="ri-error-warning-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-danger">Unable to Load Payment Data</h5>
                                <p class="text-muted mb-4">${message}</p>
                                <button type="button" class="btn btn-primary" onclick="location.reload()">
                                    <i class="ri-refresh-line me-1"></i> Retry
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML = errorHTML;
        }

        function updatePaymentSummary(data) {
            // Update counter values based on new API structure
            const summary = data.summary || {};
            document.getElementById('total-participants').querySelector('.counter-value').textContent = summary.total_participants || 0;
            document.getElementById('paid-participants').querySelector('.counter-value').textContent = summary.paid_participants || 0;
            document.getElementById('cancelled-participants').querySelector('.counter-value').textContent = summary.cancelled_participants || 0;
            document.getElementById('rejected-participants').querySelector('.counter-value').textContent = summary.rejected_participants || 0;
            document.getElementById('payment-rate').querySelector('.counter-value').textContent = (summary.payment_completion_rate || 0).toFixed(1);
            document.getElementById('avg-payment-time').querySelector('.counter-value').textContent = (summary.average_payment_time_days || 0).toFixed(1);
            
            // Update performance metrics
            const performanceMetrics = data.trends?.performance_metrics || {};
            document.getElementById('conversion-rate').textContent = (performanceMetrics.conversion_rate || 0).toFixed(1) + '%';
            document.getElementById('payment-success-rate').textContent = (performanceMetrics.payment_success_rate || 0).toFixed(1) + '%';
            document.getElementById('avg-days-payment').textContent = (performanceMetrics.average_days_to_payment || 0).toFixed(1) + ' days';
            
            const monthlyGrowth = performanceMetrics.monthly_growth_rate || 0;
            const growthElement = document.getElementById('monthly-growth');
            growthElement.textContent = (monthlyGrowth >= 0 ? '+' : '') + monthlyGrowth.toFixed(1) + '%';
            growthElement.className = monthlyGrowth >= 0 ? 'text-success mb-0' : 'text-danger mb-0';
        }

        function initializePaymentCharts(data) {
            // Payment Timeline Chart - use trends.payment_timeline
            const timelineData = data.trends?.payment_timeline || [];
            const timelineOptions = {
                series: [{
                    name: 'Payments',
                    data: timelineData.map(item => item.payments || 0)
                }, {
                    name: 'Unique Participants',
                    data: timelineData.map(item => item.unique_participants || 0)
                }],
                chart: {
                    type: 'line',
                    height: 350,
                    toolbar: { show: false }
                },
                colors: ['#28a745', '#007bff'],
                xaxis: {
                    categories: timelineData.map(item => {
                        const date = new Date(item.month + '-01');
                        return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                    })
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                markers: {
                    size: 6
                },
                noData: {
                    text: 'No payment timeline data available'
                }
            };
            
            new ApexCharts(document.querySelector("#payment-timeline-chart"), timelineOptions).render();

            // Payment Status Chart - use summary structure
            const summary = data.summary || {};
            const statusOptions = {
                series: [
                    summary.paid_participants || 0, 
                    summary.pending_participants || 0, 
                    summary.unpaid_participants || 0,
                    summary.cancelled_participants || 0,
                    summary.rejected_participants || 0
                ],
                chart: {
                    type: 'donut',
                    height: 300
                },
                colors: ['#28a745', '#ffc107', '#dc3545', '#6c757d', '#fd7e14'],
                labels: ['Paid', 'Pending', 'Unpaid', 'Cancelled', 'Rejected'],
                legend: {
                    position: 'bottom'
                },
                noData: {
                    text: 'No payment status data available'
                }
            };
            
            new ApexCharts(document.querySelector("#payment-status-chart"), statusOptions).render();

            // Conversion Funnel
            const funnel = data.trends?.conversion_funnel || {};
            const funnelContainer = document.getElementById('conversion-funnel-container');
            const funnelSteps = [
                { label: 'Referred', value: funnel.referred || 0, color: 'primary' },
                { label: 'Registered', value: funnel.registered || 0, color: 'info' },
                { label: 'Form Completed', value: funnel.form_completed || 0, color: 'warning' },
                { label: 'Payment Initiated', value: funnel.payment_initiated || 0, color: 'secondary' },
                { label: 'Payment Completed', value: funnel.payment_completed || 0, color: 'success' }
            ];
            
            const maxValue = Math.max(...funnelSteps.map(step => step.value)) || 1;
            funnelContainer.innerHTML = funnelSteps.map(step => {
                const percentage = ((step.value / maxValue) * 100).toFixed(1);
                return `
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-medium">${step.label}</span>
                            <span class="text-${step.color} fw-semibold">${step.value}</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-${step.color}" style="width: ${percentage}%;"></div>
                        </div>
                    </div>
                `;
            }).join('');

            // Top Converting Countries Chart
            const topCountries = data.insights?.top_converting_countries || {};
            const countryNames = Object.keys(topCountries).slice(0, 5); // Top 5 countries
            const countryRates = countryNames.map(name => topCountries[name].completion_rate || 0);
            
            const countriesOptions = {
                series: [{
                    name: 'Completion Rate',
                    data: countryRates
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false }
                },
                colors: ['#007bff'],
                xaxis: {
                    categories: countryNames,
                    labels: {
                        rotate: -45
                    }
                },
                yaxis: {
                    title: {
                        text: 'Completion Rate (%)'
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val.toFixed(1) + '%';
                    }
                },
                noData: {
                    text: 'No country data available'
                }
            };
            
            new ApexCharts(document.querySelector("#top-countries-chart"), countriesOptions).render();
        }

        // Helper function to escape HTML and clean data
        function escapeHtml(text) {
            if (!text) return 'N/A';
            
            // Clean up problematic data
            let cleanText = String(text);
            
            // Fix common data issues
            if (cleanText.includes('<font style=')) {
                cleanText = 'Unknown';
            }
            
            // Remove any HTML tags
            cleanText = cleanText.replace(/<[^>]*>/g, '');
            
            // Escape HTML
            const div = document.createElement('div');
            div.textContent = cleanText;
            return div.innerHTML;
        }

        function populateRecentPayments(payments) {
            console.log('Populating recent payments table with', payments?.length || 0, 'items');
            
            const tableBody = document.querySelector('#recent-payments-datatable tbody');
            
            if (!tableBody) {
                console.error('Table body not found');
                return;
            }
            
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#recent-payments-datatable')) {
                try {
                    $('#recent-payments-datatable').DataTable().destroy();
                    console.log('Destroyed existing DataTable');
                } catch (error) {
                    console.warn('Error destroying DataTable:', error);
                }
            }
            
            // Clear table body
            tableBody.innerHTML = '';
            
            if (!payments || payments.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No recent payment activities</td></tr>';
                console.log('No payments data to display');
                return;
            }
            
            // Validate and clean payment data
            const validPayments = payments.filter(payment => {
                return payment && payment.participant_name;
            });
            
            console.log('Valid payments:', validPayments.length, 'out of', payments.length);
            
            if (validPayments.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No valid payment data available</td></tr>';
                return;
            }
            
            // Populate table body with cleaned data
            const tableRows = validPayments.map(payment => {
                const participantName = escapeHtml(payment.participant_name);
                const nationality = escapeHtml(payment.nationality);
                const program = escapeHtml(payment.program);
                const paymentMethod = escapeHtml(payment.payment_method);
                const paymentDate = escapeHtml(payment.payment_date);
                const status = escapeHtml(payment.status);
                const daysAgo = payment.days_ago ? payment.days_ago + ' days ago' : 'N/A';
                
                return `
                    <tr>
                        <td>
                            <div>
                                <h6 class="fs-14 mb-1 fw-medium">${participantName}</h6>
                                <p class="text-muted mb-0 fs-12">
                                    <i class="ri-map-pin-line me-1"></i>${nationality}
                                </p>
                            </div>
                        </td>
                        <td>
                            <span class="fw-medium text-dark">${program}</span>
                        </td>
                        <td>
                            <span class="text-muted fs-13">${paymentMethod}</span>
                        </td>
                        <td>
                            <span class="fs-13">${paymentDate}</span>
                        </td>
                        <td>
                            <span class="badge ${getStatusBadgeClass(payment.status)} fs-11">${status}</span>
                        </td>
                        <td>
                            <span class="text-muted fs-12">${daysAgo}</span>
                        </td>
                    </tr>
                `;
            }).join('');
            
            tableBody.innerHTML = tableRows;
            
            // Initialize DataTable with better configuration
            if (typeof $ !== 'undefined' && $.fn.DataTable) {
                try {
                    const dataTable = $('#recent-payments-datatable').DataTable({
                        responsive: true,
                        pageLength: 10,
                        lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                        order: [[3, 'desc']], // Sort by payment date (newest first)
                        searching: true,
                        paging: true,
                        info: true,
                        autoWidth: false,
                        columnDefs: [
                            {
                                targets: [0], // Participant name column
                                width: '25%',
                                orderable: true
                            },
                            {
                                targets: [1], // Program column
                                width: '20%',
                                orderable: true
                            },
                            {
                                targets: [2], // Payment method column
                                width: '18%',
                                orderable: false
                            },
                            {
                                targets: [3], // Payment date column
                                width: '15%',
                                orderable: true
                            },
                            {
                                targets: [4], // Status column
                                width: '12%',
                                orderable: true
                            },
                            {
                                targets: [5], // Days ago column
                                width: '10%',
                                orderable: true,
                                type: 'num'
                            }
                        ],
                        language: {
                            search: "Search payments:",
                            lengthMenu: "Show _MENU_ payments per page",
                            info: "Showing _START_ to _END_ of _TOTAL_ payments",
                            infoEmpty: "No payments to show",
                            infoFiltered: "(filtered from _MAX_ total payments)",
                            emptyTable: "No payment activities found",
                            paginate: {
                                first: "First",
                                last: "Last",
                                next: "Next",
                                previous: "Previous"
                            },
                            zeroRecords: "No matching payment records found"
                        },
                        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                             '<"row"<"col-sm-12"tr>>' +
                             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
                    });
                    
                    console.log('DataTable initialized successfully with', validPayments.length, 'rows');
                    
                } catch (error) {
                    console.error('DataTable initialization error:', error);
                    // Table will still show data even if DataTable fails
                }
            } else {
                console.warn('DataTables library not available - showing basic table');
            }
        }

        function getStatusBadgeClass(status) {
            if (!status) return 'bg-secondary-subtle text-secondary';
            
            const statusLower = String(status).toLowerCase().trim();
            
            switch(statusLower) {
                case 'paid':
                case 'completed':
                case 'success':
                case 'successful':
                    return 'bg-success-subtle text-success';
                    
                case 'pending': 
                case 'created':
                case 'initiated':
                case 'processing':
                    return 'bg-warning-subtle text-warning';
                    
                case 'failed':
                case 'rejected':
                case 'declined':
                case 'error':
                    return 'bg-danger-subtle text-danger';
                    
                case 'cancelled':
                case 'canceled':
                case 'refunded':
                    return 'bg-secondary-subtle text-secondary';
                    
                default: 
                    return 'bg-light-subtle text-muted';
            }
        }
    </script>

</body>
</html>