<!-- Start current programs section -->
<section class="section" id="current-programs">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="mb-3 fw-semibold"><?= $category['name'] . ' Programs ' ?? 'Programs' ?></h2>
                    <p class="text-muted">Explore our active programs and initiatives</p>
                </div>
            </div>
        </div>

        <!-- Programs horizontal list -->
        <?php
        // Filter for upcoming programs only - programs where end date is in the future
        $upcoming_programs = array_filter($programs ?? [], function ($program) {
            $current_time = time();
            return isset($program['end_date']) && strtotime($program['end_date']) >= $current_time;
        });

        if (!empty($upcoming_programs)):
        ?>
            <div class="row">
                <?php foreach ($upcoming_programs as $index => $program): ?>
                    <div class="col-12 mb-4">
                        <?= view('landing/common/program_card_widget', ['program' => $program]) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center p-4 rounded-3 bg-light border">
                <div class="avatar-lg mx-auto mb-3">
                    <div class="avatar-title bg-soft-primary text-primary display-5 rounded-circle">
                        <i class="ri-calendar-event-line"></i>
                    </div>
                </div>
                <h5>No Upcoming Programs</h5>
                <p class="text-muted">Check back soon for upcoming programs and initiatives.</p>
            </div>
        <?php endif; ?>

        <!-- Button for Previous Programs -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="<?= site_url('programs') ?>" class="btn btn-outline-primary">
                    <i class="ri-history-line me-1"></i> View Other and Previous Programs
                </a>
            </div>
        </div>
    </div>
</section>
<!-- End current programs section -->