<?php

/**
 * Output
 *
 * A wrapper for echo adding a new line
 * @param string $output
 */
function output($output, $exit = false) {
    echo $output . PHP_EOL;
    if ( $exit ) exit;
}

/**
 * Clear Directory
 */
function clear_dir($dir) {
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
    rmdir($dir);
}

function starts_with($haystack, $needle) {
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

// Check we're calling from the CLI
if ( php_sapi_name() !== 'cli' ) {
    output('This app only works in the terminal!', true);
}

// Check for a command
if ( !isset($argv[1]) ) {
    output("\e[0;31mYou must specify a command! e.g. boilerplate install\e[0m\n", true);
}

// Run the command
if ( $argv[1] === 'install' ) {

    /**
     * @command install
     */

    // Get install dir
    if ( !isset($argv[2]) ) {
        output('You must specify an installation directory!');
        output('Exiting...', true);
    }

    $installDir = $argv[2];

    if ( is_dir($installDir) ) {
        output('Installation directory already exists!');
        output('Exiting...', true);
    }

    // Run the install
    output('Installing Boilerplate...');
    passthru(sprintf('git clone https://github.com/resknow/boilerplate.git %s', $installDir));

    // clean up!
    output('Cleaning up...');
    clear_dir($installDir . '/.git');

    // Setup new git repo
    passthru(sprintf('cd %s && git init && composer install', $installDir));

    output('Boilerplate installed!');

    // Install a starter template
    if ( isset($argv[3]) ) {
        $templateDir = $installDir . '/_templates';

        // Clear the templates directory
        output('Removing default templates...');
        clear_dir($templateDir);
        mkdir($templateDir);

        // Install new templates
        passthru(sprintf('git clone %s %s', $argv[3], $templateDir));

        // Clean up
        clear_dir($templateDir . '/.git');

        // Done
        output('Done! Happy coding :)');
    }

}
