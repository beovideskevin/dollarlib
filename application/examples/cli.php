<?php
/*
 config.json

{
	"ENV": "DEV",

    "DEV": 
    {
		"WEBSITE": "localhost",

		"FILES_PATH": "dollarlib.eldiletante.com/",

		"ASSETS_PATH": "application/assets/",

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
    },

	"PRO": {}
}

CLI test:
    $php cli.php 
    $php cli.php test
    $php cli.php flags -t -v -z
    $php cli.php flags -u
    $php cli.php params file1 group1
    $php cli.php params file1 group1 other
*/

function exampleHelp($args = []) {
    return "You need help, kid" . PHP_EOL;
}

function runTest($args = []) {
    return "runTest: " . print_r($args, true);
}

function runFlags($args = []) {
    return "runFlags: " . print_r($args, true);
}

function runParams($args = []) {
    return "runParams: " . print_r($args, true);
}
 