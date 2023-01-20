<?php

/****************************************************************************************
 *
 * DollarLib - https://github.com/beovideskevin/dollarlib
 * Copyright (c) 2022 El Diletante Digital v1.0
 *
 * This is the main file of the framework and probably the only one you really need.
 *
 * DollarLib is free software: you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or    (at your option) any later version.
 *
 * DollarLib is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DollarLib. If not, see <http://www.gnu.org/licenses/>.
 *****************************************************************************************/

namespace DollarLib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * This class encapsulates a few useful methods
 */
class Utils
{
    /**
     * Gets the query and extracts the action, the first part of the query, then
     * extracts the real query, everything after the action
     * @param string $fullQuery the full query
     */
    static public function extractQuery($fullQuery)
    {
        $parts = explode(' ', trim($fullQuery));

        $action = self::trimLower($parts[0]);

        if ($action == ':')
            $query = substr($fullQuery, 1);
        else {
            // Clean every accidental space between the action and the :
            for ($i = 1, $c = count($parts); $i < $c; $i++) {
                if ($parts[$i])
                    break;
                unset($parts[$i]);
            }

            if ($i < $c && $parts[$i] == ':') {
                $action .= ':';
                unset($parts[$i]);
            }

            // don't forget to unset the action
            unset($parts[0]);

            $query = implode(' ', $parts);
        }

        return [$action, $query];
    }

    /**
     * Trim and "lowercase" a string, useful to compare usernames in the database
     * @param $var the string to be process
     */
    static public function trimLower($var)
    {
        if (is_array($var)) {
            foreach ($var as $key => $value)
                $var[$key] = self::trimLower($value);
        } else
            $var = strtolower(trim($var));

        return $var;
    }

    /**
     * Takes an array and turn al his keys lowercase recursively
     * @param array $item the input array
     */
    static public function trimLowerKeys($item = '')
    {
        if (is_array($item)) {
            foreach ($item as $key => $value) {
                unset($item[$key]);
                $item[self::trimLower($key)] = self::trimLowerKeys($value);
            }
            return $item;
        } else
            return $item;
    }

    /**
     * Just return the string as html output, with especial symbols (< to &lt;) and new lines (\n to <br>) converted
     */
    static public function htmlOut($str = '')
    {
        return nl2br(htmlspecialchars($str, ENT_QUOTES));
    }

    /**
     * Return a link properly formatted
     */
    static public function linkOut($urlpage = '', $getArgs = [])
    {
        $url = rawurlencode($urlpage);
        if ($getArgs && is_array($getArgs)) {
            $url .= '?';
            foreach ($getArgs as $key => $value)
                $url .= $key . '=' . urlencode($value);
        }

        return $url;
    }

    /**
     * Return the javascript code for output
     */
    static public function jsOut($str = '')
    {
        return addslashes(preg_replace('/(\r\n|\n|\r)/', '<br>', $str));
    }
}

/**
 * The interface that declares the methods a database adapter must have,
 * both PostgreSQLAdapter and MySQLAdapter use this interface.
 */
interface DbAdapter
{
    /**
     * Creates the connection to the database
     */
    public function __construct($server, $port, $user, $pass, $database);

    /**
     * Returns the connection link to the database
     */
    public function connection();

    /**
     * Disconnect
     */
    public function disconnect();

    /**
     * Clean up the query
     */
    public function sanitize($query);

    /**
     * Query the database
     */
    public function query($query);

    /**
     * Return the results
     */
    public function result($ret);
}

/**
 * The "driver" to connect and query PostgreSQL
 */
class PostgreSQLAdapter implements DbAdapter
{
    protected static $link = NULL,
        $result = NULL;

    /**
     * Creates the connection to the database
     * @param $server the server to connect to
     * @param $port the port to connect to
     * @param $user the user that connects to the database
     * @param $pass the password of the user
     * @param $database the database name
     */
    public function __construct($server, $port, $user, $pass, $database)
    {
        self::$link = pg_connect("host={$server} port={$port} dbname={$database} user={$user} password={$pass}");

        if (!self::$link)
            throw new \Exception('PostgreSQLAdapter: Could not connect!');
    }

    /**
     * Returns the link to the connection
     */
    public function connection()
    {
        return self::$link;
    }

    /**
     * Disconnect from the database
     */
    public function disconnect()
    {
        pg_close(self::$link);
    }

    /**
     * Clean the argument, sanitize it
     * @param $query to sanitize
     */
    public function sanitize($query)
    {
        return pg_escape_string(self::$link, $query);
    }

    /**
     * Query the database
     * @param $query the query to use
     */
    public function query($query)
    {
        self::$result = pg_query($query);

        return self::$result;
    }

    /**
     * Return the result of a query
     * @param $ret the type of result: insertid; single; obj; objs; assoc or assoclist
     */
    public function result($ret = '')
    {
        $rows = [];
        switch ($ret) {
            case 'insertid':
                // for this to work you need to add to the INSERT query:
                // RETURNING id

            case 'single':
                $tmp = pg_fetch_array(self::$result);
                return $tmp[0];

            case 'obj':
                return pg_fetch_object(self::$result);

            case 'objs':
                $num = pg_numrows(self::$result);
                for ($count = 0; $count < $num && $obj = pg_fetch_object(self::$result, $count); $count++)
                    $rows[] = $obj;

                return $rows;

            case 'assoc':
                return pg_fetch_assoc(self::$result);

            case 'assoclist':
                while ($row = pg_fetch_assoc(self::$result))
                    $rows[] = $row;

                return $rows;
        }
    }
}

/**
 * The "driver" to connect and query MySQL
 */
class MySQLAdapter implements DbAdapter
{
    protected static $link = NULL,
        $result = NULL;

    /**
     * Connect to the database
     * @param $server the server to connect to
     * @param $port the port to connect to
     * @param $user the user that connects to the database
     * @param $pass the password of the user
     * @param $database the database name
     */
    public function __construct($server, $port, $user, $pass, $database)
    {
        self::$link = new \mysqli($server, $user, $pass, $database, $port);

        if (!self::$link)
            throw new \Exception('MySQLAdapter: Could not connect!');
    }

    /**
     * Returns the link to the connection
     */
    public function connection()
    {
        return self::$link;
    }

    /**
     * Disconnect from the database
     */
    public function disconnect()
    {
        mysqli_close(self::$link);
    }

    /**
     * Clean the argument, sanitize it
     * @param $query to sanitize
     */
    public function sanitize($query)
    {
        return mysqli_real_escape_string(self::$link, $query);
    }

    /**
     * Query the database
     * @param $query the query to use
     */
    public function query($query)
    {
        self::$result = self::$link->query($query);

        return self::$result;
    }

    /**
     * Return the result of a query
     * @param $ret the type of result: insertid; single; obj; objs; assoc or assoclist
     */
    public function result($ret = '')
    {
        $rows = [];
        switch ($ret) {
            case 'insertid':
                return self::$link->insert_id;

            case 'single':
                $tmp = self::$result->fetch_row();
                return $tmp[0];

            case 'obj':
                return self::$result->fetch_object();

            case 'objs':
                while ($obj = self::$result->fetch_object())
                    $rows[] = $obj;

                return $rows;

            case 'assoc':
                return self::$result->fetch_assoc();

            case 'assoclist':
                while ($row = self::$result->fetch_array(MYSQLI_ASSOC))
                    $rows[] = $row;

                return $rows;
        }
    }
}

/**
 * This is the main class to connect to the database
 */
class Database
{
    private static $driver = NULL,
        $adapterList = [
        'mysql',
        'postgresql'
    ];
    public static $adapter = '',
        $host = '',
        $port = 0,
        $database = '',
        $user = '',
        $password = '',
        $migrations = '';

    /**
     * Establish a connection to the database
     */
    public function connect($settings = [])
    {
        if (!in_array(self::$adapter, self::$adapterList)) {
            return false;
        }

        // if the link is not empty, abort the previous connection
        if (self::$driver) {
            self::$driver->disconnect();
            self::$driver = NULL;
        }

        $server = $settings['host'] ?? self::$host;
        $port = $settings['port'] ?? self::$port;
        $user = $settings['user'] ?? self::$user;
        $pass = $settings['password'] ?? self::$password;
        $database = $settings['database'] ?? self::$database;
        $adapter = $settings['adapter'] ?? self::$adapter;

        try {
            if (Utils::trimLower($adapter) == 'mysql')
                self::$driver = new MySQLAdapter($server, $port, $user, $pass, $database);
            elseif (Utils::trimLower($adapter) == 'postgresql')
                self::$driver = new PostgreSQLAdapter($server, $port, $user, $pass, $database);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        return self::$driver->connection();
    }

    /**
     * Disconnect from the database
     */
    public function disconnect()
    {
        if (!self::$driver)
            return false;

        return self::$driver->disconnect();
    }

    /**
     * Clean the argument, sanitize it
     * @param $var query or array to sanitize
     */
    public function sanitize($var)
    {
        if (!self::$driver)
            return false;

        if (!$var)
            return $var;

        if (is_array($var)) {
            foreach ($var as $key => $subvar)
                $var[$key] = $this->sanitize($subvar);
        } else
            $var = self::$driver->sanitize($var);

        return $var;
    }

    /**
     * Query the database
     * @param $query the query to use
     * @param $args the arguments to use in the query
     * @param $ret the return type
     */
    public function query($query, $args = [], $ret = '')
    {
        if (!self::$driver)
            return false;

        if ($args) {
            $args = $this->sanitize($args);
            $query = preg_replace_callback('/\?/', function ($match) use (&$args) {
                return array_shift($args);
            }, $query);
        }

        if (!self::$driver->query($query)) {
            error_log('WOW (query): ' . $query);

            return false;
        } elseif ($ret) {
            $ret = Utils::trimLower($ret);
            return self::$driver->result($ret);
        }

        return true;
    }

    /**
     * Return the result of a query
     * @param $ret the type of result: insertid; single; obj; assoclist or assoc (this is the default)
     */
    public function result($ret = '')
    {
        if (!self::$driver)
            return false;

        $ret = Utils::trimLower($ret);
        return self::$driver->result($ret);
    }

    /**
     * Run the migrations in a SQL script to build the database
     * @param $filename the file where the query is stored
     */
    public function migrate($filename = '')
    {
        if (!self::$driver)
            return false;

        if ($filename)
            $query = file_get_contents(FILES_BASE_PATH . $filename);
        elseif (self::$migrations)
            $query = file_get_contents(self::$migrations);

        if (!$query)
            return false;

        $lines = explode(";", $query);
        foreach ($lines as $line) {
            if (!$line)
                continue;

            if (!$this->query(trim($line)))
                return false;
        }
        return true;
    }
}

/**
 * This class acts like a sort of a controller or router
 */
class Application
{
    const RESERVED = [
        "_404",
        "_args",
        "_call",
        "_default",
        '_delete',
        "_enforce",
        '_get',
        "_httperrors",
        "_language",
        "_layout",
        '_post',
        '_put',
        "_redirect",
        "_urlargs"
    ];

    protected static $config = [],
                     $default = "index",
                     $error404,
                     $httperrors = [],
                     $routes = [],
                     $includes = [
                        "EXCEPTIONS" => "",
                        "FOLDERS" => "",
                        "VENDORS" => ""
                     ];

    protected $action,
              $args = [],
              $enforce,
              $redirect;


    /**
     * Returns the configuration
     */
    public function getConfig($index = "")
    {
        if ($index && is_array($index)) {
            $result = [];
            foreach ($index as $i) {
                if (isset(self::$config[$i]))
                    $result[$i] = self::$config[$i];
            }
        } else {
            $result = $index && isset(self::$config[$index]) ? self::$config[$index] : self::$config;
        }

        return $result;
    }

    /**
     * Loads the configuration and registers the classes
     * @param $filepath the config file, if empty gets the default config.json
     */
    public function setConfig($filepath = '')
    {
        $filepath = $filepath ? $filepath : 'config.json';

        self::$config = json_decode(file_get_contents($filepath), true);

        if (!self::$config)
            die ('No configuration file or error while parsing it!');

        // Get the environment and set it as root index in the config array
        if (isset(self::$config['ENV'])) {
            $env = self::$config['ENV'];
            if (empty(self::$config[$env]))
                die ('No configuration for this environment!');
            self::$config = self::$config[$env];
        }

        foreach (self::$config as $key => $value) {
            switch (Utils::trimLower($key)) {
                // the main domain
                case 'website':
                    DEFINE('WEBSITE', $value);
                    break;

                // the main path
                case 'files_base_path':
                    DEFINE('FILES_BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . $value);
                    DEFINE('FILES_RELATIVE_PATH', $value);
                    break;

                // the path for the classes to include
                case 'register':
                    foreach (self::$config[$key] as $regKey => $regVal) {
                        switch (Utils::trimLower($regKey)) {
                            case 'exceptions':
                                self::$includes['EXCEPTIONS'] = $regVal;
                                break;

                            case 'folders':
                                self::$includes['FOLDERS'] = $regVal;
                                break;

                            case 'vendors':
                                self::$includes['VENDORS'] = $regVal;
                                break;
                        }
                    }
                    break;

                // set the routes
                case 'routes':
                    foreach (self::$config[$key] as $rKey => $rVal) {
                        switch (Utils::trimLower($rKey)) {
                            case '_default':
                                self::$default = Utils::trimLowerKeys($rVal);
                                break;

                            case '_404':
                                self::$error404 = Utils::trimLowerKeys($rVal);
                                break;

                            case '_httperrors':
                                self::$httperrors = Utils::trimLowerKeys($rVal);
                                break;

                            default:
                                self::$routes[Utils::trimLower($rKey)] = Utils::trimLowerKeys($rVal);
                                break;
                        }
                    }
                    break;

                // set the default template and language
                case 'template':
                    foreach (self::$config[$key] as $tKey => $tVal) {
                        switch (Utils::trimLower($tKey)) {
                            case 'layout_path':
                                DEFINE('LAYOUT_PATH', $tVal);
                                break;

                            case 'default_layout':
                                Template::$defaultLayout = $tVal;
                                break;

                            case 'language_path':
                                DEFINE('LANGUAGE_PATH', $tVal);
                                break;

                            case 'default_language':
                                Template::$defaultLanguage = $tVal;
                                break;
                        }
                    }
                    break;

                // Database connection data
                case 'database':
                    foreach (self::$config[$key] as $dbKey => $dbVal) {
                        switch (Utils::trimLower($dbKey)) {
                            case 'adapter':
                                Database::$adapter = $dbVal;
                                break;
                            case 'host':
                                Database::$host = $dbVal;
                                break;
                            case 'port':
                                Database::$port = $dbVal;
                                break;
                            case 'database':
                                Database::$database = $dbVal;
                                break;
                            case 'user':
                                Database::$user = $dbVal;
                                break;
                            case 'password':
                                Database::$password = $dbVal;
                                break;
                            case 'migrations':
                                Database::$migrations = $dbVal;
                        }
                    }
                    break;

                // SMTP email configuration
                case 'email':
                    foreach (self::$config[$key] as $eKey => $eVal) {
                        switch (Utils::trimLower($eKey)) {
                            case 'system':
                                Email::$system = $eVal;
                                break;
                            case 'from':
                                Email::$from = $eVal;
                                break;
                            case 'server':
                                Email::$server = $eVal;
                                break;
                            case 'port':
                                Email::$port = $eVal;
                                break;
                            case 'user':
                                Email::$user = $eVal;
                                break;
                            case 'password':
                                Email::$password = $eVal;
                                break;
                            case 'layout':
                                Email::$layout = $eVal;
                                break;
                        }
                    }
                    break;
            }
        }

        // if the website, main path, etc. are not set, lets set the default
        if (!defined('WEBSITE'))
            DEFINE('WEBSITE', '/');

        if (!defined('FILES_BASE_PATH')) {
            DEFINE('FILES_BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/');
            DEFINE('FILES_RELATIVE_PATH', '/');
        }

        if (!defined('LAYOUT_PATH'))
            DEFINE('LAYOUT_PATH', 'layout/');

        if (!defined('LANGUAGE_PATH'))
            DEFINE('LANGUAGE_PATH', 'language/');

        // overwrite the default language using the session
        if (!empty($_SESSION['LANGUAGE_IN_USE']))
            Template::$defaultLanguage = $_SESSION['LANGUAGE_IN_USE'];
    }

    /**
     * This method recursively includes all the files and classes
     * @param $folder the folder where to start
     * @param $exceptions files not to include
     * @param $first flag to indicate that this is the first call to the method
     */
    public function register($folder = "", $exceptions = [])
    {
        // The first time we are going to do som general including
        if (!$folder) {
            // Include the vendors
            if (!empty(self::$includes['VENDORS']) && is_dir(FILES_BASE_PATH . self::$includes['VENDORS']) &&
                file_exists(FILES_BASE_PATH . self::$includes['VENDORS'] . 'autoload.php')) {
                require_once(FILES_BASE_PATH . self::$includes['VENDORS'] . 'autoload.php');
            }

            // register all the classes
            if (!empty(self::$includes['FOLDERS'])) {
                $folders = explode(';', self::$includes['FOLDERS']);

                $exceptions = empty(self::$includes['EXCEPTIONS']) ? [] : explode(';', self::$includes['EXCEPTIONS']);

                if (!in_array('$_', $exceptions))
                    $exceptions[] = '$_';

                foreach ($folders as $f) {
                    if (!$f)
                        continue;
                    $this->register(FILES_BASE_PATH . $f, $exceptions);
                }
            }
        } else {
            $otherFiles = [];

            $files = scandir($folder);
            foreach ($files as $f) {
                if ($f == '.' || $f == '..' || in_array($f, $exceptions))
                    continue;
                elseif (is_dir($folder . $f)) {
                    if ($f == 'vendor' && file_exists($folder . 'vendor/autoload.php'))
                        continue;
                    else
                        $otherFiles[] = $folder . $f;
                } elseif (substr($f, -4) == '.php')
                    include_once($folder . $f);
            }

            if ($otherFiles)
                foreach ($otherFiles as $of)
                    $this->register($of . '/', $exceptions);
        }
    }

    /**
     * Gets some info like layout, language, etc. that cascades down
     * @param $action
     */
    public function process($action)
    {
        // Assign the action first
        $this->action = $action;

        // get the arguments
        if (isset($action['_args']))
            $this->args = json_decode($action['_args'], true);

        // private area of the website
        if (isset($action['_enforce']))
            $this->enforce = $action['_enforce'];

        // if there is a redirect, every route under this one will be redirected
        if (isset($action['_redirect']))
            $this->redirect = $action['_redirect'];

        // set the layout
        if (isset($action['_layout']))
            Template::$defaultLayout = $action['_layout'];

        // set the language
        if (isset($action['_language']))
            Template::$defaultLanguage = $action['_language'];
    }

    /**
     * Routes the app, that is calls the function or method inside a class that is requested
     * @param $urlpath the path to map as a function or method call
     * @param $args the arguments passed to the method or function
     */
    public function route($urlpath = '', $args = [])
    {
        if (!$urlpath && isset($_REQUEST['_url']))
            $urlpath = $_REQUEST['_url'];

        if (isset(self::$httperrors[$urlpath])) {
            http_response_code(self::$httperrors[$urlpath]);
            die();
        }

        // Split the url in chunks by the /
        $url = array_filter(explode('/', Utils::trimLower($urlpath)));

        // if there is no _url put the default
        if (!$url)
            $this->action = self::$default;
        else {
            $this->action = self::$routes;
            // loop over the url parts, find the index in the routes and process the action
            for ($index = 0; $index < count($url) && is_array($this->action); $index++) {
                foreach ($this->action as $key => $value) {
                    if (in_array($key, self::RESERVED))
                        continue;

                    if ($key == Utils::trimLower($url[$index]) ||
                        strpos($key, Utils::trimLower($url[$index]) . "/") === 0)
                    {
                        $this->process($value);
                        $tmpURL = explode("/:", rtrim($key, '/'));
                        for ($j = 1; $j < count($tmpURL) && ++$index < count($url); $j++)
                            $this->args[ltrim($tmpURL[$j], ':')] = $url[$index];
                        continue 2;
                    }
                }

                // the url is not an index in the routes, so 404
                $this->action = self::$error404 ?? self::$default;
                break;
            }
        }

        // redirect to another url
        if (!empty($this->redirect)) {
            header('Location: ' . $this->redirect);
            die();
        }

        $args = array_merge(
            $args,
            $this->args,
            $_REQUEST,
            ["BODY" => json_decode(file_get_contents('php://input'), true)]
        );

        // if the result is still an array, get the action function
        if (is_array($this->action)) {
            $methods = array_intersect(['_get', '_post', '_put', '_delete'], array_keys($this->action));
            if (in_array(strtolower("_" . $_SERVER['REQUEST_METHOD']), $methods))
                $this->action = $this->action[strtolower("_" .$_SERVER['REQUEST_METHOD'])];
            else if (!empty($this->action['_call']))
                $this->action = $this->action['_call'];
        }

        // validate main action and call the function that enforces login
        if (!is_string($this->action) || !is_callable($this->action) ||
            ($this->enforce && is_callable($this->enforce) && !call_user_func($this->enforce)))
        {
            // there is something wrong here...
            http_response_code(403);
            die();
        } else {
            // finally, call the target function
            return call_user_func($this->action, $args);
        }
    }
}

/**
 * This class takes care of the view
 */
class Template
{
    public static $defaultLayout = '',
                  $defaultLanguage = '';

    protected $fullLayout = '',
              $fullLanguage = [];

    /**
     * Set the layout
     * @param $layout the file to get the layout from, if empty the default is set
     */
    public function setLayout($layout = '')
    {
        $filename = LAYOUT_PATH . ($layout ? $layout : self::$defaultLayout);

        $this->fullLayout = $this->inject($filename);

        return $this->fullLayout;
    }

    /**
     * Get the layout, just returns the layout
     */
    public function getLayout()
    {
        return $this->fullLayout;
    }

    /**
     * Set the language of the template
     * @param $language the file where the language is stored
     */
    public function setLang($language = '')
    {
        $filename = FILES_BASE_PATH . LANGUAGE_PATH . ($language ? $language : self::$defaultLanguage);

        $allLines = file($filename);

        $this->fullLanguage = [];
        foreach ($allLines as $line) {
            $line = trim($line);
            if (!$line)
                continue;
            $keyValue = explode('=>', $line);
            if ($keyValue[0][0] == '#' || empty($keyValue[1]))
                continue;
            $key = trim(array_shift($keyValue));
            $this->fullLanguage[$key] = implode('=>', $keyValue);
        }

        $_SESSION['LANGUAGE_IN_USE'] = $language ? $language : self::$defaultLanguage;
        $this->fullLanguage['FILES_BASE_PATH'] = FILES_BASE_PATH;
        $this->fullLanguage['FILES_RELATIVE_PATH'] = FILES_RELATIVE_PATH;

        return $this->fullLanguage;
    }

    /**
     * Get language or a specific index of the language array
     * @param $props the index of the language array or if empty returns the whole thing
     */
    public function getLang($props = '')
    {
        if (!$this->fullLanguage)
            $this->setLang();

        if (!$props)
            $res = $this->fullLanguage;
        elseif (is_array($props)) {
            $res = [];
            foreach ($props as $p)
                if (isset($this->fullLanguage[$p]))
                    $res[$p] = $this->fullLanguage[$p];
        } elseif (isset($this->fullLanguage[$props]))
            $res = $this->fullLanguage[$props];
        else
            $res = false;

        return $res;
    }

    /**
     * Apply a language to the template
     * @param $html the html template to process
     * @param $allDefs all the indexes to substitute in the template
     * @param $clean if true remove all the not used anchors in teh template
     */
    protected function apply($html, $allDefs, $clean = false)
    {
        foreach ($allDefs as $name => $content) {
            $content = addcslashes($content, '\\$'); // this is escaping the $ in the string
            $html = preg_replace('/\<:' . $name . '\/\>/', $content, $html);
            $html = preg_replace('/{{' . $name . '}}/', $content, $html);
        }

        if ($clean) {
            $html = preg_replace('/\<:(.*)\/\>/', '', $html);
            $html = preg_replace('/{{((?:[^}]|}[^}])+)}}/', '', $html);
        }

        return $html;
    }

    /**
     * This method is useful to inject code in a template
     * @param $filename
     * @param $allDef all the indexes to substitute in the template
     * @param $clean if true remove all the not used anchors in teh template
     */
    public function inject($filename, $allDef = [], $clean = false)
    {
        $filename = FILES_BASE_PATH . $filename;

        if (!file_exists($filename))
            return '';

        $code = file_get_contents($filename);

        if ($code === FALSE)
            return '';

        if ($allDef)
            $code = $this->apply($code, $allDef, $clean);

        return $code;
    }

    /**
     * Renders the template with the definitions of the results
     * @param $results the values to insert in the template
     */
    public function render($results = [])
    {
        if (is_string($results))
            return $results;

        if (!$this->fullLayout)
            $this->setLayout();

        if (!$this->fullLanguage)
            $this->setLang();

        // Apply the results first to the layout
        $tmpHtml = $this->apply($this->fullLayout, $results);

        // Now apply the default language. This second call is necessary because inside de definitions of $results
        // we can put other language tags <:/>, exmaple: "BTN" => "<input type='submit' value='<:SUBMIT_BTN/>'>"
        return $this->apply($tmpHtml, $this->fullLanguage, true);
    }
}

/**
 * Class to send emails
 */
class Email
{
    public static $system = '',
                  $from = '',
                  $server = '',
                  $port = '',
                  $user = '',
                  $password = '',
                  $layout = '';

    /**
     * The main method of the class, use this one to send emails
     * @param $emailto the destinatary email, it can be empty if passing emailto in the params array
     * @param $params this is an array with the index/values pair for emailto, subject, emailfrom and name from, or just a string with the subject of the email
     * @param $content the content of the email
     * @param $language if you want to use a specific language in the email
     */
    function sendEmail($emailto, $params, $content, $language = '', $emailfrom = '', $namefrom = '')
    {
        $pattern = ['/\n/', '/\r/', '/content-type:/i', '/to:/i', '/from:/i', '/cc:/i'];

        if ($emailto) {
            $emailto = preg_replace($pattern, '', $emailto);
        }

        if ($emailfrom) {
            $emailfrom = preg_replace($pattern, '', $emailfrom);
        }

        if ($namefrom) {
            $namefrom = preg_replace($pattern, '', $namefrom);
        }

        $subject = "";
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                switch ($key) {
                    case 'emailto':
                        $emailto = preg_replace($pattern, '', $value);
                        break;
                    case 'subject':
                        $subject = preg_replace($pattern, '', $value);
                        break;
                    case 'emailfrom':
                        $emailfrom = preg_replace($pattern, '', $value);
                        break;
                    case 'namefrom':
                        $namefrom = preg_replace($pattern, '', $value);
                        break;
                }
            }
        } else {
            $subject = preg_replace($pattern, '', $params);
        }

        $body = "";
        if (is_array($content)) {
            $template = new Template();
            if ($language)
                $template->setLang($language);
            $template->setLayout(self::$layout);
            $body = wordwrap($template->render($content));
        } else {
            $body = wordwrap($content);
        }

        if (class_exists('PHPMailer\PHPMailer\PHPMailer') && self::$server && self::$port &&
            self::$user && self::$password) {
            // if SMTP email sending is allowed use PHPMailer
            return $this->sendPHPMailer($emailto, $subject, $body, $emailfrom, $namefrom);
        } else {
            // sent the email with php mail
            return $this->sendMail($emailto, $subject, $body, $emailfrom, $namefrom);
        }
    }

    /**
     * Send email over SMTP
     * @param $emailto the email to send the email to
     * @param $subject the subject of the email
     * @param $body the full body of the email
     */
    protected function sendPHPMailer($emailto, $subject, $body, $emailfrom = '', $namefrom = '')
    {
        $mail = new PHPMailer();

        // for debug only
        // $mail->SMTPDebug = 3;
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $mail->isSMTP();
        $mail->Host = self::$server;
        $mail->Port = self::$port;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->CharSet = 'UTF-8';

        // set the username and password
        $mail->Username = self::$user;
        $mail->Password = self::$password;

        $emailfrom = $emailfrom ? $emailfrom : self::$system;
        $namefrom = $namefrom ? $namefrom : self::$from;

        // set the email the subject and the content
        $mail->setFrom($emailfrom, $namefrom);
        $mail->addAddress($emailto);
        $mail->Subject = $subject;
        $mail->IsHTML(true);
        $mail->Body = $body;
        $mail->AltBody = strip_tags(str_replace('<br>', '\n', $body));

        // send email
        if (!$mail->send()) {
            error_log('Message could not be sent');
            error_log('Mailer Error: ' . $mail->ErrorInfo);
            return false;
        }

        error_log('Mailer Success: ' . print_r($mail, true));
        return true;
    }

    /**
     * Send email
     * @param $emailto the email to send the email to
     * @param $subject the subject of the email
     * @param $body the full body of the email
     */
    protected function sendMail($emailto, $subject, $body, $emailfrom, $namefrom)
    {
        $from = self::$from . ' <' . self::$system . '>';

        // create a boundary for the email. This
        $boundary = uniqid('ch');

        // set the headers
        $headers = 'MIME-Version: 1.0' . "\n";
        $headers .= 'From: ' . $from . "\n";
        $headers .= 'Reply-To: ' . $namefrom . "<" . $emailfrom . ">\n";
        $headers .= 'Content-Type: multipart/alternative;boundary=' . $boundary . "\n";

        // set the body and the txt version
        $message = "\n\n--" . $boundary . "\n";
        $message .= 'Content-type: text/plain;charset=utf-8' . "\n\n";
        $message .= strip_tags(str_replace('<br>', "\n", $body));
        $message .= "\n\n--" . $boundary . "\n";
        $message .= 'Content-type: text/html;charset=utf-8' . "\n\n";
        $message .= $body;
        $message .= "\n\n--" . $boundary . '--';

        // send email
        return mail($emailto, $subject, $message, $headers);
    }
}

/**
 * Class to send calls over the internet
 */
class Curl
{
    private $handle = null,
            $lastUrl = '';

    /**
     * Method to send a call over the internet
     * @param $method the method of the call (GET, POST, PUT, DELETE)
     * @param $url the url of the curl call
     * @param $request the body of the request for POST, PUT, etc
     * @param $headers the headers of the curl call
     * @param $options more options for the curl call ()
     * @param $connectTimeout the amount of time to the timeout
     */
    public function sendHttp($url, $method, $request = '', $headers = array(), $options = array(), $connectTimeout = 30)
    {
        $ret = array(
            'result' => false,
            'code' => 0
        );

        // if arguments are incorrect type, return unsuccessful
        if (!$method || !$url) {
            return $ret;
        }

        // if this is a new url, reset the handle and set the lastUrl
        if ($url !== $this->lastUrl) {
            if ($this->handle) {
                curl_close($this->handle);
            }
            $this->handle = curl_init($url);
            $this->lastUrl = $url;
        }

        if (is_array($request)) {
            foreach ($request as $key => $value) {
                switch (Utils::trimLower($key)) {
                    case 'headers':
                        $headers = $value;
                        unset($request[$key]);
                        break;
                    case 'options':
                        $options = $value;
                        unset($request[$key]);
                        break;
                    case 'connectTimeout':
                        $connectTimeout = $value;
                        unset($request[$key]);
                        break;
                }
            }

            if (isset($request['request'])) {
                $request = $request['request'];
            }
        }

        // if $request is still an array use http_build_query
        if (is_array($request)) {
            $request = http_build_query($request);
        }

        // setup the request method and (optional) data
        switch ($method) {
            case 'GET':
                // don't do anything
                break;

            case 'PUT':
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, $request);
                curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "PUT");
                break;

            case 'POST':
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, $request);
                curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "POST");
                break;

            case 'DELETE':
                curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;

            default:
                return $ret;
        }

        if ($headers) {
            // set the headers
            $curlHeaders = array();
            foreach ($headers as $header => $value) {
                $curlHeaders[] = "{$header}: {$value}";
            }
            curl_setopt($this->handle, CURLOPT_HTTPHEADER, $curlHeaders);
        }

        if ($options) {
            // set the options
            foreach ($options as $key => $option) {
                curl_setopt($this->handle, $key, $option);
            }
        }

        // set the timeout
        curl_setopt($this->handle, CURLOPT_TIMEOUT, $connectTimeout);

        // return a string instead of outputting
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);

        // execute the call
        $result = curl_exec($this->handle);

        $ret['result'] = $result;
        $ret['code'] = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);

        // this function have to be called several times because the messages are queued
        while (openssl_error_string()) ;

        return $ret;
    }
}

/**
 * This is a quick access to the main features
 * @param $query this is the main query, usually in this syntax "action: query"
 * @param $options this options to insert in the query
 * @param $extras more options basically
 */
$_ = function ($query = '', $options = [], $extras = '') {
    static $app = NULL,
    $database = NULL,
    $template = NULL,
    $email = NULL,
    $curl = NULL;

    $app = $app ?? new Application();
    $database = $database ?? new Database();
    $template = $template ?? new Template();
    $email = $email ?? new Email();
    $curl = $curl ?? new Curl();

    // first get the parts of the query into an array
    list($action, $query) = Utils::extractQuery($query);

    // check if the action is defined
    switch ($action) {
        /*********************
         * Express Init & Run
         *********************/
        // run the app, automatic and simple
        case 'run':
            $app->setConfig();
            $app->register();
            $database->connect();
            // if the router returned null pass an empty array to the render,
            // this avoids warnings from php
            die ($template->render($app->route() ?? []));

        /*********************
         * APP ACTIONS
         *********************/

        // load the config from some file
        case 'setconfig:':
            $options = $query;

        // set the config
        case 'setconfig':
            return $app->setConfig($options);

        // Get a specific index in the configuration
        case 'getconfig:':
            $options = $query;

        // Get the configuration
        case 'getconfig':
            return $app->getConfig($options);

        // Register other dependencies and libs
        case 'register:':
            $extras = $options;
            $options = $query;

        // Register the dependencies
        case 'register':
            return $app->register($options, $extras);

        // Route to some path
        case 'route:':
            $options = $query;

        // Route the app
        case 'route':
            return $app->route($options);

        /*********************
         * TEMPLATE ACTIONS
         *********************/

        // get the full layout
        case 'getlayout':
            return $template->getLayout();

        // set layout from some specific file
        case 'setlayout:':
            $options = $query;

        // set layout
        case 'setlayout':
            return $template->setLayout($options);

        // get some of the value sof the language
        case 'getlang:':
            $options = $query;

        // get the language
        case 'getlang':
            return $template->getLang($options);

        // set language to some specific file
        case 'setlang:':
            $options = $query;

        // set language
        case 'setlang':
            return $template->setlang($options);

        // inject a file form the query
        case 'inject:':
            return $template->inject($query, $options, $extras ? $extras : false);

        // render the results
        case 'render':
            return $template->render($options);

        /*********************
         * DATABASE ACTIONS
         *********************/

        // connect to the database
        case 'connect':
            return $database->connect($options);

        case 'disconnect':
            return $database->disconnect();

        // run the migrations
        case 'migrations:':
            $options = $query;

        case 'migrations':
            return $database->migrate($options);

        // sanitize the values
        case 'sanitize':
            return $database->sanitize($options);

        case 'insertid:': // When using postgresql add RETURNING id to the query
        case 'single:':
        case 'obj:':
        case 'objs:':
        case 'assoclist:':
        case 'assoc:':
            $extras = substr($action, 0, -1);

        // run a literal query
        case 'query:':
        case ':':
            return $database->query($query, $options, $extras);

        // A couple of short hand notations
        case 'count:':
            return $database->query("SELECT count(*) FROM " . $query, $options, "single");

        case '*:':
            return $database->query("SELECT * FROM " . $query, $options, "assoclist");

        // get the results form the query
        case 'results:':
            $options = $query;

        // get the results from the query using the options
        case 'results':
            return $database->result($options, $extras);

        /*********************
         * EMAIL ACTIONS
         *********************/

        // send email
        case 'email:':
            return $email->sendEmail($query, $options, $extras);
            break;

        /*********************
         * CURL ACTIONS
         *********************/

        // send curl call
        case 'curl:':
            return $curl->sendHttp($query, $options, $extras);
            break;

        /*********************
         * Unknown action,
         * return error
         *********************/

        default:
            error_log('WOW (not an option): ' . $action . ' \n query: ' . print_r($query, true) . '\n options: ' . print_r($options, true));
            return false;
    }
};