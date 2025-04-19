<?php
/**
 * Program FAQs section for program detail page
 */
?>

<!-- Program FAQs Section -->
<div class="card mb-4 border-0 shadow-sm" id="faqs">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-4">
            <div class="flex-shrink-0">
                <div class="avatar-sm">
                    <div class="avatar-title bg-info-subtle text-info rounded-circle fs-18">
                        <i class="ri-question-answer-line"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <h3 class="card-title mb-0">Frequently Asked Questions (FAQs)</h3>
            </div>
        </div>

        <?php if (empty($faqs)): ?>
            <div class="text-center py-4">
                <div class="avatar-md mx-auto mb-3">
                    <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                        <i class="ri-question-mark"></i>
                    </div>
                </div>
                <h5 class="mb-1">No FAQs Available</h5>
                <p class="text-muted mb-0">FAQs for this program will be added soon.</p>
            </div>
        <?php else: ?>
            <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box" id="program-faqs">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq-heading-<?= $index ?>">
                            <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#faq-collapse-<?= $index ?>" 
                                    aria-expanded="<?= $index == 0 ? 'true' : 'false' ?>" aria-controls="faq-collapse-<?= $index ?>">
                                <?= esc($faq['question']) ?>
                            </button>
                        </h2>
                        <div id="faq-collapse-<?= $index ?>" class="accordion-collapse collapse <?= $index == 0 ? 'show' : '' ?>" 
                             aria-labelledby="faq-heading-<?= $index ?>" data-bs-parent="#program-faqs">
                            <div class="accordion-body">
                                <?= $faq['answer'] ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- End Program FAQs Section -->
