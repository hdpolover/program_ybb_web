<div class="tab-pane fade" id="steparrow-entry" role="tabpanel" aria-labelledby="steparrow-entry-tab">
    <div>
        <div class="mb-3">
            <?php if (isset($webSettings['is_journal_type']) && $webSettings['is_journal_type'] === true): ?>
                <div class="alert alert-info border-0 shadow-sm rounded-3 p-4 mb-4">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            <i class="ri-book-mark-line fs-28 text-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-2 fw-semibold">Journal Submission Information</h5>
                            <p class="mb-2 text-dark">This is a journal-type submission. The subtheme you select will be used to determine your abstract contents and research focus.</p>
                            <p class="mb-0 text-dark">Instead of submitting essays, you'll be guided through the abstract submission process after completing your registration.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="alert alert-light border shadow-sm rounded-3 p-4 mb-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <i class="ri-group-line fs-28 text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2 fw-semibold">About Participation Category</h5>
                        <p class="mb-2 text-dark">The participation category determines your placement group in the program. This selection affects:</p>
                        <ul class="mb-0 text-dark">
                            <li>Award eligibility and evaluation criteria</li>
                            <li>Group-specific activities and opportunities</li>
                            <li>Specific requirements and expectations</li>
                        </ul>
                    </div>
                </div>
            </div>

            <label class="form-label" for="entry-competition-category">Participation Category</label>
            <select class="form-select" id="entry-competition-category" required>
                <option value="">Select participation category</option> <?php foreach ($competitionCategories as $category): ?>
                    <option value="<?= $category['id'] ?>"
                        data-description="<?= htmlspecialchars($category['desc'] ?? '') ?>"
                        <?= (isset($participantCompetitionCategory) && $participantCompetitionCategory['competition_category_id'] == $category['id']) ? 'selected' : '' ?>>
                        <?= $category['category'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a participation category</div>
            <div id="category-description" class="form-text mt-2 fst-italic <?= (isset($participantCompetitionCategory) && !empty($participantCompetitionCategory['competition_category_id']) && isset($category['desc']) && !empty($category['desc'])) ? '' : 'd-none' ?>">
                <?php
                if (isset($participantCompetitionCategory) && !empty($participantCompetitionCategory['competition_category_id'])) {
                    foreach ($competitionCategories as $category) {
                        if ($category['id'] == $participantCompetitionCategory['competition_category_id'] && isset($category['desc'])) {
                            echo htmlspecialchars($category['desc']);
                            break;
                        }
                    }
                }
                ?>
            </div>
        </div>

        <div class="mb-3">
            <?php if (isset($webSettings['is_journal_type']) && $webSettings['is_journal_type'] === true): ?>
                <div class="alert alert-light border shadow-sm rounded-3 p-4 mb-4">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            <i class="ri-file-text-line fs-28 text-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-2 fw-semibold">About Program Subtheme</h5>
                            <p class="mb-2 text-dark">For journal submissions, your selected subtheme is crucial as it will:</p>
                            <ul class="mb-0 text-dark">
                                <li>Define the scope of your abstract submission</li>
                                <li>Guide your research focus and approach</li>
                                <li>Determine the evaluation criteria for your work</li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-light border shadow-sm rounded-3 p-4 mb-4">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            <i class="ri-file-text-line fs-28 text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-2 fw-semibold">About Program Subtheme</h5>
                            <p class="mb-2 text-dark">The program subtheme provides focus for your essay submissions and helps align your work with the program's objectives:</p>
                            <ul class="mb-0 text-dark">
                                <li>Guides your essay content and approach</li>
                                <li>Ensures relevance to program goals</li>
                                <li>Helps evaluators assess your submissions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <label class="form-label" for="entry-subtheme">Program Subthemes</label>
            <select class="form-select" id="entry-subtheme" required>
                <option value="">Select subtheme</option>
                <?php foreach ($programSubthemes as $subtheme): ?>
                    <option value="<?= $subtheme['id'] ?>"
                        data-description="<?= htmlspecialchars($subtheme['desc'] ?? '') ?>"
                        <?= (isset($participantSubtheme) && $participantSubtheme['program_subtheme_id'] == $subtheme['id']) ? 'selected' : '' ?>>
                        <?= $subtheme['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a subtheme</div>
            <div id="subtheme-description" class="form-text mt-2 fst-italic <?= (isset($participantSubtheme) && !empty($participantSubtheme['program_subtheme_id']) && isset($subtheme['desc']) && !empty($subtheme['desc'])) ? '' : 'd-none' ?>">
                <?php
                if (isset($participantSubtheme) && !empty($participantSubtheme['program_subtheme_id'])) {
                    foreach ($programSubthemes as $subtheme) {
                        if ($subtheme['id'] == $participantSubtheme['program_subtheme_id'] && isset($subtheme['desc'])) {
                            echo htmlspecialchars($subtheme['desc']);
                            break;
                        }
                    }
                }
                ?>
            </div>
        </div>        <?php if (!isset($webSettings['is_journal_type']) || $webSettings['is_journal_type'] === false): ?>
            <div class="hstack gap-2 mb-3">
                <div class="flex-grow-1">
                    <hr class="text-muted">
                </div>
                <div>Entry Details</div>
                <div class="flex-grow-1">
                    <hr class="text-muted">
                </div>
            </div>            <?php if ((!isset($webSettings['is_journal_type']) || $webSettings['is_journal_type'] === false) && isset($currentProgram['essay_guideline_url']) && !empty($currentProgram['essay_guideline_url'])): ?>
                <div class="mb-3">
                    <div class="alert alert-warning border-0 shadow-sm rounded-3 p-4">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <i class="ri-file-warning-line fs-28 text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-2 fw-semibold">Essay Guidelines</h5>
                                <p class="mb-3 text-dark">Please carefully review the essay guidelines before preparing your submission. The guidelines contain important information about formatting requirements, word limits, evaluation criteria, and other essential details.</p>
                                <p class="mb-3 text-dark"><strong>Note:</strong> Submissions that do not follow the guidelines may receive lower credit scores which can lower the possibility to be selected as a fully funded participant.</p>
                                <a href="<?= htmlspecialchars($currentProgram['essay_guideline_url']) ?>"
                                    class="btn btn-warning"
                                    target="_blank"
                                    rel="noopener noreferrer">
                                    <i class="ri-file-text-line me-1"></i> View Document
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>            <?php if ((!isset($webSettings['is_journal_type']) || $webSettings['is_journal_type'] === false) && isset($currentProgram['main_essay_question']) && !empty($currentProgram['main_essay_question'])): ?>
                <div class="mb-3">
                    <div class="alert alert-primary border-0 shadow-sm rounded-3 p-4">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <i class="ri-question-line fs-24 text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-2 fw-semibold">Main Essay Question</h5>
                                <p class="mb-0 text-dark"><?= htmlspecialchars($currentProgram['main_essay_question']) ?></p>
                                <small class="text-muted mt-2 d-block">Please address this question thoroughly in your essay responses below.</small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>            <?php if ((!isset($webSettings['is_journal_type']) || $webSettings['is_journal_type'] === false) && isset($programEssays) && !empty($programEssays)): ?>
                <?php foreach ($programEssays as $index => $essay): ?>
                    <div class="mb-3">
                        <label class="form-label" for="entry-essay-<?= $index ?>"><?= $essay['questions'] . ' (max ' . $essay['max_word_count'] . ' words)' ?></label>
                        <?php
                        // Look for this essay in the participant's submitted essays
                        $essayContent = '';
                        if (isset($participantEssays) && !empty($participantEssays)) {
                            foreach ($participantEssays as $submittedEssay) {
                                if ($submittedEssay['program_essay_id'] == $essay['id']) {
                                    $essayContent = $submittedEssay['answer'];
                                    break;
                                }
                            }
                        }
                        ?>
                        <textarea class="form-control essay-textarea" id="entry-essay-<?= $index ?>"
                            name="programEssays[<?= $essay['id'] ?>]" rows="4"
                            placeholder="Your answer" required
                            data-max-words="<?= $essay['max_word_count'] ?>"><?= trim($essayContent) ?></textarea>
                        <small class="word-count-info text-muted">
                            <span class="current-word-count">0</span>/<span class="max-word-count"><?= $essay['max_word_count'] ?></span> words
                        </small>
                        <div class="invalid-feedback">Please provide an answer</div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="d-flex align-items-start gap-3 mt-4">
        <button type="button" class="btn btn-success btn-label right ms-auto nexttab" id="save-entry-btn">
            <span class="d-flex align-items-center">
                <span>Save and Continue</span>
                <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>
                <span class="loading-spinner d-none ms-2">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </span>
            </span>
        </button>
    </div>
</div>
<!-- end tab pane -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('entry-competition-category');
        const descriptionDiv = document.getElementById('category-description');

        categorySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const description = selectedOption.getAttribute('data-description');

            if (description && description.trim() !== '') {
                descriptionDiv.textContent = description;
                descriptionDiv.classList.remove('d-none');
            } else {
                descriptionDiv.classList.add('d-none');
            }
        });

        const subthemeSelect = document.getElementById('entry-subtheme');
        const subthemeDescriptionDiv = document.getElementById('subtheme-description');

        subthemeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const description = selectedOption.getAttribute('data-description');

            if (description && description.trim() !== '') {
                subthemeDescriptionDiv.textContent = description;
                subthemeDescriptionDiv.classList.remove('d-none');
            } else {
                subthemeDescriptionDiv.classList.add('d-none');
            }
        });

        const essayTextareas = document.querySelectorAll('.essay-textarea');

        essayTextareas.forEach(function(textarea) {
            const wordCountInfo = textarea.nextElementSibling;
            const currentWordCount = wordCountInfo.querySelector('.current-word-count');
            const maxWordCount = parseInt(textarea.getAttribute('data-max-words'), 10);

            // Store the last valid state of the textarea
            let lastValidValue = textarea.value;
            let lastWordCount = countWords(textarea.value);

            // Initial word count update
            updateWordCount();

            // Add event listener for input changes
            textarea.addEventListener('input', function(e) {
                updateWordCount();
            });

            // Handle edge cases like pasting long content
            textarea.addEventListener('paste', function(e) {
                // Get current text and new text being pasted
                const currentText = textarea.value;
                const pastedText = e.clipboardData.getData('text');

                // Calculate what the combined text would be
                const combinedText = currentText + pastedText;
                const wordCount = countWords(combinedText);

                // If pasting would exceed the word limit, prevent it
                if (wordCount > maxWordCount) {
                    e.preventDefault();
                    YBBAlerts.error('Pasting this text would exceed the maximum word count of ' + maxWordCount + ' words.');
                }
            });

            // Function to count words in a text
            function countWords(text) {
                const trimmedText = (text || '').trim();
                return trimmedText === '' ? 0 : trimmedText.split(/\s+/).length;
            }

            // Function to update the word count display and handle max words
            function updateWordCount() {
                const text = textarea.value;
                const wordCount = countWords(text);

                // Update the display
                currentWordCount.textContent = wordCount;

                // If word count exceeds max, prevent the input
                if (wordCount > maxWordCount) {
                    // Restore the last valid state
                    textarea.value = lastValidValue;
                    currentWordCount.textContent = lastWordCount;

                    // Add visual indicator
                    currentWordCount.classList.add('text-danger');
                    currentWordCount.classList.add('fw-bold');
                } else {
                    // Update the last valid state
                    lastValidValue = textarea.value;
                    lastWordCount = wordCount;

                    // Visual feedback based on word count
                    if (wordCount === maxWordCount) {
                        currentWordCount.classList.add('text-warning');
                        currentWordCount.classList.add('fw-bold');
                        currentWordCount.classList.remove('text-danger');
                    } else {
                        currentWordCount.classList.remove('text-danger');
                        currentWordCount.classList.remove('text-warning');
                        currentWordCount.classList.remove('fw-bold');
                    }
                }
            }
        }); // Add save button functionality
        const saveButton = document.getElementById('save-entry-btn');

        saveButton.addEventListener('click', function() {
            // Basic form validation
            const categorySelect = document.getElementById('entry-competition-category');
            const subthemeSelect = document.getElementById('entry-subtheme');
            const essayTextareas = document.querySelectorAll('.essay-textarea');

            let formValid = true;
            let errorMessage = '';

            // Validate category selection
            if (!categorySelect.value) {
                formValid = false;
                errorMessage = 'Please select a participation category.';
                categorySelect.classList.add('is-invalid');
            } else {
                categorySelect.classList.remove('is-invalid');
            }

            // Validate subtheme selection
            if (!subthemeSelect.value) {
                formValid = false;
                errorMessage = errorMessage || 'Please select a program subtheme.';
                subthemeSelect.classList.add('is-invalid');
            } else {
                subthemeSelect.classList.remove('is-invalid');
            }

            // Validate essay answers
            essayTextareas.forEach(function(textarea) {
                if (!textarea.value.trim()) {
                    formValid = false;
                    errorMessage = errorMessage || 'Please answer all essay questions.';
                    textarea.classList.add('is-invalid');
                } else {
                    textarea.classList.remove('is-invalid');
                }
            });

            if (!formValid) {
                YBBAlerts.error('Validation Error', errorMessage);
                return;
            }

            // Show loading state
            const spinner = this.querySelector('.loading-spinner');
            spinner.classList.remove('d-none');
            this.disabled = true;

            // Collect essay data
            let essays = [];
            document.querySelectorAll('.essay-textarea').forEach(function(textarea) {
                const essayId = textarea.name.match(/\[(\d+)\]/)[1];
                const answer = textarea.value.trim();

                // Skip empty answers (though we've already validated above)
                if (!answer) return;

                essays.push({
                    program_essay_id: essayId,
                    answer: answer
                });
            });

            // Check if we have any essays to submit
            if (essays.length === 0 && document.querySelectorAll('.essay-textarea').length > 0) {
                YBBAlerts.error('Error', 'No essay answers provided. Please enter your essay responses.');
                spinner.classList.add('d-none');
                this.disabled = false;
                return;
            }

            // Collect form data
            const formData = {
                essays: essays,
                competition_category_id: categorySelect.value,
                program_subtheme_id: subthemeSelect.value
            };

            // Get participant ID from session
            const participant_id = <?= isset($participant['id']) ? $participant['id'] : 'null' ?>;

            if (!participant_id) {
                console.error('Participant ID not found');
                return;
            }

            console.log('Submitting entry data:', formData);

            // Send API request
            fetch(`/submission/entry/${participant_id}/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show success message with callback to navigate to next tab
                        YBBAlerts.success('Data Saved', 'Your entry information has been saved successfully.', function() {
                            document.getElementById('steparrow-misc-tab').click();
                        });
                    } else {
                        // Show error with details from the server
                        const errorMessage = data.message || 'There was a problem saving your entry information.';
                        YBBAlerts.error('Error Saving Data', errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Error saving data:', error);
                    YBBAlerts.error('Error Saving Data', 'An unexpected error occurred while saving your data. Please try again later.');

                    // Log additional debug information
                    console.log('Form data that failed to submit:', formData);
                })
                .finally(() => {
                    // Hide loading state
                    spinner.classList.add('d-none');
                    this.disabled = false;
                });
        });
    });
</script>