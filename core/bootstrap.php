<?php

define('ABS_PATH', dirname(__FILE__) . '/..');

if (file_exists(ABS_PATH . '/app-config.php')) {

    require_once(ABS_PATH . '/app-config.php');

} elseif (file_exists(ABS_PATH . '/app-config-sample.php')) {

    require_once(ABS_PATH . '/app-config-sample.php');
} else {

    die('Did not find any config file.');
}
