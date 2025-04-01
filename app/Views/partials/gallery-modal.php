<?php
/**
 * Gallery modal component
 * Shows a modal with larger images and descriptions when gallery items are clicked
 */
?>
<!-- Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-semibold" id="galleryModalLabel">Image Title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <img src="" id="galleryModalImg" class="img-fluid w-100" alt="Gallery Image" style="max-height: 80vh; object-fit: contain;">
                <div class="p-3">
                    <p class="text-muted mb-0" id="galleryModalDesc">Image description goes here</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            
            </div>
        </div>
    </div>
</div>