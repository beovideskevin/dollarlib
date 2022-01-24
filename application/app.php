<?php

function index($args) {
	global $_;
	$result = [];
	return $result;
}

function en ($args) {
	global $_;
	$_("setLang: en");
	return index($args);
}

function es ($args) {
	global $_;
	$_("setLang: es");
	return index($args);
}