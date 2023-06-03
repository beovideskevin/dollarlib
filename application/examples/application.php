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

		"ROUTES":
		{
			"_default": "checkApplication",
			"login": "login",
			"logout": "logout",
			"admin": {
				"_enforce": "checkLogin",
				"_call": "checkAdmin",
				"more": "checkMore",
				"evenMore": {
					"_call": "checkEvenMore"
				}
			},
			"layout": {
				"_layout": "email.html",
				"_language": "es.ini",
				"_call": "checkLayout"
			},
			"urlargs/:id": {
				"_call": "checkUrlArgs"
			},
			"args": {
				"_call": "checkArgs",
				"_args": {"key": "value"}
			},
			"notgood": {
				"_redirect": "checkRedirect"
			},
			"checkRedirect": "checkRedirect",
			"_404": "check404",
			"_httperrors": {
				"error301" : "301"
			},
			"checkClass": "myClass::index",
			"api": {
				"_layout": "json.html",
				"_get": "getMethod",
				"_post":  "postMethod",
				"_put":  "putMethod",
				"_delete":  "deleteMethod"
			}
		}
	},

	"PRO": {}
}

Routes to test
	/
	/admin
	/login
	/admin
	/admin/more
	/admin/evenmore
	/logout
	/layout
    /urlargs/id_123456
	/args
	/notgood
	/404
	/error301
	/checkClass

    GET, POST, PUT, DELETE:
    /api
	
*/

function checkApplication($args = [])
{
	global $_;

	// This is how you get the configuration, all of it of some index
	$results = $_("getconfig");
	$pre = "<b>getconfig:</b>\n" . print_r($results, true) . "\n";
	$results = $_("getconfig: WEBSITE");
	$pre .= "<b>getconfig WEBSITE: </b>\n" . print_r($results, true) . "\n\n";
	$results = $_("getconfig", ["WEBSITE", "FILES_PATH"]);
	$pre .= "<b>getconfig WEBSITE and FILES_PATH:</b>\n" . print_r($results, true) . "\n\n";
	

	$pre .= "<b>These constants are defined:</b>\n" . 
			"WEBSITE: " . WEBSITE . "\n" . 
			"FILES_ABSOLUTE_PATH: " . FILES_ABSOLUTE_PATH . "\n" .
			"FILES_RELATIVE_PATH: " . FILES_RELATIVE_PATH . "\n" .
			"ASSETS_BASE_PATH: " . ASSETS_BASE_PATH . "\n" .
			"LAYOUT_PATH: " . LAYOUT_PATH . "\n" .
			"LANGUAGE_PATH: " . LANGUAGE_PATH;

	/**
	 * This is how I start a website
	 */
	// session_start();
	// require_once('$_.php');
	// $_("run");
	//
	// These three options are not used often, the system loads the config, registers and routes 
	// automatically when run is called, you can still need them in some cases:
	// $_("setconfig");
	// $_("register");
	// $_("route");

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

function checkUrlArgs($args = []) {
    return [
        "EXAMPLE" => "Application",
        "NOTES"   => "This class would be a controller if the lib was an MVC. It takes care of loading the configuration, including 
					  the other files and routing.",
        "OUTPUT"  => '<pre><code>' . print_r($args, true) . '</code></pre>'
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

class myClass {
	static function index($args = []) {
		return [
			"EXAMPLE" => "Application",
			"NOTES"   => "This class would be a controller if the lib was an MVC. It takes care of loading the configuration, including 
						  the other files and routing.",
			"OUTPUT"  => 'The index method was called'
		];
	}
}

function getMethod($args = []) {
    return [
        "OUTPUT"  => 'GET: ' . print_r($args, true) 
    ];
}

function postMethod($args = []) {
    return [
        "OUTPUT"  => 'POST: ' . print_r($args, true)
    ];
}

function putMethod($args = []) {
    return [
        "OUTPUT"  => 'PUT: ' . print_r($args, true)
    ];
}

function deleteMethod($args = []) {
    return [
        "OUTPUT"  => 'DELETE: ' . print_r($args, true)
    ];
}
