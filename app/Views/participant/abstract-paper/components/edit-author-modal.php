<?php
// Define essential variables for this component with safety checks
$participant_data = $participant_data ?? [];
$abstract = $participant_data['abstract'] ?? [];
?>
<!-- Edit Author Modal -->
<div class="modal fade" id="editAuthorModal" tabindex="-1" aria-labelledby="editAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editAuthorModalLabel">
                    <i class="bx bx-edit me-1"></i> Edit Author
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAuthorForm" action="<?= base_url('abstract-paper/update-author') ?>" method="post">
                    <input type="hidden" name="author_id" id="edit_author_id">
                    <input type="hidden" name="abstract_id" value="<?= $participant_data['abstract']['id'] ?>">

                    <div class="mb-3">
                        <label for="edit_full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_institution" class="form-label">Institution <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_institution" name="institution" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Address</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editAuthorForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- View Author Modal -->
<div class="modal fade" id="viewAuthorModal" tabindex="-1" aria-labelledby="viewAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewAuthorModalLabel">
                    <i class="bx bx-user-circle me-1"></i> Author Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="avatar avatar-lg">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                            <i class="bx bxs-user-circle fs-1"></i>
                        </span>
                    </div>
                    <h5 class="mt-3 mb-1" id="view_full_name">John Doe</h5>
                    <div class="mb-2" id="view_role_badge">
                        <span class="badge bg-secondary">Co-Author</span>
                    </div>
                </div>

                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><i class="bx bx-envelope text-primary me-2"></i>Email</h6>
                        </div>
                        <p class="mb-1" id="view_email">example@example.com</p>
                    </div>                    <div class="list-group-item px-0">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><i class="bx bx-buildings text-primary me-2"></i>Institution</h6>
                        </div>
                        <p class="mb-1" id="view_institution">University of Example</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary edit-from-view">
                    <i class="bx bx-edit me-1"></i> Edit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Author Confirmation Modal -->
<div class="modal fade" id="deleteAuthorModal" tabindex="-1" aria-labelledby="deleteAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAuthorModalLabel">
                    <i class="bx bx-trash me-1"></i> Delete Author
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bx bx-error-circle text-danger fs-1"></i>
                    <h4 class="mt-2">Are you sure?</h4>
                    <p class="text-muted">Do you really want to delete <strong id="delete_author_name"></strong>? This action cannot be undone.</p>
                </div>
                <form id="deleteAuthorForm" action="<?= base_url('abstract-paper/delete-author') ?>" method="post">
                    <input type="hidden" name="author_id" id="delete_author_id">
                    <input type="hidden" name="abstract_id" value="<?= $participant_data['abstract']['id'] ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteAuthorForm" class="btn btn-danger">
                    <i class="bx bx-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>
