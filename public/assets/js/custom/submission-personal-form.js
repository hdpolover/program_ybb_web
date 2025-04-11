/**
 * Personal submission form handler
 * Handles nationality and phone number selections, initialization and extraction
 */
document.addEventListener('DOMContentLoaded', function() {
    // Define all helper functions first
    function getNationalityCodeAndFlag() {
        const container = document.querySelector('[data-input-flag][data-option-flag-img-name]');
        if (!container) return {
            code: '',
            flag: ''
        };

        const selectedValue = container.querySelector('.flag-input').value;
        let code = '';
        let flag = '';

        // Find the matching list item by country name
        const allCountryItems = container.querySelectorAll('.dropdown-menu-list li');
        for (const item of allCountryItems) {
            const countryNameElement = item.querySelector('.country-name');
            if (countryNameElement && countryNameElement.textContent.trim() === selectedValue) {
                // Found the matching country - extract code and flag
                const codeElement = item.querySelector('.countrylist-codeno');
                if (codeElement) {
                    code = codeElement.textContent.trim();
                }

                const flagImg = item.querySelector('.options-flagimg');
                if (flagImg && flagImg.src) {
                    flag = flagImg.src.split('/').pop().split('.')[0]; // Get the flag image name
                }

                // Mark this item as active for consistency
                item.classList.add('active');

                // Remove active class from other items
                allCountryItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                    }
                });

                break;
            }
        }

        console.log('Nationality data:', {
            value: selectedValue,
            code: code,
            flag: flag
        });

        return {
            code: code,
            flag: flag
        };
    }

    function getPhoneInputData(inputId) {
        const inputElement = document.getElementById(inputId);
        if (!inputElement) return {
            code: '',
            number: '',
            full: '',
            flag: ''
        };

        const container = inputElement.closest('[data-input-flag]');
        if (!container) return {
            code: '',
            number: '',
            full: '',
            flag: ''
        };

        // Find the active item in the dropdown - it has the most accurate data
        const activeItem = container.querySelector('.dropdown-menu-list li.active');
        
        // Default values if nothing is found
        let code = '';
        let flag = '';
        const number = inputElement.value.trim();
        
        if (activeItem) {
            // Get code from the active item
            const itemCodeElement = activeItem.querySelector('.countrylist-codeno');
            if (itemCodeElement) {
                code = itemCodeElement.textContent.trim();
            }
            
            // Get flag from the active item
            const itemFlagImg = activeItem.querySelector('.options-flagimg');
            if (itemFlagImg && itemFlagImg.src) {
                flag = itemFlagImg.src.split('/').pop().split('.')[0];
            }
        } else {
            // Fallback to button elements if no active item
            const codeElement = container.querySelector('.country-codeno');
            if (codeElement) {
                code = codeElement.textContent.trim();
            }
            
            const flagImg = container.querySelector('.country-flagimg');
            if (flagImg && flagImg.src) {
                flag = flagImg.src.split('/').pop().split('.')[0];
            }
        }

        console.log(`Phone data for ${inputId}:`, {
            code,
            number,
            full: code + number,
            flag,
        });

        return {
            code: code,
            number: number,
            full: code + number,
            flag: flag
        };
    }

    // Initialize nationality from database values
    function initializeNationality(savedNationality, savedNationalityCode, savedNationalityFlag) {            
        if (!savedNationality && !savedNationalityCode && !savedNationalityFlag) return;
        
        // Find nationality container
        const nationalityContainer = document.querySelector('[data-input-flag][data-option-flag-img-name]');
        if (!nationalityContainer) return;

        // Set value in the input field (already done in the HTML)
        const flagInput = nationalityContainer.querySelector('.flag-input');
        
        // Ensure dropdown items are loaded
        const checkDropdownReady = setInterval(function() {
            const dropdownItems = nationalityContainer.querySelectorAll('.dropdown-menu-list li');
            if (dropdownItems.length > 0) {
                clearInterval(checkDropdownReady);
                
                // Try to find the matching country in the dropdown
                let matchFound = false;
                dropdownItems.forEach(item => {
                    // Remove any existing active classes first
                    item.classList.remove('active');
                    
                    const countryName = item.querySelector('.country-name')?.textContent.trim();
                    
                    // Try to match by name
                    if (countryName === savedNationality) {
                        item.classList.add('active');
                        const flagImg = item.querySelector('.options-flagimg')?.getAttribute('src');
                        if (flagImg) {
                            // Set the background image for the input field
                            flagInput.style.backgroundImage = `url(${flagImg})`;
                            // Set background properties for proper display
                            flagInput.style.backgroundRepeat = "no-repeat";
                            flagInput.style.backgroundPosition = "10px center";
                            flagInput.style.paddingLeft = "40px";
                        }
                        matchFound = true;
                    }
                });
                
                // If no match by name, try by code or flag if provided
                if (!matchFound && (savedNationalityCode || savedNationalityFlag)) {
                    dropdownItems.forEach(item => {
                        const countryCode = item.querySelector('.countrylist-codeno')?.textContent.trim();
                        const flagImg = item.querySelector('.options-flagimg')?.getAttribute('src');
                        const flagName = flagImg?.split('/')?.pop()?.split('.')?.[0];
                        
                        if ((savedNationalityCode && countryCode === savedNationalityCode) || 
                            (savedNationalityFlag && flagName === savedNationalityFlag)) {
                            item.classList.add('active');
                            const countryName = item.querySelector('.country-name')?.textContent.trim();
                            if (countryName && !savedNationality) {
                                flagInput.value = countryName;
                            }
                            if (flagImg) {
                                // Set the background image for the input field
                                flagInput.style.backgroundImage = `url(${flagImg})`;
                                // Set background properties for proper display
                                flagInput.style.backgroundRepeat = "no-repeat";
                                flagInput.style.backgroundPosition = "10px center";
                                flagInput.style.paddingLeft = "40px";
                            }
                        }
                    });
                }
            }
        }, 300); // Check every 300ms for dropdown items to be loaded
    }

    function initializeSinglePhoneNumber(inputId, savedCode, savedFlag) {
        const inputElement = document.getElementById(inputId);
        if (!inputElement) return;
        
        const container = inputElement.closest('[data-input-flag]');
        if (!container) return;
        
        const flagImg = container.querySelector('.country-flagimg');
        const codeElement = container.querySelector('.country-codeno');
        
        // Ensure dropdown items are loaded
        const checkDropdownReady = setInterval(function() {
            const dropdownItems = container.querySelectorAll('.dropdown-menu-list li');
            if (dropdownItems.length > 0) {
                clearInterval(checkDropdownReady);
                
                let matchFound = false;
                
                // Clean saved code (sometimes it might have + in the beginning)
                const cleanedCode = savedCode.replace(/\+/g, '').trim();
                
                // Try to find the matching country in the dropdown
                dropdownItems.forEach(item => {
                    // Remove any existing active classes first
                    item.classList.remove('active');
                    
                    // Get country code from item (clean it too just in case)
                    const itemCodeElement = item.querySelector('.countrylist-codeno');
                    const itemCode = itemCodeElement ? 
                        itemCodeElement.textContent.replace(/\+/g, '').trim() : '';
                    
                    // Get flag from item
                    const itemFlagImg = item.querySelector('.options-flagimg');
                    const itemFlagSrc = itemFlagImg ? itemFlagImg.getAttribute('src') : '';
                    const itemFlag = itemFlagSrc ? 
                        itemFlagSrc.split('/').pop().split('.')[0] : '';
                        
                    // Match by code or flag
                    if ((cleanedCode && itemCode === cleanedCode) || 
                        (savedFlag && itemFlag === savedFlag)) {
                        
                        // Mark as active
                        item.classList.add('active');
                        
                        // Update the button with flag and code
                        if (flagImg && itemFlagImg) {
                            flagImg.setAttribute('src', itemFlagImg.getAttribute('src'));
                        }
                        
                        if (codeElement && itemCodeElement) {
                            codeElement.textContent = itemCodeElement.textContent;
                        }
                        
                        matchFound = true;
                    }
                });
                
                // If no match was found, try to default to the first item
                if (!matchFound && dropdownItems.length > 0) {
                    const firstItem = dropdownItems[0];
                    firstItem.classList.add('active');
                    
                    const firstItemFlagImg = firstItem.querySelector('.options-flagimg');
                    const firstItemCodeElement = firstItem.querySelector('.countrylist-codeno');
                    
                    if (flagImg && firstItemFlagImg) {
                        flagImg.setAttribute('src', firstItemFlagImg.getAttribute('src'));
                    }
                    
                    if (codeElement && firstItemCodeElement) {
                        codeElement.textContent = firstItemCodeElement.textContent;
                    }
                }
            }
        }, 300); // Check every 300ms for dropdown items to be loaded
    }

    // Make functions available globally
    window.YBBPersonalForm = {
        getNationalityCodeAndFlag: getNationalityCodeAndFlag,
        getPhoneInputData: getPhoneInputData,
        initializeNationality: initializeNationality,
        initializeSinglePhoneNumber: initializeSinglePhoneNumber
    };
});
