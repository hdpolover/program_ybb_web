# Image Compression for Landing Pages - Timeout Issues Fixed

This document explains the image compression setup and recent fixes for timeout issues.

## 🚨 Recent Timeout Fix

**Issue**: Maximum execution time exceeded errors on image processing
**Solution**: Implemented safe mode and aggressive timeout prevention

## Current Status

### Safe Mode (ACTIVE)
- `SAFE_IMAGE_PROCESSING_ONLY = true` in Constants.php
- Only processes local/trusted images
- Bypasses external URLs that cause timeouts
- Prevents external API/storage server delays

### Emergency Options

#### Option 1: Complete Disable (Immediate relief)
```php
// In app/Config/Constants.php
define('DISABLE_IMAGE_PROCESSING', true);
```

#### Option 2: Safe Mode Only (Current setting)
```php
// In app/Config/Constants.php  
define('SAFE_IMAGE_PROCESSING_ONLY', true);
```

#### Option 3: Re-enable with better limits
```php
// Remove or comment out both constants to re-enable
// But use the improved timeout/size limits
```

## Compression Settings

### Default Settings
- **Quality**: 85% (moderate compression)
- **Max Size Before Compression**: 400KB
- **Timeout**: 10 seconds for downloads
- **Max File Size**: 10MB
- **Max Dimensions**: 4000x4000 pixels

### Image Type Presets

#### Hero/Banner Images (`compress_hero_image()`)
- **Size**: 1920x600px
- **Quality**: 82% (slightly lower for large images)
- **Max Size**: 600KB
- **Usage**: Main header banners, hero sections

#### Gallery Images (`compress_gallery_image()`)
- **Size**: 600x400px
- **Quality**: 85% (good balance)
- **Max Size**: 400KB
- **Usage**: Photo galleries, content images

#### Thumbnails (`compress_thumbnail()`)
- **Size**: 150x150px
- **Quality**: 88% (higher for small images)
- **Max Size**: 200KB
- **Usage**: Avatars, small preview images

#### Card Images (`compress_card_image()`)
- **Size**: 400x250px
- **Quality**: 85% (balanced)
- **Max Size**: 300KB
- **Usage**: Content cards, medium-sized images

## Benefits

1. **Moderate Compression**: Images maintain good visual quality while reducing file sizes
2. **Faster Loading**: Smaller file sizes improve page load times
3. **Responsive Design**: Different sizes for different use cases
4. **Automatic Caching**: Processed images are cached to avoid reprocessing
5. **Fallback Support**: Original images are served if compression fails

## Configuration

Settings can be adjusted in `app/Config/ImageCompression.php`:

```php
// Example: Adjust gallery image quality
$config->gallery['quality'] = 90; // Higher quality
$config->gallery['max_size'] = 500; // Larger file size allowed
```

## Usage Examples

```php
<!-- Hero image -->
<img src="<?= compress_hero_image($banner_url) ?>" alt="Banner">

<!-- Gallery image -->
<img src="<?= compress_gallery_image($photo_url) ?>" alt="Photo">

<!-- Thumbnail -->
<img src="<?= compress_thumbnail($avatar_url) ?>" alt="Avatar">

<!-- Card image -->
<img src="<?= compress_card_image($content_image) ?>" alt="Content">

<!-- Custom size -->
<img src="<?= compress_gallery_image($image, 800, 600) ?>" alt="Custom">
```

## Performance Features

- **10-second timeout** for image downloads
- **Size validation** before processing
- **Memory management** for large images
- **Error handling** with graceful fallbacks
- **Automatic cleanup** of resources

## Emergency Disable

If needed, you can disable image processing by adding this to `app/Config/Constants.php`:

```php
define('DISABLE_IMAGE_PROCESSING', true);
```

This will serve original images without any processing.