<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title'=>'Abstract Version Comparison')); ?>

    <?= $this->include('partials/head-css') ?>

    <!-- Custom CSS for comparison page -->
    <link href="/assets/css/comparison.css" rel="stylesheet" type="text/css" />

</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?= $this->include('partials/menu') ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <?php echo view('partials/page-title', array('pagetitle'=>'Abstract Paper', 'title'=>'Version Comparison')); ?>                    <!-- Back Button -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <a href="/abstract-paper" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Abstract Papers
                            </a>
                        </div>
                    </div><!-- Version Overview Card -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-code-branch me-2"></i>Version Comparison Overview
                                    </h5>
                                    <p class="text-muted mb-0">
                                        Comparing Version <?= esc($data['version1']['version_number']) ?> with Version <?= esc($data['version2']['version_number']) ?>
                                    </p>
                                </div>
                                <div class="card-body">
                                    <!-- Statistics Row -->
                                    <div class="row text-center mb-4">
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <span class="stat-number text-primary"><?= $data['comparison']['summary']['total_changes'] ?></span>
                                                <span class="stat-label text-muted">Total Changes</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <span class="stat-number text-info"><?= count($data['comparison']['fields']) ?></span>
                                                <span class="stat-label text-muted">Fields Compared</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <span class="stat-number text-warning"><?= gmdate('H:i:s', $data['comparison']['metadata']['time_difference']) ?></span>
                                                <span class="stat-label text-muted">Time Difference</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <span class="stat-number <?= $data['comparison']['summary']['has_changes'] ? 'text-success' : 'text-secondary' ?>">
                                                    <?= $data['comparison']['summary']['has_changes'] ? 'Yes' : 'No' ?>
                                                </span>
                                                <span class="stat-label text-muted">Has Changes</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    <!-- Version Details Cards -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex align-items-center">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-file-alt me-2"></i>Version <?= esc($data['version1']['version_number']) ?>
                                        <span class="badge bg-secondary ms-2"><?= esc($data['version1']['status']) ?></span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <h6 class="mb-2"><?= esc($data['version1']['title']) ?></h6>
                                    <p class="text-muted mb-0">
                                        <small>Created: <?= date('M j, Y g:i A', strtotime($data['version1']['created_at'])) ?></small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex align-items-center">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-file-alt me-2"></i>Version <?= esc($data['version2']['version_number']) ?>
                                        <span class="badge bg-primary ms-2"><?= esc($data['version2']['status']) ?></span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <h6 class="mb-2"><?= esc($data['version2']['title']) ?></h6>
                                    <p class="text-muted mb-0">
                                        <small>Created: <?= date('M j, Y g:i A', strtotime($data['version2']['created_at'])) ?></small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>                    <!-- Controls and Actions Card -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                <input type="text" id="fieldSearch" class="form-control" placeholder="Search fields...">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="btn-group w-100" role="group" aria-label="View filters">
                                                <input type="radio" class="btn-check" name="viewFilter" id="showAll" value="all" checked>
                                                <label class="btn btn-outline-primary" for="showAll">All Fields</label>

                                                <input type="radio" class="btn-check" name="viewFilter" id="showChanged" value="changed">
                                                <label class="btn btn-outline-warning" for="showChanged">Changed Only</label>

                                                <input type="radio" class="btn-check" name="viewFilter" id="showUnchanged" value="unchanged">
                                                <label class="btn btn-outline-success" for="showUnchanged">Unchanged Only</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-outline-secondary" id="expandAll">
                                                    <i class="fas fa-expand-alt me-1"></i>Expand All
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" id="collapseAll">
                                                    <i class="fas fa-compress-alt me-1"></i>Collapse All
                                                </button>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" id="downloadReport"><i class="fas fa-download me-2"></i>Download Report</a></li>
                                                        <li><a class="dropdown-item" href="#" id="printComparison"><i class="fas fa-print me-2"></i>Print</a></li>
                                                        <li><a class="dropdown-item" href="#" id="shareComparison"><i class="fas fa-share-alt me-2"></i>Share</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    <!-- Field-by-Field Comparison -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-list-alt me-2"></i>Detailed Field Comparison
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($data['comparison']['fields'] as $field): ?>
                                        <div class="comparison-field border rounded mb-3">
                                            <div class="field-header bg-light p-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fas fa-tag me-2"></i><?= esc($field['label']) ?>
                                                    </span>
                                                    <span class="badge <?= $field['has_change'] ? 'bg-warning' : 'bg-success' ?>">
                                                        <?= $field['has_change'] ? 'Changed' : 'Unchanged' ?>
                                                    </span>
                                                </div>
                                            </div>                                            <div class="field-content p-3">
                                                <?php if ($field['has_change']): ?>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="version-column bg-light p-3 rounded">
                                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                                    <h6 class="mb-0">Version <?= esc($data['version1']['version_number']) ?></h6>
                                                                    <button class="btn btn-sm btn-outline-secondary copy-btn"
                                                                        data-content="<?= esc($field['version1_value']) ?>"
                                                                        data-bs-toggle="tooltip" title="Copy content">
                                                                        <i class="fas fa-copy"></i>
                                                                    </button>
                                                                </div>
                                                                <?php if (in_array($field['field'], ['content'])): ?>
                                                                    <div class="content-preview bg-white border rounded p-2">
                                                                        <?= $field['version1_value'] ?: '<em class="text-muted">No content</em>' ?>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="text-break">
                                                                        <?= esc($field['version1_value']) ?: '<em class="text-muted">Empty</em>' ?>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <?php if (isset($field['version1_word_count'])): ?>
                                                                    <div class="word-count-change text-muted mt-2">
                                                                        <small>
                                                                            <i class="fas fa-chart-line me-1"></i>
                                                                            Words: <?= $field['version1_word_count'] ?>
                                                                            <?php if (isset($field['version1_char_count'])): ?>
                                                                                | Characters: <?= $field['version1_char_count'] ?>
                                                                            <?php endif; ?>
                                                                        </small>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="version-column bg-primary bg-opacity-10 p-3 rounded">
                                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                                    <h6 class="mb-0">Version <?= esc($data['version2']['version_number']) ?></h6>
                                                                    <button class="btn btn-sm btn-outline-secondary copy-btn"
                                                                        data-content="<?= esc($field['version2_value']) ?>"
                                                                        data-bs-toggle="tooltip" title="Copy content">
                                                                        <i class="fas fa-copy"></i>
                                                                    </button>
                                                                </div>
                                                                <?php if (in_array($field['field'], ['content'])): ?>
                                                                    <div class="content-preview bg-white border rounded p-2">
                                                                        <?= $field['version2_value'] ?: '<em class="text-muted">No content</em>' ?>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="text-break">
                                                                        <?= esc($field['version2_value']) ?: '<em class="text-muted">Empty</em>' ?>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <?php if (isset($field['version2_word_count'])): ?>
                                                                    <div class="word-count-change <?= $field['word_count_difference'] > 0 ? 'text-success' : ($field['word_count_difference'] < 0 ? 'text-danger' : 'text-muted') ?> mt-2">
                                                                        <small>
                                                                            <i class="fas fa-chart-line me-1"></i>
                                                                            Words: <?= $field['version2_word_count'] ?>
                                                                            <?php if ($field['word_count_difference'] != 0): ?>
                                                                                <span class="badge bg-<?= $field['word_count_difference'] > 0 ? 'success' : 'danger' ?>">
                                                                                    <?= $field['word_count_difference'] > 0 ? '+' : '' ?><?= $field['word_count_difference'] ?>
                                                                                </span>
                                                                            <?php endif; ?>
                                                                            <?php if (isset($field['version2_char_count'])): ?>
                                                                                | Characters: <?= $field['version2_char_count'] ?>
                                                                                <?php if (isset($field['char_count_difference']) && $field['char_count_difference'] != 0): ?>
                                                                                    <span class="badge bg-<?= $field['char_count_difference'] > 0 ? 'success' : 'danger' ?>">
                                                                                        <?= $field['char_count_difference'] > 0 ? '+' : '' ?><?= $field['char_count_difference'] ?>
                                                                                    </span>
                                                                                <?php endif; ?>
                                                                            <?php endif; ?>
                                                                        </small>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>                                                <?php else: ?>
                                                    <div class="text-center py-3">
                                                        <p class="mb-0 text-muted">
                                                            <i class="fas fa-check-circle text-success me-2"></i>
                                                            No changes in this field
                                                        </p>
                                                        <?php if (!empty($field['version1_value'])): ?>
                                                            <small class="text-muted">
                                                                Current value: <?= esc(strlen($field['version1_value']) > 100 ? substr($field['version1_value'], 0, 100) . '...' : $field['version1_value']) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>                    <!-- Metadata Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Comparison Metadata
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Version <?= esc($data['version1']['version_number']) ?> Details</h6>
                                            <ul class="list-unstyled">
                                                <li><strong>Created:</strong> <?= date('M j, Y g:i A', strtotime($data['comparison']['metadata']['version1_created_at'])) ?></li>
                                                <li><strong>Updated:</strong> <?= date('M j, Y g:i A', strtotime($data['comparison']['metadata']['version1_updated_at'])) ?></li>
                                                <li><strong>Status:</strong> <span class="badge bg-secondary"><?= esc($data['version1']['status']) ?></span></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Version <?= esc($data['version2']['version_number']) ?> Details</h6>
                                            <ul class="list-unstyled">
                                                <li><strong>Created:</strong> <?= date('M j, Y g:i A', strtotime($data['comparison']['metadata']['version2_created_at'])) ?></li>
                                                <li><strong>Updated:</strong> <?= date('M j, Y g:i A', strtotime($data['comparison']['metadata']['version2_updated_at'])) ?></li>
                                                <li><strong>Status:</strong> <span class="badge bg-primary"><?= esc($data['version2']['status']) ?></span></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6>Time Analysis</h6>
                                            <p class="mb-0">
                                                <strong>Time between versions:</strong>
                                                <span class="text-primary fw-bold">
                                                    <?php
                                                    $timeDiff = $data['comparison']['metadata']['time_difference'];
                                                    $days = floor($timeDiff / 86400);
                                                    $hours = floor(($timeDiff % 86400) / 3600);
                                                    $minutes = floor(($timeDiff % 3600) / 60);

                                                    if ($days > 0) {
                                                        echo "{$days} day" . ($days > 1 ? 's' : '') . ", {$hours} hour" . ($hours > 1 ? 's' : '');
                                                    } elseif ($hours > 0) {
                                                        echo "{$hours} hour" . ($hours > 1 ? 's' : '') . ", {$minutes} minute" . ($minutes > 1 ? 's' : '');
                                                    } else {
                                                        echo "{$minutes} minute" . ($minutes > 1 ? 's' : '');
                                                    }
                                                    ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    <!-- Final Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="/abstract-paper/edit/<?= esc($data['abstract']['id']) ?>" class="btn btn-primary me-2">
                                <i class="fas fa-edit me-2"></i>Edit Latest Version
                            </a>
                            <a href="/abstract-paper" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Abstract Papers
                            </a>
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

    <!-- App js -->
    <script src="/assets/js/app.js"></script>

    <!-- Abstract comparison js -->
    <script src="/assets/js/abstract-comparison.js"></script>    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize comparison page functionality
            if (typeof AbstractVersionComparison !== 'undefined') {
                window.abstractComparison = new AbstractVersionComparison();
            }
            
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

</body>

</html>