<div class="tab-pane fade" id="steparrow-entry" role="tabpanel" aria-labelledby="steparrow-entry-tab">
    <div>
        <div class="mb-3">
            <label class="form-label" for="entry-title">Entry Title</label>
            <input type="text" class="form-control" id="entry-title" placeholder="Enter your entry title" required>
            <div class="invalid-feedback">Please enter your entry title</div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="entry-category">Category</label>
            <select class="form-select" id="entry-category" required>
                <option value="">Select category</option>
                <option value="category1">Category 1</option>
                <option value="category2">Category 2</option>
                <option value="category3">Category 3</option>
            </select>
            <div class="invalid-feedback">Please select a category</div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="entry-description">Project Description</label>
            <textarea class="form-control" id="entry-description" rows="4" placeholder="Describe your project" required></textarea>
            <div class="invalid-feedback">Please provide a project description</div>
        </div>
    </div>
    <div class="d-flex align-items-start gap-3 mt-4">
        <button type="button" class="btn btn-light btn-label previestab" data-previous="steparrow-professional-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Previous</button>
        <button type="button" class="btn btn-success btn-label right ms-auto nexttab" data-nexttab="steparrow-misc-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next Step</button>
    </div>
</div>
<!-- end tab pane -->