<div class="tab-pane" id="achievements" role="tabpanel">
    <form action="javascript:void(0);">
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-3 pb-2">
                    <label for="achievementEditor" class="form-label">Achievements</label>
                    <!-- QuillJS Snow Editor container -->
                    <div id="achievementEditor" class="snow-editor" style="height: 300px;"></div>
                    <input type="hidden" name="achievements_content" id="achievements_content">
                </div>
            </div>
            <!--end col-->

            <div class="col-lg-12">
                <div class="mb-3 pb-2">
                    <label for="experienceEditor" class="form-label">Experiences</label>
                    <!-- QuillJS Snow Editor container for experiences -->
                    <div id="experienceEditor" class="snow-editor" style="height: 300px;"></div>
                    <input type="hidden" name="experiences_content" id="experiences_content">
                </div>
            </div>
            <!--end col-->

            <div class="col-lg-12">
                <div class="hstack gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary" id="updateAchievements">Updates</button>
                    <button type="button" class="btn btn-soft-success">Cancel</button>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </form>
</div>
<!--end tab-pane-->

<!-- Include QuillJS CSS -->
<link href="<?= base_url('assets/libs/quill/quill.core.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?= base_url('assets/libs/quill/quill.snow.css') ?>" rel="stylesheet" type="text/css" />

<!-- Include QuillJS JavaScript -->
<script src="<?= base_url('assets/libs/quill/quill.min.js') ?>"></script>

<!-- Initialize QuillJS editor -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Achievement editor
        var achievementQuill = new Quill('#achievementEditor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'header': [1, 2, 3, 4, 5, 6, false]
                    }],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'align': []
                    }],
                    ['clean']
                ]
            },
            placeholder: 'Enter your achievements...'
        });

        // Experience editor
        var experienceQuill = new Quill('#experienceEditor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'header': [1, 2, 3, 4, 5, 6, false]
                    }],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'align': []
                    }],
                    ['clean']
                ]
            },
            placeholder: 'Enter your experiences...'
        });

        // Store content in hidden inputs when form is submitted
        document.getElementById('updateAchievements').addEventListener('click', function() {
            document.getElementById('achievements_content').value = achievementQuill.root.innerHTML;
            document.getElementById('experiences_content').value = experienceQuill.root.innerHTML;
            // Form submission logic can be added here
            console.log('Saved achievements:', achievementQuill.root.innerHTML);
            console.log('Saved experiences:', experienceQuill.root.innerHTML);
        });
    });
</script>