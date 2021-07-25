<?php

/**
 * Manage the language
 */
function lan($args) 
{
    global $_;

    if (!empty($args['lan'])) {
        $_SESSION['LANGUAGE_IN_USE'] = $args['lan'];
        $_("setlang: " . $args['lan']);
    }
}

/**
 * Enforce the login
 */
function enforce () 
{
	if (empty($_SESSION['rent']) || empty($_SESSION['renter'])) {
		header('Location: /logout');
        die();
	}
}

/**
 * Log out
 */
function logout () 
{
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    header('Location: /login');
}

