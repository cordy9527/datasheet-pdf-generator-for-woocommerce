#!/bin/bash
# Deploy to WordPress.org SVN from Git
# Usage: ./deploy-to-wporg.sh [version]

set -e

# Configuration
PLUGIN_SLUG="datasheet-pdf-generator-for-woocommerce"
GITHUB_REPO_PATH=$(pwd)
SVN_REPO_URL="https://plugins.svn.wordpress.org/$PLUGIN_SLUG"
SVN_REPO_PATH="/tmp/$PLUGIN_SLUG-svn"

# Version from argument or auto-detect from main file
if [ -z "$1" ]; then
    VERSION=$(grep "Version:" class-cordy-pdf-generator.php | awk '{print $3}')
else
    VERSION=$1
fi

echo "🚀 Deploying version $VERSION to WordPress.org"

# Step 1: Run build
echo "📦 Building plugin..."
./build.sh

# Step 2: Checkout SVN repo
echo "📥 Checking out SVN repository..."
if [ -d "$SVN_REPO_PATH" ]; then
    rm -rf "$SVN_REPO_PATH"
fi
svn co "$SVN_REPO_URL" "$SVN_REPO_PATH"

# Step 3: Clear trunk
echo "🧹 Clearing trunk..."
rm -rf "$SVN_REPO_PATH/trunk"/*

# Step 4: Unzip built plugin to trunk
echo "📋 Copying files to trunk..."
unzip -q "dist/$PLUGIN_SLUG.$VERSION.zip" -d "$SVN_REPO_PATH/trunk/"

# Step 5: Copy assets (screenshots, banner, icon)
echo "🎨 Copying assets..."
if [ -d ".wordpress-org" ]; then
    cp -r .wordpress-org/* "$SVN_REPO_PATH/assets/" 2>/dev/null || true
fi

# Step 6: SVN add/remove files
cd "$SVN_REPO_PATH"
svn stat | grep '^!' | awk '{print $2}' | xargs -r svn delete --force
svn stat | grep '^?' | awk '{print $2}' | xargs -r svn add

# Step 7: Show changes
echo "📊 Changes to commit:"
svn stat

# Step 8: Confirm before committing
read -p "🤔 Commit to trunk? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Deployment cancelled"
    exit 1
fi

# Step 9: Commit to trunk
echo "✍️  Committing to trunk..."
svn ci -m "Update trunk to version $VERSION"

# Step 10: Create tag
read -p "🏷️  Create tag $VERSION? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "🏷️  Creating tag $VERSION..."
    svn cp "^/trunk" "^/tags/$VERSION" -m "Tagging version $VERSION"
    echo "✅ Tag $VERSION created"
fi

echo "🎉 Deployment complete!"
echo "📍 View at: https://wordpress.org/plugins/$PLUGIN_SLUG/"

# Cleanup
cd "$GITHUB_REPO_PATH"
rm -rf "$SVN_REPO_PATH"
