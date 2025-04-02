<div class="tab-pane fade" id="steparrow-misc" role="tabpanel" aria-labelledby="steparrow-misc-tab">
    <div>
        <div class="mb-3">
            <label for="misc-file" class="form-label">Upload Supporting Documents</label>
            <input class="form-control" type="file" id="misc-file" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="misc-comments">Additional Comments</label>
            <textarea class="form-control" id="misc-comments" rows="3" placeholder="Enter any additional information"></textarea>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="misc-terms" required>
            <label class="form-check-label" for="misc-terms">
                I agree to the terms and conditions
            </label>
            <div class="invalid-feedback">You must agree before submitting</div>
        </div>
    </div>
    <div class="d-flex align-items-start gap-3 mt-4">
        <button type="button" class="btn btn-light btn-label previestab" data-previous="steparrow-entry-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
        <button type="button" class="btn btn-success btn-label right ms-auto nexttab" data-nexttab="steparrow-preview-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Preview</button>
    </div>
</div>
<!-- end tab pane -->