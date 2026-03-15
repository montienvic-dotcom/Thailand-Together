#!/bin/bash
# Thailand Together — Load Test Runner
# Usage: ./run.sh <scenario> [options]
#
# Scenarios:
#   tourist      — Tourist journey simulation (primary)
#   stress       — API integration stress test
#   database     — Database bottleneck detection
#
# Options:
#   --vus N      — Override virtual users (default: scenario-specific)
#   --duration D — Override duration (default: scenario-specific)
#   --env ENV    — Environment: local, staging, production (default: local)

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

SCENARIO="${1:-tourist}"
VUS=""
DURATION=""
ENV="local"

# Parse options
shift || true
while [[ $# -gt 0 ]]; do
    case $1 in
        --vus) VUS="$2"; shift 2 ;;
        --duration) DURATION="$2"; shift 2 ;;
        --env) ENV="$2"; shift 2 ;;
        *) echo "Unknown option: $1"; exit 1 ;;
    esac
done

# Environment URLs
case $ENV in
    local)      BASE_URL="http://localhost:8000" ;;
    staging)    BASE_URL="${STAGING_URL:-http://staging.thailandtogether.com}" ;;
    production) BASE_URL="${PRODUCTION_URL:-https://api.thailandtogether.com}" ;;
    *)          echo "Unknown environment: $ENV"; exit 1 ;;
esac

# Map scenario to file
case $SCENARIO in
    tourist)    SCENARIO_FILE="$SCRIPT_DIR/scenarios/tourist-journey.js" ;;
    stress)     SCENARIO_FILE="$SCRIPT_DIR/scenarios/api-integration-stress.js" ;;
    database)   SCENARIO_FILE="$SCRIPT_DIR/scenarios/database-stress.js" ;;
    *)          echo "Unknown scenario: $SCENARIO"; exit 1 ;;
esac

if [ ! -f "$SCENARIO_FILE" ]; then
    echo "Error: Scenario file not found: $SCENARIO_FILE"
    exit 1
fi

# Build k6 flags
FLAGS="-e BASE_URL=$BASE_URL"

if [ -n "$VUS" ]; then
    FLAGS="$FLAGS --vus $VUS"
fi

if [ -n "$DURATION" ]; then
    FLAGS="$FLAGS --duration $DURATION"
fi

# Report output
REPORT_DIR="$SCRIPT_DIR/../reports"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
REPORT_FILE="$REPORT_DIR/${SCENARIO}_${TIMESTAMP}.json"

echo "========================================"
echo "  Thailand Together Load Test"
echo "========================================"
echo "  Scenario:    $SCENARIO"
echo "  Environment: $ENV"
echo "  Base URL:    $BASE_URL"
echo "  Report:      $REPORT_FILE"
echo "========================================"
echo ""

k6 run $FLAGS --out json="$REPORT_FILE" "$SCENARIO_FILE"

echo ""
echo "Report saved to: $REPORT_FILE"
