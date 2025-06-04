<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Abstract Management')); ?>

    <!-- quill css -->
    <link href="/assets/libs/quill/quill.core.css" rel="stylesheet" type="text/css" />
    <link href="/assets/libs/quill/quill.bubble.css" rel="stylesheet" type="text/css" />
    <link href="/assets/libs/quill/quill.snow.css" rel="stylesheet" type="text/css" />

    <!-- Sweet Alert css-->
    <link href="/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .bg-light-subtle {
            background-color: rgba(var(--bs-light-rgb), 0.5) !important;
        }

        /* Enhanced word count styling */
        .word-count-info {
            transition: all 0.3s ease;
        }

        .word-count-info .current-word-count {
            font-weight: 500;
        }

        .word-count-info.text-warning .current-word-count {
            animation: pulse-warning 1.5s infinite;
        }

        .word-count-info.text-danger .current-word-count {
            animation: pulse-danger 1s infinite;
        }

        @keyframes pulse-warning {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        @keyframes pulse-danger {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Quill editor styling for word limit exceeded */
        .ql-editor.exceeds-limit {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
    </style>

    <?= $this->include('partials/head-css') ?>

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

                    <?php
                    $pageTitle = isset($abstract) ? 'Edit Abstract' : 'Create New Abstract';
                    echo view('partials/page-title', array('pagetitle' => 'Abstract Management', 'title' => $pageTitle));
                    ?>

                    <!-- Display validation errors and flash messages --> <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('warning')): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <i class="bx bx-info-circle fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading"><?= session()->has('warning_title') ? session('warning_title') : 'Warning' ?></h5>
                                    <p class="mb-0"><?= session('warning') ?></p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($abstract) && isset($abstract['is_draft']) && $abstract['is_draft']): ?> <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <i class="bx bx-edit-alt fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading">Editing Draft Abstract</h5>
                                    <p class="mb-0">This abstract is currently saved as a draft. You can continue editing and save as a draft again, or complete all required fields and submit when ready.</p>
                                    <hr>
                                    <p class="mb-0 small">
                                        <i class="bx bx-info-circle me-1"></i> Topic and title are required to save a draft. All fields marked with <span class="text-danger">*</span> are required for final submission.
                                    </p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($abstract) && isset($abstract['current_version'])): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <i class="bx bx-revision fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading">Editing Abstract</h5>
                                    <p class="mb-0">You are currently editing version <?= $abstract['current_version']['version_number'] ?> of your abstract, created on <?= date('F j, Y', strtotime($abstract['current_version']['created_at'])) ?>.</p>
                                    <?php if (isset($abstract['current_version']['updated_at']) && $abstract['current_version']['created_at'] !== $abstract['current_version']['updated_at']): ?>
                                        <p class="mb-0 small">Last updated on <?= date('F j, Y', strtotime($abstract['current_version']['updated_at'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0"><?= $pageTitle ?></h4>
                                    <div class="text-muted small">
                                        <i class="bx bx-info-circle me-1"></i> Fields marked with <span class="text-danger">*</span> are required for final submission. Only <strong>Topic</strong> and <strong>Title</strong> are required for saving as draft.
                                    </div>                                </div>
                                <div class="card-body">
                                    <?php
                                    // Check if editing should be restricted
                                    $abstractStatus = '';
                                    $hasFeedback = false;
                                    $canEdit = true;
                                    
                                    if (isset($abstract)) {
                                        $abstractStatus = strtolower($abstract['status'] ?? 'draft');
                                        $hasFeedback = !empty($abstract['reviewers']);
                                        $canEdit = ($abstractStatus !== 'submitted') || $hasFeedback;
                                    }
                                    ?>
                                    
                                    <?php if (isset($abstract) && !$canEdit): ?>
                                        <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-lock fs-4 me-3 text-warning"></i>
                                                <div>
                                                    <h6 class="alert-heading mb-1">Editing Restricted</h6>
                                                    <p class="mb-0">This abstract has been submitted and is currently under review. You cannot make changes until reviewers provide feedback requiring revisions.</p>
                                                    <div class="mt-2">
                                                        <a href="<?= base_url('abstract-paper/view/' . $abstract['id']) ?>" class="btn btn-sm btn-primary">
                                                            <i class="bx bx-arrow-back me-1"></i> Back to View
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <style>
                                            #abstractForm {
                                                opacity: 0.6;
                                                pointer-events: none;
                                                user-select: none;
                                            }
                                        </style>
                                    <?php elseif (isset($abstract) && $abstractStatus === 'submitted' && $hasFeedback): ?>
                                        <div class="alert alert-info border-0 shadow-sm mb-4" role="alert">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-edit fs-4 me-3 text-info"></i>
                                                <div>
                                                    <h6 class="alert-heading mb-1">Revision Allowed</h6>
                                                    <p class="mb-0">Reviewers have provided feedback on your submitted abstract. You can now make revisions based on their comments.</p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form id="abstractForm" method="POST" action="<?= isset($abstract) ? '/abstract-paper/update/' . $abstract['id'] : '/abstract-paper/save' ?>"><?= csrf_field() ?>
                                        <input type="hidden" name="abstract_id" value="<?= isset($abstract) ? $abstract['id'] : '' ?>">
                                        <?php if (isset($abstract) && isset($abstract['current_version'])): ?>
                                            <input type="hidden" name="version_id" value="<?= $abstract['current_version']['id'] ?>">
                                            <input type="hidden" name="version_number" value="<?= $abstract['current_version']['version_number'] ?>">
                                        <?php endif; ?>
                                        <input type="hidden" name="program_id" value="<?= session()->get('current_program_id') ?>">
                                        <input type="hidden" name="primary_participant_id" value="<?= session()->get('current_participant_id') ?>">
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label for="abstract_topic_id" class="form-label">Topic <span class="text-danger">*</span></label>
                                                <select class="form-select" id="abstract_topic_id" name="abstract_topic_id" required>
                                                    <option value="">Select Topic</option>
                                                    <?php if (isset($topics) && is_array($topics)): ?>
                                                        <?php foreach ($topics as $topic): ?>
                                                            <option value="<?= $topic['id'] ?>"
                                                                data-description="<?= htmlspecialchars($topic['description'] ?? '') ?>"
                                                                <?= (isset($abstract) && isset($abstract['abstract_topic_id']) && $abstract['abstract_topic_id'] == $topic['id']) ? 'selected' : '' ?>>
                                                                <?= $topic['name'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                                <div class="invalid-feedback">Please select a topic.</div>
                                                <div id="topic-description" class="form-text text-muted mt-2"></div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="title" name="title"
                                                    value="<?= isset($abstract) && isset($abstract['current_version']) ? $abstract['current_version']['title'] : (isset($abstract) && isset($abstract['versions']) && !empty($abstract['versions']) ? $abstract['versions'][0]['title'] : '') ?>"
                                                    placeholder="Enter a concise and descriptive title for your abstract" required>
                                                <div class="d-flex justify-content-between">
                                                    <div class="invalid-feedback">Please enter the abstract title.</div>
                                                    <small class="text-muted mt-1 word-count-info">
                                                        <span class="current-word-count" id="title-word-count">0</span> / <?= isset($abstractSettings['title_length']) ? $abstractSettings['title_length'] : 15 ?> words
                                                    </small>
                                                </div>
                                                <div class="form-text text-muted">A good title should clearly represent the content and focus of your research.</div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label for="keywords" class="form-label">Keywords <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="keywords" name="keywords"
                                                    value="<?= isset($abstract) && isset($abstract['current_version']) ? $abstract['current_version']['keywords'] : (isset($abstract) && isset($abstract['versions']) && !empty($abstract['versions']) ? $abstract['versions'][0]['keywords'] : '') ?>"
                                                    placeholder="Enter keywords separated by commas">
                                                <div class="d-flex justify-content-between">
                                                    <div class="invalid-feedback">Please enter keywords.</div>
                                                    <small class="text-muted mt-1 word-count-info">
                                                        <span class="current-word-count" id="keywords-word-count">0</span> / <?= isset($abstractSettings['keywords_length']) ? $abstractSettings['keywords_length'] : 5 ?> words
                                                    </small>
                                                </div>
                                                <div class="form-text text-muted">Enter keywords separated by commas (e.g., research, medicine, science)</div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label class="form-label">Abstract Content <span class="text-danger">*</span></label>
                                                <div id="abstract-editor" style="height: 300px;">
                                                    <?= isset($abstract) && isset($abstract['current_version']) ? $abstract['current_version']['content'] : (isset($abstract) && isset($abstract['versions']) && !empty($abstract['versions']) ? $abstract['versions'][0]['content'] : '') ?>
                                                </div>
                                                <input type="hidden" name="content" id="abstract-content">
                                                <div class="d-flex justify-content-between">
                                                    <div class="invalid-feedback" id="content-feedback">Please enter abstract content.</div>
                                                    <small class="text-muted mt-1 word-count-info">
                                                        <span class="current-word-count" id="content-word-count">0</span> / <?= isset($abstractSettings['content_length']) ? $abstractSettings['content_length'] : 500 ?> words
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label for="refs" class="form-label">References <span class="text-danger">*</span></label>
                                                <textarea class="form-control" id="refs" name="refs" rows="6"
                                                    placeholder="Enter references in the required format (e.g., APA, IEEE, etc.)"><?= isset($abstract) && isset($abstract['current_version']) ? $abstract['current_version']['refs'] : (isset($abstract) && isset($abstract['versions']) && !empty($abstract['versions']) ? $abstract['versions'][0]['refs'] : '') ?></textarea>
                                                <div class="d-flex justify-content-between">
                                                    <div class="invalid-feedback">Please enter references.</div>
                                                    <small class="text-muted mt-1 word-count-info">
                                                        <span class="current-word-count" id="refs-word-count">0</span> / <?= isset($abstractSettings['refs_length']) ? $abstractSettings['refs_length'] : 120 ?> words
                                                    </small>
                                                </div>
                                                <div class="form-text text-muted">Include all references cited in your abstract following the conference's citation format</div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-lg-12">
                                                <div class="d-flex flex-column">
                                                    <div class="text-muted mb-3 ms-auto">
                                                        <small><i class="bx bx-info-circle me-1"></i> You can save your work as a draft with just <strong>Topic</strong> and <strong>Title</strong> and complete it later.</small>
                                                    </div>
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <button type="button" class="btn btn-secondary" id="save-draft-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Save your work without submitting">
                                                            <i class="bx bx-save me-1"></i> Save Draft                                                        </button> <button type="button" class="btn btn-primary" id="submit-btn">
                                                            <i class="bx bx-check-circle me-1"></i> Submit Abstract
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

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

    <!-- jQuery -->
    <script src="/assets/libs/jquery/jquery.min.js"></script>

    <!-- quill js -->
    <script src="/assets/libs/quill/quill.min.js"></script>

    <!-- Sweet Alerts js -->
    <script src="/assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- Abstract Version Manager -->
    <script src="/assets/js/abstract-version-manager.js"></script>

    <!-- init js -->
    <script>        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - starting initialization');
            console.log('jQuery available:', typeof $ !== 'undefined');
            console.log('SweetAlert available:', typeof Swal !== 'undefined');
            console.log('setupNewAbstractHandlers function:', typeof setupNewAbstractHandlers);
            
            // Word count limits from abstract settings (dynamic from controller)
            const WORD_LIMITS = {
                title: <?= isset($abstractSettings['title_length']) ? $abstractSettings['title_length'] : 15 ?>,
                keywords: <?= isset($abstractSettings['keywords_length']) ? $abstractSettings['keywords_length'] : 5 ?>,
                content: <?= isset($abstractSettings['content_length']) ? $abstractSettings['content_length'] : 500 ?>,
                refs: <?= isset($abstractSettings['refs_length']) ? $abstractSettings['refs_length'] : 120 ?>
            }; // Word counting function
            function countWords(text) {
                if (!text || text.trim() === '') return 0;
                return text.trim().split(/\s+/).length;
            }

            // Store last valid states for each field
            const lastValidStates = {
                title: {
                    value: '',
                    wordCount: 0
                },
                keywords: {
                    value: '',
                    wordCount: 0
                },
                content: {
                    value: '',
                    wordCount: 0
                },
                refs: {
                    value: '',
                    wordCount: 0
                }
            }; // Update word count display and validation with strict enforcement
            function updateWordCount(fieldId, text, limit, isStrict = true) {
                const wordCount = countWords(text);
                const countElement = document.getElementById(fieldId + '-word-count');
                const fieldElement = document.getElementById(fieldId);
                const wordCountInfo = countElement ? countElement.closest('.word-count-info') : null;

                if (countElement && wordCountInfo) {
                    // If word count exceeds limit and strict mode is enabled, restore last valid state
                    if (isStrict && wordCount > limit && lastValidStates[fieldId]) {
                        // Restore the field to its last valid state
                        if (fieldId === 'content') {
                            quill.root.innerHTML = lastValidStates[fieldId].value;
                        } else {
                            fieldElement.value = lastValidStates[fieldId].value;
                        }

                        // Update display with last valid count
                        countElement.textContent = lastValidStates[fieldId].wordCount;

                        // Show visual feedback
                        countElement.classList.add('text-danger', 'fw-bold');
                        wordCountInfo.classList.remove('text-muted', 'text-warning');
                        wordCountInfo.classList.add('text-danger');

                        // Show alert for exceeding limit
                        if (typeof YBBAlerts !== 'undefined') {
                            YBBAlerts.error(`Text exceeds the maximum word count of ${limit} words.`);
                        } else {
                            console.warn(`Text exceeds the maximum word count of ${limit} words.`);
                        }

                        return false;
                    } else {
                        // Update last valid state if within limit
                        if (wordCount <= limit) {
                            lastValidStates[fieldId] = {
                                value: text,
                                wordCount: wordCount
                            };
                        }

                        // Update display
                        countElement.textContent = wordCount;

                        // Update styling based on limit
                        if (wordCount > limit) {
                            countElement.classList.add('text-danger', 'fw-bold');
                            countElement.classList.remove('text-warning');
                            wordCountInfo.classList.remove('text-muted', 'text-warning');
                            wordCountInfo.classList.add('text-danger');
                            fieldElement.classList.add('is-invalid');
                        } else if (wordCount === limit) {
                            countElement.classList.add('text-warning', 'fw-bold');
                            countElement.classList.remove('text-danger');
                            wordCountInfo.classList.remove('text-muted', 'text-danger');
                            wordCountInfo.classList.add('text-warning');
                            fieldElement.classList.remove('is-invalid');
                        } else if (wordCount > limit * 0.9) { // Warning at 90%
                            countElement.classList.add('text-warning');
                            countElement.classList.remove('text-danger', 'fw-bold');
                            wordCountInfo.classList.remove('text-muted', 'text-danger');
                            wordCountInfo.classList.add('text-warning');
                            fieldElement.classList.remove('is-invalid');
                        } else {
                            countElement.classList.remove('text-danger', 'text-warning', 'fw-bold');
                            wordCountInfo.classList.remove('text-danger', 'text-warning');
                            wordCountInfo.classList.add('text-muted');
                            fieldElement.classList.remove('is-invalid');
                        }
                    }
                }

                return wordCount <= limit;
            }

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            }); // Initialize Quill editor
            var quill = new Quill('#abstract-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{
                            'header': 1
                        }, {
                            'header': 2
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'script': 'sub'
                        }, {
                            'script': 'super'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        ['clean']
                    ]
                },
                placeholder: 'Write your abstract content here...',
            }); // Initialize word counts for existing content after a small delay to ensure everything is ready
            setTimeout(function() {
                // Initialize last valid states with current content
                const titleValue = document.getElementById('title').value;
                const keywordsValue = document.getElementById('keywords').value;
                const contentValue = quill.getText();
                const refsValue = document.getElementById('refs').value;

                lastValidStates.title = {
                    value: titleValue,
                    wordCount: countWords(titleValue)
                };
                lastValidStates.keywords = {
                    value: keywordsValue,
                    wordCount: countWords(keywordsValue)
                };
                lastValidStates.content = {
                    value: quill.root.innerHTML,
                    wordCount: countWords(contentValue)
                };
                lastValidStates.refs = {
                    value: refsValue,
                    wordCount: countWords(refsValue)
                };

                // Update displays without strict enforcement for initial load
                updateWordCount('title', titleValue, WORD_LIMITS.title, false);
                updateWordCount('keywords', keywordsValue, WORD_LIMITS.keywords, false);
                updateWordCount('content', contentValue, WORD_LIMITS.content, false);
                updateWordCount('refs', refsValue, WORD_LIMITS.refs, false);
            }, 100); // Add word count tracking for title
            const titleElement = document.getElementById('title');
            titleElement.addEventListener('input', function() {
                updateWordCount('title', this.value, WORD_LIMITS.title, true);
            });
            titleElement.addEventListener('keyup', function() {
                updateWordCount('title', this.value, WORD_LIMITS.title, true);
            });
            titleElement.addEventListener('paste', function(e) {
                // Get current text and new text being pasted
                const currentText = this.value;
                const pastedText = e.clipboardData.getData('text');

                // Calculate what the combined text would be
                const combinedText = currentText + pastedText;
                const wordCount = countWords(combinedText);

                // If pasting would exceed the word limit, prevent it
                if (wordCount > WORD_LIMITS.title) {
                    e.preventDefault();
                    if (typeof YBBAlerts !== 'undefined') {
                        YBBAlerts.error('Pasting this text would exceed the maximum word count of ' + WORD_LIMITS.title + ' words.');
                    } else {
                        alert('Pasting this text would exceed the maximum word count of ' + WORD_LIMITS.title + ' words.');
                    }
                }
            }); // Add word count tracking for keywords
            const keywordsElement = document.getElementById('keywords');
            keywordsElement.addEventListener('input', function() {
                updateWordCount('keywords', this.value, WORD_LIMITS.keywords, true);
            });
            keywordsElement.addEventListener('keyup', function() {
                updateWordCount('keywords', this.value, WORD_LIMITS.keywords, true);
            });
            keywordsElement.addEventListener('paste', function(e) {
                // Get current text and new text being pasted
                const currentText = this.value;
                const pastedText = e.clipboardData.getData('text');

                // Calculate what the combined text would be
                const combinedText = currentText + pastedText;
                const wordCount = countWords(combinedText);

                // If pasting would exceed the word limit, prevent it
                if (wordCount > WORD_LIMITS.keywords) {
                    e.preventDefault();
                    if (typeof YBBAlerts !== 'undefined') {
                        YBBAlerts.error('Pasting this text would exceed the maximum word count of ' + WORD_LIMITS.keywords + ' words.');
                    } else {
                        alert('Pasting this text would exceed the maximum word count of ' + WORD_LIMITS.keywords + ' words.');
                    }
                }
            }); // Add word count tracking for Quill editor
            quill.on('text-change', function() {
                updateWordCount('content', quill.getText(), WORD_LIMITS.content, true);
            });

            // Handle paste events for Quill editor
            quill.clipboard.addMatcher(Node.TEXT_NODE, function(node, delta) {
                const currentText = quill.getText();
                const pastedText = node.textContent;
                const combinedText = currentText + pastedText;
                const wordCount = countWords(combinedText);

                if (wordCount > WORD_LIMITS.content) {
                    if (typeof YBBAlerts !== 'undefined') {
                        YBBAlerts.error('Pasting this text would exceed the maximum word count of ' + WORD_LIMITS.content + ' words.');
                    } else {
                        console.warn('Pasting this text would exceed the maximum word count of ' + WORD_LIMITS.content + ' words.');
                    }
                    return new Quill.import('delta')(); // Return empty delta to prevent paste
                }
                return delta;
            }); // Add word count tracking for references
            const refsElement = document.getElementById('refs');
            refsElement.addEventListener('input', function() {
                updateWordCount('refs', this.value, WORD_LIMITS.refs, true);
            });
            refsElement.addEventListener('keyup', function() {
                updateWordCount('refs', this.value, WORD_LIMITS.refs, true);
            });
            refsElement.addEventListener('paste', function(e) {
                // Get current text and new text being pasted
                const currentText = this.value;
                const pastedText = e.clipboardData.getData('text');

                // Calculate what the combined text would be
                const combinedText = currentText + pastedText;
                const wordCount = countWords(combinedText);

                // If pasting would exceed the word limit, prevent it
                if (wordCount > WORD_LIMITS.refs) {
                    e.preventDefault();
                    if (typeof YBBAlerts !== 'undefined') {
                        YBBAlerts.error('Pasting this text would exceed the maximum word count of ' + WORD_LIMITS.refs + ' words.');
                    } else {
                        alert('Pasting this text would exceed the maximum word count of ' + WORD_LIMITS.refs + ' words.');
                    }
                }
            });            // Show SweetAlert messages if there are flash messages
            <?php if (session()->has('abstract_success')): ?>
                <?php 
                $successData = session('abstract_success');
                $abstractId = $successData['id'] ?? null;
                $abstractTitle = $successData['title'] ?? 'Your Abstract';
                $status = $successData['status'] ?? 'unknown';
                $isDraft = $successData['is_draft'] ?? false;
                $message = $successData['message'] ?? '';
                ?>                Swal.fire({
                    title: '<?= $isDraft ? "Draft Saved!" : "Abstract Submitted!" ?>',
                    html: `
                        <div class="text-start">
                            <p><?= esc($message) ?></p>
                            <hr>
                            <div class="card bg-light mb-0 mt-3">
                                <div class="card-body p-3">
                                    <h6 class="card-title">Abstract Details</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>ID:</strong> <?= esc($abstractId) ?></li>
                                        <li><strong>Title:</strong> <?= esc($abstractTitle) ?></li>
                                        <li><strong>Status:</strong> <span class="badge bg-<?= $isDraft ? 'warning' : 'success' ?>"><?= ucfirst(esc($status)) ?></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                }).then(() => {
                    // Redirect to abstract-paper index to show the details
                    window.location.href = '<?= base_url("abstract-paper") ?>';
                });
            <?php elseif (session()->has('success')): ?>
                <?php
                $abstractData = session()->getFlashdata('abstract_data');
                $hasAbstractData = !empty($abstractData) && is_array($abstractData);
                ?>
                Swal.fire({
                    title: '<?= session()->has('success_title') ? session('success_title') : 'Success!' ?>',
                    html: `
                        <div class="text-start">
                            <p><?= session('success') ?></p>
                            <?php if ($hasAbstractData): ?>
                            <hr>
                            <div class="card bg-light mb-0 mt-3">
                                <div class="card-body p-3">
                                    <h6 class="card-title">Abstract Details</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>ID:</strong> <?= $abstractData['id'] ?? 'N/A' ?></li>
                                        <li><strong>Title:</strong> <?= $abstractData['title'] ?? 'Your Abstract' ?></li>
                                        <li><strong>Status:</strong> <span class="badge bg-<?= ($abstractData['status'] ?? '') === 'draft' ? 'warning' : 'info' ?>"><?= ucfirst($abstractData['status'] ?? 'Pending') ?></span></li>
                                    </ul>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                });
            <?php endif; ?><?php if (session()->has('error')): ?>
                Swal.fire({
                    title: '<?= session()->has('error_title') ? session('error_title') : 'Error!' ?>',
                    html: `
                        <div class="text-start">
                            <p><?= session('error') ?></p>
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="bx bx-info-circle me-1"></i>
                                <small>If this problem persists, please contact support with reference to the time of this error.</small>
                            </div>
                        </div>
                    `,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                });            <?php endif; ?>            // Form submission handling - clean implementation
            console.log('Setting up form handlers');
            console.log('jQuery available:', typeof $ !== 'undefined');
            console.log('SweetAlert available:', typeof Swal !== 'undefined');

            // Helper function to validate form
            function validateForm(isFullValidation = true) {
                console.log('Validating form, full validation:', isFullValidation);
                
                // Get editor content and set to hidden field
                const content = quill.root.innerHTML;
                document.getElementById('abstract-content').value = content;

                // Clear all validation states first
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.getElementById('content-feedback').style.display = 'none';

                let isValid = true;

                // Topic is required for both draft and full submission
                if (!document.getElementById('abstract_topic_id').value) {
                    document.getElementById('abstract_topic_id').classList.add('is-invalid');
                    isValid = false;
                }

                // Title is required for both draft and submission
                const titleValue = document.getElementById('title').value.trim();
                if (!titleValue) {
                    document.getElementById('title').classList.add('is-invalid');
                    isValid = false;
                } else {
                    const titleWordCount = countWords(titleValue);
                    if (titleWordCount > WORD_LIMITS.title) {
                        document.getElementById('title').classList.add('is-invalid');
                        isValid = false;
                    }
                }

                // For full submission, require additional fields
                if (isFullValidation) {
                    const keywordsValue = document.getElementById('keywords').value.trim();
                    if (!keywordsValue) {
                        document.getElementById('keywords').classList.add('is-invalid');
                        isValid = false;
                    } else {
                        const keywordsWordCount = countWords(keywordsValue);
                        if (keywordsWordCount > WORD_LIMITS.keywords) {
                            document.getElementById('keywords').classList.add('is-invalid');
                            isValid = false;
                        }
                    }

                    const contentText = quill.getText().trim();
                    if (contentText.length === 0) {
                        document.getElementById('abstract-editor').classList.add('is-invalid');
                        document.getElementById('content-feedback').style.display = 'block';
                        isValid = false;
                    } else {
                        const contentWordCount = countWords(contentText);
                        if (contentWordCount > WORD_LIMITS.content) {
                            document.getElementById('abstract-editor').classList.add('is-invalid');
                            document.getElementById('content-feedback').style.display = 'block';
                            document.getElementById('content-feedback').textContent = `Content exceeds maximum word limit of ${WORD_LIMITS.content} words.`;
                            isValid = false;
                        }
                    }

                    const refsValue = document.getElementById('refs').value.trim();
                    if (!refsValue) {
                        document.getElementById('refs').classList.add('is-invalid');
                        isValid = false;
                    } else {
                        const refsWordCount = countWords(refsValue);
                        if (refsWordCount > WORD_LIMITS.refs) {
                            document.getElementById('refs').classList.add('is-invalid');
                            isValid = false;
                        }
                    }
                }

                return isValid;
            }

            // Initialize form elements
            const abstractForm = document.getElementById('abstractForm');
            const submitBtn = document.getElementById('submit-btn');
            const saveDraftBtn = document.getElementById('save-draft-btn');
            
            console.log('Form elements found:', {
                form: abstractForm !== null,
                submitBtn: submitBtn !== null,
                saveDraftBtn: saveDraftBtn !== null
            });

            if (!abstractForm || !submitBtn || !saveDraftBtn) {
                console.error('Critical form elements not found!');
                return;
            }

            // Save Draft button handler
            saveDraftBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Save Draft button clicked');

                // Basic validation for draft
                const title = document.getElementById('title').value.trim();
                const topicId = document.getElementById('abstract_topic_id').value;

                if (!topicId || !title) {
                    Swal.fire({
                        title: 'Form Error!',
                        text: 'Please select a topic and provide a title for your draft.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#5156be'
                    });
                    return;
                }

                // Show saving indicator
                Swal.fire({
                    title: 'Saving Draft',
                    text: 'Your abstract will be saved as a draft.',
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Set content from Quill editor
                document.getElementById('abstract-content').value = quill.root.innerHTML;

                // Set status to draft
                let statusField = abstractForm.querySelector('input[name="status"]');
                if (!statusField) {
                    statusField = document.createElement('input');
                    statusField.type = 'hidden';
                    statusField.name = 'status';
                    abstractForm.appendChild(statusField);
                }
                statusField.value = 'draft';

                // Submit the form
                abstractForm.submit();
            });

            // Submit button handler
            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Submit button clicked');

                // Full validation for submission
                if (!validateForm(true)) {
                    Swal.fire({
                        title: 'Form Error!',
                        text: 'Please fill in all required fields correctly.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#5156be'
                    });
                    return;
                }

                // Show confirmation dialog
                Swal.fire({
                    title: 'Submit Abstract',
                    html: `
                        <div class="text-start">
                            <p>Are you sure you want to submit this abstract? This will finalize your submission.</p>
                            <p class="mb-0"><strong>Note:</strong></p>
                            <ul class="text-start mb-0">
                                <li>All required fields must be completed.</li>
                                <li>After submission, your abstract will be sent for review.</li>
                                <li>You can still view your submission and track its status.</li>
                            </ul>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, submit it!',
                    cancelButtonText: 'No, review again',
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Submitting Abstract',
                            text: 'Your abstract is being submitted for review.',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Set content from Quill editor
                        document.getElementById('abstract-content').value = quill.root.innerHTML;

                        // Set status to submitted
                        let statusField = abstractForm.querySelector('input[name="status"]');
                        if (!statusField) {
                            statusField = document.createElement('input');
                            statusField.type = 'hidden';
                            statusField.name = 'status';
                            abstractForm.appendChild(statusField);
                        }
                        statusField.value = 'submitted';

                        // Submit the form
                        abstractForm.submit();
                    }
                });
            });

            // Clear validation error on input - updated to work with new word count system
            const inputs = document.querySelectorAll('.form-control, .form-select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Only clear validation state for non-word-count fields
                    if (!['title', 'keywords', 'refs'].includes(this.id)) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Clear editor validation on input
            quill.on('text-change', function() {
                // Only clear validation state for content feedback, word count styling is handled separately
                document.getElementById('content-feedback').style.display = 'none';
                document.getElementById('content-feedback').textContent = 'Please enter abstract content.';
            }); // Handle topic selection to show description
            const topicSelect = document.getElementById('abstract_topic_id');
            const topicDescription = document.getElementById('topic-description');

            // Function to show/hide topic description
            function updateTopicDescription() {
                const selectedOption = topicSelect.options[topicSelect.selectedIndex];
                const description = selectedOption.getAttribute('data-description');

                // Simply update the text content directly
                topicDescription.textContent = description || '';
            }

            // Update description on page load if a topic is already selected
            if (topicSelect.value) {
                updateTopicDescription();
            }

            // Add event listener for topic selection change
            topicSelect.addEventListener('change', updateTopicDescription);
        });
    </script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
</body>

</html>