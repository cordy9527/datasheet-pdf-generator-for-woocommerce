# Visual Assets for WordPress.org

## Overview

Visual assets (icons, banners, screenshots) are **NOT** included in your plugin ZIP file. They are added separately to WordPress.org via SVN after your plugin is approved.

## When to Add Assets

You have two options:

### Option 1: Submit without assets (Recommended for first submission)
- Submit your plugin ZIP without any visual assets
- WordPress.org will use default placeholders
- Add assets later via SVN after approval
- This gets your plugin approved faster

### Option 2: Add assets after approval
- Wait for plugin approval email
- Add assets via SVN to make your plugin look professional
- Update anytime without affecting plugin functionality

## Required Asset Sizes

### Plugin Icon (Square)
- **icon-256x256.png** (required) - 256×256 pixels
- **icon-128x128.png** (required) - 128×128 pixels  
- **icon.svg** (optional, recommended) - SVG format

### Plugin Banner (Wide)
- **banner-1544x500.png** (high-res) - 1544×500 pixels
- **banner-772x250.png** (low-res) - 772×250 pixels

### Screenshots
- **screenshot-1.png** - First screenshot (corresponds to "1." in readme.txt)
- **screenshot-2.png** - Second screenshot (corresponds to "2." in readme.txt)
- **screenshot-3.png** - etc.
- Recommended size: 1280×720 or larger
- Format: PNG or JPG

## How to Add Assets (After Approval)

### Step 1: Create Your Assets

Create the following images:

**Icon Ideas:**
- Use your Agent Cordy logo
- Simple, recognizable at small sizes
- Solid background color or transparent

**Banner Ideas:**
- Show the plugin in action
- Include plugin name/tagline
- Professional, clean design

**Screenshots:**
1. PDF Datasheet metabox in product editor
2. Settings page with branding options
3. Generated PDF example (show the actual PDF)
4. Product page with download tab
5. Live PDF preview window

### Step 2: Checkout SVN Assets Folder

After your plugin is approved, you'll receive an SVN URL. Then:

```bash
# Checkout only the assets folder
svn co https://plugins.svn.wordpress.org/datasheet-pdf-generator/assets

# Navigate to the folder
cd assets
```

### Step 3: Add Your Images

```bash
# Copy your assets
cp /path/to/icon-256x256.png .
cp /path/to/icon-128x128.png .
cp /path/to/banner-1544x500.png .
cp /path/to/banner-772x250.png .
cp /path/to/screenshot-1.png .
cp /path/to/screenshot-2.png .
cp /path/to/screenshot-3.png .
cp /path/to/screenshot-4.png .
cp /path/to/screenshot-5.png .

# Add to SVN
svn add *.png

# Commit (will appear on WordPress.org immediately)
svn ci -m "Add plugin assets"
```

### Step 4: Verify

Visit your plugin page to see the assets:
`https://wordpress.org/plugins/datasheet-pdf-generator/`

Assets appear immediately after SVN commit (no approval needed).

## Alternative: GitHub Action Method

You can also add assets via GitHub by creating a `.wordpress-org` folder in your repo:

```bash
# Create assets folder in your repo
mkdir -p .wordpress-org

# Add your assets
cp /path/to/icon-256x256.png .wordpress-org/
cp /path/to/banner-1544x500.png .wordpress-org/
# etc.

# Commit to GitHub
git add .wordpress-org
git commit -m "Add WordPress.org assets"
git push
```

The GitHub Action will automatically sync these to SVN when you push a tag.

## Asset Design Tips

### Icon
- Keep it simple and recognizable
- Use high contrast
- Avoid small text (won't be readable at 128px)
- Test at both sizes (256px and 128px)
- Consider using your Agent Cordy logo

### Banner
- Use your brand colors
- Include plugin name
- Show a key feature or benefit
- Keep text large and readable
- Avoid clutter

### Screenshots
- Use actual plugin screenshots (not mockups)
- Show real data/content
- Highlight key features
- Keep UI clean and professional
- Add captions in readme.txt to explain each screenshot

## Screenshot Captions in readme.txt

Your readme.txt already has this section:

```
== Screenshots ==

1. PDF Datasheet metabox in the product editor
2. Settings page with branding options
3. Generated PDF example with two-column layout
4. Product page download tab
5. Live PDF preview
```

Make sure your screenshot filenames match:
- screenshot-1.png → Caption 1
- screenshot-2.png → Caption 2
- etc.

## Tools for Creating Assets

### Icon
- Figma, Sketch, Adobe Illustrator
- Canva (free templates)
- Export at 256×256 and 128×128

### Banner
- Figma, Photoshop, Canva
- Export at 1544×500 and 772×250

### Screenshots
- Take screenshots of your plugin in action
- Use a clean WordPress install
- Crop to focus on relevant areas
- Optimize file size (use PNG compression)

## Important Notes

1. **Assets are optional for submission** - You can submit without them
2. **Assets don't affect approval** - They're purely visual
3. **Update anytime** - Change assets via SVN without plugin review
4. **No size limit** - Assets aren't counted in the 10MB plugin ZIP limit
5. **Instant updates** - Asset changes appear immediately on WordPress.org

## Recommended Workflow

1. ✅ Submit plugin ZIP without assets (get approved first)
2. ✅ While waiting for approval, create your assets
3. ✅ After approval, add assets via SVN
4. ✅ Update assets anytime to improve presentation

This approach gets your plugin live faster, then you can polish the visuals.

---

**Bottom Line:** Don't let assets delay your submission. Submit the plugin first, add assets later!
