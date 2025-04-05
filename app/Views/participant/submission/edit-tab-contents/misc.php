<div class="tab-pane fade" id="steparrow-misc" role="tabpanel" aria-labelledby="steparrow-misc-tab">
    <div>
        <div class="mb-3">
            <label class="form-label" for="instagram-account">Instagram Account</label>
            <input type="text" class="form-control" id="instagram-account" placeholder="Enter your Instagram account">
        </div>

        <div class="mb-3">
            <label class="form-label" for="knowledge-source">Knowledge Source</label>
            <select class="form-select" id="knowledge-source">
                <option value="" selected disabled>Select knowledge source</option>
                <option value="instagram">Instagram</option>
                <option value="website">Website</option>
                <option value="friend">Friend</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="source-account-name">Source Account Name</label>
            <input type="text" class="form-control" id="source-account-name" placeholder="Enter source account name">
        </div>

        <div class="mb-3">
            <label class="form-label" for="twibbon-link">Twibbon Link</label>
            <div class="input-group">
                <input type="url" class="form-control" id="twibbon-link" placeholder="Enter twibbon link">
                <a href="#" class="btn btn-info" id="twibbon-guide-btn" data-bs-toggle="modal" data-bs-target="#twibbonGuideModal">
                    <i class="ri-information-line me-1"></i>Twibbon Guide
                </a>
            </div>
        </div>

        <!-- Twibbon Guide Modal -->
        <div class="modal fade" id="twibbonGuideModal" tabindex="-1" aria-labelledby="twibbonGuideModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="twibbonGuideModalLabel">Twibbon Guide</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Add your twibbon guide content here -->
                        <p>Follow these steps to use the twibbon:</p>
                        <ol>
                            <li>Visit the twibbon link <a href="<?= $currentProgram['twibbon'] ?>" target="_blank">here</a></li>
                            <li>Upload your photo</li>
                            <li>Download the generated image</li>
                            <li>Share to your social media</li>
                            <li>Copy and paste your twibbon post link to this input</li>
                        </ol>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Twibbon Guide Modal -->

        <div class="mb-3">
            <label class="form-label" for="share-desc">Share Description</label>
            <div class="form-text mb-2">
                <?php echo $currentProgram['share_desc']; ?>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="requirement-link">Requirement Link</label>
            <input type="url" class="form-control" id="requirement-link" placeholder="Enter requirement link">
        </div>

    </div>
    <div class="d-flex align-items-start gap-3 mt-4">
        <button type="button" class="btn btn-success btn-label right ms-auto nexttab" data-nexttab="steparrow-preview-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Preview</button>
    </div>
</div>
<!-- end tab pane -->