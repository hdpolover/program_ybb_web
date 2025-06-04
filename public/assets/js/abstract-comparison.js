/**
 * Abstract Version Comparison JavaScript Module
 * Provides enhanced functionality for comparing abstract versions
 */

class AbstractVersionComparison {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeComponents();
        this.highlightChanges();
        console.log('Abstract Version Comparison module initialized');
    }

    bindEvents() {
        // Bind field header clicks for collapsible content
        document.querySelectorAll('.field-header').forEach(header => {
            header.addEventListener('click', this.toggleFieldContent.bind(this));
        });

        // Bind copy to clipboard functionality
        document.querySelectorAll('.copy-content, .copy-btn').forEach(button => {
            button.addEventListener('click', this.copyToClipboard.bind(this));
        });

        // Bind version switching
        document.querySelectorAll('.version-selector').forEach(selector => {
            selector.addEventListener('change', this.switchVersion.bind(this));
        });

        // Bind view filter functionality
        document.querySelectorAll('input[name="viewFilter"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.filterFields(e.target.value);
            });
        });

        // Bind expand/collapse all functionality
        const expandAllBtn = document.getElementById('expandAll');
        const collapseAllBtn = document.getElementById('collapseAll');
        
        if (expandAllBtn) {
            expandAllBtn.addEventListener('click', this.expandAllFields.bind(this));
        }
        
        if (collapseAllBtn) {
            collapseAllBtn.addEventListener('click', this.collapseAllFields.bind(this));
        }

        // Bind field search functionality
        const fieldSearch = document.getElementById('fieldSearch');
        if (fieldSearch) {
            fieldSearch.addEventListener('input', this.searchFields.bind(this));
        }

        // Bind download comparison report
        const downloadBtn = document.getElementById('downloadReport');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', this.downloadReport.bind(this));
        }

        // Bind print functionality
        const printBtn = document.getElementById('printComparison');
        if (printBtn) {
            printBtn.addEventListener('click', this.printComparison.bind(this));
        }

        // Bind share functionality
        const shareBtn = document.getElementById('shareComparison');
        if (shareBtn) {
            shareBtn.addEventListener('click', this.shareComparison.bind(this));
        }
    }

    initializeComponents() {
        // Initialize tooltips
        this.initTooltips();
        
        // Setup search functionality
        this.setupSearch();
        
        // Setup statistics animation
        this.animateStatistics();
        
        // Setup responsive table handling
        this.setupResponsiveTables();
    }

    initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    setupSearch() {
        const searchInput = document.getElementById('fieldSearch');
        if (searchInput) {
            searchInput.addEventListener('input', this.searchFields.bind(this));
        }
    }

    searchFields(event) {
        const searchTerm = event.target.value.toLowerCase();
        const fields = document.querySelectorAll('.comparison-field');

        fields.forEach(field => {
            const fieldLabel = field.querySelector('.field-header span').textContent.toLowerCase();
            const fieldContent = field.querySelector('.field-content').textContent.toLowerCase();
            
            if (fieldLabel.includes(searchTerm) || fieldContent.includes(searchTerm)) {
                field.style.display = 'block';
                this.highlightSearchTerm(field, searchTerm);
            } else {
                field.style.display = 'none';
            }
        });
    }

    highlightSearchTerm(field, searchTerm) {
        if (!searchTerm) return;
        
        const content = field.querySelector('.field-content');
        const originalText = content.getAttribute('data-original-text') || content.innerHTML;
        
        if (!content.getAttribute('data-original-text')) {
            content.setAttribute('data-original-text', originalText);
        }
        
        const highlightedText = originalText.replace(
            new RegExp(`(${this.escapeRegExp(searchTerm)})`, 'gi'),
            '<mark>$1</mark>'
        );
        
        content.innerHTML = highlightedText;
    }

    escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    animateStatistics() {
        const statNumbers = document.querySelectorAll('.stat-number');
        statNumbers.forEach(stat => {
            this.animateNumber(stat);
        });
    }

    animateNumber(element) {
        const target = parseInt(element.textContent) || 0;
        const duration = 1000;
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    setupResponsiveTables() {
        // Handle responsive comparison tables
        const versionComparisons = document.querySelectorAll('.version-comparison');
        
        const handleResize = () => {
            versionComparisons.forEach(comparison => {
                if (window.innerWidth < 768) {
                    comparison.classList.add('mobile-layout');
                } else {
                    comparison.classList.remove('mobile-layout');
                }
            });
        };

        window.addEventListener('resize', handleResize);
        handleResize(); // Initial call
    }

    toggleFieldContent(event) {
        const header = event.currentTarget;
        const content = header.nextElementSibling;
        const indicator = header.querySelector('.change-indicator');
        
        if (content.classList.contains('collapsed')) {
            content.classList.remove('collapsed');
            content.style.display = 'block';
            header.setAttribute('aria-expanded', 'true');
            indicator.style.opacity = '1';
        } else {
            content.classList.add('collapsed');
            content.style.display = 'none';
            header.setAttribute('aria-expanded', 'false');
            indicator.style.opacity = '0.7';
        }
    }

    highlightChanges() {
        // Enhanced highlighting for changed fields
        document.querySelectorAll('.change-indicator.changed').forEach(indicator => {
            const field = indicator.closest('.comparison-field');
            field.style.borderLeftColor = '#ffc107';
            field.style.borderLeftWidth = '4px';
            field.classList.add('has-changes');
        });

        // Add visual emphasis to word count changes
        document.querySelectorAll('.word-count-change').forEach(element => {
            const changeValue = parseInt(element.textContent.match(/\(([+-]?\d+)\)/)?.[1]);
            if (changeValue > 0) {
                element.classList.add('positive-change');
            } else if (changeValue < 0) {
                element.classList.add('negative-change');
            }
        });
    }

    copyToClipboard(event) {
        const button = event.currentTarget;
        const content = button.getAttribute('data-content');
        
        navigator.clipboard.writeText(content).then(() => {
            this.showNotification('Content copied to clipboard!', 'success');
            button.classList.add('copied');
            setTimeout(() => {
                button.classList.remove('copied');
            }, 2000);
        }).catch(err => {
            this.showNotification('Failed to copy content', 'error');
            console.error('Failed to copy: ', err);
        });
    }

    switchVersion(event) {
        const selector = event.currentTarget;
        const newVersionId = selector.value;
        const currentUrl = window.location.pathname;
        const urlParts = currentUrl.split('/');
        
        // Assuming URL structure: /abstract-paper/compare/version1/version2
        if (selector.classList.contains('version1-selector')) {
            urlParts[urlParts.length - 2] = newVersionId;
        } else {
            urlParts[urlParts.length - 1] = newVersionId;
        }
        
        const newUrl = urlParts.join('/');
        window.location.href = newUrl;
    }

    downloadReport() {
        this.showNotification('Preparing comparison report...', 'info');
        
        // Generate a comprehensive report
        const reportData = this.generateReportData();
        const reportHtml = this.generateReportHtml(reportData);
        
        // Create and download the report
        const blob = new Blob([reportHtml], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `abstract-comparison-${new Date().toISOString().split('T')[0]}.html`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        this.showNotification('Report downloaded successfully!', 'success');
    }

    printComparison() {
        // Create a print-friendly version
        const printWindow = window.open('', '_blank');
        const printContent = this.generatePrintContent();
        
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }

    generateReportData() {
        const data = {
            timestamp: new Date().toISOString(),
            versions: {
                version1: this.extractVersionData('.version-1'),
                version2: this.extractVersionData('.version-2')
            },
            statistics: this.extractStatistics(),
            changes: this.extractChanges()
        };
        
        return data;
    }

    extractVersionData(selector) {
        const versionElement = document.querySelector(selector);
        if (!versionElement) return null;
        
        return {
            number: versionElement.querySelector('h6')?.textContent || '',
            created: versionElement.querySelector('.text-muted')?.textContent || '',
            content: versionElement.querySelector('.content-preview')?.textContent || ''
        };
    }

    extractStatistics() {
        const stats = {};
        document.querySelectorAll('.stat-item').forEach(item => {
            const number = item.querySelector('.stat-number')?.textContent || '';
            const label = item.querySelector('.stat-label')?.textContent || '';
            stats[label] = number;
        });
        return stats;
    }

    extractChanges() {
        const changes = [];
        document.querySelectorAll('.comparison-field').forEach(field => {
            const label = field.querySelector('.field-header span')?.textContent || '';
            const hasChange = field.querySelector('.change-indicator.changed') !== null;
            changes.push({ field: label, hasChange });
        });
        return changes;
    }

    generateReportHtml(data) {
        return `
<!DOCTYPE html>
<html>
<head>
    <title>Abstract Version Comparison Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .section { margin-bottom: 30px; }
        .version-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .changes-list { list-style-type: none; padding: 0; }
        .changes-list li { padding: 5px 0; border-bottom: 1px solid #eee; }
        .changed { color: #856404; font-weight: bold; }
        .unchanged { color: #004085; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Abstract Version Comparison Report</h1>
        <p>Generated on: ${new Date(data.timestamp).toLocaleString()}</p>
    </div>
    
    <div class="section">
        <h2>Statistics</h2>
        ${Object.entries(data.statistics).map(([key, value]) => `<p><strong>${key}:</strong> ${value}</p>`).join('')}
    </div>
    
    <div class="section">
        <h2>Changes Summary</h2>
        <ul class="changes-list">
            ${data.changes.map(change => `<li class="${change.hasChange ? 'changed' : 'unchanged'}">${change.field}: ${change.hasChange ? 'Changed' : 'Unchanged'}</li>`).join('')}
        </ul>
    </div>
</body>
</html>`;
    }

    generatePrintContent() {
        const originalContent = document.querySelector('.container-fluid').innerHTML;
        return `
<!DOCTYPE html>
<html>
<head>
    <title>Abstract Version Comparison</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .no-print { display: none !important; }
        .comparison-field { page-break-inside: avoid; margin-bottom: 20px; }
        .version-comparison { display: flex; gap: 20px; }
        .version-column { flex: 1; }
        @media print {
            .back-button, .btn, button { display: none !important; }
        }
    </style>
</head>
<body>
    ${originalContent}
</body>
</html>`;
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }

    // Utility method to highlight differences in text
    highlightDifferences(text1, text2) {
        // Simple word-based diff highlighting
        const words1 = text1.split(' ');
        const words2 = text2.split(' ');
        
        // Basic implementation - could be enhanced with proper diff algorithm
        const highlighted1 = words1.map(word => {
            return words2.includes(word) ? word : `<span class="removed">${word}</span>`;
        }).join(' ');
        
        const highlighted2 = words2.map(word => {
            return words1.includes(word) ? word : `<span class="added">${word}</span>`;
        }).join(' ');
        
        return { highlighted1, highlighted2 };
    }

    // Method to export comparison data as JSON
    exportAsJson() {
        const data = this.generateReportData();
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `abstract-comparison-${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    // Filter fields based on change status
    filterFields(filterType) {
        const fields = document.querySelectorAll('.comparison-field');
        
        fields.forEach(field => {
            const hasChange = field.querySelector('.badge.bg-warning') !== null;
            
            switch (filterType) {
                case 'all':
                    field.style.display = 'block';
                    break;
                case 'changed':
                    field.style.display = hasChange ? 'block' : 'none';
                    break;
                case 'unchanged':
                    field.style.display = hasChange ? 'none' : 'block';
                    break;
            }
        });
        
        this.updateVisibleFieldCount();
    }
    
    // Expand all field contents
    expandAllFields() {
        document.querySelectorAll('.field-content.collapsed').forEach(content => {
            content.classList.remove('collapsed');
            content.style.display = 'block';
            const header = content.previousElementSibling;
            if (header) {
                header.setAttribute('aria-expanded', 'true');
            }
        });
    }
    
    // Collapse all field contents
    collapseAllFields() {
        document.querySelectorAll('.field-content').forEach(content => {
            if (!content.classList.contains('collapsed')) {
                content.classList.add('collapsed');
                content.style.display = 'none';
                const header = content.previousElementSibling;
                if (header) {
                    header.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }
    
    // Search through fields
    searchFields(event) {
        const searchTerm = event.target.value.toLowerCase();
        const fields = document.querySelectorAll('.comparison-field');
        
        fields.forEach(field => {
            const fieldText = field.textContent.toLowerCase();
            field.style.display = fieldText.includes(searchTerm) ? 'block' : 'none';
        });
        
        this.updateVisibleFieldCount();
    }
    
    // Update visible field count
    updateVisibleFieldCount() {
        const visibleFields = document.querySelectorAll('.comparison-field[style*="block"], .comparison-field:not([style*="none"])').length;
        const totalFields = document.querySelectorAll('.comparison-field').length;
        
        // Create or update field count indicator
        let countIndicator = document.getElementById('fieldCount');
        if (!countIndicator) {
            countIndicator = document.createElement('span');
            countIndicator.id = 'fieldCount';
            countIndicator.className = 'text-muted ms-2';
            const cardTitle = document.querySelector('.card-title');
            if (cardTitle) {
                cardTitle.appendChild(countIndicator);
            }
        }
        
        countIndicator.textContent = ` (${visibleFields} of ${totalFields} fields shown)`;
    }
    
    // Share comparison functionality
    shareComparison() {
        if (navigator.share) {
            navigator.share({
                title: 'Abstract Version Comparison',
                text: 'Check out this abstract version comparison',
                url: window.location.href
            }).then(() => {
                this.showNotification('Comparison shared successfully!', 'success');
            }).catch(err => {
                console.log('Error sharing:', err);
                this.copyUrlToClipboard();
            });
        } else {
            this.copyUrlToClipboard();
        }
    }
    
    // Copy URL to clipboard
    copyUrlToClipboard() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            this.showNotification('Comparison URL copied to clipboard!', 'success');
        }).catch(err => {
            this.showNotification('Failed to copy URL', 'error');
        });
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.abstractComparison = new AbstractVersionComparison();
});

// Global utility functions
window.AbstractVersionComparison = AbstractVersionComparison;
