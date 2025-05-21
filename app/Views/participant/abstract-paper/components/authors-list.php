<div class="card border">
    <div class="card-body">
        <h5 class="font-size-16 mb-4">Co-Authors</h5>

        <?php foreach ($abstractData['authors'] as $author): ?>
            <?php if ($author['isPrimary']): ?>
            <div>
                <h6 class="font-size-14">Primary Author</h6>
                <div class="mb-3">
                    <p class="font-size-15 mb-1"><?= esc($author['name']) ?></p>
                    <p class="text-muted mb-0"><?= esc($author['affiliation']) ?></p>
                </div>
            </div>
            <?php else: ?>
            <div class="mt-4">
                <div class="mb-3">
                    <p class="font-size-15 mb-1"><?= esc($author['name']) ?></p>
                    <p class="text-muted mb-0"><?= esc($author['affiliation']) ?></p>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
