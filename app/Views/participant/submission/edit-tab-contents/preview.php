<div class="tab-pane fade" id="steparrow-preview" role="tabpanel" aria-labelledby="steparrow-preview-tab">
    <div class="text-center">
        <div class="avatar-md mt-5 mb-4 mx-auto">
            <div class="avatar-title bg-light text-success display-4 rounded-circle">
                <i class="ri-checkbox-circle-fill"></i>
            </div>
        </div>

        <p class="text-muted mb-4">
            <?= isset($currentProgram['confirmation_desc']) ? $currentProgram['confirmation_desc'] : 'Please review your submission details before submitting.' ?>
        </p>

        <div class="d-flex justify-content-center gap-3 mt-4">
            <button type="submit" class="btn btn-success">Submit Application</button>
        </div>
    </div>
</div>
<!-- end tab pane -->