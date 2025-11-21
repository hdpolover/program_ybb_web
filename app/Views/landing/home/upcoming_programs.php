<!-- Start current programs section -->
<section class="section" id="current-programs">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="mb-3 fw-semibold"><?= ($category['name'] ?? 'Our') . ' Programs' ?></h2>
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
        // Urutkan dari yang terbaru (end_date desc)
        usort($upcoming_programs, function ($a, $b) {
            return strtotime($b['end_date']) <=> strtotime($a['end_date']);
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
        <div class="text-center p-5 rounded-4 bg-light border shadow-sm">
            <div class="avatar-lg mx-auto mb-4">
                <div class="avatar-title bg-soft-info text-info display-4 rounded-circle shadow-sm pulse-animation">
                    <i class="ri-calendar-event-line"></i>
                </div>
            </div>
            <h4 class="fw-semibold text-primary mb-3">No Upcoming Programs</h4>
            <p class="text-muted mb-4">We're preparing exciting new initiatives for you. Check back soon!</p>
            <div class="d-inline-block bg-soft-warning px-3 py-1 rounded-pill">
                <i class="ri-notification-3-line me-1"></i>
                <span class="small">Stay tuned for updates</span>
            </div>
        </div>

        <style>
        .pulse-animation {
            animation: pulse-effect 2s infinite;
        }

        @keyframes pulse-effect {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.08);
            }

            100% {
                transform: scale(1);
            }
        }
        </style>
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