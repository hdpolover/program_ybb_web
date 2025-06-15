<style>
    /* Fix for horizontal overflow and layout issues */
    .abstract-layout {
        overflow-x: hidden;
        max-width: 100%;
    }

    .abstract-layout * {
        box-sizing: border-box;
    }

    .abstract-layout .row {
        margin-left: 0;
        margin-right: 0;
    }

    .abstract-layout .row> * {
        padding-left: calc(var(--bs-gutter-x) * 0.5);
        padding-right: calc(var(--bs-gutter-x) * 0.5);
    }

    /* Ensure cards don't overflow */
    .abstract-layout .card {
        max-width: 100%;
    }

    /* Fix any wide elements */
    .abstract-layout .table-responsive {
        max-width: 100%;
    }
    
    /* Additional fixes for horizontal scroll */
    .abstract-layout .container-fluid {
        overflow-x: hidden;
    }
    
    .abstract-layout .row > * {
        min-width: 0;
    }
    
    /* Ensure no fixed widths cause overflow */
    .abstract-layout pre,
    .abstract-layout code {
        word-wrap: break-word;
        white-space: pre-wrap;
    }
    
    /* Prevent any modal or element from causing overflow */
    .abstract-layout .modal-dialog {
        max-width: calc(100vw - 30px);
    }

    /* Enhanced Status Badge with Pulse Animation */
    .status-pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.05);
            opacity: 0.9;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Timeline for submission details */
    .timeline-simple .timeline-item {
        position: relative;
        padding-left: 1rem;
    }

    .timeline-simple .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0.5rem;
        width: 8px;
        height: 8px;
        background: #dee2e6;
        border-radius: 50%;
    }

    /* Author Type Selection Cards */
    .author-type-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e9ecef;
        background: #fff;
    }

    .author-type-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #dee2e6;
    }

    .author-type-card.selected {
        border-color: #0d6efd;
        background: linear-gradient(135deg, #f8f9ff 0%, #e7f1ff 100%);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
    }

    .author-type-card.selected[data-type="participant"] {
        border-color: #198754;
        background: linear-gradient(135deg, #f8fff9 0%, #e7f6ec 100%);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.15);
    }

    .author-type-card .avatar-title {
        transition: all 0.3s ease;
    }

    .author-type-card:hover .avatar-title {
        transform: scale(1.1);
    }

    .author-type-card .btn {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .author-type-card.selected .btn {
        transform: scale(1.05);
        font-weight: 600;
    }

    /* Search Section Enhancement */
    .bg-soft-success {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }

    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    /* Form Field Enhancements */
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Alert Enhancements */
    .alert {
        border-radius: 8px;
    }

    /* Card hover effects */
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Avatar enhancements */
    .avatar-lg {
        width: 4rem;
        height: 4rem;
    }

    .avatar-sm {
        width: 2.5rem;
        height: 2.5rem;
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    /* Button loading state */
    .btn:disabled {
        opacity: 0.7;
    }

    /* Input group enhancements */
    .input-group .form-control:focus {
        z-index: 3;
    }

    .input-group-text {
        border-color: #dee2e6;
    }

    /* Search result animation */
    .alert.fade.show {
        animation: slideInUp 0.3s ease;
    }

    @keyframes slideInUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .author-type-card {
            margin-bottom: 1rem;
        }

        .author-type-card .card-body {
            padding: 1rem;
        }

        .avatar-lg {
            width: 3rem;
            height: 3rem;
        }
    }

    /* Enhanced card layout for better spacing and height consistency */
    .card.h-100 {
        display: flex;
        flex-direction: column;
    }

    .card.h-100 .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    /* Author list compact styling */
    .author-list-compact .author-item {
        transition: all 0.2s ease;
    }

    .author-list-compact .author-item:hover {
        background-color: #f8f9fa !important;
        transform: translateX(2px);
    }

    /* Ensure equal height cards in second row */
    .row .col-lg-4 .card {
        min-height: 400px;
    }

    /* Compact badge styling */
    .badge.flex-shrink-0 {
        font-size: 0.7rem;
        white-space: nowrap;
    }

    /* Text truncation utility */
    .min-width-0 {
        min-width: 0;
    }

    /* Abstract content area improvements */
    .abstract-content-area {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }

    /* Paper upload states */
    .paper-upload-state {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    }

    /* Feedback styling */
    .feedback-item {
        transition: all 0.2s ease;
    }

    .feedback-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }

    .bg-soft-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }

    .feedback-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .feedback-list::-webkit-scrollbar {
        width: 4px;
    }

    .feedback-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .feedback-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .feedback-list::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Feedback badge animation */
    .feedback-badge {
        animation: fadeInUp 0.3s ease;
    }

    @keyframes fadeInUp {
        from {
            transform: translateY(10px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Author type conditional styling */
    .non-participant-optional,
    .non-participant-text {
        display: none;
    }

    .author-type-card[data-type="new"].selected~* .non-participant-optional,
    .author-type-card[data-type="new"].selected~* .non-participant-text {
        display: inline;
    }

    .author-type-card[data-type="new"].selected~* .participant-required,
    .author-type-card[data-type="new"].selected~* .participant-text {
        display: none;
    }

    .author-type-card[data-type="participant"].selected~* .non-participant-optional,
    .author-type-card[data-type="participant"].selected~* .non-participant-text {
        display: none;
    }

    .author-type-card[data-type="participant"].selected~* .participant-required,
    .author-type-card[data-type="participant"].selected~* .participant-text {
        display: inline;
    }

    /* Enhanced version status indicators */
    .badge.bg-warning.text-dark {
        animation: pulse-warning 2s infinite;
    }

    @keyframes pulse-warning {
        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.8;
        }
    }

    /* Active version highlight */
    .accordion-item.active-version {
        border-left: 4px solid #ffc107;
        background: linear-gradient(90deg, rgba(255, 193, 7, 0.05) 0%, transparent 50%);
    }

    /* Version comparison visual aids */
    .version-comparison-card {
        position: relative;
        overflow: hidden;
    }

    .version-comparison-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #ffc107, #fd7e14);
    }

    /* Enhanced alert styling for version information */
    .alert-light {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
    }    /* Version History Modal Enhancements */
    #versionHistoryModal .modal-header {
        background: linear-gradient(135deg, #405189 0%, #5a67d8 100%);
        border-bottom: none;
        color: white;
    }

    #versionHistoryModal .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    /* Version Timeline Indicator */
    .version-timeline-info {
        background: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%);
        border-left: 4px solid #405189;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    /* Version Accordion Enhancements */
    #versionAccordion .accordion-item {
        border: 1px solid #e0e6ed;
        border-radius: 0.5rem !important;
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    #versionAccordion .accordion-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    #versionAccordion .accordion-item:first-child {
        border-left: 3px solid #28a745;
    }

    #versionAccordion .accordion-item:last-child {
        border-left: 3px solid #6c757d;
    }

    #versionAccordion .accordion-button {
        padding: 1rem 1.25rem;
        font-weight: 500;
        border: none;
        background: transparent;
    }

    #versionAccordion .accordion-button:not(.collapsed) {
        background: rgba(64, 81, 137, 0.05);
        color: #405189;
        box-shadow: none;
    }

    #versionAccordion .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(64, 81, 137, 0.25);
    }

    /* Version Content Styling */
    .abstract-content.bg-light {
        border: 1px solid #e9ecef;
        border-left: 4px solid #405189;
    }

    /* Version Feedback Styling */
    .version-feedback-list .feedback-item {
        transition: all 0.2s ease;
    }

    .version-feedback-list .feedback-item:hover {
        transform: translateX(2px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }

    .text-warning {
        color: #856404 !important;
    }

    .border-warning {
        border-color: #ffc107 !important;
    }

    /* Responsive adjustments for version modal */
    @media (max-width: 768px) {
        #versionHistoryModal .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
        
        #versionAccordion .accordion-button {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }
        
        .badge {
            font-size: 0.7rem;
        }
    }

    /* Version switcher button enhancement */
    .btn-outline-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
        transition: all 0.2s ease;
    }
</style>
