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
			"DEFAULT_LANGUAGE": "en.ini",
			"LAYOUT_PATH": "application/layout/",
			"DEFAULT_LAYOUT": "main.html"
		},

		"REGISTER":
		{
			"EXCEPTIONS": "",
			"FOLDERS": "application/examples/"
		},

		"ROUTES":
		{
			"_default": "checkTemplate",
			"es": "checkES",
			"en": "checkEN"
		}
	},

	"PRO": {}
}

Routes to test
	/
	/es
	/en

*/

use DollarLib\Utils;

function checkTemplate($args = []) 
{
	global $_;

	// At this point the template has not being set
	$results = $_("getlayout");
	$pre = "<b>getlayout:</b>\n" . htmlspecialchars($results) .  "\n\n";

	// Set the layout
	$results = $_("setlayout", "email.html");
	$pre .= "<b>setlayout, email.html:</b>\n" . htmlspecialchars($results) .  "\n\n";

	$results = $_("getlayout");
	$pre .= "<b>getlayout:</b>\n" . htmlspecialchars($results) .  "\n\n";

	// Get all the language
	$results = $_("getlang");
	$pre .= "<b>getlang:</b>\n" . print_r($results, true) . "\n\n";

	// Get one of the indexes of the languages
	$results = $_("getlang: TITLE");
	$pre .= "<b>getlang: TITLE</b>\n" . print_r($results, true) . "\n\n";

	// Get the same index again
	$results = $_("getlang", "TITLE");
	$pre .= "<b>getlang: TITLE</b>\n" . print_r($results, true) . "\n\n";

	// Set the language to en.ini, this also saves the value in the session 
	$results = $_("setlang", "en.ini");
	$pre .= "<b>setlang en.ini:</b>\n" . print_r($results, true) . "\n\n";

	// Set the language to es.ini, this also saves the value in the session
	$results = $_("setlang: es.ini");
	$pre .= "<b>setlang es.ini:</b>\n" . print_r($results, true) . "\n\n";

	// Notice that we do not have inject without : because we need the last argument for the clean up flag
	$results = $_("inject: application/examples/includes/example.html");
	$pre .= "<b>htmlOut:</b>\n" . Utils::htmlOut($results, true) . "\n\n";

	// This is for JS, @TODO use assets path
	$results = $_("inject: application/examples/includes/example.js");
	$pre .= "<b>jsOut:</b>\n" . Utils::jsOut($results) . "\n\n";

	// This is for links, it could be useful
	$pre .= "<b>linkOut:</b>\n" . Utils::linkOut("<a href='www.google.com'>this link</a>") . "\n\n";

	// Lets try importing something and using the language
	$results = $_("inject: application/examples/includes/example.html", ["TEXT" => "say hello"]);
	$pre .= "<b>inject with language:</b>\n" . htmlspecialchars($results) . "\n\n";

	// This is only used when you didn't started the app with run, mostly is not used 
	$results = $_("render", [
			"OUTPUT"  => '{"value":"this is a json"}'
		]);
	$pre .= "<b>render with language:</b>\n" . htmlspecialchars($results) . "\n\n";

	// Lets reset the layout and language this or it is not going to work 
	$_("setlayout", "main.html");
	$_("setlang", "en.ini");

	return [
		"STYLES"  => $_("inject: application/examples/includes/example.css"), // this will make every p look bold
		"EXAMPLE" => "Template",
		"NOTES"   => "The idea of the whole lib is to create the HTML completely inside the PHP code. This class 
					  basically manages the layout of the web, inserting the language when render is called.",
		"OUTPUT"  => '<pre><code>' . $pre . '</code></pre>'
	];
}

function checkES()
{
	global $_;
	$_("setlang", "es.ini");
	return [
		"EXAMPLE" => "Template",
		"NOTES"   => "One of the most important pieces was to make it easy to change the text from spanish to english."
	];
}

function checkEN()
{
	global $_;
	$_("setlang", "en.ini");
	return [
		"EXAMPLE" => "Template",
		"NOTES"   => "One of the most important pieces was to make it easy to change the text from spanish to english."
	];
}