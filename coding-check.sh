#!/usr/bin/env bash

vendor/bin/php-cs-fixer fix --dry-run --diff -v --config .php_cs
