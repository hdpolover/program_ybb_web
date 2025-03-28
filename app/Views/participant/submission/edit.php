<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/simple-title-meta', array('title' => 'Profile Settings')); ?>

    <?= $this->include('partials/head-css') ?>

    <!-- QuillJS CSS -->
    <link href="<?= base_url('assets/libs/quill/quill.core.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/libs/quill/quill.snow.css') ?>" rel="stylesheet" type="text/css" />
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
                    <?php echo view('partials/page-title', array('pagetitle' => 'Submission', 'title' => 'Edit Form')); ?>

                    <!--end card-->

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">WYF PARTICIPANT FORM</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <form action="#" class="form-steps" autocomplete="off">

                                <div class="step-arrow-nav mb-4">
                                    <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="steparrow-personal-tab" data-bs-toggle="pill" data-bs-target="#steparrow-personal" type="button" role="tab" aria-controls="steparrow-personal" aria-selected="true">Personal Details</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="steparrow-professional-tab" data-bs-toggle="pill" data-bs-target="#steparrow-professional" type="button" role="tab" aria-controls="steparrow-professional" aria-selected="false">Professional Profile</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="steparrow-entry-tab" data-bs-toggle="pill" data-bs-target="#steparrow-entry" type="button" role="tab" aria-controls="steparrow-entry" aria-selected="false">Entry Information</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="steparrow-misc-tab" data-bs-toggle="pill" data-bs-target="#steparrow-misc" type="button" role="tab" aria-controls="steparrow-misc" aria-selected="false">Miscellaneous</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="steparrow-preview-tab" data-bs-toggle="pill" data-bs-target="#steparrow-preview" type="button" role="tab" aria-controls="steparrow-preview" aria-selected="false">Preview</button>
                                        </li>
                                    </ul>
                                </div>

                                <div class="tab-content">
                                    <!-- Personal Details Tab -->
                                    <div class="tab-pane fade show active" id="steparrow-personal" role="tabpanel" aria-labelledby="steparrow-personal-tab">
                                        <div>
                                            <div class="text-center">
                                                <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                                                    <img src="/assets/images/users/avatar-1.jpg" class="rounded-circle avatar-xxl img-thumbnail user-profile-image" alt="user-profile-image">
                                                    <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                                        <input id="profile-img-file-input" type="file" class="profile-img-file-input">
                                                        <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                                            <span class="avatar-title rounded-circle bg-light text-body">
                                                                <i class="ri-camera-fill"></i>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="personal-fullname">Full Name</label>
                                                        <input type="text" class="form-control" id="personal-fullname" placeholder="Enter your full name" required>
                                                        <div class="invalid-feedback">Please enter your full name</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="personal-gender">Gender</label>
                                                        <select class="form-select" id="personal-gender" required>
                                                            <option value="">Select gender</option>
                                                            <option value="male">Male</option>
                                                            <option value="female">Female</option>
                                                            <option value="other">Other</option>
                                                            <option value="prefer-not">Prefer not to say</option>
                                                        </select>
                                                        <div class="invalid-feedback">Please select your gender</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="personal-birthdate">Birth Date</label>
                                                        <input type="date" class="form-control" id="personal-birthdate" required>
                                                        <div class="invalid-feedback">Please enter your birth date</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="personal-nationality">Nationality</label>
                                                        <div data-input-flag data-option-flag-img-name>
                                                            <input type="text" class="form-control rounded-end flag-input" readonly placeholder="Select nationality" data-bs-toggle="dropdown" aria-expanded="false" />
                                                            <div class="dropdown-menu w-100">
                                                                <div class="p-2 px-3 pt-1 searchlist-input">
                                                                    <input type="text" class="form-control form-control-sm border search-countryList" placeholder="Search country..." />
                                                                </div>
                                                                <ul class="list-unstyled dropdown-menu-list mb-0"></ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="personal-phone">Phone Number</label>
                                                        <div class="input-group" data-input-flag>
                                                            <button class="btn btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false"><img src="/assets/images/flags/us.svg" alt="flag img" height="20" class="country-flagimg rounded"><span class="ms-2 country-codeno">+ 1</span></button>
                                                            <input type="text" class="form-control rounded-end flag-input" id="personal-phone" value="" placeholder="Enter phone number" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" required />
                                                            <div class="dropdown-menu w-100">
                                                                <div class="p-2 px-3 pt-1 searchlist-input">
                                                                    <input type="text" class="form-control form-control-sm border search-countryList" placeholder="Search country name or code..." />
                                                                </div>
                                                                <ul class="list-unstyled dropdown-menu-list mb-0"></ul>
                                                            </div>
                                                            <div class="invalid-feedback">Please enter your phone number</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="personal-email">Email Address</label>
                                                        <input type="email" class="form-control" id="personal-email" placeholder="Enter your email" required>
                                                        <div class="invalid-feedback">Please enter a valid email address</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <h5 class="mt-3 mb-3">Emergency Contact</h5>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="emergency-name">Contact Name</label>
                                                        <input type="text" class="form-control" id="emergency-name" placeholder="Enter emergency contact name" required>
                                                        <div class="invalid-feedback">Please enter emergency contact name</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="emergency-relationship">Relationship</label>
                                                        <select class="form-select" id="emergency-relationship" required>
                                                            <option value="">Select relationship</option>
                                                            <option value="parent">Parent</option>
                                                            <option value="spouse">Spouse</option>
                                                            <option value="sibling">Sibling</option>
                                                            <option value="relative">Other Relative</option>
                                                            <option value="friend">Friend</option>
                                                            <option value="guardian">Legal Guardian</option>
                                                            <option value="other">Other</option>
                                                        </select>
                                                        <div class="invalid-feedback">Please select relationship</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="emergency-phone">Emergency Contact Phone</label>
                                                        <div class="input-group" data-input-flag>
                                                            <button class="btn btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false"><img src="/assets/images/flags/us.svg" alt="flag img" height="20" class="country-flagimg rounded"><span class="ms-2 country-codeno">+ 1</span></button>
                                                            <input type="text" class="form-control rounded-end flag-input" id="emergency-phone" value="" placeholder="Enter phone number" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" required />
                                                            <div class="dropdown-menu w-100">
                                                                <div class="p-2 px-3 pt-1 searchlist-input">
                                                                    <input type="text" class="form-control form-control-sm border search-countryList" placeholder="Search country name or code..." />
                                                                </div>
                                                                <ul class="list-unstyled dropdown-menu-list mb-0"></ul>
                                                            </div>
                                                            <div class="invalid-feedback">Please enter emergency contact phone number</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="emergency-email">Emergency Contact Email</label>
                                                        <input type="email" class="form-control" id="emergency-email" placeholder="Enter emergency contact email">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start gap-3 mt-4">
                                            <button type="button" class="btn btn-success btn-label right ms-auto nexttab" data-nexttab="steparrow-professional-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next Step</button>
                                        </div>
                                    </div>
                                    <!-- end tab pane -->

                                    <!-- Professional Profile Tab -->
                                    <div class="tab-pane fade" id="steparrow-professional" role="tabpanel" aria-labelledby="steparrow-professional-tab">
                                        <div>

                                            <div class="mb-3">
                                                <label class="form-label" for="professional-occupation">Occupation</label>
                                                <input type="text" class="form-control" id="professional-occupation" placeholder="Enter your occupation" required>
                                                <div class="invalid-feedback">Please enter your occupation</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="professional-organization">Organization</label>
                                                <input type="text" class="form-control" id="professional-organization" placeholder="Enter organization name" required>
                                                <div class="invalid-feedback">Please enter your organization</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="professional-experience">Years of Experience</label>
                                                <select class="form-select" id="professional-experience" required>
                                                    <option value="">Select experience</option>
                                                    <option value="0-2">0-2 years</option>
                                                    <option value="3-5">3-5 years</option>
                                                    <option value="6-10">6-10 years</option>
                                                    <option value="10+">More than 10 years</option>
                                                </select>
                                                <div class="invalid-feedback">Please select your experience</div>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <button type="button" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="pills-experience-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Submit</button>
                                        </div>
                                    </div>
                                    <!-- end tab pane -->

                                    <!-- Entry Information Tab -->
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

                                    <!-- Miscellaneous Tab -->
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

                                    <!-- Preview Tab -->
                                    <div class="tab-pane fade" id="steparrow-preview" role="tabpanel" aria-labelledby="steparrow-preview-tab">
                                        <div class="text-center">
                                            <div class="avatar-md mt-5 mb-4 mx-auto">
                                                <div class="avatar-title bg-light text-success display-4 rounded-circle">
                                                    <i class="ri-checkbox-circle-fill"></i>
                                                </div>
                                            </div>
                                            <h5>Review Your Submission</h5>
                                            <p class="text-muted">Please review all the information you've provided before submitting.</p>

                                            <div class="d-flex justify-content-center gap-3 mt-4">
                                                <button type="button" class="btn btn-light btn-label previestab" data-previous="steparrow-misc-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Back to Edit</button>
                                                <button type="submit" class="btn btn-success">Submit Application</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end tab pane -->
                                </div>
                                <!-- end tab content -->
                            </form>
                        </div>
                        <!-- end card body -->
                    </div>

                    <!--end row-->

                </div>
                <!-- container-fluid -->
            </div><!-- End Page-content -->

            <?= $this->include('partials/footer') ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->


    <?= $this->include('partials/vendor-scripts') ?>

    <script>
        // Set base URL for the flag-input script to use
        var baseAssetsUrl = "<?= base_url('assets/json/') ?>";
    </script>

    <!-- input flag init -->
    <script src="/assets/js/custom/submission-flag-input.init.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>

</body>

</html>