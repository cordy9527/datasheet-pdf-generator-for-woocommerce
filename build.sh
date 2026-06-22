#!/usr/bin/env bash
# =============================================================================
# Datasheet PDF Generator — Build Script
# =============================================================================
# Produces distribution-ready plugin ZIP from this repository.
#
# Usage:
#   ./build.sh    →  dist/datasheet-pdf-generator.<version>.zip
#
# Requirements: bash, rsync, zip
#   Optional:   composer  (falls back to copying vendor/ if already present)
# =============================================================================
set -euo pipefail

# Run from the repo root regardless of CWD.
REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${REPO_DIR}"

PLUGIN_SLUG="datasheet-pdf-generator"
MAIN_FILE="${PLUGIN_SLUG}.php"
BUILD_TMP="${REPO_DIR}/.build-tmp"
DIST_DIR="${REPO_DIR}/dist"

# ── Terminal colours ──────────────────────────────────────────────────────────
if [ -t 1 ]; then
    GREEN='\033[0;32m'; YELLOW='\033[1;33m'; RED='\033[0;31m'; BOLD='\033[1m'; RESET='\033[0m'
else
    GREEN=''; YELLOW=''; RED=''; BOLD=''; RESET=''
fi

ok()   { echo -e "${GREEN}  ✓${RESET}  $*"; }
warn() { echo -e "${YELLOW}  ⚠${RESET}  $*"; }
err()  { echo -e "${RED}  ✗${RESET}  $*" >&2; }
info() { echo -e "\n${BOLD}▶ $*${RESET}"; }

# ── Helpers ───────────────────────────────────────────────────────────────────

get_version() {
    grep -m1 " \* Version:" "${MAIN_FILE}" | awk '{ print $NF }'
}

# Remove temp dir on exit (success or failure).
cleanup() { rm -rf "${BUILD_TMP}"; }
trap cleanup EXIT

# ── Build function ────────────────────────────────────────────────────────────

build() {
    local version
    version="$(get_version)"

    local zip_path="${DIST_DIR}/${PLUGIN_SLUG}.${version}.zip"

    info "Building v${version}"

    # ── 1. Stage files ────────────────────────────────────────────────────────
    rm -rf "${BUILD_TMP}"
    mkdir -p "${BUILD_TMP}/${PLUGIN_SLUG}"

    rsync -a --exclude-from=".distignore" --exclude="vendor/" ./ "${BUILD_TMP}/${PLUGIN_SLUG}/"
    ok "Files staged"

    # ── 2. Composer dependencies ───────────────────────────────────────────
    # Expects vendor/ to already exist in the repo (run composer install first).
    if [ -d "${REPO_DIR}/vendor" ]; then
        cp -r "${REPO_DIR}/vendor" "${BUILD_TMP}/${PLUGIN_SLUG}/vendor"

        # Remove shell scripts from dependencies; WordPress.org does not permit them in plugin uploads.
        find "${BUILD_TMP}/${PLUGIN_SLUG}/vendor" -type f -name '*.sh' -delete

        # Remove unnecessary mPDF fonts to reduce file size (keep only DejaVu for fallback)
        cd "${BUILD_TMP}/${PLUGIN_SLUG}/vendor/mpdf/mpdf/ttfonts"
        find . -type f ! -name 'DejaVu*' -delete
        cd "${REPO_DIR}"

        ok "Vendor dependencies copied (shell scripts removed, fonts optimized)"
    else
        err "vendor/ not found. Run 'composer install' first."
        exit 1
    fi

    # ── 3. Package ────────────────────────────────────────────────────────────
    mkdir -p "${DIST_DIR}"

    # Remove previous build of the same version if it exists.
    [ -f "${zip_path}" ] && rm -f "${zip_path}"

    ( cd "${BUILD_TMP}" && zip -rq "${zip_path}" "${PLUGIN_SLUG}" \
        -x "*/.DS_Store" -x "*/__MACOSX/*" )

    ok "ZIP created → ${zip_path}"
}

# ── Entry point ───────────────────────────────────────────────────────────────

build

echo ""
echo -e "${GREEN}Done.${RESET} Output: ${DIST_DIR}/"
