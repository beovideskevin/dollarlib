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
			"_default": "checkCurl"
		}
	},

	"PRO": {}
}

*/

use DollarLib\Curl;

// Useful snippets of code, you do not have to use all those options: 
function checkCurl() 
{
	global $_;

	// Easy curl call, there is a lot more to this but Im to tired to care right now 
	$results = $_("curl: https://api.coinbase.com/v2/prices/spot?currency=USD", "GET" , 
		[
			'HEADERS' => [],
			'OPTIONS' => []
		]
	);
	$pre = "<b>curl:</b>\n" . print_r($results, true) . "\n";

	// A longer example using the class Curl, not tested but it should be ok
	// $data_array = [];
	// $query_string = http_build_query($data_array); 
	// $userpwd = array(
	// 	"api_username" => "", 
	// 	"api_password" => ""
	// );
	// $curl = new Curl();
	// $results = $curl->sendHttp(
	//  	"https://www.example.com/api",
	// 	"POST", 
	// 	"",
	// 	[],
	// 	[
	// 		CURLOPT_SSL_VERIFYPEER => 0,
	// 		CURLOPT_RETURNTRANSFER => true,
	// 		CURLOPT_VERBOSE        => false,
	// 		CURLOPT_POSTFIELDS     => $query_string,
	// 		CURLOPT_POST           => true,
	// 		CURLOPT_HTTPGET        => true,
	// 		CURLOPT_TIMEOUT        => 30,
	// 		CURLOPT_CONNECTTIMEOUT => 30,
	// 		CURLOPT_USERAGENT      => 'InvestiGate API Access',
	// 		CURLOPT_USERPWD        => $userpwd['api_username'].':'.$userpwd['api_password']
	// 	]
	// );
	// $pre = "<b>curl</b>\n" . print_r($results, true) . "\n";
	
	return [
		"EXAMPLE" => "Curl",
		"NOTES"   => "",
		"OUTPUT"     => '<pre><code>' . $pre . '</code></pre>'
	];
}