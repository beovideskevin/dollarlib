<?php

// JUST FOR DEBUGGING
// error_reporting (E_ALL);

require_once('$_.php');
$_("cli");

/**
 * Run the migrations 
 */
function runMigrations($args = []) {
    global $_;

    if ($_("migrations"))
        return "OK";
   
    return "KO";
}

/**
 * The help
 */
function cliHelp($args = []) {
    return "You need help, kid" . PHP_EOL;
}