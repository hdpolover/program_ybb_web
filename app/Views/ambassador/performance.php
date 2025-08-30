<?php
$greetingText = "";
$hour = date('H');

if (($hour >= 0 && $hour < 5) || ($hour >= 5 && $hour < 12)) {
    $greetingText = "Good Morning";
} elseif ($hour >= 12 && $hour < 17) {
    $greetingText = "Good Afternoon";
} else {
    $greetingText = "Good Evening";
}

$full_name = $ambassador['full_name'] ?? $ambassador['name'] ?? 'Ambassador';
?>

<?= $this->include('partials/main') ?>

<head>
    <!-- Title Meta -->
    <?= $this->include('partials/title-meta', ['meta_title' => "Performance Analytics"]) ?>

    <!-- apexcharts css -->
    <link href="/assets/libs/apexcharts/apexcharts.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>
    
    <style>
        .achievement-item {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .achievement-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: rgba(var(--bs-primary-rgb), 0.2);
        }
        
        .achievement-item.completed {
            background: linear-gradient(135deg, rgba(var(--bs-success-rgb), 0.05) 0%, rgba(var(--bs-success-rgb), 0.1) 100%);
            border-color: rgba(var(--bs-success-rgb), 0.2);
        }
        
        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }
        
        @keyframes progress-bar-stripes {
            0% { background-position: 1rem 0; }
            100% { background-position: 0 0; }
        }
        
        .achievement-glow {
            position: relative;
            overflow: hidden;
        }
        
        .achievement-glow::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .achievement-glow:hover::before {
            left: 100%;
        }
        
        .avatar-title {
            transition: all 0.3s ease;
        }
        
        .achievement-item:hover .avatar-title {
            transform: scale(1.1);
        }
    </style>
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?= $this->include('partials/ambassador-menu') ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Performance Analytics</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="/ambassadors/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Performance Analytics</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Conversion Metrics Cards -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted mb-0">Total Referrals</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h5 class="text-primary fs-14 mb-0">
                                                <i class="ri-group-line align-middle"></i>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="total-referrals-metric">
                                                <span class="counter-value" data-target="0">0</span>
                                            </h4>
                                            <span class="badge bg-primary-subtle text-primary mb-0">All time</span>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                                <i class="ri-group-line text-primary"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted mb-0">Registration Rate</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h5 class="text-success fs-14 mb-0">
                                                <i class="ri-user-add-line align-middle"></i>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="registration-rate-metric">
                                                <span class="counter-value" data-target="0">0</span>%
                                            </h4>
                                            <span class="badge bg-success-subtle text-success mb-0">Conversion</span>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                                <i class="ri-user-add-line text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted mb-0">Payment Rate</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h5 class="text-warning fs-14 mb-0">
                                                <i class="ri-money-dollar-circle-line align-middle"></i>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="payment-rate-metric">
                                                <span class="counter-value" data-target="0">0</span>%
                                            </h4>
                                            <span class="badge bg-warning-subtle text-warning mb-0">Payment conversion</span>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                                <i class="ri-money-dollar-circle-line text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted mb-0">Completed Registrations</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h5 class="text-info fs-14 mb-0">
                                                <i class="ri-checkbox-circle-line align-middle"></i>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="completed-registrations-metric">
                                                <span class="counter-value" data-target="0">0</span>
                                            </h4>
                                            <span class="badge bg-info-subtle text-info mb-0">Successful</span>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                                <i class="ri-checkbox-circle-line text-info"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row">
                        <!-- Referral Timeline Chart -->
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Referral Performance Timeline</h4>
                                </div>
                                <div class="card-body">
                                    <div id="referral_timeline_chart" data-colors='["--vz-primary", "--vz-success"]' class="apex-charts" dir="ltr"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Conversion Rates -->
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Monthly Conversion Rates</h4>
                                </div>
                                <div class="card-body">
                                    <div id="monthly_conversion_chart" data-colors='["--vz-primary"]' class="apex-charts" dir="ltr"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Achievement Progress & Monthly Performance -->
                    <div class="row">
                        <!-- Achievement Progress -->
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Achievement Progress</h4>
                                </div>
                                <div class="card-body">
                                    <div id="achievement-progress-container">
                                        <!-- Will be populated via JavaScript -->
                                        <div class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 text-muted">Loading achievement data...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Performance Table -->
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Monthly Performance Summary</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Month</th>
                                                    <th>Referrals</th>
                                                    <th>Registrations</th>
                                                    <th>Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody id="monthly-performance-table">
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <div class="spinner-border text-primary spinner-border-sm" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                        Loading performance data...
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
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?= $this->include('partials/footer') ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!-- apexcharts -->
    <script src="/assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>

    <!-- Performance Analytics Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadPerformanceAnalytics();
        });

        async function loadPerformanceAnalytics() {
            try {
                const response = await fetch('/ambassadors/dashboard/performance');
                const data = await response.json();
                
                if (data.status === 'success') {
                    const performanceData = data.data;
                    console.log('Performance data loaded:', performanceData);
                    
                    // Update metrics
                    updateMetrics(performanceData.conversion_metrics);
                    
                    // Create charts
                    createTimelineChart(performanceData.referral_timeline);
                    createConversionChart(performanceData.monthly_performance);
                    
                    // Update achievement progress
                    updateAchievementProgress(performanceData.achievement_progress);
                    
                    // Update monthly performance table
                    updateMonthlyPerformanceTable(performanceData.monthly_performance);
                    
                } else {
                    console.error('Failed to load performance data:', data.message);
                    showError('Failed to load performance analytics');
                }
            } catch (error) {
                console.error('Error loading performance analytics:', error);
                showError('Network error loading performance data');
            }
        }

        function updateMetrics(metrics) {
            if (!metrics) return;
            
            // Update counter values
            updateCounter('total-referrals-metric', metrics.total_referrals || 0);
            updateCounter('registration-rate-metric', Math.round((metrics.registration_rate || 0) * 10) / 10);
            updateCounter('payment-rate-metric', Math.round((metrics.payment_rate || 0) * 10) / 10);
            updateCounter('completed-registrations-metric', metrics.completed_registrations || 0);
        }

        function createTimelineChart(timelineData) {
            if (!timelineData || timelineData.length === 0) return;
            
            const options = {
                series: [{
                    name: 'Referrals',
                    data: timelineData.map(item => item.referrals)
                }, {
                    name: 'Registrations',
                    data: timelineData.map(item => item.registrations)
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: true
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                title: {
                    text: 'Referral vs Registration Timeline',
                    align: 'left'
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: timelineData.map(item => item.month)
                },
                colors: ['#405189', '#0ab39c']
            };

            const chart = new ApexCharts(document.querySelector("#referral_timeline_chart"), options);
            chart.render();
        }

        function createConversionChart(monthlyData) {
            if (!monthlyData || monthlyData.length === 0) return;
            
            const options = {
                series: [{
                    name: 'Conversion Rate %',
                    data: monthlyData.map(item => Math.round((item.conversion_rate || 0) * 10) / 10)
                }],
                chart: {
                    height: 350,
                    type: 'bar'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val + '%';
                    }
                },
                xaxis: {
                    categories: monthlyData.map(item => item.month)
                },
                colors: ['#405189']
            };

            const chart = new ApexCharts(document.querySelector("#monthly_conversion_chart"), options);
            chart.render();
        }

        function updateAchievementProgress(achievements) {
            const container = document.getElementById('achievement-progress-container');
            
            if (!achievements || achievements.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <div class="avatar-lg mx-auto mb-4">
                            <div class="avatar-title bg-light text-muted rounded-circle">
                                <i class="ri-award-line fs-24"></i>
                            </div>
                        </div>
                        <h6 class="text-muted mb-1">No Achievements Yet</h6>
                        <p class="text-muted small mb-0">Start referring participants to unlock achievements!</p>
                    </div>
                `;
                return;
            }
            
            // Define achievement tier icons and colors
            const achievementTiers = {
                'Getting Started': { icon: 'ri-seedling-line', color: 'info' },
                'Rising Star': { icon: 'ri-star-line', color: 'warning' },
                'Top Performer': { icon: 'ri-trophy-line', color: 'primary' },
                'Super Ambassador': { icon: 'ri-medal-line', color: 'success' },
                'Legend': { icon: 'ri-vip-crown-line', color: 'danger' }
            };
            
            // Create grid layout
            container.innerHTML = `
                <div class="row g-3">
                    ${achievements.map(achievement => {
                        const tierInfo = achievementTiers[achievement.title] || { icon: 'ri-award-line', color: 'secondary' };
                        const progressColor = achievement.completed ? 'success' : 'primary';
                        const badgeClass = achievement.completed ? 'bg-success' : 'bg-light text-muted';
                        const iconClass = achievement.completed ? `text-${tierInfo.color}` : 'text-muted';
                        const completedClass = achievement.completed ? 'completed' : '';
                        
                        return `
                            <div class="col-lg-6 col-xl-4">
                                <div class="achievement-item achievement-glow h-100 p-3 border rounded ${completedClass}">
                                    <div class="text-center mb-3">
                                        <div class="avatar-md mx-auto mb-3">
                                            <div class="avatar-title bg-${achievement.completed ? tierInfo.color + '-subtle' : 'light'} rounded-circle">
                                                <i class="${tierInfo.icon} fs-20 ${iconClass}"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-1 fw-semibold d-flex align-items-center justify-content-center">
                                            ${achievement.title}
                                            ${achievement.completed ? '<i class="ri-verified-badge-fill text-success ms-2 fs-14"></i>' : ''}
                                        </h6>
                                        <p class="text-muted mb-0 small">
                                            <i class="ri-group-line me-1"></i>
                                            <span class="fw-medium">${achievement.current.toLocaleString()}</span> / 
                                            <span class="fw-medium">${achievement.threshold.toLocaleString()}</span>
                                        </p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">Progress</small>
                                            <span class="badge ${badgeClass} fs-11 px-2 py-1 rounded-pill">
                                                ${achievement.completed ? 
                                                    '<i class="ri-check-line me-1"></i>Complete' : 
                                                    achievement.progress + '%'
                                                }
                                            </span>
                                        </div>
                                        <div class="progress rounded-pill" style="height: 8px;">
                                            <div class="progress-bar bg-${progressColor} progress-bar-striped ${achievement.completed ? '' : 'progress-bar-animated'} rounded-pill" 
                                                 role="progressbar" 
                                                 style="width: ${Math.min(achievement.progress, 100)}%"
                                                 aria-valuenow="${achievement.progress}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    ${achievement.completed ? `
                                        <div class="text-center">
                                            <small class="text-${tierInfo.color} fw-medium d-inline-flex align-items-center">
                                                <i class="ri-trophy-line me-1"></i>Unlocked!
                                                <i class="ri-sparkling-2-line ms-1"></i>
                                            </small>
                                        </div>
                                    ` : `
                                        <div class="text-center">
                                            <small class="text-muted">
                                                ${achievement.threshold - achievement.current > 0 ? 
                                                    `${(achievement.threshold - achievement.current).toLocaleString()} more to unlock` : 
                                                    'Almost there!'
                                                }
                                            </small>
                                        </div>
                                    `}
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
        }

        function updateMonthlyPerformanceTable(monthlyData) {
            const tableBody = document.getElementById('monthly-performance-table');
            
            if (!monthlyData || monthlyData.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">
                            No performance data available
                        </td>
                    </tr>
                `;
                return;
            }
            
            tableBody.innerHTML = monthlyData.map(data => `
                <tr>
                    <td><strong>${data.month}</strong></td>
                    <td>
                        <span class="badge bg-primary-subtle text-primary fs-12">
                            ${data.referrals}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-success-subtle text-success fs-12">
                            ${data.registrations}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${data.conversion_rate >= 60 ? 'bg-success' : data.conversion_rate >= 40 ? 'bg-warning' : 'bg-danger'} fs-12">
                            ${Math.round((data.conversion_rate || 0) * 10) / 10}%
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        function updateCounter(elementId, targetValue) {
            const element = document.querySelector(`#${elementId} .counter-value`);
            if (element) {
                element.setAttribute('data-target', targetValue);
                element.textContent = targetValue;
            }
        }

        function showError(message) {
            console.error('Performance page error:', message);
            
            // Show error in main containers
            const containers = [
                'achievement-progress-container',
                'monthly-performance-table'
            ];
            
            containers.forEach(containerId => {
                const container = document.getElementById(containerId);
                if (container) {
                    container.innerHTML = `
                        <div class="text-center py-4 text-danger">
                            <i class="ri-error-warning-line fs-24 mb-2"></i>
                            <p class="mb-2">${message}</p>
                            <button class="btn btn-sm btn-outline-primary" onclick="loadPerformanceAnalytics()">
                                <i class="ri-refresh-line"></i> Retry
                            </button>
                        </div>
                    `;
                }
            });
        }
    </script>

</body>

</html>