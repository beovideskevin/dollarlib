<?php

function index($args = []) 
{
	global $_;

	$results = [
		"EXAMPLE" => 'DollarLib',
		"NOTES"   => 'DollarLib es un framework de php muy ingenuo. Lo uso solamente para mis propios sitios web. Puedes ver el código completo en github: 
					  <a href="https://github.com/beovideskevin/dollarlib" target="_blank">https://github.com/beovideskevin/dollarlib</a>.',
		"OUTPUT"  => "<h4>README.md</h4><pre><code>".Utils::htmlOut(file_get_contents(FILES_BASE_PATH . "/../README.md")) ."</code></pre>" 
	];
	return $results;
}

