<?php
// Define essential variables for this component with safety checks
$participant_data = $participant_data ?? [];
$abstract = $participant_data['abstract'] ?? [];
?>
<!-- Upload Paper Modal -->
<div class="modal fade" id="uploadPaperModal" tabindex="-1" aria-labelledby="uploadPaperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPaperModalLabel">Upload Full Paper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>            </div>
            <form action="<?= base_url('abstract-paper/upload-paper/' . $participant_data['abstract']['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <i class="bx bx-info-circle me-1"></i> Please upload your full paper in PDF format. Maximum file size: 10MB.
                    </div>                    <div class="mb-3">
                        <label for="paperFile" class="form-label">Select Paper File (PDF)</label>
                        <input type="file" class="form-control" id="paperFile" name="paper_file" accept=".pdf" required>
                    </div>

                    <div class="mb-3">
                        <label for="paperNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="paperNotes" name="paper_notes" rows="3" placeholder="Any additional notes about your paper..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-upload me-1"></i> Upload Paper
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Paper Modal -->
<div class="modal fade" id="updatePaperModal" tabindex="-1" aria-labelledby="updatePaperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content">
            <div class="modal-header">                <h5 class="modal-title" id="updatePaperModalLabel">Update Paper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('abstract-paper/update-paper/' . $participant_data['abstract']['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <i class="bx bx-info-circle me-1"></i> You are updating your existing paper. Please provide the new version.
                    </div>

                    <div class="mb-3">
                        <label for="updatePaperFile" class="form-label">Select New Paper File (PDF)</label>                        <input type="file" class="form-control" id="updatePaperFile" name="paper_file" accept=".pdf" required>
                    </div>

                    <div class="mb-3">
                        <label for="updateNotes" class="form-label">Change Notes (Optional)</label>
                        <textarea class="form-control" id="updateNotes" name="change_notes" rows="3" placeholder="Describe what changes you've made in this version..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-refresh me-1"></i> Update Paper
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Replace Paper Modal -->
<div class="modal fade" id="replacePaperModal" tabindex="-1" aria-labelledby="replacePaperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replacePaperModalLabel">Replace Paper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('abstract-paper/replace-paper/' . $participant_data['abstract']['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-warning mb-4">
                        <i class="bx bx-warning me-1"></i> You are about to replace your current paper. This action cannot be undone.
                    </div>

                    <div class="mb-3">
                        <label for="replacePaperFile" class="form-label">Select New Paper File (PDF)</label>                        <input type="file" class="form-control" id="replacePaperFile" name="paper_file" accept=".pdf" required>
                    </div>

                    <div class="mb-3">
                        <label for="replaceNotes" class="form-label">Replacement Notes (Optional)</label>
                        <textarea class="form-control" id="replaceNotes" name="replacement_notes" rows="3" placeholder="Describe why you're replacing the paper..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bx bx-refresh me-1"></i> Replace Paper
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Paper Modal -->
<div class="modal fade" id="deletePaperModal" tabindex="-1" aria-labelledby="deletePaperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePaperModalLabel">Confirm Paper Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('abstract-paper/delete-paper/' . $participant_data['abstract']['id']) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-soft-danger text-danger rounded-circle">
                                <i class="bx bx-trash fs-2"></i>
                            </div>
                        </div>
                        <h6 class="text-danger">Are you sure you want to delete your paper?</h6>
                        <p class="text-muted small mb-0">This action cannot be undone. Your paper file will be permanently removed.</p>
                    </div>

                    <div class="mb-3">
                        <label for="deletionReason" class="form-label">Reason for deletion (Optional)</label>
                        <textarea class="form-control" id="deletionReason" name="deletion_reason" rows="2" placeholder="Why are you removing the paper?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-trash me-1"></i> Delete Paper
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
