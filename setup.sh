#!/bin/bash

echo "Running composer install..."
composer install

if [ $? -eq 0 ]; then
    echo "composer install completed successfully."
else
    echo "composer install failed."
    exit 1
fi

echo "Running composer dump-autoload -a..."
composer dump-autoload -a

if [ $? -eq 0 ]; then
    echo "composer dump-autoload -a completed successfully."
else
    echo "composer dump-autoload -a failed."
    exit 1
fi

echo "All composer commands executed successfully."
