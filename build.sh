#!/bin/bash
#
# Build script for Estevão Liturgical Calendar plugin
# Creates a distribution-ready zip file
#

# Plugin info
PLUGIN_SLUG="estevao-liturgical-calendar"
VERSION=$(grep -oP "Version:\s*\K[0-9.]+" estevao-liturgical-calendar.php)

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Building ${PLUGIN_SLUG} v${VERSION}${NC}"
echo ""

# Create dist directory
echo -e "${YELLOW}Creating dist directory...${NC}"
rm -rf dist
mkdir -p dist/${PLUGIN_SLUG}

# Copy files
echo -e "${YELLOW}Copying plugin files...${NC}"
cp estevao-liturgical-calendar.php dist/${PLUGIN_SLUG}/
cp readme.txt dist/${PLUGIN_SLUG}/
cp README.md dist/${PLUGIN_SLUG}/
cp -r includes dist/${PLUGIN_SLUG}/
cp -r assets dist/${PLUGIN_SLUG}/

# Remove any unwanted files
echo -e "${YELLOW}Cleaning up...${NC}"
find dist/${PLUGIN_SLUG} -name ".DS_Store" -delete
find dist/${PLUGIN_SLUG} -name "*.map" -delete

# Create zip
echo -e "${YELLOW}Creating zip file...${NC}"
cd dist
zip -r ${PLUGIN_SLUG}-${VERSION}.zip ${PLUGIN_SLUG} -x "*.DS_Store" -x "*/.git/*"
cd ..

# Show result
echo ""
echo -e "${GREEN}Build complete!${NC}"
echo ""
ls -lh dist/${PLUGIN_SLUG}-${VERSION}.zip
echo ""
echo -e "Upload ${YELLOW}dist/${PLUGIN_SLUG}-${VERSION}.zip${NC} to GitHub releases"
