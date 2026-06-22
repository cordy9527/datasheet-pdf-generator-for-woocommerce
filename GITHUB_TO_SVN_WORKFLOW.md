# GitHub to WordPress.org SVN Deployment Guide

This guide explains how to manage your plugin development on GitHub while deploying to WordPress.org's SVN repository.

## Overview

- **Development**: GitHub (Git)
- **Distribution**: WordPress.org (SVN)
- **Strategy**: Develop on GitHub, deploy releases to SVN

---

## Initial Setup

### 1. Get SVN Access

After WordPress.org approval, you'll receive SVN access:
```
https://plugins.svn.wordpress.org/datasheet-pdf-generator-for-woocommerce/
```

### 2. Test SVN Access

```bash
svn co https://plugins.svn.wordpress.org/datasheet-pdf-generator-for-woocommerce/ /tmp/test-svn
# Enter your WordPress.org credentials when prompted
```

### 3. WordPress.org Asset Directory (Optional)

Create a `.wordpress-org` folder in your GitHub repo for plugin assets:

```
.wordpress-org/
├── banner-772x250.png      # Plugin header banner
├── banner-1544x500.png     # High-res banner
├── icon-128x128.png        # Plugin icon
├── icon-256x256.png        # High-res icon
└── screenshot-1.png        # Screenshots (screenshot-1.png, screenshot-2.png, etc.)
```

These will be copied to the `assets` folder in SVN.

---

## Deployment Methods

### Method 1: Automated with GitHub Actions (Recommended)

#### Setup

1. Create workflow file:

```bash
mkdir -p .github/workflows
```

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to WordPress.org

on:
  release:
    types: [published]

jobs:
  deploy:
    name: Deploy to WordPress.org
    runs-on: ubuntu-latest
    steps:
      - name: Checkout code
        uses: actions/checkout@v3

      - name: Build plugin
        run: |
          chmod +x build.sh
          ./build.sh

      - name: WordPress Plugin Deploy
        uses: 10up/action-wordpress-plugin-deploy@stable
        env:
          SVN_USERNAME: ${{ secrets.SVN_USERNAME }}
          SVN_PASSWORD: ${{ secrets.SVN_PASSWORD }}
          SLUG: datasheet-pdf-generator-for-woocommerce
          BUILD_DIR: dist/datasheet-pdf-generator-for-woocommerce
          ASSETS_DIR: .wordpress-org
```

2. Add secrets to GitHub:
   - Go to: **Settings** → **Secrets and variables** → **Actions**
   - Add: `SVN_USERNAME` (your WordPress.org username)
   - Add: `SVN_PASSWORD` (your WordPress.org password)

3. Deploy by creating a GitHub release:
   ```bash
   git tag 1.0.0
   git push origin 1.0.0
   ```
   Then create a release on GitHub from that tag → automatic deployment!

**Pros:**
- ✅ Fully automated
- ✅ No manual SVN interaction
- ✅ Deploy from any machine via GitHub UI

**Cons:**
- ❌ Less control over process
- ❌ Requires GitHub secrets setup

---

### Method 2: Manual Script (Included)

Use the included `deploy-to-wporg.sh` script for manual deployment with full control.

#### Usage

```bash
# Deploy current version (auto-detected from plugin file)
./deploy-to-wporg.sh

# Or specify version manually
./deploy-to-wporg.sh 1.0.0
```

#### What it does:

1. ✅ Runs `build.sh` to create ZIP
2. ✅ Checks out SVN repository
3. ✅ Clears trunk
4. ✅ Copies built plugin to trunk
5. ✅ Copies assets (screenshots, banners)
6. ✅ Shows you changes before committing
7. ✅ Commits to trunk
8. ✅ Prompts to create version tag

**Pros:**
- ✅ Full control over each step
- ✅ Review changes before committing
- ✅ No third-party dependencies

**Cons:**
- ❌ Manual process
- ❌ Requires SVN installed locally

---

## SVN Repository Structure

WordPress.org SVN has this structure:

```
datasheet-pdf-generator-for-woocommerce/
├── trunk/                  # Development version (what users see)
│   ├── class-cordy-pdf-generator.php
│   ├── readme.txt
│   └── ...
├── tags/                   # Released versions
│   ├── 1.0.0/
│   ├── 1.0.1/
│   └── 1.1.0/
└── assets/                 # Screenshots, banners, icons
    ├── banner-772x250.png
    ├── icon-128x128.png
    └── screenshot-1.png
```

### Important Notes:

- **trunk** = what users download and see on WordPress.org
- **tags** = archived versions (for rollback)
- **assets** = visual assets (don't go in plugin ZIP)

---

## Recommended Workflow

### Development Cycle

```bash
# 1. Develop on GitHub
git checkout -b feature/new-feature
# ... make changes ...
git commit -m "Add new feature"
git push origin feature/new-feature

# 2. Merge to main
git checkout main
git merge feature/new-feature
git push origin main

# 3. Update version number
# Edit class-cordy-pdf-generator.php (Version: 1.0.1)
# Edit readme.txt (Stable tag: 1.0.1)
git commit -am "Bump version to 1.0.1"
git push origin main

# 4. Deploy to WordPress.org
./deploy-to-wporg.sh 1.0.1

# Or with GitHub Actions:
git tag 1.0.1
git push origin 1.0.1
# Then create GitHub release
```

---

## Version Numbering

Follow [Semantic Versioning](https://semver.org/):

- **Major** (1.0.0 → 2.0.0): Breaking changes
- **Minor** (1.0.0 → 1.1.0): New features, backward-compatible
- **Patch** (1.0.0 → 1.0.1): Bug fixes

### Files to update:

1. `class-cordy-pdf-generator.php`:
   ```php
   * Version: 1.0.1
   ```

2. `readme.txt`:
   ```
   Stable tag: 1.0.1
   ```

3. `CHANGELOG.md` (if you have one)

---

## Common Tasks

### Update Plugin Without New Version

If you need to fix `readme.txt` or assets without a new version:

```bash
# Just commit to trunk
svn co https://plugins.svn.wordpress.org/datasheet-pdf-generator-for-woocommerce/trunk /tmp/plugin-trunk
cd /tmp/plugin-trunk
# ... edit readme.txt ...
svn ci -m "Update readme"
```

### Update Assets Only

```bash
svn co https://plugins.svn.wordpress.org/datasheet-pdf-generator-for-woocommerce/assets /tmp/plugin-assets
cd /tmp/plugin-assets
# ... add new screenshots ...
svn add screenshot-4.png
svn ci -m "Add screenshot 4"
```

### Rollback a Version

If a release has critical bugs:

1. Go to WordPress.org dashboard
2. Change "Stable tag" in trunk's `readme.txt` to previous version
3. Or use SVN:
   ```bash
   svn co https://plugins.svn.wordpress.org/datasheet-pdf-generator-for-woocommerce/trunk /tmp/plugin-trunk
   cd /tmp/plugin-trunk
   # Edit readme.txt: Stable tag: 1.0.0
   svn ci -m "Rollback to 1.0.0 due to critical bug"
   ```

---

## Best Practices

### ✅ DO:

- Keep GitHub as source of truth
- Test locally before deploying
- Use semantic versioning
- Update `readme.txt` changelog
- Create tags for each release
- Keep `.wordpress-org` assets updated
- Test on fresh WordPress install before releasing

### ❌ DON'T:

- Edit SVN directly (use GitHub → SVN flow)
- Forget to update version numbers
- Skip testing before deployment
- Commit directly to trunk without building
- Include development files (.git, node_modules) in SVN

---

## Troubleshooting

### "Authentication failed"

Check your WordPress.org credentials. SVN uses your WordPress.org username and password (not email).

### "File size too large"

WordPress.org has a 10MB limit per file. You're good with 3.4MB!

### "Missing readme.txt"

Ensure `readme.txt` exists and follows [WordPress standards](https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/).

### Changes not showing on WordPress.org

- Wait 5-15 minutes for cache
- Check you committed to `trunk` (not just tags)
- Verify `Stable tag` in readme.txt matches your tag

---

## First Deployment

After approval, for your very first deployment:

```bash
# 1. Checkout empty SVN repo
svn co https://plugins.svn.wordpress.org/datasheet-pdf-generator-for-woocommerce/ /tmp/first-deploy
cd /tmp/first-deploy

# 2. Unzip your built plugin to trunk
mkdir -p trunk
unzip ~/path/to/datasheet-pdf-generator.1.0.0.zip -d trunk/

# 3. Add assets if you have them
mkdir -p assets
cp .wordpress-org/* assets/ 2>/dev/null || true

# 4. Add all files
svn add trunk/* assets/* --force

# 5. Commit
svn ci -m "Initial release 1.0.0"

# 6. Create first tag
svn cp ^/trunk ^/tags/1.0.0 -m "Tagging version 1.0.0"
```

After this initial deployment, use the automated methods above for future updates.

---

## Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [SVN Best Practices](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/)
- [10up GitHub Action](https://github.com/10up/action-wordpress-plugin-deploy)
- [Semantic Versioning](https://semver.org/)

---

## Quick Reference

| Task | Command |
|------|---------|
| Deploy release | `./deploy-to-wporg.sh` |
| Check SVN status | `svn co https://plugins.svn.wordpress.org/[slug]/ /tmp/check && cd /tmp/check && svn stat` |
| Update readme only | Edit in SVN trunk, `svn ci -m "Update readme"` |
| Rollback version | Edit `Stable tag` in trunk's readme.txt |
| View plugin page | `https://wordpress.org/plugins/datasheet-pdf-generator-for-woocommerce/` |

---

## Support

If you encounter issues:
1. Check [WordPress.org support forums](https://wordpress.org/support/plugin/datasheet-pdf-generator-for-woocommerce/)
2. Email plugins@wordpress.org
3. Ask in #pluginreview on [WordPress Slack](https://make.wordpress.org/chat/)
