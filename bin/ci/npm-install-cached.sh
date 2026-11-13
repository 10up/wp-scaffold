#!/usr/bin/env bash
# Installs node_modules for CI, unless the ones already there (restored from the
# cache) were installed from this exact package-lock.json.
set -euo pipefail

if sha256sum --check --status node_modules/.lock-sha 2>/dev/null; then
	echo "node_modules already matches package-lock.json."
	exit 0
fi

npm ci --ignore-scripts --no-audit --no-fund
sha256sum package-lock.json > node_modules/.lock-sha
