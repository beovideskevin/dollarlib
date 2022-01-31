<?php 

/**
config.json

{
	"WEBSITE": "localhost",

	"FILES_BASE_PATH": "/dollarlib.eldiletante.com/application/",

	"TEMPLATE": {
		"LANGUAGE_PATH": "language/",
		"DEFAULT_LANGUAGE": "es",
		"LAYOUT_PATH": "layout/",
		"DEFAULT_LAYOUT": "simple"
	},

	"REGISTER":
	{
		"EXCEPTIONS": "",
		"FOLDERS": "/examples"
	},

	"ROUTES":
	{
		"redirect": {
			"redirect": "redirectPage"
		}
	}
}

*/

// Route /redirect
function redirectPage (args[]) {
	global $_;
	$result = [
		"OUTPUT" => "redirectPage"
	]
	return $result;
}