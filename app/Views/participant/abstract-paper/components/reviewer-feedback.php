<div class="card border mt-4">
    <div class="card-body">
        <h5 class="font-size-16 mb-4">Reviewer Feedback</h5>

        <?php foreach ($abstractData['reviewers'] as $reviewer): ?>
        <div>
            <h6 class="font-size-14 mb-1"><?= esc($reviewer['name']) ?></h6>
            <p class="font-weight-bold mb-2"><?= esc($reviewer['status']) ?></p>
            <p class="text-muted mb-2">
                <?= esc($reviewer['comments']) ?>
            </p>
            <p class="text-muted font-size-12 mb-0"><?= esc($reviewer['date']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>
