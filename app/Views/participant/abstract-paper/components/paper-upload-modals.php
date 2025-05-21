<!-- Upload Paper Modal -->
<div class="modal fade" id="uploadPaperModal" tabindex="-1" aria-labelledby="uploadPaperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPaperModalLabel">Upload Full Paper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('participant/abstract/upload-paper/' . $abstractData['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <i class="bx bx-info-circle me-1"></i> Please upload your full paper in PDF format. Maximum file size: 10MB.
                    </div>

                    <div class="mb-3">
                        <label for="paperFile" class="form-label">Select Paper File (PDF)</label>
                        <input type="file" class="form-control" id="paperFile" name="paper_file" accept=".pdf" required>
                    </div>

                    <div class="mb-3">
                        <label for="paperVersion" class="form-label">Version</label>
                        <input type="text" class="form-control" id="paperVersion" name="paper_version" value="1.0" required>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePaperModalLabel">Update Paper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('participant/abstract/update-paper/' . $abstractData['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <i class="bx bx-info-circle me-1"></i> You are updating your existing paper. Please provide the new version.
                    </div>

                    <div class="mb-3">
                        <label for="updatePaperFile" class="form-label">Select New Paper File (PDF)</label>
                        <input type="file" class="form-control" id="updatePaperFile" name="paper_file" accept=".pdf" required>
                    </div>

                    <div class="mb-3">
                        <label for="updatePaperVersion" class="form-label">New Version</label>
                        <input type="text" class="form-control" id="updatePaperVersion" name="paper_version" value="<?= esc($abstractData['paper_version'] ?? '1.0') ?>" required>
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
