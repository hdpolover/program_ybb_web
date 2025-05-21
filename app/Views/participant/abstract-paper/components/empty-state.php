<!-- Empty state - No abstract submitted yet -->
<h4 class="card-title mb-4">Submit Your Abstract</h4>

<div class="row">
    <div class="col-lg-8">
        <div class="mb-4">
            <div class="text-center p-4 border rounded">
                <div class="mb-4">
                    <i class="mdi mdi-file-document-outline text-primary" style="font-size: 3.5rem;"></i>
                </div>
                <h5 class="font-size-16 mb-3">You haven't submitted an abstract yet</h5>
                <p class="text-muted mb-4">
                    Create a new abstract to start the submission process. You can add co-authors and upload your paper after creating the abstract.
                </p>                <a href="<?= base_url('abstract-paper/create') ?>" class="btn btn-primary waves-effect waves-light">
                    <i class="mdi mdi-plus me-1"></i> Create New Abstract
                </a>
            </div>
        </div>

        <div class="mb-4">
            <h5 class="font-size-15">Submission Guidelines</h5>
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
                        <h5 class="font-size-14">Create Your Abstract</h5>
                        <p class="text-muted mb-0">Prepare your abstract with title, content, and references</p>
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
                        <h5 class="font-size-14">Add Your Co-Authors</h5>
                        <p class="text-muted mb-0">Include all contributors with their affiliations</p>
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
                        <h5 class="font-size-14">Upload Full Paper</h5>
                        <p class="text-muted mb-0">Submit your complete paper document (PDF format)</p>
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
                        If you have questions about the submission process, please contact our support team.
                    </p>
                    <button type="button" class="btn btn-soft-primary btn-sm">
                        <i class="mdi mdi-email-outline me-1"></i> Contact Support
                    </button>
                </div>

                <div class="mt-4">
                    <h6 class="font-size-14 mb-2">Submission Resources</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="py-1">
                            <a href="javascript:void(0)" class="text-muted">
                                <i class="mdi mdi-file-pdf-outline text-danger me-1"></i> Abstract Template
                            </a>
                        </li>
                        <li class="py-1">
                            <a href="javascript:void(0)" class="text-muted">
                                <i class="mdi mdi-file-document-outline text-primary me-1"></i> Submission Guidelines
                            </a>
                        </li>
                        <li class="py-1">
                            <a href="javascript:void(0)" class="text-muted">
                                <i class="mdi mdi-frequently-asked-questions text-info me-1"></i> FAQ
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
