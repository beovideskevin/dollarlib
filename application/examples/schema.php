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
		"default": "checkSchemas"
	},

	"TEMPLATE": 
	{
		"LANGUAGE_PATH": "language/",
		"DEFAULT_LANGUAGE": "es.ini",
		"LAYOUT_PATH": "layout/",
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

	"SCHEMAS": {
		"user": {
			"id": "id",
			"relations": {
				"addressList": [
					"assoclist: SELECT * FROM address WHERE user = ?", "id"
				],
				"phone": [
					"obj: SELECT * FROM phone WHERE id = ?", "phone"
				]
			}
		},
        "phone": {
            "id": "id",
			"validation": "checkPhoneNumber"
        },
        "address": {
            "id": "id",
            "relations": {
                "relatedUser": [
                    "schema: SELECT * FROM user WHERE id = ?", "user", "user"
                ]
            }
        }
 	}
}

*/

function checkSchemas($args = []) 
{
	global $_;

	// Run the migrations, this will execute the code in migrations.sql, 
	// I  do this to fill the database with records 
	$_("migrations");

	// Using "schemas:" you get back an array of objects of type user
	$users = $_("schemas: select * from user", [], "user");
	$pre = "<b>Get all users:</b> \n" . print_r($users, true) . "\n\n";

	// Using "schema:" you just get the first user object returned by the query
	$user = $_("schema: select * from user", [], "user");
	$pre .= "<b>Get one user:</b> \n" . print_r($user, true) . "\n\n";
	
	$pepe = new Schemas("user"); // This is how you create a new user
	// You can access the properties one by one, or just assign them in bulk with an array
	$pepe->assign([
			"name" => "pepe"
	]);
	$pepe->save(); // this will insert the new object
	$pre .= "<b>New user pepe:</b> \n" . print_r($pepe, true) . "\n\n";

	$pepe->name = "pepe garcia"; // change the name 
	$pepe->save(); // this will update the new object
	$pre .= "<b>Updated user pepe:</b> \n" . print_r($pepe, true) . "\n\n";

	// In this case we are loading the user 1 as a schema, you have to be careful because if the user
	// loads the address as a schema too, you could en up creating a loop
	$address = new Schemas("address");
	$address->load(1); // This will get the address id == 1 from the database
	$pre .= "<b>Load address:</b> \n" . print_r($address, true) . "\n\n";

	$address->user = 2; // Change the user for this address
	$address->save(); // this will update this address, changing the user associated with
	$address->load(); // refresh the object so the new user is loaded
	$pre .= "<b>Updated address:</b> \n" . print_r($address, true) . "\n\n";

	$pepe->load(); // this will refresh the object, loading the related address
	$pre .= "<b>Refresh user pepe:</b> \n" . print_r($pepe, true) . "\n\n";

	// Get the first phone and test the validation
	$phone = $_("schema: select * from phone where id = ?", [1], "phone");
	$pre .= "<b>Get first phone:</b> \n" . print_r($phone, true) . "\n\n";
	$phone->number = "";
	$result = $phone->save();
	$pre .= "<b>Result of the update: </b>" . ($result ? "worked" : "failed") . "\n\n";
	$pre .= "<b>But the phone number is still empty: </b>" . $phone->number . "\n\n";
	$phone->load();
	$pre .= "<b>After reload:</b> \n" . print_r($phone, true) . "\n\n";

	return [
		"EXAMPLE" => "Schemas",
		"NOTES"   => "What I tried to do here is a class that works as a model, by providing relations and some methods like save and assign. 
		 			  Since I don't pretend to create a full MVC the functionality provided is very basic.",
		"OUTPUT"     => '<pre><code>' . $pre . '</code></pre>'
	];
}

// Validates the phone schema, the number can not be empty
function checkPhoneNumber($phone)
{
	return !empty($phone->number);
}