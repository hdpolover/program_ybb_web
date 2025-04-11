<!-- Program Sidebar -->
<!-- Application CTA Card -->
<?php 
// Use the application CTA component with the card style
echo $this->include('landing/program-detail/components/application-cta', [
    'program' => $program, 
    'style' => 'card',
    'buttonText' => 'Register Now'
]);
?>

<!-- Program Details Card -->
<div class="card mb-4">
    <div class="card-body">
        <h3 class="card-title mb-3">Program Details</h3>
        <?php 
        // Program info with badge display style
        $display_style = 'badge';
        echo $this->include('landing/program-detail/components/program-info', [
            'program' => $program,
            'display_style' => $display_style
        ]); 
        ?>
        
        <ul class="list-group list-group-flush">
            <?php if (isset($program['duration']) && !empty($program['duration'])) : ?>
                <li class="list-group-item px-0 d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-time-line text-primary me-2 fs-16"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fs-15 mb-1">Duration</h5>
                        <p class="text-muted mb-0"><?= $program['duration'] ?></p>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (isset($program['location']) && !empty($program['location'])) : ?>
                <li class="list-group-item px-0 d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-map-pin-line text-primary me-2 fs-16"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fs-15 mb-1">Location</h5>
                        <p class="text-muted mb-0"><?= $program['location'] ?></p>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (isset($program['price']) || isset($program['fee'])) : ?>
                <li class="list-group-item px-0 d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-money-dollar-circle-line text-primary me-2 fs-16"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fs-15 mb-1">Fee</h5>
                        <p class="text-muted mb-0"><?= $program['price'] ?? $program['fee'] ?? 'Contact for pricing' ?></p>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (isset($program['capacity']) && !empty($program['capacity'])) : ?>
                <li class="list-group-item px-0 d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-group-line text-primary me-2 fs-16"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fs-15 mb-1">Capacity</h5>
                        <p class="text-muted mb-0"><?= $program['capacity'] ?> participants</p>
                    </div>
                </li>
            <?php endif; ?>
        </ul>

        <div class="mt-4">
            <a href="javascript:void(0);" class="btn btn-soft-info w-100 mb-2">Download Brochure</a>
            <a href="<?= base_url('contact') ?>" class="btn btn-soft-secondary w-100">Contact Program Coordinator</a>
        </div>
    </div>
</div>

<!-- Share Card -->
<div class="card mb-4">
    <div class="card-body">
        <h3 class="card-title mb-3">Share This Program</h3>
        <div class="d-flex gap-2">
            <a href="javascript:void(0);" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(window.location.href), '_blank')" class="btn btn-soft-primary btn-icon" title="Share on Facebook">
                <i class="ri-facebook-fill fs-16"></i>
            </a>
            <a href="javascript:void(0);" onclick="window.open('https://twitter.com/intent/tweet?text=<?= urlencode($program['name'] ?? 'Check out this program') ?>&url='+encodeURIComponent(window.location.href), '_blank')" class="btn btn-soft-info btn-icon" title="Share on Twitter">
                <i class="ri-twitter-fill fs-16"></i>
            </a>
            <a href="mailto:?subject=<?= urlencode($program['name'] ?? 'Check out this program') ?>&body=<?= urlencode('I thought you might be interested in this program: ' . (isset($program['name']) ? $program['name'] . ' - ' : '') . (current_url())) ?>" class="btn btn-soft-danger btn-icon" title="Share via Email">
                <i class="ri-mail-line fs-16"></i>
            </a>
            <a href="javascript:void(0);" onclick="window.open('https://api.whatsapp.com/send?text=<?= urlencode($program['name'] ?? 'Check out this program') ?>: '+encodeURIComponent(window.location.href), '_blank')" class="btn btn-soft-success btn-icon" title="Share on WhatsApp">
                <i class="ri-whatsapp-line fs-16"></i>
            </a>
            <a href="javascript:void(0);" onclick="window.open('https://www.linkedin.com/sharing/share-offsite/?url='+encodeURIComponent(window.location.href), '_blank')" class="btn btn-soft-secondary btn-icon" title="Share on LinkedIn">
                <i class="ri-linkedin-fill fs-16"></i>
            </a>
        </div>
    </div>
</div>

<!-- Related Programs Card -->
<?php if (isset($relatedPrograms) && !empty($relatedPrograms)): ?>
<div class="card">
    <div class="card-body">
        <h3 class="card-title mb-3">You May Also Like</h3>
        <div class="related-programs">
            <?php foreach ($relatedPrograms as $index => $relatedProgram): 
                // Define a set of colors for the icons
                $bgColors = ['bg-primary', 'bg-danger', 'bg-success', 'bg-info'];
                $bgColor = $bgColors[$index % count($bgColors)];
                
                // Define a set of icons
                $icons = ['ri-award-line', 'ri-book-open-line', 'ri-code-s-slash-line', 'ri-lightbulb-line'];
                $icon = $icons[$index % count($icons)];
            ?>
            <div class="d-flex mb-3 p-2 bg-light rounded">
                <div class="flex-shrink-0">
                    <div class="avatar-sm">
                        <div class="avatar-title <?= $bgColor ?> rounded">
                            <i class="<?= $icon ?> text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="fs-15 mb-1">
                        <a href="<?= base_url('programs/' . ($relatedProgram['slug'] ?? $relatedProgram['id']) . '/details') ?>" class="text-dark">
                            <?= $relatedProgram['name'] ?? 'Related Program' ?>
                        </a>
                    </h5>
                    <p class="text-muted mb-0"><?= $relatedProgram['tagline'] ?? substr($relatedProgram['description'] ?? '', 0, 50) . (strlen($relatedProgram['description'] ?? '') > 50 ? '...' : '') ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Fallback related programs if none provided -->
<div class="card">
    <div class="card-body">
        <h3 class="card-title mb-3">You May Also Like</h3>
        <div class="related-programs">
            <div class="d-flex mb-3 p-2 bg-light rounded">
                <div class="flex-shrink-0">
                    <div class="avatar-sm">
                        <div class="avatar-title bg-primary rounded">
                            <i class="ri-award-line text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="fs-15 mb-1">Leadership Development</h5>
                    <p class="text-muted mb-0">Enhance your leadership skills</p>
                </div>
            </div>
            <div class="d-flex mb-3 p-2 bg-light rounded">
                <div class="flex-shrink-0">
                    <div class="avatar-sm">
                        <div class="avatar-title bg-danger rounded">
                            <i class="ri-book-open-line text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="fs-15 mb-1">Digital Marketing</h5>
                    <p class="text-muted mb-0">Learn modern marketing techniques</p>
                </div>
            </div>
            <div class="d-flex p-2 bg-light rounded">
                <div class="flex-shrink-0">
                    <div class="avatar-sm">
                        <div class="avatar-title bg-success rounded">
                            <i class="ri-code-s-slash-line text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="fs-15 mb-1">Web Development</h5>
                    <p class="text-muted mb-0">Build responsive websites</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<!-- End Program Sidebar -->