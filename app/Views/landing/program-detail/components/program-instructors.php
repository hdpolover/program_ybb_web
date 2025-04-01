<?php
/**
 * Program Instructors Component
 * 
 * Displays program instructors or facilitators
 * 
 * @param array $instructors Array of instructor data
 */
?>

<div class="program-instructors mb-4">
    <div class="d-flex align-items-center mb-4">
        <div class="flex-shrink-0">
            <div class="avatar-sm">
                <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-18">
                    <i class="ri-user-star-line"></i>
                </div>
            </div>
        </div>
        <div class="flex-grow-1 ms-3">
            <h4 class="mb-0">Program Instructors</h4>
        </div>
    </div>

    <div class="row g-4">
        <?php if (!empty($instructors)): ?>
            <?php foreach ($instructors as $instructor): ?>
                <div class="col-lg-6">
                    <div class="card team-box border">
                        <div class="card-body px-3">
                            <div class="row align-items-center">
                                <div class="col-lg-12">
                                    <div class="team-profile-img mb-3">
                                        <div class="row">
                                            <div class="col-3">
                                                <?php if (!empty($instructor['photo'])): ?>
                                                    <img src="<?= $instructor['photo'] ?>" alt="<?= $instructor['name'] ?>" class="rounded avatar-xl img-thumbnail">
                                                <?php else: ?>
                                                    <div class="avatar-xl">
                                                        <div class="avatar-title bg-soft-primary text-primary rounded fs-24">
                                                            <?= substr($instructor['name'] ?? 'Instructor', 0, 1) ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-9">
                                                <h5 class="mt-2 mb-1"><?= $instructor['name'] ?? 'Instructor Name' ?></h5>
                                                <p class="text-muted mb-0"><?= $instructor['title'] ?? 'Program Instructor' ?></p>
                                                
                                                <?php if(!empty($instructor['specialization'])): ?>
                                                <p class="text-muted mt-2 mb-0"><small><i class="ri-award-fill me-1 text-primary"></i><?= $instructor['specialization'] ?></small></p>
                                                <?php endif; ?>
                                                
                                                <?php if(!empty($instructor['experience'])): ?>
                                                <p class="text-muted mb-0"><small><i class="ri-time-fill me-1 text-primary"></i><?= $instructor['experience'] ?> experience</small></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Short bio -->
                                <?php if (!empty($instructor['bio'])): ?>
                                <div class="col-lg-12">
                                    <div class="mt-3">
                                        <p class="text-muted"><?= nl2br($instructor['bio']) ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Social links -->
                                <?php if (!empty($instructor['social_links'])): ?>
                                <div class="col-lg-12 mt-3">
                                    <div class="d-flex gap-2">
                                        <?php if (!empty($instructor['social_links']['linkedin'])): ?>
                                        <a href="<?= $instructor['social_links']['linkedin'] ?>" class="btn btn-soft-primary btn-sm" target="_blank"><i class="ri-linkedin-box-fill"></i></a>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($instructor['social_links']['twitter'])): ?>
                                        <a href="<?= $instructor['social_links']['twitter'] ?>" class="btn btn-soft-info btn-sm" target="_blank"><i class="ri-twitter-fill"></i></a>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($instructor['social_links']['website'])): ?>
                                        <a href="<?= $instructor['social_links']['website'] ?>" class="btn btn-soft-danger btn-sm" target="_blank"><i class="ri-global-line"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card border">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                <i class="ri-user-search-line"></i>
                            </div>
                        </div>
                        <h5 class="mb-1">Instructor Information Coming Soon</h5>
                        <p class="text-muted">Details about our program instructors will be updated shortly.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>