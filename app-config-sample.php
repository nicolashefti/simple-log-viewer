<?php

define('LOG_PATH', ABS_PATH . '/core/tests/fixtures/log');

// Displays log entries starting with the most recent
define('DISPLAY_REVERSE', true);

// Files that you want to have access to, inside the LOG_PATH directory
$files = [
  'apache1' => [
    'name' => 'Apache Error',
    'path' => LOG_PATH . '/apache/error.log'
  ],
  'apache2' => [
    'name' => 'Apache Access',
    'path' => LOG_PATH . '/apache/access.log'
  ],
  'www'     => [
    'name' => 'PHP Error',
    'path' => LOG_PATH . '/www/www-error.log'
  ],
  'fpm'     => [
    'name' => 'PHP FPM',
    'path' => LOG_PATH . '/www/fpm.log'
  ],
  'cron1'   => [
    'name' => 'Cron User',
    'path' => LOG_PATH . '/cron/user.log'
  ],
  'cron2'   => [
    'name' => 'Cron Admin',
    'path' => LOG_PATH . '/cron/admin.log'
  ],
];