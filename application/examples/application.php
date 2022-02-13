<?php 

/*
config.json

{
	"WEBSITE": "localhost",

	"FILES_BASE_PATH": "/dollarlib.eldiletante.com/application/",

	"REGISTER":
	{
		"EXCEPTIONS": "",
		"FOLDERS": "examples/"
	},

	"ROUTES":
	{
		"default": "checkApplication",
		"login": "login",
		"logout": "logout",
		"admin": {
			"enforce": "checkLogin",
			"action": "checkAdmin",
			"more": "checkMore",
			"evenMore": {
				"action": "checkEvenMore"
			}
		},
		"layout": {
			"layout": "email.html",
			"language": "es.ini",
			"action": "checkLayout"
		},
		"args": {
			"action": "checkArgs",
			"args": "{\"key\": \"value\"}"
		},
		"notgood": {
			"redirect": "checkRedirect"
		},
		"checkRedirect": "checkRedirect",
		"404": "check404",
		"httperrors": {
			"error301" : "301"
		}
	},

	"TEMPLATE": 
	{
		"LANGUAGE_PATH": "language/",
		"DEFAULT_LANGUAGE": "es.ini",
		"LAYOUT_PATH": "layout/",
		"DEFAULT_LAYOUT": "main.html"
	}
}

Routes to test
	/
	/admin
	/login
	/admin/more
	/admin/evenmore
	/logout
	/layout
	/args
	/notgood
	/404
	/error301

*/

function checkApplication($args = [])
{
	global $_;

	// This is how you get the configuration, all of it of some index
	$results = $_("getconfig");
	$pre = "<b>getconfig</b>\n" . print_r($results, true) . "\n";
	$results = $_("getconfig: WEBSITE");
	$pre .= "<b>getconfig</b>\n" . print_r($results, true) . "\n\n";
	$results = $_("getconfig", "WEBSITE");
	$pre .= "<b>getconfig</b>\n" . print_r($results, true) . "\n\n";

	/**
	 * This is how I start a website
	 */
	// JUST FOR DEBUGGING
	// error_reporting (E_ALL);
	// session_start();
	// require_once('$_.php');
	// $_("run");
	//
	// This three options are not used often, the system loads the config, registers and routes 
	// automatically when run is called, you can still need them in some cases
	// "setconfig"
	// "register"
	// "route"

	return [
		"EXAMPLE" => "Application",
		"NOTES"   => "This class would be a controller if the lib was an MVC. It takes care of loading the configuration, including 
					  the other files and routing.",
		"OUTPUT"  => '<pre><code>' . $pre . '</code></pre>'
	];
}

// Route /redirect
function redirectPage ($args = []) {
	global $_;
	$result = [
		"OUTPUT" => "redirectPage"
	];
	return $result;
}

function login($args = [])
{
	$_SESSION['user'] = 1;
	return [
		"EXAMPLE" => "Application",
		"NOTES"   => "This class would be a controller if the lib was an MVC. It takes care of loading the configuration, including 
					  the other files and routing.",
		"OUTPUT"  => 'Login'
	];
}

function logout()
{
	unset($_SESSION['user']);
	return [
		"EXAMPLE" => "Application",
		"NOTES"   => "This class would be a controller if the lib was an MVC. It takes care of loading the configuration, including 
					  the other files and routing.",
		"OUTPUT"  => 'Logout'
	];
}

function checkLogin($args = [])
{
	return !empty($_SESSION['user']);
}

function checkRedirect($args = [])
{
	return [
		"EXAMPLE" => "Application",
		"NOTES"   => "This class would be a controller if the lib was an MVC. It takes care of loading the configuration, including 
					  the other files and routing.",
		"OUTPUT"  => 'You were redirected'
	];
}

function check404($args = [])
{
	return [
		"EXAMPLE" => "Application",
		"NOTES"   => "This class would be a controller if the lib was an MVC. It takes care of loading the configuration, including 
						the other files and routing.",
		"OUTPUT"  => '404'
	];
}

function checkAdmin($args = [])
{
	return [
		"EXAMPLE" => "Application",
		"NOTES"   => "This class would be a controller if the lib was an MVC. It takes care of loading the configuration, including 
						the other files and routing.",
		"OUTPUT"  => 'Admin page'
	];
}

function checkMore($args = []) 
{
	return [
		"EXAMPLE" => "Application",
		"NOTES"   => "This class would be a controller if the lib was an MVC. It takes care of loading the configuration, including 
						the other files and routing.",
		"OUTPUT"  => 'More'
	];
}

function checkEvenMore($args = [])
{
	return [
		"EXAMPLE" => "Application",
		"NOTES"   => "This class would be a controller if the lib was an MVC. It takes care of loading the configuration, including 
						the other files and routing.",
		"OUTPUT"  => 'Even More'
	];
}

function checkLayout($args = [])
{
	return [
		"OUTPUT"  => 'This is the email layout. Not much to look at :)'
	];
}

function checkArgs($args = [])
{
	return [
		"EXAMPLE" => "Application",
		"NOTES"   => "This class would be a controller if the lib was an MVC. It takes care of loading the configuration, including 
						the other files and routing.",
		"OUTPUT"  => '<pre><code>' . print_r($args, true) . '</code></pre>'
	];
}