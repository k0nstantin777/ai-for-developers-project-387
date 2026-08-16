#!/bin/sh
set -e

echo "Waiting for backend..."
until curl -s "http://backend:8000/api/event-types" > /dev/null 2>&1; do
  sleep 2
done
echo "Backend is ready."

echo "Waiting for frontend..."
until curl -s "http://frontend:5173" > /dev/null 2>&1; do
  sleep 2
done
echo "Frontend is ready."

echo "Resetting database..."
curl -s -X POST "http://backend:8000/api/testing/reset-database"
echo ""

echo "Running Playwright tests..."
./node_modules/.bin/playwright test "$@"
