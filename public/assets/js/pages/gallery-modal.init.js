/*
 * Gallery Modal Initialization
 * Handles opening the modal with image, title and description
 */

document.addEventListener("DOMContentLoaded", function() {
    // Get all gallery popup links
    const galleryItems = document.querySelectorAll('.gallery-popup');
    const galleryModal = document.getElementById('galleryModal');
    
    if (galleryItems.length > 0 && galleryModal) {
        // Make sure Bootstrap is properly loaded before initializing the modal
        let modalInstance;
        
        // Check if Bootstrap is available
        if (typeof bootstrap !== 'undefined') {
            modalInstance = new bootstrap.Modal(galleryModal);
        } else {
            console.error('Bootstrap library is not loaded properly. The gallery modal won\'t work.');
            return;
        }
        
        const modalImg = document.getElementById('galleryModalImg');
        const modalTitle = document.getElementById('galleryModalLabel');
        const modalDesc = document.getElementById('galleryModalDesc');
        
        // Add click event to each gallery item
        galleryItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Set modal content from data attributes
                const imgSrc = this.getAttribute('data-src');
                const title = this.getAttribute('data-title');
                const description = this.getAttribute('data-description');
                
                modalImg.src = imgSrc;
                modalTitle.textContent = title || 'Gallery Image';
                modalDesc.textContent = description || '';
                
                // Show the modal
                modalInstance.show();
                
                console.log("Gallery modal opened with image:", imgSrc);
            });
        });
        
        // Add keyboard navigation
        let currentIndex = 0;
        
        document.addEventListener('keydown', function(e) {
            if (!galleryModal.classList.contains('show')) return;
            
            // Find current index
            const imgSrc = modalImg.src;
            galleryItems.forEach((item, index) => {
                if (item.getAttribute('data-src') === imgSrc) {
                    currentIndex = index;
                }
            });
            
            // Navigate with arrow keys
            if (e.key === 'ArrowLeft') {
                currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
                updateModal(galleryItems[currentIndex]);
            } else if (e.key === 'ArrowRight') {
                currentIndex = (currentIndex + 1) % galleryItems.length;
                updateModal(galleryItems[currentIndex]);
            }
        });
        
        // Update modal function
        function updateModal(item) {
            const imgSrc = item.getAttribute('data-src');
            const title = item.getAttribute('data-title');
            const description = item.getAttribute('data-description');
            
            modalImg.src = imgSrc;
            modalTitle.textContent = title || 'Gallery Image';
            modalDesc.textContent = description || '';
        }
    } else {
        console.log("Gallery items or modal not found:", { 
            galleryItemsCount: galleryItems?.length, 
            modalExists: !!galleryModal 
        });
    }
    
    // Add hover effect to gallery images
    const galleryImages = document.querySelectorAll('.gallery-img');
    galleryImages.forEach(img => {
        img.addEventListener('mouseover', function() {
            this.style.transform = 'scale(1.05)';
        });
        img.addEventListener('mouseout', function() {
            this.style.transform = 'scale(1)';
        });
    });
});