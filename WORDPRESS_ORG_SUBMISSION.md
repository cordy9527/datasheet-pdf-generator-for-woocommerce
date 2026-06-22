# WordPress.org Submission Checklist

This document outlines the steps to submit your plugin to WordPress.org and set up automated deployment.

## Pre-Submission Checklist

### ✅ Required Files
- [x] `readme.txt` - WordPress.org format (created)
- [x] `datasheet-pdf-generator.php` - Main plugin file with proper headers
- [x] `.distignore` - Excludes dev files from deployment
- [x] License file (GPL2 declared in headers)
- [x] All dependencies bundled in `vendor/` folder

### ✅ Plugin Requirements

#### Code Quality
- [x] No PHP errors or warnings
- [x] Follows WordPress Coding Standards
- [x] All user input is sanitized
- [x] All output is escaped
- [x] Uses WordPress APIs (no direct database queries without $wpdb)
- [x] Nonces used for AJAX requests
- [x] Capability checks (`current_user_can()`)

#### Functionality
- [x] No "phone home" code (no unauthorized external requests)
- [x] No obfuscated code
- [x] No tracking without user consent
- [x] GPL-compatible license (GPL2)
- [x] Proper text domain for translations (`datasheet-pdf-generator`)
- [x] No hardcoded links to your site (except in readme.txt)

#### Assets
- [ ] Plugin icon (256x256 and 128x128) - **TODO: Create these**
- [ ] Plugin banner (1544x500 and 772x250) - **TODO: Create these**
- [ ] Screenshots (at least 3-5 recommended) - **TODO: Create these**

### ✅ Version Numbers
- [x] Version in `datasheet-pdf-generator.php` matches `readme.txt` (1.0.0)
- [x] Stable tag in `readme.txt` set to 1.0.0

## WordPress.org Submission Process

### Step 1: Create WordPress.org Account
1. Go to https://wordpress.org/support/register.php
2. Create an account (if you don't have one)
3. Verify your email address

### Step 2: Submit Plugin
1. Go to https://wordpress.org/plugins/developers/add/
2. Upload a ZIP file of your plugin (use `./build.sh free` to create it)
3. Fill out the submission form
4. Wait for review (typically 2-14 days)

### Step 3: After Approval
You'll receive an email with:
- Your SVN repository URL: `https://plugins.svn.wordpress.org/datasheet-pdf-generator/`
- Instructions for initial commit

## GitHub Actions Auto-Deploy Setup

### Step 1: Get WordPress.org SVN Credentials
After your plugin is approved, you'll have SVN access. Your credentials are:
- **Username**: Your WordPress.org username
- **Password**: Your WordPress.org password (or generate an app password)

### Step 2: Add GitHub Secrets
1. Go to your GitHub repository
2. Navigate to **Settings → Secrets and variables → Actions**
3. Click **New repository secret**
4. Add these two secrets:
   - Name: `SVN_USERNAME`, Value: Your WordPress.org username
   - Name: `SVN_PASSWORD`, Value: Your WordPress.org password

### Step 3: How Auto-Deploy Works
The GitHub Action (`.github/workflows/deploy.yml`) will:
1. Trigger when you push a git tag (e.g., `1.0.0`, `1.0.1`)
2. Install Composer dependencies
3. Deploy to WordPress.org SVN automatically
4. Create a GitHub release with the plugin ZIP

### Step 4: Creating a Release
When you're ready to release a new version:

```bash
# 1. Update version numbers
# - datasheet-pdf-generator.php (Version: 1.0.1)
# - readme.txt (Stable tag: 1.0.1)
# - Add changelog entry in readme.txt

# 2. Commit changes
git add .
git commit -m "Release version 1.0.1"
git push

# 3. Create and push tag
git tag 1.0.1
git push origin 1.0.1

# 4. GitHub Actions will automatically:
#    - Deploy to WordPress.org
#    - Create GitHub release with ZIP file
```

## Assets for WordPress.org

After your plugin is approved, you can add visual assets via SVN:

### Required Assets
Create these images and add them to the `/assets` folder in SVN:

1. **Icon** (PNG, square)
   - `icon-256x256.png` (required)
   - `icon-128x128.png` (required)
   - `icon.svg` (optional, recommended)

2. **Banner** (JPG or PNG)
   - `banner-1544x500.png` (high-res, required)
   - `banner-772x250.png` (low-res, required)

3. **Screenshots** (PNG or JPG)
   - `screenshot-1.png` - Corresponds to "1." in readme.txt Screenshots section
   - `screenshot-2.png` - Corresponds to "2." in readme.txt Screenshots section
   - `screenshot-3.png` - etc.

### Adding Assets via SVN
```bash
# Checkout the assets folder
svn co https://plugins.svn.wordpress.org/datasheet-pdf-generator/assets

# Add your images
cd assets
cp /path/to/icon-256x256.png .
cp /path/to/banner-1544x500.png .
cp /path/to/screenshot-1.png .

# Commit
svn add *.png
svn ci -m "Add plugin assets"
```

Or use the GitHub Action by creating an `/.wordpress-org` folder in your repo with the assets.

## Testing Before Submission

### Local Testing
1. Install on a fresh WordPress site
2. Test with WooCommerce active
3. Test all features:
   - PDF generation
   - PDF preview
   - Settings page
   - Product tab visibility
   - Variable products

### Build Test
```bash
# Run composer install
composer install --no-dev --optimize-autoloader

# Build the free version
./build.sh free

# Test the ZIP
# - Extract it
# - Install on a test site
# - Verify all features work
# - Check for any missing files
```

### Code Validation
```bash
# Check for PHP errors (if you have PHP_CodeSniffer)
phpcs --standard=WordPress datasheet-pdf-generator.php includes/

# Validate readme.txt
# Use: https://wordpress.org/plugins/developers/readme-validator/
```

## Common Rejection Reasons (Avoid These)

❌ **Calling external services without user consent**
✅ Your plugin doesn't make external requests

❌ **Including non-GPL libraries**
✅ mPDF is LGPL (compatible with GPL)

❌ **Obfuscated or encoded code**
✅ All code is readable

❌ **Trademark violations in plugin name**
✅ "Datasheet PDF Generator" is generic

❌ **Incomplete or missing readme.txt**
✅ readme.txt is complete with all sections

❌ **Security issues (XSS, SQL injection, CSRF)**
✅ All inputs sanitized, outputs escaped, nonces used

## Post-Approval Maintenance

### Updating the Plugin
1. Make your changes
2. Update version numbers (plugin file + readme.txt)
3. Add changelog entry in readme.txt
4. Commit and push
5. Create and push a new tag
6. GitHub Actions deploys automatically

### Support
- Monitor the WordPress.org support forum for your plugin
- Respond to user questions within a reasonable time
- Mark resolved threads as resolved

### Stats
- View download stats at: `https://wordpress.org/plugins/datasheet-pdf-generator/advanced/`

## Quick Reference

### Important URLs
- Plugin submission: https://wordpress.org/plugins/developers/add/
- Readme validator: https://wordpress.org/plugins/developers/readme-validator/
- Plugin handbook: https://developer.wordpress.org/plugins/
- SVN repository (after approval): https://plugins.svn.wordpress.org/datasheet-pdf-generator/

### Version Checklist for Each Release
- [ ] Update version in `datasheet-pdf-generator.php`
- [ ] Update stable tag in `readme.txt`
- [ ] Add changelog entry in `readme.txt`
- [ ] Test locally
- [ ] Commit changes
- [ ] Create git tag
- [ ] Push tag (triggers auto-deploy)

## Need Help?

- WordPress Plugin Handbook: https://developer.wordpress.org/plugins/
- WordPress.org Support: https://wordpress.org/support/forum/plugins-and-hacks/
- GitHub Actions Docs: https://docs.github.com/en/actions
