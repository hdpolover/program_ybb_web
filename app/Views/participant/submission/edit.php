<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/simple-title-meta', array('title' => 'Profile Settings')); ?>

    <?= $this->include('partials/head-css') ?>
    
    <!-- QuillJS CSS -->
    <link href="<?= base_url('assets/libs/quill/quill.core.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/libs/quill/quill.snow.css') ?>" rel="stylesheet" type="text/css" />

    <!-- Custom CSS for nav tabs -->
    <style>
        .nav-tabs-custom {
            border-bottom: 2px solid #e9ebec;
        }
        
        .nav-tabs-custom .nav-item {
            position: relative;
        }
        
        .nav-tabs-custom .nav-item .nav-link {
            border: none;
        }
        
        .nav-tabs-custom .nav-item .nav-link.active {
            color: var(--vz-primary);
        }
        
        .nav-tabs-custom .nav-item .nav-link.active:after {
            content: "";
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--vz-primary);
            transition: all 0.3s ease;
        }

        .nav-tabs-custom .nav-item .nav-link:hover {
            color: var(--vz-primary);
        }
    </style>

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
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#personal-details" role="tab">
                                                <i class="fas fa-home"></i> Personal Details
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#achievements" role="tab">
                                                <i class="far fa-user"></i> Achievements
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#questions" role="tab">
                                                <i class="far fa-envelope"></i> Questions
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#miscs" role="tab">
                                                <i class="far fa-envelope"></i> Miscs
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#preview" role="tab">
                                                <i class="far fa-envelope"></i> Preview
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body p-4">
                                    <div class="tab-content">
                                        <?= $this->include('participant/submission/edit-tab-contents/personal') ?>

                                        <?= $this->include('participant/submission/edit-tab-contents/achievements') ?>

                                        <div class="tab-pane" id="experience" role="tabpanel">
                                            <form>
                                                <div id="newlink">
                                                    <div id="1">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="mb-3">
                                                                    <label for="jobTitle" class="form-label">Job Title</label>
                                                                    <input type="text" class="form-control" id="jobTitle" placeholder="Job title" value="Lead Designer / Developer">
                                                                </div>
                                                            </div>
                                                            <!--end col-->
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="companyName" class="form-label">Company Name</label>
                                                                    <input type="text" class="form-control" id="companyName" placeholder="Company name" value="Themesbrand">
                                                                </div>
                                                            </div>
                                                            <!--end col-->
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="experienceYear" class="form-label">Experience Years</label>
                                                                    <div class="row">
                                                                        <div class="col-lg-5">
                                                                            <select class="form-control" data-choices data-choices-search-false name="experienceYear" id="experienceYear">
                                                                                <option value="">Select years</option>
                                                                                <option value="Choice 1">2001</option>
                                                                                <option value="Choice 2">2002</option>
                                                                                <option value="Choice 3">2003</option>
                                                                                <option value="Choice 4">2004</option>
                                                                                <option value="Choice 5">2005</option>
                                                                                <option value="Choice 6">2006</option>
                                                                                <option value="Choice 7">2007</option>
                                                                                <option value="Choice 8">2008</option>
                                                                                <option value="Choice 9">2009</option>
                                                                                <option value="Choice 10">2010</option>
                                                                                <option value="Choice 11">2011</option>
                                                                                <option value="Choice 12">2012</option>
                                                                                <option value="Choice 13">2013</option>
                                                                                <option value="Choice 14">2014</option>
                                                                                <option value="Choice 15">2015</option>
                                                                                <option value="Choice 16">2016</option>
                                                                                <option value="Choice 17" selected>2017</option>
                                                                                <option value="Choice 18">2018</option>
                                                                                <option value="Choice 19">2019</option>
                                                                                <option value="Choice 20">2020</option>
                                                                                <option value="Choice 21">2021</option>
                                                                                <option value="Choice 22">2022</option>
                                                                            </select>
                                                                        </div>
                                                                        <!--end col-->
                                                                        <div class="col-auto align-self-center">
                                                                            to
                                                                        </div>
                                                                        <!--end col-->
                                                                        <div class="col-lg-5">
                                                                            <select class="form-control" data-choices data-choices-search-false name="choices-single-default2">
                                                                                <option value="">Select years</option>
                                                                                <option value="Choice 1">2001</option>
                                                                                <option value="Choice 2">2002</option>
                                                                                <option value="Choice 3">2003</option>
                                                                                <option value="Choice 4">2004</option>
                                                                                <option value="Choice 5">2005</option>
                                                                                <option value="Choice 6">2006</option>
                                                                                <option value="Choice 7">2007</option>
                                                                                <option value="Choice 8">2008</option>
                                                                                <option value="Choice 9">2009</option>
                                                                                <option value="Choice 10">2010</option>
                                                                                <option value="Choice 11">2011</option>
                                                                                <option value="Choice 12">2012</option>
                                                                                <option value="Choice 13">2013</option>
                                                                                <option value="Choice 14">2014</option>
                                                                                <option value="Choice 15">2015</option>
                                                                                <option value="Choice 16">2016</option>
                                                                                <option value="Choice 17">2017</option>
                                                                                <option value="Choice 18">2018</option>
                                                                                <option value="Choice 19">2019</option>
                                                                                <option value="Choice 20" selected>2020</option>
                                                                                <option value="Choice 21">2021</option>
                                                                                <option value="Choice 22">2022</option>
                                                                            </select>
                                                                        </div>
                                                                        <!--end col-->
                                                                    </div>
                                                                    <!--end row-->
                                                                </div>
                                                            </div>
                                                            <!--end col-->
                                                            <div class="col-lg-12">
                                                                <div class="mb-3">
                                                                    <label for="jobDescription" class="form-label">Job Description</label>
                                                                    <textarea class="form-control" id="jobDescription" rows="3" placeholder="Enter description">You always want to make sure that your fonts work well together and try to limit the number of fonts you use to three or less. Experiment and play around with the fonts that you already have in the software you're working with reputable font websites. </textarea>
                                                                </div>
                                                            </div>
                                                            <!--end col-->
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <a class="btn btn-success" href="javascript:deleteEl(1)">Delete</a>
                                                            </div>
                                                        </div>
                                                        <!--end row-->
                                                    </div>
                                                </div>
                                                <div id="newForm" style="display: none;">

                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="hstack gap-2">
                                                        <button type="submit" class="btn btn-success">Update</button>
                                                        <a href="javascript:new_link()" class="btn btn-primary">Add New</a>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </form>
                                        </div>
                                        <!--end tab-pane-->
                                        <div class="tab-pane" id="privacy" role="tabpanel">
                                            <div class="mb-4 pb-2">
                                                <h5 class="card-title text-decoration-underline mb-3">Security:</h5>
                                                <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0">
                                                    <div class="flex-grow-1">
                                                        <h6 class="fs-14 mb-1">Two-factor Authentication</h6>
                                                        <p class="text-muted">Two-factor authentication is an enhanced security meansur. Once enabled, you'll be required to give two types of identification when you log into Google Authentication and SMS are Supported.</p>
                                                    </div>
                                                    <div class="flex-shrink-0 ms-sm-3">
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary">Enable Two-facor Authentication</a>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0 mt-2">
                                                    <div class="flex-grow-1">
                                                        <h6 class="fs-14 mb-1">Secondary Verification</h6>
                                                        <p class="text-muted">The first factor is a password and the second commonly includes a text with a code sent to your smartphone, or biometrics using your fingerprint, face, or retina.</p>
                                                    </div>
                                                    <div class="flex-shrink-0 ms-sm-3">
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary">Set up secondary method</a>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0 mt-2">
                                                    <div class="flex-grow-1">
                                                        <h6 class="fs-14 mb-1">Backup Codes</h6>
                                                        <p class="text-muted mb-sm-0">A backup code is automatically generated for you when you turn on two-factor authentication through your iOS or Android Twitter app. You can also generate a backup code on twitter.com.</p>
                                                    </div>
                                                    <div class="flex-shrink-0 ms-sm-3">
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary">Generate backup codes</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <h5 class="card-title text-decoration-underline mb-3">Application Notifications:</h5>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <label for="directMessage" class="form-check-label fs-14">Direct messages</label>
                                                            <p class="text-muted">Messages from people you follow</p>
                                                        </div>
                                                        <div class="flex-shrink-0">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" role="switch" id="directMessage" checked />
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex mt-2">
                                                        <div class="flex-grow-1">
                                                            <label class="form-check-label fs-14" for="desktopNotification">
                                                                Show desktop notifications
                                                            </label>
                                                            <p class="text-muted">Choose the option you want as your default setting. Block a site: Next to "Not allowed to send notifications," click Add.</p>
                                                        </div>
                                                        <div class="flex-shrink-0">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" role="switch" id="desktopNotification" checked />
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex mt-2">
                                                        <div class="flex-grow-1">
                                                            <label class="form-check-label fs-14" for="emailNotification">
                                                                Show email notifications
                                                            </label>
                                                            <p class="text-muted"> Under Settings, choose Notifications. Under Select an account, choose the account to enable notifications for. </p>
                                                        </div>
                                                        <div class="flex-shrink-0">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" role="switch" id="emailNotification" />
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex mt-2">
                                                        <div class="flex-grow-1">
                                                            <label class="form-check-label fs-14" for="chatNotification">
                                                                Show chat notifications
                                                            </label>
                                                            <p class="text-muted">To prevent duplicate mobile notifications from the Gmail and Chat apps, in settings, turn off Chat notifications.</p>
                                                        </div>
                                                        <div class="flex-shrink-0">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" role="switch" id="chatNotification" />
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex mt-2">
                                                        <div class="flex-grow-1">
                                                            <label class="form-check-label fs-14" for="purchaesNotification">
                                                                Show purchase notifications
                                                            </label>
                                                            <p class="text-muted">Get real-time purchase alerts to protect yourself from fraudulent charges.</p>
                                                        </div>
                                                        <div class="flex-shrink-0">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" role="switch" id="purchaesNotification" />
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div>
                                                <h5 class="card-title text-decoration-underline mb-3">Delete This Account:</h5>
                                                <p class="text-muted">Go to the Data & Privacy section of your profile Account. Scroll to "Your data & privacy options." Delete your Profile Account. Follow the instructions to delete your account :</p>
                                                <div>
                                                    <input type="password" class="form-control" id="passwordInput" placeholder="Enter your password" value="make@321654987" style="max-width: 265px;">
                                                </div>
                                                <div class="hstack gap-2 mt-3">
                                                    <a href="javascript:void(0);" class="btn btn-soft-danger">Close & Delete This Account</a>
                                                    <a href="javascript:void(0);" class="btn btn-light">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end tab-pane-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end col-->
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

    <!-- profile-setting init js -->
    <script src="/assets/js/pages/profile-setting.init.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
</body>

</html>