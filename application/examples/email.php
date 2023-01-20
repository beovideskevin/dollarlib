<?php 

/*
config.json

{
	"WEBSITE": "localhost",

	"FILES_BASE_PATH": "/dollarlib.eldiletante.com/",

	"REGISTER":
	{
		"EXCEPTIONS": "",
		"FOLDERS": "application/examples/",
		"VENDORS": "vendor/"
	},

	"TEMPLATE": 
	{
		"LANGUAGE_PATH": "application/language/",
		"DEFAULT_LANGUAGE": "es.ini",
		"LAYOUT_PATH": "application/layout/",
		"DEFAULT_LAYOUT": "main.html"
	},

	"recaptcha": {
		"secretKey": "",
		"siteKey": ""
	},

	"EMAIL": {
		"SYSTEM": "",
		"FROM": "",
		"SERVER": "",
		"PORT": "",
		"USER": "",
		"PASSWORD": "",
		"LAYOUT": "email"
	},

	"ROUTES":
	{
		"_default": "checkEmail"
	}
}

*/

use DollarLib\Utils;

function checkEmail($args = []) 
{
	global $_;

	// I check fot a field like this in the form, it should be empty
	// this is for the robots filling field out there, they fill everything
	// since this is hidden a human would not fill it
	$html = '<div class="form-hidden-row"><input type="hidden" name="hidden" value="" /></div>';

	// In order to use recaptcha you are going to need this script included in the page 
	$js = '<script src="https://www.google.com/recaptcha/api.js"></script>';

	/**
	 * FULL EXAMPLE
	 */ 
	
	// $emailMsg = "Email was NOT sent!"; // Set the error message
	// You want to send an email using the email template, set it
	// Since this method is mostly used like an ajax call it is ok to set the layout 
	// $_("setlayout: email.html");
	// $emailConfig = $_("getConfig: EMAIL");  // Get the config of the captcha
	// $recaptcha = $_("getConfig: recaptcha");  // Get the config of the captcha
	// if (!empty($args['g-recaptcha-response']) && !empty($args['email']) && 
    //     !empty($args['name']) && !empty($args['message']) && empty($args['hidden'])) // Check the fields of the form and the captcha
	// {
    //     $output = json_decode(
	// 		file_get_contents(
	// 			"https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha['secretKey'] . "&response=" . $args['g-recaptcha-response']
	// 		), 
	// 		true
	// 	);
    //     if (isset($output['success']) && $output['success'] == true &&  // Check with google if captcha worked
				// $result = $_(											   // and send the email
				// 	"email: " . $emailConfig['SYSTEM'],
				// 	[
				// 		"subject"   => "Email from yourwebsite.com",  
				// 		"emailfrom" => $args['email'],
				// 		"namefrom"  => $args['name']
				// 	], 
				// 	[
				// 		"OUTPUT" => $args['message'] . "<br>Origin: yourwebsite.com"
				// 	]
				// )
	// 		)
	// 	{
	// 		$emailMsg = "Email sent!"; // If we got this far the email was sent
    //     }
    // }

	return [
		"EXAMPLE" => "Email",
		"NOTES"   => "This is a basic class for sending emails. It can send emails using PHPMailer, so install composer 
					 and PHPMailer. The folder vendor is registered automatically.",
		"OUTPUT"  => "<pre><code><b>You need these two snippets</b>\n\n" . Utils::htmlOut($html) . "\n\n" . Utils::htmlOut($js) . '</code></pre>'
	];
}