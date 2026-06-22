# GitHub Actions Auto-Deployment Setup

Your plugin is now configured for automatic deployment to WordPress.org using GitHub Actions!

## ✅ What's Been Set Up

1. **`.github/workflows/deploy.yml`** - GitHub Actions workflow
2. **`.wordpress-org/`** - Directory for plugin assets (banners, icons, screenshots)

## 🔧 Setup Steps

### Step 1: Add GitHub Secrets

Go to your GitHub repository and add your WordPress.org credentials:

1. Navigate to: **Settings** → **Secrets and variables** → **Actions**
2. Click **"New repository secret"**
3. Add these two secrets:

   **Secret 1:**
   - Name: `SVN_USERNAME`
   - Value: Your WordPress.org username

   **Secret 2:**
   - Name: `SVN_PASSWORD`
   - Value: Your WordPress.org password

⚠️ **Important:** Use your WordPress.org username (not email) and password.

### Step 2: Add Plugin Assets (Optional but Recommended)

Add visual assets to make your plugin stand out:

```bash
# Add these files to .wordpress-org/ directory:
.wordpress-org/
├── icon-128x128.png          # Required - shows in search results
├── icon-256x256.png          # Optional - retina version
├── banner-772x250.png        # Optional - header banner
├── banner-1544x500.png       # Optional - retina banner
└── screenshot-1.png          # Optional - screenshots
```

See `.wordpress-org/README.md` for detailed guidelines.

### Step 3: Commit & Push to GitHub

```bash
git add .github/ .wordpress-org/
git commit -m "Add GitHub Actions auto-deployment"
git push origin main
```

## 🚀 How to Deploy

### Every Future Release:

```bash
# 1. Update version numbers in your plugin files
# Edit class-cordy-pdf-generator.php: Version: 1.0.1
# Edit readme.txt: Stable tag: 1.0.1

# 2. Commit your changes
git add .
git commit -m "Release version 1.0.1"
git push origin main

# 3. Create and push a Git tag
git tag 1.0.1
git push origin 1.0.1

# 4. Create a GitHub Release
# Go to: https://github.com/YOUR-USERNAME/YOUR-REPO/releases/new
# - Choose the tag you just pushed (1.0.1)
# - Title: "Version 1.0.1"
# - Description: List your changes
# - Click "Publish release"
```

**That's it!** GitHub Actions will automatically:
- ✅ Build your plugin
- ✅ Deploy to WordPress.org SVN trunk
- ✅ Create the version tag
- ✅ Upload assets (banners, icons, screenshots)

## 📊 Monitor Deployment

After publishing a GitHub release:

1. Go to: **Actions** tab in your GitHub repo
2. Watch the "Deploy to WordPress.org" workflow
3. Green checkmark = successful deployment
4. Red X = something failed (check logs)

## 🔍 Verify Deployment

After deployment completes (usually 2-5 minutes):

```bash
# Check your plugin page (wait 5-15 min for cache)
https://wordpress.org/plugins/datasheet-pdf-generator-for-woocommerce/
```

## 🎯 First Deployment

For your first deployment after approval:

```bash
# 1. Ensure secrets are added to GitHub
# 2. Tag version 1.0.0
git tag 1.0.0
git push origin 1.0.0

# 3. Create GitHub release from tag 1.0.0
# Title: "Initial Release - Version 1.0.0"
# Description: "First public release of Datasheet PDF Generator"

# 4. Publish release → automatic deployment begins!
```

## 📝 Release Notes Template

When creating GitHub releases, use this template:

```markdown
## 🎉 Version 1.0.1

### ✨ New Features
- Feature 1
- Feature 2

### 🐛 Bug Fixes
- Fix 1
- Fix 2

### 🔧 Improvements
- Improvement 1
- Improvement 2

### 📚 Documentation
- Doc update 1
```

## 🆘 Troubleshooting

### Deployment Failed

**Check the Actions log:**
1. Go to **Actions** tab
2. Click on the failed workflow
3. Expand the failed step to see error details

**Common issues:**

- **"Authentication failed"**
  - Check your SVN_USERNAME and SVN_PASSWORD secrets
  - Ensure you're using username (not email) and correct password

- **"Build failed"**
  - Test build locally: `./build.sh`
  - Check for syntax errors or missing files

- **"SVN commit failed"**
  - First deployment? May need manual initial commit (see workflow guide)
  - Check SVN permissions at WordPress.org

### Deployment Succeeded but Changes Not Showing

- Wait 5-15 minutes for WordPress.org cache to clear
- Check trunk was updated: `svn co https://plugins.svn.wordpress.org/datasheet-pdf-generator-for-woocommerce/trunk /tmp/check`
- Verify "Stable tag" in readme.txt matches your release version

## 🔄 Update Only Assets (No New Version)

To update banners/icons without a new plugin version:

```bash
# 1. Update files in .wordpress-org/
# 2. Commit and push
git add .wordpress-org/
git commit -m "Update plugin assets"
git push origin main

# 3. Create a release with same version but mark as "pre-release"
# Or update assets manually via SVN (see GITHUB_TO_SVN_WORKFLOW.md)
```

## 📋 Quick Reference

| Action | Command |
|--------|---------|
| Create release | `git tag 1.0.1 && git push origin 1.0.1` then GitHub UI |
| Check workflow | Go to **Actions** tab in GitHub |
| View plugin page | `https://wordpress.org/plugins/datasheet-pdf-generator-for-woocommerce/` |
| Test build locally | `./build.sh` |
| Manual fallback | `./deploy-to-wporg.sh 1.0.1` |

## 🎓 Best Practices

✅ **DO:**
- Test plugin locally before releasing
- Update changelog in readme.txt
- Use semantic versioning (1.0.0, 1.0.1, 1.1.0, 2.0.0)
- Write clear release notes in GitHub
- Add plugin assets for better visibility

❌ **DON'T:**
- Skip version bumps in plugin files
- Deploy without testing
- Reuse version numbers
- Include development files in build

## 🔗 Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [10up Deploy Action](https://github.com/10up/action-wordpress-plugin-deploy)
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [Semantic Versioning](https://semver.org/)

---

**Need more control?** See `GITHUB_TO_SVN_WORKFLOW.md` for manual deployment options.

**Questions?** Check the WordPress.org support forums or the full workflow guide.
