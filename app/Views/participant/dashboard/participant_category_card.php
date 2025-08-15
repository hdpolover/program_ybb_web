<?php
/**
 * Participant Category Information Card
 * 
 * Displays the participant's current category (self_funded or fully_funded)
 * and provides information about both categories with switching options
 */

// Get participant category from the detailed participant data
// Check multiple possible field names for category
$participantCategory = null;
if (isset($detailedParticipant)) {
    $participantCategory = $detailedParticipant['category'] ?? 
                          $detailedParticipant['registration_type'] ?? 
                          $detailedParticipant['funding_type'] ?? 
                          $detailedParticipant['participant_category'] ??
                          null;
}

// If still not found, check current participant data
if (!$participantCategory && isset($currentParticipant)) {
    $participantCategory = $currentParticipant['category'] ?? 
                          $currentParticipant['registration_type'] ?? 
                          $currentParticipant['funding_type'] ?? 
                          $currentParticipant['participant_category'] ??
                          null;
}

// Default to self_funded if not found, but log for debugging
if (!$participantCategory) {
    $participantCategory = 'self_funded';
    error_log('DEBUG: Participant category not found in data. Available fields in detailedParticipant: ' . 
        (isset($detailedParticipant) ? implode(', ', array_keys($detailedParticipant)) : 'not available'));
    error_log('DEBUG: Available fields in currentParticipant: ' . 
        (isset($currentParticipant) ? implode(', ', array_keys($currentParticipant)) : 'not available'));
}

$participantFullName = $currentParticipant['full_name'] ?? 'Participant';

// Determine display information based on category
$isFullyFunded = ($participantCategory === 'fully_funded');
$categoryDisplayName = $isFullyFunded ? 'Fully Funded' : 'Self Funded';
$categoryIcon = $isFullyFunded ? 'ri-shield-check-fill' : 'ri-user-fill';
$categoryColorClass = $isFullyFunded ? 'success' : 'primary'; // Changed from 'warning' to 'primary' (blue)
$categoryBgClass = $isFullyFunded ? 'bg-success-subtle' : 'bg-primary-subtle'; // Changed from 'bg-warning-subtle'

// Temporary debug output (remove this in production)
if (isset($_GET['debug'])) {
    echo '<div class="alert alert-info mt-3">';
    echo '<h6>Debug Information:</h6>';
    echo '<p><strong>Participant Category Found:</strong> ' . ($participantCategory ?: 'null') . '</p>';
    if (isset($detailedParticipant)) {
        echo '<p><strong>Detailed Participant Fields:</strong> ' . implode(', ', array_keys($detailedParticipant)) . '</p>';
        echo '<p><strong>Detailed Participant Data:</strong> <pre>' . print_r($detailedParticipant, true) . '</pre></p>';
    }
    if (isset($currentParticipant)) {
        echo '<p><strong>Current Participant Fields:</strong> ' . implode(', ', array_keys($currentParticipant)) . '</p>';
        echo '<p><strong>Current Participant Data:</strong> <pre>' . print_r($currentParticipant, true) . '</pre></p>';
    }
    echo '</div>';
}
?>

<style>
.category-card {
    background: linear-gradient(135deg, 
        rgba(var(--vz-primary-rgb), 0.05) 0%, 
        rgba(var(--vz-info-rgb), 0.03) 100%);
    border: 1px solid rgba(var(--vz-primary-rgb), 0.1);
    border-radius: 1rem;
    transition: all 0.3s ease;
}

.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px -8px rgba(var(--vz-primary-rgb), 0.15);
}

.category-badge {
    font-size: 0.85rem;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    letter-spacing: 0.5px;
}

.category-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.15);
}

.info-button {
    transition: all 0.3s ease;
}

.info-button:hover {
    transform: scale(1.05);
}

/* Custom orange/amber colors for self-funded category */
.participant-category-card .bg-warning-subtle {
    background-color: rgba(235, 144, 49, 0.1) !important;
}

.participant-category-card .text-warning {
    color: #d47e00 !important;
}

.participant-category-card .category-badge.bg-warning-subtle {
    background-color: rgba(235, 144, 49, 0.1) !important;
    color: #d47e00 !important;
    border: 1px solid rgba(235, 144, 49, 0.3) !important;
}

/* Modal enhancements */
.category-modal .modal-dialog {
    max-width: 800px;
}

.category-comparison {
    background: linear-gradient(135deg, 
        rgba(var(--vz-light-rgb), 0.8) 0%, 
        rgba(255, 255, 255, 0.9) 100%);
    border-radius: 1rem;
    padding: 2rem;
    margin: 1rem 0;
}

.category-option {
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid rgba(var(--vz-border-color-rgb), 0.3);
    border-radius: 1rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.category-option:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px -8px rgba(0, 0, 0, 0.15);
}

.category-option.active {
    border-color: var(--vz-primary);
    background: rgba(var(--vz-primary-rgb), 0.08);
}

.category-option.self-funded {
    border-left: 4px solid #d47e00; /* Changed from var(--vz-warning) to custom orange */
}

.category-option.fully-funded {
    border-left: 4px solid var(--vz-success);
}

/* Dark mode support */
[data-bs-theme="dark"] .category-card {
    background: linear-gradient(135deg, 
        rgba(var(--vz-primary-rgb), 0.08) 0%, 
        rgba(var(--vz-info-rgb), 0.05) 100%);
    border-color: rgba(var(--vz-border-color-rgb), 0.3);
}

[data-bs-theme="dark"] .category-comparison {
    background: linear-gradient(135deg, 
        rgba(var(--vz-dark-rgb), 0.8) 0%, 
        rgba(var(--vz-dark-rgb), 0.9) 100%);
}

[data-bs-theme="dark"] .category-option {
    background: rgba(var(--vz-dark-rgb), 0.6);
    border-color: rgba(var(--vz-border-color-rgb), 0.2);
}

/* Dark mode custom orange colors */
[data-bs-theme="dark"] .participant-category-card .bg-warning-subtle {
    background-color: rgba(220, 130, 30, 0.15) !important;
}

[data-bs-theme="dark"] .participant-category-card .text-warning {
    color: #e6a640 !important;
}
</style>

<div class="row mb-4 participant-category-card">
    <div class="col-12">
        <div class="card category-card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="category-icon <?= $categoryBgClass ?> text-<?= $categoryColorClass ?> me-3">
                            <i class="<?= $categoryIcon ?>"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Your Registration Category</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="category-badge <?= $categoryBgClass ?> text-<?= $categoryColorClass ?>">
                                    <?= $categoryDisplayName ?>
                                </span>
                                <?php if ($isFullyFunded): ?>
                                    <small class="text-muted">Reimbursement System</small>
                                <?php else: ?>
                                    <small class="text-muted">Standard Payment</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary info-button" data-bs-toggle="modal" data-bs-target="#categoryInfoModal">
                            <i class="ri-information-line me-1"></i> Learn More
                        </button>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#categoryInfoModal" data-action="switch">
                            <i class="ri-shuffle-line me-1"></i> Switch Category
                        </button>
                    </div>
                </div>
                <div class="mt-3 p-3 bg-light rounded">
                    <small class="text-muted">
                        <i class="ri-information-line me-1"></i>
                        <?php if ($isFullyFunded): ?>
                            You're registered as a <strong>Fully Funded</strong> participant. Complete all requirements including essays and interviews. You'll pay program fees in scheduled batches, and if selected after the evaluation process, all your payments will be reimbursed.
                        <?php else: ?>
                            You're registered as a <strong>Self Funded</strong> participant. Complete the standard registration process and make your payments according to the scheduled payment batches as they become due.
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Information Modal -->
<div class="modal fade category-modal" id="categoryInfoModal" tabindex="-1" aria-labelledby="categoryInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryInfoModalLabel">
                    <i class="ri-user-settings-line me-2"></i>Registration Categories
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="category-comparison">
                    <div class="text-center mb-4">
                        <h6 class="text-primary mb-2">Understanding Your Registration Path</h6>
                        <p class="text-muted">Each category offers different experiences and requirements. Both categories follow the same payment schedule with fees paid in batches over time, but the funding source differs based on your selection status.</p>
                    </div>

                    <div class="row g-3">
                        <!-- Self Funded Option -->
                        <div class="col-md-6">
                            <div class="category-option self-funded <?= !$isFullyFunded ? 'active' : '' ?>" data-category="self_funded">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="category-icon bg-warning-subtle text-warning me-3" style="background-color: rgba(235, 144, 49, 0.1) !important; color: #d47e00 !important;">
                                        <i class="ri-user-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0" style="color: #d47e00 !important;">Self Funded</h6>
                                        <small class="text-muted">Standard Registration</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="fs-6 mb-2">Requirements:</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="ri-check-line text-success me-1"></i> Complete registration form and documentation</li>
                                        <li><i class="ri-check-line text-success me-1"></i> Submit required documents on time</li>
                                        <li><i class="ri-calendar-check-line text-success me-1"></i> Pay fees according to scheduled payment batches</li>
                                        <li><i class="ri-speed-line text-info me-1"></i> Streamlined administrative processing</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fs-6 mb-2">Benefits:</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="ri-shield-check-line text-success me-1"></i> Guaranteed program participation</li>
                                        <li><i class="ri-timer-flash-line text-success me-1"></i> Faster application processing</li>
                                        <li><i class="ri-user-star-line text-success me-1"></i> Full access to all program features</li>
                                        <li><i class="ri-checkbox-circle-line text-success me-1"></i> No competitive selection process</li>
                                    </ul>
                                </div>

                                <div class="p-2 rounded" style="background-color: rgba(235, 144, 49, 0.1) !important;">
                                    <small style="color: #d47e00 !important;"><i class="ri-wallet-3-line me-1"></i> <strong>Payment:</strong> You pay all scheduled fee batches yourself</small>
                                </div>
                            </div>
                        </div>

                        <!-- Fully Funded Option -->
                        <div class="col-md-6">
                            <div class="category-option fully-funded <?= $isFullyFunded ? 'active' : '' ?>" data-category="fully_funded">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="category-icon bg-success-subtle text-success me-3">
                                        <i class="ri-shield-check-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-success">Fully Funded</h6>
                                        <small class="text-muted">Reimbursement System</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="fs-6 mb-2">Requirements:</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="ri-file-edit-line text-primary me-1"></i> Complete comprehensive application essays</li>
                                        <li><i class="ri-user-voice-line text-primary me-1"></i> Participate in interview sessions</li>
                                        <li><i class="ri-calendar-check-line text-primary me-1"></i> Pay fees according to scheduled payment batches</li>
                                        <li><i class="ri-hourglass-line text-warning me-1"></i> Wait for competitive selection results</li>
                                        <li><i class="ri-star-line text-info me-1"></i> Meet higher academic/professional standards</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fs-6 mb-2">Benefits (If Selected):</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="ri-refund-2-line text-success me-1"></i> <strong>Full reimbursement</strong> of all paid fees</li>
                                        <li><i class="ri-vip-crown-line text-success me-1"></i> Premium program access and privileges</li>
                                        <li><i class="ri-team-line text-success me-1"></i> Exclusive networking opportunities</li>
                                        <li><i class="ri-award-line text-success me-1"></i> Recognition and certificate of achievement</li>
                                        <li><i class="ri-graduation-cap-line text-success me-1"></i> Enhanced learning resources</li>
                                    </ul>
                                </div>

                                <div class="p-2 bg-success-subtle rounded">
                                    <small class="text-success"><i class="ri-money-dollar-circle-line me-1"></i> <strong>Payment:</strong> Pay scheduled batches initially, get <strong>full refund</strong> if selected</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="mt-4 p-3 bg-info-subtle rounded">
                        <h6 class="text-info mb-2"><i class="ri-information-line me-1"></i> Important Payment & Selection Information</h6>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2"><i class="ri-calendar-schedule-line text-info me-1"></i> <strong>Payment Schedule:</strong> All participants pay program fees in scheduled batches over time - not as a single upfront payment</li>
                            <li class="mb-2"><i class="ri-shield-check-line text-info me-1"></i> <strong>Fully Funded Process:</strong> Pay each batch as scheduled, complete essays and interviews, then get full reimbursement if selected</li>
                            <li class="mb-2"><i class="ri-group-line text-info me-1"></i> <strong>Selection Quota:</strong> Fully funded slots vary based on qualifications, available funding, and program capacity</li>
                            <li class="mb-2"><i class="ri-user-check-line text-info me-1"></i> <strong>Self Funded Guarantee:</strong> Guaranteed participation without competitive selection - just follow payment schedule</li>
                            <li><i class="ri-time-line text-info me-1"></i> <strong>Category Switching:</strong> May have deadlines based on program timeline and essay submission periods</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="switchCategoryBtn" 
                        data-current-category="<?= $participantCategory ?>" 
                        data-participant-id="<?= $currentParticipant['id'] ?? '' ?>">
                    <i class="ri-shuffle-line me-1"></i> 
                    <?php if ($isFullyFunded): ?>
                        Switch to Self Funded
                    <?php else: ?>
                        Switch to Fully Funded
                    <?php endif; ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle category switching with full API integration
    const switchBtn = document.getElementById('switchCategoryBtn');
    
    if (switchBtn) {
        switchBtn.addEventListener('click', function() {
            const currentCategory = this.getAttribute('data-current-category');
            const participantId = this.getAttribute('data-participant-id');
            const targetCategory = currentCategory === 'fully_funded' ? 'self_funded' : 'fully_funded';
            const targetCategoryText = targetCategory === 'fully_funded' ? 'Fully Funded' : 'Self Funded';
            const currentCategoryText = currentCategory === 'fully_funded' ? 'Fully Funded' : 'Self Funded';
            
            // Enhanced confirmation dialog with SweetAlert2
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Switch Registration Category?',
                    html: `
                        <div class="text-start">
                            <p class="mb-3">You are about to switch from <strong>${currentCategoryText}</strong> to <strong>${targetCategoryText}</strong>.</p>
                            <div class="alert ${targetCategory === 'fully_funded' ? 'alert-info' : 'alert-warning'}">
                                <h6><i class="ri-information-line me-1"></i> ${targetCategory === 'fully_funded' ? 'Fully Funded' : 'Self Funded'} Requirements:</h6>
                                <ul class="mb-0 small">
                                    ${targetCategory === 'fully_funded' ? `
                                        <li>Complete comprehensive application essays</li>
                                        <li>Participate in interview sessions</li>
                                        <li>Pay all scheduled fee batches initially</li>
                                        <li>Get full reimbursement if selected</li>
                                        <li>Competitive selection process</li>
                                    ` : `
                                        <li>Guaranteed program participation</li>
                                        <li>Standard registration requirements</li>
                                        <li>Pay all scheduled fee batches yourself</li>
                                        <li>No competitive selection needed</li>
                                        <li>Streamlined process</li>
                                    `}
                                </ul>
                            </div>
                            <p class="mb-0"><strong>Are you sure you want to proceed?</strong></p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0066cc',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: `<i class="ri-shuffle-line me-1"></i> Yes, Switch to ${targetCategoryText}`,
                    cancelButtonText: 'Cancel',
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'category-switch-modal'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        performCategorySwitch(participantId, targetCategory, targetCategoryText);
                    }
                });
            } else {
                // Fallback to regular confirm dialog
                const confirmMessage = `Are you sure you want to switch from ${currentCategoryText} to ${targetCategoryText}?\n\n` +
                    `This will change your registration requirements and process.`;
                
                if (confirm(confirmMessage)) {
                    performCategorySwitch(participantId, targetCategory, targetCategoryText);
                }
            }
        });
    }
    
    function performCategorySwitch(participantId, targetCategory, targetCategoryText) {
        const switchBtn = document.getElementById('switchCategoryBtn');
        
        // Show loading state with SweetAlert
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Switching Category...',
                html: `Please wait while we update your participant category to <strong>${targetCategoryText}</strong>.`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        } else {
            // Disable button and show loading for fallback
            switchBtn.disabled = true;
            switchBtn.innerHTML = '<i class="ri-loader-2-line me-1 spin"></i> Processing...';
        }
        
        // Make API call to switch category using the controller endpoint
        fetch('<?= site_url('dashboard/switch-category') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                participant_id: participantId,
                target_category: targetCategory
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Category Switched Successfully!',
                        html: `
                            <div class="text-start">
                                <p class="mb-3">${data.message}</p>
                                <div class="alert alert-success">
                                    <h6><i class="ri-check-line me-1"></i> What's Next:</h6>
                                    <ul class="mb-0 small">
                                        ${targetCategory === 'fully_funded' ? `
                                            <li>Check your email for application essay instructions</li>
                                            <li>Complete all required documentation</li>
                                            <li>Prepare for the interview process</li>
                                            <li>Continue with scheduled payment obligations</li>
                                            <li>Wait for selection results and potential reimbursement</li>
                                        ` : `
                                            <li>Your participation is now guaranteed</li>
                                            <li>Continue with scheduled payment obligations</li>
                                            <li>Complete standard program requirements</li>
                                            <li>No additional essays or interviews needed</li>
                                        `}
                                    </ul>
                                </div>
                                <p class="mb-0 text-muted small"><i class="ri-refresh-line me-1"></i> This page will refresh to show your updated status.</p>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'Perfect!'
                    }).then(() => {
                        // Reload page to reflect changes
                        window.location.reload();
                    });
                } else {
                    alert(data.message + '\n\nThe page will now reload to reflect the changes.');
                    window.location.reload();
                }
            } else {
                // Show error message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Category Switch Failed',
                        html: `
                            <div class="text-start">
                                <p class="mb-2">${data.message}</p>
                                <div class="text-muted small">
                                    <p class="mb-1"><i class="ri-information-line me-1"></i> This might happen if:</p>
                                    <ul class="mb-0">
                                        <li>The switching deadline has passed</li>
                                        <li>Your current program status doesn't allow changes</li>
                                        <li>There are pending requirements to complete first</li>
                                        <li>Technical issues with the system</li>
                                    </ul>
                                </div>
                            </div>
                        `,
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'I Understand'
                    });
                } else {
                    alert('Error: ' + data.message);
                }
            }
        })
        .catch(error => {
            console.error('Category switch error:', error);
            
            // Show error message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Connection Error',
                    html: `
                        <div class="text-start">
                            <p class="mb-2">Unable to switch your participant category at this time.</p>
                            <div class="text-muted small">
                                <p class="mb-1"><i class="ri-wifi-off-line me-1"></i> Possible causes:</p>
                                <ul class="mb-0">
                                    <li>Internet connection issues</li>
                                    <li>Server temporarily unavailable</li>
                                    <li>Session timeout</li>
                                </ul>
                                <p class="mt-2 mb-0">Please check your connection and try again, or contact support if the problem persists.</p>
                            </div>
                        </div>
                    `,
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Try Again Later'
                });
            } else {
                alert('Failed to switch category. Please check your connection and try again, or contact support.');
                // Reset button for fallback
                switchBtn.disabled = false;
                const currentCategory = switchBtn.getAttribute('data-current-category');
                const targetText = currentCategory === 'fully_funded' ? 'Switch to Self Funded' : 'Switch to Fully Funded';
                switchBtn.innerHTML = `<i class="ri-shuffle-line me-1"></i> ${targetText}`;
            }
        });
    }
    
    // Handle modal trigger with action data
    const modal = document.getElementById('categoryInfoModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const action = button?.getAttribute('data-action');
            
            if (action === 'switch') {
                // Automatically focus on the switch button when opened for switching
                setTimeout(() => {
                    const switchBtn = document.getElementById('switchCategoryBtn');
                    if (switchBtn) {
                        switchBtn.focus();
                    }
                }, 500);
            }
        });
    }
});

// CSS for enhanced styling and animations
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .category-switch-modal .swal2-popup {
        font-size: 14px;
    }
    
    .category-switch-modal .alert {
        border: 1px solid;
        border-radius: 6px;
        padding: 12px;
    }
    
    .category-switch-modal .alert-info {
        background-color: #e3f2fd;
        border-color: #1976d2;
        color: #1565c0;
    }
    
    .category-switch-modal .alert-warning {
        background-color: #fff3e0;
        border-color: #f57c00;
        color: #ef6c00;
    }
    
    .category-switch-modal .alert-success {
        background-color: #e8f5e8;
        border-color: #4caf50;
        color: #2e7d32;
    }
`;
document.head.appendChild(style);
</script>
</script>
