#!/bin/bash
set -e

# Execute this every 5 minutes: */5 * * * * /path/to/cron.sh

composer db:backup
