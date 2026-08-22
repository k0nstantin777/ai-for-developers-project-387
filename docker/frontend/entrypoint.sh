#!/bin/sh
set -e

echo "Installing npm dependencies..."
npm install

exec npm run dev -- --host
