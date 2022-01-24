<?php

function index($args = []) {
	global $_;
	$result = [
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
					</div> '
	];
	return $result;
}

function en ($args = []) {
	global $_;
	$_("setLang: en");
	return index($args);
}

function es ($args = []) {
	global $_;
	$_("setLang: es");
	return index($args);
}