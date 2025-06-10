<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Abstract Version Comparison')); ?>

    <?= $this->include('partials/head-css') ?> <!-- Custom CSS for comparison page -->
    <link href="/assets/css/comparison.css" rel="stylesheet" type="text/css" />
    <!-- Enhanced comparison page styles -->
    <style>
        .comparison-header {
            background: linear-gradient(135deg, #405189 0%, #5a67d8 100%);
            border-radius: 0.75rem;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 4px 20px rgba(64, 81, 137, 0.15);
        }

        .version-timeline {
            position: relative;
            padding: 2rem 0;
        }

        .version-timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
            transform: translateX(-50%);
        }

        .version-card {
            border: 2px solid transparent;
            transition: all 0.3s ease;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            background: white;
        }

        .version-card.older {
            border-color: #f1734f;
            background: linear-gradient(45deg, #fef5f0 0%, #ffffff 100%);
        }

        .version-card.newer {
            border-color: #299cdb;
            background: linear-gradient(45deg, #f0f9ff 0%, #ffffff 100%);
        }

        .version-card .card-body {
            padding: 2rem;
        }

        .version-badge {
            position: absolute;
            top: -10px;
            right: 20px;
            z-index: 10;
            border-radius: 1.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .comparison-field {
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        .comparison-field:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-1px);
        }

        .field-header {
            background: linear-gradient(90deg, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 1rem 1.5rem;
        }

        .field-header:hover {
            background: linear-gradient(90deg, #e9ecef 0%, #f8f9fa 100%);
        }

        .field-content {
            background: white;
            padding: 0;
        }

        .version-column {
            position: relative;
            border-radius: 0;
            padding: 1.5rem;
        }

        .version-column.older {
            background: linear-gradient(135deg, #fef5f0 0%, #fdf2ed 100%);
            border-left: 4px solid #f1734f;
        }

        .version-column.newer {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-left: 4px solid #299cdb;
        }

        .action-buttons {
            position: sticky;
            top: 80px;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .content-preview {
            max-height: 200px;
            overflow-y: auto;
            border-radius: 0.5rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            border: 1px solid #e9ecef;
        }

        .collapse-toggle {
            transition: transform 0.3s ease;
            color: #405189;
        }

        .edit-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .edit-status-badge.editable {
            background: linear-gradient(45deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .edit-status-badge.not-editable {
            background: linear-gradient(45deg, #fee2e2, #fecaca);
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Status badge colors matching the web theme */
        .badge.bg-secondary {
            background-color: #6c757d !important;
        }

        .badge.bg-info {
            background-color: #299cdb !important;
        }

        .badge.bg-warning {
            background-color: #f1734f !important;
        }

        .badge.bg-success {
            background-color: #64748b !important;
        }

        /* Button theme matching */
        .btn-primary {
            background-color: #405189;
            border-color: #405189;
        }

        .btn-primary:hover {
            background-color: #364471;
            border-color: #364471;
        }

        .btn-outline-primary {
            color: #405189;
            border-color: #405189;
        }

        .btn-outline-primary:hover {
            background-color: #405189;
            border-color: #405189;
        }

        .btn-outline-warning {
            color: #f1734f;
            border-color: #f1734f;
        }

        .btn-outline-warning:hover {
            background-color: #f1734f;
            border-color: #f1734f;
        }

        .btn-outline-success {
            color: #64748b;
            border-color: #64748b;
        }

        .btn-outline-success:hover {
            background-color: #64748b;
            border-color: #64748b;
        }

        /* Enhanced badges */
        .badge.bg-warning.bg-opacity-20 {
            background-color: rgba(241, 115, 79, 0.1) !important;
            color: #f1734f !important;
            border-color: #f1734f !important;
        }

        .badge.bg-success.bg-opacity-20 {
            background-color: rgba(100, 116, 139, 0.1) !important;
            color: #64748b !important;
            border-color: #64748b !important;
        }

        .badge.bg-danger.bg-opacity-20 {
            background-color: rgba(220, 53, 69, 0.1) !important;
            color: #dc3545 !important;
            border-color: #dc3545 !important;
        }

        @media (max-width: 768px) {
            .version-timeline::before {
                display: none;
            }

            .comparison-header {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .action-buttons {
                padding: 1rem;
                margin-bottom: 1.5rem;
            }

            .version-column {
                padding: 1rem;
            }
        }
    </style>

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

                    <?php echo view('partials/page-title', array('pagetitle' => 'Abstract Paper', 'title' => 'Version Comparison')); ?>

                    <?php
                    // Debug: Check if data exists and log structure
                    if (!isset($data) || empty($data)) {
                        echo '<div class="alert alert-danger">
                                <h4>No Comparison Data Available</h4>
                                <p>The comparison data could not be loaded. Please check:</p>
                                <ul>
                                    <li>Both version IDs are valid</li>
                                    <li>You have access to the abstract</li>
                                    <li>The versions exist in the system</li>
                                </ul>
                                <a href="/abstract-paper" class="btn btn-primary">Back to Abstract Papers</a>
                              </div>';
                        echo '</div></div></div></div></body></html>';
                        exit;
                    }

                    // Check required data structure
                    $requiredKeys = ['version1', 'version2', 'comparison', 'abstract'];
                    $missingKeys = [];
                    foreach ($requiredKeys as $key) {
                        if (!isset($data[$key])) {
                            $missingKeys[] = $key;
                        }
                    }

                    if (!empty($missingKeys)) {
                        echo '<div class="alert alert-warning">
                                <h4>Incomplete Comparison Data</h4>
                                <p>Missing data sections: ' . implode(', ', $missingKeys) . '</p>
                                <p>Available data keys: ' . implode(', ', array_keys($data)) . '</p>
                                <a href="/abstract-paper" class="btn btn-primary">Back to Abstract Papers</a>
                              </div>';
                        echo '</div></div></div></div></body></html>';
                        exit;
                    }

                    // Optional debug section (uncomment to debug data structure)
                    // if (isset($_GET['debug']) && $_GET['debug'] === '1') {
                    //     echo '<div class="alert alert-info">
                    //             <h4>Debug Information</h4>
                    //             <pre>' . print_r($data, true) . '</pre>
                    //           </div>';
                    // }
                    ?> <!-- Enhanced Comparison Header -->
                    <div class="comparison-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bx bx-git-compare fs-1 me-3 opacity-75"></i>
                                    <div>
                                        <h3 class="mb-1 fw-bold">Version Comparison</h3>
                                        <p class="mb-0 opacity-75">
                                            <?= esc($data['abstract']['title'] ?? 'Abstract Title') ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Quick Stats -->
                                <div class="d-flex flex-wrap gap-3 mt-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-changes me-2"></i>
                                        <span class="fw-semibold"><?= $data['comparison']['summary']['total_changes'] ?? 0 ?></span>
                                        <span class="opacity-75 ms-1">changes</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-time me-2"></i>
                                        <span class="fw-semibold">
                                            <?php
                                            $timeDiff = $data['comparison']['metadata']['time_difference'] ?? 0;
                                            if ($timeDiff > 0) {
                                                $days = floor($timeDiff / 86400);
                                                $hours = floor(($timeDiff % 86400) / 3600);
                                                if ($days > 0) {
                                                    echo "{$days}d {$hours}h";
                                                } elseif ($hours > 0) {
                                                    echo "{$hours}h";
                                                } else {
                                                    echo floor($timeDiff / 60) . "m";
                                                }
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </span>
                                        <span class="opacity-75 ms-1">apart</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <!-- Edit Status and Action -->
                                <?php
                                $latestStatus = strtolower($data['version2']['status'] ?? 'draft');
                                $isEditable = in_array($latestStatus, ['draft', 'under_review']) ||
                                    ($latestStatus === 'submitted' && !empty($data['abstract']['has_feedback']));
                                ?>
                                <div class="mb-3">
                                    <div class="edit-status-badge <?= $isEditable ? 'editable' : 'not-editable' ?>">
                                        <i class="bx <?= $isEditable ? 'bx-edit' : 'bx-lock' ?>"></i>
                                        <?= $isEditable ? 'Editable' : 'Read Only' ?>
                                    </div>
                                </div>

                                <div class="d-flex flex-column gap-2">
                                    <?php if ($isEditable && isset($data['abstract']['id'])): ?>
                                        <a href="/abstract-paper/edit/<?= esc($data['abstract']['id']) ?>"
                                            class="btn btn-light btn-sm">
                                            <i class="bx bx-edit me-1"></i> Edit Latest
                                        </a>
                                    <?php endif; ?>
                                    <a href="/abstract-paper" class="btn btn-outline-light btn-sm">
                                        <i class="bx bx-arrow-back me-1"></i> Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div> <!-- Compact Version Timeline -->
                    <div class="version-timeline mb-4">
                        <div class="row align-items-center g-4">
                            <div class="col-md-5">
                                <div class="version-card older position-relative">
                                    <div class="version-badge bg-warning text-dark">Older Version</div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h5 class="mb-0 fw-bold text-dark">Version <?= esc($data['version1']['version_number'] ?? 'Unknown') ?></h5>
                                            <?php
                                            $status1 = strtolower($data['version1']['status'] ?? 'draft');
                                            $statusConfig1 = [
                                                'draft' => ['class' => 'bg-secondary', 'icon' => 'bx-edit'],
                                                'submitted' => ['class' => 'bg-info', 'icon' => 'bx-paper-plane'],
                                                'under_review' => ['class' => 'bg-warning', 'icon' => 'bx-time-five'],
                                                'accepted' => ['class' => 'bg-success', 'icon' => 'bx-check-circle']
                                            ];
                                            $config1 = $statusConfig1[$status1] ?? $statusConfig1['draft'];
                                            ?>
                                            <span class="badge <?= $config1['class'] ?> d-flex align-items-center gap-1 px-3 py-2">
                                                <i class="bx <?= $config1['icon'] ?>"></i>
                                                <?= esc($data['version1']['status'] ?? 'Draft') ?>
                                            </span>
                                        </div>
                                        <h6 class="text-muted mb-3 fw-normal"><?= esc($data['version1']['title'] ?? 'Untitled') ?></h6>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="bx bx-time me-2"></i>
                                            <span class="fw-medium">
                                                <?= isset($data['version1']['created_at']) ? date('M j, Y • g:i A', strtotime($data['version1']['created_at'])) : 'Unknown' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 text-center">
                                <div class="d-flex flex-column align-items-center py-3">
                                    <i class="bx bx-git-compare fs-1 text-muted mb-2" style="font-size: 2.5rem !important;"></i>
                                    <span class="fw-semibold text-muted text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Compare</span>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="version-card newer position-relative">
                                    <div class="version-badge bg-success text-white">Latest Version</div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h5 class="mb-0 fw-bold text-dark">Version <?= esc($data['version2']['version_number'] ?? 'Unknown') ?></h5>
                                            <?php
                                            $status2 = strtolower($data['version2']['status'] ?? 'draft');
                                            $statusConfig2 = [
                                                'draft' => ['class' => 'bg-secondary', 'icon' => 'bx-edit'],
                                                'submitted' => ['class' => 'bg-info', 'icon' => 'bx-paper-plane'],
                                                'under_review' => ['class' => 'bg-warning', 'icon' => 'bx-time-five'],
                                                'accepted' => ['class' => 'bg-success', 'icon' => 'bx-check-circle']
                                            ];
                                            $config2 = $statusConfig2[$status2] ?? $statusConfig2['draft'];
                                            ?>
                                            <span class="badge <?= $config2['class'] ?> d-flex align-items-center gap-1 px-3 py-2">
                                                <i class="bx <?= $config2['icon'] ?>"></i>
                                                <?= esc($data['version2']['status'] ?? 'Draft') ?>
                                            </span>
                                        </div>
                                        <h6 class="text-muted mb-3 fw-normal"><?= esc($data['version2']['title'] ?? 'Untitled') ?></h6>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="bx bx-time me-2"></i>
                                            <span class="fw-medium">
                                                <?= isset($data['version2']['created_at']) ? date('M j, Y • g:i A', strtotime($data['version2']['created_at'])) : 'Unknown' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- Compact Action Controls -->
                    <div class="action-buttons">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="bx bx-search text-muted"></i>
                                    </span>
                                    <input type="text" id="fieldSearch" class="form-control border-0"
                                        placeholder="Search fields..." style="background: #f8f9fa;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="btn-group btn-group-sm w-100" role="group">
                                    <input type="radio" class="btn-check" name="viewFilter" id="showAll" value="all" checked>
                                    <label class="btn btn-outline-primary" for="showAll">
                                        <i class="bx bx-list-ul me-1"></i>All
                                    </label>

                                    <input type="radio" class="btn-check" name="viewFilter" id="showChanged" value="changed">
                                    <label class="btn btn-outline-warning" for="showChanged">
                                        <i class="bx bx-changes me-1"></i>Changed
                                    </label>

                                    <input type="radio" class="btn-check" name="viewFilter" id="showUnchanged" value="unchanged">
                                    <label class="btn btn-outline-success" for="showUnchanged">
                                        <i class="bx bx-check me-1"></i>Same
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div><!-- Enhanced Field Comparison -->
                    <div class="row">
                        <div class="col-12">
                            <?php if (!empty($data['comparison']['fields'])): ?>
                                <?php foreach ($data['comparison']['fields'] as $field): ?> <div class="comparison-field" data-field="<?= esc($field['field'] ?? '') ?>">
                                        <div class="field-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <i class="bx bx-tag me-2 text-primary"></i>
                                                    <span class="fw-semibold"><?= esc($field['label'] ?? ucfirst(str_replace('_', ' ', $field['field'] ?? ''))) ?></span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if ($field['has_change'] ?? false): ?>
                                                        <span class="badge bg-warning bg-opacity-20 text-warning border border-warning">
                                                            <i class="bx bx-changes me-1"></i>Changed
                                                        </span>
                                                        <?php if (isset($field['word_count_difference']) && $field['word_count_difference'] != 0): ?>
                                                            <span class="badge bg-<?= $field['word_count_difference'] > 0 ? 'success' : 'danger' ?> bg-opacity-20 
                                                                         text-<?= $field['word_count_difference'] > 0 ? 'success' : 'danger' ?> 
                                                                         border border-<?= $field['word_count_difference'] > 0 ? 'success' : 'danger' ?>">
                                                                <?= $field['word_count_difference'] > 0 ? '+' : '' ?><?= $field['word_count_difference'] ?> words
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="badge bg-success bg-opacity-20 text-success border border-success">
                                                            <i class="bx bx-check me-1"></i>No Change
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field-content">
                                            <?php if ($field['has_change'] ?? false): ?>
                                                <div class="row g-0">
                                                    <div class="col-md-6">
                                                        <div class="version-column older p-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="fw-semibold text-muted text-uppercase">
                                                                    Version <?= esc($data['version1']['version_number'] ?? 'Unknown') ?>
                                                                </small>
                                                                <button class="btn btn-sm btn-outline-secondary copy-btn"
                                                                    data-content="<?= esc($field['version1_value'] ?? '') ?>"
                                                                    title="Copy content">
                                                                    <i class="bx bx-copy"></i>
                                                                </button>
                                                            </div>

                                                            <?php if (in_array($field['field'] ?? '', ['content'])): ?>
                                                                <div class="content-preview border rounded p-2 bg-light">
                                                                    <?= ($field['version1_value'] ?? '') ?: '<em class="text-muted">No content</em>' ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="text-break small">
                                                                    <?= esc($field['version1_value'] ?? '') ?: '<em class="text-muted">Empty</em>' ?>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if (isset($field['version1_word_count'])): ?>
                                                                <div class="text-muted mt-2">
                                                                    <small>
                                                                        <i class="bx bx-text me-1"></i>
                                                                        <?= $field['version1_word_count'] ?> words
                                                                        <?php if (isset($field['version1_char_count'])): ?>
                                                                            • <?= $field['version1_char_count'] ?> chars
                                                                        <?php endif; ?>
                                                                    </small>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="version-column newer p-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="fw-semibold text-muted text-uppercase">
                                                                    Version <?= esc($data['version2']['version_number'] ?? 'Unknown') ?>
                                                                </small>
                                                                <button class="btn btn-sm btn-outline-secondary copy-btn"
                                                                    data-content="<?= esc($field['version2_value'] ?? '') ?>"
                                                                    title="Copy content">
                                                                    <i class="bx bx-copy"></i>
                                                                </button>
                                                            </div>

                                                            <?php if (in_array($field['field'] ?? '', ['content'])): ?>
                                                                <div class="content-preview border rounded p-2 bg-light">
                                                                    <?= ($field['version2_value'] ?? '') ?: '<em class="text-muted">No content</em>' ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="text-break small">
                                                                    <?= esc($field['version2_value'] ?? '') ?: '<em class="text-muted">Empty</em>' ?>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if (isset($field['version2_word_count'])): ?>
                                                                <div class="text-muted mt-2">
                                                                    <small>
                                                                        <i class="bx bx-text me-1"></i>
                                                                        <?= $field['version2_word_count'] ?> words
                                                                        <?php if (($field['word_count_difference'] ?? 0) != 0): ?>
                                                                            <span class="badge bg-<?= ($field['word_count_difference'] ?? 0) > 0 ? 'success' : 'danger' ?> ms-1">
                                                                                <?= ($field['word_count_difference'] ?? 0) > 0 ? '+' : '' ?><?= $field['word_count_difference'] ?? 0 ?>
                                                                            </span>
                                                                        <?php endif; ?>
                                                                        <?php if (isset($field['version2_char_count'])): ?>
                                                                            • <?= $field['version2_char_count'] ?> chars
                                                                        <?php endif; ?>
                                                                    </small>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-center py-4 bg-light">
                                                    <div class="text-success mb-2">
                                                        <i class="bx bx-check-circle fs-2"></i>
                                                    </div>
                                                    <p class="mb-1 fw-semibold text-muted">No changes in this field</p>
                                                    <?php if (!empty($field['version1_value'] ?? '')): ?>
                                                        <small class="text-muted">
                                                            <?= esc(strlen($field['version1_value'] ?? '') > 80 ? substr($field['version1_value'], 0, 80) . '...' : ($field['version1_value'] ?? '')) ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <div class="text-info mb-3">
                                        <i class="bx bx-info-circle" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="text-muted">No comparison data available</h5>
                                    <p class="text-muted">There are no fields to compare between these versions.</p>
                                    <a href="/abstract-paper" class="btn btn-primary">
                                        <i class="bx bx-arrow-back me-1"></i>Back to Abstracts
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div> <!-- Compact Final Actions -->
                    <div class="text-center mt-4 pt-4 border-top">
                        <?php
                        $latestStatus = strtolower($data['version2']['status'] ?? 'draft');
                        $isEditable = in_array($latestStatus, ['draft', 'under_review']) ||
                            ($latestStatus === 'submitted' && !empty($data['abstract']['has_feedback']));
                        ?>

                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <?php if ($isEditable && isset($data['abstract']['id'])): ?>
                                <a href="/abstract-paper/edit/<?= esc($data['abstract']['id']) ?>"
                                    class="btn btn-primary">
                                    <i class="bx bx-edit me-2"></i>Edit Latest Version
                                </a>
                            <?php endif; ?>

                            <a href="/abstract-paper/view/<?= esc($data['abstract']['id'] ?? '') ?>"
                                class="btn btn-outline-secondary">
                                <i class="bx bx-show me-2"></i>View Abstract
                            </a> <a href="/abstract-paper" class="btn btn-outline-secondary back-button">
                                <i class="bx bx-arrow-back me-2"></i>Back to List
                            </a>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                Comparison generated on <?= date('M j, Y \a\t g:i A') ?> •
                                Time between versions:
                                <?php
                                $timeDiff = $data['comparison']['metadata']['time_difference'] ?? 0;
                                if ($timeDiff > 0) {
                                    $days = floor($timeDiff / 86400);
                                    $hours = floor(($timeDiff % 86400) / 3600);
                                    $minutes = floor(($timeDiff % 3600) / 60);
                                    if ($days > 0) {
                                        echo "{$days} day" . ($days > 1 ? 's' : '') . " {$hours} hour" . ($hours > 1 ? 's' : '');
                                    } elseif ($hours > 0) {
                                        echo "{$hours} hour" . ($hours > 1 ? 's' : '') . " {$minutes} minute" . ($minutes > 1 ? 's' : '');
                                    } else {
                                        echo "{$minutes} minute" . ($minutes > 1 ? 's' : '');
                                    }
                                } else {
                                    echo 'Unknown';
                                }
                                ?>
                            </small>
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
    <script src="/assets/js/abstract-comparison.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize comparison page functionality
            if (typeof AbstractVersionComparison !== 'undefined') {
                window.abstractComparison = new AbstractVersionComparison();
            }

            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Search functionality
            const searchInput = document.getElementById('fieldSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    document.querySelectorAll('.comparison-field').forEach(field => {
                        const fieldName = field.querySelector('.field-header span').textContent.toLowerCase();
                        if (fieldName.includes(searchTerm)) {
                            field.style.display = 'block';
                        } else {
                            field.style.display = 'none';
                        }
                    });
                });
            }

            // Filter functionality
            document.querySelectorAll('input[name="viewFilter"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const filterValue = this.value;
                    document.querySelectorAll('.comparison-field').forEach(field => {
                        const hasChangeBadge = field.querySelector('.badge[class*="bg-warning"]');
                        const hasChange = hasChangeBadge !== null;

                        if (filterValue === 'all') {
                            field.style.display = 'block';
                        } else if (filterValue === 'changed' && hasChange) {
                            field.style.display = 'block';
                        } else if (filterValue === 'unchanged' && !hasChange) {
                            field.style.display = 'block';
                        } else {
                            field.style.display = 'none';
                        }
                    });
                });
            }); // Copy functionality with tooltip feedback
            document.querySelectorAll('.copy-btn').forEach(btn => {
                const tooltip = new bootstrap.Tooltip(btn);

                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const content = this.getAttribute('data-content');
                    navigator.clipboard.writeText(content).then(() => {
                        // Update tooltip text
                        btn.setAttribute('title', 'Copied!');
                        tooltip.dispose();
                        new bootstrap.Tooltip(btn).show();

                        // Show visual feedback
                        const originalIcon = this.innerHTML;
                        this.innerHTML = '<i class="bx bx-check text-success"></i>';

                        // Reset after delay
                        setTimeout(() => {
                            this.innerHTML = originalIcon;
                            btn.setAttribute('title', 'Copy content');
                            tooltip.dispose();
                            new bootstrap.Tooltip(btn);
                        }, 1500);
                    }).catch(() => {
                        // Fallback for older browsers
                        const textArea = document.createElement('textarea');
                        textArea.value = content;
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);

                        // Show same feedback
                        btn.setAttribute('title', 'Copied!');
                        tooltip.dispose();
                        new bootstrap.Tooltip(btn).show();

                        const originalIcon = this.innerHTML;
                        this.innerHTML = '<i class="bx bx-check text-success"></i>';
                        setTimeout(() => {
                            this.innerHTML = originalIcon;
                            btn.setAttribute('title', 'Copy content');
                            tooltip.dispose();
                            new bootstrap.Tooltip(btn);
                        }, 1500);
                    });
                });
            });

            // Back navigation handler
            const handleBackNavigation = (e) => {
                e.preventDefault();

                // Close any open SweetAlert modals
                if (Swal.isVisible()) {
                    Swal.close();
                }

                // Close any open Bootstrap modals
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                });

                // Close any open tooltips
                const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltips.forEach(tooltip => {
                    const bsTooltip = bootstrap.Tooltip.getInstance(tooltip);
                    if (bsTooltip) {
                        bsTooltip.dispose();
                    }
                });

                // Delay navigation slightly to ensure cleanup
                setTimeout(() => {
                    window.location.href = e.target.closest('a').href;
                }, 100);
            };

            // Add click handler to all back buttons
            document.querySelectorAll('a[href="/abstract-paper"]').forEach(backBtn => {
                backBtn.addEventListener('click', handleBackNavigation);
            });

            // Handle page unload cleanup
            window.addEventListener('beforeunload', function() {
                // Close any open SweetAlert modals
                if (Swal.isVisible()) {
                    Swal.close();
                }
            });
        });
    </script>

</body>

</html>