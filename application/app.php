<?php

// The default entry point is the function index
function index($args = []) 
{
	global $_;

	$results = [
		"EXAMPLE" => 'DollarLib',
		"NOTES"   => 'Dollarlib is a simple php lib, or a naive and flexible framework. I just use it for my own websites. Check the code at github: 
					  <a href="https://github.com/beovideskevin/dollarlib" target="_blank">https://github.com/beovideskevin/dollarlib</a>.',
		"OUTPUT"  => "<h4>README.md</h4><pre><code style='overflow: auto;'>" . htmlspecialchars(file_get_contents(FILES_ABSOLUTE_PATH . "/README.md")) ."</code></pre>" 
	];
	return $results;
}
