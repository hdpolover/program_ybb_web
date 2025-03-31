<?php

function formatDateRange($start_date, $end_date)
{
    if (date('Y-m', strtotime($start_date)) == date('Y-m', strtotime($end_date))) {
        return date('F j', strtotime($start_date)) . '-' . date('j, Y', strtotime($end_date));
    } else {
        return date('F j', strtotime($start_date)) . ' - ' . date('F j, Y', strtotime($end_date));
    }
}
?>

<!-- start timeline section -->
<div class="timeline-wrapper">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-4">
                    <div class="hori-timeline">
                        <div class="swiper timelineSlider">
                            <div class="d-flex align-items-start gap-3 mb-4">
                                <div>
                                    <h4 class="fw-semibold">Program Timeline</h4>
                                    <p class="text-muted mb-0">Key events and milestones</p>
                                </div>
                                <div class="ms-auto">
                                    <div class="swiper-button-prev rounded-circle timeline-nav-btn" id="timeline-prev-btn"></div>
                                    <div class="swiper-button-next rounded-circle timeline-nav-btn" id="timeline-next-btn"></div>
                                </div>
                            </div>
                            <div class="swiper-wrapper">
                                <?php 
                                if (isset($program_schedules) && !empty($program_schedules)) :
                                    foreach ($program_schedules as $schedule) :
                                        $start_date = date('M d', strtotime($schedule['start_date']));
                                        $end_date = date('M d, Y', strtotime($schedule['end_date']));
                                        $date_display = ($start_date != $end_date) ? "$start_date - $end_date" : $end_date;
                                ?>
                                <div class="swiper-slide">
                                    <div class="card timeline-card border-0 shadow-sm">
                                        <div class="card-body p-4">
                                            <div class="acitivity-item mb-3">
                                                <div class="acitivity-avatar">
                                                    <div class="avatar-sm">
                                                        <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                            <i class="ri-calendar-event-fill"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="acitivity-content">
                                                    <h6 class="mb-1 fw-semibold"><?= $schedule['name'] ?></h6>
                                                    <p class="text-muted mb-3"><?= $date_display ?></p>
                                                    <p class="text-muted mb-0"><?= isset($schedule['description']) ? substr($schedule['description'], 0, 150) : '' ?><?= isset($schedule['description']) && strlen($schedule['description']) > 150 ? '...' : '' ?></p>
                                                    <?php if (isset($schedule['location']) && !empty($schedule['location'])) : ?>
                                                    <div class="mt-3">
                                                        <span class="badge badge-soft-info">
                                                            <i class="ri-map-pin-line me-1"></i> <?= $schedule['location'] ?>
                                                        </span>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    endforeach;
                                else :
                                ?>
                                <div class="swiper-slide">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body p-4">
                                            <div class="text-center">
                                                <div class="avatar-md mx-auto mb-4">
                                                    <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                                        <i class="ri-calendar-line"></i>
                                                    </div>
                                                </div>
                                                <h5>No schedule available</h5>
                                                <p class="text-muted">Schedule information will be updated soon.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-lg-12">
            <div class="mt-4">
                <div class="swiper vertical-timeline" id="vertical-timeline">
                    <div class="swiper-wrapper">
                        <?php 
                        if (isset($program_schedules) && !empty($program_schedules)) :
                            foreach ($program_schedules as $key => $schedule) :
                                $is_past = strtotime($schedule['end_date']) < time();
                                $is_current = strtotime($schedule['start_date']) <= time() && strtotime($schedule['end_date']) >= time();
                                $status_class = $is_past ? 'bg-success' : ($is_current ? 'bg-warning' : 'bg-danger');
                                $status_text = $is_past ? 'Completed' : ($is_current ? 'In Progress' : 'Upcoming');
                        ?>
                        <div class="swiper-slide">
                            <div class="timeline-item">
                                <div class="timeline-year"><?= date('Y', strtotime($schedule['start_date'])) ?></div>
                                <div class="timeline-content">
                                    <div class="timeline-date"><?= date('M d', strtotime($schedule['start_date'])) ?> - <?= date('M d', strtotime($schedule['end_date'])) ?></div>
                                    <div class="timeline-info">
                                        <h6 class="mb-1 fw-semibold"><?= $schedule['name'] ?></h6>
                                        <p class="text-muted mb-0"><?= isset($schedule['description']) ? substr($schedule['description'], 0, 100) : '' ?><?= isset($schedule['description']) && strlen($schedule['description']) > 100 ? '...' : '' ?></p>
                                        <div class="mt-2">
                                            <span class="badge <?= $status_class ?> text-white"><?= $status_text ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end timeline section -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize the Swiper Timeline Slider
    var timelineSlider = new Swiper(".timelineSlider", {
        slidesPerView: 1,
        spaceBetween: 25,
        loop: false,
        navigation: {
            nextEl: "#timeline-next-btn",
            prevEl: "#timeline-prev-btn",
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });

    // Initialize the Vertical Timeline
    var verticalTimeline = new Swiper("#vertical-timeline", {
        direction: "vertical",
        slidesPerView: "auto",
        spaceBetween: 25,
        mousewheel: true,
    });
});
</script>

<style>
/* Timeline Styles */
.timeline-nav-btn {
    width: 36px !important;
    height: 36px !important;
    font-size: 18px !important;
    background-color: var(--vz-primary) !important;
    color: #fff !important;
    opacity: 0.65;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.timeline-nav-btn:hover {
    opacity: 1;
}

.timeline-nav-btn::after {
    font-size: 14px !important;
}

/* Timeline Card */
.timeline-card {
    transition: all 0.3s ease;
}

.timeline-card:hover {
    transform: translateY(-5px);
}

.acitivity-item {
    position: relative;
}

.acitivity-avatar {
    position: absolute;
    top: 0;
    left: 0;
}

.acitivity-content {
    padding-left: 3rem;
}

/* Vertical Timeline */
.timeline-item {
    position: relative;
    padding: 1.5rem;
    border-left: 2px dashed var(--vz-primary);
    margin-left: 3rem;
}

.timeline-year {
    position: absolute;
    left: -3rem;
    top: 1.5rem;
    width: 2rem;
    height: 2rem;
    background-color: var(--vz-primary);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.75rem;
}

.timeline-date {
    font-size: 0.875rem;
    color: var(--vz-primary);
    font-weight: 500;
    margin-bottom: 0.75rem;
}

.vertical-timeline {
    height: 400px;
    padding-right: 1rem;
}
</style>