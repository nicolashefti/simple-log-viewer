<?php
/*
    @name           Server Logs Viewer
    @description    Emulates the tail() function. View the latest lines of your LAMP server logs in your browser.
    @author         Alexandre Plennevaux (pixeline.be)
    @date           15.07.2014
*/

// Absolute local path to your server 'log' directory
define('LOG_PATH', '/srv/data/var/log/');
define('DISPLAY_REVERSE', true); // true = displays log entries starting with the most recent

// Files that you want to have access to, inside the LOG_PATH directory
$files = [
    'apache1' => ['name' => 'Apache Error Log', 'path' => LOG_PATH . 'apache/error.log'],
    'WWW'     => ['name' => 'www Error Log', 'path' => LOG_PATH . 'www/www-error.log'],
    'FPM'     => ['name' => 'FPM Log', 'path' => LOG_PATH . 'www/fpm.log'],
    'apache2' => ['name' => 'Apache Access Log', 'path' => LOG_PATH . 'apache/access.log'],
    'cron1'   => ['name' => 'Cron User Log', 'path' => LOG_PATH . 'cron/user.log'],
    'cron2'   => ['name' => 'Cron Admin Log', 'path' => LOG_PATH . 'cron/admin.log'],

];

// Set a Smart default: Apache Error log
$log = (!isset($_GET['p'])) ? 'apache1' : $_GET['p'];
$lines = (!isset($_GET['lines'])) ? '10' : $_GET['lines'];

$file = $files[$log]['path'];

$title = $files[$log]['name'];
?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Server Logs</title>
        <meta name="description"
              content="Gandi SimpleHosting Server Logs gives an easy access to the (sometimes very heavy) server's last logs, typically on a Gandi SimpleHosting server.">
        <meta name="author" content="pixeline">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pure/0.6.0/pure-min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div id="layout">
        <!-- Menu toggle -->
        <a href="#menu" id="menuLink" class="menu-link">
            <!-- Hamburger icon -->
            <span></span>
        </a>

        <div id="menu">
            <div class="pure-menu">
                <a class="pure-menu-heading" href="#">Server Logs</a>

            </div>
        </div>

        <div id="main">
            <div class="header">
                <h1><?= $title; ?></h1>

                <h2 class="content-subhead">Here are the last <?= $lines ?> of your <?= $title ?>
                    <small>(<?= $file ?>)</small>
                </h2>
                <p>How many lines to display?

                <form action="" method="get">
                    <input type="hidden" name="p" value="<?= $log ?>">
                    <select name="lines" onchange="this.form.submit()">
                        <option value="10" <?= ($lines == '10') ? 'selected' : '' ?>>10</option>
                        <option value="50" <?= ($lines == '50') ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= ($lines == '100') ? 'selected' : '' ?>>100</option>
                        <option value="500" <?= ($lines == '500') ? 'selected' : '' ?>>500</option>
                    </select>
                </form>
                </p>
            </div>

            <div class="content">

                <code><pre style="font-size:14px;font-family:monospace;color:black;white-space: inherit"><ol reversed>
                            <?
                            $output = tail($file, $lines);
                            $output = explode("\n", $output);
                            if (DISPLAY_REVERSE) {
                                // Latest first
                                $output = array_reverse($output);
                            }
                            foreach ($output as $out) {
                                if (trim($out) != '') {
                                    echo '<li>' . htmlspecialchars($out) . '</li>';
                                }
                            }
                            ?>
                        </ol></pre>
                </code>
                <footer>
                    <p class="credits"><a href="//pixeline.be">Script provided by pixeline</a>, thanks to <a
                            href="//purecss.io/">purecss.io</a></p>
                </footer>
            </div>
        </div>
    </div>
    <script>
        (function (window, document) {

            var layout = document.getElementById('layout'),
                menu = document.getElementById('menu'),
                menuLink = document.getElementById('menuLink');

            function toggleClass(element, className) {
                var classes = element.className.split(/\s+/),
                    length = classes.length,
                    i = 0;

                for (; i < length; i++) {
                    if (classes[i] === className) {
                        classes.splice(i, 1);
                        break;
                    }
                }
                // The className is not found
                if (length === classes.length) {
                    classes.push(className);
                }

                element.className = classes.join(' ');
            }

            menuLink.onclick = function (e) {
                var active = 'active';

                e.preventDefault();
                toggleClass(layout, active);
                toggleClass(menu, active);
                toggleClass(menuLink, active);
            };

        }(this, this.document));
    </script>
    </body>
    </html>
<?php

function tail($filename, $lines = 10, $buffer = 4096)
{
    // Open the file
    $f = fopen($filename, "rb");

    // Jump to last character
    fseek($f, -1, SEEK_END);

    // Read it and adjust line number if necessary
    // (Otherwise the result would be wrong if file doesn't end with a blank line)
    if (fread($f, 1) != "\n") $lines -= 1;

    // Start reading
    $output = '';
    $chunk = '';

    // While we would like more
    while (ftell($f) > 0 && $lines >= 0) {
        // Figure out how far back we should jump
        $seek = min(ftell($f), $buffer);

        // Do the jump (backwards, relative to where we are)
        fseek($f, -$seek, SEEK_CUR);

        // Read a chunk and prepend it to our output
        $output = ($chunk = fread($f, $seek)) . $output;

        // Jump back to where we started reading
        fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

        // Decrease our line counter
        $lines -= substr_count($chunk, "\n");
    }

    // While we have too many lines
    // (Because of buffer size we might have read too many)
    while ($lines++ < 0) {
        // Find first newline and remove all text before that
        $output = substr($output, strpos($output, "\n") + 1);
    }

    // Close file and return
    fclose($f);
    return $output;
}
