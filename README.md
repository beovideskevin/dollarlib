# Dollarlib

Dollarlib is a simple php lib, kind of a very flexible framework. You can see a full working example here: <a href="https://dollarlib.com/">dollarlib.com</a>. I created this lib to build my new media art website: <a href="https://eldiletante.com/">eldiletante.com</a>

I developed this framkework using using WAMP. All the examples should work in an Apache virtual host; create your own and name it "dollarlib.loc". After cloning the repository create two files. The first "/.htaccess" with the code: 
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^((?s).*)$ index.php?_url=$1 [QSA,L]
</IfModule>

<Files "config.json">
	Order Allow,Deny
	Deny from all
</Files>

Options -Indexes
```
After doing this add a file named "config.json" to the root folder with the following code:
```
{
	"FILES_BASE_PATH": "/",
	
	"TEMPLATE": {
		"LANGUAGE_PATH": "app/languages/",
		"DEFAULT_LANGUAGE": "en",
		"LAYOUT_PATH": "app/layouts/",
		"DEFAULT_LAYOUT": "example"
	},
	
	"DATABASE": {
		"ADAPTER": "MYSQL",
		"HOST": "localhost",
		"PORT": "3306",
		"DATABASE": "dollarlib",
		"USER": "yourusername",
		"PASSWORD": "yourpassword"
	},
	
	"EMAIL": {
		"SYSTEM": "contact@dollarlib.loc",
		"FROM": "Dollarlib",
		"SERVER": "",
		"PORT": "",
		"USER": "",
		"PASSWORD": "",
		"LAYOUT": "email"
	},
	
	"recaptcha": {
		"secretKey": "secretKeyRecaptcha",
		"siteKey": "siteKeyRecaptcha"
	},
	
	"REGISTER": {
		"EXCEPTIONS": "",
		"FOLDERS": "app/"
	},
	
	"ROUTES": {
		"DEFAULT": {
			"action" : "login",
			"args": "{\"rent\": 123456}"
		},

		"pay": {
			"action": "pay"
		},
		
		"addcard": {
			"action": "addCard",
			"enforce": "enforce"
		},

		"dashboard": {
			"action": "dashboard",
			"enforce": "enforce"
		},

		"contact": "contact",

		"snippets": "snippets",
		
		"logout": "logout",

		"es": {
			"redirect": "/?lan=es"
		},

		"en": {
			"redirect": "/?lan=en"
		},

		"login": {
			"redirect": "/"
		},

		"404": {
			"redirect": "/"
		}
	}
}
```
Don't forget to replace the values like "yourusername", "yourpassword", etc.

If you don't want to use a virtual host, you could just clone the repository in the root of your web documents and create the htaccess files. Create the config file but change the second line to "FILES_BASE_PATH": "/dollarlib/". Then go to /app/layouts/ and edit "main.html" so every reference to an external resource is prefixed with "/dollarlib/".

If you are using IIS or Nginx you will need to make some changes to "web.config" or the configuration files of your server (reproduce the logic in "/.htaccess"). 

In order to make the example work you will need to import into the database the script located in the folder "migrartions". This was created for MySQL, if you are using PostgreSQL or somethng else, you may need to make some changes there too.

After this is done you can check the example in the routes:
```
dollarlib.loc/
dollarlib.loc/contact
```

TO DO:
- [ ] Create the code snippets as documentation... no rush