<?php
/**
 * Program Speakers Section
 * Displays program speakers with modern card design and modal dialog for details
 */
?>

<!-- Program Speakers Section -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-4">
            <div class="flex-shrink-0 me-3">
                <div class="avatar-sm bg-primary bg-gradient rounded">
                    <span class="avatar-title">
                        <i class="ri-mic-line fs-20"></i>
                    </span>
                </div>
            </div>
            <div class="flex-grow-1">
                <h5 class="card-title mb-1">Featured Speakers</h5>
                <p class="text-muted mb-0">Meet our distinguished speakers and industry experts</p>
            </div>
        </div>

        <div class="row g-4">
            <?php 
            // Separate keynote and regular speakers
            $keynoteSpeakers = array_filter($speakers, function($speaker) {
                return $speaker['is_keynote'] == '1';
            });
            $regularSpeakers = array_filter($speakers, function($speaker) {
                return $speaker['is_keynote'] != '1';
            });
            
            // Combine arrays with keynote speakers first
            $allSpeakers = array_merge($keynoteSpeakers, $regularSpeakers);
            
            foreach ($allSpeakers as $index => $speaker): 
                $isKeynote = $speaker['is_keynote'] == '1';
                $cardClass = $isKeynote ? 'speaker-card keynote-speaker' : 'speaker-card';
                $colClass = $isKeynote ? 'col-lg-6 col-md-6' : 'col-lg-4 col-md-6';
            ?>
                <div class="<?= $colClass ?>" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                    <div class="<?= $cardClass ?>" data-speaker-id="<?= $speaker['id'] ?>">
                        <div class="speaker-card-inner">
                            <!-- Front Side -->
                            <div class="speaker-card-front">
                                <div class="position-relative h-100">
                                    <?php if ($isKeynote): ?>
                                        <div class="keynote-badge">
                                            <span class="badge bg-warning text-dark">
                                                <i class="ri-star-fill me-1"></i>Keynote Speaker
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="speaker-image-container">
                                        <?php if (!empty($speaker['photo_url'])): ?>
                                            <img src="<?= $speaker['photo_url'] ?>" 
                                                 alt="<?= htmlspecialchars($speaker['name']) ?>"
                                                 class="speaker-image"
                                                 onerror="console.error('Speaker image failed to load:', this.src); this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <!-- Fallback avatar -->
                                            <div class="speaker-avatar-fallback" style="display: none;">
                                                <div class="avatar-lg bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center">
                                                    <span class="text-white fs-1 fw-bold">
                                                        <?= strtoupper(substr($speaker['name'], 0, 1)) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- Default avatar with initials -->
                                            <div class="speaker-avatar-fallback">
                                                <div class="avatar-lg bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center">
                                                    <span class="text-white fs-1 fw-bold">
                                                        <?= strtoupper(substr($speaker['name'], 0, 1)) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="speaker-info p-3">
                                        <h6 class="speaker-name mb-1 text-dark"><?= htmlspecialchars($speaker['name']) ?></h6>
                                        <p class="speaker-title text-primary mb-2 fw-medium"><?= htmlspecialchars($speaker['title']) ?></p>
                                        <p class="speaker-organization text-muted small mb-0">
                                            <i class="ri-building-line me-1"></i><?= htmlspecialchars($speaker['organization']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Back Side -->
                            <div class="speaker-card-back">
                                <div class="p-4 h-100 d-flex flex-column">
                                    <div class="text-center mb-3">
                                        <h6 class="text-white mb-1"><?= htmlspecialchars($speaker['name']) ?></h6>
                                        <p class="text-white-50 small mb-0"><?= htmlspecialchars($speaker['title']) ?></p>
                                        <hr class="border-white-50 my-2">
                                    </div>
                                    
                                    <?php if (!empty($speaker['bio'])): ?>
                                        <div class="flex-grow-1 mb-3">
                                            <h6 class="text-warning small mb-2">About</h6>
                                            <p class="text-white small mb-0" style="line-height: 1.4;">
                                                <?= strlen($speaker['bio']) > 100 ? substr(htmlspecialchars($speaker['bio']), 0, 100) . '...' : htmlspecialchars($speaker['bio']) ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($speaker['expertise_areas'])): ?>
                                        <div class="mb-3">
                                            <h6 class="text-warning small mb-2">Expertise</h6>
                                            <div class="expertise-tags-back">
                                                <?php 
                                                $expertiseAreas = explode(',', $speaker['expertise_areas']);
                                                foreach (array_slice($expertiseAreas, 0, 3) as $area): ?>
                                                    <span class="badge bg-white bg-opacity-20 text-white me-1 mb-1"><?= trim(htmlspecialchars($area)) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($speaker['session_title'])): ?>
                                        <div class="mb-3">
                                            <h6 class="text-warning small mb-1">Session</h6>
                                            <p class="text-white small mb-1"><?= htmlspecialchars($speaker['session_title']) ?></p>
                                            <?php if (!empty($speaker['session_time'])): ?>
                                                <p class="text-white-50 small mb-0">
                                                    <i class="ri-time-line me-1"></i><?= htmlspecialchars($speaker['session_time']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-auto pt-3">
                                        <button class="btn btn-warning btn-sm w-100 fw-medium" 
                                                onclick="event.stopPropagation(); openSpeakerModal(<?= htmlspecialchars(json_encode($speaker)) ?>)">
                                            <i class="ri-eye-line me-2"></i>View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($speakers)): ?>
            <div class="text-center py-5">
                <div class="text-muted">
                    <i class="ri-mic-line fs-1 mb-3 d-block"></i>
                    <h6>No Speakers Announced Yet</h6>
                    <p>Stay tuned for exciting speaker announcements!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Speaker Details Modal -->
<div class="modal fade" id="speakerModal" tabindex="-1" aria-labelledby="speakerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="speakerModalLabel">Speaker Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Profile Image Section -->
                    <div class="col-md-4 bg-light d-flex align-items-center justify-content-center p-4">
                        <div class="text-center">
                            <div class="position-relative mb-3">
                                <img id="modalSpeakerImageLarge" src="" alt="" 
                                     class="shadow-lg" 
                                     style="width: 200px; height: 200px; object-fit: cover; border: 4px solid #fff; border-radius: 12px;">
                                <div id="modalKeynoteBadgeLarge" class="position-absolute top-0 end-0 badge bg-warning text-dark fs-6 px-3 py-2" style="display: none; transform: translate(25%, -25%);">
                                    <i class="ri-star-fill me-1"></i>Keynote
                                </div>
                            </div>
                            <h4 class="text-dark mb-1" id="modalSpeakerNameLarge"></h4>
                            <p class="text-primary fw-medium mb-2" id="modalSpeakerTitleLarge"></p>
                            <p class="text-muted" id="modalSpeakerOrganizationLarge"></p>
                        </div>
                    </div>
                    
                    <!-- Details Section -->
                    <div class="col-md-8 p-4">
                        <div class="h-100 d-flex flex-column">
                            <!-- Biography Section -->
                            <div class="mb-4" id="modalBioSectionLarge">
                                <h6 class="text-dark mb-3 d-flex align-items-center">
                                    <i class="ri-user-line me-2 text-primary"></i>Biography
                                </h6>
                                <p class="text-muted" id="modalSpeakerBioLarge" style="line-height: 1.6;"></p>
                            </div>
                            
                            <!-- Session Information -->
                            <div class="mb-4" id="modalSessionSectionLarge" style="display: none;">
                                <h6 class="text-dark mb-3 d-flex align-items-center">
                                    <i class="ri-presentation-line me-2 text-primary"></i>Session Information
                                </h6>
                                <div class="p-3 bg-light rounded">
                                    <h6 class="mb-2 text-primary" id="modalSessionTitleLarge"></h6>
                                    <p class="text-muted mb-2 small" id="modalSessionDescriptionLarge"></p>
                                    <div id="modalSessionTimeLarge"></div>
                                </div>
                            </div>
                            
                            <!-- Areas of Expertise -->
                            <div class="mb-4" id="modalExpertiseSectionLarge">
                                <h6 class="text-dark mb-3 d-flex align-items-center">
                                    <i class="ri-lightbulb-line me-2 text-primary"></i>Areas of Expertise
                                </h6>
                                <div id="modalExpertiseTagsLarge"></div>
                            </div>
                            
                            <!-- Contact & Social Links -->
                            <div class="mt-auto">
                                <h6 class="text-dark mb-3 d-flex align-items-center">
                                    <i class="ri-links-line me-2 text-primary"></i>Connect with Speaker
                                </h6>
                                <div class="d-flex flex-wrap gap-2" id="modalSocialLinksLarge">
                                    <!-- Social links will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Speaker Styles -->
<style>
.speaker-card {
    perspective: 1000px;
    height: 400px;
    cursor: pointer;
}

.speaker-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 0.8s ease;
    transform-style: preserve-3d;
}

.speaker-card:hover .speaker-card-inner {
    transform: rotateY(180deg);
}

.speaker-card-front, .speaker-card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    background: #fff;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.speaker-card-back {
    background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
    transform: rotateY(180deg);
}

.keynote-speaker .speaker-card-front {
    border: 2px solid #60a5fa;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.keynote-speaker .speaker-card-back {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.speaker-image-container {
    height: 280px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
}

.speaker-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.speaker-card:hover .speaker-image {
    transform: scale(1.05);
}

.speaker-avatar-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.keynote-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 10;
}

.speaker-info {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.speaker-name {
    font-weight: 600;
    color: #111827;
    font-size: 1.1rem;
}

.speaker-title {
    font-weight: 500;
    font-size: 0.95rem;
    color: #3b82f6;
}

.speaker-organization {
    font-size: 0.85rem;
    color: #6b7280;
}

.expertise-tags .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    background-color: #f3f4f6;
    color: #6b7280;
    border: none;
    font-weight: 500;
}

.expertise-tags-back .badge {
    font-size: 0.7rem;
    padding: 0.3rem 0.6rem;
    font-weight: 500;
    background-color: rgba(255, 255, 255, 0.9) !important;
    color: #1e40af !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Modal Styles */
.modal-content {
    border-radius: 16px;
}

.modal-header {
    border-radius: 16px 16px 0 0;
    padding: 1.5rem;
}

.modal-body {
    min-height: 500px;
}

#modalExpertiseTagsLarge .badge {
    background-color: #e0e7ff;
    color: #3730a3;
    border: none;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
    font-size: 0.85rem;
}

#modalSocialLinksLarge .btn {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .speaker-card {
        height: 380px;
    }
    
    .speaker-image-container {
        height: 250px;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .modal-body .row .col-md-4,
    .modal-body .row .col-md-8 {
        padding: 1rem;
    }
    
    #modalSpeakerImageLarge {
        width: 150px !important;
        height: 150px !important;
    }
}

/* Tablet adjustments */
@media (max-width: 992px) {
    .speaker-card {
        height: 390px;
    }
    
    .modal-dialog {
        margin: 1rem;
    }
}

/* Ensure proper modal sizing on larger screens */
@media (min-width: 1200px) {
    .modal-xl {
        max-width: 1000px;
    }
}
</style>

<!-- Speaker Modal Script -->
<script>
function openSpeakerModal(speaker) {
    const modal = new bootstrap.Modal(document.getElementById('speakerModal'));
    
    // Populate speaker name and basic info
    document.getElementById('modalSpeakerNameLarge').textContent = speaker.name;
    document.getElementById('modalSpeakerTitleLarge').textContent = speaker.title;
    document.getElementById('modalSpeakerOrganizationLarge').innerHTML = '<i class="ri-building-line me-1"></i>' + speaker.organization;
    
    // Handle large speaker image
    const modalImageLarge = document.getElementById('modalSpeakerImageLarge');
    
    if (speaker.photo_url) {
        modalImageLarge.src = speaker.photo_url;
        modalImageLarge.style.display = 'block';
        
        // Add error handler to see if image fails to load
        modalImageLarge.onerror = function() {
            console.error('Failed to load speaker image:', speaker.photo_url);
            // Show fallback
            modalImageLarge.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNGM0Y0RjYiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iIzZCNzI4MCIgZm9udC1zaXplPSI0OCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXdlaWdodD0iYm9sZCI+' + btoa(speaker.name.charAt(0).toUpperCase()) + '</text></svg>';
        };
    } else {
        modalImageLarge.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNGM0Y0RjYiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iIzZCNzI4MCIgZm9udC1zaXplPSI0OCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXdlaWdodD0iYm9sZCI+' + btoa(speaker.name.charAt(0).toUpperCase()) + '</text></svg>';
        modalImageLarge.style.display = 'block';
    }
    
    // Handle keynote badge
    const keynoteBadgeLarge = document.getElementById('modalKeynoteBadgeLarge');
    if (speaker.is_keynote == '1') {
        keynoteBadgeLarge.style.display = 'block';
    } else {
        keynoteBadgeLarge.style.display = 'none';
    }
    
    // Handle biography
    const bioSectionLarge = document.getElementById('modalBioSectionLarge');
    const bioTextLarge = document.getElementById('modalSpeakerBioLarge');
    if (speaker.bio && speaker.bio.trim() !== '') {
        bioTextLarge.textContent = speaker.bio;
        bioSectionLarge.style.display = 'block';
    } else {
        bioSectionLarge.style.display = 'none';
    }
    
    // Handle session information
    const sessionSectionLarge = document.getElementById('modalSessionSectionLarge');
    const sessionTitleLarge = document.getElementById('modalSessionTitleLarge');
    const sessionDescriptionLarge = document.getElementById('modalSessionDescriptionLarge');
    const sessionTimeLarge = document.getElementById('modalSessionTimeLarge');
    
    if (speaker.session_title && speaker.session_title.trim() !== '') {
        sessionTitleLarge.textContent = speaker.session_title;
        sessionDescriptionLarge.textContent = speaker.session_description || '';
        if (speaker.session_time) {
            sessionTimeLarge.innerHTML = '<small class="text-muted"><i class="ri-time-line me-1"></i>' + speaker.session_time + '</small>';
        } else {
            sessionTimeLarge.innerHTML = '';
        }
        sessionSectionLarge.style.display = 'block';
    } else {
        sessionSectionLarge.style.display = 'none';
    }
    
    // Handle expertise areas
    const expertiseSectionLarge = document.getElementById('modalExpertiseSectionLarge');
    const expertiseTagsLarge = document.getElementById('modalExpertiseTagsLarge');
    
    if (speaker.expertise_areas && speaker.expertise_areas.trim() !== '') {
        const areas = speaker.expertise_areas.split(',');
        expertiseTagsLarge.innerHTML = areas.map(area => 
            `<span class="badge">${area.trim()}</span>`
        ).join('');
        expertiseSectionLarge.style.display = 'block';
    } else {
        expertiseSectionLarge.style.display = 'none';
    }
    
    // Handle social links
    const socialLinksLarge = document.getElementById('modalSocialLinksLarge');
    let linksHtml = '';
    
    if (speaker.linkedin_url && speaker.linkedin_url.trim() !== '') {
        linksHtml += `<a href="${speaker.linkedin_url}" target="_blank" class="btn btn-outline-primary">
            <i class="ri-linkedin-fill me-2"></i>LinkedIn
        </a>`;
    }
    
    if (speaker.instagram_url && speaker.instagram_url.trim() !== '') {
        linksHtml += `<a href="${speaker.instagram_url}" target="_blank" class="btn btn-outline-danger">
            <i class="ri-instagram-line me-2"></i>Instagram
        </a>`;
    }
    
    if (speaker.email && speaker.email.trim() !== '') {
        linksHtml += `<a href="mailto:${speaker.email}" class="btn btn-outline-success">
            <i class="ri-mail-line me-2"></i>Email
        </a>`;
    }
    
    if (speaker.website && speaker.website.trim() !== '') {
        linksHtml += `<a href="${speaker.website}" target="_blank" class="btn btn-outline-info">
            <i class="ri-global-line me-2"></i>Website
        </a>`;
    }
    
    if (!linksHtml) {
        linksHtml = '<p class="text-muted mb-0">No contact information available</p>';
    }
    
    socialLinksLarge.innerHTML = linksHtml;
    
    modal.show();
}
</script>