<?php

// JUST FOR DEBUGGING
// error_reporting (E_ALL);

require_once('$_.php');
$_('register: application/examples/', []); // This is only needed for the examples
$_("cli");

/**
 * Run the migrations 
 */
function runMigrations($args = []) {
    global $_;

    if ($_("migrations"))
        return "OK" . PHP_EOL;
   
    return "KO" . PHP_EOL;
}

/**
 * The help
 */
function cliHelp($args = []) {
    return "You need help, kid" . PHP_EOL;
}