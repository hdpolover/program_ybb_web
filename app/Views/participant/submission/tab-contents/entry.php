<div class="tab-pane" id="entry" role="tabpanel">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">

                        <div class="col-lg-12">
                            <div class="mb-3">
                                <h6 class="fw-semibold">Participant Competition Category</h6>
                                <div class="d-flex mt-2">
                                    <div class="flex-shrink-0">
                                        <i class="ri-medal-line text-primary fs-17 me-2"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="text-muted mb-0">
                                            <?php
                                            $categoryText = '-';

                                            if (isset($participantCompetitionCategory) && !empty($participantCompetitionCategory)) {
                                                // loop through the competition categories to find the name
                                                foreach ($competitionCategories as $category) {
                                                    if ($category['id'] == $participantCompetitionCategory['competition_category_id']) {
                                                        $categoryText = $category['category'];
                                                        break;
                                                    }
                                                }
                                            }

                                            echo $categoryText;
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <h6 class="fw-semibold">Participant Program Subtheme</h6>
                                <div class="d-flex mt-2">
                                    <div class="flex-shrink-0">
                                        <i class="ri-bookmark-line text-primary fs-17 me-2"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="text-muted mb-0">
                                            <?php
                                            $subthemeText = '-';

                                            if (isset($participantSubtheme) && !empty($participantSubtheme) && isset($programSubthemes)) {
                                                foreach ($programSubthemes as $subtheme) {
                                                    if ($subtheme['id'] == $participantSubtheme['program_subtheme_id']) {
                                                        $subthemeText = $subtheme['name'];
                                                        break;
                                                    }
                                                }
                                            } 

                                            echo $subthemeText;
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3 mt-2">
                                <h6 class="fw-semibold text-primary"><?= $currentProgram['main_essay_question']; ?></h6>
                            </div>
                        </div>

                        <?php if (isset($programEssays) && !empty($programEssays)) : ?>
                            <?php foreach ($programEssays as $essay) : ?>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h6 class="fw-semibold"><?= $essay['questions'] ?? 'Essay Question' ?></h6>
                                        <div class="d-flex mt-2">
                                            <div class="flex-shrink-0">
                                                <i class="ri-question-answer-line text-primary fs-17 me-2"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="text-muted mb-0">
                                                    <?php
                                                    $essayContent = '';
                                                    if (isset($participantEssays) && !empty($participantEssays)) {
                                                        foreach ($participantEssays as $participantEssay) {
                                                            if ($participantEssay['program_essay_id'] == $essay['id']) {
                                                                $essayContent = $participantEssay['answer'];
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    echo !empty($essayContent) ? $essayContent : 'No submission yet';
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <p class="text-muted">No essay questions available for this program.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!--end col-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>