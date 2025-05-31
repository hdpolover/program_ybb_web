<!-- Empty state - No abstract submitted yet -->
<h4 class="card-title mb-4">Submit Your Abstract</h4>

<div class="row">
    <div class="col-lg-8">
        <div class="mb-4">
            <div class="text-center p-4 border rounded">
                <div class="mb-4">
                    <i class="mdi mdi-file-document-outline text-primary" style="font-size: 3.5rem;"></i>
                </div>
                <h5 class="font-size-16 mb-3">You haven't submitted an abstract yet</h5>                <p class="text-muted mb-4">
                    Start your contribution by submitting your work. This is an essential step in the academic submission process. You'll need to provide relevant details including topic, title, keywords, and content summary. You can also collaborate with co-authors to enhance your submission before finalizing.
                </p>
                <button id="create-abstract-btn" class="btn btn-primary waves-effect waves-light">
                    <i class="bx bx-plus me-1"></i> Create New Abstract
                </button>
            </div>
        </div>
        <div class="mb-4">
            <h5 class="font-size-15">Submission Process Flow</h5>
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
                        <h5 class="font-size-14">Abstract Submission</h5>
                        <p class="text-muted mb-0">Submit your abstract with a topic, title, keywords, and detailed content. You can save as draft and refine before final submission.</p>
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
                        <h5 class="font-size-14">Manage Co-Authors</h5>
                        <p class="text-muted mb-0">Add, edit, or remove co-authors for your abstract. Collaborate with them to improve your submission before finalizing.</p>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-xs">
                            <div class="avatar-title rounded-circle bg-light text-primary">
                                3
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="font-size-14">Abstract Acceptance</h5>
                        <p class="text-muted mb-0">Upon approval, your abstract will be marked as accepted and you can proceed to the next step in the submission process.</p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-xs">
                            <div class="avatar-title rounded-circle bg-light text-primary">
                                4
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="font-size-14">Full Paper Submission</h5>
                        <p class="text-muted mb-0">Once your abstract is accepted, you'll be able to upload your complete research paper in PDF format following the provided template.</p>
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
                        Have questions about the submission or review process? Our support team is ready to assist you with any inquiries or technical issues.
                    </p>
                    <button type="button" class="btn btn-soft-primary btn-sm">
                        <i class="mdi mdi-email-outline me-1"></i> Contact Support
                    </button>
                </div>

                <div class="mt-4">
                    <h6 class="font-size-14 mb-2">Helpful Resources</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="py-1">
                            <a href="javascript:void(0)" class="text-muted">
                                <i class="mdi mdi-file-pdf-outline text-danger me-1"></i> Abstract & Paper Templates
                            </a>
                        </li>
                        <li class="py-1">
                            <a href="javascript:void(0)" class="text-muted">
                                <i class="mdi mdi-file-document-outline text-primary me-1"></i> Detailed Submission Guidelines
                            </a>
                        </li>
                        <li class="py-1">
                            <a href="javascript:void(0)" class="text-muted">
                                <i class="mdi mdi-comment-question-outline text-info me-1"></i> Frequently Asked Questions
                            </a>
                        </li>
                        <li class="py-1">
                            <a href="javascript:void(0)" class="text-muted">
                                <i class="mdi mdi-lightbulb-outline text-warning me-1"></i> Tips for Successful Submissions
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('create-abstract-btn').addEventListener('click', function() {
            Swal.fire({
                title: 'Start Your Abstract Submission',
                html: '<div class="text-start">' +
                    '<p><strong>Ready to share your research?</strong></p>' +
                    '<p>In the next step, you will need to provide:</p>' +
                    '<ul class="mb-3 ps-3">' +
                    '<li>Topic selection for your research</li>' +
                    '<li>Title of your abstract</li>' +
                    '<li>Keywords related to your research</li>' +
                    '<li>Abstract content with your research details</li>' +
                    '</ul>' +
                    '<p class="mb-3"><i class="mdi mdi-information-outline me-1"></i> You\'ll be automatically assigned as the primary author. You can add co-authors later, even while your abstract is in draft status.</p>' +
                    '<p><i class="mdi mdi-account-group-outline me-1"></i> Collaborate with your co-authors to refine your submission before finalizing.</p>' +
                    '<p class="mb-0 mt-3 text-muted"><small>Please review the submission guidelines for detailed requirements.</small></p>' +
                    '</div>',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Begin Submission',
                cancelButtonText: 'Not Now',
                confirmButtonColor: '#5156be',
                cancelButtonColor: '#74788d',
                allowOutsideClick: false,
                customClass: {
                    htmlContainer: 'swal2-html-container text-start'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('abstract-paper/create') ?>';
                }
            });
        });
    });
</script>