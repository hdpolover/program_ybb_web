<!-- TEST: Minimal abstract view for debugging -->
<div class="test-abstract-layout">
    <h3>Abstract View Test</h3>
    <p>If you can see this, the abstract view is being loaded correctly.</p>
    
    <!-- Test each component include one by one -->
    <div class="test-section">
        <h4>Testing Styles Include:</h4>
        <?= $this->include('participant/abstract-paper/components/abstract-view-styles') ?>
        <p>✓ Styles loaded</p>
    </div>
    
    <div class="test-section">
        <h4>Testing Helpers Include:</h4>
        <?= $this->include('participant/abstract-paper/components/abstract-view-helpers') ?>
        <p>✓ Helpers loaded</p>
    </div>
    
    <div class="test-section">
        <h4>Testing Header Include:</h4>
        <?= $this->include('participant/abstract-paper/components/abstract-header') ?>
        <p>✓ Header loaded</p>
    </div>
    
    <p><strong>If you see this message, the view includes are working properly.</strong></p>
</div>
