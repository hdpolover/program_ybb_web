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
        </div> <?php if (empty($faqs)): ?>
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
            <?php
                    // Group FAQs by category
                    $faqsByCategory = [];
                    foreach ($faqs as $faq) {
                        if (!isset($faqsByCategory[$faq['faq_category']])) {
                            $faqsByCategory[$faq['faq_category']] = [];
                        }
                        $faqsByCategory[$faq['faq_category']][] = $faq;
                    }

                    // Sort each category by order_number, but only if order_number is not null or empty
                    foreach ($faqsByCategory as $category => $categoryFaqs) {
                        usort($categoryFaqs, function ($a, $b) {
                            // If either order_number is null/empty, maintain original order
                            if (empty($a['order_number']) || empty($b['order_number'])) {
                                return 0;
                            }
                            return $a['order_number'] - $b['order_number'];
                        });
                    }

                    // Format category names for display
                    function formatCategoryName($category)
                    {
                        return ucwords(str_replace('_', ' ', $category));
                    }

                    // Global counter for unique IDs
                    $globalIndex = 0;
            ?>
            <?php foreach ($faqsByCategory as $category => $categoryFaqs): ?>
                <?php
                        // Reset first item flag for each category
                        $firstItemInAccordion = true;
                        // Get the count of FAQs in this category
                        $faqCount = count($categoryFaqs);
                ?>
                <div class="mb-4">
                    <div class="category-header py-2 px-3 mb-3 bg-light rounded-3 border-start border-info border-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="ri-bookmark-line text-info me-2"></i>
                                <h5 class="faq-category-title mb-0 fs-5"><?= formatCategoryName($category) ?></h5>
                            </div>
                            <span class="badge bg-info-subtle text-info rounded-pill"><?= $faqCount ?> <?= $faqCount > 1 ? 'items' : 'item' ?></span>
                        </div>
                    </div>

                    <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box" id="program-faqs-<?= $category ?>">
                        <?php foreach ($categoryFaqs as $index => $faq): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq-heading-<?= $globalIndex ?>">
                                    <button class="accordion-button <?= ($firstItemInAccordion ? '' : 'collapsed') ?>" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq-collapse-<?= $globalIndex ?>"
                                        aria-expanded="<?= ($firstItemInAccordion ? 'true' : 'false') ?>" aria-controls="faq-collapse-<?= $globalIndex ?>">
                                        <?= esc($faq['question']) ?>
                                    </button>
                                </h2>
                                <div id="faq-collapse-<?= $globalIndex ?>" class="accordion-collapse collapse <?= ($firstItemInAccordion ? 'show' : '') ?>"
                                    aria-labelledby="faq-heading-<?= $globalIndex ?>" data-bs-parent="#program-faqs-<?= $category ?>">
                                    <div class="accordion-body">
                                        <?= $faq['answer'] ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $globalIndex++;
                            $firstItemInAccordion = false;
                            ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<!-- End Program FAQs Section -->