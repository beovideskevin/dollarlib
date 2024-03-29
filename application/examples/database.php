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

		"DATABASE": 
		{
			"ADAPTER": "mysql",
			"PORT": "3306",
			"HOST": "mysql",
			"DATABASE": "",
			"USER": "",
			"PASSWORD": "",
			"MIGRATIONS": "migrations.sql"
		},

		"REGISTER":
		{
			"EXCEPTIONS": "",
			"FOLDERS": "application/examples/"
		},

		"ROUTES":
		{
			"_default": "checkDB"
		}
	},

	"PRO": {}
}

*/

function checkDB($args = [])
{
	global $_;

	// Run the migrations, this will execute the code in migrations.sql, 
	// I  do this to fill the database with records 
	$_("migrations");

	// Get a list of all addresses
	$results = $_("assoclist: SELECT * FROM address");
	$pre = "<b>assoclist: SELECT * FROM address</b>\n" . print_r($results, true) . "\n";

	// Get the first address
	$results = $_("assoc: SELECT * FROM address WHERE id = ?", [1]);
	$pre .= "<b>assoc: SELECT * FROM address WHERE id = ?</b>\n" . print_r($results, true) . "\n";

	// Get address as object
	$results = $_("obj: SELECT * FROM address WHERE id = ?", [1]);
	$pre .= "<b>obj: SELECT * FROM address WHERE id = ?</b>\n" . print_r($results, true) .  "\n";

	// Get a list of addresses objects
	$results = $_("objs: SELECT * FROM address");
	$pre .= "<b>objs: SELECT * FROM address</b>\n" . print_r($results, true) . "\n";
	
	// Count the addresses, this is a shorthand
	$results = $_("count: address");
	$pre .= "<b>count: address</b>\n" . print_r($results, true) . "\n\n";

	// If tou dont ask a return type it just return boolean
	$results = $_("query: SELECT * FROM address WHERE id = ?", [1]);
	$pre .= "<b>query: SELECT * FROM address WHERE id = ?</b>\n" . print_r($results, true) . "\n\n";

	// Now we want the results
	$results = $_("results: assoclist");
	$pre .= "<b>results: assoclist</b>\n" . print_r($results, true) . "\n";

	// You can ask this two ways, like most things here
	$_("results", "assoclist");
	$pre .= "<b>results</b>\n" . print_r($results, true) . "\n";

	// This is another way of getting a list of addresses
	$results = $_(": SELECT * FROM address WHERE id = ?", [1], 'assoclist');
	$pre .= "<b>: SELECT * FROM address WHERE id = ?</b>\n" . print_r($results, true) . "\n";
	
	// This will return the id of the record
	$results = $_("insertid: INSERT INTO `address` (`street`, `city`, `country`, `user`) VALUES ('1223 sw', 'Miami', 'US', 1)");
	$pre .= "<b>insertid: INSERT INTO `address` (`street`, `city`, `country`, `user`) VALUES ('1223 sw', 'Miami', 'US', 1)</b>\n" . print_r($results, true) . "\n\n";
	
	// This is how you update a record
	$results = $_(": UPDATE address SET street = 'new street' WHERE id = 1");
	$pre .= "<b>: UPDATE address SET street = 'new street' WHERE id = 1</b>\n" . print_r($results, true) . "\n\n";

	// Get all the record from the table address, this is a shorthand
	$results = $_("*: address ");
	$pre .= "<b>*: address </b>\n" . print_r($results, true) . "\n";

	// Usually this is done automatically, so no need to use it most of the time
	$results = $_("sanitize", ["text with ' ' ' in the middle"]);
	$pre .= "<b>sanitize</b>\n" . print_r($results, true) . "\n";

	// I never use these, but there are there
	// $_("connect");
	// $_("disconnect");
	// In order to change the connection you need to assign the properties in te database model 
	// So disconnect first, assign the values and re-connect
	// $_("disconnect);
	// $_("connect", ['adapter' => '', 'host' => '', 'port' => '', 'user' => '', 'password' => '', 'database' => ''])
	// I haven't tested this, so...
	
	return [
		"EXAMPLE" => "Database",
		"NOTES"   => "This class is the one that interacts with the database. It supports MySQL and PostgreSQL. 
					 The idea was to create a very simple way to connect and make queries.",
		"OUTPUT"  => '<pre><code>' . $pre . '</code></pre>'
	];
}