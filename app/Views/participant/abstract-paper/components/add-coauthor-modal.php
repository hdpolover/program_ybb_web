<?php
// Define essential variables for this component with safety checks
$participant_data = $participant_data ?? [];
$abstract = $participant_data['abstract'] ?? [];
?>
<!-- Manage Authors Modal -->
<div class="modal fade" id="addCoAuthorModal" tabindex="-1" aria-labelledby="addCoAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addCoAuthorModalLabel">
                    <i class="bx bx-user-plus me-2"></i>Manage Authors
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Navigation Tabs -->
                <div class="bg-light border-bottom">
                    <ul class="nav nav-tabs nav-tabs-custom border-0 m-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active px-4 py-3" data-bs-toggle="tab" href="#authorList" role="tab">
                                <i class="bx bx-list-ul me-2"></i>Current Authors
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" data-bs-toggle="tab" href="#addAuthor" role="tab">
                                <i class="bx bx-plus-circle me-2"></i>Add New Author
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="tab-content p-4">
                    <!-- Author List Tab Content -->
                    <div class="tab-pane fade show active" id="authorList" role="tabpanel">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="text-primary mb-0">
                                        <i class="bx bx-users me-2"></i>Authors for this Abstract
                                    </h6>
                                    <span class="badge bg-soft-primary text-primary">
                                        <?= count($participant_data['abstract']['authors'] ?? []) ?> Author(s)
                                    </span>
                                </div>

                                <?php if (!empty($participant_data['abstract']['authors'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="border-0 fw-semibold">#</th>
                                                    <th class="border-0 fw-semibold">Author Details</th>
                                                    <th class="border-0 fw-semibold">Institution</th>
                                                    <th class="border-0 fw-semibold">Role</th>
                                                    <th class="border-0 fw-semibold text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody> <?php foreach ($participant_data['abstract']['authors'] as $index => $author): ?>
                                                    <tr class="border-bottom">
                                                        <td class="fw-medium"><?= $index + 1 ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm me-3">
                                                                    <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                        <i class="bx bx-user fs-5"></i>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1 fw-semibold"><?= esc($author['full_name']) ?></h6>
                                                                    <p class="text-muted mb-0 small">
                                                                        <i class="bx bx-envelope me-1"></i><?= esc($author['email'] ?? 'No email provided') ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="text-truncate" style="max-width: 200px;">
                                                                <i class="bx bx-buildings me-1 text-muted"></i>
                                                                <?= esc($author['institution'] ?? 'Not specified') ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="text-center">
                                                                <?php if (isset($author['is_participant']) && $author['is_participant'] == '1'): ?>
                                                                    <span class="badge bg-primary fs-6 px-3 py-2">
                                                                        <i class="bx bx-star me-1"></i>Primary Author
                                                                    </span>
                                                                <?php elseif (isset($author['is_presenting']) && $author['is_presenting'] == '1'): ?>
                                                                    <span class="badge bg-success fs-6 px-3 py-2">
                                                                        <i class="bx bx-microphone me-1"></i>Presenting Author
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary fs-6 px-3 py-2">
                                                                        <i class="bx bx-user me-1"></i>Co-Author
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex justify-content-center gap-1">
                                                                <button type="button" class="btn btn-soft-info btn-sm view-author"
                                                                    data-author='<?= json_encode($author) ?>'
                                                                    data-bs-toggle="tooltip" title="View Details">
                                                                    <i class="bx bx-show"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-soft-primary btn-sm edit-author"
                                                                    data-author='<?= json_encode($author) ?>'
                                                                    data-bs-toggle="tooltip" title="Edit Author">
                                                                    <i class="bx bx-edit"></i>
                                                                </button>
                                                                <?php if (!(isset($author['is_participant']) && $author['is_participant'] == '1')): ?>
                                                                    <button type="button" class="btn btn-soft-danger btn-sm delete-author"
                                                                        data-author-id="<?= $author['id'] ?>"
                                                                        data-author-name="<?= esc($author['full_name']) ?>"
                                                                        data-bs-toggle="tooltip" title="Remove Author">
                                                                        <i class="bx bx-trash"></i>
                                                                    </button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <div class="avatar-lg mx-auto mb-4">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                <i class="bx bx-user-plus fs-2"></i>
                                            </div>
                                        </div>
                                        <h5 class="text-muted mb-3">No Authors Added Yet</h5>
                                        <p class="text-muted mb-4">Click "Add New Author" tab to add co-authors to your abstract.</p>
                                        <button type="button" class="btn btn-primary" onclick="document.querySelector('[href=&quot;#addAuthor&quot;]').click()">
                                            <i class="bx bx-plus me-1"></i>Add Your First Author
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div><!-- Add Author Tab Content -->
                    <div class="tab-pane fade" id="addAuthor" role="tabpanel">
                        <form id="addAuthorForm" action="<?= base_url('abstract-paper/add-author') ?>" method="post">
                            <input type="hidden" name="abstract_id" value="<?= $participant_data['abstract']['id'] ?>">
                            <input type="hidden" name="program_id" value="<?= session()->get('current_program_id') ?>">
                            <input type="hidden" name="participant_id" id="selected_participant_id">

                            <!-- Author Type Selection Cards -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 text-primary">
                                    <i class="bx bx-user-plus me-2"></i>Author Information
                                </h6>
                                <p class="text-muted mb-3">Please specify if this author is already registered in the program or is a new contributor.</p>

                                <div class="row g-3">
                                    <!-- Registered Participant Card -->
                                    <div class="col-md-6">
                                        <div class="card border-2 h-100 author-type-card" data-type="participant">
                                            <div class="card-body text-center p-4">
                                                <div class="avatar-lg mx-auto mb-3">
                                                    <div class="avatar-title bg-soft-success text-success rounded-circle">
                                                        <i class="bx bx-user-check fs-2"></i>
                                                    </div>
                                                </div>
                                                <h5 class="card-title mb-2">Registered Participant</h5>
                                                <p class="card-text text-muted small">
                                                    This author is already registered in the current program.
                                                    We'll search by email to auto-fill their details.
                                                </p>                                                <input type="radio" name="is_participant" value="1" id="is_participant_yes" class="form-check-input d-none">
                                                <label for="is_participant_yes" class="btn btn-outline-success btn-sm">
                                                    <i class="bx bx-search me-1"></i>Search Participant
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- New Author Card -->
                                    <div class="col-md-6">
                                        <div class="card border-2 h-100 author-type-card selected" data-type="new">
                                            <div class="card-body text-center p-4">
                                                <div class="avatar-lg mx-auto mb-3">
                                                    <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                        <i class="bx bx-user-plus fs-2"></i>
                                                    </div>
                                                </div>
                                                <h5 class="card-title mb-2">New Author</h5>
                                                <p class="card-text text-muted small">
                                                    This author is not registered in the program.
                                                    You'll need to manually enter their information.
                                                </p>
                                                <input type="radio" name="is_participant" value="0" id="is_participant_no" class="form-check-input d-none" checked>
                                                <label for="is_participant_no" class="btn btn-primary btn-sm">
                                                    <i class="bx bx-plus me-1"></i>Add Manually
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Participant Search Section -->
                            <div id="participant_search_section" class="mb-4" style="display: none;">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-primary text-white rounded-circle">
                                                    <i class="bx bx-search fs-5"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Search Registered Participant</h6>
                                                <p class="text-muted mb-0 small">Enter the participant's email address to search</p>
                                            </div>
                                        </div>

                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="bx bx-envelope text-muted"></i>
                                            </span>
                                            <input type="email" class="form-control border-start-0" id="search_email"
                                                placeholder="participant@example.com" autocomplete="off">
                                            <button type="button" class="btn btn-primary" id="search_participant_btn">
                                                <i class="bx bx-search me-1"></i>
                                                <span class="btn-text">Search</span>
                                                <span class="spinner-border spinner-border-sm d-none" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </span>
                                            </button>
                                        </div>

                                        <div id="search_result"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Author Details Form -->
                            <div id="author_details_section">
                                <div class="card">
                                    <div class="card-header bg-white">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-info text-white rounded-circle">
                                                    <i class="bx bx-user fs-5"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Author Details</h6>
                                                <p class="text-muted mb-0 small">Enter the author's information below</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="full_name" class="form-label fw-semibold">
                                                    <i class="bx bx-user me-1 text-primary"></i>Full Name
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="full_name" name="full_name"
                                                    placeholder="Enter author's full name" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label fw-semibold">
                                                    <i class="bx bx-envelope me-1 text-primary"></i>Email Address
                                                    <span class="text-danger participant-required">*</span>
                                                    <span class="text-muted non-participant-optional">(Optional for non-participants)</span>
                                                </label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    placeholder="author@example.com" required>
                                                <div class="form-text text-muted">
                                                    <small class="participant-text">
                                                        <i class="bx bx-info-circle me-1"></i>We'll check if this email can be added to your abstract
                                                    </small>
                                                    <small class="non-participant-text d-none">
                                                        <i class="bx bx-info-circle me-1"></i>Email is optional for non-participants but recommended for communication
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for="institution" class="form-label fw-semibold">
                                                    <i class="bx bx-buildings me-1 text-primary"></i>Institution/Organization
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="institution" name="institution"
                                                    placeholder="University or organization name" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="button" class="btn btn-light me-md-2" data-bs-dismiss="modal">
                                    <i class="bx bx-x me-1"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-user-plus me-1"></i>Add Author
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
