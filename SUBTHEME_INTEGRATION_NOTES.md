# Subtheme Integration in AbstractPaper Controller

## Changes Made to index() Method

### Overview
Modified the `index()` method in `AbstractPaper.php` to properly handle and highlight subtheme data instead of topic data, with appropriate warning messages when subtheme data is not available.

### Key Changes

1. **Subtheme Data Processing**: Extract and validate subtheme information from the API response
2. **Highlighting Data**: Create structured highlight data for selected subthemes
3. **Warning System**: Implement warning messages when subtheme is not selected or participant is not eligible
4. **Enhanced Logging**: Added detailed logging for subtheme operations

### New Data Structure Passed to View

```php
$data = [
    'title' => 'Abstract and Paper',
    'participant_data' => $abstractData,
    'selected_subtheme' => $selectedSubtheme,           // Raw subtheme data
    'subtheme_highlight' => $subthemeHighlight,         // Formatted for highlighting
    'subtheme_warning' => $subthemeWarning,             // Warning messages
    'eligible_for_abstract' => $eligibleForAbstract     // Eligibility status
];
```

### Subtheme Highlight Structure

When a subtheme is selected, `subtheme_highlight` contains:
```php
[
    'id' => $selectedSubtheme['id'],
    'name' => $selectedSubtheme['subtheme_name'],
    'description' => $selectedSubtheme['subtheme_description'],
    'program_subtheme_id' => $selectedSubtheme['program_subtheme_id'],
    'is_active' => $selectedSubtheme['is_active']
]
```

### Warning Structure

When warnings are needed, `subtheme_warning` contains:
```php
[
    'title' => 'Warning Title',
    'message' => 'Detailed warning message',
    'type' => 'warning'|'info'|'error'
]
```

## View Implementation Examples

### Highlighting Selected Subtheme
```php
<?php if (isset($subtheme_highlight)): ?>
    <div class="alert alert-success border-left-success">
        <div class="alert-heading">
            <i class="fas fa-check-circle"></i> Selected Subtheme
        </div>
        <h5><?= esc($subtheme_highlight['name']) ?></h5>
        <?php if (!empty($subtheme_highlight['description'])): ?>
            <p class="mb-0"><?= esc($subtheme_highlight['description']) ?></p>
        <?php endif; ?>
    </div>
<?php endif; ?>
```

### Displaying Warnings
```php
<?php if (isset($subtheme_warning)): ?>
    <div class="alert alert-<?= $subtheme_warning['type'] === 'warning' ? 'warning' : 'info' ?> border-left-<?= $subtheme_warning['type'] === 'warning' ? 'warning' : 'info' ?>">
        <div class="alert-heading">
            <i class="fas fa-exclamation-triangle"></i> <?= esc($subtheme_warning['title']) ?>
        </div>
        <p class="mb-0"><?= esc($subtheme_warning['message']) ?></p>
    </div>
<?php endif; ?>
```

### Conditional Abstract Creation Button
```php
<?php if ($eligible_for_abstract && isset($selected_subtheme)): ?>
    <a href="<?= base_url('abstract-paper/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create New Abstract
    </a>
<?php else: ?>
    <button class="btn btn-secondary" disabled>
        <i class="fas fa-lock"></i> Abstract Creation Not Available
    </button>
<?php endif; ?>
```

## Benefits

1. **Clear Visual Feedback**: Users can immediately see which subtheme they have selected
2. **Early Warning System**: Users are warned if they haven't selected a subtheme
3. **Better UX**: Prevents confusion about topic vs subtheme selection
4. **Consistent Data**: Unified approach to handling subtheme data across the application
5. **Improved Debugging**: Enhanced logging for troubleshooting subtheme-related issues

## API Response Structure Expected

The API should return data in this format:
```json
{
    "participant_id": "32045",
    "selected_subtheme": {
        "id": "4708",
        "program_subtheme_id": "14",
        "participant_id": "32045",
        "is_active": "1",
        "is_deleted": "0",
        "created_at": "2024-11-13 00:59:08",
        "updated_at": "2024-11-13 00:59:08",
        "subtheme_name": "Sustainable Communities (SDG 11) — Eco-friendly urban spaces",
        "subtheme_description": "Description of the subtheme"
    },
    "eligible_for_abstract": true,
    "abstract": {
        // abstract data structure
    }
}
```
