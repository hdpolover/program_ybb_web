<!-- Not eligible state - Registration required -->
<h4 class="card-title mb-4">Abstract Submission</h4>

<div class="row">
    <div class="col-lg-8">
        <div class="mb-4">
            <div class="text-center p-4 border rounded">
                <div class="mb-4">
                    <i class="mdi mdi-alert-circle-outline text-warning" style="font-size: 3.5rem;"></i>
                </div>
                <h5 class="font-size-16 mb-3">Registration Required</h5>
                <p class="text-muted mb-4">
                    You are not eligible to submit an abstract yet. To be eligible, you need to complete your registration
                    and pay the registration fee.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="<?= base_url('registration') ?>" class="btn btn-primary waves-effect waves-light">
                        <i class="mdi mdi-clipboard-text-outline me-1"></i> Complete Registration
                    </a>
                    <a href="<?= base_url('payment') ?>" class="btn btn-info waves-effect waves-light">
                        <i class="mdi mdi-credit-card-outline me-1"></i> Make Payment
                    </a>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h5 class="font-size-15">Registration Process</h5>
            <div class="mt-3">
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-xs">
                            <div class="avatar-title rounded-circle bg-light text-primary">
                                1
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="font-size-14">Complete Registration Form</h5>
                        <p class="text-muted mb-0">Fill in all required information in the registration form</p>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-xs">
                            <div class="avatar-title rounded-circle bg-light text-primary">
                                2
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="font-size-14">Pay Registration Fee</h5>
                        <p class="text-muted mb-0">Complete payment to confirm your registration</p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-xs">
                            <div class="avatar-title rounded-circle bg-light text-primary">
                                3
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="font-size-14">Submit Abstract</h5>
                        <p class="text-muted mb-0">Once registration is confirmed, you can submit your abstract</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <?= $this->include('participant/abstract-paper/components/important-dates') ?>

        <div class="card border mt-4">
            <div class="card-body">
                <h5 class="font-size-16 mb-4">Need Help?</h5>

                <div class="mb-4">
                    <p class="text-muted">
                        If you have questions about the registration or payment process, please contact our support team.
                    </p>
                    <button type="button" class="btn btn-soft-primary btn-sm">
                        <i class="mdi mdi-email-outline me-1"></i> Contact Support
                    </button>
                </div>

                <div class="mt-4">
                    <h6 class="font-size-14 mb-2">Useful Resources</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="py-1">
                            <a href="javascript:void(0)" class="text-muted">
                                <i class="mdi mdi-information-outline text-info me-1"></i> Registration Guide
                            </a>
                        </li>
                        <li class="py-1">
                            <a href="javascript:void(0)" class="text-muted">
                                <i class="mdi mdi-credit-card-outline text-success me-1"></i> Payment Methods
                            </a>
                        </li>
                        <li class="py-1">
                            <a href="javascript:void(0)" class="text-muted">
                                <i class="mdi mdi-frequently-asked-questions text-warning me-1"></i> FAQ
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
