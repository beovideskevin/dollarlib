<?php
class Rent extends Table
{
	function __construct () 
	{
		parent::__construct("rent", "id");
	}
}

class Renter extends Table
{
	function __construct () 
	{
		parent::__construct("renter", "id");
	}
}

class Receipt extends Table
{
	function __construct () 
	{
		parent::__construct("receipt", "id");
	}
}

