<div class="tab-pane fade" id="steparrow-professional" role="tabpanel" aria-labelledby="steparrow-professional-tab">
    <div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="professional-education">Education Level</label>
                    <select class="form-select" id="professional-education" required>
                        <option value="">Select education level</option>
                        <option value="high-school">High School</option>
                        <option value="diploma">Diploma</option>
                        <option value="bachelors">Bachelor's Degree</option>
                        <option value="masters">Master's Degree</option>
                        <option value="doctorate">Doctorate</option>
                        <option value="other">Other</option>
                    </select>
                    <div class="invalid-feedback">Please select your education level</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="professional-institution">Institution</label>
                    <input type="text" class="form-control" id="professional-institution" placeholder="Enter your institution name" required>
                    <div class="invalid-feedback">Please enter your institution name</div>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="professional-major">Major/Field of Study</label>
            <input type="text" class="form-control" id="professional-major" placeholder="Enter your major or field of study" required>
            <div class="invalid-feedback">Please enter your major</div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="professional-occupation">Occupation</label>
                    <input type="text" class="form-control" id="professional-occupation" placeholder="Enter your occupation" required>
                    <div class="invalid-feedback">Please enter your occupation</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="professional-organization">Organization</label>
                    <input type="text" class="form-control" id="professional-organization" placeholder="Enter organization name" required>
                    <div class="invalid-feedback">Please enter your organization</div>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="professional-experiences">Professional Experiences</label>
            <div id="professional-experiences-editor" class="snow-editor" style="height: 200px;"></div>
            <input type="hidden" id="professional-experiences" name="professional-experiences">
            <div class="form-text">Describe your relevant professional experiences</div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="professional-achievements">Achievements</label>
            <div id="professional-achievements-editor" class="snow-editor" style="height: 200px;"></div>
            <input type="hidden" id="professional-achievements" name="professional-achievements">
            <div class="form-text">List your key achievements and recognitions</div>
        </div>
        <div class="mb-3">
            <label for="professional-resume" class="form-label">Upload CV/Resume (Optional)</label>
            <input class="form-control" type="file" id="professional-resume" accept=".pdf,.doc,.docx">
            <div class="form-text">Accepted formats: PDF, DOC, DOCX (Max size: 5MB)</div>
        </div>
    </div>
    <div class="d-flex align-items-start gap-3 mt-4">
        <button type="button" class="btn btn-success btn-label right ms-auto nexttab" data-nexttab="steparrow-entry-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next Step</button>
    </div>
</div>
<!-- end tab pane -->