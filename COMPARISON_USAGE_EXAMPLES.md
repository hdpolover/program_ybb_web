# Abstract Version Comparison - Usage Examples

## Basic Usage

### 1. Direct URL Access
```
http://yoursite.com/abstract-versions/compare/1/2
```
Where `1` and `2` are the version IDs you want to compare.

### 2. AJAX Request Example
```javascript
fetch('/abstract-versions/compare/1/2', {
    method: 'GET',
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Comparison data:', data.data.comparison);
        // Process the comparison data
        displayComparison(data.data.comparison);
    } else {
        console.error('Error:', data.message);
    }
})
.catch(error => {
    console.error('Request failed:', error);
});
```

### 3. Using jQuery
```javascript
$.ajax({
    url: '/abstract-versions/compare/1/2',
    method: 'GET',
    dataType: 'json',
    success: function(data) {
        if (data.success) {
            // Handle successful comparison
            console.log('Field comparisons:', data.data.comparison.field_comparisons);
        }
    },
    error: function(xhr, status, error) {
        console.error('Comparison failed:', error);
    }
});
```

## Interactive Features Usage

### 1. Initialize the JavaScript Module
```javascript
document.addEventListener('DOMContentLoaded', function() {
    // The module is automatically initialized
    window.abstractComparison = new AbstractVersionComparison();
});
```

### 2. Programmatic Field Filtering
```javascript
// Filter to show only changed fields
filterFields('changed');

// Show all fields
filterFields('all');

// Show only unchanged fields
filterFields('unchanged');
```

### 3. Expand/Collapse Operations
```javascript
// Expand all fields
document.getElementById('expandAll').click();

// Collapse all fields
document.getElementById('collapseAll').click();
```

### 4. Copy Content to Clipboard
```javascript
// The copy buttons are automatically bound, but you can also trigger programmatically
window.abstractComparison.copyToClipboard({
    target: document.querySelector('.copy-content[data-field="title"]')
});
```

### 5. Download Report
```javascript
// Download comparison as a report
window.abstractComparison.downloadReport();
```

### 6. Print Comparison
```javascript
// Print the comparison
window.abstractComparison.printComparison();
```

## Response Format Example

```json
{
  "success": true,
  "data": {
    "comparison": {
      "abstract_id": 123,
      "version1": {
        "id": 1,
        "version_number": 1,
        "data": {
          "title": "Original Title",
          "content": "Original abstract content...",
          "keywords": "keyword1, keyword2"
        },
        "metadata": {
          "created_at": "2024-01-01 10:00:00",
          "updated_at": "2024-01-01 10:00:00"
        }
      },
      "version2": {
        "id": 2,
        "version_number": 2,
        "data": {
          "title": "Updated Title",
          "content": "Updated abstract content...",
          "keywords": "keyword1, keyword2, keyword3"
        },
        "metadata": {
          "created_at": "2024-01-03 14:30:00",
          "updated_at": "2024-01-03 14:30:00"
        }
      },
      "field_comparisons": {
        "title": {
          "changed": true,
          "version1_content": "Original Title",
          "version2_content": "Updated Title",
          "version1_word_count": 2,
          "version2_word_count": 2,
          "version1_char_count": 14,
          "version2_char_count": 13
        },
        "content": {
          "changed": true,
          "version1_content": "Original abstract content...",
          "version2_content": "Updated abstract content...",
          "version1_word_count": 25,
          "version2_word_count": 28,
          "version1_char_count": 150,
          "version2_char_count": 165
        },
        "keywords": {
          "changed": true,
          "version1_content": "keyword1, keyword2",
          "version2_content": "keyword1, keyword2, keyword3",
          "version1_word_count": 2,
          "version2_word_count": 3,
          "version1_char_count": 18,
          "version2_char_count": 28
        }
      },
      "statistics": {
        "total_fields": 8,
        "changed_fields": 3,
        "unchanged_fields": 5,
        "time_difference": "2 days ago"
      }
    }
  }
}
```

## Error Handling Examples

### 1. Version Not Found
```json
{
  "success": false,
  "message": "Version not found",
  "code": "VERSION_NOT_FOUND"
}
```

### 2. Unauthorized Access
```json
{
  "success": false,
  "message": "You don't have permission to view this abstract",
  "code": "UNAUTHORIZED_ACCESS"
}
```

### 3. Invalid Parameters
```json
{
  "success": false,
  "message": "Invalid version parameters",
  "code": "INVALID_PARAMETERS"
}
```

## Integration Examples

### 1. Add Compare Button to Version List
```html
<div class="version-item">
    <span>Version 2</span>
    <div class="actions">
        <a href="/abstract-versions/compare/1/2" class="btn btn-sm btn-outline-primary">
            Compare with v1
        </a>
    </div>
</div>
```

### 2. Dynamic Version Selector
```javascript
function loadVersionComparison(baseVersionId) {
    const versionSelect = document.getElementById('compareVersionSelect');
    const selectedVersionId = versionSelect.value;
    
    if (selectedVersionId && selectedVersionId !== baseVersionId) {
        window.location.href = `/abstract-versions/compare/${baseVersionId}/${selectedVersionId}`;
    }
}
```

### 3. Modal Comparison (AJAX)
```javascript
function showComparisonModal(version1Id, version2Id) {
    $('#comparisonModal').modal('show');
    $('#comparisonModal .modal-body').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    
    fetch(`/abstract-versions/compare/${version1Id}/${version2Id}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderComparisonInModal(data.data.comparison);
        } else {
            $('#comparisonModal .modal-body').html(`<div class="alert alert-danger">${data.message}</div>`);
        }
    });
}
```

## Keyboard Shortcuts

When viewing the comparison page, these keyboard shortcuts are available:

- **Ctrl + F**: Search/filter fields
- **Ctrl + P**: Print comparison
- **Ctrl + E**: Expand all fields
- **Ctrl + Shift + C**: Collapse all fields

## Browser Compatibility

- Chrome 70+
- Firefox 65+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance Tips

1. **Large Content**: For very large abstracts, the comparison loads progressively
2. **Caching**: Repeated comparisons use client-side caching where possible
3. **Mobile**: On mobile devices, the layout automatically adjusts for better readability

## Troubleshooting

### Common Issues:

1. **Comparison not loading**: Check that both version IDs exist and belong to the same abstract
2. **JavaScript errors**: Ensure the abstract-comparison.js file is loaded correctly
3. **Layout issues**: Verify that Bootstrap CSS is loaded
4. **Permission errors**: Ensure the user is logged in and owns the abstract being compared

### Debug Mode:
Add `?debug=1` to the URL to enable detailed error reporting (development only).
