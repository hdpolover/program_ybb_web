/**
 * Nationality flag initialization fix
 * This script specifically addresses the issue with nationality flag not loading correctly
 */
document.addEventListener('DOMContentLoaded', function() {
    // Fix for nationality flag initialization
    function initializeNationalityFlag() {
        // Get saved values from PHP
        const savedNationality = document.getElementById('saved-nationality')?.value || '';
        const savedNationalityFlag = document.getElementById('saved-nationality-flag')?.value || '';
        
        if (!savedNationality && !savedNationalityFlag) return;
        
        console.log('Initializing nationality with flag:', savedNationalityFlag, 'and name:', savedNationality);
        
        // Find nationality container and input
        const nationalityContainer = document.querySelector('[data-input-flag][data-option-flag-img-name]');
        if (!nationalityContainer) {
            console.error('Nationality container not found');
            return;
        }
        
        const flagInput = nationalityContainer.querySelector('.flag-input');
        if (!flagInput) {
            console.error('Flag input not found');
            return;
        }
        
        // Ensure dropdown items are loaded before trying to match
        const checkDropdownReady = setInterval(function() {
            const dropdownItems = nationalityContainer.querySelectorAll('.dropdown-menu-list li');
            if (dropdownItems.length > 0) {
                clearInterval(checkDropdownReady);
                
                let matchFound = false;
                
                // First try to match by name (most reliable)
                if (savedNationality) {
                    for (const item of dropdownItems) {
                        const countryName = item.querySelector('.country-name')?.textContent.trim();
                        if (countryName === savedNationality) {
                            // Found the matching country
                            const flagImg = item.querySelector('.options-flagimg')?.getAttribute('src');
                            if (flagImg) {
                                console.log('Matched by name, setting flag:', flagImg);
                                // Set the background image for the input field
                                flagInput.style.backgroundImage = `url(${flagImg})`;
                                flagInput.style.backgroundRepeat = "no-repeat";
                                flagInput.style.backgroundPosition = "10px center";
                                flagInput.style.paddingLeft = "40px";
                                
                                // Mark this item as active
                                item.classList.add('active');
                            }
                            matchFound = true;
                            break;
                        }
                    }
                }
                
                // If no match by name, try by flag
                if (!matchFound && savedNationalityFlag) {
                    console.log('Trying to match by flag:', savedNationalityFlag);
                    for (const item of dropdownItems) {
                        const flagImg = item.querySelector('.options-flagimg')?.getAttribute('src');
                        const flagName = flagImg?.split('/')?.pop()?.split('.')?.[0];
                        
                        if (flagName === savedNationalityFlag) {
                            console.log('Matched by flag, setting flag:', flagImg);
                            // Found the matching flag
                            flagInput.style.backgroundImage = `url(${flagImg})`;
                            flagInput.style.backgroundRepeat = "no-repeat";
                            flagInput.style.backgroundPosition = "10px center";
                            flagInput.style.paddingLeft = "40px";
                            
                            // If input value is empty, set it to the country name
                            if (!flagInput.value || flagInput.value === '') {
                                const countryName = item.querySelector('.country-name')?.textContent.trim();
                                if (countryName) {
                                    flagInput.value = countryName;
                                }
                            }
                            
                            // Mark this item as active
                            item.classList.add('active');
                            matchFound = true;
                            break;
                        }
                    }
                }
                
                if (!matchFound) {
                    console.warn('No match found for nationality or flag');
                }
            }
        }, 200);
    }
    
    // Run the initialization 
    setTimeout(initializeNationalityFlag, 300);
});
