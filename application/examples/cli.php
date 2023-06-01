<?php
/*
 config.json

{
	"WEBSITE": "localhost",

	"FILES_BASE_PATH": "/dollarlib.eldiletante.com/",

	"TEMPLATE": 
	{
		"LANGUAGE_PATH": "application/language/",
		"DEFAULT_LANGUAGE": "es.ini",
		"LAYOUT_PATH": "application/layout/",
		"DEFAULT_LAYOUT": "main.html"
	},

	"REGISTER":
	{
		"EXCEPTIONS": "",
		"FOLDERS": "application/examples/"
	},

	"CLI": {
        "_help": "exampleHelp",
        "test": "runTest",
        "flags": {
            "_call": "runFlags",
            "_flags": [
                "t",
                "v",
                "z"
            ]
        },
        "params": {
            "_call": "runParams",
            "_params": [
                "file",
                "group"
            ]
        }
    }
}

CLI test:
    $php cli.php 
    $php cli.php test
    $php cli.php flags
    $php cli.php params
*/

function exampleHelp($args = []) {
    return "You need help, kid" . PHP_EOL;
}

function runTest($args = []) {
    return print_r($args, true);
}

function runFlags($args = []) {
    return print_r($args, true);
}

function runParams($args = []) {
    return print_r($args, true);
}
 