<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Referred Participants')); ?>

    <?= $this->include('partials/head-css') ?>

    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <style>
        .sortable {
            cursor: pointer;
            user-select: none;
            position: relative;
        }
        
        .sortable:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .sortable.sort-asc .sort-icon::before {
            content: "\ea4e"; /* ri-arrow-up-line */
            color: var(--bs-primary) !important;
        }
        
        .sortable.sort-desc .sort-icon::before {
            content: "\ea50"; /* ri-arrow-down-line */
            color: var(--bs-primary) !important;
        }
        
        .btn-outline-primary:hover, 
        .btn-outline-success:hover {
            transform: translateY(-1px);
            transition: all 0.2s ease-in-out;
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

            <div class="page-content">                <div class="container-fluid"> <?php echo view('partials/page-title', array('pagetitle' => 'Ambassador', 'title' => 'Referred Participants')); ?>

                    <!-- Statistics Cards -->
                    <div class="row" id="statistics-cards">
                        <!-- Total Referrals Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0">Total Referrals</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light rounded fs-3">
                                                <i class="ri-group-line text-primary"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="total-referrals-count">
                                                <?= $statistics['total_referrals'] ?? 0 ?>
                                            </h4>
                                            <span class="badge bg-info-subtle text-info"><i class="ri-user-add-line align-bottom me-1"></i> Total participants</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Countries Represented Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0">Countries</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light rounded fs-3">
                                                <i class="ri-earth-line text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="countries-count">
                                                <?= $statistics['countries_count'] ?? 0 ?>
                                            </h4>
                                            <span class="badge bg-success-subtle text-success"><i class="ri-flag-line align-bottom me-1"></i> Nationalities</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- This Month Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0">This Month</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light rounded fs-3">
                                                <i class="ri-calendar-check-line text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="this-month-count">
                                                <?= $statistics['this_month_count'] ?? 0 ?>
                                            </h4>
                                            <span class="badge bg-warning-subtle text-warning"><i class="ri-time-line align-bottom me-1"></i> This month</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Program Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0">Program</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light rounded fs-3">
                                                <i class="ri-award-line text-danger"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-16 fw-semibold ff-secondary mb-4 text-truncate">
                                                <?= isset($currentProgram['name']) ? $currentProgram['name'] : 'N/A' ?>
                                            </h4>
                                            <span class="badge bg-danger-subtle text-danger"><i class="ri-medal-line align-bottom me-1"></i> Current program</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Statistics Cards -->

                    <?php if (ENVIRONMENT === 'development'): ?>
                    <!-- Debug Information (only in development) -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6>Debug Info (Development Only):</h6>
                                <p><strong>Participants Data:</strong> 
                                    <?= !empty($participantsData['participants']) ? count($participantsData['participants']) . ' items loaded' : 'No participants data' ?>
                                </p>
                                <p><strong>Total from API:</strong> 
                                    <?= $participantsData['pagination']['total'] ?? 'Unknown' ?>
                                </p>
                                <p><strong>Current Page:</strong> 
                                    <?= $participantsData['pagination']['current_page'] ?? 'Unknown' ?>
                                </p>
                                <?php if (!empty($participantsData['participants'])): ?>
                                    <p><strong>First Participant:</strong> 
                                        <?= esc($participantsData['participants'][0]['full_name'] ?? 'No name') ?>
                                    </p>
                                <?php endif; ?>
                                <p><strong>Ambassador Session:</strong> 
                                    <?= isset($ambassador['id']) ? 'Active (ID: ' . $ambassador['id'] . ')' : 'Not found' ?>
                                </p>
                                <p><strong>Raw Data Structure:</strong></p>
                                <pre style="max-height: 200px; overflow-y: auto; font-size: 12px;"><?= htmlspecialchars(print_r($participantsData ?? ['error' => 'No data'], true)) ?></pre>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="card-title mb-0">Your Referred Participants</h5>
                                        </div>
                                        <div class="col-auto">
                                            <!-- Filters -->
                                            <div class="d-flex gap-2">
                                                <div class="search-box">
                                                    <input type="text" class="form-control" id="search-participants" placeholder="Search participants...">
                                                    <i class="ri-search-line search-icon"></i>
                                                </div>
                                                <select class="form-select" id="form-status-filter" style="width: auto;">
                                                    <option value="">All Status</option>
                                                    <option value="not_started">Not Started</option>
                                                    <option value="in_progress">In Progress</option>
                                                    <option value="submitted">Submitted</option>
                                                </select>
                                                <select class="form-select" id="category-filter" style="width: auto;">
                                                    <option value="">All Categories</option>
                                                    <option value="fully_funded">Fully Funded</option>
                                                    <option value="self_funded">Self Funded</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="referred-participants-table" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th class="sortable" data-sort="name">
                                                        Participant Info 
                                                        <i class="ri-arrow-up-down-line ms-1 text-muted sort-icon"></i>
                                                    </th>
                                                    <th class="sortable" data-sort="nationality">
                                                        Nationality 
                                                        <i class="ri-arrow-up-down-line ms-1 text-muted sort-icon"></i>
                                                    </th>
                                                    <th class="sortable" data-sort="form_status">
                                                        Status / Category
                                                        <i class="ri-arrow-up-down-line ms-1 text-muted sort-icon"></i>
                                                    </th>
                                                    <th class="sortable" data-sort="registration_date">
                                                        Registration Date 
                                                        <i class="ri-arrow-up-down-line ms-1 text-muted sort-icon"></i>
                                                    </th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="participants-table-body">
                                                <?php if (!empty($participantsData['participants'])): ?>
                                                    <?php foreach ($participantsData['participants'] as $index => $participant): ?>
                                                        <tr>
                                                            <td><?= ($participantsData['pagination']['from'] ?? 1) + $index ?></td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-shrink-0 me-2">
                                                                        <div class="avatar-xs">
                                                                            <div class="avatar-title bg-light text-primary rounded-circle fs-13">
                                                                                <?= !empty($participant['full_name']) ? strtoupper(substr($participant['full_name'], 0, 1)) : 'N' ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-1"><?= esc($participant['full_name'] ?? 'N/A') ?></h6>
                                                                        <?php 
                                                                        // Obscure email for privacy
                                                                        $email = $participant['email'] ?? '';
                                                                        $obscuredEmail = '';
                                                                        if ($email && strpos($email, '@') !== false) {
                                                                            $parts = explode('@', $email);
                                                                            $username = $parts[0];
                                                                            $domain = $parts[1];
                                                                            
                                                                            // Show first 2 chars + asterisks + last 1 char of username
                                                                            if (strlen($username) > 3) {
                                                                                $obscuredUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 3) . substr($username, -1);
                                                                            } else {
                                                                                $obscuredUsername = substr($username, 0, 1) . str_repeat('*', max(1, strlen($username) - 1));
                                                                            }
                                                                            
                                                                            // Show full domain
                                                                            $obscuredEmail = $obscuredUsername . '@' . $domain;
                                                                        } else {
                                                                            $obscuredEmail = 'N/A';
                                                                        }
                                                                        ?>
                                                                        <small class="text-muted d-block"><?= esc($obscuredEmail) ?></small>
                                                                        <?php if (!empty($participant['institution']) && $participant['institution'] !== 'N/A'): ?>
                                                                            <small class="text-info d-block"><i class="ri-building-line me-1"></i><?= esc($participant['institution']) ?></small>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                $nationality = strip_tags($participant['nationality'] ?? '');
                                                                echo !empty(trim($nationality)) ? esc($nationality) : '-';
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                $status = $participant['form_status'] ?? 'not_started';
                                                                $statusClass = $status === 'submitted' ? 'bg-success-subtle text-success' : ($status === 'in_progress' ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle text-secondary');
                                                                $statusText = $status === 'submitted' ? 'Submitted' : ($status === 'in_progress' ? 'In Progress' : 'Not Started');
                                                                ?>
                                                                <span class="badge <?= $statusClass ?> fs-12">
                                                                    <?= $statusText ?>
                                                                </span>
                                                                <br>
                                                                <small class="text-muted"><?= ucfirst(str_replace('_', ' ', $participant['category'] ?? 'N/A')) ?></small>
                                                            </td>
                                                            <td><?= !empty($participant['registration_date']) ? date('M d, Y', strtotime($participant['registration_date'])) : 'N/A' ?></td>
                                                            <td>
                                                                <div class="d-flex gap-1">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                            onclick="viewParticipantDetails('<?= $participant['id'] ?? '' ?>')" 
                                                                            title="View Details" data-bs-toggle="tooltip">
                                                                        <i class="ri-eye-line"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                                            onclick="viewPayments('<?= $participant['id'] ?? '' ?>')" 
                                                                            title="View Payments" data-bs-toggle="tooltip">
                                                                        <i class="ri-money-dollar-circle-line"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">
                                                            No participants found
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div class="pagination-info">
                                            <span class="text-muted" id="pagination-info">
                                                Showing <?= $participantsData['pagination']['from'] ?? 0 ?> to <?= $participantsData['pagination']['to'] ?? 0 ?> of <?= $participantsData['pagination']['total'] ?? 0 ?> entries
                                            </span>
                                        </div>
                                        <nav aria-label="Participants pagination">
                                            <ul class="pagination pagination-sm mb-0" id="pagination-controls">
                                                <?php 
                                                $pagination = $participantsData['pagination'] ?? [];
                                                $currentPage = $pagination['current_page'] ?? 1;
                                                $lastPage = $pagination['last_page'] ?? 1;
                                                $baseUrl = '/ambassadors/referred-participants';
                                                $params = [];
                                                if (!empty($searchTerm)) $params['search'] = $searchTerm;
                                                if (!empty($statusFilter)) $params['form_status'] = $statusFilter;
                                                if (!empty($categoryFilter)) $params['category'] = $categoryFilter;
                                                ?>
                                                
                                                <!-- Previous button -->
                                                <?php if ($currentPage > 1): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="<?= $baseUrl ?>?<?= http_build_query(array_merge($params, ['page' => $currentPage - 1])) ?>">Previous</a>
                                                    </li>
                                                <?php endif; ?>
                                                
                                                <!-- Page numbers -->
                                                <?php for ($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++): ?>
                                                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                                        <a class="page-link" href="<?= $baseUrl ?>?<?= http_build_query(array_merge($params, ['page' => $i])) ?>"><?= $i ?></a>
                                                    </li>
                                                <?php endfor; ?>
                                                
                                                <!-- Next button -->
                                                <?php if ($currentPage < $lastPage): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="<?= $baseUrl ?>?<?= http_build_query(array_merge($params, ['page' => $currentPage + 1])) ?>">Next</a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?= $this->include('partials/footer') ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Participant Details Modal -->
    <div class="modal fade" id="participantDetailsModal" tabindex="-1" aria-labelledby="participantDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="participantDetailsModalLabel">
                        <i class="ri-user-line me-2"></i>Participant Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="participantDetailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading participant details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentDetailsModalLabel">
                        <i class="ri-money-dollar-circle-line me-2"></i>Payment Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="paymentDetailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading payment details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?= $this->include('partials/vendor-scripts') ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Table sorting functionality
            $('.sortable').on('click', function() {
                const sortField = $(this).data('sort');
                const currentUrl = new URL(window.location);
                const currentSort = currentUrl.searchParams.get('sort_by');
                const currentOrder = currentUrl.searchParams.get('sort_order');
                
                let newOrder = 'ASC';
                if (currentSort === sortField && currentOrder === 'ASC') {
                    newOrder = 'DESC';
                }
                
                // Update URL parameters
                currentUrl.searchParams.set('sort_by', sortField);
                currentUrl.searchParams.set('sort_order', newOrder);
                currentUrl.searchParams.set('page', '1'); // Reset to first page
                
                // Update visual indicators
                $('.sortable').removeClass('sort-asc sort-desc');
                $(this).addClass(newOrder === 'ASC' ? 'sort-asc' : 'sort-desc');
                
                // Navigate to new URL
                window.location.href = currentUrl.toString();
            });
            
            // Set current sort visual indicators
            const urlParams = new URLSearchParams(window.location.search);
            const currentSortBy = urlParams.get('sort_by');
            const currentSortOrder = urlParams.get('sort_order');
            
            if (currentSortBy) {
                const sortHeader = $(`[data-sort="${currentSortBy}"]`);
                if (currentSortOrder === 'DESC') {
                    sortHeader.addClass('sort-desc');
                } else {
                    sortHeader.addClass('sort-asc');
                }
            }
            
            // Initialize search functionality
            $('#search-participants').on('input', debounce(function() {
                const searchValue = $(this).val();
                const currentUrl = new URL(window.location);
                
                if (searchValue) {
                    currentUrl.searchParams.set('search', searchValue);
                } else {
                    currentUrl.searchParams.delete('search');
                }
                currentUrl.searchParams.set('page', '1'); // Reset to first page
                
                window.location.href = currentUrl.toString();
            }, 500));

            // Form status filter functionality
            $('#form-status-filter').on('change', function() {
                const statusValue = $(this).val();
                const currentUrl = new URL(window.location);
                
                if (statusValue) {
                    currentUrl.searchParams.set('form_status', statusValue);
                } else {
                    currentUrl.searchParams.delete('form_status');
                }
                currentUrl.searchParams.set('page', '1'); // Reset to first page
                
                window.location.href = currentUrl.toString();
            });
            
            // Category filter functionality
            $('#category-filter').on('change', function() {
                const categoryValue = $(this).val();
                const currentUrl = new URL(window.location);
                
                if (categoryValue) {
                    currentUrl.searchParams.set('category', categoryValue);
                } else {
                    currentUrl.searchParams.delete('category');
                }
                currentUrl.searchParams.set('page', '1'); // Reset to first page
                
                window.location.href = currentUrl.toString();
            });
            
            // Set current filter values from URL
            const searchParam = urlParams.get('search');
            const formStatusParam = urlParams.get('form_status');
            const categoryParam = urlParams.get('category');
            
            if (searchParam) {
                $('#search-participants').val(searchParam);
            }
            if (formStatusParam) {
                $('#form-status-filter').val(formStatusParam);
            }
            if (categoryParam) {
                $('#category-filter').val(categoryParam);
            }
        });

        // Test function to verify modal works
        function testPaymentModal() {
            console.log('Testing payment modal...');
            const modal = new bootstrap.Modal(document.getElementById('paymentDetailsModal'));
            modal.show();
            
            document.getElementById('paymentDetailsContent').innerHTML = `
                <div class="text-center py-4">
                    <h5>Modal Test Successful!</h5>
                    <p>If you can see this, the modal is working correctly.</p>
                    <button class="btn btn-primary" onclick="testPaymentModal()">Test Again</button>
                </div>
            `;
        }
        
        // Expose test function to global scope for debugging
        window.testPaymentModal = testPaymentModal;

        // View participant details with modal
        function viewParticipantDetails(participantId) {
            if (!participantId) {
                alert('Invalid participant ID');
                return;
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('participantDetailsModal'));
            modal.show();
            
            // Reset modal content to loading state
            document.getElementById('participantDetailsContent').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading participant details...</p>
                </div>
            `;
            
            // Simulate API call (replace with actual API endpoint)
            setTimeout(() => {
                // Find participant data from current page
                const participantData = findParticipantById(participantId);
                
                if (participantData) {
                    displayParticipantDetails(participantData);
                } else {
                    // If not found in current page, make API call
                    fetchParticipantDetails(participantId);
                }
            }, 500);
        }

        // View payments with modal
        function viewPayments(participantId) {
            console.log('viewPayments called with participantId:', participantId);
            
            if (!participantId) {
                console.error('Invalid participant ID');
                alert('Invalid participant ID');
                return;
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('paymentDetailsModal'));
            modal.show();
            
            console.log('Modal should be visible now');
            
            // Reset modal content to loading state
            document.getElementById('paymentDetailsContent').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading payment details...</p>
                </div>
            `;
            
            // Call payment details API
            fetchPaymentDetails(participantId);
        }
        
        // Helper function to find participant in current page data
        function findParticipantById(participantId) {
            // This would normally come from the server-side data
            // For now, we'll simulate it
            const participants = <?= json_encode($participantsData['participants'] ?? []) ?>;
            return participants.find(p => p.id == participantId);
        }
        
        // Display participant details in modal
        function displayParticipantDetails(participant) {
            const obscureEmail = (email) => {
                if (!email || !email.includes('@')) return 'N/A';
                const [username, domain] = email.split('@');
                if (username.length > 3) {
                    return username.substring(0, 2) + '*'.repeat(username.length - 3) + username.slice(-1) + '@' + domain;
                }
                return username.substring(0, 1) + '*'.repeat(Math.max(1, username.length - 1)) + '@' + domain;
            };
            
            const formatDate = (dateString) => {
                if (!dateString) return 'N/A';
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            };
            
            const getStatusBadge = (status) => {
                const statusMap = {
                    'submitted': { class: 'bg-success text-white', text: 'Submitted' },
                    'in_progress': { class: 'bg-warning text-dark', text: 'In Progress' },
                    'not_started': { class: 'bg-secondary text-white', text: 'Not Started' }
                };
                const statusInfo = statusMap[status] || statusMap['not_started'];
                return `<span class="badge ${statusInfo.class}">${statusInfo.text}</span>`;
            };
            
            document.getElementById('participantDetailsContent').innerHTML = `
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-24">
                                ${participant.full_name ? participant.full_name.charAt(0).toUpperCase() : 'N'}
                            </div>
                        </div>
                        <h5 class="mb-1">${participant.full_name || 'N/A'}</h5>
                        <p class="text-muted mb-2">${obscureEmail(participant.email)}</p>
                        ${getStatusBadge(participant.form_status)}
                    </div>
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="text-muted">Full Name:</th>
                                        <td>${participant.full_name || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted">Email:</th>
                                        <td>${obscureEmail(participant.email)}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted">Institution:</th>
                                        <td>${participant.institution || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted">Nationality:</th>
                                        <td>${participant.nationality || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted">Registration Date:</th>
                                        <td>${formatDate(participant.registration_date)}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted">Form Status:</th>
                                        <td>${getStatusBadge(participant.form_status)}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                ${participant.form_status === 'submitted' ? `
                <div class="mt-4">
                    <h6 class="text-muted mb-3">Additional Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-success-subtle text-success rounded">
                                                <i class="ri-check-line"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Application Submitted</h6>
                                            <p class="text-muted mb-0 small">All required forms submitted</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-info-subtle text-info rounded">
                                                <i class="ri-calendar-line"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Registered</h6>
                                            <p class="text-muted mb-0 small">${formatDate(participant.registration_date)}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ` : ''}
            `;
        }
        
        // Fetch participant details from API
        function fetchParticipantDetails(participantId) {
            // This would be an actual API call
            fetch(`/api/ambassador/participants/${participantId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        displayParticipantDetails(data.participant);
                    } else {
                        document.getElementById('participantDetailsContent').innerHTML = `
                            <div class="text-center py-4">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-danger-subtle text-danger rounded-circle">
                                        <i class="ri-error-warning-line fs-24"></i>
                                    </div>
                                </div>
                                <h6 class="text-danger">Failed to Load Details</h6>
                                <p class="text-muted">Unable to fetch participant details. Please try again.</p>
                                <button class="btn btn-outline-primary btn-sm" onclick="fetchParticipantDetails('${participantId}')">
                                    <i class="ri-refresh-line me-1"></i>Retry
                                </button>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching participant details:', error);
                    document.getElementById('participantDetailsContent').innerHTML = `
                        <div class="text-center py-4">
                            <div class="avatar-lg mx-auto mb-3">
                                <div class="avatar-title bg-warning-subtle text-warning rounded-circle">
                                    <i class="ri-wifi-off-line fs-24"></i>
                                </div>
                            </div>
                            <h6 class="text-warning">Connection Error</h6>
                            <p class="text-muted">Please check your internet connection and try again.</p>
                            <button class="btn btn-outline-primary btn-sm" onclick="fetchParticipantDetails('${participantId}')">
                                <i class="ri-refresh-line me-1"></i>Retry
                            </button>
                        </div>
                    `;
                });
        }
        
        // Fetch payment details from API
        function fetchPaymentDetails(participantId) {
            console.log('fetchPaymentDetails called for participant:', participantId);
            
            // Call internal route that will proxy to external API
            const apiUrl = `/ambassadors/dashboard/participant-payment/${participantId}`;
            console.log('Calling API URL:', apiUrl);
            
            fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('API response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('API response data:', data);
                
                if (data.status === 'success') {
                    console.log('Payment data received successfully');
                    displayPaymentDetails(data.data);
                } else {
                    console.error('API returned error:', data.message);
                    showPaymentError('API Error', data.message || 'Unable to fetch payment details. Please try again.', participantId);
                }
            })
            .catch(error => {
                console.error('Error fetching payment details:', error);
                
                // Determine the type of error and show appropriate message
                let errorTitle = 'Connection Error';
                let errorMessage = 'Please check your internet connection and try again.';
                
                if (error.message && error.message.includes('404')) {
                    errorTitle = 'Not Found';
                    errorMessage = 'Payment details not found for this participant.';
                } else if (error.message && error.message.includes('401')) {
                    errorTitle = 'Session Expired';
                    errorMessage = 'Your session has expired. Please sign in again.';
                } else if (error.message && error.message.includes('500')) {
                    errorTitle = 'Server Error';
                    errorMessage = 'There was a server error. Please try again in a few minutes.';
                }
                
                showPaymentError(errorTitle, errorMessage, participantId);
            });
        }
        
        // Helper function to show payment error messages
        function showPaymentError(title, message, participantId = null) {
            const retryButton = participantId ? 
                `<button class="btn btn-outline-primary btn-sm" onclick="fetchPaymentDetails('${participantId}')">
                    <i class="ri-refresh-line me-1"></i>Retry
                </button>` : '';
                
            document.getElementById('paymentDetailsContent').innerHTML = `
                <div class="text-center py-4">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-warning-subtle text-warning rounded-circle">
                            <i class="ri-wifi-off-line fs-24"></i>
                        </div>
                    </div>
                    <h6 class="text-warning">${title}</h6>
                    <p class="text-muted">${message}</p>
                    ${retryButton}
                </div>
            `;
        }
        
        // Display payment details in modal
        function displayPaymentDetails(responseData) {
            console.log('displayPaymentDetails called with data:', responseData);
            
            // Add safety checks
            if (!responseData) {
                console.error('No response data provided');
                document.getElementById('paymentDetailsContent').innerHTML = `
                    <div class="text-center py-4">
                        <h6 class="text-danger">No Data Available</h6>
                        <p class="text-muted">No payment data was received from the server.</p>
                    </div>
                `;
                return;
            }
            
            const { participant, payment_summary, payment_history, program_payment_requirements } = responseData;
            
            console.log('Extracted data:', { participant, payment_summary, payment_history, program_payment_requirements });
            
            const obscureEmail = (email) => {
                if (!email || !email.includes('@')) return 'N/A';
                const [username, domain] = email.split('@');
                if (username.length > 3) {
                    return username.substring(0, 2) + '*'.repeat(username.length - 3) + username.slice(-1) + '@' + domain;
                }
                return username.substring(0, 1) + '*'.repeat(Math.max(1, username.length - 1)) + '@' + domain;
            };
            
            const getPaymentStatusBadge = (status) => {
                const statusMap = {
                    'completed': { class: 'bg-success', icon: 'ri-check-line', text: 'Completed' },
                    'pending': { class: 'bg-warning', icon: 'ri-time-line', text: 'Pending' },
                    'failed': { class: 'bg-danger', icon: 'ri-close-line', text: 'Failed' },
                    'cancelled': { class: 'bg-secondary', icon: 'ri-close-circle-line', text: 'Cancelled' }
                };
                const statusInfo = statusMap[status.toLowerCase()] || statusMap['pending'];
                return `<span class="badge ${statusInfo.class}"><i class="${statusInfo.icon} me-1"></i>${statusInfo.text}</span>`;
            };
            
            const getCompletionStatusBadge = (status) => {
                return status === 'completed' 
                    ? '<span class="badge bg-success"><i class="ri-check-line me-1"></i>Payment Complete</span>'
                    : '<span class="badge bg-warning"><i class="ri-time-line me-1"></i>Payment Pending</span>';
            };
            
            const formatDate = (dateString) => {
                if (!dateString) return 'N/A';
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            };
            
            const formatNotes = (notes) => {
                if (!notes) return 'No additional notes';
                return notes.replace(/\n/g, '<br>');
            };
            
            document.getElementById('paymentDetailsContent').innerHTML = `
                <!-- Participant Info Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 bg-primary-subtle">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-1 text-primary">${participant.full_name}</h5>
                                        <p class="text-muted mb-1">${obscureEmail(participant.email)}</p>
                                        <p class="text-muted mb-0"><strong>Program:</strong> ${participant.program_name}</p>
                                    </div>
                                    <div class="col-auto">
                                        ${getCompletionStatusBadge(payment_summary.payment_completion_status)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- Payment Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-0 bg-light text-center">
                                    <div class="card-body p-3">
                                        <h4 class="text-primary mb-1">${payment_summary.total_payments}</h4>
                                        <small class="text-muted">Total Payments</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 bg-success-subtle text-center">
                                    <div class="card-body p-3">
                                        <h4 class="text-success mb-1">${payment_summary.completed_payments}</h4>
                                        <small class="text-muted">Completed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 bg-warning-subtle text-center">
                                    <div class="card-body p-3">
                                        <h4 class="text-warning mb-1">${payment_summary.pending_payments}</h4>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 bg-danger-subtle text-center">
                                    <div class="card-body p-3">
                                        <h4 class="text-danger mb-1">${payment_summary.failed_payments}</h4>
                                        <small class="text-muted">Failed</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment History -->
                        <h6 class="mb-3">Payment History</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead class="table-light">
                                    <tr>
                                        <th>Status</th>
                                        <th>Payment Method</th>
                                        <th>Date</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${payment_history && payment_history.length > 0 ? payment_history.map(payment => `
                                        <tr>
                                            <td>${getPaymentStatusBadge(payment.status)}</td>
                                            <td><small>${payment.payment_method || 'N/A'}</small></td>
                                            <td><small>${formatDate(payment.created_at)}</small></td>
                                            <td>
                                                <small class="text-muted">
                                                    ${payment.notes ? formatNotes(payment.notes).substring(0, 100) + (payment.notes.length > 100 ? '...' : '') : 'No notes'}
                                                </small>
                                            </td>
                                        </tr>
                                    `).join('') : '<tr><td colspan="4" class="text-center text-muted">No payment history found</td></tr>'}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Participant Information -->
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="ri-user-line me-2"></i>Participant Information
                                </h6>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Category</small>
                                    <span class="badge ${participant.category === 'fully_funded' ? 'bg-success' : 'bg-info'}">
                                        ${participant.category === 'fully_funded' ? 'Fully Funded' : 'Self Funded'}
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Form Status</small>
                                    <span>${participant.form_status}</span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Registration Date</small>
                                    <span>${formatDate(participant.registration_date)}</span>
                                </div>
                                <div class="mb-0">
                                    <small class="text-muted d-block">Latest Payment</small>
                                    <span>${formatDate(payment_summary.latest_payment_date)}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Program Payment Requirements -->
                        <div class="card border-0 bg-info-subtle mt-3">
                            <div class="card-body">
                                <h6 class="card-title text-info mb-3">
                                    <i class="ri-file-list-line me-2"></i>Payment Requirements
                                </h6>
                                <div class="small">
                                    ${program_payment_requirements.map(req => `
                                        <div class="mb-2 pb-2 border-bottom border-light">
                                            <div class="fw-medium">${req.name}</div>
                                            <div class="text-muted">${req.description}</div>
                                            <div class="text-muted">Deadline: ${formatDate(req.deadline)}</div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            `;
        }



        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>
</body>

</html>