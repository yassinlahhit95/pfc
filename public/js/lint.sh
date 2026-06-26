#!/usr/bin/env bash
# lint.sh - run ESLint and Stylelint for the project

echo "Running ESLint on JavaScript files..."
# Lint all .js files under public/js (recursively)
npx eslint "public/js/**/*.js" --max-warnings=0 || echo "ESLint completed with warnings/errors."

echo "Running Stylelint on CSS files..."
# Lint all .css files under public/css (recursively)
npx stylelint "public/css/**/*.css" --max-warnings=0 || echo "Stylelint completed with warnings/errors."

echo "Linting finished."
