# WordPress.org Assets

This directory contains visual assets for the WordPress.org plugin listing page.

## Files

Place your plugin assets here:

- `banner-772x250.png` - Plugin header banner (required for featured plugins)
- `banner-1544x500.png` - High-resolution banner (2x)
- `icon-128x128.png` - Plugin icon (required)
- `icon-256x256.png` - High-resolution icon (2x)
- `screenshot-1.png` - First screenshot
- `screenshot-2.png` - Second screenshot
- `screenshot-3.png` - Additional screenshots...

## Guidelines

### Banner
- Size: 772×250px (or 1544×500px for retina)
- Format: PNG or JPG
- Shows at the top of your plugin page
- Optional but highly recommended

### Icon
- Size: 128×128px (or 256×256px for retina)
- Format: PNG or JPG
- Shows in plugin search results
- Required for better visibility

### Screenshots
- Format: PNG or JPG
- Dimensions: ideally 1280×800px or larger
- Named sequentially: screenshot-1.png, screenshot-2.png, etc.
- Correspond to the screenshot descriptions in readme.txt

## Notes

- These files are deployed to the SVN `assets/` directory
- They do NOT go inside the plugin ZIP file
- Update anytime by pushing to GitHub and creating a new release
- Can also be updated manually via SVN without creating a new plugin version

## Example Structure

```
.wordpress-org/
├── banner-772x250.png
├── banner-1544x500.png
├── icon-128x128.png
├── icon-256x256.png
├── screenshot-1.png
└── screenshot-2.png
```
