/**
 * Fix for the YBB personal submission form
 * This script fixes issues with the form submission by properly defining the required functions
 */
document.addEventListener('DOMContentLoaded', function() {
    // Define the nationality helper function in the global scope
    window.getNationalityCodeAndFlag = function() {
        const container = document.querySelector('[data-input-flag][data-option-flag-img-name]');
        if (!container) return { code: '', flag: '' };

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
                    flag = flagImg.src.split('/').pop().split('.')[0];
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

        return { code: code, flag: flag };
    };

    // Define the phone input helper function in the global scope
    window.getPhoneInputData = function(inputId) {
        const inputElement = document.getElementById(inputId);
        if (!inputElement) return { code: '', number: '', full: '', flag: '' };

        const container = inputElement.closest('[data-input-flag]');
        if (!container) return { code: '', number: '', full: '', flag: '' };

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

        return {
            code: code,
            number: number,
            full: code + number,
            flag: flag
        };
    };

    // Fix the Save button functionality
    const saveButton = document.getElementById('save-personal-btn');
    if (saveButton) {
        saveButton.addEventListener('click', function() {
            // Show loading state
            const spinner = this.querySelector('.loading-spinner');
            spinner.classList.remove('d-none');
            this.disabled = true;

            // Collect form data using the global helper functions
            const formData = {
                participant: {
                    full_name: document.getElementById('personal-fullname').value,
                    birthdate: document.getElementById('personal-birthdate').value,
                    gender: document.getElementById('personal-gender').value,
                    nationality: document.querySelector('[data-input-flag][data-option-flag-img-name] .flag-input').value,
                    nationality_code: window.getNationalityCodeAndFlag().code,
                    nationality_flag: window.getNationalityCodeAndFlag().flag,
                    origin_address: document.getElementById('personal-origin-address').value,
                    current_address: document.getElementById('personal-current-address').value,
                    country_code: window.getPhoneInputData('personal-phone').code,
                    phone_number: window.getPhoneInputData('personal-phone').number,
                    phone_flag: window.getPhoneInputData('personal-phone').flag,
                    emergency_country_code: window.getPhoneInputData('emergency-phone').code,
                    emergency_phone_flag: window.getPhoneInputData('emergency-phone').flag,
                    emergency_account: window.getPhoneInputData('emergency-phone').number,
                    contact_relation: document.getElementById('emergency-relationship').value,
                    tshirt_size: document.getElementById('personal-tshirt').value,
                    disease_history: document.getElementById('personal-disease').value,
                    profile_image: document.querySelector('input[name="profile_image_data"]')?.value || null
                }
            };

            console.log('Submitting form data:', formData);

            // Get participant ID from session
            const participant_id = document.getElementById('participant-id-holder')?.value;
            
            if (!participant_id) {
                console.error('Missing participant ID');
                spinner.classList.add('d-none');
                this.disabled = false;
                YBBAlerts.error('Error Saving Data', 'Could not find participant ID.');
                return;
            }

            // Send the data to the server
            fetch(`/submission/personal/${participant_id}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'include',
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    YBBAlerts.success('Data Saved', 'Your personal information has been saved successfully.', function() {
                        document.getElementById('steparrow-professional-tab').click();
                    });
                } else {
                    // Show error with SweetAlert
                    const errorMessage = data.message || 'There was a problem saving your personal information.';
                    YBBAlerts.error('Error Saving Data', errorMessage);
                }
            })
            .catch(error => {
                console.error('Error saving data:', error);
                YBBAlerts.error('Error Saving Data', 'An unexpected error occurred while saving your data. Please try again later.');
            })
            .finally(() => {
                // Hide loading state
                spinner.classList.add('d-none');
                this.disabled = false;
            });
        });
    } else {
        console.error('Save button not found');
    }
});
