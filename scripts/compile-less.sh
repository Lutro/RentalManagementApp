#!/bin/bash

# Run from main project folder
# ./scripts/compile-less.sh

# Compile all less files in a single css
find less/ -name '*.less' -exec lessc {} \; > styles/styles.css