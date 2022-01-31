<?php

function index($args = []) 
{
	global $_;
	$results = [
		"OUTPUT" => '<div class="section" id="snippetSection">
						<div class="container">
							<div class="">
								<h4 id="mainTitle">SNIPPETS</h4>
							</div>
							<pre>
								<code>
								// Some code here
								</code>
							</pre>
						</div>
					</div>'
	];
	return $results;
}

function user ($args = []) 
{
	$results = [
		"JSON"  => json_encode(
			[
				"user" => [
					"user" => 1
				]
			]
		),
		"ERROR" => "<:ERR/>"
	];
	return $results;
}

function checkKey($args = []) 
{
	return !(empty($_SERVER['HTTP_APIKEY']) || $_SERVER['HTTP_APIKEY'] != "ca300a3dff5f1bbbee3878b41126cb4f");
}

function noResource () 
{
	$results = [
		"JSON"  => "",
		"ERROR" => "<:NOMETHODORRESOURCE/>"
	];
	return $results;
}

function txn ($args = []) 
{
	$results = [
		"JSON"  => json_encode(
			[
				"txn" => [
					"id" => 1, 
					"user" => 1,
					"amount" => "$100"
				]
			]
		),
		"ERROR" => "<:ERR/>"
	];
	return $results;
}

function en ($args = []) 
{
	global $_;
	$_("setLang: en.ini");
	return index($args);
}

function es ($args = [])
{
	global $_;
	$_("setLang: es.ini");
	return index($args);
}