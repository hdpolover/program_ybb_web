/**
 * YBB Personal Submission Form Handler
 * 
 * Comprehensive file that handles all JavaScript functionality for the personal submission form:
 * - Profile image upload and preview
 * - Nationality selection and flag display
 * - Phone number country code selection
 * - Form submission and validation
 */

document.addEventListener('DOMContentLoaded', function () {

    // ======= HELPER FUNCTIONS =======

    /**
     * Extract nationality code and flag from selected country
     */
    function getNationalityCodeAndFlag() {
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

        return { code, flag };
    }

    /**
     * Extract phone data from phone input fields
     */
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

        return {
            code: code,
            number: number,
            full: code + number,
            flag: flag
        };
    }

    // ======= INITIALIZATION =======

    /**
     * Initialize nationality field from database values
     */
    function initializeNationality() {
        // Get stored values from hidden fields
        const savedNationality = document.getElementById('saved-nationality')?.value || '';
        const savedNationalityCode = document.getElementById('saved-nationality-code')?.value || '';
        const savedNationalityFlag = document.getElementById('saved-nationality-flag')?.value || '';

        if (!savedNationality && !savedNationalityCode && !savedNationalityFlag) return;

        console.log('Initializing nationality with:', {
            name: savedNationality,
            code: savedNationalityCode,
            flag: savedNationalityFlag
        });

        // Find nationality container
        const nationalityContainer = document.querySelector('[data-input-flag][data-option-flag-img-name]');
        if (!nationalityContainer) return;

        // Set value in the input field (already done in the HTML)
        const flagInput = nationalityContainer.querySelector('.flag-input');

        // Ensure dropdown items are loaded
        const checkDropdownReady = setInterval(function () {
            const dropdownItems = nationalityContainer.querySelectorAll('.dropdown-menu-list li');
            if (dropdownItems.length > 0) {
                clearInterval(checkDropdownReady);

                // Try to find the matching country in the dropdown
                let matchFound = false;

                // First try to match by name (most reliable)
                if (savedNationality) {
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
                }

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

    /**
     * Initialize phone number fields from database values
     */
    function initializePhoneNumbers() {
        // Initialize personal phone number
        initializeSinglePhoneNumber(
            'personal-phone',
            document.getElementById('saved-country-code')?.value || '+1',
            document.getElementById('saved-phone-flag')?.value || 'us'
        );

        // Initialize emergency contact phone
        initializeSinglePhoneNumber(
            'emergency-phone',
            document.getElementById('saved-emergency-country-code')?.value || '+1',
            document.getElementById('saved-emergency-phone-flag')?.value || 'us'
        );
    }

    /**
     * Initialize a single phone number field
     */
    function initializeSinglePhoneNumber(inputId, savedCode, savedFlag) {
        const inputElement = document.getElementById(inputId);
        if (!inputElement) return;

        const container = inputElement.closest('[data-input-flag]');
        if (!container) return;

        const flagImg = container.querySelector('.country-flagimg');
        const codeElement = container.querySelector('.country-codeno');

        // Ensure dropdown items are loaded
        const checkDropdownReady = setInterval(function () {
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

    // ======= PROFILE IMAGE HANDLING =======

    function setupProfileImageHandling() {
        const profileImgInput = document.getElementById('profile-img-file-input');
        const profileImageContainer = document.getElementById('profile-image-container');
        const defaultImgSrc = 'https://storage.ybbfoundation.com/general-files/default.jpg';

        if (!profileImgInput || !profileImageContainer) return;

        // Create a reset button
        const resetButton = document.createElement('button');
        resetButton.type = 'button';
        resetButton.className = 'btn btn-sm btn-outline-danger position-absolute bottom-0 start-0 m-1';
        resetButton.innerHTML = '<i class="ri-delete-bin-line"></i>';
        resetButton.title = 'Remove image';
        resetButton.style.display = 'none'; // Hidden by default

        // Add the reset button to the profile user container
        document.querySelector('.profile-user')?.appendChild(resetButton);        // Handle image selection
        profileImgInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                // Validate the file is an image
                if (!file.type.match('image.*')) {
                    alert('Please select an image file');
                    return;
                }

                // Size validation - limit to 2MB to prevent timeout issues
                if (file.size > 2 * 1024 * 1024) {
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Image Too Large',
                            html: 'To prevent upload timeouts, please select an image smaller than 2MB.<br>Your selected image is ' + (file.size / (1024 * 1024)).toFixed(2) + 'MB.',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert('Image size should be less than 2MB. Your selected image is ' + (file.size / (1024 * 1024)).toFixed(2) + 'MB.');
                    }
                    return;
                }

                // Show loading while processing the image
                if (window.Swal) {
                    Swal.fire({
                        title: 'Processing Image',
                        html: 'Please wait while we optimize your image...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                } const reader = new FileReader();
                reader.onload = function (e) {
                    // Create an image object to resize/optimize the image
                    const img = new Image();
                    img.onload = function () {
                        // Close the processing dialog if it's open
                        if (window.Swal) {
                            Swal.close();
                        }

                        // Set a max width/height for the resized image to reduce size
                        const MAX_WIDTH = 600;
                        const MAX_HEIGHT = 600;

                        // Calculate new dimensions while maintaining aspect ratio
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > MAX_WIDTH) {
                                height = Math.round(height * (MAX_WIDTH / width));
                                width = MAX_WIDTH;
                            }
                        } else {
                            if (height > MAX_HEIGHT) {
                                width = Math.round(width * (MAX_HEIGHT / height));
                                height = MAX_HEIGHT;
                            }
                        }

                        // Create a canvas to resize the image
                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;

                        // Draw the resized image on the canvas
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        // Get the resized image as a data URL with reduced quality
                        const optimizedImageData = canvas.toDataURL('image/jpeg', 0.8);

                        // Set background image with optimized image
                        profileImageContainer.style.backgroundImage = `url('${optimizedImageData}')`;

                        // Store the optimized image data
                        const imgData = document.createElement('input');
                        imgData.type = 'hidden';
                        imgData.id = 'profile_image_data';
                        imgData.name = 'profile_image_data';
                        imgData.value = optimizedImageData;

                        // Remove any existing image data input
                        const existingData = document.querySelector('input[name="profile_image_data"]');
                        if (existingData) {
                            existingData.parentNode.removeChild(existingData);
                        }

                        // Add the new image data input to the profile user container
                        const profileUserContainer = document.querySelector('.profile-user');
                        if (profileUserContainer) {
                            profileUserContainer.appendChild(imgData);
                        } else {
                            document.body.appendChild(imgData);
                        }

                        // Don't store in sessionStorage as it can easily exceed storage limits

                        // Show reset button
                        resetButton.style.display = 'block';
                    };

                    // Set the image source to the original data URL
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Reset button functionality
        resetButton.addEventListener('click', function () {
            // Reset to default image
            profileImageContainer.style.backgroundImage = `url('${defaultImgSrc}')`;

            // Clear file input
            profileImgInput.value = '';

            // Remove the stored image data
            const existingData = document.querySelector('input[name="profile_image_data"]');
            if (existingData) {
                existingData.parentNode.removeChild(existingData);
            }

            // Hide reset button
            resetButton.style.display = 'none';
        });
    }

    // ======= FORM SUBMISSION =======    
    function setupFormSubmission() {
        const saveButton = document.getElementById('save-personal-btn');
        if (!saveButton) return;

        saveButton.addEventListener('click', function () {
            // Show loading state on the button
            const spinner = this.querySelector('.loading-spinner');
            if (spinner) spinner.classList.remove('d-none');
            this.disabled = true;

            // Show loading modal if SweetAlert2 is available
            if (window.Swal) {
                const hasProfileImage = document.querySelector('input[name="profile_image_data"]')?.value || null;
                const loadingMessage = hasProfileImage ?
                    'Uploading profile picture and saving your data...' :
                    'Saving your personal information...';

                Swal.fire({
                    title: 'Saving Data',
                    html: loadingMessage,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            // Collect form data
            const formData = {
                participant: {
                    full_name: document.getElementById('personal-fullname')?.value || '',
                    birthdate: document.getElementById('personal-birthdate')?.value || '',
                    gender: document.getElementById('personal-gender')?.value || '',
                    nationality: document.querySelector('[data-input-flag][data-option-flag-img-name] .flag-input')?.value || '',
                    nationality_code: getNationalityCodeAndFlag().code,
                    nationality_flag: getNationalityCodeAndFlag().flag,
                    origin_address: document.getElementById('personal-origin-address')?.value || '',
                    current_address: document.getElementById('personal-current-address')?.value || '',
                    country_code: getPhoneInputData('personal-phone').code,
                    phone_number: getPhoneInputData('personal-phone').number,
                    phone_flag: getPhoneInputData('personal-phone').flag,
                    emergency_country_code: getPhoneInputData('emergency-phone').code,
                    emergency_phone_flag: getPhoneInputData('emergency-phone').flag,
                    emergency_account: getPhoneInputData('emergency-phone').number,
                    contact_relation: document.getElementById('emergency-relationship')?.value || '',
                    tshirt_size: document.getElementById('personal-tshirt')?.value || '',
                    disease_history: document.getElementById('personal-disease')?.value || '',
                    profile_image: document.querySelector('input[name="profile_image_data"]')?.value || null
                }
            };

            // Get participant ID from session
            const participant_id = document.getElementById('participant-id-holder')?.value;

            if (!participant_id) {
                console.error('Missing participant ID');
                if (spinner) spinner.classList.add('d-none');
                this.disabled = false;
                if (typeof YBBAlerts !== 'undefined') {
                    YBBAlerts.error('Error Saving Data', 'Could not find participant ID.');
                } else {
                    alert('Error: Could not find participant ID.');
                }
                return;
            }


            // Send the data to the server
            console.log('Sending data to server with participant ID:', participant_id);
            // Use window.location.origin instead of BASE_URL which might not be defined
            const baseUrl = window.location.origin || '';
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
                    console.log('Server response:', data);
                    // Close loading modal if it exists
                    if (window.Swal) {
                        Swal.close();
                    }                    if (data.success) {
                        // Update the topbar with the new user information if available
                        if (typeof updateUserInfoInTopbar === 'function') {
                            const fullName = formData.participant.full_name;
                            
                            // Get the proper picture URL from the server response instead of using the base64 data
                            const pictureUrl = data.participant && data.participant.picture_url ? data.participant.picture_url : null;
                            
                            // Only update the topbar if we got a valid URL back from the server
                            if (fullName || pictureUrl) {
                                updateUserInfoInTopbar(fullName, pictureUrl);
                            }
                        }
                        
                        // Show success message
                        if (typeof YBBAlerts !== 'undefined') {
                            YBBAlerts.success('Data Saved', 'Your personal information has been saved successfully.', function () {
                                document.getElementById('steparrow-professional-tab')?.click();
                            });
                        } else {
                            alert('Your personal information has been saved successfully.');
                            document.getElementById('steparrow-professional-tab')?.click();
                        }
                    } else {
                        // Show error message
                        const errorMessage = data.message || 'There was a problem saving your personal information.';
                        if (typeof YBBAlerts !== 'undefined') {
                            YBBAlerts.error('Error Saving Data', errorMessage);
                        } else {
                            alert('Error: ' + errorMessage);
                        }
                    }
                }).catch(error => {
                    console.error('Error saving data:', error);
                    if (typeof YBBAlerts !== 'undefined') {
                        YBBAlerts.error('Error Saving Data', 'An unexpected error occurred while saving your data. Please try again later.');
                    } else {
                        alert('An unexpected error occurred while saving your data. Please try again later.');
                    }
                }).finally(() => {
                    // Hide button loading state
                    if (spinner) spinner.classList.add('d-none');
                    this.disabled = false;

                    // We've removed the overlay creation, so no need to remove it here
                });
        });
    }

    // ======= INITIALIZATION =======

    // Initialize everything
    setupProfileImageHandling();
    initializeNationality();
    initializePhoneNumbers();
    setupFormSubmission();

    // Make helper functions available globally if needed
    window.YBBPersonalForm = {
        getNationalityCodeAndFlag: getNationalityCodeAndFlag,
        getPhoneInputData: getPhoneInputData
    };
});
