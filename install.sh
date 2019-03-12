#!/bin/bash

echo -e "\n"
echo "Simple shell script running composer and setting up DB."
echo -e "\n"
  
composer install
composer update
bin/console d:d:c
bin/console d:m:m
bin/console d:m:s
bin/console s:r
