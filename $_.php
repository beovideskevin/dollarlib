<?php

/****************************************************************************************

	DollarLib - https://github.com/beovideskevin/my2cents
	Copyright (c) 2019 El Diletante Digital

	This is the main file of the framework and probably the only one you really need.
	
	My2cents is free software: you can redistribute it and/or modify it under the terms 
	of the GNU General Public License as published by the Free Software Foundation, 
	either version 3 of the License, or	(at your option) any later version.

	My2cents is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
	without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
	PURPOSE.  See the GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with My2cents. If not, see <http://www.gnu.org/licenses/>.

*****************************************************************************************/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
	
/**
 * This class encapsulates a few useful methods 
 */
Class Utils {
	/**
	 * Gets the query and extracts the action, the first part of the query, then
	 * extracts the real query, everything after the action
	 * @param string $fullQuery the full query
	 */
	static public function extractQuery($fullQuery) 
	{
		$parts = explode (' ', trim($fullQuery));
		
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

			// dont forget to unset the action
			unset($parts[0]);

			$query = implode(' ', $parts);
		}
			
		return [$action, $query];
	}

	/**
	 * Trim and "lowercase" a string, useful to compare usernames in the database 
	 * @param string $str the string to be process
	 */
	static public function trimLower ($str) 
	{
		return strtolower(trim($str));
	}

	/**
	 * Takes an array and turn al his keys lowercase recursively
	 * @param array $item the input array
	 */
	static public function trimLowerKeys ($item = '') {
		if (is_array($item)) {
			foreach ($item as $key => $value) {
				unset($item[$key]);
				$item[self::trimLower($key)] = self::trimLowerKeys($value);
			}
			return $item;
		}	
		else
			return $item;
	}
}

/**
 * The "driver" to connect and query PostgreSQL
 */
class PostgreSQLAdapter 
{
	protected static $link = NULL, 
				     $result = NULL; 

	/**
	 * @param $server the server to connect to 
	 * @param $port the port to connect to
	 * @param $user the user that connects to the database
	 * @param $pass the password of the user
	 * @param $database the database name 
 	 */ 
	public function connect($server, $port, $user, $pass, $database) 
	{
		self::$link = pg_connect("host={$server} port={$port} dbname={$database} user={$user} password={$pass}");
		
		if (!self::$link) 
			die('Could not connect!');
		
		return self::$link;
	}
	
	/**
	 * Disconnect from the database
 	 */ 
	public function disconnect () 
	{
		pg_close(self::$link);
	}
	
	/**
	 * Clean the argument, sanitize it
	 * @param $var 
 	 */ 
	public function sanitize ($var) 
	{
		return pg_escape_string(self::$link, $var);
	}
	
	/**
	 * Query the database
	 * @param $query the query to use
 	 */ 
	public function query ($query)
	{
		self::$result = pg_query($query);
		
		return self::$result;
	}
	
	/**
	 * Return the result of a query
	 * @param $ret the type of result: insertid; single; obj; assoclist or assoc (this is the default)
 	 */ 
	public function result ($ret = 'assoclist')
	{
		$ret = Utils::trimLower($ret);
		
		switch ($ret) {
			case 'insertid':
				// for this to work you need to add to the INSERT query:
				// RETURNING id
			case 'single':
				$tmp = pg_fetch_array(self::$result);
				return $tmp[0];
				
			case 'obj':
				return pg_fetch_object(self::$result);

            case 'assoc':
                return pg_fetch_assoc(self::$result);

            case 'assoclist':
            default:
				$rows = [];
				while($row = pg_fetch_assoc(self::$result))
					$rows[] = $row;
				return $rows;
		}
	}
}

/**
 * The "driver" to connect and query MySQL
 */
class MySQLAdapter 
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
	public function connect($server, $port, $user, $pass, $database) 
	{
		self::$link = new mysqli($server, $user, $pass, $database, $port);
		
		if (!self::$link) 
			die('Could not connect!');
		
		return self::$link;
	}
	
	/**
	 * Disconnect from the database
 	 */ 
	public function disconnect () 
	{
		mysqli_close(self::$link);
	}
	
	/**
	 * Clean the argument, sanitize it
	 * @param $var 
 	 */ 
	public function sanitize ($var) 
	{	
		return mysqli_real_escape_string(self::$link, $var);
	}
	
	/**
	 * Query the database
	 * @param $query the query to use
 	 */ 
	public function query ($query)  
	{
		self::$result = self::$link->query($query);
		
		return self::$result;
	}
	
	/**
	 * Return the result of a query
	 * @param $ret the type of result: insertid; single; obj; assoclist or assoc (this is the default)
 	 */ 
	public function result ($ret = 'assoclist')
	{
		$ret = Utils::trimLower($ret);

		switch ($ret) {
			case 'single':
				$tmp = self::$result->fetch_row();
				return $tmp[0];

			case 'insertid':
				return self::$link->insert_id;

			case 'obj':
				return self::$result->fetch_object();

			case 'assoc':
                return self::$result->fetch_assoc();

            case 'assoclist':
            default:
                $rows = [];
                while($row = self::$result->fetch_array(MYSQLI_ASSOC))
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
	public static $driver = NULL, 
				  $adapter = '', 
				  $host = '', 
				  $port = 0, 
				  $database = '', 
				  $user = '', 
				  $password = '';

	/**
	 * Establish a connection to the databse
 	 */ 
	public function connect () 
	{
		// if the link is not empty, abort the previous connection
		if (!empty(self::$driver)) { 
			self::$driver->disconnect();
			self::$driver = NULL;
		}

		$server = self::$host;
		$port = self::$port;
		$user = self::$user;
		$pass = self::$password;
		$database = self::$database;
		
		if (Utils::trimLower(self::$adapter) == 'mysql')
			self::$driver = new MySQLAdapter();
		elseif (Utils::trimLower(self::$adapter) == 'postgresql')
			self::$driver = new PostgreSQLAdapter();
		else
			die ('No adapter for the database!');

		return self::$driver->connect($server, $port, $user, $pass, $database);
	}
	
	/**
	 * Disconnect from the database
 	 */ 
	public function disconnect () 
	{
		self::$driver->disconnect();
	}
	
	/**
	 * Clean the argument, sanitize it
	 * @param $var 
 	 */ 
	public function sanitize ($var) 
	{
		if (empty($var)) 
			return $var;

		if (is_array($var)) {
			foreach ($var as $key => $subvar) 
				$result[$key] = $this->sanitize($subvar);

			$var = $result;
		}
		else 
			$var = self::$driver->sanitize($var);
		
		return $var;
	}
		
	/**
	 * Query the database
	 * @param $query the query to use
 	 */ 
	public function query ($query, $args = [], $ret = '') 
	{
		if (!empty($args)) {
			$args = $this->sanitize($args);
			$query = preg_replace_callback( '/\?/', function( $match) use( &$args) {
				return "'" . array_shift($args) . "'";
			}, $query);
		}
		
		if (!self::$driver->query($query)) {
			error_log('WOW (query): ' . $query);
			
			return false;
		}
		elseif (!empty($ret))
			return self::$driver->result($ret);

		return true;
	}
	
	/**
	 * Return the result of a query
	 * @param $ret the type of result: insertid; single; obj; assoclist or assoc (this is the default)
 	 */ 
	public function result ($ret = '') 
	{
		return self::$driver->result($ret);
	}
}

/**
 * This class works as a model. To add functionality to this table class just implement validations. 
 */
class Table extends Database
{
	protected $_table, 
		      $_id;

	/**
	 * Create a 
	 * @param $table the table we are using
	 * @param $id the id field 
 	 */ 
	function __construct ($table, $id = false) 
	{
		if (!$table) 
			throw new Exception('ERROR: NO TABLE');
		
		$this->_table = $table;
		$this->_id = $id;
	}
	
	/** 
	 * Assign values to the object
	 * @param $properties an array with the properties to assign to the object
	 */
	public function assign ($properties = []) 
	{
		if (!$properties)
			return false;

		foreach ($properties as $key => $value)
			$this->$key = $value;
		
		if (is_callable([$this, 'validate']) && !$this->validate())
			return false;
					
		if (is_callable([$this, 'relate']) && !$this->relate())
			return false;

		return true;
	}

	/**
	 * Sync the current object with the database, get the info from the database
	 * @param $id the id of the record
	 */
	public function sync ($id = '') 
	{
		global $_;

		$idName = $this->_id;
		
		$id = $id ? $id : $this->$idName;
		
		$result = $_("assoc: SELECT * FROM {$this->_table} WHERE {$idName} = '?' LIMIT 1", [$id]);
		
		if ($result) {
			return $this->assign($result);
		}
		
		return false;
	}

	/**
	 * Save the model, insert or update a record
	 * @param $properties an array with the properties to assign to the object
 	 */ 
	public function save ($properties = []) 
	{ 
		global $_;

		// assign data if there is any
		if ($properties) {
			if (!$this->assign($properties))
				return false;
			$recordProps = $properties;
		}
		else 
			$recordProps = get_object_vars($this);
		
		$idName = $this->_id;
		$tableName = $this->_table;
		
		if ($idName && !empty($this->$idName)) { // this is an update
			$query = "";
			foreach ($recordProps as $key => $value) {
				if ($key == '_table' || $key == '_id')
					continue;
				
				$query .= $query ? "," : "";
				$query .= $key . "='" . $value . "'";
			}
			$result = $_(": UPDATE {$tableName} SET {$query} WHERE {$idName} = '{$this->$idName}'");
		}
		else { // this is a new record
			$insertKey = "";
			$insertValue = "";
			foreach ($recordProps as $key => $value) {
				if ($key == '_table' || $key == '_id')
					continue;
				
				$insertKey .= $insertKey ? "," : "";
				$insertValue .= $insertValue ? "," : "";
				
				$insertKey .= $key;
				$insertValue .= "'" . $value . "'"; 
			}
			$query = "insertid: INSERT INTO {$tableName} ({$insertKey}) VALUES ({$insertValue})";
			
			if (Utils::trimLower(Database::$adapter) == "postgresql") 
				$query .= " RETURNING {$idName}";
			
			$result = $_($query);
			
			if ($result) 
				$this->$idName = $result;
		}
	
		if ($result) 
			if (is_callable([$this, 'cascade']) && !$this->cascade())
				return false;

		return $result;
	}
	
	/**
	 * Deletes the record. 
	 */
	public function delete() 
	{
		// I didn't implemented this method because I think you should never delete anything.
		// IMO you should mark the record as deleted using a flag, like active = 0.
		// If you still feel you should delete something, don't forget to implement and call cascade.
		// if (is_callable([$this, 'cascade']) && !$this->cascade()) return false;
	}

}

/**
 * This interface is useful to extend the functionality of the Table class:
 * 	protected function validate () {} // This method is call for validate the values of the object
 * 	protected function relate () {} // This method is call to buil the external relationships
 * 	protected function cascade () {} // This method is called to delete the related records when deleting this one
 */
interface Validations 
{
	/**
	 * In this method you should implement the validations, 
	 * it returns true if the values of the object are correct 
	 * and false if they are wrong
	 */
	function validate ();
	
	/**
	 * In this method you should build external relationships to this object 
	 * and store them in the properties
	 */
	function relate ();
	
	/**
	 * This method is called after save to update the related records 
	 */
	function cascade ();
}

/**
 * This class acts like a sort of a controller or router
 */
class App
{
	protected static $config = [], 
				     $url = [], 
				     $routes = [], 
					 $includes = [],
					 $enforce = "";

    /**
     * Returns the configuration
     */
    public function getConfig($index = "")
    {
        $result = $index && isset(self::$config[$index]) ? self::$config[$index] : self::$config;
        return $result;
    }

	/** 
	 * Loads the configuration and registers the classes
	 * @param $filepath the config file, if empty gets the default config.json
	 */
	public function config($filepath = '') 
	{ 
		$filepath = $filepath ? $filepath . '.json' : '../config.json';
					
		self::$config = json_decode(file_get_contents($filepath), true);
		
		if (empty(self::$config))
			die ('No configuration file or error while parsing it!');
		
		foreach (self::$config as $key => $value) {
			switch (Utils::trimLower($key)) {
				// the main path
				case 'files_base_path': 
					DEFINE ('FILES_BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . $value);
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
							case 'default':
								self::$routes['DEFAULT'] = Utils::trimLowerKeys($rVal);
								break;
								
							default:
								self::$routes[Utils::trimLower($rKey)] = Utils::trimLowerKeys($rVal);
								break;
						}
					}
					break;
				
				// set the dafault template and language 
				case 'template':
					foreach (self::$config[$key] as $tKey => $tVal) {
						switch (Utils::trimLower($tKey)) {
							case 'layout_path': 
								DEFINE ('LAYOUT_PATH', $tVal);
								break;

							case 'default_layout':  
								if (!empty($_SESSION['LAYOUT_IN_USE']))
									Template::$defaultLayout = $_SESSION['LAYOUT_IN_USE'];
								else
									Template::$defaultLayout = $tVal;
								break;

							case 'language_path': 
								DEFINE ('LANGUAGE_PATH', $tVal);
								break;

							case 'default_language': 
								if (!empty($_SESSION['LANGUAGE_IN_USE']))
									Template::$defaultLanguage = $_SESSION['LANGUAGE_IN_USE'];
								else
									Template::$defaultLanguage = $tVal;
								break;
						}
					}
					break;

				// MySQL conection data
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
		
		// if the main path is not set, lets set the default
		if (!defined('FILES_BASE_PATH')) 
			DEFINE ('FILES_BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/');
	}
	
	/** 
	 * This method recursively includes all the files and classes
	 * @param $folder the folder where to start
	 * @param $exceptions files not to include
	 */
	protected function register ($folder, $exceptions) 
	{
		if (!$folder)
			return false;
		
		$otherFiles = [];

		if (!in_array('$_', $exceptions))
			$exceptions[] = '$_';
			
		$files = scandir($folder);
		foreach ($files as $f) {
			if ($f == '.' || $f == '..' || in_array($f, $exceptions))
				continue;
			// this is for the libraries installed with composer
			elseif (is_dir($folder . $f) && $f == 'vendor' && file_exists($folder . 'vendor/autoload.php')) 
				require_once($folder . 'vendor/autoload.php'); 
			elseif (is_dir($folder . $f)) 
				$otherFiles[] = $folder . $f;
			elseif (substr($f, -4) == '.php')
				include_once($folder . $f);
		}

		if (!empty($otherFiles))
			foreach ($otherFiles as $of)
				$this->register($of . '/', $exceptions);
	}

	/**
	 * Gets some info like layout, language, etc. that cascades down  
	 * @param $action 
	 */
	public function processAction($action) {
		// private area of the website
		if (isset($action['enforce']))  
			self::$enforce = $action['enforce'];
		
		// set the layout
		if (isset($action['layout'])) 
			Template::$defaultLayout = $action['layout'];
		
		// set the language
		if (isset($action['language']))	
			Template::$defaultLanguage = $action['language'];
		
		// register any needed classes
		if (isset($action['register']))	
			self::$includes['FOLDERS'] = $action['register'];
	}
	
	/** 
	 * Routes the app, that is calls the function or method inside a class that is requested
	 * @param $urlpath the path to map as a function or method call
	 * @param $args the arguments passed to the method or function
	 */
	public function route ($urlpath = '', $args = []) 
	{
		if (!$urlpath && isset($_REQUEST['_url']))
			$urlpath = $_REQUEST['_url'];
				
		if ($urlpath) {
			$all = explode('/', Utils::trimLower($urlpath));
			foreach ($all as $value) {
				if ($value) 
					self::$url[] = $value;
			}
		}
		
		$args = array_merge($args, $_REQUEST);

		// if there is no _url put the default
		if (!self::$url) {
			$action = self::$routes['DEFAULT'];
			$this->processAction($action);
		} 
		else {
			$action = self::$routes;
			$index = 0;
			
			while($index < count(self::$url) && is_array($action)) {
				// check for the action
				if (!empty($action[Utils::trimLower(self::$url[$index])])) { 
					$action = $action[Utils::trimLower(self::$url[$index])];
					$this->processAction($action);
					$index++;
				}
				else {
					$action = self::$routes['404'];
					break;
				}
			}
		}

        // if the result is still an array
        if (is_array($action)) {
			// get the arguments if any 
			if (!empty($action['args'])) 
				$args = array_merge($args, json_decode($action['args'], true));

            // redirect to another url
            if (!empty($action['redirect'])) {
                header ('Location: ' . $action['redirect']);
                die ();
            }

            // preferred way when a class is called
            elseif (!empty($action['method']) && !empty($action['class'])) {
                $class = $action['class'];
                $action = $action['method'];
            }

            // preferred way when the action is just a function
            elseif (!empty($action['action']))
                $action = $action['action'];

            // there is something wrong here...
            else
                $action = self::$routes['404'];
        }

        // register all the classes
		if (!empty(self::$includes['FOLDERS'])) { 
			$folders = explode(';', self::$includes['FOLDERS']); 

			$exceptions = empty(self::$includes['EXCEPTIONS']) ? [] : explode(';', self::$includes['EXCEPTIONS']); 
			
			foreach ($folders as $f) {
				if (!$f) 
					continue;
				$this->register(FILES_BASE_PATH . $f, $exceptions);
			}
		}

		if (!empty(self::$includes['VENDORS']) && is_dir(FILES_BASE_PATH . self::$includes['VENDORS']) && 
			file_exists(FILES_BASE_PATH . self::$includes['VENDORS'] . 'autoload.php')) {
			require_once(FILES_BASE_PATH . self::$includes['VENDORS'] . 'autoload.php'); 
		}
		
		// call the function that enforces login
		if (!empty(self::$enforce) && is_callable(self::$enforce)) 
			call_user_func(self::$enforce, $args);
		
		// lets call the main action inside a class
		if (!empty($class) && is_callable([$c = new $class($args), $action])) 
			return $c->$action($args);
		
		// lets call the main action as a function
		elseif (!empty($action) && is_callable($action)) 
			return call_user_func($action, $args); 
		
		else 
			die('No action method found!'); 
	}
}

/**
 * This class takes care of the view
 */
class Template
{
	public static $defaultLayout = '',
				  $defaultLanguage = [];
				  
	protected $fullLayout = '',
			  $fullLanguage = [];
	
	/**
	 * Set the layout
	 * @param $layout the file to get the layout from, if empty the default is set
 	 */ 
	public function setLayout ($layout = '') 
	{
		$filename = LAYOUT_PATH . ($layout ? $layout : self::$defaultLayout);

		$this->fullLayout = $this->inject($filename . '.html');

		return $this->fullLayout;
	}
	
	/**
	 * Get the layout, just returns the layout
 	 */ 
	public function getLayout () 
	{
		return $this->fullLayout;
	}
	
	/**
	 * Set the language of the template
	 * @param $language 
 	 */
	public function setLang ($language = '')
	{
		$filename = LANGUAGE_PATH . ($language ? $language : self::$defaultLanguage);

		$allLines = file(FILES_BASE_PATH . $filename . '.ini');
		
		$this->fullLanguage = [];
		foreach ($allLines as $line) {
			$line = trim($line);
			if (empty($line)) 
				continue;
			$keyValue = explode('=>', $line);
			if ($keyValue[0][0] == '#' || empty($keyValue[1])) 
				continue;
			$this->fullLanguage[trim($keyValue[0])] = trim($keyValue[1]);
		}

		return $this->fullLanguage;
	}
	
	/**
	 * Get language or a specific index of the language array
	 * @param $propos the index of the language array or if empty returns the whole thing
 	 */
	public function getLang ($props = '') 
	{
		if (!$this->fullLanguage)
			$this->setLang();
		
		if (empty($props))
			$res = $this->fullLanguage;
		elseif (is_array($props)) {
			$res = [];
			foreach ($props as $p) 
				if (isset($this->fullLanguage[$p]))
					$res[$p] = $this->fullLanguage[$p];
		}
		elseif (isset($this->fullLanguage[$props]))
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
	protected function apply ($html, $allDefs, $clean = false) 
	{
		foreach ($allDefs as $name => $content) {
			// this is escaping the $ in the string
			$content = addcslashes($content, '\\$'); 
			$html = preg_replace('/\<:'.$name.'\/\>/', $content, $html);
			$html = preg_replace('/{{'.$name.'}}/', $content, $html);
		}

		if ($clean) {
			$html = preg_replace('/\<:(.*)\/\>/', '', $html);
			$html = preg_replace('/{{((?:[^}]|}[^}])+)}}/', '', $html);
		}

		return $html;
	}

	/**
	 * This method is usefull to inject code in a template
	 * @param $filename 
	 * @param $allDef all the indexes to substitute in the template
	 * @param $clean if true remove all the not used anchors in teh template 
 	 */
	public function inject ($filename, $allDef = [], $clean = false) 
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
	public function render ($results = [])
	{
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

	/**
	 * Just return the string as html output, with especial symbols (< to &lt;) and new lines (\n to <br>) converted
	 */
	static public function htmlOut ($str = '') 
	{
		return nl2br(htmlspecialchars($str, ENT_QUOTES)); 
	}

	/**
	 * Return a link properly formatted
	 */
	static public function linkOut ($urlpage = '', $getArgs = []) 
	{
		$url = rawurlencode($urlpage);
		if (!empty($getArgs)) {
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
	function sendEmail ($emailto, $params, $content, $language = '',  $emailfrom = '', $namefrom = '') 
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
		}
		else {
			$subject = preg_replace($pattern, '', $params);
		}
		
		$body = "";
		if (is_array($content)) {
			$template = new Template();
			if ($language)
				$template->setLang($language);
			$template->setLayout(self::$layout);
			$body = wordwrap($template->render($content));
		}
		else {
			$body = wordwrap($content);
		}

		if (class_exists('PHPMailer\PHPMailer\PHPMailer') && !empty(self::$server) && !empty(self::$port) &&
			!empty(self::$user) && !empty(self::$password)) { 
			// if smpt email sending is allowed use PHPMailer
			return $this->sendPHPMailer($emailto, $subject, $body, $emailfrom, $namefrom);
		}
		else { 
			// sent the email with php mail
			return $this->sendMail($emailto, $subject, $body);
		}
	}
	
	/**
	 * Send email over SMTP
	 * @param $emailto the email to send the email to
	 * @param $subject the subject of the email
	 * @param $body the full body of the email
	 */
	protected function sendPHPMailer ($emailto, $subject, $body, $emailfrom = '', $namefrom = '') 
	{
		$mail = new PHPMailer();

		// for debug only
		// $mail->SMTPDebug = 3;

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
		 
		return true;
	} 
	
	/**
	 * Send email 
	 * @param $emailto the email to send the email to
	 * @param $subject the subject of the email
	 * @param $body the full body of the email
	 */
	protected function sendMail ($emailto, $subject, $body) 
	{
		$from = self::$from . ' <' . self::$system . '>';

		// create a boundary for the email. This
		$boundary = uniqid('ch');

		// set the headers
		$headers = 'MIME-Version: 1.0' . "\n";
		$headers .= 'From: ' . $from . "\n";
		$headers .= 'Reply-To: ' . $from . "\n";
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
	 * @param $connectTimeout the amount of time tofor the timeout
	 */
	public function sendHttp($url, $method, $request = '', $headers = array(), $options = array(), $connectTimeout = 30) 
	{
		$ret = array(
			'result' => false,
			'code'   => 0
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
				switch ($key) {
					case 'headers': 
						$headers = $value;
						break;
					case 'options': 
						$options = $value;
						break;
					case 'connectTimeout': 
						$connectTimeout = $value;
						break;
				}
			}

			if (isset($request['request'])) {
				$request = $request['request'];
			}
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
  
		$result = curl_exec($this->handle);

		$ret['result'] = $result;
		$ret['code'] = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);
	  
		// this function have to be called several times because the messages are queued
		while (openssl_error_string());

		return $ret;
	}
	
	/*
		Useful snippets of code, you do not have to use all those options: 

		$query_string = http_build_query($data_array); 
		$time = 30;
		$useragent='InvestiGate API Access';
		$userpwd = array(
			"api_username" => "", 
			"api_password" => ""
		);
		$curl = new Curl();
		$result = $curl->sendHttp(
			"POST", 
			"https://www.example.com/",
			"",
			[],
			[
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_VERBOSE        => false,
				CURLOPT_POSTFIELDS     => $query_string,
				CURLOPT_POST           => true,
				CURLOPT_HTTPGET        => true,
				CURLOPT_TIMEOUT        => $time,
				CURLOPT_CONNECTTIMEOUT => $time,
				CURLOPT_USERAGENT      => $useragent,
				CURLOPT_USERPWD        => $userpwd['api_username'].':'.$userpwd['api_password']
			]
		);
	*/
}

/**
 * This is a quick access to the main features 
 * @param $query this is the main query, usually in this syntax "action: query"
 * @param $options this options to insert in the query
 * @param $extras more options basically
 */
$_ = function ($query = '', $options = [], $extras = '') 
{
	static $app = NULL, 
		   $database = NULL, 
		   $template = NULL, 
		   $email = NULL;

	$app = $app ?? new App();
	$database = $database ?? new Database();
	$template = $template ?? new Template();
	$email = $email ?? new Email();
	$curl = $curl ?? new Curl();
	
	// first get the parts of the query into an array
	list($action, $query) = Utils::extractQuery($query);
	
	// check if the action is defined
	switch ($action) 
	{
		/*********************
		 * Express Init & Run
		 *********************/
		
		// run the app, automatic and simple
		case 'run': 
			$app->config();
			$database->connect();
			// if the router returned null pass an empty array to the render, 
			// this avoids warnings from php 
			echo $template->render($app->route() ?? []); 
			break;
		
		/*********************
		 * APP ACTIONS
		 *********************/

		// load the config from some file
		case 'config:':
			$options = $query;
			
		// set the config	
		case 'config': 
			return $app->config($options);

		// Get a specific index in the configuration
        case 'getconfig:':
            $options = $query;

        // Get the configuration
        case 'getconfig':
            return $app->getConfig($options);

		// route to some path
		case 'route:':
			$extras = $options;
			$options = $query;
			
		// route the app
		case 'route': 
			return $app->route($options, $extras);

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
	
		// set language to some specific file
		case 'setlang:': 
			$options = $query;
			
		// set language
		case 'setlang': 
			return $template->setlang($options);
		
		// get some of the value sof the language
		case 'getlang:':
			$options = $query;
			
		// get the language
		case 'getlang':
			return $template->getLang($options);
			
		// inject a file
		case 'inject:':
			return $template->inject($query, $options, $extras ? false : true);
	
		// render the results
		case 'render':
			return $template->render($options);
	
		/*********************
		 * DATABASE ACTIONS
		 *********************/
		
		 // connect to the database
		case 'connect':
			return $database->connect();
		
		// sanitize the values
		case 'sanitize':
			return $database->sanitize($options);
		
		// run a literal query and overwrites the default return type with the value 
		case 'single:':
		case 'insertid:':
		case 'obj:':
		case 'assoclist:':
		case 'assoc:':
			$extras = substr($action, 0, -1);

		// run a literal query 
		case 'query:':
		case ':':
			return $database->query($query, $options, $extras ? $extras : []);

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
		 * Unknowed action,
		 * return error 
		 *********************/

		default:
			error_log('WOW (not an option): ' . $action . ' \n query: ' . print_r($query, true) . '\n options: ' . print_r($options, true));
			return false;
	}
};
